
INSERT INTO `Perfil`(`nomePerfil`) VALUES ('Administrador');
INSERT INTO `Perfil`(`nomePerfil`) VALUES ('Simpatizante');
INSERT INTO `Perfil`(`nomePerfil`) VALUES ('Utilizador');

INSERT INTO `Pessoa`(`idPessoa`, `nomePerfil`, `username`, `email`, `password`) VALUES (1,'Administrador','admin1','admin1@gmail.com','123');
INSERT INTO `Pessoa`(`idPessoa`, `nomePerfil`, `username`, `email`, `password`) VALUES (2,'Administrador','admin2','admin2@gmail.com','1234');

INSERT INTO `Categoria`(`idCategoria`, `nomeCategoria`, `idPrincipal`) VALUES (1,'Moda',0);
INSERT INTO `Categoria`(`idCategoria`, `nomeCategoria`, `idPrincipal`) VALUES (2,'Festa',0);
INSERT INTO `Categoria`(`idCategoria`, `nomeCategoria`, `idPrincipal`) VALUES (3,'Roupa',1);

INSERT INTO `emailAccounts`(`accountName`, `useSSL`, `smtpServer`, `port`, `timeout`, `loginName`, `email`, `password`, `displayName`) VALUES('GMAIL', '1', 'smtp.gmail.com', '465', '30', '<>@gmail.com', '<>@gmail.com', '...', '...');
