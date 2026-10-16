<?php
session_start();

require_once "conecta.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$nome_pasta = trim($_GET["pasta"] ?? "");

if ($nome_pasta == "") {
    header("Location: pastas.php");
    exit;
}

$sql = "SELECT materiais.id, materiais.nome_material,
               materiais.categoria, materiais.area,
               materiais.nivel, materiais.imagem,
               materiais.descricao, usuarios.nome
        FROM materiais_salvos
        INNER JOIN materiais
            ON materiais_salvos.id_material = materiais.id
        INNER JOIN usuarios
            ON materiais.id_usuario = usuarios.id
        WHERE materiais_salvos.id_usuario = ?
          AND materiais_salvos.nome_pasta = ?
        ORDER BY materiais_salvos.data_adicao DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $id_usuario, $nome_pasta);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Visualização dos materiais e conteúdos organizados em uma pasta." />
    <meta name="author" content="BookBridge" />
    <title>Detalhes da Pasta - BookBridge</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">
    <main class="flex-grow-1">
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
        <!-- Conteúdo da Pasta -->
        <section class="py-5">

            <div class="ms-4 mb-4">
                <a href="pastas.php" class="text-decoration-none fw-bold" style="color:#4E0119;">
    <i class="bi bi-arrow-left"></i>
    Voltar para Minhas Pastas
</a>
            </div>

            <div class="container px-5 my-3">
                <div class="row gx-5 justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="text-center">
                           <h2 class="fw-bolder" style="color:#4E0119;">
    <?php echo htmlspecialchars($nome_pasta); ?>
</h2>
                        </div>
                    </div>
                </div>
            </div>

           <div class="row gx-4 justify-content-center mt-5">

    <?php if (mysqli_num_rows($resultado) > 0) { ?>

        <?php while ($material = mysqli_fetch_assoc($resultado)) { ?>

            <div class="col-lg-3 col-md-6 mb-4 d-flex justify-content-center">

                <div class="card shadow border-0">

                    <div class="position-relative">

                        <?php if (!empty($material["imagem"])) { ?>

                            <img class="card-img-top imagem-material"
                                src="<?php echo htmlspecialchars($material["imagem"]); ?>"
                                alt="<?php echo htmlspecialchars($material["nome_material"]); ?>">

                        <?php } else { ?>

                            <div class="text-center py-5 bg-light">
                                <i class="bi bi-book" style="font-size: 3rem;"></i>
                            </div>

                        <?php } ?>

                    </div>

                    <div class="card-body p-4">

                        <div class="badge rounded-pill mb-2"
                            style="background-color:#FBDD91; color:#4E0119;">

                            <?php echo htmlspecialchars($material["categoria"]); ?>

                        </div>

                        <h5 class="card-title mb-3">
                            <?php echo htmlspecialchars($material["nome_material"]); ?>
                        </h5>

                        <p class="text-muted mb-3">
                            Área:
                            <?php
                            echo !empty($material["area"])
                                ? htmlspecialchars($material["area"])
                                : "Não informada";
                            ?>
                        </p>

                        <p class="text-muted mb-3">
                            <i class="bi bi-person-fill"></i>
                            Compartilhado por:
                            <?php echo htmlspecialchars($material["nome"]); ?>
                        </p>

                      <a href="material-detalhes.php?id=<?php echo (int) $material["id"]; ?>"
    class="btn mb-2"
    style="background-color:#4E0119; color:white;">

    Ver detalhes

</a>

<form action="processa-remocao-pasta.php" method="POST"
    onsubmit="return confirm('Deseja remover este material da pasta?');">

    <input type="hidden" name="id_material"
        value="<?php echo (int) $material["id"]; ?>">

    <input type="hidden" name="nome_pasta"
        value="<?php echo htmlspecialchars($nome_pasta, ENT_QUOTES, 'UTF-8'); ?>">

    <button type="submit" class="btn btn-outline-danger mt-2">
        <i class="bi bi-trash"></i>
        Remover da pasta
    </button>

</form>
                    </div>

                </div>

            </div>

        <?php } ?>

    <?php } else { ?>

        <div class="col-12 text-center py-5">

            <i class="bi bi-folder2-open"
                style="font-size:3rem; color:#DF8F9C;"></i>

            <h5 class="mt-3" style="color:#4E0119;">
                Esta pasta ainda não possui materiais.
            </h5>

            <a href="materiais.php"
                class="btn rounded-pill px-4 mt-2"
                style="background-color:#DF8F9C; color:#4E0119;">

                Explorar doações

            </a>

        </div>

    <?php } ?>

</div>

            <div style="height: 120px;"></div>

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


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>