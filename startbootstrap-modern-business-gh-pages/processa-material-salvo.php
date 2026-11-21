<?php
session_start();

require_once "conecta.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: conteudos.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$id_conteudo = filter_input(INPUT_POST, "id_conteudo", FILTER_VALIDATE_INT);
$nome_pasta = trim($_POST["nome_pasta"] ?? "");

if (!$id_conteudo || $nome_pasta == "" || mb_strlen($nome_pasta) > 100) {
    die("Conteúdo ou pasta inválidos.");
}

$sql = "SELECT id FROM pastas
        WHERE id_usuario = ?
          AND nome_pasta = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $id_usuario, $nome_pasta);
mysqli_stmt_execute($stmt);

$resultado_pasta = mysqli_stmt_get_result($stmt);
$pasta = mysqli_fetch_assoc($resultado_pasta);

mysqli_stmt_close($stmt);

if (!$pasta) {
    die("Essa pasta não existe ou não pertence à sua conta.");
}

$sql = "SELECT id FROM conteudos WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_conteudo);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$conteudo = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

if (!$conteudo) {
    die("Conteúdo não encontrado.");
}

$sql = "INSERT INTO conteudos_salvos (id_usuario, id_conteudo, nome_pasta)
        VALUES (?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iis", $id_usuario, $id_conteudo, $nome_pasta);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: detalhes-conteudo.php?id=" . $id_conteudo . "&salvo=1");
    exit;
}

$erro = mysqli_stmt_errno($stmt);
mysqli_stmt_close($stmt);

if ($erro == 1062) {
    header("Location: detalhes-conteudo.php?id=" . $id_conteudo . "&salvo=2");
    exit;
}

die("Não foi possível salvar o conteúdo. Tente novamente.");
?>
