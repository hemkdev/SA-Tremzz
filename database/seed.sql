USE tremzz_db;

INSERT INTO usuarios (nome, email, senha, cargo) VALUES 
    ('Sistema', 'admin@gmail.com', '$2y$10$DKeblHu.0Jbk9pnBBhCcPOaWn.ZCilkXBTlW3xIf3Si9vXsEfTmUe', 'administrador'),
    ('Maquinista', 'maquinista@gmail.com', '$2y$10$DKeblHu.0Jbk9pnBBhCcPOaWn.ZCilkXBTlW3xIf3Si9vXsEfTmUe', 'maquinista'),
    ('Teste', 'teste@gmail.com', '$2y$10$DKeblHu.0Jbk9pnBBhCcPOaWn.ZCilkXBTlW3xIf3Si9vXsEfTmUe', 'usuário');


INSERT INTO mensagens (usuario_id, nome, texto, imagem) VALUES
    (2, 'Sistema', 'As mensagens estão funcionando!', 'bate-papo'),
    (2, 'Amigo do rogério maquinista', 'To chegando Rogério', 'estacao'),
    (3, 'Rogério maquinista', 'To chegando!', 'usuario');