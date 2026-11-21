<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

// Verifica se o usuário é colaborador
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "colaborador") {
    header("Location: minha-conta.php");
    exit;
}

// Aceita somente requisições POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: minhas-publicacoes.php");
    exit;
}

// Verifica os campos obrigatórios
if (
    !isset($_POST["id_conteudo"]) ||
    !isset($_POST["nome_conteudo"]) ||
    !isset($_POST["categoria"]) ||
    !isset($_POST["area"]) ||
    !isset($_POST["nivel"]) ||
    !isset($_POST["descricao"]) ||
    !isset($_FILES["imagem"]) ||
    !isset($_FILES["arquivo_pdf"])
) {
    die("Preencha todos os campos.");
}

$id_conteudo = (int) $_POST["id_conteudo"];
$id_usuario = (int) $_SESSION["id_usuario"];

$nome_conteudo = trim($_POST["nome_conteudo"]);
$categoria = trim($_POST["categoria"]);
$area = trim($_POST["area"]);
$nivel = trim($_POST["nivel"]);
$descricao = trim($_POST["descricao"]);

// Verifica se o ID é válido
if ($id_conteudo <= 0) {
    die("Publicação inválida.");
}

// Verifica se os campos estão preenchidos
if (
    $nome_conteudo == "" ||
    $categoria == "" ||
    $area == "" ||
    $nivel == "" ||
    $descricao == ""
) {
    die("Preencha todos os campos.");
}

// Verifica os limites de caracteres
if (
    strlen($nome_conteudo) > 150 ||
    strlen($area) > 100 ||
    strlen($descricao) > 2000
) {
    die("O nome, a área ou a descrição ultrapassou o limite permitido.");
}

// Categorias permitidas
$categorias_permitidas = [
    "Artigo",
    "Apostila",
    "Mapa mental",
    "Lista de exercícios",
    "Resumo",
    "Simulados",
    "Slides",
    "Outros"
];

// Níveis permitidos
$niveis_permitidos = [
    "Ensino fundamental",
    "Ensino médio",
    "Ensino técnico",
    "Ensino superior",
    "Curso livre",
    "Outros"
];

if (
    !in_array($categoria, $categorias_permitidas, true) ||
    !in_array($nivel, $niveis_permitidos, true)
) {
    die("Categoria ou nível inválido.");
}

require_once "conecta.php";

// Busca a publicação e verifica se pertence ao usuário
$sql = "SELECT imagem, arquivo_pdf
        FROM conteudos
        WHERE id = ? AND id_usuario = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ii", $id_conteudo, $id_usuario);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$publicacao = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

if (!$publicacao) {
    die("Publicação não encontrada.");
}

// Mantém os arquivos atuais
$caminho_imagem = $publicacao["imagem"];
$caminho_pdf = $publicacao["arquivo_pdf"];

$nova_imagem = "";
$novo_pdf = "";

$pasta_imagens = "uploads/conteudos/imagens/";
$pasta_pdfs = "uploads/conteudos/pdfs/";

// Verifica se foi enviada uma nova imagem
if ($_FILES["imagem"]["error"] != UPLOAD_ERR_NO_FILE) {

    $imagem = $_FILES["imagem"];

    if ($imagem["error"] != UPLOAD_ERR_OK) {
        die("Erro ao enviar a imagem.");
    }

    if ($imagem["size"] > 5 * 1024 * 1024) {
        die("A imagem deve ter até 5 MB.");
    }

    $tipos_imagem = [
        "image/jpeg" => "jpg",
        "image/png" => "png"
    ];

    $arquivo_imagem = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_imagem = finfo_file($arquivo_imagem, $imagem["tmp_name"]);
    finfo_close($arquivo_imagem);

    if (!isset($tipos_imagem[$tipo_imagem])) {
        die("A capa deve ser uma imagem JPG ou PNG válida.");
    }

    if (!is_dir($pasta_imagens) && !mkdir($pasta_imagens, 0755, true)) {
        die("Não foi possível preparar a pasta das imagens.");
    }

    $nome_imagem = bin2hex(random_bytes(16)) . "." . $tipos_imagem[$tipo_imagem];

    $nova_imagem = $pasta_imagens . $nome_imagem;

    if (!move_uploaded_file($imagem["tmp_name"], $nova_imagem)) {
        die("Não foi possível salvar a nova imagem.");
    }

    $caminho_imagem = $nova_imagem;
}

