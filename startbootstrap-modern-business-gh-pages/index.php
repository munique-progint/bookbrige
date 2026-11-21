<?php
session_start();

$logado = isset($_SESSION["id_usuario"]);
?>
<!DOCTYPE html>
<html lang="pt-br"></html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Plataforma de compartilhamento de materiais de estudo e conteúdos educacionais" />
    <meta name="author" content="BookBridge" />

    <title>Home - BookBridge</title>

    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">

    <main class="flex-shrink-0">

        <!-- Navegação-->
        <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#FBDD91;">
            <div class="container px-5">

                <a class="navbar-brand fs-2 fw-bold" href="index.php" style="color:#DF8F9C;">
                    BookBridge
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Início</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="conteudos.php">Conteúdos</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="materiais.php">Doações</a>
                        </li>

                        <?php if ($logado) { ?>

                            <li class="nav-item">
                                <a class="nav-link" href="minha-conta.php">Minha Conta</a>
                            </li>

                        <?php } else { ?>

                            <li class="nav-item">
                                <a class="nav-link" href="login.html">Entrar</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="cadastro.html">Cadastre-se</a>
                            </li>

                        <?php } ?>

                    </ul>

                </div>
            </div>
        </nav>

        <!-- Header-->
        <header class="py-5 hero-bookbridge">
            <div class="container px-5">

                <div class="row gx-5 align-items-center justify-content-center">

                    <div class="col-lg-8 col-xl-7 col-xxl-6">

                        <div class="my-5 text-center text-xl-start">

                            <h1 class="display-5 fw-bolder mb-2" style="color:#4E0119;">
                                COMPARTILHE
                                CONHECIMENTO.<br>TRANSFORME VIDAS.
                            </h1>

                            <p class="lead fw-normal mb-4" style="color:#6B5E54;">
                                Encontre materiais de estudo, doe
                                materiais que você não utiliza mais e compartilhe conhecimento. Juntos, tornamos a
                                educação mais acessível para todos.
                            </p>

                            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center justify-content-xl-start">

                                <a class="btn btn-lg px-4 me-sm-3 botao-doacao"
                                    href="<?php echo $logado ? 'doar-material.html' : 'login.html'; ?>">
                                    Faça uma doação
                                </a>

                                <a class="btn btn-lg px-4 botao-explorar" href="materiais.php">
                                    Explore materiais
                                </a>

                            </div>

                        </div>
                    </div>

                    <div class="col-xl-5 col-xxl-6 d-none d-xl-block text-center">
                        <img class="img-fluid rounded-3 my-5"
                            src="assets/img/foto-home.png" alt="Ilustração de um estudante" />
                    </div>

                </div>
            </div>
        </header>

        <!-- Features section-->
        <section class="py-5" id="features">

            <div class="container px-5 my-5">

                <div class="row gx-5">

                    <div class="col-lg-4 mb-5 mb-lg-0">
                        <h2 class="fw-bolder mb-0">Como funciona o BookBridge?</h2>
                    </div>

                    <div class="col-lg-8">

                        <div class="row gx-5 row-cols-1 row-cols-md-2">

                            <div class="col mb-5 h-100">
                                <div class="feature rounded-3 mb-3">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>

                                <h2 class="h5">Acesse Conteúdos Digitais</h2>

                                <p class="mb-0">
                                    Acesse apostilas, resumos, listas de exercícios e simulados publicados
                                    pelos colaboradores da plataforma.
                                </p>
                            </div>

                            <div class="col mb-5 h-100">
                                <div class="feature rounded-3 mb-3">
                                    <i class="bi bi-book"></i>
                                </div>

                                <h2 class="h5">Doe Materiais</h2>

                                <p class="mb-0">
                                    Doe livros, apostilas e outros materiais que você não utiliza mais e
                                    ajude outros estudantes a continuarem aprendendo.
                                </p>
                            </div>

                            <div class="col mb-5 mb-md-0 h-100">
                                <div class="feature rounded-3 mb-3">
                                    <i class="bi bi-search"></i>
                                </div>

                                <h2 class="h5">Encontre Materiais</h2>

                                <p class="mb-0">
                                    Busque livros, apostilas e conteúdos digitais disponíveis para facilitar
                                    seus estudos.
                                </p>
                            </div>

                            <div class="col h-100">
                                <div class="feature rounded-3 mb-3">
                                    <i class="bi bi-people"></i>
                                </div>

                                <h2 class="h5">Torne-se um Colaborador</h2>

                                <p class="mb-0">
                                    Torne-se um colaborador e publique materiais educacionais, compartilhe
                                    conhecimento e ajude estudantes de todo o país a aprender mais.
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Nossa missão -->
        <div class="py-5 bg-light">

            <div class="container px-5 my-5">

                <div class="row gx-5 justify-content-center">

                    <div class="col-lg-10 col-xl-7">

                        <div class="text-center">

                            <h2 class="fw-bold mb-3" style="color:#4E0119;">
                                Nossa missão
                            </h2>

                            <div class="fs-4 fst-italic lh-lg" style="color:#4E0119;">
                                "Assim como uma ponte conecta diferentes caminhos, o BookBridge conecta estudantes,
                                conhecimentos e oportunidades, aproximando pessoas e tornando o aprendizado mais
                                acessível para todos."
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Materiais em destaque -->
        <section class="py-5">

            <div class="container px-5 my-5">

                <div class="row gx-5 justify-content-center">

                    <div class="col-lg-8 col-xl-6">

                        <div class="text-center">

                            <h2 class="fw-bolder" style="color:#4E0119;">
                                Materiais em destaque
                            </h2>

                            <p class="lead fw-normal text-muted mb-5">
                                Confira alguns dos materiais disponíveis na plataforma.
                            </p>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row gx-4 justify-content-center">

                <?php
                $materiais_destaque = [
                    ["fotolivromat.jpg", "Livro", "Matemática Básica", "físico"],
                    ["fotopdfprogramacao.jpg", "Resumo", "Introdução à Programação", "digital"],
                    ["fotoenemapostila.jpg", "Apostila", "Redação para ENEM", "físico"],
                    ["fotolivrobiologia.jpg", "Livro", "Biologia: Ensino médio", "físico"],
                    ["fotopdfhistoria.jpg", "Apostila", "História Geral: Brasil e Mundo", "digital"],
                    ["fotofichario.jpg", "Material escolar", "Fichário de Resumos e Anotações", "físico"],
                    ["fotocalculadoracientifica.jpg", "Material escolar", "Calculadora Científica", "físico"],
                    ["fotolivrodefisica.jpg", "Livro", "Exercícios de Física", "físico"]
                ];

                foreach ($materiais_destaque as $material) {
                ?>

                    <div class="col-lg-3 col-md-6 mb-4 d-flex justify-content-center">

                        <div class="card shadow border-0">

                            <img class="card-img-top imagem-material"
                                src="assets/img/<?php echo htmlspecialchars($material[0]); ?>"
                                alt="<?php echo htmlspecialchars($material[2]); ?>">

                            <div class="card-body p-4">

                                <div class="badge rounded-pill mb-2"
                                    style="background-color:#FBDD91; color:#4E0119;">

                                    <?php echo htmlspecialchars($material[1]); ?>

                                </div>

                                <h5 class="card-title mb-3">
                                    <?php echo htmlspecialchars($material[2]); ?>
                                </h5>

                                <p class="text-muted mb-3">
                                    Tipo: <?php echo htmlspecialchars($material[3]); ?>
                                </p>

                                <a href="materiais.php" class="btn"
                                    style="background-color:#4E0119; color:white;">
                                    Ver materiais
                                </a>

                            </div>
                        </div>
                    </div>

                <?php } ?>

            </div>

            <!-- Chamar para ação-->
            <aside class="rounded-3 p-3 p-sm-4 mt-5" style="background-color:#FBDD91;">

                <div class="d-flex align-items-center justify-content-between flex-column flex-xl-row text-center text-xl-start">

                    <div class="mb-4 mb-xl-0">

                        <div class="fs-3 fw-bold" style="color:#4E0119;">
                            Doe materiais e transforme vidas.
                        </div>

                        <div style="color:#6B5E54;">
                            Compartilhe livros e materiais que podem ajudar outros estudantes.
                        </div>

                    </div>

                    <div class="ms-xl-4">

                        <a href="<?php echo $logado ? 'doar-material.html' : 'login.html'; ?>"
                            class="btn px-4 py-2 fw-bold rounded-pill"
                            style="background-color:#DF8F9C; color:#4E0119;">

                            Faça uma doação

                        </a>

                    </div>

                </div>
            </aside>

        </section>

    </main>

    <!-- Footer-->
    <footer class="py-4 mt-auto" style="background-color:#4E0119;">

        <div class="container px-5">

            <div class="row align-items-center justify-content-between flex-column flex-sm-row">

                <div class="col-auto">
                    <div class="small m-0 text-white">
                        © 2026 BookBridge - Compartilhando conhecimento
                    </div>
                </div>

            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>

</body>
</html>
