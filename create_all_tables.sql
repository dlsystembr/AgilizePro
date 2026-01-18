SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS fornecedores;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS itens_pedido;
DROP TABLE IF EXISTS pedidos_compra;

CREATE TABLE fornecedores (
    idFornecedores INT NOT NULL AUTO_INCREMENT,
    nomeFornecedor VARCHAR(255) NOT NULL,
    cnpj VARCHAR(20) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    celular VARCHAR(20) NULL,
    email VARCHAR(100) NOT NULL,
    rua VARCHAR(70) NULL,
    numero VARCHAR(15) NULL,
    bairro VARCHAR(45) NULL,
    cidade VARCHAR(45) NULL,
    estado VARCHAR(20) NULL,
    cep VARCHAR(20) NULL,
    fornecedor TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (idFornecedores)
) ENGINE=InnoDB;

CREATE TABLE produtos (
    idProdutos INT NOT NULL AUTO_INCREMENT,
    codDeBarra VARCHAR(70) NULL DEFAULT NULL,
    descricao VARCHAR(80) NOT NULL,
    unidade VARCHAR(20) NULL,
    precoCompra DECIMAL(10,2) NULL,
    precoVenda DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL,
    estoqueMinimo INT NULL,
    saida TINYINT(1) NULL DEFAULT 0,
    entrada TINYINT(1) NULL DEFAULT 0,
    PRIMARY KEY (idProdutos)
) ENGINE=InnoDB;

CREATE TABLE pedidos_compra (
    id INT NOT NULL AUTO_INCREMENT,
    data_pedido DATE NOT NULL,
    data_aprovacao DATE NULL,
    fornecedor_id INT NOT NULL,
    usuario_id INT NOT NULL,
    observacoes TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Pendente',
    PRIMARY KEY (id),
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(idFornecedores),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(idUsuarios)
) ENGINE=InnoDB;

CREATE TABLE itens_pedido (
    id INT NOT NULL AUTO_INCREMENT,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (pedido_id) REFERENCES pedidos_compra(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(idProdutos)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS=1; 