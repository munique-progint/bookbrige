<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: minhas-doacoes.php");
    exit;
}

if (!isset($_POST["id_material"]) || !is_numeric($_POST["id_material"]) || $_POST["id_material"] <= 0) {
    $_SESSION["msg"] = "Material inválido.";
    $_SESSION["class"] = "danger";
    header("Location: minhas-doacoes.php");
    exit;
}

$id_material = $_POST["id_material"];
$id_usuario = $_SESSION["id_usuario"];

require_once "conecta.php";

// Busca o material e verifica se pertence ao usuário conectado.
$sql = "SELECT imagem FROM materiais WHERE id = ? AND id_usuario = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id_material, $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) == 0) {
    $_SESSION["msg"] = "Material não encontrado ou você não tem permissão para excluí-lo.";
    $_SESSION["class"] = "danger";
    header("Location: minhas-doacoes.php");
    exit;
}

$material = mysqli_fetch_assoc($resultado);
$caminho_imagem = $material["imagem"];

// Exclui somente o material pertencente ao usuário conectado.
$sql = "DELETE FROM materiais WHERE id = ? AND id_usuario = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id_material, $id_usuario);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    // Remove a imagem somente se estiver na pasta de uploads de materiais.
    if (!empty($caminho_imagem) && strpos($caminho_imagem, "uploads/materiais/") === 0) {
        $arquivo_imagem = __DIR__ . "/" . $caminho_imagem;

        if (is_file($arquivo_imagem)) {
            unlink($arquivo_imagem);
        }
    }

    $_SESSION["msg"] = "Material excluído com sucesso!";
    $_SESSION["class"] = "success";
} else {
    $_SESSION["msg"] = "Não foi possível excluir o material.";
    $_SESSION["class"] = "danger";
}

header("Location: minhas-doacoes.php");
exit;
?>
