<div class="new122">
  <div class="widget-title" style="margin:-15px -10px 0">
    <h5>Permissões de Menu - Tenant: <?= $tenant->ten_nome ?></h5>
  </div>
  <a href="<?= base_url("index.php/super/tenants") ?>" class="button btn" style="max-width: 120px; margin-bottom: 15px;">
    <span class="button__icon"><i class='bx bx-arrow-back'></i></span>
    <span class="button__text2">Voltar</span>
  </a>

  <div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
      <span class="icon">
        <i class="icon-lock"></i>
      </span>
      <h5 style="padding: 3px 0"></h5>
    </div>
    <div class="widget-content nopadding tab-content">
      <form action="<?= base_url("index.php/super/permissoesMenu/{$tenant->ten_id}") ?>" method="post" id="formPermissoes">
        <div style="padding: 20px;">
          <div class="span12" style="margin-left: 0; margin-bottom: 20px;">
            <p><strong>Selecione quais módulos este tenant terá acesso:</strong></p>
            <p class="text-info">Ao habilitar um módulo, todas as permissões relacionadas (visualizar, adicionar, editar, excluir) serão habilitadas automaticamente.</p>
            
            <label style="margin-top: 15px;">
              <input name="marcarTodos" type="checkbox" value="1" id="marcarTodos" />
              <span class="lbl"> Marcar Todos</span>
            </label>
          </div>
          
          <div class="accordion" id="collapse-group">
            <?php 
            $accordion_index = 0;
            $icon_map = [
              'Cliente' => 'bx-group',
              'Produto' => 'bx-package',
              'Servico' => 'bx-wrench',
              'Os' => 'bx-file',
              'Venda' => 'bx-cart',
              'Financeiro' => 'bx-money',
              'Pessoa' => 'bx-user',
              'Nfecom' => 'bx-receipt',
              'Auditoria' => 'bx-shield',
              'OperacaoComercial' => 'bx-store',
              'Ncm' => 'bx-list-ul',
              'Usuario' => 'bx-user-circle',
              'Emitente' => 'bx-building',
              'Empresa' => 'bx-buildings',
              'Permissao' => 'bx-lock',
              'Backup' => 'bx-data',
              'Pagamento' => 'bx-credit-card',
              'Arquivo' => 'bx-folder',
              'Lancamento' => 'bx-money-withdraw',
              'Categoria' => 'bx-category',
              'Conta' => 'bx-wallet',
              'Garantia' => 'bx-shield-quarter',
              'Cobranca' => 'bx-credit-card-front',
              'PedidoCompra' => 'bx-shopping-bag',
              'TipoPessoa' => 'bx-user-pin',
              'Configuracao' => 'bx-cog',
              'Nfe' => 'bx-receipt',
              'FaturamentoEntrada' => 'bx-file-blank',
              'Veiculo' => 'bx-car',
              'TipoCliente' => 'bx-user-check',
              'Contrato' => 'bx-file-blank',
              'ClassificacaoFiscal' => 'bx-receipt',
              'ConfigFiscal' => 'bx-cog',
              'Tributacao' => 'bx-calculator',
              'TributacaoProduto' => 'bx-calculator',
              'Aliquota' => 'bx-percent',
            ];
            
            // Ordenar módulos alfabeticamente
            ksort($menus_agrupados);
            
            foreach ($menus_agrupados as $modulo => $permissoes): 
              $accordion_index++;
              $modulo_nome = ucfirst($modulo);
              $icon = isset($icon_map[$modulo_nome]) ? $icon_map[$modulo_nome] : 'bx-category';
              $collapse_id = 'collapse' . $accordion_index;
              $modulo_habilitado = in_array($modulo, $modulos_habilitados);
              $total_permissoes = count($permissoes);
            ?>
              <div class="accordion-group widget-box">
                <div class="accordion-heading">
                  <div class="widget-title">
                    <a data-parent="#collapse-group" href="#<?= $collapse_id ?>" data-toggle="collapse">
                      <span><i class='bx <?= $icon ?> icon-cli'></i></span>
                      <h5 style="padding-left: 28px; display: inline-block; margin: 0;">
                        <?= $modulo_nome ?>
                        <span class="label label-info" style="margin-left: 10px; font-size: 11px;">
                          <?= $total_permissoes ?> permissão(ões)
                        </span>
                      </h5>
                    </a>
                  </div>
                </div>
                <div class="collapse <?= $accordion_index == 1 ? 'in' : '' ?> accordion-body" id="<?= $collapse_id ?>">
                  <div class="widget-content">
                    <div style="padding: 15px; background: #f9f9f9; border-radius: 5px; margin-bottom: 15px;">
                      <label style="font-size: 16px; font-weight: bold; cursor: pointer;">
                        <input name="modulos[<?= $modulo ?>]" 
                               class="modulo-checkbox" 
                               type="checkbox" 
                               value="1" 
                               <?= $modulo_habilitado ? 'checked="checked"' : '' ?> 
                               id="modulo_<?= $modulo ?>" />
                        <span class="lbl" style="margin-left: 10px;"> 
                          <strong>Habilitar Módulo <?= $modulo_nome ?></strong>
                          <?php if ($modulo_habilitado): ?>
                            <span class="label label-success" style="margin-left: 10px;">Ativo</span>
                          <?php else: ?>
                            <span class="label label-default" style="margin-left: 10px;">Inativo</span>
                          <?php endif; ?>
                        </span>
                      </label>
                      <p style="margin-top: 10px; margin-left: 30px; color: #666; font-size: 12px;">
                        Ao habilitar, todas as <?= $total_permissoes ?> permissões deste módulo serão ativadas automaticamente.
                      </p>
                    </div>
                    
                    <div style="margin-top: 15px;">
                      <strong>Permissões incluídas neste módulo:</strong>
                      <ul style="margin-top: 10px; list-style: none; padding: 0;">
                        <?php foreach ($permissoes as $codigo => $nome): ?>
                          <li style="padding: 5px 0; border-bottom: 1px solid #eee;">
                            <i class="bx bx-check" style="color: #5cb85c; margin-right: 5px;"></i>
                            <?= $nome ?>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="form-actions" style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px;">
            <button type="submit" class="btn btn-success btn-large">
              <i class="icon-save"></i> Salvar Permissões
            </button>
            <a href="<?= base_url("index.php/super/tenants") ?>" class="btn btn-large">
              <i class="icon-arrow-left"></i> Voltar
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?= base_url() ?>assets/js/validate.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    // Marcar todos os módulos
    $('#marcarTodos').change(function() {
      var isChecked = $(this).is(':checked');
      $('.modulo-checkbox').prop('checked', isChecked);
    });
    
    // Validação do formulário
    $("#formPermissoes").validate({
      rules: {},
      messages: {}
    });
  });
</script>
