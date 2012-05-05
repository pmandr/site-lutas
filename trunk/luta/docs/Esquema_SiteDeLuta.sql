CREATE TABLE Local(
      nomeLoc VARCHAR(20) NOT NULL,
      cidade VARCHAR(10) NOT NULL,
      estado CHAR(2) NOT NULL,
      endereco VARCHAR(20) NOT NULL,
      capacidade INTEGER NOT NULL DEFAULT 200,

      CONSTRAINT pk_Local PRIMARY KEY (nomeLoc)
) ENGINE=InnoDB;

CREATE TABLE OrganizacaoPromotora(
      CNPJ INTEGER NOT NULL,
      nome VARCHAR(20) NOT NULL DEFAULT 'Promotora',
      presidente VARCHAR(20) DEFAULT 'Dana White',
      telefone INTEGER,

      CONSTRAINT pk_OrgProm PRIMARY KEY (CNPJ)
) ENGINE=InnoDB;

CREATE TABLE EventoDeLuta(
      nro INTEGER NOT NULL AUTO_INCREMENT,
      nome VARCHAR(40) NOT NULL DEFAULT 'UFC',
      data DATE NOT NULL,
      local VARCHAR(20) NOT NULL,
      horario CHAR(5) DEFAULT '08:00',      
      cnpjPromotora INTEGER,
      lucro FLOAT DEFAULT 0.0,
      custo FLOAT DEFAULT 1000.0,
      responsavel VARCHAR(20) COMMENT 'promoter',
      qtdIngressosOferecidos INTEGER DEFAULT 100,

      
      CONSTRAINT pk_siteeventos PRIMARY KEY (nro),
      CONSTRAINT sk_siteeventos UNIQUE (data,local),
      CONSTRAINT fhorario CHECK(horario LIKE '__:__'),
      CONSTRAINT fk_loc FOREIGN KEY(local) REFERENCES Local(nomeLoc) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT fk_promotora FOREIGN KEY(cnpjPromotora) REFERENCES OrganizacaoPromotora(CNPJ) ON DELETE CASCADE ON UPDATE CASCADE /*,
      CONSTRAINT max_cap CHECK(qtdIngressosOferecidos <= Local(capacidade)) */
) ENGINE=InnoDB;

