<?php
session_start();

// Verificação de sessão e admin (essencial para segurança)
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true || !isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: ../../login.php");  // Ajuste o caminho se necessário
    exit;
}

require "../../../config/bd.php";  // Sua conexão MySQLi (ajuste o caminho se necessário)

// Processar apenas se for POST e o botão "editar" foi clicado (para adição ou edição)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    // Sanitizar e validar inputs
    $id = intval($_POST['id'] ?? 0);  // ID do registro (0 para adição)
    $itinerario_id = intval($_POST['itinerario_id'] ?? 0);
    $maquinista_id = intval($_POST['maquinista_id'] ?? 0);
    $trem_id = intval($_POST['trem_id'] ?? 0);
    $estacao_origem_id = intval($_POST['estacao_origem_id'] ?? 0);
    $estacao_destino_id = intval($_POST['estacao_destino_id'] ?? 0);
    $via_estacao_id = intval($_POST['via_estacao_id'] ?? 0);  // Pode ser 0 (opcional)

        // Se via_estacao_id for 0 ou vazio, envia como NULL para o banco
        $via_estacao_id_db = ($via_estacao_id > 0) ? $via_estacao_id : null;

    // Validações básicas: Todos os IDs devem ser inteiros positivos, e duracao_estimada deve ser maior que 0
    if ($itinerario_id <= 0 || $maquinista_id <= 0 || $trem_id <= 0 || $estacao_origem_id <= 0 || $estacao_destino_id <= 0 ) {
        $_SESSION['erro'] = "Todos os campos obrigatórios devem ser IDs válidos e positivos.";
        header("Location: ../linhas.php?erro=1");
        exit;
    }
    // via_estacao_id é opcional, então não é verificado como obrigatório

    if ($id === 0) {  // Adição (INSERT)
            $stmt = $conn->prepare("INSERT INTO rotas (itinerario_id, maquinista_id, trem_id, estacao_origem_id, estacao_destino_id, via_estacao_id) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                $_SESSION['erro'] = "Erro ao preparar INSERT: " . $conn->error;
                header("Location: ../linhas.php?erro=1");
                exit;
            }
            // Se via_estacao_id_db for null, passa como referência e altera para null
            if ($via_estacao_id_db === null) {
                $null = null;
                $stmt->bind_param("iiiiii", $itinerario_id, $maquinista_id, $trem_id, $estacao_origem_id, $estacao_destino_id, $null);
            } else {
                $stmt->bind_param("iiiiii", $itinerario_id, $maquinista_id, $trem_id, $estacao_origem_id, $estacao_destino_id, $via_estacao_id_db);
            }
            if ($stmt->execute()) {
                $_SESSION['sucesso'] = "Rota adicionada com sucesso!";
                $stmt->close();
                $conn->close();
                header("Location: ../linhas.php?sucesso=1");
                exit;
            } else {
                $_SESSION['erro'] = "Erro ao adicionar rota: " . $stmt->error;
                $stmt->close();
            }
    } else {  // Edição (UPDATE)
            $stmt = $conn->prepare("UPDATE rotas SET itinerario_id = ?, maquinista_id = ?, trem_id = ?, estacao_origem_id = ?, estacao_destino_id = ?, via_estacao_id = ? WHERE id = ?");
            if (!$stmt) {
                $_SESSION['erro'] = "Erro ao preparar UPDATE: " . $conn->error;
                header("Location: ../linhas.php?erro=1");
                exit;
            }
            if ($via_estacao_id_db === null) {
                $null = null;
                $stmt->bind_param("iiiiiii", $itinerario_id, $maquinista_id, $trem_id, $estacao_origem_id, $estacao_destino_id, $null, $id);
            } else {
                $stmt->bind_param("iiiiiii", $itinerario_id, $maquinista_id, $trem_id, $estacao_origem_id, $estacao_destino_id, $via_estacao_id_db, $id);
            }
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $_SESSION['sucesso'] = "Rota atualizada com sucesso!";
                } else {
                    $_SESSION['erro'] = "Nenhuma rota encontrada com este ID.";
                }
                $stmt->close();
                $conn->close();
                header("Location: ../linhas.php?sucesso=1");
                exit;
            } else {
                $_SESSION['erro'] = "Erro ao atualizar rota: " . $stmt->error;
                $stmt->close();
            }
    }

    // Fechar conexão em caso de erro
    $conn->close();
    header("Location: ../linhas.php?erro=1");
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletar'])) {
    // Processar exclusão (baseado no seu exemplo, mas usando prepared statement para segurança)
    $id = intval($_GET['id'] ?? 0);  // Captura o ID da URL ou POST

    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM rotas WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['sucesso'] = "Rota excluída com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao excluir rota: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['erro'] = "Erro ao preparar exclusão: " . $conn->error;
        }
    } else {
        $_SESSION['erro'] = "ID inválido para exclusão.";
    }

    $conn->close();
    header("Location: ../linhas.php?sucesso=1");  // Ou ?erro=1 se falhar
    exit;
} else {
    // Se não for POST válido, redirecione para evitar acesso direto
    header("Location: ../linhas.php");
    exit;
}
