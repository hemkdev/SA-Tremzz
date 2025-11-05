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

INSERT INTO suporte (usuario_id, assunto, descricao) VALUES
    (3, 'Problema no login', 'Não consigo fazer login na minha conta. Sempre recebo uma mensagem de erro dizendo que minhas credenciais são inválidas. Já tentei redefinir minha senha, mas o problema persiste. Preciso de ajuda urgente para acessar minha conta novamente.'),
    (4, 'Erro ao enviar mensagem', 'Estou enfrentando um problema ao tentar enviar mensagens através do sistema. Sempre que tento enviar uma mensagem, recebo um erro indicando que a mensagem não pôde ser entregue. Já verifiquei minha conexão com a internet e está funcionando corretamente. Preciso de assistência para resolver esse problema.');

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

INSERT INTO rotas (itinerario_id, maquinista_id, trem_id, estacao_origem_id, estacao_destino_id, via_estacao_id) VALUES
    (1, 2, 1, 1, 4, 2),
    (2, 3, 2, 4, 3, NULL);


INSERT INTO dados_sensores (id_sensor, id_itinerario, valor, carimbo_data) VALUES
    (1, 1, 123.4567, '2025-11-05 09:00:00'),
    (2, 1, 10.0000, '2025-11-05 09:05:00'),
    (3, 2, 25.5000, '2025-11-05 09:10:00');

INSERT INTO manutencoes (id_trem, tipo, data_agendada, data_conclusao, status) VALUES
    (1, 'técnica', '2025-11-10', '2025-11-12', 'Pendente'),
    (3, 'sistema', '2025-11-01', '2025-11-02', 'Concluída');
