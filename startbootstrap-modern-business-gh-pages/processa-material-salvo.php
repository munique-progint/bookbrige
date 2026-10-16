<?php
session_start();

require_once "conecta.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: materiais.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$id_material = filter_input(INPUT_POST, "id_material", FILTER_VALIDATE_INT);
$nome_pasta = trim($_POST["nome_pasta"] ?? "");

if (!$id_material || $nome_pasta == "" || mb_strlen($nome_pasta) > 100) {
    die("Material ou pasta inválidos.");
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

$sql = "SELECT id FROM materiais
        WHERE id = ? AND status = 'Disponível'";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_material);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$material = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

if (!$material) {
    die("Material não encontrado ou indisponível.");
}

$sql = "INSERT INTO materiais_salvos (id_usuario, id_material, nome_pasta)
        VALUES (?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iis", $id_usuario, $id_material, $nome_pasta);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: material-detalhes.php?id=" . $id_material . "&salvo=1");
    exit;
}

$erro = mysqli_stmt_errno($stmt);
mysqli_stmt_close($stmt);

if ($erro == 1062) {
    header("Location: material-detalhes.php?id=" . $id_material . "&salvo=2");
    exit;
}

die("Não foi possível salvar o material. Tente novamente.");
?>