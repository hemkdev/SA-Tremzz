CREATE DATABASE TREMzz_db;

USE TREMzz_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(15),
    senha VARCHAR(255) NOT NULL,
    cargo ENUM('usuario', 'administrador', 'maquinista') NOT NULL DEFAULT 'usuario',
    foto VARCHAR(255) DEFAULT '../../assets/img/perfil.png' NOT NULL
);

CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome VARCHAR(30) NOT NULL,
    texto VARCHAR(87) NOT NULL,
    imagem ENUM('estação', 'bate-papo', 'usuario', 'trem') NOT NULL,
    data_hora_envio DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT fk_usuario_mensagem FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE sensores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    localizacao ENUM('Estação 1', 'Estação 2', 'Estação 3', 'Estação principal') NOT NULL,
    tipo ENUM('LDR', 'Ultrassônico', 'DHT11') NOT NULL, -- Luminosidade, distância e temperatura/umidade
    status ENUM('ativado', 'desativado') NOT NULL,
    data_hora_adicao DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE TABLE estacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome ENUM('Estação 1', 'Estação 2', 'Estação 3', 'Estação principal') NOT NULL,
    descricao VARCHAR(255)
);

CREATE TABLE itinerarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,  
    descricao TEXT
);

CREATE TABLE dados_sensores (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_sensor INT NOT NULL,
    id_itinerario INT NOT NULL, 
    valor DECIMAL(12,4) NOT NULL,
    carimbo_data DATETIME,
    FOREIGN KEY (id_sensor) REFERENCES sensores(id) ON DELETE CASCADE,
    FOREIGN KEY (id_itinerario) REFERENCES itinerarios(id) ON DELETE CASCADE
);

CREATE TABLE Trens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    modelo VARCHAR (50) NOT NULL,
    tipo_carga VARCHAR(50) NOT NULL,
    status ENUM('Disponível', 'Em rota', 'Em manutenção')
);

CREATE TABLE manutencoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_trem INT,
    tipo ENUM('técnica', 'sistema'),
    data_agendada DATE NOT NULL,
    data_conclusao DATE NOT NULL,
    status ENUM ('Pendente', 'Em andamento', 'Concluída'),
    FOREIGN KEY (id_trem) REFERENCES Trens(id) ON DELETE CASCADE
    );

CREATE TABLE Rotas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    itinerario_id INT NOT NULL,
    maquinista_id INT NOT NULL,
    trem_id INT NOT NULL,
    estacao_origem_id INT NOT NULL,
    estacao_destino_id INT NOT NULL,
    via_estacao_id INT, 
    FOREIGN KEY (itinerario_id) REFERENCES Itinerarios(id) ON DELETE CASCADE,
    FOREIGN KEY (maquinista_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (trem_id) REFERENCES Trens(id) ON DELETE CASCADE,
    FOREIGN KEY (estacao_origem_id) REFERENCES Estacoes(id) ON DELETE RESTRICT,
    FOREIGN KEY (estacao_destino_id) REFERENCES Estacoes(id) ON DELETE RESTRICT,
    FOREIGN KEY (via_estacao_id) REFERENCES Estacoes(id) ON DELETE RESTRICT
);