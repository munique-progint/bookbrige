<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

$msg_conteudo = "";
$class_conteudo = "";

if (isset($_SESSION["msg_conteudo"])) {
    $msg_conteudo = $_SESSION["msg_conteudo"];
    $class_conteudo = $_SESSION["class_conteudo"];

    unset($_SESSION["msg_conteudo"]);
    unset($_SESSION["class_conteudo"]);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Página para publicação de conteúdos digitais no BookBridge.">
    <meta name="author" content="BookBridge">

    <title>Cadastrar Conteúdo - BookBridge</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">

</head>

<body>

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

    <section class="py-5 mb-5">
        <div class="container px-5" style="margin-bottom: 150px;">

            <div class="text-center mb-5">

                <h2 class="fw-bolder" style="color:#4E0119;">
                    Publique um material digital
                </h2>

                <p class="lead text-muted mb-1">
                    Compartilhe conteúdos que podem ajudar outros estudantes.
                </p>

            </div>

            <div class="row justify-content-center">

                <div class="col-lg-8">

                    <div class="card formulario-card p-5 mx-auto" style="max-width: 700px; width: 100%;">
                        <form action="processa-conteudo.php" method="POST" enctype="multipart/form-data">
                            <?php if ($msg_conteudo != "") { ?>
    <div class="alert <?php echo $class_conteudo == 'sucesso' ? 'alert-success' : 'alert-danger'; ?>">
        <?php echo htmlspecialchars($msg_conteudo); ?>
    </div>
<?php } ?>

                      <label for="nome_conteudo" class="mb-2 fw-bold fs-5">
    Nome do conteúdo
</label>

<input type="text" id="nome_conteudo" name="nome_conteudo"
    class="form-control fs-5 mb-4 w-100"
    placeholder="Ex: Resumo de Biologia - Bioquímica"
    maxlength="150" required>


                     <label for="categoria" class="mb-2 fw-bold fs-5">
    Categoria
</label>

<select id="categoria" name="categoria" class="form-control fs-5 mb-4" required>
    <option value="">Selecione</option>
    <option value="Artigo">Artigo</option>
    <option value="Apostila">Apostila</option>
    <option value="Mapa mental">Mapa mental</option>
    <option value="Lista de exercícios">Lista de exercícios</option>
    <option value="Resumo">Resumo</option>
    <option value="Simulados">Simulados</option>
    <option value="Slides">Slides</option>
    <option value="Outros">Outros</option>
</select>
                      <label for="nivel" class="mb-2 fw-bold fs-5">
    Nível
</label>

<select id="nivel" name="nivel" class="form-control fs-5 mb-4" required>
    <option value="">Selecione</option>
    <option value="Ensino fundamental">Ensino fundamental</option>
    <option value="Ensino médio">Ensino médio</option>
    <option value="Ensino técnico">Ensino técnico</option>
    <option value="Ensino superior">Ensino superior</option>
    <option value="Curso livre">Curso livre</option>
    <option value="Outros">Outros</option>
</select>

                     <label for="descricao" class="mb-2 fw-bold fs-5">
    Descrição
</label>

<textarea id="descricao" name="descricao"
    class="form-control fs-5 mb-4" rows="4"
    maxlength="2000"
    placeholder="Conte um pouco sobre o material"
    required></textarea>

                      <div class="mb-4">
    <label for="imagem" class="form-label fw-bold">
        Imagem de capa
    </label>

    <input type="file" id="imagem" name="imagem"
        class="form-control" accept=".jpg,.jpeg,.png" required>
</div>

<div class="mb-4">
    <label for="arquivo_pdf" class="form-label fw-bold">
        Arquivo do conteúdo (PDF)
    </label>

    <input type="file" id="arquivo_pdf" name="arquivo_pdf"
        class="form-control" accept=".pdf" required>
</div>

<button type="submit" class="btn px-5 py-3 rounded-pill fw-bold fs-5"
    style="background-color:#4E0119; color:white;">
    Publicar
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
                    <div class="small m-0 text-white">© 2026 BookBridge - Compartilhando conhecimento</div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>

</body>

</html>