// Verifica se foi enviado um novo PDF
if ($_FILES["arquivo_pdf"]["error"] != UPLOAD_ERR_NO_FILE) {

    $arquivo_pdf = $_FILES["arquivo_pdf"];

    if ($arquivo_pdf["error"] != UPLOAD_ERR_OK) {
        if ($nova_imagem != "" && file_exists($nova_imagem)) {
            unlink($nova_imagem);
        }

        die("Erro ao enviar o PDF.");
    }

    if ($arquivo_pdf["size"] > 20 * 1024 * 1024) {
        if ($nova_imagem != "" && file_exists($nova_imagem)) {
            unlink($nova_imagem);
        }

        die("O PDF deve ter até 20 MB.");
    }

    $arquivo_tipo_pdf = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_pdf = finfo_file($arquivo_tipo_pdf, $arquivo_pdf["tmp_name"]);
    finfo_close($arquivo_tipo_pdf);

    if ($tipo_pdf != "application/pdf") {
        if ($nova_imagem != "" && file_exists($nova_imagem)) {
            unlink($nova_imagem);
        }

        die("O arquivo enviado deve ser um PDF válido.");
    }

    if (!is_dir($pasta_pdfs) && !mkdir($pasta_pdfs, 0755, true)) {
        if ($nova_imagem != "" && file_exists($nova_imagem)) {
            unlink($nova_imagem);
        }

        die("Não foi possível preparar a pasta dos PDFs.");
    }

    $nome_pdf = bin2hex(random_bytes(16)) . ".pdf";

    $novo_pdf = $pasta_pdfs . $nome_pdf;

    if (!move_uploaded_file($arquivo_pdf["tmp_name"], $novo_pdf)) {
        if ($nova_imagem != "" && file_exists($nova_imagem)) {
            unlink($nova_imagem);
        }

        die("Não foi possível salvar o novo PDF.");
    }

    $caminho_pdf = $novo_pdf;
}

// Atualiza os dados da publicação
try {

    $sql = "UPDATE conteudos
            SET nome_conteudo = ?,
                categoria = ?,
                area = ?,
                nivel = ?,
                descricao = ?,
                imagem = ?,
                arquivo_pdf = ?
            WHERE id = ? AND id_usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssii",
        $nome_conteudo,
        $categoria,
        $area,
        $nivel,
        $descricao,
        $caminho_imagem,
        $caminho_pdf,
        $id_conteudo,
        $id_usuario
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Remove os arquivos antigos quando foram substituídos
    if ($nova_imagem != "" && strpos($publicacao["imagem"], $pasta_imagens) === 0) {
        if (file_exists($publicacao["imagem"])) {
            unlink($publicacao["imagem"]);
        }
    }

    if ($novo_pdf != "" && strpos($publicacao["arquivo_pdf"], $pasta_pdfs) === 0) {
        if (file_exists($publicacao["arquivo_pdf"])) {
            unlink($publicacao["arquivo_pdf"]);
        }
    }

    header("Location: minhas-publicacoes.php");
    exit;

} catch (mysqli_sql_exception $e) {

    // Remove os novos arquivos se a atualização falhar
    if ($nova_imagem != "" && file_exists($nova_imagem)) {
        unlink($nova_imagem);
    }

    if ($novo_pdf != "" && file_exists($novo_pdf)) {
        unlink($novo_pdf);
    }

    die("Não foi possível atualizar a publicação. Tente novamente.");
}

?>
