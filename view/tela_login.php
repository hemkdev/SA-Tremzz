<?php
    require "../config/bd.php";
    session_start(); 

    $erro = "";
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["login"])) { //verifica se o botão foi clicado
            $email = trim($_POST["email"] ?? ""); //evita espaços vazios
            $senha = trim($_POST["senha"] ?? "");

            // Verifica se o nome de usuário e senha estão corretos
           $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = ? ");
           $stmt -> bind_param("ss", $email, $senha);
           $stmt -> execute();
           $resultado = $stmt->get_result();
        
           // Verifica se encontrou um usuário com as credenciais fornecidas
           if  ($resultado->num_rows === 1) {
                $dados = $resultado->fetch_assoc();

                $_SESSION['email'] = $dados['email'];
                $_SESSION['id'] = $dados['id'];

                header("location: pagina_inicial.php");
                exit;
            } else {
                $erro = "Usuário ou senha inválidos";
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
    <link rel="stylesheet" href="../css/tela5.css">
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
        <form>
            <div class="registro">
                <fieldset>
                    <legend>Faça o Login:</legend>
                    <input type="email" id="email" name="email" placeholder="E-mail" required>
                    <br>
                    <input type="password" id="senha" name="senha" placeholder="Senha" required>
                    <br>
                    <button type="submit" name="login">Login</button>
                </fieldset>
            </div>
        </form>

        <div class="social-login">
            <p>Faça login por outras plataformas:</p>
            <div class="social-buttons">
                <button class="social-btn google">
                  <a>Google</a>
                </button>
                <button class="social-btn facebook">
                    <a>Facebook</a>
                </button>
            </div>
        </div>

        <div class="texto-login">
            <span>Não tem uma conta ainda?</span>
            <a href="../html/tela4.html">Registrar-se</a>
        </div>
    </main>
</body>
</html>