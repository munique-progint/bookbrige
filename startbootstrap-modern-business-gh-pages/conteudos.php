<?php
session_start();

require_once "conecta.php";

$pesquisa = trim($_GET["pesquisa"] ?? "");
$categoria = trim($_GET["categoria"] ?? "");
$nivel = trim($_GET["nivel"] ?? "");

$sql = "SELECT conteudos.id, conteudos.nome_conteudo,
               conteudos.categoria, conteudos.nivel,
               conteudos.descricao, conteudos.imagem,
               conteudos.arquivo_pdf, usuarios.nome
        FROM conteudos
        INNER JOIN usuarios ON conteudos.id_usuario = usuarios.id
        WHERE 1 = 1";

$parametros = [];
$tipos = "";

if ($pesquisa != "") {
    $sql .= " AND conteudos.nome_conteudo LIKE ?";
    $parametros[] = "%" . $pesquisa . "%";
    $tipos .= "s";
}

if ($categoria != "") {
    $sql .= " AND conteudos.categoria = ?";
    $parametros[] = $categoria;
    $tipos .= "s";
}

if ($nivel != "") {
    $sql .= " AND conteudos.nivel = ?";
    $parametros[] = $nivel;
    $tipos .= "s";
}

$sql .= " ORDER BY conteudos.id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Não foi possível consultar os conteúdos.");
}

if (!empty($parametros)) {
    mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
}

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    die("Não foi possível consultar os conteúdos.");
}

