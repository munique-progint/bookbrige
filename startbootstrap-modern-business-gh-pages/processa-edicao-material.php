<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

// Aceita somente requisições POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: minhas-doacoes.php");
    exit;
}

// Verifica os campos obrigatórios
if (
    !isset($_POST["id_material"]) ||
    !isset($_POST["nome_material"]) ||
    !isset($_POST["categoria"]) ||
    !isset($_POST["area"]) ||
    !isset($_POST["estado"]) ||
    !isset($_POST["nivel"]) ||
    !isset($_POST["descricao"])
) {
    $_SESSION["msg"] = "Preencha todos os campos.";
    $_SESSION["class"] = "danger";
    header("Location: minhas-doacoes.php");
    exit;
}

$id_material = (int) $_POST["id_material"];
$id_usuario = (int) $_SESSION["id_usuario"];

$nome_material = trim($_POST["nome_material"]);
$categoria = trim($_POST["categoria"]);
$area = trim($_POST["area"]);
$estado = strtoupper(trim($_POST["estado"]));
$nivel = trim($_POST["nivel"]);
$descricao = trim($_POST["descricao"]);

// Verifica se o ID é válido
if ($id_material <= 0) {
    die("Material inválido.");
}

// Verifica se os campos estão preenchidos
if (
    $nome_material == "" ||
    $categoria == "" ||
    $area == "" ||
    $estado == "" ||
    $nivel == "" ||
    $descricao == ""
) {
    $_SESSION["msg"] = "Preencha todos os campos.";
    $_SESSION["class"] = "danger";
    header("Location: editar-material.php?id=" . $id_material);
    exit;
}

// Verifica os limites de caracteres
if (strlen($nome_material) > 150 || strlen($descricao) > 2000) {
    $_SESSION["msg"] = "O nome ou a descrição ultrapassou o limite permitido.";
    $_SESSION["class"] = "danger";
    header("Location: editar-material.php?id=" . $id_material);
    exit;
}

// Categorias permitidas
$categorias_permitidas = [
    "Livro",
    "Apostilas",
    "Materiais escolares"
];

// Áreas permitidas
$areas_permitidas = [
    "Matemática",
    "Português",
    "Literatura",
    "Redação",
    "Inglês",
    "Espanhol",
    "História",
    "Geografia",
    "Filosofia",
    "Sociologia",
    "Biologia",
    "Física",
    "Química",
    "Artes",
    "Educação Física",
    "Todas as matérias",
    "Outra"
];

// Níveis permitidos
$niveis_permitidos = [
    "Ensino Fundamental",
    "Ensino Médio",
    "Ensino Superior",
    "Curso livre",
    "Outros"
];

if (
    !in_array($categoria, $categorias_permitidas, true) ||
    !in_array($area, $areas_permitidas, true) ||
    !in_array($nivel, $niveis_permitidos, true) ||
    !preg_match("/^[A-Z]{2}$/", $estado)
) {
    $_SESSION["msg"] = "Categoria, área, estado ou nível inválido.";
    $_SESSION["class"] = "danger";
    header("Location: editar-material.php?id=" . $id_material);
    exit;
}

require_once "conecta.php";

// Busca o material e verifica se pertence ao usuário
$sql = "SELECT imagem
        FROM materiais
        WHERE id = ? AND id_usuario = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ii", $id_material, $id_usuario);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$material = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

if (!$material) {
    die("Material não encontrado.");
}

// Mantém a imagem atual
$caminho_imagem = $material["imagem"];
$nova_imagem = "";

$pasta_imagens = "uploads/materiais/";

// Verifica se foi enviada uma nova imagem
if (
    isset($_FILES["imagem"]) &&
    $_FILES["imagem"]["error"] != UPLOAD_ERR_NO_FILE
) {

    $imagem = $_FILES["imagem"];

    if ($imagem["error"] != UPLOAD_ERR_OK) {
        $_SESSION["msg"] = "Erro ao enviar a imagem.";
        $_SESSION["class"] = "danger";
        header("Location: editar-material.php?id=" . $id_material);
        exit;
    }

    if ($imagem["size"] > 5 * 1024 * 1024) {
        $_SESSION["msg"] = "A imagem deve ter até 5 MB.";
        $_SESSION["class"] = "danger";
        header("Location: editar-material.php?id=" . $id_material);
        exit;
    }

    $tipos_imagem = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    $arquivo_imagem = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_imagem = finfo_file($arquivo_imagem, $imagem["tmp_name"]);
    finfo_close($arquivo_imagem);

    if (
        !isset($tipos_imagem[$tipo_imagem]) ||
        getimagesize($imagem["tmp_name"]) === false
    ) {
        $_SESSION["msg"] = "Envie uma imagem JPG, PNG ou WEBP válida.";
        $_SESSION["class"] = "danger";
        header("Location: editar-material.php?id=" . $id_material);
        exit;
    }

    if (!is_dir($pasta_imagens) && !mkdir($pasta_imagens, 0755, true)) {
        $_SESSION["msg"] = "Não foi possível preparar a pasta das imagens.";
        $_SESSION["class"] = "danger";
        header("Location: editar-material.php?id=" . $id_material);
        exit;
    }

    $nome_imagem = bin2hex(random_bytes(16)) . "." . $tipos_imagem[$tipo_imagem];

    $nova_imagem = $pasta_imagens . $nome_imagem;

    if (!move_uploaded_file($imagem["tmp_name"], $nova_imagem)) {
        $_SESSION["msg"] = "Não foi possível salvar a nova imagem.";
        $_SESSION["class"] = "danger";
        header("Location: editar-material.php?id=" . $id_material);
        exit;
    }

    $caminho_imagem = $nova_imagem;
}

// Atualiza os dados do material
try {

    $sql = "UPDATE materiais
            SET nome_material = ?,
                categoria = ?,
                area = ?,
                estado = ?,
                nivel = ?,
                descricao = ?,
                imagem = ?
            WHERE id = ? AND id_usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssii",
        $nome_material,
        $categoria,
        $area,
        $estado,
        $nivel,
        $descricao,
        $caminho_imagem,
        $id_material,
        $id_usuario
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Remove a imagem antiga somente quando uma nova foi enviada
    if (
        $nova_imagem != "" &&
        !empty($material["imagem"]) &&
        strpos($material["imagem"], $pasta_imagens) === 0 &&
        file_exists($material["imagem"])
    ) {
        unlink($material["imagem"]);
    }

    $_SESSION["msg"] = "Material atualizado com sucesso!";
    $_SESSION["class"] = "sucesso";

    header("Location: minhas-doacoes.php");
    exit;

} catch (mysqli_sql_exception $e) {

    // Remove a nova imagem se a atualização falhar
    if ($nova_imagem != "" && file_exists($nova_imagem)) {
        unlink($nova_imagem);
    }

    $_SESSION["msg"] = "Não foi possível atualizar o material. Tente novamente.";
    $_SESSION["class"] = "danger";

    header("Location: editar-material.php?id=" . $id_material);
    exit;
}

?>
