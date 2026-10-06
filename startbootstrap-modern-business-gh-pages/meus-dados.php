<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}
require_once "conecta.php";

$id_usuario = $_SESSION["id_usuario"];

$sql = "SELECT nome, email, telefone, tipo_usuario
        FROM usuarios
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result(
    $stmt,
    $nome,
    $email,
    $telefone,
    $tipo_usuario
);

mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$msg = $_SESSION["msg"] ?? "";
$class = $_SESSION["class"] ?? "";

unset($_SESSION["msg"]);
unset($_SESSION["class"]);

if ($class == "sucesso") {
    $classe_alerta = "alert-success";
} else {
    $classe_alerta = "alert-danger";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Área para visualizar e editar os dados da conta do usuário.">
    <meta name="author" content="BookBridge">

    <title>Meus Dados | BookBridge</title>

    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">

    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">

    <main class="flex-shrink-0 ">

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
                        <li class="nav-item"><a class="nav-link" href="conteudos.html">Conteúdos</a></li>
                        <li class="nav-item"><a class="nav-link" href="materiais.html">Doações</a></li>
                        <li class="nav-item"><a class="nav-link" href="minha-conta.html">Minha Conta</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <section class="conta-section">

            <div class="container">

                <div class="conta-topo">

                    <h2 class="fw-bolder" style="color:#DF8F9C;">
                        Meus Dados
                    </h2>

                    <p class="descricao-conta">
                        Visualize e atualize as informações da sua conta.
                    </p>
<?php if ($msg != "") { ?>

    <div class="alert <?php echo $classe_alerta; ?> text-center mt-3">
        <?php echo htmlspecialchars($msg); ?>
    </div>

<?php } ?>
                </div>

            </div>

            <div class="row justify-content-center">

                <div class="col-lg-6">

                    <div class="conta-card">

                        <div class="editar-conta">
                            <button type="button" id="btnEditar" class="btn-editar" aria-label="Editar dados">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </div>

                        <form action="processa-dados.php" method="POST">

                            <div class="mb-3">
                                <label class="form-label">Nome completo</label>
                                <input type="text" name="nome" class="form-control"
    value="<?php echo htmlspecialchars($nome); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control"
    value="<?php echo htmlspecialchars($email); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Telefone</label>
                           <input type="text" name="telefone" class="form-control"
    value="<?php echo htmlspecialchars($telefone); ?>" disabled>
                            </div>

                            <div class="mb-4">

                                <label class="form-label">
                                    Tipo de conta
                                </label>

                                <select name="tipo_usuario" class="form-select" disabled>
    <option value="estudante"
        <?php if ($tipo_usuario == "estudante") echo "selected"; ?>>
        Estudante
    </option>

    <option value="colaborador"
        <?php if ($tipo_usuario == "colaborador") echo "selected"; ?>>
        Colaborador Educacional
    </option>
</select>
                            </div>

                            <div id="areaSalvar">

                                <button type="submit" class="btn btn-lg w-100 btn-salvar">
                                    Salvar Alterações
                                </button>

                            </div>

                            <div class="links-conta text-center mt-5">

                                <a href="alterar-senha.html" class="d-block mb-2">
                                    Alterar senha
                                </a>

                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalExcluirConta">
                                    Excluir conta
                                </a>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </section>

        <!-- Modal Excluir Conta -->
        <div class="modal fade" id="modalExcluirConta" tabindex="-1">

            <div class="modal-dialog modal-dialog-centered modal-lg">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Excluir conta
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <p class="fw-semibold mb-2">
                            Tem certeza que deseja excluir sua conta?
                        </p>

                        <p class="text-muted mb-2">
                            Esta ação removerá permanentemente sua conta e não poderá ser desfeita.
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

        <script>
            const btnEditar = document.getElementById("btnEditar");

            btnEditar.addEventListener("click", function () {

                document.querySelectorAll(".form-control").forEach(campo => {
                    campo.disabled = false;
                });

                document.querySelector(".form-select").disabled = false;
                document.getElementById("areaSalvar").classList.add("mostrar");

            });
        </script>

</body>

</html>