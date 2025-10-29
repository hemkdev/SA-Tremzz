<?php
session_start();

if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true || !isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: ../../login.php"); 
    exit;
}

require "../../../config/bd.php";

// Obter ação da requisição
$action = $_GET['action'] ?? '';

// Para requisições POST, obter os dados do corpo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Se não for JSON, tenta usar POST normal
        $input = $_POST;
    }
} else {
    $input = $_GET;
}

switch ($action) {
    case 'get_stations':
        getStations($conn);
        break;
        
    case 'get_routes':
        getRoutes($conn);
        break;
    case 'get_itinerarios':
        getItinerarios($conn);
        break;
    case 'get_maquinistas':
        getMaquinistas($conn);
        break;
        
    case 'save_station':
        saveStation($conn, $input);
        break;
        
    case 'delete_station':
        deleteStation($conn, $input);
        break;
        
    case 'save_route':
        saveRoute($conn, $input);
        break;
        
    case 'delete_route':
        deleteRoute($conn, $input);
        break;
        
    case 'update_station_position':
        updateStationPosition($conn, $input);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
        break;
}

// Função para obter estações
function getStations($conn) {
    try {
        // Detectar se colunas latitude/longitude existem
        $colsRes = $conn->query("SHOW COLUMNS FROM estacoes LIKE 'latitude'");
        $hasLat = ($colsRes && $colsRes->num_rows > 0);
        $colsRes = $conn->query("SHOW COLUMNS FROM estacoes LIKE 'longitude'");
        $hasLng = ($colsRes && $colsRes->num_rows > 0);

        $select = 'id, nome, descricao';
        if ($hasLat) $select .= ', latitude';
        if ($hasLng) $select .= ', longitude';

        $result = $conn->query("SELECT $select FROM estacoes ORDER BY nome");
        if ($result) {
            $stations = [];
            while ($row = $result->fetch_assoc()) {
                // Fornecer lat/lng como números ou null
                if (isset($row['latitude'])) $row['latitude'] = $row['latitude'] !== null ? (float)$row['latitude'] : null;
                if (isset($row['longitude'])) $row['longitude'] = $row['longitude'] !== null ? (float)$row['longitude'] : null;
                $stations[] = $row;
            }
            echo json_encode($stations);
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao obter estações: ' . $e->getMessage()]);
    }
}

// Função para obter rotas com suas estações
function getRoutes($conn) {
    try {
    // O schema atual usa as tabelas `Rotas` e `itinerarios` (origem/destino/via). Não usamos uma tabela rota_estacoes.
        // Vamos retornar informações da rota juntando nomes dos itinerários, maquinista e trem, e os nomes das estações de origem/destino/via.
        $sql = "SELECT r.id, r.itinerario_id, r.maquinista_id, r.trem_id, r.estacao_origem_id, r.estacao_destino_id, r.via_estacao_id,
                       it.nome AS itinerario_nome,
                       t.modelo AS trem_modelo,
                       u.nome AS maquinista_nome,
                       eo.nome AS origem_nome, ed.nome AS destino_nome, ev.nome AS via_nome
                FROM Rotas r
                LEFT JOIN itinerarios it ON it.id = r.itinerario_id
                LEFT JOIN Trens t ON t.id = r.trem_id
                LEFT JOIN usuarios u ON u.id = r.maquinista_id
                LEFT JOIN estacoes eo ON eo.id = r.estacao_origem_id
                LEFT JOIN estacoes ed ON ed.id = r.estacao_destino_id
                LEFT JOIN estacoes ev ON ev.id = r.via_estacao_id
                ORDER BY r.id";

        $result = $conn->query($sql);
        if (!$result) throw new Exception($conn->error);

        $routes = [];
        while ($row = $result->fetch_assoc()) {
            // Structure route with station ids and names. Frontend may provide estacoes array; here we return available station ids and names.
            $route = [
                'id' => (int)$row['id'],
                'itinerario_id' => $row['itinerario_id'] !== null ? (int)$row['itinerario_id'] : null,
                'itinerario_nome' => $row['itinerario_nome'] ?? null,
                'maquinista_id' => $row['maquinista_id'] !== null ? (int)$row['maquinista_id'] : null,
                'maquinista_nome' => $row['maquinista_nome'] ?? null,
                'trem_id' => $row['trem_id'] !== null ? (int)$row['trem_id'] : null,
                'trem_modelo' => $row['trem_modelo'] ?? null,
                'estacao_origem_id' => $row['estacao_origem_id'] !== null ? (int)$row['estacao_origem_id'] : null,
                'origem_nome' => $row['origem_nome'] ?? null,
                'estacao_destino_id' => $row['estacao_destino_id'] !== null ? (int)$row['estacao_destino_id'] : null,
                'destino_nome' => $row['destino_nome'] ?? null,
                'via_estacao_id' => $row['via_estacao_id'] !== null ? (int)$row['via_estacao_id'] : null,
                'via_nome' => $row['via_nome'] ?? null,
            ];

            // If coordinates for these stations exist, fetch them to help drawing a polyline in frontend
            $stationIds = array_filter([$route['estacao_origem_id'], $route['via_estacao_id'], $route['estacao_destino_id']]);
            $route['estacoes'] = [];
            if (count($stationIds) > 0) {
                $in = implode(',', array_map('intval', $stationIds));
                // Check if latitude/longitude exist in estacoes
                $colsRes = $conn->query("SHOW COLUMNS FROM estacoes LIKE 'latitude'");
                $hasLat = ($colsRes && $colsRes->num_rows > 0);
                $colsRes = $conn->query("SHOW COLUMNS FROM estacoes LIKE 'longitude'");
                $hasLng = ($colsRes && $colsRes->num_rows > 0);

                if ($hasLat && $hasLng) {
                    $q = $conn->query("SELECT id, nome, latitude, longitude FROM estacoes WHERE id IN ($in)");
                    while ($s = $q->fetch_assoc()) {
                        $s['latitude'] = $s['latitude'] !== null ? (float)$s['latitude'] : null;
                        $s['longitude'] = $s['longitude'] !== null ? (float)$s['longitude'] : null;
                        $route['estacoes'][] = $s;
                    }
                    // Sort estacoes to origin -> via -> destino order
                    usort($route['estacoes'], function($a, $b) use ($route) {
                        $order = [];
                        if ($route['estacao_origem_id']) $order[] = $route['estacao_origem_id'];
                        if ($route['via_estacao_id']) $order[] = $route['via_estacao_id'];
                        if ($route['estacao_destino_id']) $order[] = $route['estacao_destino_id'];
                        $ia = array_search($a['id'], $order);
                        $ib = array_search($b['id'], $order);
                        return $ia - $ib;
                    });
                } else {
                    // If no coordinates, return just station ids/names
                    $q = $conn->query("SELECT id, nome FROM estacoes WHERE id IN ($in)");
                    while ($s = $q->fetch_assoc()) {
                        $route['estacoes'][] = $s;
                    }
                }
            }

            $routes[] = $route;
        }

        echo json_encode($routes);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao obter rotas: ' . $e->getMessage()]);
    }
}

// Função para obter itinerários
function getItinerarios($conn) {
    try {
        $result = $conn->query("SELECT id, nome FROM itinerarios ORDER BY nome");
        if (!$result) throw new Exception($conn->error);

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        echo json_encode($items);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao obter itinerários: ' . $e->getMessage()]);
    }
}

// Função para obter maquinistas (usuários com cargo 'maquinista')
function getMaquinistas($conn) {
    try {
        $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE cargo = 'maquinista' ORDER BY nome");
        $stmt->execute();
        $res = $stmt->get_result();
        $items = [];
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        echo json_encode($items);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao obter maquinistas: ' . $e->getMessage()]);
    }
}

// Função para salvar estação
function saveStation($conn, $input) {
    try {
        $id = $input['id'] ?? null;
        $nome = $input['nome'] ?? '';
        $endereco = $input['endereco'] ?? '';
        $latitude = isset($input['latitude']) ? $input['latitude'] : null;
        $longitude = isset($input['longitude']) ? $input['longitude'] : null;

        // Verificar se colunas latitude/longitude existem para aplicar validação condicional
        $colsRes = $conn->query("SHOW COLUMNS FROM estacoes LIKE 'latitude'");
        $hasLat = ($colsRes && $colsRes->num_rows > 0);
        $colsRes = $conn->query("SHOW COLUMNS FROM estacoes LIKE 'longitude'");
        $hasLng = ($colsRes && $colsRes->num_rows > 0);

        if (empty($nome) || ($hasLat && ($latitude === null || $latitude === '')) || ($hasLng && ($longitude === null || $longitude === ''))) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            return;
        }
        
        // Verificar se colunas latitude/longitude existem
        $colsRes = $conn->query("SHOW COLUMNS FROM estacoes LIKE 'latitude'");
        $hasLat = ($colsRes && $colsRes->num_rows > 0);
        $colsRes = $conn->query("SHOW COLUMNS FROM estacoes LIKE 'longitude'");
        $hasLng = ($colsRes && $colsRes->num_rows > 0);

        if ($id) {
            // Atualizar estação existente
            if ($hasLat && $hasLng) {
                $stmt = $conn->prepare("UPDATE estacoes SET nome = ?, descricao = ?, latitude = ?, longitude = ? WHERE id = ?");
                $stmt->bind_param("ssddi", $nome, $endereco, $latitude, $longitude, $id);
            } else {
                $stmt = $conn->prepare("UPDATE estacoes SET nome = ?, descricao = ? WHERE id = ?");
                $stmt->bind_param("ssi", $nome, $endereco, $id);
            }
        } else {
            // Inserir nova estação
            if ($hasLat && $hasLng) {
                $stmt = $conn->prepare("INSERT INTO estacoes (nome, descricao, latitude, longitude) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssdd", $nome, $endereco, $latitude, $longitude);
            } else {
                $stmt = $conn->prepare("INSERT INTO estacoes (nome, descricao) VALUES (?, ?)");
                $stmt->bind_param("ss", $nome, $endereco);
            }
        }
        
        if ($stmt->execute()) {
            $newId = $id ?: $conn->insert_id;
            echo json_encode(['success' => true, 'id' => $newId]);
        } else {
            throw new Exception($stmt->error);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar estação: ' . $e->getMessage()]);
    }
}

// Função para excluir estação
function deleteStation($conn, $input) {
    try {
        $id = $input['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
            return;
        }
        
        // Verificar se a estação está sendo usada em Rotas (origem/destino/via)
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Rotas WHERE estacao_origem_id = ? OR estacao_destino_id = ? OR via_estacao_id = ?");
        $stmt->bind_param("iii", $id, $id, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Não é possível excluir a estação pois ela está sendo usada em uma ou mais rotas']);
            return;
        }
        
        // Excluir estação
        $stmt = $conn->prepare("DELETE FROM estacoes WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception($stmt->error);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir estação: ' . $e->getMessage()]);
    }
}

// Função para salvar rota
function saveRoute($conn, $input) {
    try {
        // O schema atual define a tabela `Rotas` com campos: itinerario_id, maquinista_id, trem_id, estacao_origem_id, estacao_destino_id, via_estacao_id
        // Aceitamos input em duas formas: campos específicos (itinerario_id, maquinista_id, trem_id, origem, destino, via)
        // ou um array 'estacoes' onde mapeamos [0]=origem, [n-1]=destino, [1]=via (se existir).

        $itinerario_id = isset($input['itinerario_id']) ? (int)$input['itinerario_id'] : null;
        $maquinista_id = isset($input['maquinista_id']) ? (int)$input['maquinista_id'] : null;
        $trem_id = isset($input['trem_id']) ? (int)$input['trem_id'] : null;

        $estacoes = [];
        if (isset($input['estacoes'])) {
            if (is_string($input['estacoes'])) $estacoes = json_decode($input['estacoes'], true);
            else $estacoes = $input['estacoes'];
        }

        $origem = isset($input['estacao_origem_id']) ? (int)$input['estacao_origem_id'] : null;
        $destino = isset($input['estacao_destino_id']) ? (int)$input['estacao_destino_id'] : null;
        $via = isset($input['via_estacao_id']) ? (int)$input['via_estacao_id'] : null;

        if (is_array($estacoes) && count($estacoes) >= 2) {
            $origem = (int)$estacoes[0];
            $destino = (int)$estacoes[count($estacoes) - 1];
            if (count($estacoes) > 2) $via = (int)$estacoes[1];
        }

        if (!$origem || !$destino) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos: é necessário origem e destino.']);
            return;
        }

        // itinerario_id é obrigatório conforme schema
        if (!$itinerario_id || $itinerario_id === 0) {
            echo json_encode(['success' => false, 'message' => 'itinerario_id é obrigatório']);
            return;
        }

        // maquinista_id também é obrigatório conforme restrição do schema
        if (!$maquinista_id || $maquinista_id === 0) {
            echo json_encode(['success' => false, 'message' => 'maquinista_id é obrigatório']);
            return;
        }

        // Inserir rota na tabela Rotas. Montamos placeholders apenas para os valores que não são NULL.
        $allCols = ['itinerario_id', 'maquinista_id', 'trem_id', 'estacao_origem_id', 'estacao_destino_id', 'via_estacao_id'];
        $allVals = [$itinerario_id, $maquinista_id, $trem_id, $origem, $destino, $via];

        $placeholders = [];
        $params = [];
        $types = '';

        foreach ($allVals as $idx => $val) {
            if ($val === null || $val === 0) {
                $placeholders[] = 'NULL';
            } else {
                $placeholders[] = '?';
                $types .= 'i';
                $params[] = $val;
            }
        }

        $sql = 'INSERT INTO Rotas (' . implode(', ', $allCols) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception($conn->error);
        }

        if (count($params) > 0) {
            // bind_param needs arguments passed by reference
            $bindArgs = array_merge([$types], $params);
            $refs = [];
            foreach ($bindArgs as $key => $value) {
                $refs[$key] = &$bindArgs[$key];
            }
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $id_rota = $conn->insert_id;
        $stmt->close();

        echo json_encode(['success' => true, 'id' => $id_rota]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar rota: ' . $e->getMessage()]);
    }
}

// Função para excluir rota
function deleteRoute($conn, $input) {
    try {
        $id = $input['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
            return;
        }
        
    // Excluir rota
    $stmt = $conn->prepare("DELETE FROM Rotas WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception($stmt->error);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir rota: ' . $e->getMessage()]);
    }
}

// Função para atualizar posição da estação
function updateStationPosition($conn, $input) {
    try {
        $id = $input['id'] ?? null;
        $latitude = $input['latitude'] ?? 0;
        $longitude = $input['longitude'] ?? 0;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
            return;
        }

        // Verificar se colunas existem
        $colsRes = $conn->query("SHOW COLUMNS FROM estacoes LIKE 'latitude'");
        $hasLat = ($colsRes && $colsRes->num_rows > 0);
        $colsRes = $conn->query("SHOW COLUMNS FROM estacoes LIKE 'longitude'");
        $hasLng = ($colsRes && $colsRes->num_rows > 0);

        if (!($hasLat && $hasLng)) {
            echo json_encode(['success' => false, 'message' => 'Colunas latitude/longitude não existem na tabela estacoes']);
            return;
        }

        $stmt = $conn->prepare("UPDATE estacoes SET latitude = ?, longitude = ? WHERE id = ?");
        $stmt->bind_param("ddi", $latitude, $longitude, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception($stmt->error);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar posição: ' . $e->getMessage()]);
    }
}

// Fechar conexão
$conn->close();
?>