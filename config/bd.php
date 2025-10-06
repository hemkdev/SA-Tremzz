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

    // FALTANTES //

    // USUÁRIO
    // Perfil -> Página de suporte
    // Perfil -> Página de notificações
    // Perfil -> Privacidade -> Verificação de telefone
    // Perfil -> Página de ultimas atividades/Relatórios
    // Buscar -> Vizualização de rotas

    // ADM
    // Perfil -> Página de gerenciamento de usuários
    // Perfil -> Página de gerenciamento de notificações
    // Gerenciamento -> CRUD para gerenciamento de sensores
    // Gerenciamento -> CRUD para gerenciamento de itinerários
    // Gerenciamento -> CRUD para gerenciamento de linhas
    // Gerenciamento -> CRUD para gerenciamento de trens
    // Gerenciamento -> CRUD para gerenciamento de rotas

    // MAQUINISTA
    // Todas as páginas
    // Página home com vizualização de trens,linhas e rotas que estão ligadas ao maquinista
    // Página para adição de itinerários e relatórios, com horário de partida e chegada
    // Página de perfil
    // Página de chat para mandar mensagens para usuários

    // BANCO DE DADOS
    // Tabela -> linhas
    // Tabela -> rotas
    // Tabela -> trens
    // Tabela -> itinerários
    // Tabela -> sensores
    // FOREIGN KEY do maquinista -> trens, itinerários e linhas
    // FOREIGN KEY do trens -> rotas, linhas

?>
