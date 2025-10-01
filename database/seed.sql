USE tremzz_db;

INSERT INTO usuarios (id, nome, email, telefone, senha, cargo) VALUES
('0', 'administrador', 'admin@gmail.com', '(47) 98765-4321', '$2y$10$DKeblHu.0Jbk9pnBBhCcPOaWn.ZCilkXBTlW3xIf3Si9vXsEfTmUe', 'administrador'), -- senha: senha
('1', 'teste', 'teste@gmail.com', '(47) 12345-6789', '$2y$10$DKeblHu.0Jbk9pnBBhCcPOaWn.ZCilkXBTlW3xIf3Si9vXsEfTmUe', 'usuario') -- senha: senha

INSERT INTO mensagens (usuario_id, texto, horario, dia, imagem)
VALUES (0, 'As mensagens estão funcionando!', '08:28:00', '2025-10-01', 'bate-papo');