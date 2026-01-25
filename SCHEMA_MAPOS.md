# Esquema do Banco de Dados - MAPOS

## Tabela: `aliquotas`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI |  | auto_increment |
| uf_origem | char(2) | NO | MUL |  |  |
| uf_destino | char(2) | NO |  |  |  |
| aliquota_origem | decimal(10,2) | NO |  |  |  |
| aliquota_destino | decimal(10,2) | NO |  |  |  |
| created_at | datetime | YES |  |  |  |
| updated_at | datetime | YES |  |  |  |

---

## Tabela: `anexos`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idAnexos | int(11) | NO | PRI |  | auto_increment |
| anexo | varchar(45) | YES |  |  |  |
| thumb | varchar(45) | YES |  |  |  |
| url | varchar(300) | YES |  |  |  |
| path | varchar(300) | YES |  |  |  |
| os_id | int(11) | NO | MUL |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| os_id | fk_anexos_os1 | os | idOs |

---

## Tabela: `anotacoes_os`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idAnotacoes | int(11) | NO | PRI |  | auto_increment |
| anotacao | varchar(255) | NO |  |  |  |
| data_hora | datetime | NO |  |  |  |
| os_id | int(11) | NO |  |  |  |

---

## Tabela: `bairros`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| BAI_ID | int(11) | NO | PRI |  | auto_increment |
| BAI_NOME | varchar(100) | NO | MUL |  |  |
| MUN_ID | int(11) | NO | MUL |  |  |
| BAI_DATA_INCLUSAO | datetime | NO |  | current_timestamp() |  |
| BAI_DATA_ATUALIZACAO | datetime | YES |  |  | on update current_timestamp() |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| MUN_ID | fk_bairros_municipios | municipios | MUN_ID |

---

## Tabela: `categorias`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idCategorias | int(11) | NO | PRI |  | auto_increment |
| categoria | varchar(80) | YES |  |  |  |
| cadastro | date | YES |  |  |  |
| status | tinyint(1) | YES |  |  |  |
| tipo | varchar(15) | YES |  |  |  |

---

## Tabela: `ci_sessions`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | varchar(128) | NO |  |  |  |
| ip_address | varchar(45) | NO |  |  |  |
| timestamp | int(1) unsigned | NO |  | 0 |  |
| data | blob | NO |  |  |  |

---

## Tabela: `classificacao_fiscal`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| CLF_ID | int(11) | NO | PRI |  | auto_increment |
| OPC_ID | int(11) | NO | MUL |  |  |
| CLF_CST | varchar(2) | YES |  |  |  |
| CLF_CSOSN | varchar(3) | YES |  |  |  |
| CLF_NATUREZA_CONTRIBUINTE | enum('Contribuinte','Não Contribuinte') | NO |  | Não Contribuinte |  |
| CLF_CFOP | varchar(4) | NO |  |  |  |
| CLF_DESTINACAO | varchar(100) | NO |  |  |  |
| CLF_OBJETIVO_COMERCIAL | enum('Consumo','Revenda','Industrialização','Orgão Público') | NO |  | Consumo |  |
| CLF_FINALIDADE | varchar(30) | YES |  |  |  |
| CLF_TIPO_ICMS | enum('ICMS Normal','Substituição Tributaria','Serviço') | NO |  | ICMS Normal |  |
| CLF_CCLASSTRIB | varchar(6) | YES |  |  |  |
| CLF_CST_IBS | varchar(3) | YES |  |  |  |
| CLF_ALIQ_IBS | decimal(10,2) | YES |  |  |  |
| CLF_CST_CBS | varchar(3) | YES |  |  |  |
| CLF_ALIQ_CBS | decimal(10,2) | YES |  |  |  |
| CLF_DATA_INCLUSAO | datetime | YES |  |  |  |
| CLF_DATA_ALTERACAO | datetime | YES |  |  |  |
| CLF_SITUACAO | tinyint(1) | NO |  | 1 |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| OPC_ID | clf_opc_fk | operacao_comercial | OPC_ID |

---

## Tabela: `clientes`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| CLN_ID | int(11) | NO | PRI |  | auto_increment |
| PES_ID | int(11) | NO | UNI |  |  |
| CLN_LIMITE_CREDITO | decimal(15,2) | YES |  |  |  |
| CLN_SITUACAO | tinyint(1) | NO | MUL | 1 |  |
| CLN_DATA_CADASTRO | datetime | YES |  |  |  |
| CLN_LASTUPDATE | datetime | YES |  |  | on update current_timestamp() |
| CLN_COMPRAR_APRAZO | tinyint(1) | NO |  | 0 |  |
| CLN_BLOQUEIO_FINANCEIRO | tinyint(1) | NO |  | 0 |  |
| CLN_DIAS_CARENCIA | int(11) | YES |  |  |  |
| CLN_EMITIR_NFE | tinyint(1) | NO |  | 0 |  |
| CLN_DATA_INCLUSAO | datetime | NO |  | current_timestamp() |  |
| CLN_DATA_ALTERACAO | datetime | YES |  |  | on update current_timestamp() |
| CLN_OBJETIVO_COMERCIAL | varchar(255) | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| PES_ID | fk_clientes_pessoas | pessoas | PES_ID |

---

## Tabela: `clientes_`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idClientes | int(11) | NO | PRI |  | auto_increment |
| asaas_id | varchar(255) | YES |  |  |  |
| nomeCliente | varchar(255) | NO |  |  |  |
| sexo | varchar(20) | YES |  |  |  |
| pessoa_fisica | tinyint(1) | NO |  | 1 |  |
| documento | varchar(20) | NO |  |  |  |
| natureza_contribuinte | enum('inscrito','nao_inscrito','nao_informado') | NO |  | nao_informado |  |
| telefone | varchar(20) | NO |  |  |  |
| celular | varchar(20) | YES |  |  |  |
| email | varchar(100) | NO |  |  |  |
| senha | varchar(200) | NO |  |  |  |
| dataCadastro | date | YES |  |  |  |
| rua | varchar(70) | YES |  |  |  |
| numero | varchar(15) | YES |  |  |  |
| bairro | varchar(45) | YES |  |  |  |
| cidade | varchar(45) | YES |  |  |  |
| estado | varchar(20) | YES |  |  |  |
| cep | varchar(20) | YES |  |  |  |
| objetivo_comercial | enum('consumo','revenda') | YES |  |  |  |
| inscricao | varchar(50) | YES |  |  |  |
| ibge | varchar(30) | YES |  |  |  |
| contato | varchar(45) | YES |  |  |  |
| complemento | varchar(45) | YES |  |  |  |
| fornecedor | tinyint(1) | NO |  | 0 |  |

---

## Tabela: `clientes_vendedores`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| CLV_ID | int(11) | NO | PRI |  | auto_increment |
| CLN_ID | int(11) | NO | MUL |  |  |
| VEN_ID | int(11) | NO | MUL |  |  |
| CLV_PADRAO | tinyint(1) | YES |  | 0 |  |
| CLV_DATA_INCLUSAO | timestamp | NO |  | current_timestamp() |  |
| CLV_DATA_ATUALIZACAO | timestamp | NO |  | current_timestamp() | on update current_timestamp() |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| CLN_ID | FK_CLV_CLIENTE | clientes | CLN_ID |
| VEN_ID | FK_CLV_VENDEDOR | vendedores | VEN_ID |

---

## Tabela: `cobrancas`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idCobranca | int(11) | NO | PRI |  | auto_increment |
| charge_id | int(11) | NO |  |  |  |
| conditional_discount_date | date | NO |  |  |  |
| created_at | datetime | NO |  |  |  |
| custom_id | int(11) | YES |  |  |  |
| expire_at | date | NO |  |  |  |
| message | varchar(255) | YES |  |  |  |
| payment_method | varchar(36) | YES |  |  |  |
| payment_url | varchar(255) | YES |  |  |  |
| request_delivery_address | varchar(64) | YES |  |  |  |
| status | varchar(36) | YES |  |  |  |
| total | decimal(10,2) | YES |  | 0.00 |  |
| barcode | varchar(255) | YES |  |  |  |
| link | varchar(255) | YES |  |  |  |
| payment | varchar(64) | YES |  |  |  |
| pdf | varchar(255) | YES |  |  |  |
| vendas_id | int(11) | YES | MUL |  |  |
| os_id | int(11) | YES | MUL |  |  |
| clientes_id | int(11) | YES | MUL |  |  |
| payment_gateway | varchar(255) | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| clientes_id | fk_cobrancas_clientes1 | clientes_ | idClientes |
| os_id | fk_cobrancas_os1 | os | idOs |
| vendas_id | fk_cobrancas_vendas1 | vendas | idVendas |

