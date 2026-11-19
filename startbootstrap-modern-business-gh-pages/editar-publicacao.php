<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "colaborador") {
    header("Location: conteudos.php");
    exit;
}

require_once "conecta.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"]) || (int) $_GET["id"] <= 0) {
    header("Location: minhas-publicacoes.php");
    exit;
}

$id_conteudo = (int) $_GET["id"];
$id_usuario = (int) $_SESSION["id_usuario"];

$sql = "SELECT id, nome_conteudo, categoria, nivel, descricao, imagem, arquivo_pdf
        FROM conteudos
        WHERE id = ? AND id_usuario = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Não foi possível consultar a publicação.");
}

mysqli_stmt_bind_param($stmt, "ii", $id_conteudo, $id_usuario);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    die("Não foi possível consultar a publicação.");
}

$resultado = mysqli_stmt_get_result($stmt);
$publicacao = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

if (!$publicacao) {
    header("Location: minhas-publicacoes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Página para editar uma publicação de conteúdo digital no BookBridge.">
    <meta name="author" content="BookBridge">

    <title>Editar Publicação - BookBridge</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">

</head>

<body style="background-color:#FFF;">

    <!-- Navegação-->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#FBDD91;">
        <div class="container px-5">
            <a class="navbar-brand fs-2 fw-bold" href="index.html" style="color:#DF8F9C;">BookBridge</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.html">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="conteudos.php">Conteúdos</a></li>
                    <li class="nav-item"><a class="nav-link" href="materiais.php">Doações</a></li>
                    <li class="nav-item"><a class="nav-link" href="minha-conta.php">Minha Conta</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5 mb-5">
        <div class="container px-5" style="margin-bottom: 150px;">

            <div class="text-center mb-5">

                <div class="text-start mb-4">

                    <a href="minhas-publicacoes.php" class="text-decoration-none"
                        style="color:#4E0119; font-weight:600;">

                        <i class="bi bi-arrow-left"></i>
                        Voltar para Minhas Publicações

                    </a>

                </div>

                <h2 class="fw-bolder" style="color:#DF8F9C;">
                    Editar Publicação
                </h2>

                <p class="fs-4">
                    Edite as informações do seu material digital.
                </p>

            </div>


            <div class="row justify-content-center">

                <div class="col-lg-8">

                    
<div class="card editar-publicacao-card p-5 mx-auto" style="max-width: 700px; width: 100%;">

    <form action="processa-edicao-publicacao.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id_conteudo" value="<?php echo $id_conteudo; ?>">

        <label class="mb-2 fw-bold fs-5">
            Nome do material
        </label>

        <input
            type="text"
            name="nome_conteudo"
            class="form-control fs-5 mb-4"
            value="<?php echo htmlspecialchars($publicacao["nome_conteudo"], ENT_QUOTES, "UTF-8"); ?>"
            maxlength="150"
            required
        >

        <label class="mb-2 fw-bold fs-5">
            Categoria
        </label>

        <select name="categoria" class="form-control fs-5 mb-4" required>

            <?php
            $categorias = [
                "Artigo",
                "Apostila",
                "Mapa mental",
                "Lista de exercícios",
                "Resumo",
                "Simulados",
                "Slides",
                "Outros"
            ];

            foreach ($categorias as $categoria) {
            ?>

                <option
                    value="<?php echo htmlspecialchars($categoria, ENT_QUOTES, "UTF-8"); ?>"
                    <?php echo $publicacao["categoria"] == $categoria ? "selected" : ""; ?>
                >
                    <?php echo htmlspecialchars($categoria, ENT_QUOTES, "UTF-8"); ?>
                </option>

            <?php } ?>

        </select>

        <label class="mb-2 fw-bold fs-5">
            Nível de ensino
        </label>

        <select name="nivel" class="form-control fs-5 mb-4" required>

            <?php
            $niveis = [
                "Ensino fundamental",
                "Ensino médio",
                "Ensino técnico",
                "Ensino superior",
                "Curso livre",
                "Outros"
            ];

            foreach ($niveis as $nivel) {
            ?>

                <option
                    value="<?php echo htmlspecialchars($nivel, ENT_QUOTES, "UTF-8"); ?>"
                    <?php echo $publicacao["nivel"] == $nivel ? "selected" : ""; ?>
                >
                    <?php echo htmlspecialchars($nivel, ENT_QUOTES, "UTF-8"); ?>
                </option>

            <?php } ?>

        </select>

        <label class="mb-2 fw-bold fs-5">
            Descrição
        </label>

        <textarea
            name="descricao"
            class="form-control fs-5 mb-4"
            rows="4"
            maxlength="2000"
            required
        ><?php echo htmlspecialchars($publicacao["descricao"], ENT_QUOTES, "UTF-8"); ?></textarea>

        <label class="mb-2 fw-bold fs-5">
            Imagem atual da capa
        </label>

        <div class="text-center mb-4">

            <img
                src="<?php echo htmlspecialchars($publicacao["imagem"], ENT_QUOTES, "UTF-8"); ?>"
                class="img-fluid rounded shadow"
                style="max-width:220px;"
                alt="Capa da publicação"
            >

        </div>

        <label class="mb-2 fw-bold">
            Alterar imagem da capa
        </label>

        <input
            type="file"
            name="imagem"
            class="form-control mb-2"
            accept=".jpg,.jpeg,.png"
        >

        <small class="text-muted">
            Se não selecionar outra imagem, a atual será mantida.
        </small>

        <hr class="my-4">

        <label class="mb-2 fw-bold fs-5">
            Arquivo PDF atual
        </label>

        <p class="text-muted mb-3">
            <i class="bi bi-file-earmark-pdf-fill"></i>

            <a
                href="<?php echo htmlspecialchars($publicacao["arquivo_pdf"], ENT_QUOTES, "UTF-8"); ?>"
                target="_blank"
            >
                <?php echo htmlspecialchars(basename($publicacao["arquivo_pdf"]), ENT_QUOTES, "UTF-8"); ?>
            </a>
        </p>

        <label class="mb-2 fw-bold">
            Alterar arquivo PDF
        </label>

        <input
            type="file"
            name="arquivo_pdf"
            class="form-control mb-2"
            accept=".pdf"
        >

        <small class="text-muted">
            Se não selecionar outro PDF, o atual será mantido.
        </small>

        <button
            type="submit"
            class="btn px-4 py-2 rounded-pill fw-bold mt-4"
            style="background-color:#4E0119; color:white;"
        >
            Salvar Alterações
        </button>

    </form>

</div>

                </div>
            </div>
        </div>

    </section>

    <footer class="py-4 mt-auto" style="background-color:#4E0119;">
        <div class="container px-5">
            <div class="row align-items-center justify-content-between flex-column flex-sm-row">
                <div class="col-auto">
                    <div class="small m-0 text-white">© 2026 BookBridge - Compartilhando conhecimento</div>
                </div>

            </div>
        </div>
    </footer>

    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>