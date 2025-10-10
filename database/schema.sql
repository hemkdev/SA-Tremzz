CREATE DATABASE TREMzz_db;

USE TREMzz_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(15),
    senha VARCHAR(255) NOT NULL,
    cargo ENUM('usuario', 'administrador', 'maquinista') NOT NULL DEFAULT 'usuario',
    foto_perfil ENUM('foto1.jpg','foto2.jpg','foto3.jpg')
);

CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome VARCHAR(30) NOT NULL,
    texto VARCHAR(87) NOT NULL,
    imagem ENUM('estação', 'bate-papo', 'usuario', 'trem'),
    data_hora_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_mensagem FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    destino VARCHAR(30),
    rua VARCHAR(50) NOT NULL,
    cep VARCHAR(10) NOT NULL,
    CONSTRAINT fk_usuario_atividade FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE linhas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    cor VARCHAR(50)
);

CREATE TABLE rotas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    linha_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    sentido ENUM('ida', 'volta') NOT NULL,
    maquinista_supervisor_id INT,
    CONSTRAINT fk_linha_rota FOREIGN KEY (linha_id) REFERENCES linhas(id) ON DELETE CASCADE,
    CONSTRAINT fk_maquinista_rota FOREIGN KEY (maquinista_supervisor_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE trens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_identificacao VARCHAR(50) NOT NULL UNIQUE,
    maquinista_responsavel_id INT,
    modelo VARCHAR(100),
    capacidade INT,
    status ENUM('operacional', 'manutencao', 'parado') NOT NULL DEFAULT 'operacional',
    CONSTRAINT fk_maquinista_trem FOREIGN KEY (maquinista_responsavel_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE itinerarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trem_id INT NOT NULL,
    rota_id INT NOT NULL,
    maquinista_id INT NOT NULL,
    horario_partida TIME NOT NULL,
    horario_chegada TIME NOT NULL,
    dia_semana VARCHAR(20) NOT NULL,
    CONSTRAINT fk_trem_itinerario FOREIGN KEY (trem_id) REFERENCES trens(id) ON DELETE CASCADE,
    CONSTRAINT fk_rota_itinerario FOREIGN KEY (rota_id) REFERENCES rotas(id) ON DELETE CASCADE,
    CONSTRAINT fk_maquinista_itinerario FOREIGN KEY (maquinista_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE sensores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trem_id INT,
    localizacao VARCHAR(100) NOT NULL,
    tipo ENUM('temperatura', 'velocidade', 'pressao', 'vibracao', 'outros') NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    unidade VARCHAR(20),
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_trem_sensor FOREIGN KEY (trem_id) REFERENCES trens(id) ON DELETE CASCADE
);

ALTER TABLE usuarios ADD COLUMN foto_perfil VARCHAR(255) DEFAULT '../assets/img/perfil.png' NULL;
UPDATE usuarios SET foto = '../assets/img/perfil.png' WHERE foto IS NULL OR foto = '';