---

## Tabela: `configuracoes`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idConfig | int(11) | NO | PRI |  | auto_increment |
| config | varchar(20) | NO | UNI |  |  |
| valor | text | YES |  |  |  |
| ambiente | tinyint(1) | YES |  | 2 |  |
| versao_nfe | varchar(10) | YES |  | 4.00 |  |
| tipo_impressao_danfe | tinyint(1) | YES |  | 1 |  |
| orientacao_danfe | char(1) | YES |  | P |  |
| csc | varchar(255) | YES |  |  |  |
| csc_id | varchar(255) | YES |  |  |  |

---

## Tabela: `configuracoes_nfce`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI |  | auto_increment |
| tipo_documento | varchar(10) | NO |  | NFCe |  |
| ambiente | tinyint(1) | NO |  | 2 |  |
| versao_nfce | varchar(10) | NO |  | 4.00 |  |
| tipo_impressao_danfe | tinyint(1) | NO |  | 4 |  |
| sequencia_nfce | int(11) | NO |  | 1 |  |
| csc | varchar(100) | YES |  |  |  |
| csc_id | varchar(100) | YES |  |  |  |
| preview_nfce | tinyint(1) | NO |  | 0 |  |
| created_at | datetime | YES |  |  |  |
| updated_at | datetime | YES |  |  |  |

---

## Tabela: `configuracoes_nfe`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idConfiguracao | int(11) | NO | PRI |  | auto_increment |
| tipo_documento | varchar(10) | NO |  | NFe |  |
| ambiente | tinyint(1) | NO |  | 2 |  |
| versao_nfe | varchar(10) | NO |  | 4.00 |  |
| tipo_impressao_danfe | tinyint(1) | NO |  | 1 |  |
| orientacao_danfe | char(1) | NO |  | P |  |
| sequencia_nota | int(11) | NO |  | 1 |  |
| sequencia_nfce | int(11) | NO |  | 1 |  |
| csc | varchar(100) | YES |  |  |  |
| csc_id | varchar(100) | YES |  |  |  |
| imprimir_logo_nfe | tinyint(1) | NO |  | 1 |  |
| preview_nfe | tinyint(1) | NO |  | 0 |  |
| created_at | datetime | YES |  |  |  |
| updated_at | datetime | YES |  |  |  |

---

## Tabela: `contas`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idContas | int(11) | NO | PRI |  | auto_increment |
| conta | varchar(45) | YES |  |  |  |
| banco | varchar(45) | YES |  |  |  |
| numero | varchar(45) | YES |  |  |  |
| saldo | decimal(10,2) | YES |  |  |  |
| cadastro | date | YES |  |  |  |
| status | tinyint(1) | YES |  |  |  |
| tipo | varchar(80) | YES |  |  |  |

---

## Tabela: `documentos`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| DOC_ID | int(11) | NO | PRI |  | auto_increment |
| PES_ID | int(11) | NO | MUL |  |  |
| DOC_TIPO_DOCUMENTO | varchar(60) | NO | MUL |  |  |
| END_ID | int(11) | YES | MUL |  |  |
| DOC_ORGAO_EXPEDIDOR | varchar(60) | YES |  |  |  |
| DOC_NUMERO | varchar(60) | NO | MUL |  |  |
| DOC_NATUREZA_CONTRIBUINTE | enum('Contribuinte','Não Contribuinte') | YES |  |  |  |
| DOC_DATA_INCLUSAO | datetime | NO |  | current_timestamp() |  |
| DOC_DATA_ATUALIZACAO | datetime | YES |  |  | on update current_timestamp() |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| END_ID | fk_documentos_enderecos | enderecos | END_ID |
| PES_ID | fk_documentos_pessoas | pessoas | PES_ID |

---

## Tabela: `documentos_faturados`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| DCF_ID | int(11) | NO | PRI |  | auto_increment |
| ORV_ID | int(11) | NO | MUL |  |  |
| PES_ID | int(11) | NO | MUL |  |  |
| DCF_NUMERO | varchar(20) | NO |  |  |  |
| DCF_SERIE | varchar(10) | YES |  |  |  |
| DCF_MODELO | varchar(5) | YES |  |  |  |
| DCF_TIPO | char(1) | NO |  |  |  |
| DCF_DATA_EMISSAO | date | NO |  |  |  |
| DCF_DATA_SAIDA | date | YES |  |  |  |
| DCF_VALOR_PRODUTOS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_DESCONTO | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_FRETE | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_SEGURO | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_OUTRAS_DESPESAS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_BASE_ICMS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_ICMS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_ICMS_DESONERADO | decimal(15,2) | NO |  | 0.00 |  |
| DCF_BASE_IPI | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_IPI | decimal(15,2) | NO |  | 0.00 |  |
| DCF_BASE_PIS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_PIS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_BASE_COFINS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_COFINS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_BASE_IBS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_IBS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_BASE_CBS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_CBS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_RETENCAO_IRRF | decimal(15,2) | NO |  | 0.00 |  |
| DCF_RETENCAO_PIS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_RETENCAO_COFINS | decimal(15,2) | NO |  | 0.00 |  |
| DCF_RETENCAO_CSLL | decimal(15,2) | NO |  | 0.00 |  |
| DCF_VALOR_TOTAL | decimal(15,2) | NO |  | 0.00 |  |
| DCF_STATUS | varchar(20) | NO |  | ABERTO |  |
| DCF_INFORMACOES_ADICIONAIS | text | YES |  |  |  |
| DCF_LASTUPDATE | timestamp | NO |  | current_timestamp() | on update current_timestamp() |
| DCF_DATA_FATURAMENTO | date | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| ORV_ID | fk_dcf_ordem_servico | ordem_servico | ORV_ID |
| PES_ID | fk_dcf_pessoas | pessoas | PES_ID |

---

## Tabela: `email_queue`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI |  | auto_increment |
| to | varchar(255) | NO |  |  |  |
| cc | varchar(255) | YES |  |  |  |
| bcc | varchar(255) | YES |  |  |  |
| message | text | NO |  |  |  |
| status | enum('pending','sending','sent','failed') | YES |  |  |  |
| date | datetime | YES |  |  |  |
| headers | text | YES |  |  |  |

---

## Tabela: `emails`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| EML_ID | int(11) | NO | PRI |  | auto_increment |
| PES_ID | int(11) | NO | MUL |  |  |
| EML_TIPO | enum('Geral','Comercial','Financeiro','Nota Fiscal') | NO | MUL |  |  |
| EML_EMAIL | varchar(150) | NO | MUL |  |  |
| EML_NOME | varchar(150) | YES |  |  |  |
| EML_DATA_INCLUSAO | datetime | NO |  | current_timestamp() |  |
| EML_DATA_ATUALIZACAO | datetime | YES |  |  | on update current_timestamp() |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| PES_ID | fk_emails_pessoas | pessoas | PES_ID |

---

## Tabela: `emitente`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI |  | auto_increment |
| nome | varchar(255) | YES |  |  |  |
| cnpj | varchar(45) | YES |  |  |  |
| ie | varchar(50) | YES |  |  |  |
| rua | varchar(70) | YES |  |  |  |
| numero | varchar(15) | YES |  |  |  |
| bairro | varchar(45) | YES |  |  |  |
| cidade | varchar(45) | YES |  |  |  |
| uf | varchar(20) | YES |  |  |  |
| telefone | varchar(20) | YES |  |  |  |
| email | varchar(255) | YES |  |  |  |
| url_logo | varchar(225) | YES |  |  |  |
| cep | varchar(20) | YES |  |  |  |

---

