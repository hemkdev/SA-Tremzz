USE tremzz_db;

INSERT INTO usuarios (nome, email, senha, cargo, foto) VALUES 
    ('Sistema', 'admin@gmail.com', '$2y$10$DKeblHu.0Jbk9pnBBhCcPOaWn.ZCilkXBTlW3xIf3Si9vXsEfTmUe', 'administrador', '../../assets/img/usuarios/admin.png');

INSERT INTO usuarios (nome, email, senha, cargo) VALUES 
    ('Bob ', 'bob@gmail.com', '$2y$10$DKeblHu.0Jbk9pnBBhCcPOaWn.ZCilkXBTlW3xIf3Si9vXsEfTmUe', 'maquinista'),
    ('joão', 'joao@gmail.com', '$2y$10$DKeblHu.0Jbk9pnBBhCcPOaWn.ZCilkXBTlW3xIf3Si9vXsEfTmUe', 'maquinista'),
    ('Teste', 'teste@gmail.com', '$2y$10$DKeblHu.0Jbk9pnBBhCcPOaWn.ZCilkXBTlW3xIf3Si9vXsEfTmUe', 'usuário');


INSERT INTO mensagens (usuario_id, nome, texto, imagem) VALUES
    (2, 'Sistema', 'As mensagens estão funcionando!', 'bate-papo'),
    (2, 'Amigo do rogério maquinista', 'To chegando Rogério', 'estacao'),
    (3, 'Rogério maquinista', 'To chegando!', 'usuario');

INSERT INTO sensores (localizacao, tipo, status) VALUES
    ('Estação 1', 'LDR', 'ativado'),
    ('Estação 2', 'Ultrassônico', 'ativado'),
    ('Estação 3', 'DHT11', 'desativado'),
    ('Estação principal', 'LDR', 'ativado');

INSERT INTO estacoes (nome) VALUES
    ('Estação 1'),
    ('Estação 2'),
    ('Estação 3'),
    ('Estação principal');

INSERT INTO itinerarios (nome, descricao) VALUES
    ('Itinerário matinal 512', 'Entrega de suprimentos'),
    ('Itinerário juvenil', 'Transporte de passageiros');

INSERT INTO trens (modelo, tipo_carga, status) VALUES
    ('Trem de carga pesada', 'Cargas industriais', 'Disponível'),
    ('Trem de passageiros', 'Passageiros', 'Em rota'),
    ('Trem expresso', 'Correio e encomendas', 'Em manutenção');

INSERT INTO rotas (itinerario_id, maquinista_id, trem_id, estacao_origem_id, estacao_destino_id, via_estacao_id, duracao_estimada) VALUES
    (1, 2, 1, 1, 4, 2),
    (2, 3, 2, 4, 3, NULL);
