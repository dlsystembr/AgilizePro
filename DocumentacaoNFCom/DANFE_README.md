# DANFE NFCom - Documentação

## 📋 Visão Geral

A classe `NFComPreview` foi integrada ao sistema para gerar automaticamente o PDF da DANFE (Documento Auxiliar da Nota Fiscal de Comunicação) quando você acessar a URL:

```
http://192.168.1.15/mapos/index.php/nfecom/danfe/1
```

Onde `1` é o ID da NFCom no banco de dados.

## ✅ Funcionalidades Implementadas

- ✅ Geração automática de PDF da DANFE
- ✅ Layout profissional seguindo padrão SEFAZ
- ✅ QR Code para consulta online
- ✅ Código de barras para pagamento
- ✅ Suporte a Pix (opcional)
- ✅ Tabela de itens com paginação automática
- ✅ Cálculo e exibição de tributos (PIS, COFINS, FUST, FUNTTEL)
- ✅ Informações do emitente e destinatário
- ✅ Dados de faturamento e vencimento

## 📁 Arquivos Criados

1. **`application/libraries/NFComPreview.php`** - Classe principal para geração do PDF
2. **`application/libraries/NFComPreview_exemplo.php`** - Exemplo de uso standalone
3. **`application/controllers/Nfecom.php`** - Método `danfe()` atualizado (linha 550-702)

## 🔧 Dependências Necessárias

A classe NFComPreview requer as seguintes bibliotecas (via Composer):

```bash
composer require nfephp-org/sped-nfcom
composer require tecnickcom/tc-lib-barcode
```

Se você ainda não tem essas dependências instaladas, execute os comandos acima na raiz do projeto.

## 🎨 Personalização

### Logo da Empresa

Por padrão, o sistema busca o logo em:
```
assets/uploads/logomarca.png
```

Para alterar o caminho, edite a linha 583 do arquivo `Nfecom.php`:

```php
'logo' => FCPATH . 'assets/uploads/logomarca.png',
```

### Pix (Opcional)

Para habilitar o QR Code do Pix na DANFE, adicione a chave Pix na configuração da empresa ou diretamente no código.

## 🐛 Solução de Problemas

### Erro: "Class 'NFePHP\NFCom\Common\Keys' not found"

**Solução:** Instale as dependências via Composer:
```bash
cd c:\xampp\htdocs\mapos
composer install
```

### Erro: "QR Code indisponível" ou "Código de barras indisponível"

**Solução:** Verifique se a extensão GD ou Imagick está habilitada no PHP:
```bash
php -m | grep -i gd
php -m | grep -i imagick
```

Se não estiver habilitada, edite o `php.ini` e descomente:
```ini
extension=gd
```

### PDF não é gerado

**Solução:** Verifique se o diretório `assets/temp` existe e tem permissões de escrita:
```bash
mkdir assets/temp
chmod 755 assets/temp
```

## 📝 Exemplo de Uso Direto

Se você quiser usar a classe diretamente em outro lugar do código:

```php
require_once APPPATH . 'libraries/NFComPreview.php';

$config = [
    'empresa' => [
        'razao_social' => 'EMPRESA LTDA',
        'cnpj' => '12.345.678/0001-90',
        'ie' => '123456789',
        // ... outros dados
    ],
    // ... outras configurações
];

$dados = [
    'numero' => 123,
    'destinatario' => [ /* ... */ ],
    'itens' => [ /* ... */ ],
    'totais' => [ /* ... */ ],
];

$nfcomPreview = new \App\NFComPreview($config);
$resultado = $nfcomPreview->gerarPdf($dados);

// Salvar em arquivo
file_put_contents('danfe.pdf', $resultado['pdf']);

// OU enviar para o navegador
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="danfe.pdf"');
echo $resultado['pdf'];
```

## 🎯 Próximos Passos

- [ ] Adicionar suporte a múltiplas páginas para muitos itens
- [ ] Implementar cache de PDFs gerados
- [ ] Adicionar opção de download vs visualização inline
- [ ] Personalizar cores e fontes via configuração

## 📞 Suporte

Em caso de dúvidas ou problemas, verifique:
1. Os logs do PHP em `xampp/php/logs/php_error_log`
2. Os logs do Apache em `xampp/apache/logs/error.log`
3. Se todas as dependências estão instaladas corretamente