## Tabela: `empresas`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| EMP_ID | int(11) | NO | PRI |  | auto_increment |
| EMP_RAZAO_SOCIAL | varchar(255) | NO |  |  |  |
| EMP_NOME_FANTASIA | varchar(255) | YES |  |  |  |
| EMP_CNPJ | varchar(18) | NO | UNI |  |  |
| EMP_IE | varchar(20) | YES |  |  |  |
| EMP_IM | varchar(20) | YES |  |  |  |
| EMP_CNAE | varchar(10) | YES |  |  |  |
| EMP_CEP | varchar(10) | NO |  |  |  |
| EMP_LOGRADOURO | varchar(255) | NO |  |  |  |
| EMP_NUMERO | varchar(20) | NO |  |  |  |
| EMP_COMPLEMENTO | varchar(100) | YES |  |  |  |
| EMP_BAIRRO | varchar(100) | NO |  |  |  |
| EMP_CIDADE | varchar(100) | NO |  |  |  |
| EMP_UF | char(2) | NO |  |  |  |
| EMP_IBGE | varchar(10) | NO |  |  |  |
| EMP_TELEFONE | varchar(20) | NO |  |  |  |
| EMP_CELULAR | varchar(20) | YES |  |  |  |
| EMP_EMAIL | varchar(255) | NO |  |  |  |
| EMP_SITE | varchar(255) | YES |  |  |  |
| EMP_REGIME_TRIBUTARIO | enum('Simples Nacional','Lucro Presumido','Lucro Real') | NO |  | Simples Nacional |  |
| EMP_ALIQ_CRED_ICMS | decimal(5,2) | YES |  |  |  |
| EMP_MENSAGEM_SIMPLES | text | YES |  |  |  |
| EMP_LOGO_PATH | varchar(255) | YES |  |  |  |
| EMP_COR_PRIMARIA | varchar(7) | YES |  | #1a73e8 |  |
| EMP_COR_SECUNDARIA | varchar(7) | YES |  | #34a853 |  |
| EMP_ATIVO | tinyint(1) | NO |  | 1 |  |
| EMP_DATA_CADASTRO | datetime | NO |  | current_timestamp() |  |
| EMP_DATA_ATUALIZACAO | datetime | NO |  | current_timestamp() | on update current_timestamp() |

---

## Tabela: `enderecos`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| END_ID | int(11) | NO | PRI |  | auto_increment |
| PES_ID | int(11) | NO | MUL |  |  |
| EST_ID | int(11) | NO | MUL |  |  |
| MUN_ID | int(11) | NO | MUL |  |  |
| BAI_ID | int(11) | YES | MUL |  |  |
| END_TIPO_ENDENRECO | enum('Geral','Faturamento','Entrega','Cobranca') | NO |  | Geral |  |
| END_TIPO_LOGRADOURO | varchar(30) | YES |  |  |  |
| END_LOGRADOURO | varchar(150) | NO |  |  |  |
| END_NUMERO | varchar(15) | YES |  |  |  |
| END_COMPLEMENTO | varchar(60) | YES |  |  |  |
| END_CEP | varchar(10) | YES |  |  |  |
| END_ZONA | enum('Rural','Urbana') | YES |  |  |  |
| END_OBSERVACAO | varchar(255) | YES |  |  |  |
| END_PADRAO | tinyint(1) | NO |  | 0 |  |
| END_SITUACAO | tinyint(1) | NO |  | 1 |  |
| END_DATA_INCLUSAO | datetime | NO |  | current_timestamp() |  |
| END_DATA_ATUALIZACAO | datetime | YES |  |  | on update current_timestamp() |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| BAI_ID | fk_enderecos_bairros | bairros | BAI_ID |
| EST_ID | fk_enderecos_estados | estados | EST_ID |
| MUN_ID | fk_enderecos_municipios | municipios | MUN_ID |
| PES_ID | fk_enderecos_pessoas | pessoas | PES_ID |

---

## Tabela: `equipamentos`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idEquipamentos | int(11) | NO | PRI |  | auto_increment |
| equipamento | varchar(150) | NO |  |  |  |
| num_serie | varchar(80) | YES |  |  |  |
| modelo | varchar(80) | YES |  |  |  |
| cor | varchar(45) | YES |  |  |  |
| descricao | varchar(150) | YES |  |  |  |
| tensao | varchar(45) | YES |  |  |  |
| potencia | varchar(45) | YES |  |  |  |
| voltagem | varchar(45) | YES |  |  |  |
| data_fabricacao | date | YES |  |  |  |
| marcas_id | int(11) | YES | MUL |  |  |
| clientes_id | int(11) | YES | MUL |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| clientes_id | fk_equipanentos_clientes1 | clientes_ | idClientes |
| marcas_id | fk_equipanentos_marcas1 | marcas_equipamentos | idMarcas |

---

## Tabela: `equipamentos_os`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idEquipamentos_os | int(11) | NO | PRI |  | auto_increment |
| defeito_declarado | varchar(200) | YES |  |  |  |
| defeito_encontrado | varchar(200) | YES |  |  |  |
| solucao | varchar(45) | YES |  |  |  |
| equipamentos_id | int(11) | YES | MUL |  |  |
| os_id | int(11) | YES | MUL |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| equipamentos_id | fk_equipamentos_os_equipanentos1 | equipamentos | idEquipamentos |
| os_id | fk_equipamentos_os_os1 | os | idOs |

---

## Tabela: `estados`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| EST_ID | int(11) | NO | PRI |  | auto_increment |
| EST_NOME | varchar(100) | NO |  |  |  |
| EST_UF | varchar(2) | NO | UNI |  |  |
| EST_CODIGO_UF | int(2) | NO | UNI |  |  |
| EST_DATA_INCLUSAO | datetime | NO |  | current_timestamp() |  |
| EST_DATA_ALTERACAO | datetime | YES |  |  | on update current_timestamp() |

---

## Tabela: `faturamento_entrada`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI |  | auto_increment |
| fornecedor_id | int(11) | NO | MUL |  |  |
| transportador-id | int(11) | YES | MUL |  |  |
| modalidade_frete | varchar(2) | YES |  |  |  |
| peso_bruto | decimal(15,3) | YES |  |  |  |
| peso_liquido | decimal(15,3) | YES |  |  |  |
| volume | decimal(10,3) | YES |  |  |  |
| operacao_comercial_id | int(11) | YES | MUL |  |  |
| data_emissao | date | NO |  |  |  |
| data_entrada | date | NO |  |  |  |
| numero_nota | varchar(20) | YES |  |  |  |
| chave_acesso | varchar(44) | YES |  |  |  |
| valor_total | decimal(10,2) | NO |  | 0.00 |  |
| valor_produtos | decimal(10,2) | NO |  | 0.00 |  |
| valor_icms | decimal(10,2) | NO |  | 0.00 |  |
| total_base_icms_st | decimal(10,2) | NO |  | 0.00 |  |
| total_icms_st | decimal(10,2) | NO |  | 0.00 |  |
| valor_ipi | decimal(10,2) | NO |  | 0.00 |  |
| valor_frete | decimal(10,2) | NO |  | 0.00 |  |
| valor_outras_despesas | decimal(10,2) | NO |  | 0.00 |  |
| observacoes | text | YES |  |  |  |
| data_cadastro | datetime | NO |  |  |  |
| status | enum('pendente','aprovado','rejeitado') | NO |  | pendente |  |
| data_atualizacao | datetime | YES |  |  |  |
| usuario_id | int(11) | NO | MUL |  |  |
| xml_conteudo | text | YES |  |  |  |
| desconto | int(11) | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| fornecedor_id | fk_faturamento_entrada_fornecedor | clientes_ | idClientes |
| operacao_comercial_id | fk_faturamento_entrada_operacao | operacao_comercial_old | id |
| transportador-id | fk_faturamento_entrada_transportadora | clientes_ | idClientes |
| usuario_id | fk_faturamento_entrada_usuario | usuarios | idUsuarios |

---

