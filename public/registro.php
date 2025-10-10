<?php
require "../config/bd.php";
session_start();

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["registrar-se"])) { // verifica se o botão foi clicado
        $nome = trim($_POST["nome"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $telefone = trim($_POST["telefone"] ?? "");
        $senha = trim($_POST["senha"] ?? "");
        $confirmar_senha = trim($_POST["confirmar_senha"] ?? "");

        // Validação de campos vazios
        if (empty($nome) || empty($email) || empty($telefone) || empty($senha) || empty($confirmar_senha)) {
            $erro = "Todos os campos são obrigatórios.";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $senha)) {
            $erro = "Senha fraca. Use pelo menos 8 caracteres, incluindo maiúscula, minúscula, número e caractere especial.";
        } elseif ($senha !== $confirmar_senha) {
            $erro = "As senhas não coincidem.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = "E-mail inválido.";
        } else {
            // Verifica se já existe usuário com o mesmo e-mail ou telefone
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? OR telefone = ?");
            $stmt->bind_param("ss", $email, $telefone);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                $erro = "E-mail ou telefone já registrados. Tente outros.";
            } else {
                // Criptografa a senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                // Insere o novo usuário no banco
                $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nome, $email, $telefone, $senha_hash);

                if ($stmt->execute()) {
                    header("Location: login.php");
                    exit;
                } else {
                    $erro = "Erro ao registrar. Tente novamente.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Registro</title>
    <link rel="shortcut icon" href="../assets/img/tremzz_logo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <!-- Bootstrap Icons para ícones -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- CSS mínimo para cor do placeholder (essencial, pois Bootstrap não suporta nativamente) -->
    <style>
        .form-control::placeholder {
            color: #ccb2b2ff !important;
            opacity: 1;
        }
    </style>
</head>

<body style="font-family: 'Poppins', sans-serif;" class="bg-dark text-light min-vh-100">
    <header class="text-center my-5">
        <h1 class="display-3 text-danger fw-bold text-uppercase lh-1">TREMzz</h1>
    </header>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-0 rounded-3 overflow-hidden shadow-lg border border-danger border-2 mx-auto" style="max-width: 900px; box-shadow: 0 0 30px rgba(220, 53, 69, 0.3);">
                    <!-- Imagem desktop -->
                    <div class="col-md-6 p-0 d-none d-md-block" style="background: url('../assets/img/ .jpg') center/cover no-repeat; min-height: 500px;">
                        <div class="h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50">
                            <i class="bi bi-person-plus display-1 text-danger opacity-75"></i>
                        </div>
                    </div>
                    <!-- Formulário -->
                    <div class="col-12 col-md-6 p-5 p-md-4 d-flex align-items-center" style="background-color: #0e0e0eff;">
                        <main class="w-100">
                            <form method="POST" action="">
                                <fieldset class="border border-danger border-2 rounded bg-transparent p-4 w-100">
                                    <legend class="text-danger fw-semibold fs-4 px-2 w-auto">Faça o registro:</legend>

                                    <div class="mb-3">
                                        <label for="nome" class="form-label text-light">Nome</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-danger"><i class="bi bi-person text-danger"></i></span>
                                            <input type="text" id="nome" name="nome" placeholder="Nome" required class="form-control bg-dark text-light border-danger" />
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label text-light">E-mail</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-danger"><i class="bi bi-envelope text-danger"></i></span>
                                            <input type="email" id="email" name="email" placeholder="E-mail" required class="form-control bg-dark text-light border-danger" />
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="telefone" class="form-label text-light">Telefone</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-danger"><i class="bi bi-telephone text-danger"></i></span>
                                            <input type="tel" id="telefone" name="telefone" placeholder="Telefone" required class="form-control bg-dark text-light border-danger" />
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="senha" class="form-label text-light">Senha</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-danger"><i class="bi bi-lock text-danger"></i></span>
                                            <input type="password" id="senha" name="senha" placeholder="Senha" required class="form-control bg-dark text-light border-danger" />
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="confirmar_senha" class="form-label text-light">Confirmar senha</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-danger"><i class="bi bi-lock text-danger"></i></span>
                                            <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirmar senha" required class="form-control bg-dark text-light border-danger" />
                                        </div>
                                    </div>

                                    <button type="submit" name="registrar-se" class="btn btn-danger w-100">Registrar</button>
                                </fieldset>
                            </form>

                            <?php
                            if ($erro) {
                                echo "<div class='alert alert-danger mt-3 mb-0'><i class='bi bi-exclamation-triangle-fill me-2'></i>$erro</div>";
                            }
                            ?>

                            <div class="text-center mt-3">
                                <span>Já tem uma conta? </span>
                                <a href="login.php" class="text-danger fw-medium text-decoration-none">Entrar</a>
                            </div>
                        </main>
                    </div>
                    <!-- Imagem mobile -->
                    <div class="col-12 p-0 d-md-none" style="height: 200px; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('../assets/img/ .jpg') center/cover no-repeat;">
                        <div class="h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75">
                            <i class="bi bi-person-plus display-1 text-danger opacity-75"></i>
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
