<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

require_once "conecta.php";

$id_usuario = $_SESSION["id_usuario"];

$sql = "SELECT id, nome_material, categoria, nivel, descricao, imagem, status
        FROM materiais
        WHERE id_usuario = ?
        ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

?>
<?php if (isset($_SESSION["msg"])) { ?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION["class"]); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION["msg"]); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    </div>
    <?php
    unset($_SESSION["msg"]);
    unset($_SESSION["class"]);
    ?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Área onde o usuário gerencia suas doações." />
    <meta name="author" content="" />
    <title>Minhas Doações - BookBridge</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">
    <main class="d-flex flex-column flex-grow-1">
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
        <!-- Minhas Doações -->
        <section class="py-5 flex-grow-1">
            <div class="container px-5 my-3">
                <div class="row gx-5 justify-content-center">
                    <div class="col-12">
                        <div class="text-center">
                            <h2 class="fw-bolder" style="color:#4E0119;">
                                Minhas Doações
                            </h2>

                            <p class="lead fw-normal text-muted mb-5">
                                Gerencie os materiais físicos cadastrados por você.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="text-start mt-3" style="margin-left: 170px;">
                <a href="doar-material.html" class="btn px-4 py-2 rounded-pill fw-bold mt-2"
                    style="background-color:#DF8F9C; color:#4E0119;">
                    + Nova doação
                </a>
            </div>
          

            <div class="row gx-4 justify-content-center mt-3"
    style="width: 100%; max-width: 1700px; margin: 1rem auto 0;">

    <?php if (mysqli_num_rows($resultado) > 0) { ?>

    <?php while ($material = mysqli_fetch_array($resultado)) { ?>

        <div class="col-lg-3 col-md-6 mb-4 d-flex justify-content-center">

            <div class="card shadow border-0">

                <?php if ($material["imagem"] != null) { ?>

                    <img class="card-img-top imagem-material"
                        src="<?php echo htmlspecialchars($material["imagem"]); ?>"
                        alt="<?php echo htmlspecialchars($material["nome_material"]); ?>">

                <?php } else { ?>

                    <div class="d-flex align-items-center justify-content-center"
                        style="height: 180px; background-color:#FBDD91; color:#4E0119;">
                        <i class="bi bi-book" style="font-size: 50px;"></i>
                    </div>

                <?php } ?>

                <div class="card-body p-4">

                    <div class="badge rounded-pill mb-2"
                        style="background-color:#FBDD91; color:#4E0119;">
                        <?php echo htmlspecialchars($material["categoria"]); ?>
                    </div>

                    <h5 class="card-title mb-3">
                        <?php echo htmlspecialchars($material["nome_material"]); ?>
                    </h5>

                    <p class="text-muted mb-3">
                        <?php echo htmlspecialchars($material["nivel"]); ?>
                    </p>

                    <p class="mb-3">

                        <?php if ($material["status"] == "Disponível") { ?>

                            <span class="badge"
                                style="background-color:#DF8F9C; color:#4E0119;">
                                Disponível
                            </span>

                        <?php } else { ?>

                            <span class="badge"
                                style="background-color:#6B5E54; color:white;">
                                <?php echo htmlspecialchars($material["status"]); ?>
                            </span>

                        <?php } ?>

                    </p>

                   
<div class="d-flex justify-content-between mt-3">
    <a href="material-detalhes.html?id=<?php echo $material["id"]; ?>"
        class="btn btn-sm"
        style="background-color:#4E0119; color:white;">
        Ver detalhes
    </a>

    <a href="editar-material.html?id=<?php echo $material["id"]; ?>"
        class="btn btn-sm"
        style="background-color:#FBDD91; color:#4E0119;">
        Editar
    </a>

    <button type="button"
    class="btn btn-sm"
    style="background-color:#DF8F9C; color:#4E0119;"
    data-bs-toggle="modal"
    data-bs-target="#modalExcluirMaterial"
    data-id="<?php echo $material["id"]; ?>">
    Excluir
</button>
</div>


                </div>

            </div>

        </div>

       <?php } ?>

    <?php } else { ?>

        <div class="col-12 text-center py-5">

            <i class="bi bi-book" style="font-size: 50px; color: #DF8F9C;"></i>

            <h5 class="mt-3" style="color: #4E0119;">
                Você ainda não cadastrou nenhuma doação.
            </h5>

            <p class="text-muted">
                Clique em "Nova doação" para compartilhar um material.
            </p>

        </div>

    <?php } ?>

</div>


    <!-- Modal Excluir Material -->
    <div class="modal fade" id="modalExcluirMaterial" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Excluir material
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <p class="fw-semibold mb-2">
                        Tem certeza que deseja excluir este material?
                    </p>

                    <p class="text-muted mb-2">
                        Esta ação removerá permanentemente este material e não poderá ser desfeita.
                    </p>

                </div>

              
<div class="modal-footer">
    <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">
        Cancelar
    </button>

    <form action="processa-exclusao-material.php" method="POST" id="formExcluirMaterial">
        <input type="hidden" name="id_material" id="idMaterialExcluir">

        <button type="submit" class="btn btn-danger">
            Excluir
        </button>
    </form>
</div>

                </div>
            </div>
        </div>
    </div>
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
    <!-- Bootstrap core JS-->
   
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById("modalExcluirMaterial").addEventListener("show.bs.modal", function (event) {
        var botao = event.relatedTarget;
        var idMaterial = botao.getAttribute("data-id");

        document.getElementById("idMaterialExcluir").value = idMaterial;
    });
</script>
</body>
</html>