## Tabela: `faturamento_entrad-itens`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| FEI_ID | int(11) | NO | PRI |  | auto_increment |
| faturamento_entrad-id | int(11) | NO | MUL |  |  |
| PRO_ID | int(11) | YES | MUL |  |  |
| FEI_QUANTIDADE | decimal(10,2) | YES |  |  |  |
| FEI_VALOR_TOTAL | decimal(10,2) | YES |  |  |  |
| aliquot-icms | decimal(5,2) | NO |  | 0.00 |  |
| valor_icms | decimal(10,2) | NO |  | 0.00 |  |
| base_icms_st | decimal(10,2) | NO |  | 0.00 |  |
| valor_icms_st | decimal(10,2) | NO |  | 0.00 |  |
| aliquot-ipi | decimal(5,2) | NO |  | 0.00 |  |
| valor_ipi | decimal(10,2) | NO |  | 0.00 |  |
| desconto | int(11) | YES |  |  |  |
| base_calculo_icms_st | int(11) | YES |  |  |  |
| aliquot-icms_st | int(11) | YES |  |  |  |
| total_item | int(11) | YES |  |  |  |
| cst | int(11) | YES |  |  |  |
| cfop | int(11) | YES |  |  |  |
| base_calculo_icms | decimal(15,4) | YES |  |  |  |
| valor_unitario | decimal(10,4) | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| faturamento_entrad-id | fk_faturamento_entrad-itens_faturamento | faturamento_entrada | id |
| PRO_ID | fk_faturamento_entrad-itens_produto | produtos | PRO_ID |

---

## Tabela: `fornecedores`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idFornecedores | int(11) | NO | PRI |  | auto_increment |
| nomeFornecedor | varchar(255) | NO |  |  |  |
| cnpj | varchar(20) | NO |  |  |  |
| telefone | varchar(20) | NO |  |  |  |
| celular | varchar(20) | YES |  |  |  |
| email | varchar(100) | NO |  |  |  |
| rua | varchar(70) | YES |  |  |  |
| numero | varchar(15) | YES |  |  |  |
| bairro | varchar(45) | YES |  |  |  |
| cidade | varchar(45) | YES |  |  |  |
| estado | varchar(20) | YES |  |  |  |
| cep | varchar(20) | YES |  |  |  |
| fornecedor | tinyint(1) | NO |  | 1 |  |

---

## Tabela: `garantias`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idGarantias | int(11) | NO | PRI |  | auto_increment |
| dataGarantia | date | YES |  |  |  |
| refGarantia | varchar(15) | YES |  |  |  |
| textoGarantia | text | YES |  |  |  |
| usuarios_id | int(11) | YES | MUL |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| usuarios_id | fk_garantias_usuarios1 | usuarios | idUsuarios |

---

## Tabela: `itens_de_vendas`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| ITV_ID | int(11) | NO | PRI |  | auto_increment |
| ITV_SUBTOTAL | decimal(10,2) | YES |  |  |  |
| ITV_QUANTIDADE | int(11) | YES |  |  |  |
| ITV_PRECO | decimal(10,2) | YES |  |  |  |
| vendas_id | int(11) | NO | MUL |  |  |
| PRO_ID | int(11) | YES | MUL |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| PRO_ID | fk_itens_de_vendas_produtos1 | produtos | PRO_ID |
| vendas_id | fk_itens_de_vendas_vendas1 | vendas | idVendas |

---

## Tabela: `itens_faturados`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| ITF_ID | int(11) | NO | PRI |  | auto_increment |
| ITF_QUANTIDADE | decimal(10,3) | NO |  |  |  |
| ITF_VALOR_UNITARIO | decimal(15,2) | NO |  |  |  |
| ITF_VALOR_TOTAL | decimal(15,2) | NO |  |  |  |
| ITF_DESCONTO | decimal(15,2) | NO |  | 0.00 |  |
| ITF_UNIDADE | varchar(6) | YES |  |  |  |
| DCF_ID | int(11) | NO | MUL |  |  |
| PRO_ID | int(11) | NO | MUL |  |  |
| NCM_ID | int(11) | YES | MUL |  |  |
| CLF_ID | int(11) | YES | MUL |  |  |
| ITF_PRO_DESCRICAO | varchar(255) | NO |  |  |  |
| ITF_PRO_NCM | varchar(8) | YES |  |  |  |
| ITF_NCM_CEST | varchar(7) | YES |  |  |  |
| ITF_CFOP | varchar(4) | YES |  |  |  |
| ITF_ICMS_CST | varchar(3) | YES |  |  |  |
| ITF_CSOSN | varchar(4) | YES |  |  |  |
| ITF_ICMS_ALIQUOTA | decimal(5,2) | YES |  |  |  |
| ITF_ICMS_VALOR_BASE | decimal(15,2) | YES |  |  |  |
| ITF_ICMS_VALOR | decimal(15,2) | YES |  |  |  |
| ITF_COD_BENEFICIO | varchar(10) | YES |  |  |  |
| ITF_MOT_DESONERADO | varchar(2) | YES |  |  |  |
| ITF_BASE_DESONERADO_ICMS | decimal(15,2) | YES |  |  |  |
| ITF_VALOR_DESONERADO_ICMS | decimal(15,2) | YES |  |  |  |
| ITF_PIS_CST | varchar(3) | YES |  |  |  |
| ITF_PIS_ALIQUOTA | decimal(5,2) | YES |  |  |  |
| ITF_PIS_VALOR_BASE | decimal(15,2) | YES |  |  |  |
| ITF_PIS_VALOR | decimal(15,2) | YES |  |  |  |
| ITF_COFINS_CST | varchar(3) | YES |  |  |  |
| ITF_COFINS_ALIQUOTA | decimal(5,2) | YES |  |  |  |
| ITF_COFINS_VALOR_BASE | decimal(15,2) | YES |  |  |  |
| ITF_COFINS_VALOR | decimal(15,2) | YES |  |  |  |
| ITF_IPI_CST | varchar(3) | YES |  |  |  |
| ITF_IPI_ALIQUOTA | decimal(5,2) | YES |  |  |  |
| ITF_IPI_VALOR_BASE | decimal(15,2) | YES |  |  |  |
| ITF_IPI_VALOR | decimal(15,2) | YES |  |  |  |
| ITF_CCLASS_TRIB | varchar(10) | YES |  |  |  |
| ITF_ALIQ_IBS | decimal(5,2) | YES |  |  |  |
| ITF_VALOR_IBS | decimal(15,2) | YES |  |  |  |
| ITF_ALIQ_CBS | decimal(5,2) | YES |  |  |  |
| ITF_VALOR_CBS | decimal(15,2) | YES |  |  |  |
| ITF_RETENCAO_IRRF | decimal(15,2) | NO |  | 0.00 |  |
| ITF_RETENCAO_PIS | decimal(15,2) | NO |  | 0.00 |  |
| ITF_RETENCAO_COFINS | decimal(15,2) | NO |  | 0.00 |  |
| ITF_RETENCAO_CSLL | decimal(15,2) | NO |  | 0.00 |  |
| ITF_IBT | char(1) | YES |  |  |  |
| ITF_IBT_CST | varchar(3) | YES |  |  |  |
| ITF_IBT_ALIQUOTA | decimal(5,2) | YES |  |  |  |
| ITF_IBT_VALOR_BASE | decimal(15,2) | YES |  |  |  |
| ITF_IBT_VALOR | decimal(15,2) | YES |  |  |  |
| ITF_LASTUPDATE | timestamp | NO |  | current_timestamp() | on update current_timestamp() |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| CLF_ID | FK_ITF_CLF | classificacao_fiscal | CLF_ID |
| DCF_ID | FK_ITF_DCF | documentos_faturados | DCF_ID |
| PRO_ID | FK_ITF_PRO | produtos | PRO_ID |

---

## Tabela: `itens_pedido`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| ITP_ID | int(11) | NO | PRI |  | auto_increment |
| pedido_id | int(11) | NO | MUL |  |  |
| PRO_ID | int(11) | YES | MUL |  |  |
| ITP_QUANTIDADE | int(11) | YES |  |  |  |
| ITP_PRECO_UNIT | decimal(10,2) | YES |  |  |  |
| ITP_SUBTOTAL | decimal(10,2) | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| pedido_id | itens_pedido_ibfk_1 | pedidos_compra | idPedido |
| PRO_ID | itens_pedido_ibfk_2 | produtos | PRO_ID |

---

