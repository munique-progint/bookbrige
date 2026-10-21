<?php
session_start();

require_once "conecta.php";

$pesquisa = trim($_GET["pesquisa"] ?? "");
$categoria = trim($_GET["categoria"] ?? "");
$area = trim($_GET["area"] ?? "");
$nivel = trim($_GET["nivel"] ?? "");
$estado = trim($_GET["estado"] ?? "");
$estados_permitidos = [
    "AC", "AL", "AP", "AM", "BA", "CE", "DF",
    "ES", "GO", "MA", "MT", "MS", "MG", "PA",
    "PB", "PR", "PE", "PI", "RJ", "RN", "RS",
    "RO", "RR", "SC", "SP", "SE", "TO"
];

if ($estado != "" && !in_array($estado, $estados_permitidos, true)) {
    $estado = "";
}

$sql = "SELECT materiais.id, materiais.nome_material, materiais.categoria,
               materiais.area, materiais.nivel, materiais.descricao,
               materiais.imagem, materiais.status, usuarios.nome
        FROM materiais
        INNER JOIN usuarios ON materiais.id_usuario = usuarios.id
        WHERE materiais.status = 'Disponível'";

$parametros = [];
$tipos = "";

if ($pesquisa != "") {
    $sql .= " AND (materiais.nome_material LIKE ? OR materiais.area LIKE ? OR materiais.descricao LIKE ?)";
    $parametros[] = "%" . $pesquisa . "%";
    $parametros[] = "%" . $pesquisa . "%";
    $parametros[] = "%" . $pesquisa . "%";
    $tipos .= "sss";
}
if ($categoria != "") {
    $sql .= " AND materiais.categoria = ?";
    $parametros[] = $categoria;
    $tipos .= "s";
}
if ($area != "") {
    $sql .= " AND materiais.area = ?";
    $parametros[] = $area;
    $tipos .= "s";
}

if ($nivel != "") {
    $sql .= " AND materiais.nivel = ?";
    $parametros[] = $nivel;
    $tipos .= "s";
}
if ($estado != "") {
    $sql .= " AND materiais.estado = ?";
    $parametros[] = $estado;
    $tipos .= "s";
}

$sql .= " ORDER BY materiais.id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Não foi possível consultar os materiais.");
}

if (!empty($parametros)) {
    mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
}

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    die("Não foi possível consultar os materiais.");
}

$resultado = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="pt-BR">


<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Materiais Doação - BookBridge</title>
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
                        <li class="nav-item"><a class="nav-link" href="conteudos.html">Conteúdos</a></li>
                        
<li class="nav-item"><a class="nav-link" href="materiais.php">Doações</a></li>

                        <li class="nav-item"><a class="nav-link" href="login.html">Entrar</a></li>
                        <li class="nav-item"><a class="nav-link" href="cadastro.html">Cadastre-se</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Blog preview section-->
        <section class="py-5">
            <div class="container px-5 my-5">
                <div class="row gx-5 justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="text-center">
                            <h2 class="fw-bolder" style="color:#4E0119;">
                                Materiais para Doação
                            </h2>

                            <p class="lead fw-normal text-muted mb-5">
                                Encontre livros, apostilas e outros materiais físicos compartilhados pela comunidade.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

          <!-- Filtros -->
<form method="GET" action="materiais.php">
    <div class="text-center mb-5">

        <input
            class="form-control mx-auto mb-3"
            style="max-width: 700px; height: 45px;"
            type="text"
            name="pesquisa"
            placeholder="🔍 Pesquisar material"
            value="<?php echo htmlspecialchars($_GET["pesquisa"] ?? ""); ?>">

                <div class="row justify-content-center g-3 mt-3 mb-4">

                    <div class="col-auto">
                    <select class="form-select"
    name="categoria"
    style="width: 210px; height: 50px; font-size: 1.08rem; font-weight: 500;">

    <option value="">Todas as categorias</option>

    <option value="Livro"
        <?php echo (($_GET["categoria"] ?? "") == "Livro") ? "selected" : ""; ?>>
        Livro
    </option>

    <option value="Apostilas"
        <?php echo (($_GET["categoria"] ?? "") == "Apostilas") ? "selected" : ""; ?>>
        Apostilas
    </option>

    <option value="Materiais escolares"
        <?php echo (($_GET["categoria"] ?? "") == "Materiais escolares") ? "selected" : ""; ?>>
        Materiais escolares
    </option>

</select>
</div>

