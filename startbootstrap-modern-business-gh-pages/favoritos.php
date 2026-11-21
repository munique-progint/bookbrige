<?php
session_start();

include("conecta.php");

// Verifica se o usuário está logado
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

// Busca conteúdos digitais e materiais físicos favoritados
$sql = "SELECT
            favoritos.id AS id_favorito,
            'conteudo' AS tipo,
            conteudos.id AS id_item,
            conteudos.nome_conteudo AS nome,
            conteudos.categoria,
            conteudos.nivel,
            conteudos.imagem,
            NULL AS area,
            usuarios.nome AS autor
        FROM favoritos
        INNER JOIN conteudos
            ON favoritos.id_conteudo = conteudos.id
        INNER JOIN usuarios
            ON conteudos.id_usuario = usuarios.id
        WHERE favoritos.id_usuario = ?
          AND favoritos.id_conteudo IS NOT NULL

        UNION ALL

        SELECT
            favoritos.id AS id_favorito,
            'material' AS tipo,
            materiais.id AS id_item,
            materiais.nome_material AS nome,
            materiais.categoria,
            materiais.nivel,
            materiais.imagem,
            materiais.area,
            usuarios.nome AS autor
        FROM favoritos
        INNER JOIN materiais
            ON favoritos.id_material = materiais.id
        INNER JOIN usuarios
            ON materiais.id_usuario = usuarios.id
        WHERE favoritos.id_usuario = ?
          AND favoritos.id_material IS NOT NULL

        ORDER BY id_favorito DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ii", $id_usuario, $id_usuario);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Página de favoritos do BookBridge.">

    <title>Favoritos - BookBridge</title>

    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">

    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">

    <main class="flex-shrink-0">

        <!-- Navegação -->
        <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#FBDD91;">

            <div class="container px-5">

                <a class="navbar-brand fs-2 fw-bold" href="index.php" style="color:#DF8F9C;">
                    BookBridge
                </a>

                <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation">

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

                        <li class="nav-item">
                            <a class="nav-link" href="minha-conta.php">Minha Conta</a>
                        </li>

                    </ul>

                </div>

            </div>

        </nav>

        <!-- Seção de favoritos -->
        <section class="py-5 secao-favoritos" style="background-color:#FFF;">

            <div class="container px-5 my-3">

                <div class="text-center mb-5">

                    <h2 class="fw-bold" style="color:#DF8F9C;">
                        Meus favoritos
                    </h2>

                    <p class="text-muted">
                        Aqui estão os conteúdos e materiais que você salvou.
                    </p>

                </div>

                <div class="row gx-4 justify-content-center mt-4">

                    <?php if (mysqli_num_rows($resultado) > 0) { ?>

                        <?php while ($item = mysqli_fetch_assoc($resultado)) { ?>

                            <div class="col-lg-3 col-md-6 mb-4 d-flex justify-content-center">

                                <div class="card shadow border-0 card-favorito">

                                    <div class="position-relative">

                                        <?php if (!empty($item["imagem"])) { ?>

                                            <img
                                                class="card-img-top imagem-material"
                                                src="<?php echo htmlspecialchars($item["imagem"]); ?>"
                                                alt="<?php echo htmlspecialchars($item["nome"]); ?>">

                                        <?php } else { ?>

                                            <div class="text-center py-5 bg-light">

                                                <i class="bi bi-book"
                                                    style="font-size:4rem; color:#4E0119;">
                                                </i>

                                            </div>

                                        <?php } ?>

                                        <!-- Botão para desfavoritar -->
                                        <form action="processa-favorito.php" method="POST">

                                            <input type="hidden" name="id_item"
                                                value="<?php echo (int) $item["id_item"]; ?>">

                                            <input type="hidden" name="tipo"
                                                value="<?php echo htmlspecialchars($item["tipo"]); ?>">

                                            <input type="hidden" name="acao" value="remover">

                                            <input type="hidden" name="origem" value="favoritos">

                                            <button type="submit"
                                                class="favorito-card"
                                                title="Remover dos favoritos"
                                                aria-label="Remover dos favoritos"
                                                style="border:none; cursor:pointer;">

                                                <i class="bi bi-heart-fill"></i>

                                            </button>

                                        </form>

                                    </div>

                                    <div class="card-body p-4">

                                        <div class="badge rounded-pill mb-2"
                                            style="background-color:#FBDD91; color:#4E0119;">

                                            <?php echo htmlspecialchars($item["categoria"]); ?>

                                        </div>

                                        <h5 class="card-title mb-3">
                                            <?php echo htmlspecialchars($item["nome"]); ?>
                                        </h5>

                                        <?php if ($item["tipo"] == "material" && !empty($item["area"])) { ?>

                                            <p class="text-muted mb-2">
                                                <i class="bi bi-bookmark"></i>
                                                <?php echo htmlspecialchars($item["area"]); ?>
                                            </p>

                                        <?php } ?>

                                        <p class="text-muted mb-2">
                                            <i class="bi bi-mortarboard"></i>
                                            <?php echo htmlspecialchars($item["nivel"]); ?>
                                        </p>

                                        <p class="text-muted mb-3">
                                            <i class="bi bi-person-fill"></i>
                                            Compartilhado por:
                                            <?php echo htmlspecialchars($item["autor"]); ?>
                                        </p>

                                        <?php if ($item["tipo"] == "conteudo") { ?>

                                            <a
                                                href="detalhes-conteudo.php?id=<?php echo (int) $item["id_item"]; ?>"
                                                class="btn"
                                                style="background-color:#4E0119; color:white;">

                                                Ver detalhes

                                            </a>

                                        <?php } else { ?>

                                            <a
                                                href="material-detalhes.php?id=<?php echo (int) $item["id_item"]; ?>"
                                                class="btn"
                                                style="background-color:#4E0119; color:white;">

                                                Ver detalhes

                                            </a>

                                        <?php } ?>

                                    </div>

                                </div>

                            </div>

                        <?php } ?>

                    <?php } else { ?>

                        <div class="col-12 text-center py-5">

                            <i class="bi bi-heart"
                                style="font-size:4rem; color:#DF8F9C;">
                            </i>

                            <h4 class="fw-bold mt-3" style="color:#4E0119;">
                                Você ainda não tem favoritos
                            </h4>

                            <p class="text-muted">
                                Explore os conteúdos e materiais disponíveis e salve seus preferidos!
                            </p>

                            <a href="conteudos.php"
                                class="btn rounded-pill px-4 py-2 mt-2"
                                style="background-color:#4E0119; color:white;">

                                Explorar conteúdos

                            </a>

                            <a href="materiais.php"
                                class="btn rounded-pill px-4 py-2 mt-2"
                                style="background-color:#DF8F9C; color:#4E0119;">

                                Ver doações

                            </a>

                        </div>

                    <?php } ?>

                </div>

            </div>

        </section>

    </main>

    <!-- Rodapé -->
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

<?php
mysqli_stmt_close($stmt);
?>
