<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.html");
    exit;
}

if (!isset($_POST["email"]) || !isset($_POST["senha"])) {
    $_SESSION["msg"] = "Preencha o e-mail e a senha.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-login.php");
    exit;
}

$email = trim($_POST["email"]);
$senha = $_POST["senha"];

if ($email == "" || $senha == "") {
    $_SESSION["msg"] = "Preencha o e-mail e a senha.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-login.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["msg"] = "Informe um e-mail válido.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-login.php");
    exit;
}

require_once "conecta.php";

try {

    $sql = "SELECT id, nome, email, senha, tipo_usuario FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 0) {
        mysqli_stmt_close($stmt);

        $_SESSION["msg"] = "E-mail ou senha incorretos.";
        $_SESSION["class"] = "erro";
        header("Location: resultado-login.php");
        exit;
    }

    mysqli_stmt_bind_result(
        $stmt,
        $id,
        $nome,
        $email_usuario,
        $senha_armazenada,
        $tipo_usuario
    );

    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!password_verify($senha, $senha_armazenada)) {
        $_SESSION["msg"] = "E-mail ou senha incorretos.";
        $_SESSION["class"] = "erro";
        header("Location: resultado-login.php");
        exit;
    }

$_SESSION["id_usuario"] = $id;
$_SESSION["nome_usuario"] = $nome;
$_SESSION["email_usuario"] = $email_usuario;
$_SESSION["tipo_usuario"] = $tipo_usuario;

header("Location: minha-conta.php");
exit;

} catch (mysqli_sql_exception $e) {

    $_SESSION["msg"] = "Erro ao realizar o login. Tente novamente.";
    $_SESSION["class"] = "erro";
    header("Location: resultado-login.php");
    exit;
}

?>