<div class="col-auto">
    <select class="form-select" name="area"
        style="width: 210px; height: 50px; font-size: 1.08rem; font-weight: 500;">
    <option value="">Todas as áreas</option>
    <option value="Matemática" <?php if ($area == "Matemática") echo "selected"; ?>>Matemática</option>
    <option value="Português" <?php if ($area == "Português") echo "selected"; ?>>Português</option>
    <option value="Literatura" <?php if ($area == "Literatura") echo "selected"; ?>>Literatura</option>
    <option value="Redação" <?php if ($area == "Redação") echo "selected"; ?>>Redação</option>
    <option value="Inglês" <?php if ($area == "Inglês") echo "selected"; ?>>Inglês</option>
    <option value="Espanhol" <?php if ($area == "Espanhol") echo "selected"; ?>>Espanhol</option>
    <option value="História" <?php if ($area == "História") echo "selected"; ?>>História</option>
    <option value="Geografia" <?php if ($area == "Geografia") echo "selected"; ?>>Geografia</option>
    <option value="Filosofia" <?php if ($area == "Filosofia") echo "selected"; ?>>Filosofia</option>
    <option value="Sociologia" <?php if ($area == "Sociologia") echo "selected"; ?>>Sociologia</option>
    <option value="Biologia" <?php if ($area == "Biologia") echo "selected"; ?>>Biologia</option>
    <option value="Física" <?php if ($area == "Física") echo "selected"; ?>>Física</option>
    <option value="Química" <?php if ($area == "Química") echo "selected"; ?>>Química</option>
    <option value="Artes" <?php if ($area == "Artes") echo "selected"; ?>>Artes</option>
    <option value="Educação Física" <?php if ($area == "Educação Física") echo "selected"; ?>>Educação Física</option>
    <option value="Todas as matérias" <?php if ($area == "Todas as matérias") echo "selected"; ?>>Todas as matérias</option>
    <option value="Outra" <?php if ($area == "Outra") echo "selected"; ?>>Outra</option>
</select>

                    </div>

                  <div class="col-auto">
    <select class="form-select"
        name="estado"
        style="width: 210px; height: 50px; font-size: 1.08rem; font-weight: 500;">

        <option value="">Todos os estados</option>

        <option value="AC" <?php echo (($_GET["estado"] ?? "") == "AC") ? "selected" : ""; ?>>AC</option>
        <option value="AL" <?php echo (($_GET["estado"] ?? "") == "AL") ? "selected" : ""; ?>>AL</option>
        <option value="AP" <?php echo (($_GET["estado"] ?? "") == "AP") ? "selected" : ""; ?>>AP</option>
        <option value="AM" <?php echo (($_GET["estado"] ?? "") == "AM") ? "selected" : ""; ?>>AM</option>
        <option value="BA" <?php echo (($_GET["estado"] ?? "") == "BA") ? "selected" : ""; ?>>BA</option>
        <option value="CE" <?php echo (($_GET["estado"] ?? "") == "CE") ? "selected" : ""; ?>>CE</option>
        <option value="DF" <?php echo (($_GET["estado"] ?? "") == "DF") ? "selected" : ""; ?>>DF</option>
        <option value="ES" <?php echo (($_GET["estado"] ?? "") == "ES") ? "selected" : ""; ?>>ES</option>
        <option value="GO" <?php echo (($_GET["estado"] ?? "") == "GO") ? "selected" : ""; ?>>GO</option>
        <option value="MA" <?php echo (($_GET["estado"] ?? "") == "MA") ? "selected" : ""; ?>>MA</option>
        <option value="MT" <?php echo (($_GET["estado"] ?? "") == "MT") ? "selected" : ""; ?>>MT</option>
        <option value="MS" <?php echo (($_GET["estado"] ?? "") == "MS") ? "selected" : ""; ?>>MS</option>
        <option value="MG" <?php echo (($_GET["estado"] ?? "") == "MG") ? "selected" : ""; ?>>MG</option>
        <option value="PA" <?php echo (($_GET["estado"] ?? "") == "PA") ? "selected" : ""; ?>>PA</option>
        <option value="PB" <?php echo (($_GET["estado"] ?? "") == "PB") ? "selected" : ""; ?>>PB</option>
        <option value="PR" <?php echo (($_GET["estado"] ?? "") == "PR") ? "selected" : ""; ?>>PR</option>
        <option value="PE" <?php echo (($_GET["estado"] ?? "") == "PE") ? "selected" : ""; ?>>PE</option>
        <option value="PI" <?php echo (($_GET["estado"] ?? "") == "PI") ? "selected" : ""; ?>>PI</option>
        <option value="RJ" <?php echo (($_GET["estado"] ?? "") == "RJ") ? "selected" : ""; ?>>RJ</option>
        <option value="RN" <?php echo (($_GET["estado"] ?? "") == "RN") ? "selected" : ""; ?>>RN</option>
        <option value="RS" <?php echo (($_GET["estado"] ?? "") == "RS") ? "selected" : ""; ?>>RS</option>
        <option value="RO" <?php echo (($_GET["estado"] ?? "") == "RO") ? "selected" : ""; ?>>RO</option>
        <option value="RR" <?php echo (($_GET["estado"] ?? "") == "RR") ? "selected" : ""; ?>>RR</option>
        <option value="SC" <?php echo (($_GET["estado"] ?? "") == "SC") ? "selected" : ""; ?>>SC</option>
        <option value="SP" <?php echo (($_GET["estado"] ?? "") == "SP") ? "selected" : ""; ?>>SP</option>
        <option value="SE" <?php echo (($_GET["estado"] ?? "") == "SE") ? "selected" : ""; ?>>SE</option>
        <option value="TO" <?php echo (($_GET["estado"] ?? "") == "TO") ? "selected" : ""; ?>>TO</option>
    </select>
                    </div>

                    <div class="col-auto">
                       <select class="form-select"
    name="nivel"
    style="width: 230px; height: 52px; font-size: 1.12rem; font-weight: 500;">

    <option value="">Todos os níveis</option>

    <option value="Ensino Fundamental"
        <?php echo (($_GET["nivel"] ?? "") == "Ensino Fundamental") ? "selected" : ""; ?>>
        Ensino Fundamental
    </option>

    <option value="Ensino Médio"
        <?php echo (($_GET["nivel"] ?? "") == "Ensino Médio") ? "selected" : ""; ?>>
        Ensino Médio
    </option>

    <option value="Ensino Superior"
        <?php echo (($_GET["nivel"] ?? "") == "Ensino Superior") ? "selected" : ""; ?>>
        Ensino Superior
    </option>
    <option value="Curso livre"
    <?php echo (($_GET["nivel"] ?? "") == "Curso livre") ? "selected" : ""; ?>>
    Curso livre
