<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: meus-dados.php");
    exit;
}

if (
    !isset($_POST["nome"]) ||
    !isset($_POST["email"]) ||
    !isset($_POST["telefone"]) ||
    !isset($_POST["tipo_usuario"])
) {
    $_SESSION["msg"] = "Dados incompletos.";
    $_SESSION["class"] = "erro";
    header("Location: meus-dados.php");
    exit;
}

$nome = trim($_POST["nome"]);
$email = trim($_POST["email"]);
$telefone = trim($_POST["telefone"]);
$tipo_usuario = $_POST["tipo_usuario"];

if (
    $nome == "" ||
    $email == "" ||
    $telefone == "" ||
    $tipo_usuario == ""
) {
    $_SESSION["msg"] = "Preencha todos os campos.";
    $_SESSION["class"] = "erro";
    header("Location: meus-dados.php");
    exit;
}

$partes_nome = preg_split('/\s+/', $nome);

if (count($partes_nome) < 2) {
    $_SESSION["msg"] = "Informe seu nome completo, com nome e sobrenome.";
    $_SESSION["class"] = "erro";
    header("Location: meus-dados.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["msg"] = "Informe um e-mail válido.";
    $_SESSION["class"] = "erro";
    header("Location: meus-dados.php");
    exit;
}

$telefone = preg_replace('/[^0-9]/', '', $telefone);

if (strlen($telefone) != 11 || $telefone[2] != "9") {
    $_SESSION["msg"] = "Informe um número de celular válido.";
    $_SESSION["class"] = "erro";
    header("Location: meus-dados.php");
    exit;
}

if ($tipo_usuario != "estudante" && $tipo_usuario != "colaborador") {
    $_SESSION["msg"] = "Tipo de usuário inválido.";
    $_SESSION["class"] = "erro";
    header("Location: meus-dados.php");
    exit;
}

require_once "conecta.php";

try {

    $id_usuario = $_SESSION["id_usuario"];

    $sql = "UPDATE usuarios
            SET nome = ?, email = ?, telefone = ?, tipo_usuario = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssi",
        $nome,
        $email,
        $telefone,
        $tipo_usuario,
        $id_usuario
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    $_SESSION["class"] = "sucesso";

    header("Location: meus-dados.php");
    exit;

} catch (mysqli_sql_exception $e) {

    $_SESSION["msg"] = "Não foi possível atualizar os dados.";
    $_SESSION["class"] = "erro";

    header("Location: meus-dados.php");
    exit;
}

?>