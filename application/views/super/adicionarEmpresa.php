<style>
    .control-group.error input, .control-group.error select { border-color: #b94a48; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
    .control-group.error .help-inline { color: #b94a48; display: inline-block; margin-left: 10px; padding: 5px 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; font-size: 12px; }
    .control-group.success input, .control-group.success select { border-color: #468847; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
    .help-inline { display: none; color: #b94a48; font-size: 12px; }
    .control-group.error .help-inline { display: inline-block; }
    .form-section { border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 20px; background: #fff; }
    .form-section-header { background: #f8f9fa; border-bottom: 1px solid #e0e0e0; padding: 12px 15px; display: flex; align-items: center; gap: 8px; font-weight: 600; color: #333; }
    .form-section-content { padding: 15px; }
    .form-section .control-label { width: 120px; text-align: right; }
    .form-section .controls { margin-left: 140px; }
    .form-section input[type="text"], .form-section input[type="email"], .form-section input[type="file"], .form-section select { width: 100%; max-width: 100%; box-sizing: border-box; height: 30px; padding: 4px 8px; line-height: 20px; font-size: 14px; }
</style>

<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-building"></i></span>
    <h5>Adicionar Empresa ao Grupo: <?= htmlspecialchars($grupo->gre_nome) ?></h5>
  </div>
  <?php if (isset($custom_error) && $custom_error): ?>
    <div class="alert alert-danger"><?= $custom_error ?></div>
  <?php endif; ?>
  <form action="<?= base_url("index.php/super/adicionarEmpresa/{$grupo->gre_id}") ?>" id="formEmpresa" method="post" class="form-horizontal" enctype="multipart/form-data">
    <div class="widget-box">
      <h5 style="padding: 3px 0"></h5>
      <div class="widget-content nopadding tab-content">

        <!-- Seção Dados Gerais -->
        <div class="form-section" style="margin-top: 20px;">
          <div class="form-section-header">
            <i class="bx bx-edit"></i>
            <span>Dados Gerais</span>
          </div>
          <div class="form-section-content">
            <div class="row-fluid" style="margin-bottom: 15px;">
              <div class="span6">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_ativo" class="control-label">Situação</label>
                  <div class="controls">
                    <select id="emp_ativo" name="emp_ativo">
                      <option value="1" <?= set_value('emp_ativo', '1') == '1' ? 'selected' : '' ?>>Ativo</option>
                      <option value="0" <?= set_value('emp_ativo') == '0' ? 'selected' : '' ?>>Inativo</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row-fluid" style="margin-bottom: 15px;">
              <div class="span12">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_cnpj" class="control-label">CNPJ<span class="required">*</span></label>
                  <div class="controls" style="display: flex; align-items: center; gap: 8px;">
                    <input id="emp_cnpj" type="text" name="emp_cnpj" value="<?= set_value('emp_cnpj') ?>" inputmode="numeric" autocomplete="off" style="flex: 1; min-width: 0;" />
                    <button type="button" id="btnBuscarDadosCnpj" class="btn btn-info" style="flex-shrink: 0; white-space: nowrap;" title="Buscar dados do CNPJ">
                      <i class="bx bx-search"></i> Buscar dados
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="row-fluid" style="margin-bottom: 15px;">
              <div class="span12">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_razao_social" class="control-label">Razão Social<span class="required">*</span></label>
                  <div class="controls">
                    <input id="emp_razao_social" type="text" name="emp_razao_social" value="<?= set_value('emp_razao_social') ?>" />
                  </div>
                </div>
              </div>
            </div>
            <div class="row-fluid">
              <div class="span12">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_nome_fantasia" class="control-label">Nome Fantasia</label>
                  <div class="controls">
                    <input id="emp_nome_fantasia" type="text" name="emp_nome_fantasia" value="<?= set_value('emp_nome_fantasia') ?>" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Seção Endereço -->
        <div class="form-section">
          <div class="form-section-header">
            <i class="bx bx-map"></i>
            <span>Endereço</span>
          </div>
          <div class="form-section-content">
            <div class="row-fluid" style="margin-bottom: 15px;">
              <div class="span6">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_cep" class="control-label">CEP</label>
                  <div class="controls">
                    <input id="emp_cep" type="text" name="emp_cep" value="<?= set_value('emp_cep') ?>" />
                  </div>
                </div>
              </div>
            </div>
            <div class="row-fluid" style="margin-bottom: 15px;">
              <div class="span8">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_logradouro" class="control-label">Logradouro</label>
                  <div class="controls">
                    <input id="emp_logradouro" type="text" name="emp_logradouro" value="<?= set_value('emp_logradouro') ?>" />
                  </div>
                </div>
              </div>
              <div class="span4">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_numero" class="control-label">Número</label>
                  <div class="controls">
                    <input id="emp_numero" type="text" name="emp_numero" value="<?= set_value('emp_numero') ?>" />
                  </div>
                </div>
              </div>
            </div>
            <div class="row-fluid" style="margin-bottom: 15px;">
              <div class="span12">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_complemento" class="control-label">Complemento</label>
                  <div class="controls">
                    <input id="emp_complemento" type="text" name="emp_complemento" value="<?= set_value('emp_complemento') ?>" />
                  </div>
                </div>
              </div>
            </div>
            <div class="row-fluid" style="margin-bottom: 15px;">
              <div class="span12">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_bairro" class="control-label">Bairro</label>
                  <div class="controls">
                    <input id="emp_bairro" type="text" name="emp_bairro" value="<?= set_value('emp_bairro') ?>" />
                  </div>
                </div>
              </div>
            </div>
            <div class="row-fluid">
              <div class="span8">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_cidade" class="control-label">Cidade</label>
                  <div class="controls">
                    <input id="emp_cidade" type="text" name="emp_cidade" value="<?= set_value('emp_cidade') ?>" />
                  </div>
                </div>
              </div>
              <div class="span4">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_uf" class="control-label">UF</label>
                  <div class="controls">
                    <select id="emp_uf" name="emp_uf">
                      <option value="">Selecione</option>
                      <?php if (!empty($estados)): foreach ($estados as $estado): ?>
                        <option value="<?= $estado->est_uf ?>" <?= set_value('emp_uf') == $estado->est_uf ? 'selected' : '' ?>><?= $estado->est_uf ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Seção Contato e Fiscal -->
        <div class="form-section">
          <div class="form-section-header">
            <i class="bx bx-phone"></i>
            <span>Contato e Informações Fiscais</span>
          </div>
          <div class="form-section-content">
            <div class="row-fluid" style="margin-bottom: 15px;">
              <div class="span6">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_telefone" class="control-label">Telefone</label>
                  <div class="controls">
                    <input id="emp_telefone" type="text" name="emp_telefone" value="<?= set_value('emp_telefone') ?>" />
                  </div>
                </div>
              </div>
              <div class="span6">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_email" class="control-label">Email</label>
                  <div class="controls">
                    <input id="emp_email" type="email" name="emp_email" value="<?= set_value('emp_email') ?>" />
                  </div>
                </div>
              </div>
            </div>
            <div class="row-fluid" style="margin-bottom: 15px;">
              <div class="span6">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_ie" class="control-label">Inscrição Estadual</label>
                  <div class="controls">
                    <input id="emp_ie" type="text" name="emp_ie" value="<?= set_value('emp_ie') ?>" />
                  </div>
                </div>
              </div>
              <div class="span6">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="emp_regime_tributario" class="control-label">Regime Tributário</label>
                  <div class="controls">
                    <select id="emp_regime_tributario" name="emp_regime_tributario">
                      <option value="">Selecione</option>
                      <option value="Simples Nacional" <?= set_value('emp_regime_tributario') == 'Simples Nacional' ? 'selected' : '' ?>>Simples Nacional</option>
                      <option value="Lucro Presumido" <?= set_value('emp_regime_tributario') == 'Lucro Presumido' ? 'selected' : '' ?>>Lucro Presumido</option>
                      <option value="Lucro Real" <?= set_value('emp_regime_tributario') == 'Lucro Real' ? 'selected' : '' ?>>Lucro Real</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row-fluid">
              <div class="span12">
                <div class="control-group" style="margin-bottom: 0;">
                  <label for="userfile" class="control-label">Logo da Empresa</label>
                  <div class="controls">
                    <input id="userfile" type="file" name="userfile" accept="image/*" />
                    <span class="help-inline" style="display: inline-block; margin-left: 10px; color: #999;">Formatos aceitos: JPG, PNG, GIF (máx. 2MB)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Seção Menus permitidos -->
        <?php if (!empty($menus)): ?>
        <div class="form-section">
          <div class="form-section-header">
            <i class="bx bx-menu"></i>
            <span>Menus permitidos para esta empresa</span>
          </div>
          <div class="form-section-content">
            <p style="margin-bottom: 12px; color: #666;">Marque os menus que esta empresa terá acesso no sistema.</p>
            <div class="row-fluid" style="display: flex; flex-wrap: wrap; gap: 8px 20px;">
              <?php foreach ($menus as $m): ?>
              <label class="checkbox inline" style="margin: 0; min-width: 180px;">
                <input type="checkbox" name="men_id[]" value="<?= (int) $m->men_id ?>" />
                <i class="bx <?= htmlspecialchars($m->men_icone ?? 'bx-circle') ?>"></i>
                <?= htmlspecialchars($m->men_nome) ?>
              </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <div class="form-actions">
          <div class="span12">
            <div class="span6 offset3" style="display: flex; justify-content: center; gap: 10px;">
              <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px">
                <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                <span class="button__text2">Adicionar</span>
              </button>
              <a href="<?= base_url("index.php/super/empresas/{$grupo->gre_id}") ?>" class="button btn btn-mini btn-warning">
                <span class="button__icon"><i class="bx bx-undo"></i></span>
                <span class="button__text2">Voltar</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script type="text/javascript">
$(document).ready(function () {
    // CNPJ sem máscara: apenas numérico (salvo assim no banco)
    if (typeof $.fn.mask === 'function') {
        $('#emp_cep').mask('00000-000');
        $('#emp_telefone').mask('(00) 00000-0000');
    }

    $('#btnBuscarDadosCnpj').on('click', function () {
        var cnpj = $('#emp_cnpj').val().replace(/\D/g, '');
        var btn = $(this);
        if (cnpj.length !== 14) {
            alert('Informe um CNPJ válido com 14 dígitos.');
            return;
        }
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Buscando...');
        $.ajax({
            url: '<?= site_url("super/buscarCnpjApi/"); ?>' + cnpj,
            method: 'GET',
            dataType: 'json',
            timeout: 35000,
            success: function (data) {
                if (data && data.erro) {
                    alert(data.erro);
                    btn.prop('disabled', false).html('<i class="bx bx-search"></i> Buscar dados');
                    return;
                }
                if (data && data.razao_social) {
                    var estab = data.estabelecimento || {};
                    $('#emp_razao_social').val(data.razao_social || '');
                    $('#emp_nome_fantasia').val(estab.nome_fantasia || '');
                    if (estab.cep) $('#emp_cep').val(estab.cep);
                    if (estab.logradouro) $('#emp_logradouro').val(estab.logradouro);
                    if (estab.numero) $('#emp_numero').val(estab.numero);
                    if (estab.complemento) $('#emp_complemento').val(estab.complemento);
                    if (estab.bairro) $('#emp_bairro').val(estab.bairro);
                    if (estab.cidade && estab.cidade.nome) $('#emp_cidade').val(estab.cidade.nome);
                    if (estab.estado && estab.estado.sigla) $('#emp_uf').val(estab.estado.sigla);
                    if (estab.email) $('#emp_email').val(estab.email.toLowerCase());
                    if (estab.ddd1 && estab.telefone1) $('#emp_telefone').val('(' + estab.ddd1 + ') ' + estab.telefone1);
                    var sn = data.simples_nacional || data.simples || {};
                    var ehSim = function (v) { return v === 'S' || v === true || String(v || '').toLowerCase().trim() === 'sim'; };
                    if (ehSim(sn.mei)) $('#emp_regime_tributario').val('Simples Nacional');
                    else if (ehSim(sn.simples)) $('#emp_regime_tributario').val('Simples Nacional');
                    else $('#emp_regime_tributario').val('Lucro Presumido');
                    if (estab.inscricoes_estaduais && estab.inscricoes_estaduais.length > 0 && estab.inscricoes_estaduais[0].inscricao_estadual) {
                        $('#emp_ie').val(estab.inscricoes_estaduais[0].inscricao_estadual);
                    }
                } else {
                    alert('CNPJ não encontrado na base de dados.');
                }
            },
            error: function (xhr, status, err) {
                if (xhr.status === 0) alert('Erro de conexão. Verifique sua internet.');
                else if (xhr.status === 404) alert('CNPJ não encontrado.');
                else if (status === 'timeout') alert('Tempo de espera esgotado. Tente novamente.');
                else alert('Erro ao buscar dados do CNPJ. Tente novamente.');
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="bx bx-search"></i> Buscar dados');
            }
        });
    });
});
</script>