</option>
    <option value="Outros"
        <?php echo (($_GET["nivel"] ?? "") == "Outros") ? "selected" : ""; ?>>
        Outros
    </option>

</select>
                    </div>
                </div>
            </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn rounded-pill px-4 py-2"
                style="background-color:#4E0119; color:white;">
                Pesquisar
            </button>

            <a href="materiais.php" class="btn rounded-pill px-4 py-2"
                style="background-color:#FBDD91; color:#4E0119;">
                Limpar filtros
            </a>
        </div>
    </div>
</form>

            <div class="text-center acao-doacao">

                <h4 class="fw-bold text-bb-vinho mb-2">
                    Tem um material para doar?
                </h4>

                <a href="doar-material.html" class="btn px-5 py-3 rounded-pill fw-bold"
                    style="background-color:#DF8F9C; color:#4E0119;">
                    + Cadastrar doação
                </a>

            </div>

     

<div class="row gx-4 justify-content-center mt-5">

    <?php if (mysqli_num_rows($resultado) > 0) { ?>

        <?php while ($material = mysqli_fetch_assoc($resultado)) { ?>

            <div class="col-lg-3 col-md-6 mb-4 d-flex justify-content-center">
                <div class="card shadow border-0">

                    <?php if (!empty($material["imagem"])) { ?>
                        <img class="card-img-top imagem-material"
                             src="<?php echo htmlspecialchars($material["imagem"]); ?>"
                             alt="<?php echo htmlspecialchars($material["nome_material"]); ?>">
                    <?php } else { ?>
                        <div class="text-center py-5 bg-light">
                            <i class="bi bi-book" style="font-size: 4rem; color: #4E0119;"></i>
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
                            <i class="bi bi-bookmark"></i>
                            <?php echo htmlspecialchars($material["area"] ?? ""); ?>
                        </p>

                        <p class="text-muted mb-3">
                            <i class="bi bi-mortarboard"></i>
                            <?php echo htmlspecialchars($material["nivel"]); ?>
                        </p>

                        <a href="material-detalhes.php?id=<?php echo (int) $material["id"]; ?>"
                           class="btn"
                           style="background-color:#4E0119; color:white;">
                            Ver detalhes
                        </a>

                    </div>
                </div>
            </div>

        <?php } ?>

  <?php } else { ?>

    <div class="col-12 text-center pt-0 pb-4">
        <i class="bi bi-search" style="font-size: 3rem; color: #DF8F9C;"></i>

        <h5 class="mt-3" style="color: #4E0119;">
            Nenhum material encontrado
        </h5>

        <p class="text-muted">
            Não encontramos materiais com os filtros selecionados.
            Tente alterar sua pesquisa ou limpar os filtros.
        </p>

        <a href="materiais.php" class="btn rounded-pill px-4 py-2"
           style="background-color: #FBDD91; color: #4E0119;">
            Limpar filtros
        </a>
    </div>

<?php } ?>

</div>


            <!-- Call to action-->

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
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>
</body>

</html>