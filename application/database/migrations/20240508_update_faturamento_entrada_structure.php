'operacao_comercial_id' => [
    'type' => 'INT',
    'constraint' => 11,
    'null' => true,
    'after' => 'fornecedor_id'
],
$this->dbforge->add_field('CONSTRAINT fk_faturamento_entrada_operacao_comercial FOREIGN KEY (operacao_comercial_id) REFERENCES operacao_comercial(opc_id) ON DELETE NO ACTION ON UPDATE NO ACTION'); 