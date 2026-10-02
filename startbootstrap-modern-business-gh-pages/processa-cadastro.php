<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: cadastro.html");
    exit;
}

if (
    !isset($_POST["nome"]) ||
    !isset($_POST["email"]) ||
    !isset($_POST["telefone"]) ||
    !isset($_POST["senha"]) ||
    !isset($_POST["confirmar_senha"]) ||
    !isset($_POST["tipoUsuario"])
) {
    $_SESSION["msg"] = "Dados do cadastro incompletos.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-cadastro.php");
    exit;
}

$nome = trim($_POST["nome"]);
$email = trim($_POST["email"]);
$telefone = trim($_POST["telefone"]);
$senha = $_POST["senha"];
$confirmar_senha = $_POST["confirmar_senha"];
$tipo_usuario = $_POST["tipoUsuario"];

if (
    $nome == "" ||
    $email == "" ||
    $telefone == "" ||
    $senha == "" ||
    $confirmar_senha == "" ||
    $tipo_usuario == ""
) {
    $_SESSION["msg"] = "Preencha todos os campos do cadastro.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-cadastro.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["msg"] = "Informe um e-mail válido.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-cadastro.php");
    exit;
}

if (strlen($senha) < 8) {
    $_SESSION["msg"] = "A senha deve ter pelo menos 8 caracteres.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-cadastro.php");
    exit;
}

if ($senha != $confirmar_senha) {
    $_SESSION["msg"] = "As senhas não coincidem.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-cadastro.php");
    exit;
}

if ($tipo_usuario != "estudante" && $tipo_usuario != "colaborador") {
    $_SESSION["msg"] = "Tipo de usuário inválido.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-cadastro.php");
    exit;
}

require_once "conecta.php";

try {
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);

        $_SESSION["msg"] = "Este e-mail já está cadastrado.";
        $_SESSION["class"] = "erro";
        header("Location: resultado-cadastro.php");
        exit;
    }

    mysqli_stmt_close($stmt);

    $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, telefone, senha, tipo_usuario)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssss",
        $nome,
        $email,
        $telefone,
        $senha_criptografada,
        $tipo_usuario
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    $_SESSION["class"] = "sucesso";

    header("Location: resultado-cadastro.php");
    exit;

} catch (mysqli_sql_exception $e) {
    $_SESSION["msg"] = "Erro ao realizar o cadastro. Tente novamente.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-cadastro.php");
    exit;
}

?>