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

        // Verifica se as senhas coincidem
        if ($senha !== $confirmar_senha) {
            $erro = "As senhas não coincidem.";
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
                    header("Location: tela_login.php");
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
        <form method="POST" action="" id="form_registro">
            <fieldset>
                <legend>Registro</legend>
                <input type="text" id="nome" name="nome" placeholder="Nome">
                <input type="email" id="email" name="email" placeholder="E-mail">
                <input type="tel" id="telefone" name="telefone" placeholder="Telefone">
                <input type="password" id="senha" name="senha" placeholder="Senha">
                <input type="password" id="senha2" name="confirmar_senha" placeholder="Confirmar Senha">
                <span id="SenhaErro" style="color: red;"></span>

                <button type="submit" name="registrar-se">
                    Registrar-se
                </button>

            </fieldset>
        </form>
        
        <?php
        if ($erro) {
            echo "<div class='erro'> $erro </div>";
        }
        ?>

        <div class="texto-login">
            <span>Já tem uma conta?</span>
            <a href="tela_login.php">Entrar</a>
        </div>
    </main>
</body>
</html>