$resultado = mysqli_stmt_get_result($stmt);
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Página para explorar conteúdos digitais compartilhados no BookBridge.">
    <meta name="author" content="BookBridge">

    <title>Conteúdos - BookBridge </title>

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

        <!-- Navegação-->
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
                       <?php if (isset($_SESSION["id_usuario"])) { ?>
    <li class="nav-item">
        <a class="nav-link" href="minha-conta.php">Minha Conta</a>
    </li>
<?php } else { ?>
    <li class="nav-item">
        <a class="nav-link" href="login.php">Entrar</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="cadastro.php">Cadastre-se</a>
    </li>
<?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Blog preview section-->
        <section class="py-5">
            <div class="container px-5 my-3">
                <div class="row gx-5 justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="text-center">
                            <h2 class="fw-bolder" style="color:#4E0119;">
                                Conteúdos Digitais
                            </h2>

                            <p class="lead fw-normal text-muted mb-5">
                                Acesse resumos, apostilas em PDF, listas de exercícios e outros conteúdos digitais
                                compartilhados pela comunidade.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

           <!-- Filtros -->
<div class="text-center mb-5">

    <form action="conteudos.php" method="GET">

        <!-- Pesquisa -->
        <input
            class="form-control mx-auto mb-3"
            style="max-width: 700px; height: 45px;"
            type="text"
            name="pesquisa"
            placeholder="Pesquisar conteúdo"
            value="<?php echo htmlspecialchars($pesquisa); ?>"
        >

        <div class="row justify-content-center g-3 mt-3 mb-4">

            <!-- Categoria -->
            <div class="col-auto">

                <select
                    class="form-select"
                    name="categoria"
                    style="width: 210px; height: 50px;"
                >
                    <option value="">Todas as categorias</option>

                    <option value="Artigo" <?php if ($categoria == "Artigo") echo "selected"; ?>>
                        Artigo
                    </option>

                    <option value="Apostila" <?php if ($categoria == "Apostila") echo "selected"; ?>>
                        Apostila
                    </option>

                    <option value="Mapa mental" <?php if ($categoria == "Mapa mental") echo "selected"; ?>>
                        Mapa mental
                    </option>

                    <option value="Lista de exercícios" <?php if ($categoria == "Lista de exercícios") echo "selected"; ?>>
                        Lista de exercícios
                    </option>

                    <option value="Resumo" <?php if ($categoria == "Resumo") echo "selected"; ?>>
                        Resumo
                    </option>

                    <option value="Simulados" <?php if ($categoria == "Simulados") echo "selected"; ?>>
                        Simulados
                    </option>

                    <option value="Slides" <?php if ($categoria == "Slides") echo "selected"; ?>>
                        Slides
                    </option>

                    <option value="Outros" <?php if ($categoria == "Outros") echo "selected"; ?>>
                        Outros
                    </option>

                </select>

            </div>

            <!-- Nível de ensino -->
            <div class="col-auto">

                <select
                    class="form-select"
                    name="nivel"
                    style="width: 230px; height: 50px;"
                >
                    <option value="">Todos os níveis</option>

                    <option value="Ensino fundamental" <?php if ($nivel == "Ensino fundamental") echo "selected"; ?>>
                        Ensino fundamental
                    </option>

                    <option value="Ensino médio" <?php if ($nivel == "Ensino médio") echo "selected"; ?>>
                        Ensino médio
                    </option>

                    <option value="Ensino técnico" <?php if ($nivel == "Ensino técnico") echo "selected"; ?>>
                        Ensino técnico
                    </option>

                    <option value="Ensino superior" <?php if ($nivel == "Ensino superior") echo "selected"; ?>>
                        Ensino superior
                    </option>

                    <option value="Curso livre" <?php if ($nivel == "Curso livre") echo "selected"; ?>>
                        Curso livre
                    </option>

                    <option value="Outros" <?php if ($nivel == "Outros") echo "selected"; ?>>
                        Outros
                    </option>

                </select>

            </div>

        </div>

        <!-- Botões -->
        <button
            type="submit"
            class="btn rounded-pill px-4"
            style="background-color:#4E0119; color:white;"
        >
            Pesquisar
        </button>

        <a
            href="conteudos.php"
            class="btn rounded-pill px-4"
            style="background-color:#FBDD91; color:#4E0119;"
        >
            Limpar filtros
        </a>

    </form>

</div>

            <div class="text-center acao-doacao">

                <h4 class="fw-bold text-bb-vinho mb-2">
                    Quer compartilhar conhecimento?
                </h4>

                <a href="#" id="btnPublicarConteudo" class="btn px-5 py-3 rounded-pill fw-bold"
                    style="background-color:#DF8F9C; color:#4E0119;">
                    + Publicar conteúdo
                </a>

            </div>
            </div>

           
<div class="row gx-4 justify-content-center mt-5">

    <?php if (mysqli_num_rows($resultado) > 0) { ?>

        <?php while ($conteudo = mysqli_fetch_assoc($resultado)) { ?>

            <div class="col-lg-3 col-md-6 mb-4 d-flex justify-content-center">

                <div class="card shadow border-0">

                    <div class="position-relative">

                        <img
                            class="card-img-top imagem-material"
                            src="<?php echo htmlspecialchars($conteudo['imagem']); ?>"
                            alt="<?php echo htmlspecialchars($conteudo['nome_conteudo']); ?>"
                        >

                    </div>

                    <div class="card-body p-4">

                        <div class="badge rounded-pill mb-2"
                            style="background-color:#DF8F9C; color:#4E0119;">

                            <?php echo htmlspecialchars($conteudo['categoria']); ?>

                        </div>

                        <h5 class="card-title mb-3">

                            <?php echo htmlspecialchars($conteudo['nome_conteudo']); ?>

                        </h5>

                        <p class="text-muted mb-3">

                            <i class="bi bi-person-fill"></i>
                            Compartilhado por:

                            <?php echo htmlspecialchars($conteudo['nome']); ?>

                        </p>

                        <p class="text-muted">

                            <?php echo htmlspecialchars($conteudo['nivel']); ?>

                        </p>
<a
    href="detalhes-conteudo.php?id=<?php echo (int) $conteudo['id']; ?>"
    class="btn"
    style="background-color:#4E0119; color:white;"
>
    Ver detalhes
</a>
                    </div>

                </div>

            </div>

        <?php } ?>

    <?php } else { ?>

        <div class="col-12 text-center">

            <p class="text-muted">
                Nenhum conteúdo encontrado.
            </p>

        </div>

    <?php } ?>

</div>

            <!-- Chamar para ação-->

            <aside class="rounded-3 p-3 p-sm-4 mt-5" style="background-color:#FBDD91;">
                <div
                    class="d-flex align-items-center justify-content-between flex-column flex-xl-row text-center text-xl-start">

                    <div class="mb-4 mb-xl-0">
                        <div class="fs-3 fw-bold" style="color:#4E0119;">
                            Doe materiais e transforme vidas.
                        </div>

                        <div style="color:#6B5E54;">
                            Compartilhe livros e materiais que podem ajudar outros estudantes.
                        </div>
                    </div>

                    <div class="ms-xl-4">
                        <a href="doar-material.html" class="btn px-4 py-2 fw-bold rounded-pill"
                            style="background-color:#DF8F9C; color:#4E0119;">
                            Fazer uma doação
                        </a>

                    </div>
                </div>

            </aside>

            </div>

        </section>
    </main>

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

    <!-- Modal Publicar Conteúdo -->
    <div class="modal fade" id="modalPublicar" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Publicar Conteúdo
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <p>
                        Apenas <strong>Colaboradores Educacionais</strong> podem publicar conteúdos.
                    </p>

                    <p class="mb-0">
                        Você pode alterar seu tipo de conta em
                        <strong>Minha Conta → Meus Dados</strong>.
                    </p>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <a href="meus-dados.html" class="btn" style="background:#DF8F9C; color:#4E0119;">
                        Ir para Minha Conta
                    </a>

                </div>
            </div>
        </div>

    </div>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>


  <script>
    const botaoPublicar = document.getElementById("btnPublicarConteudo");

    botaoPublicar.addEventListener("click", function (event) {
        event.preventDefault();

        <?php if (!isset($_SESSION["id_usuario"])) { ?>

            window.location.href = "login.php";

        <?php } elseif (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "colaborador") { ?>

            window.location.href = "cadastrar-conteudo.php";

        <?php } else { ?>

            const modal = new bootstrap.Modal(document.getElementById("modalPublicar"));
            modal.show();

        <?php } ?>
    });
</script>
</body>

</html>