<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

require_once "conecta.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"]) || (int) $_GET["id"] <= 0) {
    header("Location: minhas-doacoes.php");
    exit;
}

$id_material = (int) $_GET["id"];
$id_usuario = $_SESSION["id_usuario"];

// Busca o material pertencente ao usuário logado
$sql = "SELECT * FROM materiais
        WHERE id = ? AND id_usuario = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id_material, $id_usuario);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$material = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

if (!$material) {
    header("Location: minhas-doacoes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Página para editar um material físico cadastrado no BookBridge.">
    <meta name="author" content="BookBridge">

    <title>Editar Material - BookBridge</title>

    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">
</head>

<body style="background-color:#fff;">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#FBDD91;">
        <div class="container px-5">

            <a class="navbar-brand fs-2 fw-bold" href="index.html" style="color:#DF8F9C;">
                BookBridge
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

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

    <section class="py-5 mb-5">

        <div class="container px-5" style="margin-bottom: 150px;">

            <div class="text-center mb-5">

                <div class="text-start mb-4">
                    <a href="minhas-doacoes.php" class="text-decoration-none"
                        style="color:#4E0119; font-weight:600;">
                        <i class="bi bi-arrow-left"></i>
                        Voltar para Minhas Doações
                    </a>
                </div>

                <h2 class="fw-bolder" style="color:#DF8F9C;">
                    Editar Material
                </h2>

                <p class="fs-4">
                    Edite as informações do seu material físico.
                </p>

            </div>

            <div class="row justify-content-center">

                <div class="col-lg-8">

                    <div class="card editar-material-card p-5 mx-auto"
                        style="max-width: 700px; width: 100%;">

                        <form action="processa-edicao-material.php" method="POST"
                            enctype="multipart/form-data">

                            <input type="hidden" name="id_material"
                                value="<?php echo (int) $material["id"]; ?>">

                            <label class="mb-2 fw-bold fs-5">
                                Nome do material
                            </label>

                            <input type="text" name="nome_material"
                                class="form-control fs-5 mb-4"
                                value="<?php echo htmlspecialchars($material["nome_material"]); ?>"
                                maxlength="150" required>

                            <label class="mb-2 fw-bold fs-5">
                                Categoria
                            </label>

                            <select name="categoria" class="form-control fs-5 mb-4" required>

                                <option value="Livro" <?php echo $material["categoria"] == "Livro" ? "selected" : ""; ?>>
                                    Livro
                                </option>

                                <option value="Apostilas" <?php echo $material["categoria"] == "Apostilas" ? "selected" : ""; ?>>
                                    Apostilas
                                </option>

                                <option value="Materiais escolares" <?php echo $material["categoria"] == "Materiais escolares" ? "selected" : ""; ?>>
                                    Materiais escolares
                                </option>

                            </select>

                            <label class="mb-2 fw-bold fs-5">
                                Área
                            </label>

                            <select name="area" class="form-control fs-5 mb-4">

                                <?php
                                $areas = [
                                    "Matemática", "Português", "Literatura", "Redação",
                                    "Inglês", "Espanhol", "História", "Geografia",
                                    "Filosofia", "Sociologia", "Biologia", "Física",
                                    "Química", "Artes", "Educação Física",
                                    "Todas as matérias", "Outra"
                                ];

                                foreach ($areas as $area) {
                                    $selecionada = ($material["area"] == $area) ? "selected" : "";
                                    echo "<option value=\"" . htmlspecialchars($area) . "\" $selecionada>" . htmlspecialchars($area) . "</option>";
                                }
                                ?>

                            </select>

                            <label class="mb-2 fw-bold fs-5">
                                Estado
                            </label>

                            <input type="text" name="estado"
                                class="form-control fs-5 mb-4"
                                value="<?php echo htmlspecialchars($material["estado"] ?? ""); ?>"
                                maxlength="2" placeholder="Ex.: RS">

                            <label class="mb-2 fw-bold fs-5">
                                Nível
                            </label>

                            <select name="nivel" class="form-control fs-5 mb-4" required>

                                <option value="Ensino Fundamental" <?php echo $material["nivel"] == "Ensino Fundamental" ? "selected" : ""; ?>>
                                    Ensino Fundamental
                                </option>

                                <option value="Ensino Médio" <?php echo $material["nivel"] == "Ensino Médio" ? "selected" : ""; ?>>
                                    Ensino Médio
                                </option>

                                <option value="Ensino Superior" <?php echo $material["nivel"] == "Ensino Superior" ? "selected" : ""; ?>>
                                    Ensino Superior
                                </option>

                                <option value="Curso livre" <?php echo $material["nivel"] == "Curso livre" ? "selected" : ""; ?>>
                                    Curso livre
                                </option>

                                <option value="Outros" <?php echo $material["nivel"] == "Outros" ? "selected" : ""; ?>>
                                    Outros
                                </option>

                            </select>

                            <label class="mb-2 fw-bold fs-5">
                                Descrição
                            </label>

                            <textarea name="descricao" class="form-control fs-5 mb-4"
                                rows="4" required><?php echo htmlspecialchars($material["descricao"]); ?></textarea>

                            <label class="mb-2 fw-bold fs-5">
                                Imagem atual
                            </label>

                            <div class="text-center mb-4">

                                <?php if (!empty($material["imagem"])) { ?>

                                    <img src="<?php echo htmlspecialchars($material["imagem"]); ?>"
                                        class="img-fluid rounded shadow"
                                        style="max-width:220px;"
                                        alt="Imagem do material">

                                <?php } else { ?>

                                    <p class="text-muted">
                                        Este material não possui imagem cadastrada.
                                    </p>

                                <?php } ?>

                            </div>

                            <label class="mb-2 fw-bold fs-5">
                                Alterar imagem
                            </label>

                            <input type="file" name="imagem"
                                class="form-control mb-4"
                                accept=".jpg,.jpeg,.png,.jfif,.webp">

                            <button type="submit"
                                class="btn px-4 py-2 rounded-pill fw-bold"
                                style="background-color:#4E0119; color:white;">
                                Salvar Alterações
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </section>

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

    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
