<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon"><i class="bx bx-slider-alt"></i></span>
                <h5>Parâmetros do Sistema</h5>
            </div>

            <?php
            $agrupados = isset($parametros_agrupados) ? $parametros_agrupados : [];
            $vazio = empty($agrupados);
            ?>

            <?php if ($vazio): ?>
                <div class="widget-content">
                    <p class="alert alert-info">Nenhum parâmetro cadastrado para esta empresa.</p>
                    <p>Os parâmetros são criados por empresa. Execute a migração do banco e o seed de parâmetros iniciais, ou utilize a tela de <strong>Configurações do Sistema</strong> (menu Sistema) que ainda usa a tabela antiga.</p>
                    <a href="<?= site_url('mapos/configurar') ?>" class="btn btn-primary"><i class="bx bx-cog"></i> Ir para Configurações</a>
                </div>
            <?php else: ?>
                <form action="<?= site_url('parametros') ?>" method="post" id="formParametros" class="form-horizontal">
                    <ul class="nav nav-tabs">
                        <?php $primeiro = true; foreach (array_keys($agrupados) as $grupo): ?>
                            <li class="<?= $primeiro ? 'active' : '' ?>">
                                <a data-toggle="tab" href="#tab-<?= htmlspecialchars(preg_replace('/[^a-z0-9_]/', '_', strtolower($grupo))) ?>"><?= htmlspecialchars(ucfirst($grupo)) ?></a>
                            </li>
                            <?php $primeiro = false; endforeach; ?>
                    </ul>
                    <div class="widget-content nopadding tab-content">
                        <?php if (!empty($this->session->flashdata('success'))): ?>
                            <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
                        <?php endif; ?>
                        <?php if (!empty($this->session->flashdata('error'))): ?>
                            <div class="alert alert-error"><?= $this->session->flashdata('error') ?></div>
                        <?php endif; ?>

                        <?php $primeiro = true; foreach ($agrupados as $grupo => $params): ?>
                            <div id="tab-<?= htmlspecialchars(preg_replace('/[^a-z0-9_]/', '_', strtolower($grupo))) ?>" class="tab-pane fade <?= $primeiro ? 'in active' : '' ?>">
                                <?php foreach ($params as $p): ?>
                                    <div class="control-group">
                                        <label for="param_<?= htmlspecialchars($p->prm_nome) ?>" class="control-label"><?= htmlspecialchars($p->prm_caption ?: $p->prm_nome) ?></label>
                                        <div class="controls">
                                            <?php
                                            $nome_campo = 'param[' . htmlspecialchars($p->prm_nome) . ']';
                                            $valor = $p->prm_valor !== null ? $p->prm_valor : '';
                                            $tipo = $p->prm_tipo_dado ?: 'string';
                                            ?>
                                            <?php if ($tipo === 'boolean'): ?>
                                                <select name="<?= $nome_campo ?>" id="param_<?= htmlspecialchars($p->prm_nome) ?>">
                                                    <option value="0" <?= ($valor !== '1') ? 'selected' : '' ?>>Não</option>
                                                    <option value="1" <?= $valor === '1' ? 'selected' : '' ?>>Sim</option>
                                                </select>
                                            <?php elseif ($tipo === 'integer'): ?>
                                                <input type="number" name="<?= $nome_campo ?>" id="param_<?= htmlspecialchars($p->prm_nome) ?>" value="<?= htmlspecialchars($valor) ?>" step="1" class="span4" />
                                            <?php elseif ($tipo === 'float'): ?>
                                                <input type="number" name="<?= $nome_campo ?>" id="param_<?= htmlspecialchars($p->prm_nome) ?>" value="<?= htmlspecialchars($valor) ?>" step="0.01" class="span4" />
                                            <?php elseif ($tipo === 'datetime'): ?>
                                                <input type="datetime-local" name="<?= $nome_campo ?>" id="param_<?= htmlspecialchars($p->prm_nome) ?>" value="<?= htmlspecialchars($valor) ?>" class="span4" />
                                            <?php elseif ($tipo === 'text' || $tipo === 'json'): ?>
                                                <textarea name="<?= $nome_campo ?>" id="param_<?= htmlspecialchars($p->prm_nome) ?>" rows="3" class="span8"><?= htmlspecialchars($valor) ?></textarea>
                                            <?php else: ?>
                                                <input type="text" name="<?= $nome_campo ?>" id="param_<?= htmlspecialchars($p->prm_nome) ?>" value="<?= htmlspecialchars($valor) ?>" class="span6" />
                                            <?php endif; ?>
                                            <?php if (!empty($p->prm_descricao)): ?>
                                                <span class="help-inline"><?= htmlspecialchars($p->prm_descricao) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php $primeiro = false; endforeach; ?>

                        <div class="form-actions" style="margin-top: 20px;">
                            <button type="submit" class="button btn btn-primary">
                                <span class="button__icon"><i class="bx bx-save"></i></span>
                                <span class="button__text2">Salvar parâmetros</span>
                            </button>
                            <a href="<?= base_url() ?>" class="btn">Cancelar</a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
