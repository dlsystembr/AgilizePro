<div class="row-fluid" style="margin-top: 0">
    <div class="span4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-file-contract"></i>
                </span>
                <h5>Relatórios Rápidos</h5>
            </div>
            <div class="widget-content">
                <ul style="flex-direction: row;" class="site-stats">
                    <li><a target="_blank" href="<?php echo base_url() ?>index.php/relatorios/contratosRapid"><i class="fas fa-file-contract"></i> <small>Todos os Contratos - pdf</small></a></li>
                    <li><a target="_blank" href="<?php echo base_url() ?>index.php/relatorios/contratosRapid?format=xls"><i class="fas fa-file-contract"></i> <small>Todos os Contratos - xls</small></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="span8">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-file-contract"></i>
                </span>
                <h5>Relatórios Customizáveis</h5>
            </div>
            <div class="widget-content">
                <div class="span12 well">
                    <form target="_blank" action="<?php echo base_url() ?>index.php/relatorios/contratosCustom" method="get">
                        <div class="span12 well">
                            <div class="span6">
                                <label for="">Data de Início de:</label>
                                <input type="date" name="dataInicial" class="span12" />
                            </div>
                            <div class="span6">
                                <label for="">até:</label>
                                <input type="date" name="dataFinal" class="span12" />
                            </div>
                        </div>
                        <div class="span12 well" style="margin-left: 0">
                            <div class="span6">
                                <label for="">Cliente:</label>
                                <input type="text" id="cliente" class="span12" placeholder="Digite o nome do cliente..." />
                                <input type="hidden" name="cliente" id="clienteHide" />
                            </div>
                            <div class="span6">
                                <label for="">Tipo de Assinante:</label>
                                <select name="tipoAssinante" class="span12">
                                    <option value="">Todos</option>
                                    <option value="1">Comercial</option>
                                    <option value="2">Industrial</option>
                                    <option value="3">Residencial/PF</option>
                                    <option value="4">Produtor Rural</option>
                                    <option value="5">Orgão Público Estadual</option>
                                    <option value="6">Prestador de Telecom</option>
                                    <option value="7">Missões Diplomáticas</option>
                                    <option value="8">Igrejas e Templos</option>
                                    <option value="99">Outros</option>
                                </select>
                            </div>
                        </div>
                        <div class="span12 well" style="margin-left: 0">
                            <div class="span6">
                                <label for="">Situação:</label>
                                <select name="situacao" class="span12">
                                    <option value="">Todas</option>
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                            <div class="span6">
                                <label for="">Tipo de impressão:</label>
                                <select name="format" class="span12">
                                    <option value="">PDF</option>
                                    <option value="xls">XLS</option>
                                </select>
                            </div>
                        </div>
                        <div class="span12 well" style="margin-left: 0">
                            <div class="span12">
                                <label>
                                    <input type="checkbox" name="incluirItens" value="1" />
                                    Incluir itens (serviços) dos contratos
                                </label>
                            </div>
                        </div>
                        <div class="span12" style="display:flex;justify-content: center">
                            <button type="reset" class="button btn btn-warning">
                                <span class="button__icon"><i class="bx bx-brush-alt"></i></span>
                                <span class="button__text">Limpar</span>
                            </button>
                            <button class="button btn btn-inverse">
                                <span class="button__icon"><i class="bx bx-printer"></i></span>
                                <span class="button__text">Imprimir</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/contratos/buscarCliente",
            minLength: 2,
            select: function(event, ui) {
                $("#clienteHide").val(ui.item.id);
            }
        });
    });
</script>
