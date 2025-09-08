<?php
    require "../config/bd.php";
    session_start(); 

    $erro = "";
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["registrar-se"])) { //verifica se o botão foi clicado
            $nome = trim($_POST["nome"] ?? ""); //evita espaços vazios
            $email = trim($_POST["email"] ?? "");
            $telefone = trim($_POST["telefone"] ?? "");
            $senha = trim($_POST["senha"] ?? "");

            // Verifica se o nome de usuário já existe
            $stmt = $conn->prepare("SELECT * FROM usuario WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                $erro = "O email já esta registrado. Tente outro.";
            } else {
                // Insere o novo usuário no banco de dados
                $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?)");
                $stmt->bind_param("ss", $nome, $email, $telefone, $senha);
                
                if ($stmt->execute()) {
                    $mensagem = "Registro bem-sucedido! Você pode voltar para a página inicial e fazer login agora.";
                } else {
                    $erro = "Erro ao registrar. Tente novamente.";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TREMzz - Registre-se</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/tela_registro.css">
    <link rel="shortcut icon" href="../img/tremlogo.png">
    <!-- Fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;700&display=swap" rel="stylesheet">
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>TREMzz</h1>
    </header>

    <main>
        <form method="POST" action="">
            <div class="registro">
                <fieldset>
                    <legend>Registro</legend>
                    <input type="text" id="nome" name="nome" placeholder="Nome">
                    <br>
                    <input type="email" id="email" name="email" placeholder="E-mail">
                    <br>
                    <input type="tel" id="telefone" name="telefone" placeholder="Telefone">
                    <br>
                    <input type="password" id="senha" name="senha" placeholder="Senha">
                    <br>
                    <input type="password" id="senha" name="confirmar_senha" placeholder="Confirmar Senha">
                    <br>

                    <div class="registrar-se">
                        <button >
                        <a>Registrar-se</a>
                        </button>
                    </div>
                </fieldset>
            </div>
        </form>

        <div class="texto-login">
            <span>Já tem uma conta?</span>
            <a href="tela_login.php">Entrar</a>
        </div>
    </main>
</body>
</html>