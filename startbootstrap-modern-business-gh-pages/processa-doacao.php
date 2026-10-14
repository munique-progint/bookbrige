<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: doar-material.html");
    exit;
}

if (
    !isset($_POST["nome_material"]) ||
    !isset($_POST["categoria"]) ||
    !isset($_POST["area"]) ||
    !isset($_POST["nivel"])
) {
    $_SESSION["msg"] = "Preencha os campos obrigatórios da doação.";
    $_SESSION["class"] = "erro";
    header("Location: doar-material.html");
    exit;
}

$nome_material = trim($_POST["nome_material"]);
$categoria = trim($_POST["categoria"]);
$area = trim($_POST["area"]);
$nivel = trim($_POST["nivel"]);
$descricao = trim($_POST["descricao"] ?? "");

if (
    $nome_material == "" ||
    $categoria == "" ||
    $area == "" ||
    $nivel == ""
) {
    $_SESSION["msg"] = "Preencha todos os campos da doação.";
    $_SESSION["class"] = "erro";
    header("Location: doar-material.html");
    exit;
}

if (
    $categoria != "Livro" &&
    $categoria != "Apostila" &&
    $categoria != "Material escolar"
) {
    $_SESSION["msg"] = "Categoria inválida.";
    $_SESSION["class"] = "erro";
    header("Location: doar-material.html");
    exit;
}

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
    "Ciências",
    "Artes",
    "Educação Física",
    "Informática",
    "Todas as matérias",
    "Outra"
];

if (!in_array($area, $areas_permitidas, true)) {
    $_SESSION["msg"] = "Área/matéria inválida.";
    $_SESSION["class"] = "erro";
    header("Location: doar-material.html");
    exit;
}

if (
    $nivel != "Ensino fundamental" &&
    $nivel != "Ensino médio" &&
    $nivel != "Ensino técnico" &&
    $nivel != "Ensino superior" &&
    $nivel != "Curso livre" &&
    $nivel != "Outros"
) {
    $_SESSION["msg"] = "Nível inválido.";
    $_SESSION["class"] = "erro";
    header("Location: doar-material.html");
    exit;
}

$imagem = null;

if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] != UPLOAD_ERR_NO_FILE) {

    if ($_FILES["imagem"]["error"] != UPLOAD_ERR_OK) {
        $_SESSION["msg"] = "Erro ao enviar a imagem.";
        $_SESSION["class"] = "erro";
        header("Location: doar-material.html");
        exit;
    }

    if ($_FILES["imagem"]["size"] > 5 * 1024 * 1024) {
        $_SESSION["msg"] = "A imagem deve ter no máximo 5 MB.";
        $_SESSION["class"] = "erro";
        header("Location: doar-material.html");
        exit;
    }

    $tipo_imagem = getimagesize($_FILES["imagem"]["tmp_name"]);

    if ($tipo_imagem === false) {
        $_SESSION["msg"] = "O arquivo enviado não é uma imagem válida.";
        $_SESSION["class"] = "erro";
        header("Location: doar-material.html");
        exit;
    }

    $extensao = strtolower(pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION));

    
        if (
    $extensao != "jpg" &&
    $extensao != "jpeg" &&
    $extensao != "jfif" &&
    $extensao != "png" &&
    $extensao != "webp"

    ) {
        $_SESSION["msg"] = "Formato de imagem inválido. Use JPG, PNG ou WEBP.";
        $_SESSION["class"] = "erro";
        header("Location: doar-material.html");
        exit;
    }

    $pasta = "uploads/materiais/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    $nome_imagem = uniqid() . "." . $extensao;
    $caminho_imagem = $pasta . $nome_imagem;

    if (!move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminho_imagem)) {
        $_SESSION["msg"] = "Não foi possível salvar a imagem.";
        $_SESSION["class"] = "erro";
        header("Location: doar-material.html");
        exit;
    }

    $imagem = $caminho_imagem;
}

require_once "conecta.php";

try {

    $id_usuario = $_SESSION["id_usuario"];

 
$sql = "INSERT INTO materiais
        (id_usuario, nome_material, categoria, area, nivel, descricao, imagem)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "issssss",
    $id_usuario,
    $nome_material,
    $categoria,
    $area,
    $nivel,
    $descricao,
    $imagem
);

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


    $_SESSION["msg"] = "Doação cadastrada com sucesso!";
    $_SESSION["class"] = "sucesso";

    header("Location: minhas-doacoes.php");
    exit;

} catch (mysqli_sql_exception $e) {

    if ($imagem != null && file_exists($imagem)) {
        unlink($imagem);
    }

    $_SESSION["msg"] = "Não foi possível cadastrar a doação.";
    $_SESSION["class"] = "erro";

    header("Location: doar-material.html");
    exit;
}

?>