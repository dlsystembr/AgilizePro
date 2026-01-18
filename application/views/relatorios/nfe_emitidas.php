<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />

<div class="row-fluid" style="margin-top: 0">
    <div class="span4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                <h5>Relatórios Rápidos</h5>
            </div>
            <div class="widget-content">
                <ul style="flex-direction: row;" class="site-stats">
                    <li><a target="_blank" href="<?php echo base_url() ?>index.php/relatorios/nfe_emitidas?dataInicial=<?php echo date('Y-m-01') ?>&dataFinal=<?php echo date('Y-m-d') ?>"><i class="fas fa-file-invoice"></i> <small>NFe Emitidas - mês atual</small></a></li>
                    <li><a target="_blank" href="<?php echo base_url() ?>index.php/relatorios/exportarNfeEmitidas?dataInicial=<?php echo date('Y-m-01') ?>&dataFinal=<?php echo date('Y-m-d') ?>"><i class="fas fa-file-excel"></i> <small>NFe Emitidas - mês atual - Excel</small></a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="span8">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                <h5>Relatórios Customizáveis</h5>
            </div>
            <div class="widget-content">
                <form method="get" action="<?php echo base_url() ?>index.php/relatorios/nfe_emitidas" class="form-inline">
                    <div class="span12 well">
                        <div class="span6">
                        <label for="dataInicial">Data Inicial</label>
                            <input type="date" class="span12" id="dataInicial" name="dataInicial" value="<?php echo $dataInicial; ?>">
                    </div>
                        <div class="span6">
                        <label for="dataFinal">Data Final</label>
                            <input type="date" class="span12" id="dataFinal" name="dataFinal" value="<?php echo $dataFinal; ?>">
                        </div>
                    </div>

                    <div class="span12 well" style="margin-left: 0">
                        <div class="span6">
                        <label for="status">Status</label>
                            <select class="span12" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="1" <?php echo $status == '1' ? 'selected' : ''; ?>>Autorizada</option>
                            <option value="2" <?php echo $status == '2' ? 'selected' : ''; ?>>Cancelada</option>
                            <option value="3" <?php echo $status == '3' ? 'selected' : ''; ?>>Rejeitada</option>
                        </select>
                    </div>
                        <div class="span6">
                        <label for="modelo">Modelo</label>
                            <select class="span12" id="modelo" name="modelo">
                            <option value="">Todos</option>
                            <option value="55" <?php echo $modelo == '55' ? 'selected' : ''; ?>>NFe</option>
                            <option value="65" <?php echo $modelo == '65' ? 'selected' : ''; ?>>NFCe</option>
                        </select>
                    </div>
                    </div>

                    <div class="span12" style="display:flex;justify-content: center">
                        <button type="reset" class="button btn btn-warning">
                            <span class="button__icon"><i class="bx bx-brush-alt"></i></span>
                            <span class="button__text">Limpar</span>
                        </button>
                        <button type="button" class="button btn btn-inverse" onclick="exportarPDF()">
                            <span class="button__icon"><i class="bx bx-printer"></i></span>
                            <span class="button__text">Imprimir</span>
                        </button>
                        <button type="button" class="button btn btn-success" onclick="exportarExcel()">
                            <span class="button__icon"><i class="bx bx-file"></i></span>
                            <span class="button__text">Excel</span>
                        </button>
                        <button type="button" class="button btn btn-info" onclick="exportarXML()">
                            <span class="button__icon"><i class="bx bx-file-blank"></i></span>
                            <span class="button__text">Exportar XML</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($nfe) { ?>
<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                <h5>Resumo</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span3">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon">
                                    <i class="fas fa-file-invoice"></i>
                                </span>
                                <h5>Total NFe</h5>
                            </div>
                            <div class="widget-content">
                                <h2>R$ <?php echo number_format($total, 2, ',', '.') ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="span3">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon">
                                    <i class="fas fa-undo"></i>
                                </span>
                                <h5>Total Devolução</h5>
                            </div>
                            <div class="widget-content">
                                <h2>R$ <?php echo number_format($total_devolucao, 2, ',', '.') ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="span3">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon">
                                    <i class="fas fa-ban"></i>
                                </span>
                                <h5>Total Cancelamento</h5>
                            </div>
                            <div class="widget-content">
                                <h2>R$ <?php echo number_format($total_cancelamento, 2, ',', '.') ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="span3">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <h5>Total Líquido</h5>
                            </div>
                            <div class="widget-content">
                                <h2>R$ <?php echo number_format($total_liquido, 2, ',', '.') ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                <h5>Detalhamento</h5>
            </div>
            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="font-size: 1.2em; padding: 5px;">#</th>
                                <th style="font-size: 1.2em; padding: 5px;">Data Emissão</th>
                                <th style="font-size: 1.2em; padding: 5px;">Número NFe</th>
                                <th style="font-size: 1.2em; padding: 5px;">Chave NFe</th>
                                <th style="font-size: 1.2em; padding: 5px;">Cliente</th>
                                <th style="font-size: 1.2em; padding: 5px;">Valor Total</th>
                                <th style="font-size: 1.2em; padding: 5px;">Modelo</th>
                                <th style="font-size: 1.2em; padding: 5px;">Status</th>
                                <th style="font-size: 1.2em; padding: 5px;">Protocolo</th>
                                <th style="font-size: 1.2em; padding: 5px;">Retorno</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($nfe as $n): ?>
                                    <tr>
                                <td style="font-size: 1.2em; padding: 5px;"><?php echo $n->id; ?></td>
                                <td style="font-size: 1.2em; padding: 5px;"><?php echo date('d/m/Y H:i', strtotime($n->created_at)); ?></td>
                                <td style="font-size: 1.2em; padding: 5px;"><?php echo $n->numero_nfe; ?></td>
                                <td style="font-size: 1.2em; padding: 5px;"><?php echo $n->chave_nfe; ?></td>
                                <td style="font-size: 1.2em; padding: 5px;"><?php echo $n->nomeCliente; ?></td>
                                <td style="font-size: 1.2em; padding: 5px;">R$ <?php echo number_format($n->valor_total, 2, ',', '.'); ?></td>
                                <td style="font-size: 1.2em; padding: 5px;"><?php echo $n->modelo == 65 ? 'NFC-e' : 'NFe'; ?></td>
                                <td style="font-size: 1.2em; padding: 5px;"><?php echo $n->status == 1 ? 'Autorizada' : ($n->status == 2 ? 'Cancelada' : 'Rejeitada'); ?></td>
                                <td style="font-size: 1.2em; padding: 5px;"><?php echo $n->protocolo; ?></td>
                                <td style="font-size: 1.2em; padding: 5px;"><?php echo $n->chave_retorno_evento; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination alternate">
                    <?php echo $pagination; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script type="text/javascript">
    function exportarExcel() {
        var dataInicial = document.getElementById('dataInicial').value;
        var dataFinal = document.getElementById('dataFinal').value;
        var status = document.getElementById('status').value;
        var modelo = document.getElementById('modelo').value;
        
        window.location.href = '<?php echo base_url() ?>index.php/relatorios/nfe_emitidas?dataInicial=' + dataInicial + '&dataFinal=' + dataFinal + '&status=' + status + '&modelo=' + modelo + '&format=xls';
    }

    function exportarPDF() {
        var dataInicial = document.getElementById('dataInicial').value;
        var dataFinal = document.getElementById('dataFinal').value;
        var status = document.getElementById('status').value;
        var modelo = document.getElementById('modelo').value;
        
        window.location.href = '<?php echo base_url() ?>index.php/relatorios/nfe_emitidas?dataInicial=' + dataInicial + '&dataFinal=' + dataFinal + '&status=' + status + '&modelo=' + modelo + '&format=pdf';
    }

    function exportarXML() {
        var dataInicial = document.getElementById('dataInicial').value;
        var dataFinal = document.getElementById('dataFinal').value;
        var status = document.getElementById('status').value;
        var modelo = document.getElementById('modelo').value;
        
        window.location.href = '<?php echo base_url() ?>index.php/relatorios/exportarNfeXml?dataInicial=' + dataInicial + '&dataFinal=' + dataFinal + '&status=' + status + '&modelo=' + modelo;
    }
</script> 