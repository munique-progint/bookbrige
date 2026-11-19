<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

// Verifica se o usuário é colaborador
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "colaborador") {
    header("Location: minha-conta.php");
    exit;
}

// Aceita somente requisições POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: cadastrar-conteudo.php");
    exit;
}

// Verifica os campos obrigatórios
if (
    !isset($_POST["nome_conteudo"]) ||
    !isset($_POST["categoria"]) ||
    !isset($_POST["nivel"]) ||
    !isset($_POST["descricao"]) ||
    !isset($_FILES["imagem"]) ||
    !isset($_FILES["arquivo_pdf"])
) {
    $_SESSION["msg"] = "Preencha todos os campos.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

$nome_conteudo = trim($_POST["nome_conteudo"]);
$categoria = trim($_POST["categoria"]);
$nivel = trim($_POST["nivel"]);
$descricao = trim($_POST["descricao"]);
$id_usuario = (int) $_SESSION["id_usuario"];

// Categorias permitidas
$categorias_permitidas = [
    "Artigo",
    "Apostila",
    "Mapa mental",
    "Lista de exercícios",
    "Resumo",
    "Simulados",
    "Slides",
    "Outros"
];

// Níveis permitidos
$niveis_permitidos = [
    "Ensino fundamental",
    "Ensino médio",
    "Ensino técnico",
    "Ensino superior",
    "Curso livre",
    "Outros"
];

// Verifica se os campos estão preenchidos
if (
    $nome_conteudo == "" ||
    $categoria == "" ||
    $nivel == "" ||
    $descricao == ""
) {
    $_SESSION["msg"] = "Preencha todos os campos.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

// Verifica os limites de caracteres
if (strlen($nome_conteudo) > 150 || strlen($descricao) > 2000) {
    $_SESSION["msg"] = "O nome ou a descrição ultrapassou o limite permitido.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

// Verifica categoria e nível
if (
    !in_array($categoria, $categorias_permitidas, true) ||
    !in_array($nivel, $niveis_permitidos, true)
) {
    $_SESSION["msg"] = "Categoria ou nível inválido.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

$imagem = $_FILES["imagem"];
$arquivo_pdf = $_FILES["arquivo_pdf"];

// Verifica o envio dos arquivos
if (
    $imagem["error"] != UPLOAD_ERR_OK ||
    $arquivo_pdf["error"] != UPLOAD_ERR_OK
) {
    $_SESSION["msg"] = "Ocorreu um erro ao enviar os arquivos.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

// Verifica os tamanhos
if (
    $imagem["size"] > 5 * 1024 * 1024 ||
    $arquivo_pdf["size"] > 20 * 1024 * 1024
) {
    $_SESSION["msg"] = "A imagem deve ter até 5 MB e o PDF até 20 MB.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

// Verifica o formato da imagem
$tipos_imagem = [
    "image/jpeg" => "jpg",
    "image/png" => "png"
];

$arquivo_imagem = finfo_open(FILEINFO_MIME_TYPE);
$tipo_imagem = finfo_file($arquivo_imagem, $imagem["tmp_name"]);
finfo_close($arquivo_imagem);

if (
    !isset($tipos_imagem[$tipo_imagem]) ||
    getimagesize($imagem["tmp_name"]) === false
) {
    $_SESSION["msg"] = "A capa deve ser uma imagem JPG ou PNG válida.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

// Verifica o formato do PDF
$arquivo_tipo_pdf = finfo_open(FILEINFO_MIME_TYPE);
$tipo_pdf = finfo_file($arquivo_tipo_pdf, $arquivo_pdf["tmp_name"]);
finfo_close($arquivo_tipo_pdf);

if ($tipo_pdf != "application/pdf") {
    $_SESSION["msg"] = "O arquivo do conteúdo deve ser um PDF válido.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

require_once "conecta.php";

// Pastas dos arquivos
$pasta_imagens = "uploads/conteudos/imagens/";
$pasta_pdfs = "uploads/conteudos/pdfs/";

// Prepara as pastas
if (!is_dir($pasta_imagens) && !mkdir($pasta_imagens, 0755, true)) {
    $_SESSION["msg"] = "Não foi possível preparar a pasta das imagens.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

if (!is_dir($pasta_pdfs) && !mkdir($pasta_pdfs, 0755, true)) {
    $_SESSION["msg"] = "Não foi possível preparar a pasta dos PDFs.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

// Gera nomes únicos para os arquivos
$nome_imagem = bin2hex(random_bytes(16)) . "." . $tipos_imagem[$tipo_imagem];
$nome_pdf = bin2hex(random_bytes(16)) . ".pdf";

$caminho_imagem = $pasta_imagens . $nome_imagem;
$caminho_pdf = $pasta_pdfs . $nome_pdf;

// Salva a imagem
if (!move_uploaded_file($imagem["tmp_name"], $caminho_imagem)) {
    $_SESSION["msg"] = "Não foi possível salvar a imagem.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

// Salva o PDF
if (!move_uploaded_file($arquivo_pdf["tmp_name"], $caminho_pdf)) {
    unlink($caminho_imagem);

    $_SESSION["msg"] = "Não foi possível salvar o PDF.";
    $_SESSION["class"] = "danger";
    header("Location: cadastrar-conteudo.php");
    exit;
}

// Cadastra o conteúdo no banco
try {

    $sql = "INSERT INTO conteudos
            (id_usuario, nome_conteudo, categoria, nivel, descricao, imagem, arquivo_pdf)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "issssss",
        $id_usuario,
        $nome_conteudo,
        $categoria,
        $nivel,
        $descricao,
        $caminho_imagem,
        $caminho_pdf
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Mensagem de sucesso
    $_SESSION["msg"] = "Conteúdo publicado com sucesso!";
    $_SESSION["class"] = "sucesso";

    // Redireciona para Minhas Publicações
    header("Location: minhas-publicacoes.php");
    exit;

} catch (mysqli_sql_exception $e) {

    // Remove os arquivos caso o cadastro falhe
    if (file_exists($caminho_imagem)) {
        unlink($caminho_imagem);
    }

    if (file_exists($caminho_pdf)) {
        unlink($caminho_pdf);
    }

    $_SESSION["msg"] = "Não foi possível publicar o conteúdo. Tente novamente.";
    $_SESSION["class"] = "danger";

    header("Location: cadastrar-conteudo.php");
    exit;
}

?>
