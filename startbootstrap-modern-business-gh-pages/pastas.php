<?php
session_start();

require_once "conecta.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];

$sql = "SELECT pastas.nome_pasta,
               COUNT(materiais_salvos.id) AS quantidade
        FROM pastas
        LEFT JOIN materiais_salvos
            ON materiais_salvos.nome_pasta = pastas.nome_pasta
            AND materiais_salvos.id_usuario = pastas.id_usuario
        WHERE pastas.id_usuario = ?
        GROUP BY pastas.id, pastas.nome_pasta
        ORDER BY pastas.nome_pasta";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$pastas = [];

while ($pasta = mysqli_fetch_assoc($resultado)) {
    $pastas[] = $pasta;
}

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Área onde o usuário organiza seus materiais e conteúdos em pastas." />
    <meta name="author" content="BookBridge" />
    <title>Minhas Pastas - BookBridge</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">
    <main class="flex-shrink-0">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#FBDD91;">
            <div class="container px-5">
                <a class="navbar-brand fs-2 fw-bold" href="index.html" style="color:#DF8F9C;">BookBridge</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation"><span
                        class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="index.html">Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="conteudos.html">Conteúdos</a></li>
                        <li class="nav-item"><a class="nav-link" href="materiais.html">Doações</a></li>
                        <li class="nav-item"><a class="nav-link" href="minha-conta.html">Minha Conta</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Seção Minhas Pastas -->
        <section class="py-5 secao-favoritos" style="background-color:#FFF;">
            <div class="container px-5 my-3">
                <div class="row gx-5 justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold" style="color:#DF8F9C;">
                                Minhas Pastas
                            </h2>
                            <p class="text-center text-muted fs-5 mb-5">
                                Organize seus materiais em pastas para acessá-los rapidamente.
                            </p>

                            <div class="text-center mb-2">
                                <a href="#" class="btn rounded-pill px-4 py-2"
                                    style="background-color:#DF8F9C; color:#4E0119;" data-bs-toggle="modal"
                                    data-bs-target="#modalNovaPasta">

                                    <i class="bi bi-folder-plus"></i>
                                    Nova Pasta

                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<div class="row mt-4 gy-4">

    <?php if (count($pastas) > 0) { ?>

        <?php foreach ($pastas as $pasta) { ?>

            <div class="col-lg-10 col-xl-9 mx-auto">
                <div class="card shadow border-0 card-pasta"
                    style="width:100%; max-width:none;">

                    <div class="card-body d-flex justify-content-between align-items-center p-4">

                        <div>
                            <h4 class="mb-1" style="color:#4E0119;">
                                <i class="bi bi-folder-fill me-2"
                                    style="color:#DF8F9C;"></i>

                                <?php echo htmlspecialchars($pasta["nome_pasta"]); ?>
                            </h4>

                            <p class="text-muted mb-0">
                                <?php
                                echo (int) $pasta["quantidade"];
                                echo (int) $pasta["quantidade"] === 1
                                    ? " material"
                                    : " materiais";
                                ?>
                            </p>
                        </div>

   <div class="d-flex align-items-center gap-2">

    <a href="pastas-detalhes.php?pasta=<?php echo urlencode($pasta["nome_pasta"]); ?>"
        class="btn btn-abrir-pasta">
        Abrir
    </a>

    <form action="processa-exclusao-pasta.php" method="POST"
        onsubmit="return confirm('Tem certeza de que deseja excluir esta pasta e todos os materiais salvos nela?');">

        <input type="hidden" name="nome_pasta"
            value="<?php echo htmlspecialchars($pasta["nome_pasta"], ENT_QUOTES, 'UTF-8'); ?>">

        <button type="submit" class="btn btn-outline-danger">
            <i class="bi bi-trash"></i>
            Excluir pasta
        </button>

    </form>

</div>

                    </div>
                </div>
            </div>

        <?php } ?>

    <?php } else { ?>

        <div class="col-12 text-center py-5">
            <i class="bi bi-folder2-open"
                style="font-size:3rem; color:#DF8F9C;"></i>

            <h5 class="mt-3" style="color:#4E0119;">
                Você ainda não tem materiais salvos.
            </h5>

            <p class="text-muted">
                Explore as doações e adicione materiais às suas pastas.
            </p>

            <a href="materiais.php" class="btn rounded-pill px-4"
                style="background-color:#DF8F9C; color:#4E0119;">
                Explorar doações
            </a>
        </div>

    <?php } ?>

</div>


        </section>
    </main>

   <!-- Modal Nova Pasta -->
<div class="modal fade" id="modalNovaPasta" tabindex="-1"
    aria-labelledby="tituloNovaPasta" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form action="processa-criacao-pasta.php" method="POST">

                <div class="modal-header">

                    <h5 class="modal-title" id="tituloNovaPasta">
                        Nova Pasta
                    </h5>

                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Fechar">
                    </button>

                </div>

                <div class="modal-body">

                    <label for="nome_pasta" class="form-label">
                        Nome da pasta
                    </label>

                    <input type="text"
                        name="nome_pasta"
                        id="nome_pasta"
                        class="form-control"
                        placeholder="Digite o nome da pasta"
                        maxlength="100"
                        required>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn"
                        style="background:#DF8F9C; color:#4E0119;">
                        Criar
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


    <!-- Footer-->
    <footer class="py-4 mt-auto" style="background-color:#4E0119;">
        <div class="container px-5">
            <div class="row align-items-center justify-content-between flex-column flex-sm-row">
                <div class="col-auto">
                    <div class="small m-0 text-white">© 2026 BookBridge - Compartilhando conhecimento</div>
                </div>

            </div>
        </div>
    </footer>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>

</body>

</html>