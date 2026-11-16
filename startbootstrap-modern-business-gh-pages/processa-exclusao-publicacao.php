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

// Verifica se o ID foi enviado
if (!isset($_POST["id_conteudo"]) || !is_numeric($_POST["id_conteudo"])) {
    header("Location: minhas-publicacoes.php");
    exit;
}

$id_conteudo = (int) $_POST["id_conteudo"];
$id_usuario = (int) $_SESSION["id_usuario"];

if ($id_conteudo <= 0) {
    header("Location: minhas-publicacoes.php");
    exit;
}

require_once "conecta.php";

// Busca a publicação e verifica se pertence ao colaborador
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
    header("Location: minhas-publicacoes.php");
    exit;
}

// Exclui a publicação do banco de dados
$sql = "DELETE FROM conteudos
        WHERE id = ? AND id_usuario = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ii", $id_conteudo, $id_usuario);

if (mysqli_stmt_execute($stmt)) {

    // Exclui a imagem da publicação
    if (
        !empty($publicacao["imagem"]) &&
        strpos($publicacao["imagem"], "uploads/conteudos/imagens/") === 0 &&
        file_exists($publicacao["imagem"])
    ) {
        unlink($publicacao["imagem"]);
    }

    // Exclui o PDF da publicação
    if (
        !empty($publicacao["arquivo_pdf"]) &&
        strpos($publicacao["arquivo_pdf"], "uploads/conteudos/pdfs/") === 0 &&
        file_exists($publicacao["arquivo_pdf"])
    ) {
        unlink($publicacao["arquivo_pdf"]);
    }

    mysqli_stmt_close($stmt);

    header("Location: minhas-publicacoes.php");
    exit;

} else {

    mysqli_stmt_close($stmt);
    die("Não foi possível excluir a publicação.");
}
?>
