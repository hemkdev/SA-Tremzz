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
    nome varchar(30) NOT NULL,
    texto varchar(87) NOT NULL,
    horario time NOT NULL,
    dia date NOT NULL,
    imagem ENUM('estação', 'bate-papo', 'usuario', 'trem')
);

CREATE TABLE atividades (
    destino varchar(30),
    rua varchar(50) NOT NULL,
    cep varchar(30) NOT NULL
)