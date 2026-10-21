<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: alterar-senha.php");
    exit;
}

if (
    !isset($_POST["senhaAtual"]) ||
    !isset($_POST["novaSenha"]) ||
    !isset($_POST["confirmarSenha"])
) {
    $_SESSION["msg_senha"] = "Preencha todos os campos.";
    $_SESSION["class_senha"] = "erro";
    header("Location: alterar-senha.php");
    exit;
}

$senha_atual = $_POST["senhaAtual"];
$nova_senha = $_POST["novaSenha"];
$confirmar_senha = $_POST["confirmarSenha"];

if (
    $senha_atual == "" ||
    $nova_senha == "" ||
    $confirmar_senha == ""
) {
    $_SESSION["msg_senha"] = "Preencha todos os campos.";
    $_SESSION["class_senha"] = "erro";
    header("Location: alterar-senha.php");
    exit;
}

if (strlen($nova_senha) < 8) {
    $_SESSION["msg_senha"] = "A nova senha deve ter pelo menos 8 caracteres.";
    $_SESSION["class_senha"] = "erro";
    header("Location: alterar-senha.php");
    exit;
}

if ($nova_senha != $confirmar_senha) {
    $_SESSION["msg_senha"] = "A nova senha e a confirmação não coincidem.";
    $_SESSION["class_senha"] = "erro";
    header("Location: alterar-senha.php");
    exit;
}

require_once "conecta.php";

try {
    $id_usuario = $_SESSION["id_usuario"];

    $sql = "SELECT senha FROM usuarios WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

    if (!$usuario || !password_verify($senha_atual, $usuario["senha"])) {
        $_SESSION["msg_senha"] = "A senha atual está incorreta.";
        $_SESSION["class_senha"] = "erro";
        header("Location: alterar-senha.php");
        exit;
    }

    $senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios SET senha = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $senha_criptografada,
        $id_usuario
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION["msg_senha"] = "Senha alterada com sucesso!";
    $_SESSION["class_senha"] = "sucesso";

    header("Location: alterar-senha.php");
    exit;

} catch (mysqli_sql_exception $e) {
    $_SESSION["msg_senha"] = "Não foi possível alterar a senha. Tente novamente.";
    $_SESSION["class_senha"] = "erro";

    header("Location: alterar-senha.php");
    exit;
}

?>