<?php
require "../config/bd.php";
require "tela_registro.php";
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

            header("location: tela8.html");
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
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TREMzz - Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/tela_login.css">
    <link rel="shortcut icon" href="../img/tremlogo.png">
    <!-- fonte -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- fim fonte -->


</head>
<header>

    <header>
        <h1>TremZZ</h1>
    </header>

    <main>
        <form method="POST" action="">
            <div class="registro">
                <fieldset>
                    <legend>Faça o Login:</legend>
                    <input type="email" name="email" placeholder="E-mail" required>
                    <br>
                    <input type="password" name="senha" placeholder="Senha" required>
                    <br>
                    <button type="submit" name="login">Login</button>
            
                </fieldset>
            </div>
        </form>

        <?php
            if($erro) {
                echo "<div class='erro'>  $erro </div>";
            }
        ?>

        <div class="texto-login">
            <span>Não tem uma conta ainda?</span>
            <a href="tela_registro.php">Registrar-se</a>
        </div>

    </main>
    </body>

</html>