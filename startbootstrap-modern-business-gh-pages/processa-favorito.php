<?php

session_start();

include("conecta.php");

// Verifica se o usuário está logado
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: favoritos.php");
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

$id_item = intval($_POST["id_item"] ?? 0);
$tipo = $_POST["tipo"] ?? "";
$acao = $_POST["acao"] ?? "";
$origem = $_POST["origem"] ?? "";

// Valida os dados recebidos
if (
    $id_item <= 0 ||
    !in_array($tipo, ["conteudo", "material"]) ||
    !in_array($acao, ["adicionar", "remover"])
) {
    die("Dados inválidos.");
}

// Define a tabela e a coluna conforme o tipo do item
if ($tipo == "conteudo") {
    $coluna = "id_conteudo";
    $tabela = "conteudos";
} else {
    $coluna = "id_material";
    $tabela = "materiais";
}

// Verifica se o item existe
$sql = "SELECT id FROM $tabela WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_item);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) == 0) {
    mysqli_stmt_close($stmt);
    die("Item não encontrado.");
}

mysqli_stmt_close($stmt);

// Adiciona ou remove o favorito
if ($acao == "adicionar") {

    $sql = "INSERT INTO favoritos (id_usuario, $coluna) VALUES (?, ?)";

} else {

    $sql = "DELETE FROM favoritos WHERE id_usuario = ? AND $coluna = ?";

}

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ii", $id_usuario, $id_item);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    die("Não foi possível atualizar os favoritos.");
}

mysqli_stmt_close($stmt);

// Retorna para a página de favoritos quando a remoção foi feita nela
if ($acao == "remover" && $origem == "favoritos") {

    header("Location: favoritos.php");

} elseif ($tipo == "conteudo") {

    header("Location: conteudos.php");

} else {

    header("Location: materiais.php");

}

exit;

?>
