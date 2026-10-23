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

$id_usuario = $_SESSION["id_usuario"];

$sql = "SELECT id, nome_conteudo, categoria, nivel, descricao, imagem, arquivo_pdf
        FROM conteudos
        WHERE id_usuario = ?
        ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Não foi possível consultar suas publicações.");
}

mysqli_stmt_bind_param($stmt, "i", $id_usuario);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    die("Não foi possível consultar suas publicações.");
}

$resultado = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Área onde o colaborador gerencia suas publicações digitais." />
    <meta name="author" content="BookBridge" />
    <title>Minhas Publicações - BookBridge</title>
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
                        <li class="nav-item"><a class="nav-link" href="conteudos.php">Conteúdos</a></li>
                        <li class="nav-item"><a class="nav-link" href="materiais.php">Doações</a></li>
                        <li class="nav-item"><a class="nav-link" href="minha-conta.php">Minha Conta</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Minhas Publicações -->
        <section class="py-5">
            <div class="container px-5 my-3">
                <div class="row gx-5 justify-content-center">
                    <div class="col-12">
                        <div class="text-center">
                            <h2 class="fw-bolder" style="color:#4E0119;">
                                Minhas Publicações
                            </h2>

                            <p class="lead fw-normal text-muted mb-5">
                                Gerencie os materias digitais publicados por você.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-start mt-3" style="margin-left: 170px;">
                <a href="cadastrar-conteudo.html" class="btn px-4 py-2 rounded-pill fw-bold mt-2"
                    style="background-color:#DF8F9C; color:#4E0119;">
                    + Nova publicação
                </a>
            </div>
           
<div class="row gx-4 justify-content-center mt-3"
    style="width: 100%; max-width: 1700px; margin: 1rem auto 0;">

    <?php if (mysqli_num_rows($resultado) > 0) { ?>

        <?php while ($publicacao = mysqli_fetch_assoc($resultado)) { ?>

            <div class="col-lg-3 col-md-6 mb-4 d-flex justify-content-center">

                <div class="card shadow border-0">

                    <img
                        class="card-img-top imagem-material"
                        src="<?php echo htmlspecialchars($publicacao["imagem"], ENT_QUOTES, "UTF-8"); ?>"
                        alt="<?php echo htmlspecialchars($publicacao["nome_conteudo"], ENT_QUOTES, "UTF-8"); ?>"
                    >

                    <div class="card-body p-4">

                        <div
                            class="badge rounded-pill mb-2"
                            style="background-color: #FBDD91; color: #4E0119;"
                        >
                            <?php echo htmlspecialchars($publicacao["categoria"], ENT_QUOTES, "UTF-8"); ?>
                        </div>

                        <h5 class="card-title mb-3">
                            <?php echo htmlspecialchars($publicacao["nome_conteudo"], ENT_QUOTES, "UTF-8"); ?>
                        </h5>

                        <p class="text-muted mb-2">
                            Nível:
                            <?php echo htmlspecialchars($publicacao["nivel"], ENT_QUOTES, "UTF-8"); ?>
                        </p>

                        <p class="card-text">
                            <?php
                            $descricao = $publicacao["descricao"];

                            if (strlen($descricao) > 100) {
                                $descricao = substr($descricao, 0, 100) . "...";
                            }

                            echo htmlspecialchars($descricao, ENT_QUOTES, "UTF-8");
                            ?>
                        </p>

                        <div class="d-flex flex-wrap gap-2 mt-3">

                            <a
                                href="detalhes-conteudo.php?id=<?php echo (int) $publicacao["id"]; ?>"
                                class="btn"
                                style="background-color: #4E0119; color: white;"
                            >
                                Ver detalhes
                            </a>

                          <a
    href="editar-publicacao.php?id=<?php echo (int) $publicacao["id"]; ?>"
    class="btn"
    style="background-color: #FBDD91; color: #4E0119;"
>
    Editar
</a>
                        </div>

                    </div>

                </div>

            </div>

        <?php } ?>

    <?php } else { ?>

        <div class="col-12 text-center mt-4">
            <p class="text-muted">
                Você ainda não publicou conteúdos.
            </p>
        </div>

    <?php } ?>

</div>

            </div>
        </section>
    </main>

    <!-- Modal Excluir Publicação -->
    <div class="modal fade" id="modalExcluirPublicacao" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Excluir publicação
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <p class="fw-semibold mb-2">
                        Tem certeza que deseja excluir esta publicação?
                    </p>

                    <p class="text-muted mb-2">
                        Esta ação removerá permanentemente esta publicação e não poderá ser desfeita.
                    </p>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="button" class="btn btn-danger">

                        Excluir

                    </button>

                </div>
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