## Tabela: `lancamentos`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idLancamentos | int(11) | NO | PRI |  | auto_increment |
| descricao | varchar(255) | YES |  |  |  |
| valor | decimal(10,2) | NO |  | 0.00 |  |
| desconto | decimal(10,2) | YES |  | 0.00 |  |
| valor_desconto | decimal(10,2) | YES |  | 0.00 |  |
| tipo_desconto | varchar(8) | YES |  |  |  |
| data_vencimento | date | NO |  |  |  |
| data_pagamento | date | YES |  |  |  |
| baixado | tinyint(1) | YES |  | 0 |  |
| cliente_fornecedor | varchar(255) | YES |  |  |  |
| forma_pgto | varchar(100) | YES |  |  |  |
| tipo | varchar(45) | YES |  |  |  |
| anexo | varchar(250) | YES |  |  |  |
| clientes_id | int(11) | YES | MUL |  |  |
| categorias_id | int(11) | YES | MUL |  |  |
| contas_id | int(11) | YES | MUL |  |  |
| vendas_id | int(11) | YES |  |  |  |
| usuarios_id | int(11) | NO | MUL |  |  |
| observacoes | text | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| categorias_id | fk_lancamentos_categorias1 | categorias | idCategorias |
| clientes_id | fk_lancamentos_clientes1 | clientes_ | idClientes |
| contas_id | fk_lancamentos_contas1 | contas | idContas |
| usuarios_id | fk_lancamentos_usuarios1 | usuarios | idUsuarios |

---

## Tabela: `logs`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idLogs | int(11) | NO | PRI |  | auto_increment |
| usuario | varchar(80) | YES |  |  |  |
| tarefa | varchar(100) | YES |  |  |  |
| data | date | YES |  |  |  |
| hora | time | YES |  |  |  |
| ip | varchar(45) | YES |  |  |  |

---

## Tabela: `marcas`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| mrc_id | int(11) unsigned | NO | PRI |  | auto_increment |
| mrc_nome | varchar(100) | NO |  |  |  |
| mrc_descricao | text | YES |  |  |  |
| mrc_status | tinyint(1) | NO |  | 1 |  |
| mrc_data_cadastro | datetime | NO |  |  |  |
| mrc_data_alteracao | datetime | YES |  |  |  |
| mrc_usuario_cadastro | int(11) | NO |  |  |  |
| mrc_usuario_alteracao | int(11) | YES |  |  |  |

---

## Tabela: `marcas_equipamentos`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idMarcas | int(11) | NO | PRI |  | auto_increment |
| marca | varchar(100) | YES |  |  |  |
| cadastro | date | YES |  |  |  |
| situacao | tinyint(1) | YES |  |  |  |

---

## Tabela: `migrations`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| version | bigint(20) | NO |  |  |  |

---

## Tabela: `municipios`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| MUN_ID | int(11) | NO | PRI |  | auto_increment |
| EST_ID | int(11) | NO | MUL |  |  |
| MUN_NOME | varchar(100) | NO | MUL |  |  |
| MUN_IBGE | int(7) | NO | UNI |  |  |
| MUN_DATA_INCLUSAO | datetime | NO |  | current_timestamp() |  |
| MUN_DATA_ATUALIZACAO | datetime | YES |  |  | on update current_timestamp() |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| EST_ID | fk_municipios_estados | estados | EST_ID |

---

## Tabela: `ncms`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| NCM_ID | int(11) unsigned | NO | PRI |  | auto_increment |
| NCM_CODIGO | varchar(8) | YES | MUL |  |  |
| NCM_DESCRICAO | varchar(255) | YES |  |  |  |
| dat-inicio | date | YES |  |  |  |
| data_fim | date | YES |  |  |  |
| tipo_ato | varchar(50) | YES |  |  |  |
| numero_ato | varchar(50) | YES |  |  |  |
| ano_ato | varchar(4) | YES |  |  |  |
| created_at | datetime | YES |  |  |  |
| updated_at | datetime | YES |  |  |  |

---

## Tabela: `nfe_certificates`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) unsigned | NO | PRI |  | auto_increment |
| certificado_digital | longblob | NO |  |  |  |
| senha_certificado | varchar(255) | NO |  |  |  |
| data_validade | date | NO |  |  |  |
| nome_certificado | varchar(255) | NO |  |  |  |
| created_at | timestamp | NO |  | current_timestamp() |  |
| updated_at | timestamp | NO |  | current_timestamp() | on update current_timestamp() |

---

## Tabela: `nfe_documentos`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI |  | auto_increment |
| nfe_id | int(11) | NO | MUL |  |  |
| tipo | varchar(50) | NO |  |  |  |
| justificativa | text | YES |  |  |  |
| protocolo | varchar(50) | YES |  |  |  |
| data_evento | datetime | YES |  |  |  |
| status | tinyint(1) | NO |  | 1 |  |
| xml | longtext | YES |  |  |  |
| created_at | datetime | YES |  |  |  |
| updated_at | datetime | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| nfe_id | fk_nfe_documentos_nfe | nfe_emitidas | id |

---

## Tabela: `nfe_emitidas`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI |  | auto_increment |
| vend-id | int(11) | YES | MUL |  |  |
| entrad-id | int(11) | YES | MUL |  |  |
| cliente_id | int(11) | YES | MUL |  |  |
| modelo | int(11) | YES |  |  |  |
| numero_nfe | varchar(50) | NO |  |  |  |
| chave_nfe | varchar(44) | NO |  |  |  |
| status | varchar(20) | NO |  |  |  |
| xml | longtext | NO |  |  |  |
| xml_protocolo | text | YES |  |  |  |
| protocolo | varchar(50) | YES |  |  |  |
| motivo | text | YES |  |  |  |
| chave_retorno_evento | text | YES |  |  |  |
| valor_total | decimal(10,2) | YES |  |  |  |
| created_at | datetime | YES |  |  |  |
| updated_at | datetime | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| cliente_id | fk_nfe_emitidas_cliente | clientes_ | idClientes |
| entrad-id | fk_nfe_emitidas_entrada | faturamento_entrada | id |
| vend-id | fk_nfe_emitidas_venda | vendas | idVendas |

---

