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
$id_material = filter_input(INPUT_POST, "id_material", FILTER_VALIDATE_INT);
$nome_pasta = trim($_POST["nome_pasta"] ?? "");

if (!$id_material || $nome_pasta == "") {
    die("Dados inválidos.");
}

$sql = "DELETE FROM materiais_salvos
        WHERE id_usuario = ?
          AND id_material = ?
          AND nome_pasta = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iis", $id_usuario, $id_material, $nome_pasta);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    header("Location: pastas-detalhes.php?pasta=" . urlencode($nome_pasta));
    exit;
}

mysqli_stmt_close($stmt);

die("Não foi possível remover o material da pasta.");
?>