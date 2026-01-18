DROP TABLE IF EXISTS itens_pedido;
DROP TABLE IF EXISTS pedidos_compra;

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