## Tabela: `nfecom_capa`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| NFC_ID | int(11) | NO | PRI |  | auto_increment |
| NFC_CUF | varchar(2) | NO |  |  |  |
| NFC_TIPO_AMBIENTE | tinyint(1) | NO |  | 2 |  |
| NFC_MOD | varchar(2) | NO |  | 62 |  |
| NFC_SERIE | varchar(3) | NO |  | 1 |  |
| NFC_NNF | int(9) | NO |  |  |  |
| NFC_CNF | varchar(8) | NO |  |  |  |
| NFC_CDV | varchar(1) | NO |  |  |  |
| NFC_DHEMI | datetime | NO |  |  |  |
| NFC_TP_EMIS | tinyint(1) | NO |  | 1 |  |
| NFC_N_SITE_AUTORIZ | int(3) | NO |  | 0 |  |
| NFC_C_MUN_FG | varchar(7) | NO |  |  |  |
| NFC_FIN_NFCOM | tinyint(1) | NO |  | 0 |  |
| NFC_TP_FAT | tinyint(1) | NO |  | 0 |  |
| NFC_VER_PROC | varchar(20) | NO |  | 1.0.0 |  |
| NFC_CNPJ_EMIT | varchar(14) | NO |  |  |  |
| NFC_IE_EMIT | varchar(14) | NO |  |  |  |
| NFC_CRT_EMIT | tinyint(1) | NO |  |  |  |
| NFC_X_NOME_EMIT | varchar(60) | NO |  |  |  |
| NFC_X_FANT_EMIT | varchar(60) | YES |  |  |  |
| NFC_X_LGR_EMIT | varchar(60) | NO |  |  |  |
| NFC_NRO_EMIT | varchar(60) | YES |  |  |  |
| NFC_X_CPL_EMIT | varchar(60) | YES |  |  |  |
| NFC_X_BAIRRO_EMIT | varchar(60) | NO |  |  |  |
| NFC_C_MUN_EMIT | varchar(7) | NO |  |  |  |
| NFC_X_MUN_EMIT | varchar(60) | NO |  |  |  |
| NFC_CEP_EMIT | varchar(8) | NO |  |  |  |
| NFC_UF_EMIT | varchar(2) | NO |  |  |  |
| NFC_FONE_EMIT | varchar(14) | YES |  |  |  |
| NFC_X_NOME_DEST | varchar(60) | NO |  |  |  |
| NFC_CNPJ_DEST | varchar(14) | NO |  |  |  |
| NFC_IND_IE_DEST | tinyint(1) | NO |  | 9 |  |
| NFC_X_LGR_DEST | varchar(60) | NO |  |  |  |
| NFC_NRO_DEST | varchar(60) | YES |  |  |  |
| NFC_X_BAIRRO_DEST | varchar(60) | NO |  |  |  |
| NFC_C_MUN_DEST | varchar(7) | NO |  |  |  |
| NFC_X_MUN_DEST | varchar(60) | NO |  |  |  |
| NFC_CEP_DEST | varchar(8) | NO |  |  |  |
| NFC_UF_DEST | varchar(2) | NO |  |  |  |
| NFC_I_COD_ASSINANTE | varchar(14) | NO |  |  |  |
| NFC_TP_ASSINANTE | tinyint(1) | NO |  |  |  |
| NFC_TP_SERV_UTIL | tinyint(1) | NO |  |  |  |
| NFC_N_CONTRATO | varchar(20) | NO |  |  |  |
| NFC_D_CONTRATO_INI | date | NO |  |  |  |
| NFC_V_PROD | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_COFINS | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_PIS | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_FUST | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_FUNTEL | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_RET_PIS | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_RET_COFINS | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_RET_CSLL | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_IRRF | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_RET_TRIB_TOT | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_DESC | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_OUTRO | decimal(15,2) | NO |  | 0.00 |  |
| NFC_V_NF | decimal(15,2) | NO |  | 0.00 |  |
| NFC_COMPET_FAT | varchar(6) | NO |  |  |  |
| NFC_D_VENC_FAT | date | NO |  |  |  |
| NFC_D_PER_USO_INI | date | NO |  |  |  |
| NFC_D_PER_USO_FIM | date | NO |  |  |  |
| NFC_COD_BARRAS | varchar(50) | YES |  |  |  |
| NFC_INF_CPL | text | YES |  |  |  |
| NFC_STATUS | tinyint(1) | NO | MUL | 0 |  |
| NFC_CH_NFCOM | varchar(44) | YES | MUL |  |  |
| NFC_N_PROT | varchar(15) | YES |  |  |  |
| NFC_DH_RECBTO | datetime | YES |  |  |  |
| NFC_C_STAT | varchar(3) | YES |  |  |  |
| NFC_X_MOTIVO | varchar(255) | YES |  |  |  |
| NFC_DIG_VAL | varchar(28) | YES |  |  |  |
| NFC_XML | longtext | YES |  |  |  |
| NFC_DATA_CADASTRO | timestamp | NO |  | current_timestamp() |  |
| NFC_DATA_ATUALIZACAO | timestamp | NO |  | current_timestamp() | on update current_timestamp() |

---

## Tabela: `nfecom_itens`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| NFI_ID | int(11) | NO | PRI |  | auto_increment |
| NFC_ID | int(11) | NO | MUL |  |  |
| NFI_N_ITEM | int(3) | NO |  |  |  |
| NFI_C_PROD | varchar(60) | NO |  |  |  |
| NFI_X_PROD | varchar(120) | NO |  |  |  |
| NFI_C_CLASS | varchar(7) | NO |  |  |  |
| NFI_CFOP | varchar(4) | NO |  |  |  |
| NFI_U_MED | varchar(6) | NO |  |  |  |
| NFI_Q_FATURADA | decimal(15,4) | NO |  | 0.0000 |  |
| NFI_V_ITEM | decimal(15,2) | NO |  | 0.00 |  |
| NFI_V_DESC | decimal(15,2) | NO |  | 0.00 |  |
| NFI_V_OUTRO | decimal(15,2) | NO |  | 0.00 |  |
| NFI_V_PROD | decimal(15,2) | NO |  | 0.00 |  |
| NFI_CST_ICMS | varchar(3) | YES |  |  |  |
| NFI_CST_PIS | varchar(2) | NO |  | 01 |  |
| NFI_V_BC_PIS | decimal(15,2) | NO |  | 0.00 |  |
| NFI_P_PIS | decimal(5,2) | NO |  | 0.00 |  |
| NFI_V_PIS | decimal(15,2) | NO |  | 0.00 |  |
| NFI_CST_COFINS | varchar(2) | NO |  | 01 |  |
| NFI_V_BC_COFINS | decimal(15,2) | NO |  | 0.00 |  |
| NFI_P_COFINS | decimal(5,2) | NO |  | 0.00 |  |
| NFI_V_COFINS | decimal(15,2) | NO |  | 0.00 |  |
| NFI_V_BC_FUST | decimal(15,2) | NO |  | 0.00 |  |
| NFI_P_FUST | decimal(5,2) | NO |  | 0.00 |  |
| NFI_V_FUST | decimal(15,2) | NO |  | 0.00 |  |
| NFI_V_BC_FUNTEL | decimal(15,2) | NO |  | 0.00 |  |
| NFI_P_FUNTEL | decimal(5,2) | NO |  | 0.00 |  |
| NFI_V_FUNTEL | decimal(15,2) | NO |  | 0.00 |  |
| NFI_V_BC_IRRF | decimal(15,2) | NO |  | 0.00 |  |
| NFI_V_IRRF | decimal(15,2) | NO |  | 0.00 |  |
| NFI_DATA_CADASTRO | timestamp | NO |  | current_timestamp() |  |
| NFI_DATA_ATUALIZACAO | timestamp | NO |  | current_timestamp() | on update current_timestamp() |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| NFC_ID | nfecom_itens_ibfk_1 | nfecom_capa | NFC_ID |

---

## Tabela: `operacao_comercial`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| OPC_ID | int(11) | NO | PRI |  | auto_increment |
| OPC_SIGLA | varchar(10) | NO | UNI |  |  |
| OPC_NOME | varchar(100) | NO |  |  |  |
| OPC_NATUREZA_OPERACAO | enum('Compra','Venda','Transferencia','Outras') | NO |  |  |  |
| OPC_TIPO_MOVIMENTO | enum('Entrada','Saida') | NO |  |  |  |
| OPC_AFETA_CUSTO | tinyint(1) | NO |  | 0 |  |
| OPC_FATO_FISCAL | tinyint(1) | NO |  | 0 |  |
| OPC_GERA_FINANCEIRO | tinyint(1) | NO |  | 0 |  |
| OPC_MOVIMENTA_ESTOQUE | tinyint(1) | NO |  | 0 |  |
| OPC_SITUACAO | tinyint(1) | NO |  | 1 |  |
| OPC_FINALIDADE_NFE | tinyint(1) | NO |  | 1 |  |
| OPC_DATA_INCLUSAO | datetime | NO |  | current_timestamp() |  |
| OPC_DATA_ALTERACAO | datetime | YES |  |  | on update current_timestamp() |

---

## Tabela: `operacao_comercial_old`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI |  | auto_increment |
| created_at | datetime | YES |  |  |  |
| updated_at | datetime | YES |  |  |  |

---

## Tabela: `ordem_servico`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| ORV_ID | int(11) | NO | PRI |  | auto_increment |
| ORV_DATA_INICIAL | date | YES |  |  |  |
| ORV_DATA_FINAL | date | YES |  |  |  |
| ORV_GARANTIA | varchar(45) | YES |  |  |  |
| ORV_DESCRICAO_PRODUTO | text | YES |  |  |  |
| ORV_DEFEITO | text | YES |  |  |  |
| ORV_STATUS | varchar(45) | YES |  |  |  |
| ORV_OBSERVACOES | text | YES |  |  |  |
| ORV_LAUDO_TECNICO | text | YES |  |  |  |
| ORV_VALOR_TOTAL | decimal(10,2) | YES |  | 0.00 |  |
| ORV_DESCONTO | decimal(10,2) | YES |  | 0.00 |  |
| ORV_VALOR_DESCONTO | decimal(10,2) | YES |  | 0.00 |  |
| ORV_TIPO_DESCONTO | varchar(8) | YES |  |  |  |
| ORV_PESS_ID | int(11) unsigned | NO | MUL |  |  |
| ORV_USUARIOS_ID | int(11) | NO | MUL |  |  |
| ORV_LANCAMENTO | int(11) | YES | MUL |  |  |
| ORV_FATURADO | tinyint(1) | NO |  | 0 |  |
| ORV_GARANTIAS_ID | int(11) | YES | MUL |  |  |
| ORV_OPC_ID | int(11) | YES | MUL |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| ORV_OPC_ID | fk_ordem_servico_operacao_comercial | operacao_comercial | OPC_ID |

