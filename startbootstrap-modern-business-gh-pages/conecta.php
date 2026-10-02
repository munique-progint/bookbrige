<?php

try {
    $conn = mysqli_connect("mysql", "root", "1234", "bookbridge");
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Erro ao conectar com o banco de dados");
}

?>