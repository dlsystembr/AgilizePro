<style>
    .consulta-cadastro-form .control-group { margin-bottom: 12px; }
    .consulta-cadastro-form label { font-weight: 600; }
    .result-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 16px; margin-top: 20px; }
    .result-box.success { border-color: #28a745; background: #d4edda; }
    .result-box.error { border-color: #dc3545; background: #f8d7da; }
    .contrib-table { width: 100%; margin-top: 12px; }
    .contrib-table th, .contrib-table td { padding: 8px; text-align: left; border: 1px solid #dee2e6; }
    .contrib-table th { background: #e9ecef; }
    .raw-xml { font-size: 0.75rem; white-space: pre-wrap; word-break: break-all; max-height: 200px; overflow-y: auto; background: #272822; color: #f8f8f2; padding: 10px; border-radius: 4px; }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="bx bx-search-alt"></i></span>
        <h5>Consulta Cadastro de Contribuinte (IE/CNPJ)</h5>
    </div>
    <p class="span12" style="margin: 8px 0 16px; color: #666;">
        Teste se a IE do destinatário está cadastrada na UF (evitar rejeição 428 na NFCom). Usa o Web Service CadConsultaCadastro2 da SEFAZ.
    </p>
    <p class="span12" style="margin: 0 0 16px; padding: 8px 12px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; font-size: 0.9em;">
        <strong>Goiás (GO):</strong> o webservice da SEFAZ-GO pode retornar erro 500 (código 0x454575). Se ocorrer, tente em outro horário ou consulte o cadastro pelo <a href="https://nfgoiana.sefaz.go.gov.br/nfg/busca/empresas" target="_blank" rel="noopener">portal da SEFAZ-GO</a>.
    </p>

    <div class="widget-box">
        <div class="widget-content nopadding consulta-cadastro-form">
            <form action="<?php echo site_url('nfecom/consultaCadastro'); ?>" method="post" class="form-horizontal" style="padding: 20px;">
                <div class="control-group">
                    <label class="control-label" for="uf">UF <span class="required">*</span></label>
                    <div class="controls">
                        <select name="uf" id="uf" required>
                            <option value="">Selecione a UF</option>
                            <?php foreach (isset($ufs) && is_array($ufs) ? $ufs : [] as $u): ?>
                                <option value="<?php echo htmlspecialchars($u); ?>" <?php echo ($this->input->post('uf') === $u) ? 'selected' : ''; ?>><?php echo htmlspecialchars($u); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="cnpj">CNPJ (14 dígitos)</label>
                    <div class="controls">
                        <input type="text" name="cnpj" id="cnpj" placeholder="00.000.000/0001-00 ou só números" value="<?php echo htmlspecialchars($this->input->post('cnpj') ?? ''); ?>" maxlength="18">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="ie">IE (opcional)</label>
                    <div class="controls">
                        <input type="text" name="ie" id="ie" placeholder="Apenas dígitos" value="<?php echo htmlspecialchars($this->input->post('ie') ?? ''); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" class="btn btn-success"><i class="bx bx-search-alt"></i> Consultar</button>
                        <a href="<?php echo site_url('nfecom'); ?>" class="btn">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($resultado)): ?>
        <div class="result-box <?php echo !empty($erro) ? 'error' : 'success'; ?>" style="margin-top: 20px;">
            <?php if (!empty($erro)): ?>
                <p><strong>Erro:</strong> <?php echo htmlspecialchars($erro); ?></p>
            <?php endif; ?>

            <?php if (!empty($resultado['data'])): ?>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($resultado['data']['cStat'] ?? ''); ?> — <?php echo htmlspecialchars($resultado['data']['xMotivo'] ?? ''); ?></p>
                <?php if (!empty($resultado['data']['dhCons'])): ?>
                    <p><strong>Data da consulta:</strong> <?php echo htmlspecialchars($resultado['data']['dhCons']); ?></p>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($resultado['contribuintes'])): ?>
                <table class="table table-bordered contrib-table">
                    <thead>
                        <tr>
                            <th>CNPJ</th>
                            <th>IE</th>
                            <th>Nome</th>
                            <th>UF</th>
                            <th>Situação (cSit)</th>
                            <th>Cred. NFe</th>
                            <th>Cred. NFCe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultado['contribuintes'] as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['CNPJ'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($c['IE'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($c['xNome'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($c['UF'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($c['cSit'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($c['indCredNFe'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($c['indCredNFCe'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if (!empty($resultado['raw'])): ?>
                <details style="margin-top: 12px;" <?php echo !empty($erro) ? 'open' : ''; ?>>
                    <summary><?php echo !empty($erro) ? 'Ver resposta da SEFAZ (diagnóstico)' : 'XML de retorno (resumo)'; ?></summary>
                    <pre class="raw-xml"><?php echo htmlspecialchars(substr($resultado['raw'], 0, 5000)); ?><?php echo strlen($resultado['raw']) > 5000 ? "\n..." : ''; ?></pre>
                </details>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
