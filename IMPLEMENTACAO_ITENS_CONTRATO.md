# Implementação de Itens do Contrato

## Funcionalidade
Permitir anexar serviços/produtos aos contratos e preencher automaticamente na NFCom quando o contrato for selecionado.

## Estrutura Implementada

### 1. **Tabela `contratos_itens`** ✅
**Arquivo:** `create_contratos_itens_table.sql`

Campos:
- `CTI_ID`: ID do item
- `CTR_ID`: ID do contrato (FK)
- `PRO_ID`: ID do produto/serviço (FK)
- `CTI_PRECO`: Preço do serviço
- `CTI_QUANTIDADE`: Quantidade padrão
- `CTI_ATIVO`: Status (1-Ativo, 0-Inativo)
- `CTI_OBSERVACAO`: Observações
- `ten_id`: ID do tenant

### 2. **Métodos no Contratos_model** ✅
**Arquivo:** `application/models/Contratos_model.php`

Métodos adicionados:
- `getItensByContratoId($contratoId)`: Buscar itens de um contrato
- `addItem($data)`: Adicionar item ao contrato
- `updateItem($itemId, $data)`: Atualizar item
- `deleteItem($itemId)`: Remover item
- `deleteItensByContratoId($contratoId)`: Remover todos os itens de um contrato

### 3. **Controller Contratos** ✅
**Arquivo:** `application/controllers/Contratos.php`

Modificações:
- Método `adicionar()`: Agora salva os itens do contrato após criar o contrato
- Método `editar()`: Precisa ser atualizado para salvar itens também (pendente)

### 4. **API para buscar serviços do contrato** ✅
**Arquivo:** `application/controllers/Nfecom.php`

Método adicionado:
- `getServicosContrato()`: Retorna JSON com os serviços/itens de um contrato

**Endpoint:** `GET /nfecom/getServicosContrato/{contratoId}`

**Resposta:**
```json
[
  {
    "CTI_ID": 1,
    "PRO_ID": 123,
    "idServicos": 123,
    "nome": "Serviço de Internet",
    "PRO_DESCRICAO": "Serviço de Internet",
    "preco": "100,00",
    "CTI_PRECO": 100.00,
    "quantidade": 1,
    "CTI_QUANTIDADE": 1.0000,
    "observacao": "Observação do item"
  }
]
```

### 5. **Formulário NFCom** ✅
**Arquivo:** `application/views/nfecom/adicionarNfecom.php`

Funcionalidade adicionada:
- Quando um contrato é selecionado, busca automaticamente os serviços do contrato
- Preenche os campos de serviço automaticamente
- Adiciona todos os serviços do contrato à lista de serviços da NFCom

**Função JavaScript:**
- `buscarServicosContrato(contratoId)`: Busca e adiciona serviços do contrato

## Próximos Passos

### Pendente:
1. **Adicionar seção de itens no formulário de contrato**
   - Criar interface para adicionar/editar/remover serviços no formulário de adicionar/editar contrato
   - Permitir selecionar serviço, preço e quantidade
   - Salvar itens ao salvar o contrato

2. **Atualizar método editar() do Contratos controller**
   - Salvar itens ao editar contrato
   - Carregar itens existentes ao abrir formulário de edição

## Como Usar

1. **Criar tabela:**
   ```sql
   -- Executar o script create_contratos_itens_table.sql
   ```

2. **Adicionar serviços ao contrato:**
   - Ao criar/editar um contrato, adicionar serviços na seção "Itens do Contrato"
   - Informar preço e quantidade padrão

3. **Usar na NFCom:**
   - Selecionar um cliente
   - Selecionar um contrato do cliente
   - Os serviços do contrato serão preenchidos automaticamente

## Estrutura de Dados

### Tabela `contratos_itens`
```sql
CREATE TABLE `contratos_itens` (
    `CTI_ID` INT(11) NOT NULL AUTO_INCREMENT,
    `CTR_ID` INT(11) NOT NULL,
    `PRO_ID` INT(11) NOT NULL,
    `CTI_PRECO` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `CTI_QUANTIDADE` DECIMAL(15,4) NOT NULL DEFAULT 1.0000,
    `CTI_ATIVO` TINYINT(1) NOT NULL DEFAULT 1,
    `CTI_OBSERVACAO` TEXT NULL,
    `CTI_DATA_CADASTRO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `CTI_DATA_ATUALIZACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `ten_id` INT(11) NOT NULL,
    PRIMARY KEY (`CTI_ID`),
    FOREIGN KEY (`CTR_ID`) REFERENCES `contratos`(`CTR_ID`) ON DELETE CASCADE,
    FOREIGN KEY (`PRO_ID`) REFERENCES `produtos`(`idProdutos`) ON DELETE RESTRICT
);
```
