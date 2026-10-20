<?php
session_start();

require_once "conecta.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"]) || $_GET["id"] <= 0) {
    http_response_code(400);
    die("ID do material inválido.");
}

$id_material = (int) $_GET["id"];

$sql = "SELECT materiais.id, materiais.id_usuario,
               materiais.nome_material, materiais.categoria,
               materiais.area, materiais.estado, materiais.nivel,
               materiais.descricao, materiais.imagem, materiais.status,
               usuarios.nome, usuarios.email, usuarios.telefone
        FROM materiais
        INNER JOIN usuarios ON materiais.id_usuario = usuarios.id
        WHERE materiais.id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_material);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$material = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

if (!$material) {
    http_response_code(404);
    die("Material não encontrado.");
}

$id_usuario_logado = (int) ($_SESSION["id_usuario"] ?? 0);

$pastas = [];

if ($id_usuario_logado > 0) {
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
}
?>



<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Página com os detalhes de um material disponível para doação.">
    <meta name="author" content="BookBridge">
    <title>Detalhes do Material - BookBridge</title>
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">

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
                    <li class="nav-item"><a class="nav-link" href="conteudos.html">Conteúdos</a></li>
                    <li class="nav-item"><a class="nav-link" href="materiais.html">Doações</a></li>
                    <li class="nav-item"><a class="nav-link" href="minha-conta.html">Minha Conta</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal -->
    <main class="flex-shrink-0">
        <section class="py-5" style="background:#FFF;">
            <div class="container px-5">
                <div class="row gx-5 align-items-start">
                    <!-- Imagem do material -->
                    <div class="col-lg-4">
                        <?php if (!empty($material["imagem"])) { ?>
    <img src="<?php echo htmlspecialchars($material["imagem"]); ?>"
         class="img-fluid shadow-lg rounded-4"
         style="max-height: 450px; object-fit: cover;"
         alt="<?php echo htmlspecialchars($material["nome_material"]); ?>">
<?php } else { ?>
    <div class="text-center py-5 bg-light rounded-4">
        <i class="bi bi-book" style="font-size: 5rem; color: #4E0119;"></i>
        <p class="mt-2 mb-0">Este material não possui imagem.</p>
    </div>
<?php } ?>
                    </div>

                    <!-- Informações do material -->
                    <div class="col-lg-6">
                        <div class="bg-white detalhes-conteudo-card rounded-4 p-5">
                            <h2 class="fw-bold" style="color:#4E0119;"><?php echo htmlspecialchars($material["nome_material"]); ?></h2>
                           <span class="badge rounded-pill mb-3"
      style="background-color:#FBBD91; color:#4E0119;"><?php echo htmlspecialchars($material["categoria"]); ?></span>


                            <p class="mb-2">
    <strong>Status:</strong> <?php echo htmlspecialchars($material["status"]); ?>
</p>

                            <p class="mb-4">
    <strong>Doador:</strong> <?php echo htmlspecialchars($material["nome"]); ?>
</p>
                            <hr>

                            <h6 class="fw-bold">Descrição</h6>

                           <p class="text-muted mb-4">
    <?php echo nl2br(htmlspecialchars($material["descricao"])); ?>
</p>

                            <hr>

                            <h6 class="fw-bold mb-3">Informações do material</h6>

                            <div class="row">

                               <div class="col-6 mb-3">
    <small class="text-muted">Área</small><br>
    <?php echo !empty($material["area"]) ? htmlspecialchars($material["area"]) : "Não informada"; ?>
</div>

                              <div class="col-6 mb-3">
    <small class="text-muted">Estado</small><br>
    <?php echo !empty($material["estado"]) ? htmlspecialchars($material["estado"]) : "Não informado"; ?>
</div>

<div class="col-6 mb-3">
    <small class="text-muted">Nível</small><br>
    <?php echo htmlspecialchars($material["nivel"]); ?>
</div>

                            </div>
                            <?php
$id_usuario_logado = $_SESSION["id_usuario"] ?? 0;

if ($id_usuario_logado != $material["id_usuario"]) {
?>

                                <button type="button" class="btn px-4 py-2 rounded-pill fw-bold botao-interesse"
                                    style="background-color:#DF8F9C; color:#4E0119; width:220px;" data-bs-toggle="modal"
                                    data-bs-target="#modalContatoDoador">

                                    Tenho interesse

                                </button>

                                <button type="button" class="btn px-4 py-2 rounded-pill fw-bold text-nowrap"
                                    style="background-color:#DF8F9C; color:#4E0119; width:220px;" data-bs-toggle="modal"
                                    data-bs-target="#modalAdicionarPasta">

                                    <i class="bi bi-folder-plus"></i>
                                    Adicionar à pasta

                                </button>

                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>


  
<!-- Modal Contato do Doador -->
<div class="modal fade" id="modalContatoDoador" tabindex="-1"
    aria-labelledby="modalContatoDoadorLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalContatoDoadorLabel">
                    Contato do doador
                </h5>

                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Fechar">
                </button>
            </div>

            <div class="modal-body">

                <p>
                    <strong>Nome:</strong>
                    <?php echo htmlspecialchars($material["nome"]); ?>
                </p>

                <p>
                    <strong>E-mail:</strong>
                    <?php echo htmlspecialchars($material["email"]); ?>
                </p>

                <p>
                    <strong>Contato:</strong>
                    <?php echo htmlspecialchars($material["telefone"]); ?>
                </p>

                <hr>

                <p class="mb-0 text-muted">
                    Entre em contato com o doador para combinar a entrega do material.
                </p>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn botao-secundario"
                    data-bs-dismiss="modal">
                    Fechar
                </button>
            </div>

        </div>
    </div>
</div>


<!-- Modal Adicionar à Pasta -->
<div class="modal fade" id="modalAdicionarPasta" tabindex="-1"
    aria-labelledby="modalAdicionarPastaLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">

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

                    <input type="hidden" name="id_material"
                        value="<?php echo (int) $material["id"]; ?>">

                  <?php if (count($pastas) > 0) { ?>

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



                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn"
                        style="background-color:#DF8F9C; color:#4E0119;">
                        Adicionar
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
    <!-- Footer -->
    <footer class="mt-auto py-4" style="background-color:#4E0119;">
        <div class="container px-5 text-center text-white">
            © 2026 BookBridge - Compartilhando conhecimento
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>