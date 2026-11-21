<?php

session_start();

// Verifica se o usuário está logado.
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

// Aceita somente requisições POST.
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: meus-dados.php");
    exit;
}

require_once "conecta.php";

$id_usuario = (int) $_SESSION["id_usuario"];

try {

    // Iniciar transação.
    mysqli_begin_transaction($conn);

    // Excluir favoritos do próprio usuário.
    $sql = "DELETE FROM favoritos
            WHERE id_usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Excluir favoritos relacionados aos conteúdos publicados pelo usuário.
    $sql = "DELETE favoritos
            FROM favoritos
            INNER JOIN conteudos
            ON favoritos.id_conteudo = conteudos.id
            WHERE conteudos.id_usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Excluir favoritos relacionados aos materiais publicados pelo usuário.
    $sql = "DELETE favoritos
            FROM favoritos
            INNER JOIN materiais
            ON favoritos.id_material = materiais.id
            WHERE materiais.id_usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Excluir materiais salvos pelo usuário.
    $sql = "DELETE FROM materiais_salvos
            WHERE id_usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Excluir materiais salvos relacionados aos materiais publicados pelo usuário.
    $sql = "DELETE materiais_salvos
            FROM materiais_salvos
            INNER JOIN materiais
            ON materiais_salvos.id_material = materiais.id
            WHERE materiais.id_usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Excluir pastas do usuário.
    $sql = "DELETE FROM pastas
            WHERE id_usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Excluir conteúdos publicados pelo usuário.
    $sql = "DELETE FROM conteudos
            WHERE id_usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Excluir materiais cadastrados pelo usuário.
    $sql = "DELETE FROM materiais
            WHERE id_usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Excluir a conta do usuário.
    $sql = "DELETE FROM usuarios
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Confirmar todas as exclusões.
    mysqli_commit($conn);

    // Encerrar a sessão do usuário.
    session_unset();
    session_destroy();

    header("Location: index.php");
    exit;

} catch (mysqli_sql_exception $e) {

    // Desfazer as alterações caso alguma exclusão falhe.
    mysqli_rollback($conn);

    $_SESSION["msg"] = "Não foi possível excluir a conta. Tente novamente.";
    $_SESSION["class"] = "erro";

    header("Location: meus-dados.php");
    exit;
}

?>
