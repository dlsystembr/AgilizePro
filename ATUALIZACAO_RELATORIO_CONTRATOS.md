# Guia de Atualização - Relatório de Contratos

## Arquivos Modificados

Esta atualização adiciona o **Relatório de Contratos** ao sistema. Segue a lista de arquivos que precisam ser atualizados no servidor do cliente:

### Controllers
- `application/controllers/Relatorios.php`
- `application/controllers/Permissoes.php`

### Models
- `application/models/Relatorios_model.php`

### Views
- `application/views/relatorios/rel_contratos.php` (NOVO)
- `application/views/relatorios/imprimir/imprimirContratos.php` (NOVO)
- `application/views/relatorios/imprimir/imprimirTopo.php` (MODIFICADO)
- `application/views/permissoes/adicionarPermissao.php`
- `application/views/permissoes/editarPermissao.php`
- `application/views/permissoes/permissoes.php`
- `application/views/tema/topo.php`
- `application/views/menu.php`

### Config
- `application/config/permission.php`

### Helpers
- `application/helpers/mpdf_helper.php`

### Vendor (mPDF - Correções)
- `application/vendor/mpdf/mpdf/src/Language/ScriptToLanguage.php`
- `application/vendor/mpdf/mpdf/src/Mpdf.php`

---

## Instruções de Atualização via FTP

### 1. BACKUP (OBRIGATÓRIO)
Antes de qualquer atualização, faça backup completo:
- **Banco de dados**: Exporte via phpMyAdmin ou ferramenta de backup
- **Arquivos**: Faça download completo da pasta `application/` e `assets/`

### 2. Preparação
1. Conecte-se ao servidor via FTP (FileZilla, WinSCP, etc.)
2. Navegue até a raiz do projeto MapOS

### 3. Upload dos Arquivos

#### Opção A: Upload Individual (Recomendado para primeira vez)
Faça upload dos arquivos listados acima mantendo a estrutura de pastas:

```
application/
├── controllers/
│   ├── Relatorios.php
│   └── Permissoes.php
├── models/
│   └── Relatorios_model.php
├── views/
│   ├── relatorios/
│   │   ├── rel_contratos.php (NOVO)
│   │   └── imprimir/
│   │       ├── imprimirContratos.php (NOVO)
│   │       └── imprimirTopo.php
│   ├── permissoes/
│   │   ├── adicionarPermissao.php
│   │   ├── editarPermissao.php
│   │   └── permissoes.php
│   ├── tema/
│   │   └── topo.php
│   └── menu.php
├── config/
│   └── permission.php
└── helpers/
    └── mpdf_helper.php
```

#### Opção B: Upload em Lote (FTP com modo binário)
1. Selecione todos os arquivos modificados
2. Faça upload mantendo a estrutura de diretórios
3. Certifique-se de que os arquivos foram transferidos corretamente

### 4. Atualização do Banco de Dados

Após o upload dos arquivos, é necessário atualizar o banco de dados:

#### Via Interface Web (Recomendado)
1. Acesse o sistema como administrador
2. Vá em **Configurações > Sistema**
3. Clique no botão **"Atualizar Banco de Dados"**

#### Via Terminal/SSH (Alternativa)
```bash
php index.php tools migrate
```

### 5. Verificação

Após a atualização, verifique:

1. **Permissões**: Acesse **Permissões > Editar** e verifique se aparece a opção "Relatório Contrato" (rContrato)
2. **Menu**: Verifique se o link "Relatório de Contratos" aparece no menu de Relatórios
3. **Funcionalidade**: Acesse **Relatórios > Relatório de Contratos** e teste a geração de PDF

### 6. Permissões de Arquivo (Linux/Unix)

Se o servidor for Linux, ajuste as permissões:

```bash
chmod 644 application/controllers/Relatorios.php
chmod 644 application/controllers/Permissoes.php
chmod 644 application/models/Relatorios_model.php
chmod 644 application/views/relatorios/rel_contratos.php
chmod 644 application/views/relatorios/imprimir/imprimirContratos.php
chmod 644 application/views/relatorios/imprimir/imprimirTopo.php
chmod 755 application/views/relatorios/
chmod 755 application/views/relatorios/imprimir/
```

---

## Checklist de Atualização

- [ ] Backup do banco de dados realizado
- [ ] Backup dos arquivos realizado
- [ ] Upload dos arquivos via FTP concluído
- [ ] Estrutura de pastas verificada
- [ ] Banco de dados atualizado (migrate)
- [ ] Permissões de arquivo ajustadas (se Linux)
- [ ] Teste de acesso ao relatório realizado
- [ ] Teste de geração de PDF realizado
- [ ] Verificação de permissões no sistema realizada

---

## Rollback (Em caso de problemas)

Se houver problemas após a atualização:

1. **Restaure o backup dos arquivos** (substitua os arquivos modificados pelos do backup)
2. **Restaure o backup do banco de dados**
3. **Verifique os logs de erro** em `application/logs/`

---

## Suporte

Em caso de dúvidas ou problemas:
- Verifique os logs em `application/logs/`
- Verifique as permissões de arquivo
- Verifique se todas as dependências estão instaladas (composer)

---

## Notas Importantes

⚠️ **ATENÇÃO**: 
- Não faça upload da pasta `vendor/` inteira se o servidor já tiver dependências instaladas
- Os arquivos do mPDF (`vendor/mpdf/`) só devem ser atualizados se houver problemas com geração de PDF
- Mantenha sempre um backup antes de atualizar

---

**Data da Atualização**: 25/01/2026
**Versão**: Relatório de Contratos v1.0