---

## Tabela: `os`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idOs | int(11) | NO | PRI |  | auto_increment |
| dataInicial | date | YES |  |  |  |
| dataFinal | date | YES |  |  |  |
| garantia | varchar(45) | YES |  |  |  |
| descricaoProduto | text | YES |  |  |  |
| defeito | text | YES |  |  |  |
| status | varchar(45) | YES |  |  |  |
| observacoes | text | YES |  |  |  |
| laudoTecnico | text | YES |  |  |  |
| valorTotal | decimal(10,2) | YES |  | 0.00 |  |
| clientes_id | int(11) | NO | MUL |  |  |
| usuarios_id | int(11) | NO | MUL |  |  |
| lancamento | int(11) | YES | MUL |  |  |
| faturado | tinyint(1) | NO |  |  |  |
| garantias_id | int(11) | YES | MUL |  |  |
| desconto | decimal(10,2) | YES |  | 0.00 |  |
| valor_desconto | decimal(10,2) | YES |  | 0.00 |  |
| tipo_desconto | varchar(8) | YES |  |  |  |

---

## Tabela: `pedidos_compra`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idPedido | int(11) | NO | PRI |  | auto_increment |
| data_pedido | date | NO |  |  |  |
| fornecedor_id | int(11) | NO | MUL |  |  |
| usuario_id | int(11) | NO | MUL |  |  |
| status | varchar(45) | NO |  | pendente |  |
| observacoes | text | YES |  |  |  |
| valor_total | decimal(10,2) | NO |  | 0.00 |  |
| data_aprovacao | date | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| fornecedor_id | pedidos_compr-ibfk_1 | fornecedores | idFornecedores |
| usuario_id | pedidos_compr-ibfk_2 | usuarios | idUsuarios |

---

## Tabela: `permissoes`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idPermissao | int(11) | NO | PRI |  | auto_increment |
| nome | varchar(80) | NO |  |  |  |
| permissoes | text | YES |  |  |  |
| situacao | tinyint(1) | YES |  |  |  |
| data | date | YES |  |  |  |
| vEmpresa | varchar(1) | YES |  |  |  |
| eEmpresa | varchar(1) | YES |  |  |  |
| cEmpresa | varchar(1) | YES |  |  |  |
| vClassificacaoFiscal | tinyint(1) | YES |  | 0 |  |

---

## Tabela: `pessoas`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| PES_ID | int(11) | NO | PRI |  | auto_increment |
| PES_CPFCNPJ | varchar(20) | NO | UNI |  |  |
| PES_NOME | varchar(150) | NO | MUL |  |  |
| PES_RAZAO_SOCIAL | varchar(150) | YES | MUL |  |  |
| PES_CODIGO | varchar(50) | NO | UNI |  |  |
| PES_FISICO_JURIDICO | char(1) | NO |  |  |  |
| PES_NASCIMENTO_ABERTURA | date | YES |  |  |  |
| PES_NACIONALIDADES | varchar(100) | YES |  |  |  |
| PES_RG | varchar(20) | YES |  |  |  |
| PES_ORGAO_EXPEDIDOR | varchar(20) | YES |  |  |  |
| PES_SEXO | char(1) | YES |  |  |  |
| PES_ESTADO_CIVIL | int(11) | YES |  |  |  |
| PES_ESCOLARIDADE | int(11) | YES |  |  |  |
| PES_PROFISSAO | varchar(100) | YES |  |  |  |
| PES_OBSERVACAO | text | YES |  |  |  |
| PES_SITUACAO | tinyint(1) | NO | MUL | 1 |  |
| PES_DATA_INCLUSAO | datetime | NO |  | current_timestamp() |  |
| PES_DATA_ATUALIZACAO | datetime | YES |  |  | on update current_timestamp() |

---

## Tabela: `produtos`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| PRO_ID | int(11) | NO | PRI |  | auto_increment |
| PRO_COD_BARRA | varchar(70) | YES |  |  |  |
| PRO_DESCRICAO | varchar(80) | YES |  |  |  |
| PRO_UNID_MEDIDA | varchar(20) | YES |  |  |  |
| PRO_NCM | varchar(8) | YES |  |  |  |
| NCM_ID | int(11) | YES |  |  |  |
| mrc_id | int(11) | YES |  |  |  |
| PRO_PESO_BRUTO | decimal(10,3) | YES |  |  |  |
| PRO_PESO_LIQUIDO | decimal(10,3) | YES |  |  |  |
| PRO_LARGURA | decimal(10,2) | YES |  |  |  |
| PRO_ALTURA | decimal(10,2) | YES |  |  |  |
| PRO_COMPRIMENTO | decimal(10,2) | YES |  |  |  |
| TBP_ID | int(11) | YES |  |  |  |
| PRO_PRECO_COMPRA | decimal(10,2) | YES |  |  |  |
| PRO_PRECO_VENDA | decimal(10,2) | YES |  |  |  |
| PRO_ESTOQUE | int(11) | YES |  |  |  |
| PRO_ORIGEM | tinyint(1) | YES |  |  |  |
| PRO_ESTOQUE_MINIMO | int(11) | YES |  |  |  |
| PRO_SAIDA | tinyint(1) | YES |  |  |  |
| PRO_ENTRADA | tinyint(1) | YES |  |  |  |
| PRO_TIPO | tinyint(1) | YES |  | 1 |  |
| PRO_FINALIDADE | varchar(30) | YES |  |  |  |
| PRO_CCLASS_SERV | varchar(7) | YES |  |  |  |

---

## Tabela: `produtos_os`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| PRO_OS_ID | int(11) | NO | PRI |  | auto_increment |
| PRO_OS_QUANTIDADE | int(11) | YES |  |  |  |
| PRO_OS_DESCRICAO | varchar(80) | YES |  |  |  |
| PRO_OS_PRECO | decimal(10,2) | YES |  |  |  |
| os_id | int(11) | NO | MUL |  |  |
| PRO_ID | int(11) | YES | MUL |  |  |
| PRO_OS_SUBTOTAL | decimal(10,2) | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| PRO_ID | fk_produtos_os_produtos1 | produtos | PRO_ID |

---

## Tabela: `resets_de_senha`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI |  | auto_increment |
| email | varchar(200) | NO |  |  |  |
| token | varchar(255) | NO |  |  |  |
| data_expiracao | datetime | NO |  |  |  |
| token_utilizado | tinyint(4) | NO |  |  |  |

---

## Tabela: `servicos`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| SRV_ID | int(11) | NO | PRI |  | auto_increment |
| SRV_NOME | varchar(45) | NO |  |  |  |
| SRV_DESCRICAO | varchar(45) | YES |  |  |  |
| SRV_CODIGO | varchar(45) | YES |  |  |  |
| SRV_CCLASS | varchar(7) | YES |  |  |  |
| SRV_UNID_MEDIDA | varchar(2) | YES |  |  |  |
| SRV_PRECO | decimal(10,2) | NO |  |  |  |

---

## Tabela: `servicos_os`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| SOS_ID | int(11) | NO | PRI |  | auto_increment |
| servico | varchar(80) | YES |  |  |  |
| SOS_QUANTIDADE | double | YES |  |  |  |
| SOS_PRECO | decimal(10,2) | YES |  |  |  |
| os_id | int(11) | NO | MUL |  |  |
| PRO_ID | int(11) | YES | MUL |  |  |
| SOS_SUBTOTAL | decimal(10,2) | YES |  |  |  |

---

