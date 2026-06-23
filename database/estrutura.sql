USE db1241381;

CREATE TABLE utilizadores (
    id_utilizador INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    palavra_passe VARCHAR(255) NOT NULL,
    perfil ENUM('administrador', 'tecnico') NOT NULL DEFAULT 'tecnico',
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    ultimo_login DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id_categoria INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE localizacoes (
    id_localizacao INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    edificio VARCHAR(100) NOT NULL,
    piso VARCHAR(30) NOT NULL,
    servico VARCHAR(100) NOT NULL,
    sala VARCHAR(50) NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE fornecedores (
    id_fornecedor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    nif CHAR(9) NOT NULL UNIQUE,
    telefone VARCHAR(20) NULL,
    email VARCHAR(150) NULL,
    morada VARCHAR(255) NULL,
    website VARCHAR(200) NULL,
    pessoa_contacto VARCHAR(100) NULL,
    telefone_contacto VARCHAR(20) NULL,
    tipo ENUM(
        'fabricante',
        'distribuidor',
        'assistencia_tecnica',
        'consumiveis'
    ) NOT NULL,
    observacoes TEXT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE equipamentos (
    id_equipamento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_interno VARCHAR(50) NOT NULL UNIQUE,
    designacao VARCHAR(150) NOT NULL,
    id_categoria INT UNSIGNED NOT NULL,
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    numero_serie VARCHAR(100) NOT NULL,
    fabricante VARCHAR(150) NOT NULL,
    data_aquisicao DATE NULL,
    ano_fabrico YEAR NULL,
    custo_aquisicao DECIMAL(12,2) NULL,
    tipo_entrada ENUM('compra', 'doacao', 'aluguer', 'emprestimo') NOT NULL,
    estado ENUM(
        'ativo',
        'em_manutencao',
        'inativo',
        'em_calibracao',
        'em_quarentena',
        'abatido'
    ) NOT NULL,
    criticidade ENUM('baixa', 'media', 'alta', 'suporte_de_vida') NOT NULL,
    observacoes TEXT NULL,
    id_localizacao INT UNSIGNED NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_equipamento_serie
        UNIQUE (fabricante, modelo, numero_serie),

    CONSTRAINT fk_equipamento_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria),

    CONSTRAINT fk_equipamento_localizacao
        FOREIGN KEY (id_localizacao)
        REFERENCES localizacoes(id_localizacao)
) ENGINE=InnoDB;

CREATE TABLE equipamentos_fornecedores (
    id_equipamento INT UNSIGNED NOT NULL,
    id_fornecedor INT UNSIGNED NOT NULL,
    funcao ENUM(
        'fabricante',
        'distribuidor',
        'assistencia_tecnica',
        'consumiveis'
    ) NOT NULL,
    data_inicio DATE NULL,
    data_fim DATE NULL,

    PRIMARY KEY (id_equipamento, id_fornecedor, funcao),

    CONSTRAINT fk_equipamento_fornecedor_equipamento
        FOREIGN KEY (id_equipamento)
        REFERENCES equipamentos(id_equipamento),

    CONSTRAINT fk_equipamento_fornecedor_fornecedor
        FOREIGN KEY (id_fornecedor)
        REFERENCES fornecedores(id_fornecedor)
) ENGINE=InnoDB;

CREATE TABLE documentos (
    id_documento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM(
        'manual_utilizador',
        'manual_servico',
        'certificado_calibracao',
        'contrato_manutencao',
        'fatura',
        'declaracao_conformidade',
        'relatorio_tecnico',
        'outro'
    ) NOT NULL,
    nome VARCHAR(150) NOT NULL,
    data_documento DATE NULL,
    data_validade DATE NULL,
    caminho_ficheiro VARCHAR(255) NOT NULL,
    id_equipamento INT UNSIGNED NOT NULL,
    id_fornecedor INT UNSIGNED NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_documento_equipamento
        FOREIGN KEY (id_equipamento)
        REFERENCES equipamentos(id_equipamento),

    CONSTRAINT fk_documento_fornecedor
        FOREIGN KEY (id_fornecedor)
        REFERENCES fornecedores(id_fornecedor)
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE garantias (
    id_garantia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_equipamento INT UNSIGNED NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    entidade_responsavel VARCHAR(150) NULL,
    observacoes TEXT NULL,

    CONSTRAINT fk_garantia_equipamento
        FOREIGN KEY (id_equipamento)
        REFERENCES equipamentos(id_equipamento)
) ENGINE=InnoDB;

CREATE TABLE contratos_manutencao (
    id_contrato INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_equipamento INT UNSIGNED NOT NULL,
    id_fornecedor INT UNSIGNED NOT NULL,
    tipo_contrato VARCHAR(100) NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    periodicidade VARCHAR(50) NULL,
    observacoes TEXT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT fk_contrato_equipamento
        FOREIGN KEY (id_equipamento)
        REFERENCES equipamentos(id_equipamento),

    CONSTRAINT fk_contrato_fornecedor
        FOREIGN KEY (id_fornecedor)
        REFERENCES fornecedores(id_fornecedor)
) ENGINE=InnoDB;

CREATE TABLE conteudos_publicos (
    id_conteudo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    titulo VARCHAR(200) NULL,
    conteudo TEXT NOT NULL,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    atualizado_por INT UNSIGNED NOT NULL,

    CONSTRAINT fk_conteudo_utilizador
        FOREIGN KEY (atualizado_por)
        REFERENCES utilizadores(id_utilizador)
) ENGINE=InnoDB;

CREATE TABLE logs (
    id_log INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_utilizador INT UNSIGNED NULL,
    evento VARCHAR(100) NOT NULL,
    entidade VARCHAR(60) NULL,
    id_registo INT UNSIGNED NULL,
    detalhes TEXT NULL,
    endereco_ip VARCHAR(45) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_log_utilizador
        FOREIGN KEY (id_utilizador)
        REFERENCES utilizadores(id_utilizador)
        ON DELETE SET NULL
) ENGINE=InnoDB;