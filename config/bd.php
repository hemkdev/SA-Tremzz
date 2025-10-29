<?php

$servidor = "localhost";
$usuario = "root";
$senha = "root";
$dbname = "tremzz_db";

$conn = new mysqli($servidor, $usuario, $senha, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// USUÁRIO
// Perfil -> Página de notificações
// Perfil -> Privacidade -> Verificação de telefone
// Perfil -> Página de ultimas atividades/Relatórios
// Buscar -> Vizualização de rotas

// ADM
// Perfil -> Página de gerenciamento de usuários -- Editar cargo no modal
// Perfil -> Página de gerenciamento de notificações
// Gerenciamento -> CRUD para manutenção de trens --
