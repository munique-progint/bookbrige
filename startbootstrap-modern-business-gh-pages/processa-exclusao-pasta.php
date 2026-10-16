<?php
session_start();

require_once "conecta.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: pastas.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$nome_pasta = trim($_POST["nome_pasta"] ?? "");

if ($nome_pasta == "" || mb_strlen($nome_pasta) > 100) {
    die("Nome da pasta inválido.");
}

mysqli_begin_transaction($conn);

$sql = "DELETE FROM materiais_salvos
        WHERE id_usuario = ?
          AND nome_pasta = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $id_usuario, $nome_pasta);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_rollback($conn);
    die("Não foi possível excluir os materiais da pasta.");
}

mysqli_stmt_close($stmt);

$sql = "DELETE FROM pastas
        WHERE id_usuario = ?
          AND nome_pasta = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $id_usuario, $nome_pasta);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_rollback($conn);
    die("Não foi possível excluir a pasta.");
}

mysqli_stmt_close($stmt);

mysqli_commit($conn);

header("Location: pastas.php");
exit;
?>