<?php

session_start();

// Apaga as variáveis da sessão
session_unset();

// Apaga o cookie da sessão no navegador
if (ini_get("session.use_cookies")) {
    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $parametros["path"],
        $parametros["domain"],
        $parametros["secure"],
        $parametros["httponly"]
    );
}

// Encerra a sessão
session_destroy();

// Volta para a página inicial como visitante
header("Location: index.php");
exit;

?>