CREATE TABLE Tels_Evento(
      numEvento INTEGER NOT NULL,
      telefone INTEGER NOT NULL,
      
      CONSTRAINT pk_Tels PRIMARY KEY (numEvento, telefone),
      CONSTRAINT fkTels FOREIGN KEY (numEvento) REFERENCES EventoDeLuta(nro) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE EmpresaPatrocinadora(
      CNPJ INTEGER NOT NULL,
      nome VARCHAR(20) NOT NULL,
      website VARCHAR(30),

      CONSTRAINT pk_PAT PRIMARY KEY (CNPJ)
) ENGINE=InnoDB;

CREATE TABLE Tels_Patrocinador(
     cnpjPat INTEGER NOT NULL,
     telefone INTEGER NOT NULL,
     
     CONSTRAINT pk_TelsPat PRIMARY KEY (cnpjPat,telefone),
     CONSTRAINT fk_TelsPat FOREIGN KEY (cnpjPat) REFERENCES EmpresaPatrocinadora(CNPJ) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Contatos_Patrocinador(
     cnpjPat INTEGER NOT NULL,
     nomeContato VARCHAR(20) NOT NULL,
     
     CONSTRAINT pk_ContPat PRIMARY KEY (cnpjPat,nomeContato),
     CONSTRAINT fk_ContPat FOREIGN KEY (cnpjPat) REFERENCES EmpresaPatrocinadora(CNPJ) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
      

CREATE TABLE Patrocinio(
     nroContrato INTEGER NOT NULL AUTO_INCREMENT,
     numEvento INTEGER NOT NULL,
     cnpjPat INTEGER NOT NULL,

     CONSTRAINT pk_pat PRIMARY KEY (nroContrato),
     CONSTRAINT fk_patro FOREIGN KEY (cnpjPat) REFERENCES EmpresaPatrocinadora(CNPJ) ON DELETE CASCADE ON UPDATE CASCADE,
     CONSTRAINT fk_evento FOREIGN KEY (numEvento) REFERENCES EventoDeLuta(nro) ON DELETE CASCADE ON UPDATE CASCADE 
) ENGINE=InnoDB;

CREATE TABLE InfosPatrocinio(
      nroContrato INTEGER NOT NULL,
      preco FLOAT NOT NULL DEFAULT 200.00,
      termos VARCHAR(100),

      CONSTRAINT pk_infospat PRIMARY KEY (nroContrato),
      CONSTRAINT fk_infospat FOREIGN KEY (nroContrato) REFERENCES Patrocinio(nroContrato) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Categoria(
      tipo VARCHAR(10) NOT NULL,
      faixaDePeso CHAR(11) NOT NULL,

      CONSTRAINT pkCat PRIMARY KEY (tipo),
      CONSTRAINT faixa CHECK((faixaDePeso) LIKE '__KG A __KG')
) ENGINE=InnoDB;

CREATE TABLE Lutador(
      CPF INTEGER NOT NULL,
      nome VARCHAR(20) NOT NULL,
      nomeFantasia VARCHAR(20) NOT NULL,
      peso INTEGER,
      altura INTEGER,
      envergadura INTEGER,
      nacionalidade VARCHAR(15),
      vitorias INTEGER,
      empates INTEGER,

      derrotas INTEGER,
      categoria VARCHAR(10),
      classificacaoCat INTEGER,

      CONSTRAINT pkLut PRIMARY KEY (CPF),
      CONSTRAINT fkCat FOREIGN KEY (categoria) REFERENCES Categoria(tipo) ON DELETE SET NULL ON UPDATE CASCADE 
) ENGINE=InnoDB;

CREATE TABLE Luta(
      codLuta INTEGER NOT NULL AUTO_INCREMENT,
      Lut1 INTEGER NOT NULL,
      Lut2 INTEGER NOT NULL,
      numEvento INTEGER NOT NULL,

      CONSTRAINT pk_luta PRIMARY KEY (codLuta),
      CONSTRAINT fk_lut1 FOREIGN KEY (Lut1) REFERENCES Lutador(CPF) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT fk_lut2 FOREIGN KEY (Lut2) REFERENCES Lutador(CPF) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT fk_lutaevento FOREIGN KEY (numEvento) REFERENCES EventoDeLuta(nro) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE InfosLuta(
      luta INTEGER NOT NULL,
      horario CHAR(5),
      juiz VARCHAR(15),
      pontL1 INTEGER NOT NULL,

      pontL2 INTEGER NOT NULL,

      CONSTRAINT fhorario CHECK(horario LIKE '__:__'),
      CONSTRAINT pkInfosLuta PRIMARY KEY (luta),
      CONSTRAINT fkInfosLuta FOREIGN KEY (luta) REFERENCES Luta(codLuta) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE HabilidadesLut(
      lutador INTEGER NOT NULL,
      habilidade VARCHAR(10) NOT NULL,

      CONSTRAINT pkHab PRIMARY KEY (lutador,habilidade),
      CONSTRAINT fkLut FOREIGN KEY (lutador) REFERENCES Lutador(CPF) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE EmissoraDeTV(
     CNPJ INTEGER NOT NULL,
     nome VARCHAR(15) NOT NULL,
     agenda VARCHAR(50),

     CONSTRAINT pkEmi PRIMARY KEY(CNPJ)
) ENGINE=InnoDB;

CREATE TABLE Transmissao(
      Id INTEGER NOT NULL,
      emissora INTEGER NOT NULL,
      luta INTEGER NOT NULL,

      CONSTRAINT pkTrans PRIMARY KEY (Id),
      CONSTRAINT fkEmi FOREIGN KEY (emissora) REFERENCES EmissoraDeTV(CNPJ) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT fkLuta FOREIGN KEY (luta) REFERENCES Luta(codLuta) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE InfosTransmissao(
     Id INTEGER NOT NULL,
     horarioTrans CHAR(5) NOT NULL,
     dia DATE,
     narrador VARCHAR(15) NOT NULL,
     comentarista1 VARCHAR(15),
     comentarista2 VARCHAR(15),

     CONSTRAINT pkITrans PRIMARY KEY (Id),
     CONSTRAINT checaHorario CHECK((horarioTrans) LIKE '__:__'),
     CONSTRAINT fkITrans FOREIGN KEY (Id) REFERENCES Transmissao(Id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ReporterTrans(
     transmissao INTEGER NOT NULL,     reporter VARCHAR(15) NOT NULL,

     CONSTRAINT pkRepTrans PRIMARY KEY (transmissao, reporter),
     CONSTRAINT fkReptTrans FOREIGN KEY (transmissao) REFERENCES Transmissao(Id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE CanaisEmissora(
     emissora INTEGER NOT NULL,
     canal INTEGER NOT NULL,

     CONSTRAINT pkCanal PRIMARY KEY (emissora,canal),
     CONSTRAINT fkCanal FOREIGN KEY (emissora) REFERENCES EmissoraDeTV(CNPJ) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Website(
     endereco VARCHAR(30) NOT NULL,
     responsavel VARCHAR(20),

     CONSTRAINT pkWebsite PRIMARY KEY (endereco)
) ENGINE=InnoDB;

CREATE TABLE DivulgacaoEvento(
     website VARCHAR(30) NOT NULL,
     evento INTEGER NOT NULL, 
     nroAcessos INTEGER NOT NULL DEFAULT 0,

     CONSTRAINT pkDivulgacao PRIMARY KEY(website, evento),
     CONSTRAINT fkEve FOREIGN KEY(evento) REFERENCES EventoDeLuta(nro) ON DELETE CASCADE ON UPDATE CASCADE,
     CONSTRAINT fkWeb FOREIGN KEY(website) REFERENCES Website(endereco) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Cliente(
     CPF INTEGER NOT NULL,
     email VARCHAR(30) NOT NULL,
     nome VARCHAR (30) NOT NULL,
     endereco VARCHAR(50) NOT NULL,

     CONSTRAINT pkCli PRIMARY KEY (CPF),
     CONSTRAINT 
skClie UNIQUE (email)
) ENGINE=InnoDB;

CREATE TABLE CartoesCliente(
     cliente INTEGER NOT NULL,
     infosCartao VARCHAR(50) NOT NULL,
 
     CONSTRAINT pkCar PRIMARY KEY(cliente, infosCartao),
     CONSTRAINT fkCar FOREIGN KEY(cliente) REFERENCES Cliente(CPF) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE TelsCliente(
     cliente INTEGER NOT NULL,
     telefone INTEGER NOT NULL,
 
     CONSTRAINT pkTcli PRIMARY KEY(cliente, telefone),
     CONSTRAINT fkTcli FOREIGN KEY(cliente) REFERENCES Cliente(CPF) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Compra(
     nroCompra INTEGER NOT NULL,
     comprador INTEGER NOT NULL,
     preco FLOAT NOT NULL,
     quantidade INTEGER NOT NULL,
     desconto FLOAT DEFAULT 0.0,
     endEntrega VARCHAR(30),

     CONSTRAINT pkCompra PRIMARY KEY (nroCompra),
     CONSTRAINT fkComprad FOREIGN KEY (comprador) REFERENCES Cliente(CPF) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Ingresso(
     nroSerie INTEGER NOT NULL,
     numEvento INTEGER NOT NULL,
     preco FLOAT NOT NULL DEFAULT 10.00,
     assento VARCHAR(10),
     compra INTEGER,

     CONSTRAINT pkIngre PRIMARY KEY (nroSerie),
     CONSTRAINT fkIngreEve FOREIGN KEY(numEvento) REFERENCES EventoDeLuta(nro) ON DELETE CASCADE ON UPDATE CASCADE,
     CONSTRAINT fkIngreCompra FOREIGN KEY(compra) REFERENCES Compra(nroCompra) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE PontoDeVenda(
     nome VARCHAR(20) NOT NULL,
     horarioFunc VARCHAR(20) DEFAULT 'DAS 08:00 AS 18:00',
     endereco VARCHAR(20) NOT NULL,

     CONSTRAINT pkPV PRIMARY KEY (nome),
     CONSTRAINT checaHorarioF CHECK((horarioFunc) LIKE 'DAS __:__ AS __:__')
) ENGINE=InnoDB;

CREATE TABLE TelsPontoDeVenda(
     pontoDeVenda VARCHAR(20) NOT NULL,
     telefone INTEGER NOT NULL,

     CONSTRAINT pkTelsPV PRIMARY KEY (pontoDeVenda, telefone),
     CONSTRAINT fkTelsPV FOREIGN KEY (pontoDeVenda) REFERENCES PontoDeVenda(nome) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE VendePara(
     numEvento INTEGER NOT NULL,
     nomePonto VARCHAR(20) NOT NULL,
     ingRestantes INTEGER,
  
     CONSTRAINT pkVP PRIMARY KEY (numEvento, nomePonto),
     CONSTRAINT fkVendeEve FOREIGN KEY (numEvento) REFERENCES EventoDeLuta(nro) ON DELETE CASCADE ON UPDATE CASCADE,
     CONSTRAINT fkPon FOREIGN KEY (nomePonto) REFERENCES PontoDeVenda(nome) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
