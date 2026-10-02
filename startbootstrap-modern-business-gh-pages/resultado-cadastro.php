<?php
session_start();

$msg = $_SESSION["msg"] ?? "Não foi possível identificar o resultado do cadastro.";
$class = $_SESSION["class"] ?? "erro";

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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Resultado - BookBridge</title>

    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bookbridge.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">

    <main class="flex-shrink-0">
        <section class="py-5" style="background-color: #FFF0B3; min-height: 100vh;">

            <div class="container">

                <div class="row align-items-center justify-content-center">

                    <div class="col-lg-6 text-center mb-5 mb-lg-0">

                        <img src="assets/img/fotologin.png"
                            class="img-fluid"
                            style="max-width: 90%;"
                            alt="Ilustração BookBridge">

                    </div>

                    <div class="col-lg-6 col-xl-6">

                        <div class="card border-0 shadow-lg rounded-4 mx-auto"
                            style="max-width: 600px;">

                            <div class="card-body p-5 px-5">

                                <div class="text-center mb-4">

                                    <h1 style="color: var(--bb-rosa); font-weight:700;">
                                        BookBridge
                                    </h1>

                                    <p class="text-muted fs-5 mb-0">
                                        Resultado do cadastro
                                    </p>

                                </div>

                                <div class="alert <?php echo $classe_alerta; ?> text-center">
                                    <?php echo htmlspecialchars($msg); ?>
                                </div>

                              <?php if ($class == "sucesso") { ?>

    <a href="login.html"
        class="btn w-100 rounded-pill py-2 fw-bold mt-3"
        style="background:#DF8F9C; color:#4E0119;">
        Entrar
    </a>

<?php } else { ?>

    <a href="cadastro.html"
        class="btn w-100 rounded-pill py-2 fw-bold mt-3"
        style="background:#DF8F9C; color:#4E0119;">
        Voltar ao cadastro
    </a>

    <a href="login.html"
        class="btn rounded-pill py-2 fw-semibold w-100 mt-3"
        style="border: 2px solid #DF8F9C; color: #DF8F9C;">
        Entrar
    </a>

<?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>