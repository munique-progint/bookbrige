<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

require_once "conecta.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"]) || (int) $_GET["id"] <= 0) {
    header("Location: conteudos.php");
    exit;
}

$id_conteudo = (int) $_GET["id"];
$id_usuario_logado = (int) $_SESSION["id_usuario"];

$sql = "SELECT conteudos.id, conteudos.nome_conteudo,
               conteudos.categoria, conteudos.area, conteudos.nivel,
               conteudos.descricao, conteudos.imagem,
               conteudos.arquivo_pdf, usuarios.nome
        FROM conteudos
        INNER JOIN usuarios ON conteudos.id_usuario = usuarios.id
        WHERE conteudos.id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Não foi possível consultar o conteúdo.");
}

mysqli_stmt_bind_param($stmt, "i", $id_conteudo);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$conteudo = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

if (!$conteudo) {
    header("Location: conteudos.php");
    exit;
}

// Busca as pastas do estudante
$pastas = [];

$sql = "SELECT nome_pasta
        FROM pastas
        WHERE id_usuario = ?
        ORDER BY nome_pasta";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario_logado);
mysqli_stmt_execute($stmt);

$resultado_pastas = mysqli_stmt_get_result($stmt);

while ($pasta = mysqli_fetch_assoc($resultado_pastas)) {
    $pastas[] = $pasta;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Página de detalhes de um conteúdo digital compartilhado no BookBridge.">
    <meta name="author" content="BookBridge">

    <title>Detalhes do Conteúdo - BookBridge</title>

    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Navegação-->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#FBDD91;">
        <div class="container px-5">
            <a class="navbar-brand fs-2 fw-bold" href="index.php" style="color:#DF8F9C;">BookBridge</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="conteudos.php">Conteúdos</a></li>
                    <li class="nav-item"><a class="nav-link" href="materiais.php">Doações</a></li>
                    <li class="nav-item"><a class="nav-link" href="minha-conta.php">Minha Conta</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal -->
    <main class="flex-shrink-0">
        <section class="py-5 detalhes-conteudo-section">
            <div class="container px-5">
                <div class="row gx-5 align-items-start">

                    <!-- Imagem do conteúdo -->
                    <div class="col-lg-4 mb-4">
                        <img
                            src="<?php echo htmlspecialchars($conteudo['imagem'], ENT_QUOTES, 'UTF-8'); ?>"
                            class="img-fluid shadow-lg rounded-4"
                            style="max-height: 450px; width: 100%; object-fit: cover;"
                            alt="<?php echo htmlspecialchars($conteudo['nome_conteudo'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <!-- Informações do conteúdo -->
                    <div class="col-lg-8">
                        <div class="bg-white detalhes-conteudo-card rounded-4 p-5">

                            <h2 class="fw-bold" style="color:#4E0119;">
                                <?php echo htmlspecialchars($conteudo['nome_conteudo'], ENT_QUOTES, 'UTF-8'); ?>
                            </h2>

                            <span class="badge rounded-pill mb-3"
                                style="background-color:#FBDD91; color:#4E0119;">
                                <?php echo htmlspecialchars($conteudo['categoria'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>

                            <p class="mb-4">
                                <i class="bi bi-person-fill" style="color:#4E0119;"></i>
                                <strong>Compartilhado por:</strong>
                                <?php echo htmlspecialchars($conteudo['nome'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>

                            <hr>

                            <h6 class="fw-bold">Descrição</h6>

                            <p class="text-muted mb-4" style="white-space: pre-line;">
                                <?php echo htmlspecialchars($conteudo['descricao'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>

                            <hr>

                            <h6 class="fw-bold mb-3">Informações do conteúdo</h6>

                            <div class="row">

                                <div class="col-12 mb-3">
                                    <small class="text-muted">Área</small><br>
                                    <?php echo htmlspecialchars($conteudo['area'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </div>

                                <div class="col-12 mb-3">
                                    <small class="text-muted">Nível de ensino</small><br>
                                    <?php echo htmlspecialchars($conteudo['nivel'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>

                            </div>

                            <div class="d-flex flex-wrap gap-3 mt-4">

                                <a
                                    href="<?php echo htmlspecialchars($conteudo['arquivo_pdf'], ENT_QUOTES, 'UTF-8'); ?>"
                                    class="btn px-4 py-2 rounded-pill fw-bold"
                                    style="background-color:#DF8F9C; color:#4E0119;"
                                    target="_blank"
                                    rel="noopener">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    Abrir PDF
                                </a>

                                <button type="button"
                                    class="btn px-4 py-2 rounded-pill fw-bold"
                                    style="background-color:#DF8F9C; color:#4E0119;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAdicionarPasta">
                                    <i class="bi bi-folder-plus"></i>
                                    Adicionar à pasta
                                </button>

                                <a
                                    href="conteudos.php"
                                    class="btn px-4 py-2 rounded-pill fw-bold"
                                    style="background-color:#FBDD91; color:#4E0119;">
                                    <i class="bi bi-arrow-left"></i>
                                    Voltar aos conteúdos
                                </a>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <!-- Modal Adicionar à Pasta -->
    <div class="modal fade" id="modalAdicionarPasta" tabindex="-1"
        aria-labelledby="modalAdicionarPastaLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalAdicionarPastaLabel">
                        Adicionar à pasta
                    </h5>

                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Fechar">
                    </button>
                </div>

                <form action="processa-material-salvo.php" method="POST">

                    <div class="modal-body">

                        <input type="hidden" name="id_conteudo"
                            value="<?php echo (int) $conteudo['id']; ?>">

                        <?php if (count($pastas) > 0) { ?>

                            <p class="mb-3">Selecione uma pasta:</p>

                            <?php foreach ($pastas as $indice => $pasta) { ?>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio"
                                        name="nome_pasta"
                                        id="pasta<?php echo $indice; ?>"
                                        value="<?php echo htmlspecialchars($pasta["nome_pasta"], ENT_QUOTES, 'UTF-8'); ?>"
                                        required>

                                    <label class="form-check-label"
                                        for="pasta<?php echo $indice; ?>">
                                        <?php echo htmlspecialchars($pasta["nome_pasta"]); ?>
                                    </label>
                                </div>

                            <?php } ?>

                        <?php } else { ?>

                            <p class="text-muted">
                                Você ainda não possui pastas.
                                Acesse Minha Conta e abra a área de pastas para criar uma.
                            </p>

                        <?php } ?>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <?php if (count($pastas) > 0) { ?>

                            <button type="submit" class="btn"
                                style="background-color:#DF8F9C; color:#4E0119;">
                                Adicionar
                            </button>

                        <?php } else { ?>

                            <a href="pastas.php" class="btn"
                                style="background-color:#DF8F9C; color:#4E0119;">
                                Criar pasta
                            </a>

                        <?php } ?>

                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Footer -->
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