## Tabela: `telefones`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| TEL_ID | int(11) | NO | PRI |  | auto_increment |
| PES_ID | int(11) | NO | MUL |  |  |
| TEL_TIPO | enum('Celular','Comercial','Residencial','Whatsapp','Outros') | NO | MUL |  |  |
| TEL_DDD | varchar(3) | NO |  |  |  |
| TEL_NUMERO | varchar(12) | NO |  |  |  |
| TEL_OBSERVACAO | varchar(255) | YES |  |  |  |
| TEL_DATA_INCLUSAO | datetime | NO |  | current_timestamp() |  |
| TEL_DATA_ATUALIZACAO | datetime | YES |  |  | on update current_timestamp() |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| PES_ID | fk_telefones_pessoas | pessoas | PES_ID |

---

## Tabela: `tipos_pessoa`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| id | int(11) unsigned | NO | PRI |  | auto_increment |
| nome | varchar(50) | NO |  |  |  |
| descricao | text | YES |  |  |  |
| ativo | tinyint(1) | NO |  | 1 |  |
| created_at | datetime | YES |  |  |  |
| updated_at | datetime | YES |  |  |  |

---

## Tabela: `tributacao_estadual`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| tbe_id | int(11) | NO | PRI |  | auto_increment |
| ncm_id | int(11) unsigned | NO | MUL |  |  |
| tbe_uf | char(2) | NO | MUL |  |  |
| tbe_tipo_tributacao | enum('ICMS Normal','ST','Serviço') | NO |  |  |  |
| tbe_aliquot-icms | decimal(5,2) | YES |  | 0.00 |  |
| tbe_mva | decimal(5,2) | YES |  | 0.00 |  |
| tbe_aliquot-icms_st | decimal(5,2) | YES |  | 0.00 |  |
| tbe_percentual_reducao_icms | decimal(5,2) | YES |  | 0.00 |  |
| tbe_percentual_reducao_st | decimal(5,2) | YES |  | 0.00 |  |
| tbe_aliquota_fcp | decimal(5,2) | YES |  | 0.00 |  |
| tbe_data_cadastro | datetime | YES |  |  |  |
| tbe_data_alteracao | datetime | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| ncm_id | fk_tributacao_estadual_ncm | ncms | ncm_id |

---

## Tabela: `tributacao_federal`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| tbf_id | int(11) | NO | PRI |  | auto_increment |
| ncm_id | int(11) unsigned | NO | MUL |  |  |
| tbf_cst_ipi_entrada | varchar(2) | YES |  |  |  |
| tbf_aliquot-ipi_entrada | decimal(10,2) | YES |  |  |  |
| tbf_cst_ipi_saida | varchar(2) | YES |  |  |  |
| tbf_aliquot-ipi_saida | decimal(10,2) | YES |  |  |  |
| tbf_cst_pis_cofins_entrada | varchar(2) | YES |  |  |  |
| tbf_aliquota_pis_entrada | decimal(10,2) | YES |  |  |  |
| tbf_aliquota_cofins_entrada | decimal(10,2) | YES |  |  |  |
| tbf_cst_pis_cofins_saida | varchar(2) | YES |  |  |  |
| tbf_aliquota_pis_saida | decimal(10,2) | YES |  |  |  |
| tbf_aliquota_cofins_saida | decimal(10,2) | YES |  |  |  |
| tbf_aliquot-ii | decimal(10,2) | YES |  |  |  |
| tbf_data_cadastro | datetime | NO |  |  |  |
| tbf_data_alteracao | datetime | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| ncm_id | fk_tributacao_federal_ncm | ncms | ncm_id |

---

## Tabela: `tributacao_produto`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| TBP_ID | int(11) | NO | PRI |  | auto_increment |
| TBP_DESCRICAO | varchar(100) | YES |  |  |  |
| TBP_CST_IPI_SAIDA | varchar(100) | YES |  |  |  |
| aliq_ipi_saida | decimal(5,2) | NO |  |  |  |
| cst_pis_saida | varchar(10) | NO |  |  |  |
| aliq_pis_saida | decimal(5,2) | NO |  |  |  |
| cst_cofins_saida | varchar(10) | NO |  |  |  |
| aliq_cofins_saida | decimal(5,2) | NO |  |  |  |
| regime_fiscal_tributario | enum('ICMS Normal (Tributado)','Substituição Tributária') | NO |  |  |  |
| aliq_red_icms | decimal(5,2) | YES |  | 0.00 |  |
| aliq_iva | decimal(5,2) | YES |  | 0.00 |  |
| aliq_rd_icms_st | decimal(5,2) | YES |  | 0.00 |  |
| created_at | datetime | NO |  | current_timestamp() |  |
| updated_at | datetime | YES |  |  |  |

---

## Tabela: `usuarios`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idUsuarios | int(11) | NO | PRI |  | auto_increment |
| nome | varchar(80) | NO |  |  |  |
| rg | varchar(20) | YES |  |  |  |
| cpf | varchar(20) | NO |  |  |  |
| rua | varchar(70) | YES |  |  |  |
| numero | varchar(15) | YES |  |  |  |
| bairro | varchar(45) | YES |  |  |  |
| cidade | varchar(45) | YES |  |  |  |
| estado | varchar(20) | YES |  |  |  |
| email | varchar(80) | NO |  |  |  |
| senha | varchar(200) | NO |  |  |  |
| telefone | varchar(20) | NO |  |  |  |
| celular | varchar(20) | YES |  |  |  |
| situacao | tinyint(1) | NO |  |  |  |
| dataCadastro | date | NO |  |  |  |
| permissoes_id | int(11) | NO | MUL |  |  |
| dataExpiracao | date | YES |  |  |  |
| url_image_user | varchar(255) | YES |  |  |  |
| cep | varchar(9) | NO |  | 70005-115 |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| permissoes_id | fk_usuarios_permissoes1 | permissoes | idPermissao |

---

## Tabela: `vendas`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| idVendas | int(11) | NO | PRI |  | auto_increment |
| dataVenda | date | YES |  |  |  |
| valorTotal | decimal(10,2) | YES |  | 0.00 |  |
| desconto | decimal(10,2) | YES |  | 0.00 |  |
| valor_desconto | decimal(10,2) | YES |  | 0.00 |  |
| tipo_desconto | varchar(8) | YES |  |  |  |
| faturado | tinyint(1) | YES |  |  |  |
| clientes_id | int(11) | NO | MUL |  |  |
| operacao_comercial_id | int(11) | YES | MUL |  |  |
| usuarios_id | int(11) | YES | MUL |  |  |
| lancamentos_id | int(11) | YES | MUL |  |  |
| status | varchar(45) | YES |  |  |  |
| emitida_nfe | tinyint(1) | NO |  | 0 |  |
| garantia | int(11) | YES |  |  |  |
| observacoes | text | YES |  |  |  |
| observacoes_cliente | text | YES |  |  |  |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| clientes_id | fk_vendas_clientes1 | clientes_ | idClientes |
| lancamentos_id | fk_vendas_lancamentos1 | lancamentos | idLancamentos |
| operacao_comercial_id | fk_vendas_operacao_comercial | operacao_comercial_old | id |
| usuarios_id | fk_vendas_usuarios1 | usuarios | idUsuarios |

---

## Tabela: `vendedores`

| Campo | Tipo | Nulo | Chave | Padrão | Extra |
|---|---|---|---|---|---|
| VEN_ID | int(11) | NO | PRI |  | auto_increment |
| PES_ID | int(11) | NO | UNI |  |  |
| VEN_PERCENTUAL_COMISSAO | decimal(5,2) | YES |  |  |  |
| VEN_TIPO_COMISSAO | varchar(20) | YES |  |  |  |
| VEN_META_MENSAL | decimal(10,2) | YES |  |  |  |
| VEN_SITUACAO | tinyint(1) | YES |  | 1 |  |
| VEN_DATA_CADASTRO | timestamp | NO |  | current_timestamp() |  |
| VEN_DATA_ATUALIZACAO | timestamp | NO |  | current_timestamp() | on update current_timestamp() |

### Relacionamentos (Chaves Estrangeiras)

| Coluna | Nome da Chave | Tabela Referenciada | Coluna Referenciada |
|---|---|---|---|
| PES_ID | FK_VEN_PES_ID | pessoas | PES_ID |

---

