<!DOCTYPE html>
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
        <form>
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

        <div class="login">
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
            <span>Já tem uma conta?</span>
            <a href="../html/tela5.html">Login</a>
        </div>
    </main>
</body>
</html>