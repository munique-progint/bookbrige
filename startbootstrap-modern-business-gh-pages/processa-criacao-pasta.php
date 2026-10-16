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
    die("O nome da pasta é obrigatório e deve ter no máximo 100 caracteres.");
}

$sql = "INSERT INTO pastas (id_usuario, nome_pasta)
        VALUES (?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $id_usuario, $nome_pasta);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    header("Location: pastas.php");
    exit;
}

$erro = mysqli_stmt_errno($stmt);
mysqli_stmt_close($stmt);

if ($erro == 1062) {
    die("Você já possui uma pasta com esse nome.");
}

die("Não foi possível criar a pasta. Tente novamente.");
?>