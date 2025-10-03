CREATE DATABASE TREMzz_db;

USE TREMzz_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(15) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    cargo ENUM('usuario', 'administrador', 'maquinista') NOT NULL DEFAULT 'usuario'
);

CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome VARCHAR(30) NOT NULL,
    texto VARCHAR(87) NOT NULL,
    horario TIME NOT NULL,
    dia DATE NOT NULL,
    imagem ENUM('estação', 'bate-papo', 'usuario', 'trem'),
    CONSTRAINT fk_usuario_mensagem FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    destino VARCHAR(30),
    rua VARCHAR(50) NOT NULL,
    cep VARCHAR(10) NOT NULL,
    CONSTRAINT fk_usuario_atividade FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE linhas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
)