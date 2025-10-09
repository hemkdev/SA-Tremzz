<?php
require "../config/bd.php";
session_start();

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["login"])) { //verifica se o botão foi clicado
        $email = trim($_POST["email"] ?? ""); //evita espaços vazios
        $senha = trim($_POST["senha"] ?? "");

        // Verifica se o nome de usuário e senha estão corretos
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // Verifica se encontrou um usuário com as credenciais fornecidas
        if ($resultado->num_rows === 1) {
            $dados = $resultado->fetch_assoc();
            $senha_armazenada_rash = $dados["senha"];

            if (password_verify($senha, $senha_armazenada_rash)) {

                $_SESSION['id'] = $dados['id'];
                $_SESSION['email'] = $dados['email'];
                $_SESSION['senha'] = $dados['senha'];
                $_SESSION["cargo"] = $dados['cargo'];
                $_SESSION['nome'] = $dados['nome'];
                $_SESSION["cargo"] = $dados['cargo'];
                $_SESSION["foto"] = $dados['foto'];
                $_SESSION["conectado"] = true;

                if ($_SESSION["cargo"] === "administrador") {
                    $_SESSION['admin'] = true;
                    header("location: adm/home.php");
                    exit;
                } else if ($_SESSION["cargo"] === "maquinista") {
                    $_SESSION['maquinista'] = true;
                    header("location: maquinista/home.php");
                    exit;
                } else {
                    header("location: home.php");
                    exit;
                }
            } else {
                $erro = "Usuário ou senha inválidos";
            }
        } else {
            $erro = "Usuário não encontrado";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Login</title>
    <link rel="shortcut icon" href="../assets/img/tremlogo.png" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons para ícones -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- STYLE -->
    <style>
        .form-control::placeholder {
            color: #ccb2b2ff !important;
            /* Mude esta cor conforme necessário */
            opacity: 1;
        }
    </style>
</head>

<body style="font-family: 'Poppins', sans-serif;" class="bg-dark text-light min-vh-100">
    <header class="text-center my-5">
        <h1 class="display-3 text-danger fw-bold text-uppercase lh-1">TREMzz</h1>
        <img src="../assets/img/tremlogo.png">
    </header>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-0 rounded-3 overflow-hidden shadow-lg border border-danger border-2 mx-auto" style="max-width: 900px; box-shadow: 0 0 30px rgba(220, 53, 69, 0.3);">
                    <!-- Imagem desktop -->
                    <div class="col-md-6 p-0 d-none d-md-block" style="background: url('../assets/img/.png') center/cover no-repeat; min-height: 500px;">
                        <div class="h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50">
                            <i class="bi bi-person-circle display-1 text-danger opacity-75"></i>
                        </div>
                    </div>
                    <!-- Formulário -->
                    <div class="col-12 col-md-6 p-5 p-md-4 d-flex align-items-center" style="background-color: #0e0e0eff;">
                        <main class="w-100">
                            <form method="POST" action="">
                                <fieldset class="border border-danger border-2 rounded bg-transparent p-4 w-100">
                                    <legend class="text-danger fw-semibold fs-4 px-2 w-auto">Faça o Login:</legend>

                                    <div class="mb-3">
                                        <label for="email" class="form-label text-light">E-mail</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-danger"><i class="bi bi-envelope text-danger"></i></span>
                                            <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required class="form-control bg-dark text-light border-danger" />
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="senha" class="form-label text-light">Senha</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-danger"><i class="bi bi-lock text-danger"></i></span>
                                            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required class="form-control bg-dark text-light border-danger" />
                                        </div>
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="lembrar" />
                                        <label class="form-check-label text-light" for="lembrar">
                                            Lembrar-me
                                        </label>
                                    </div>

                                    <button type="submit" name="login" class="btn btn-danger w-100 mb-3">Entrar</button>

                                    <div class="text-center">
                                        <a href="#" class="text-secondary fw-medium text-decoration-none small">Esqueceu a senha?</a>
                                    </div>
                                </fieldset>
                            </form>

                            <?php
                            if ($erro) {
                                echo "<div class='alert alert-danger mt-3 mb-0'><i class='bi bi-exclamation-triangle-fill me-2'></i>$erro</div>";
                            }
                            ?>

                            <div class="text-center mt-4">
                                <span class="text-light">Não tem uma conta ainda? </span>
                                <a href="registro.php" class="text-danger fw-medium text-decoration-none">Registrar-se</a>
                            </div>

                            <hr class="my-4 border-danger border-2 opacity-25">

                        </main>
                    </div>
                    <!-- Imagem mobile -->
                    <div class="col-12 p-0 d-md-none" style="height: 200px; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('../assets/img/ .png') center/cover no-repeat;">
                        <div class="h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75">
                            <i class="bi bi-person-circle display-1 text-danger opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>