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

            $_SESSION['email'] = $dados['email'];
            $_SESSION['senha'] = $dados['senha'];
            $_SESSION['nome'] = $dados['nome'];
            $_SESSION["conectado"] = true;
            header("location: home.php");
            exit;
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
    <link rel="shortcut icon" href="../img/tremlogo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2c2c2c; /* cinza escuro */
            color: #f8f9fa;
            height: 100vh;
        }

        h1 {
            color: #dc3545; /* vermelho Bootstrap */
            font-weight: 700;
        }

        fieldset {
            border: 2px solid #dc3545;
            border-radius: 0.5rem;
            background-color: #3a3a3a;
            padding: 2rem;
        }

        legend {
            color: #dc3545;
            font-weight: 600;
            font-size: 1.5rem;
            width: auto;
            padding: 0 0.5rem;
        }

        .btn-login {
            background-color: #dc3545;
            border: none;
        }

        .btn-login:hover {
            background-color: #b02a37;
        }

        a {
            color: #dc3545;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
            color: #a71d2a;
        }

        .error-message {
            background-color: #b02a37;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-top: 1rem;
        }

        /* Container for image and form side by side */
        .login-container {
            max-width: 900px;
            margin: 3rem auto;
            background-color: #1f1f1f;
            border-radius: 0.75rem;
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.5);
            display: flex;
            overflow: hidden;
        }

        .login-image {
            flex: 1;
            background: url('../assets/img/ .jpg') center center/cover no-repeat;
            /* Use an appropriate image path */
        }

        .login-form {
            flex: 1;
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-image {
                height: 200px;
            }
        }
    </style>
</head>

<body>
    <header class="text-center my-4">
        <h1>TREMzz</h1>
    </header>

    <div class="login-container">
        <div class="login-image"></div>

        <main class="login-form">
            <form method="POST" action="">
                <fieldset>
                    <legend>Faça o Login:</legend>

                    <div class="mb-3">
                        <input type="email" name="email" placeholder="E-mail" required class="form-control" />
                    </div>

                    <div class="mb-3">
                        <input type="password" name="senha" placeholder="Senha" required class="form-control" />
                    </div>

                    <button type="submit" name="login" class="btn btn-login w-100">Login</button>
                </fieldset>
            </form>

            <?php
                if($erro) {
                    echo "<div class='error-message'>  $erro </div>";
                }
            ?>

            <div class="text-center mt-3">
                <span>Não tem uma conta ainda? </span>
                <a href="registro.php">Registrar-se</a>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

