CREATE TABLE Perfil (
	nomePerfil enum('administrador', 'simpatizante', 'utilizador') not null primary key
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE Pessoa (
	idPessoa int not null auto_increment primary key,
    nomePerfil enum('administrador', 'simpatizante', 'utilizador') not null,
    username varchar(20) not null,
    email varchar(120) not null,
    password varchar(20) not null,
    foreign key(nomePerfil) references Perfil(nomePerfil)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE Categoria (
    idCategoria int not null auto_increment primary key,
    nomeCategoria varchar(20) not null,
    idPrincipal int
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE PessoaCategoria (
    idPessoa int not null,
    idCategoria int not null,
    foreign key(idPessoa) references Pessoa(idPessoa),
    foreign key(idCategoria) references Categoria(idCategoria)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE Evento (
    idEvento int not null auto_increment primary key,
    idPessoa int not null,
    nomeEvento varchar(120) not null,
    publico boolean default false,
		descricao varchar(200),
		dataEvento date,
		horaEvento time,
    foreign key(idPessoa) references Pessoa(idPessoa)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE PessoaEvento (
    idPessoa int not null,
    idEvento int not null,
    foreign key(idPessoa) references Pessoa(idPessoa),
    foreign key(idEvento) references Evento(idEvento)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE EventoCategoria (
    idEvento int not null,
    idCategoria int not null,
    foreign key(idEvento) references Evento(idEvento),
    foreign key(idCategoria) references Categoria(idCategoria)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE Conteudo (
    idConteudo int not null auto_increment primary key,
    idPessoa int not null,
    nomeConteudo varchar(120) not null,
    descricao varchar(200),
    publico boolean not null default false,
    foreign key(idPessoa) references Pessoa(idPessoa)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE EventoConteudo (
    idEvento int not null,
    idConteudo int not null,
    foreign key(idEvento) references Evento(idEvento),
    foreign key(idConteudo) references Conteudo(idConteudo)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE emailAccounts (
  id INT NOT NULL AUTO_INCREMENT ,
  accountName VARCHAR( 32 ) NOT NULL ,
  useSSL TINYINT DEFAULT 0,
  smtpServer VARCHAR( 32 ) NOT NULL ,
  port INT NOT NULL ,
  timeout INT NOT NULL ,
  loginName VARCHAR( 128 ) NOT NULL ,
  email VARCHAR( 128 ) NOT NULL ,
	password VARCHAR( 128 ) NOT NULL ,
  displayName VARCHAR( 20 ) NOT NULL ,
  PRIMARY KEY (id)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;
