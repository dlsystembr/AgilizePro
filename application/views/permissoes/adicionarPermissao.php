<style>
    .widget-title h5 {
        font-weight: 500;
        padding: 5px;
        padding-left: 36px !important;
        line-height: 12px;
        margin: 5px 0 !important;
        font-size: 1.3em;
        color: var(--violeta1);
    }

    .icon-cli {
        color: #239683;
        margin-top: 3px;
        margin-left: 8px;
        position: absolute;
        font-size: 18px;
    }

    .icon-clic {
        color: #9faab7;
        top: 4px;
        right: 10px;
        position: absolute;
        font-size: 1.9em;
    }

    .icon-clic:hover {
        color: #3fadf6;
    }

    .widget-content {
        padding: 8px 12px 0;
    }

    .table td {
        padding: 5px;
    }

    .table {
        margin-bottom: 0;
    }

    .accordion .widget-box {
        margin-top: 10px;
        margin-bottom: 0;
        border-radius: 6px;
    }

    .accordion {
        margin-top: -25px;
    }

    .collapse.in {
        top: -15px
    }

    .button {
        min-width: 130px;
    }

    .form-actions {
        padding: 0;
        margin-top: 20px;
        margin-bottom: 20px;
        background-color: transparent;
        border-top: 0px;
    }

    .widget-content table tbody tr:hover {
        background: transparent;
    }

    @media (max-width: 480px) {
        .widget-content {
            padding: 10px 7px !important;
            margin-bottom: -15px;
        }
    }
</style>

<?php 
// Verificar se há permissões habilitadas para o tenant
if (!isset($permissoes_habilitadas)) {
    $permissoes_habilitadas = [];
}
$ten_id = $this->session->userdata('ten_id');
// A função permissao_habilitada() está definida no helper permission_helper.php
?>
<div class="span12" style="margin-left: 0">
    <form action="<?php echo base_url(); ?>index.php/permissoes/adicionar" id="formPermissao" method="post">
        <div class="span12" style="margin-left: 0">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <h5 style="padding:12px;padding-left:18px!important;margin:-10px 0 0!important;font-size:1.7em;">
                        Cadastro de Permissão</h5>
                </div>
                <div class="widget-content">
                    <div class="span4">
                        <label>Nome da Permissão</label>
                        <input name="nome" type="text" id="nome" class="span12" />
                    </div>
                    <div class="span4">
                        <label>Pesquisar Permissão</label>
                        <input type="text" id="pesquisarPermissao" class="span12" placeholder="Digite para pesquisar..." />
                    </div>
                    <div class="span4">
                        <label>
                            <input name="" type="checkbox" value="1" id="marcarTodos" />
                            <span class="lbl"> Marcar Todos</span>
                        </label>
                    </div>

                    <div class="control-group">
                        <label for="documento" class="control-label"></label>
                        <div class="controls">

                            <div class="widget-content" style="padding: 5px 0 !important">
                                <div id="tab1" class="tab-pane active" style="min-height: 300px">
                                    <div class="accordion" id="collapse-group">
                    <div class="accordion" id="collapse-group">
                        <?php if (permissao_habilitada('vCliente', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('aCliente', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('eCliente', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('dCliente', $permissoes_habilitadas, $ten_id)): ?>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGOne" data-toggle="collapse">
                                      <span><i class='bx bx-group icon-cli'></i></span>
                                      <h5 style="padding-left: 28px">Clientes</h5>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse in accordion-body" id="collapseGOne">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <?php if (permissao_habilitada('vCliente', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="vCliente" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Cliente</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('aCliente', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="aCliente" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Cliente</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('eCliente', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="eCliente" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Cliente</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('dCliente', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="dCliente" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Cliente</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if (modulo_habilitado(['vProduto', 'aProduto', 'eProduto', 'dProduto'], $permissoes_habilitadas, $ten_id)): ?>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGTwo" data-toggle="collapse">
                                      <span><i class='bx bx-package icon-cli'></i></span>
                                      <h5 style="padding-left: 28px">Produtos</h5>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGTwo">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <?php if (permissao_habilitada('vProduto', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="vProduto" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Produto</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('aProduto', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="aProduto" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Produto</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('eProduto', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="eProduto" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Produto</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('dProduto', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="dProduto" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Produto</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (permissao_habilitada('vPessoa', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('aPessoa', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('ePessoa', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('dPessoa', $permissoes_habilitadas, $ten_id)): ?>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGPessoas"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-group icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Pessoas</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGPessoas">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <?php if (permissao_habilitada('vPessoa', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="vPessoa" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Pessoa</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('aPessoa', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="aPessoa" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Pessoa</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('ePessoa', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="ePessoa" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Pessoa</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('dPessoa', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="dPessoa" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Pessoa</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (modulo_habilitado(['vContrato', 'aContrato', 'eContrato', 'dContrato'], $permissoes_habilitadas, $ten_id)): ?>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGContratos"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-file-blank icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Contratos</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGContratos">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <?php if (permissao_habilitada('vContrato', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="vContrato" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Contrato</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('aContrato', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="aContrato" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Contrato</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('eContrato', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="eContrato" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Contrato</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('dContrato', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="dContrato" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Contrato</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (modulo_habilitado(['vEmpresa', 'aEmpresa', 'eEmpresa', 'dEmpresa'], $permissoes_habilitadas, $ten_id)): ?>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGEmpresas"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-buildings icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Empresas</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGEmpresas">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <?php if (permissao_habilitada('vEmpresa', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="vEmpresa" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Empresa</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('aEmpresa', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="aEmpresa" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Empresa</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('eEmpresa', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="eEmpresa" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Empresa</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('dEmpresa', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="dEmpresa" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Empresa</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGThree"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-stopwatch icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Serviços</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGThree">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vServico" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Serviço</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aServico" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Serviço</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="eServico" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Serviço</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dServico" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Serviço</span>
                                                </label>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGFour"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-spreadsheet icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Ordens de Serviço</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGFour">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vOs" class="marcar" type="checkbox"
                                                        value="1" />
                                                    <span class="lbl"> Visualizar OS</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aOs" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar OS</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="eOs" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar OS</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dOs" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir OS</span>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGFive"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-cart-alt icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Vendas</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGFive">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vVenda" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Venda</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aVenda" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Venda</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="eVenda" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Venda</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dVenda" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Venda</span>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGSix"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-credit-card-front icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Cobranças</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGSix">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vCobranca"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Cobranças</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aCobranca"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Cobranças</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="eCobranca"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Cobranças</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dCobranca"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Cobranças</span>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGSeven"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-receipt icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Garantias</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGSeven">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vGarantia"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Garantia</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aGarantia"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Garantia</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="eGarantia"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Garantia</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dGarantia"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Garantia</span>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGEight"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-box icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Arquivos</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGEight">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vArquivo" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Arquivo</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aArquivo" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Arquivo</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="eArquivo" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Arquivo</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dArquivo" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Arquivo</span>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGNine"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-bar-chart-square icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Financeiro</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGNine">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vLancamento"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Lançamento</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aLancamento"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Lançamento</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="eLancamento"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Lançamento</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dLancamento"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Lançamento</span>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGTen"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-chart icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Relatórios</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGTen">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="rCliente" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Relatório Cliente</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="rServico" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Relatório Serviço</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="rOs" class="marcar" type="checkbox"
                                                        value="1" />
                                                    <span class="lbl"> Relatório OS</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="rProduto" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Relatório Produto</span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="rVenda" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Relatório Venda</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="rFinanceiro"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Relatório Financeiro</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="rNfe" class="marcar" type="checkbox"
                                                        value="1" />
                                                    <span class="lbl"> Relatório de NF-e Emitidas</span>
                                                </label>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGEleven"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-cog icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Configurações e Sistema</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGEleven">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="cUsuario" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Configurar Usuário</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="cEmitente"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Configurar Emitente</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="cPermissao"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Configurar Permissão</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="cBackup" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Backup</span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="cAuditoria" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Auditoria</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="cEmail" class="marcar" type="checkbox"
                                                        value="1" />
                                                    <span class="lbl"> Emails</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="cSistema" class="marcar" type="checkbox"
                                                        value="1" />
                                                    <span class="lbl"> Sistema</span>
                                                </label>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGNfe"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-receipt icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Emissor de Notas</h5>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGNfe">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label>
                                                        <input name="vNfe" class="marcar"
                                                            type="checkbox" value="1" />
                                                        <span class="lbl"> Visualizar Emissor de
                                                            Notas</span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input name="eNfe" class="marcar"
                                                            type="checkbox" value="1" />
                                                        <span class="lbl"> Editar Emissor de
                                                            Notas</span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGTrib"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-receipt icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Tributações</h5>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGTrib">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label>
                                                        <input name="vTributacao"
                                                            class="marcar" type="checkbox" value="1" />
                                                        <span class="lbl"> Visualizar Tributações</span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input name="vTributacaoProduto" class="marcar"
                                                            type="checkbox" value="1" />
                                                        <span class="lbl"> Visualizar Tributação
                                                            Produto</span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input name="aTributacaoProduto" class="marcar"
                                                            type="checkbox" value="1" />
                                                        <span class="lbl"> Adicionar Tributação
                                                            Produto</span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input name="eTributacaoProduto" class="marcar"
                                                            type="checkbox" value="1" />
                                                        <span class="lbl"> Editar Tributação
                                                            Produto</span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>
                                                        <input name="dTributacaoProduto" class="marcar"
                                                            type="checkbox" value="1" />
                                                        <span class="lbl"> Excluir Tributação
                                                            Produto</span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php if (permissao_habilitada('vOperacaoComercial', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('aOperacaoComercial', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('eOperacaoComercial', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('dOperacaoComercial', $permissoes_habilitadas, $ten_id)): ?>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGOperacaoComercial" data-toggle="collapse">
                                      <span><i class='bx bx-transfer icon-cli'></i></span>
                                      <h5 style="padding-left: 28px">Operação Comercial</h5>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGOperacaoComercial">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <?php if (permissao_habilitada('vOperacaoComercial', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="vOperacaoComercial" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Operação Comercial</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('aOperacaoComercial', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="aOperacaoComercial" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Operação Comercial</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('eOperacaoComercial', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="eOperacaoComercial" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Operação Comercial</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('dOperacaoComercial', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="dOperacaoComercial" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Operação Comercial</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if (permissao_habilitada('vClassificacaoFiscal', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('aClassificacaoFiscal', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('eClassificacaoFiscal', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('dClassificacaoFiscal', $permissoes_habilitadas, $ten_id)): ?>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGClassificacaoFiscal" data-toggle="collapse">
                                      <span><i class='bx bx-receipt icon-cli'></i></span>
                                      <h5 style="padding-left: 28px">Classificação Fiscal</h5>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGClassificacaoFiscal">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <?php if (permissao_habilitada('vClassificacaoFiscal', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="vClassificacaoFiscal" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Classificação Fiscal</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <tr>
                                            <?php if (permissao_habilitada('aClassificacaoFiscal', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="aClassificacaoFiscal" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Classificação Fiscal</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <tr>
                                            <?php if (permissao_habilitada('eClassificacaoFiscal', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="eClassificacaoFiscal" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Classificação Fiscal</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <tr>
                                            <?php if (permissao_habilitada('dClassificacaoFiscal', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="dClassificacaoFiscal" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Classificação Fiscal</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGAliquotas" data-toggle="collapse">
                                      <span><i class='bx bx-percentage icon-cli'></i></span>
                                      <h5 style="padding-left: 28px">Alíquotas</h5>
                                      <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGAliquotas">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vAliquota" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Alíquota</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aAliquota" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Alíquota</span>
                                                </label>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Faturamento de Entrada -->
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGFaturamentoEntrada" data-toggle="collapse">
                                      <span><i class='bx bx-file-invoice icon-cli'></i></span>
                                      <h5 style="padding-left: 28px">Faturamento de Entrada</h5>
                                      <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGFaturamentoEntrada">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vFaturamentoEntrada" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Faturamento de
                                                        Entrada</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aFaturamentoEntrada" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Faturamento de
                                                        Entrada</span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="eFaturamentoEntrada" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Faturamento de
                                                        Entrada</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dFaturamentoEntrada" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Faturamento de Entrada</span>
                                                </label>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGNcm"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-package icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">NCMs</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse in accordion-body" id="collapseGNcm">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vNcm" class="marcar" type="checkbox"
                                                        value="1" />
                                                    <span class="lbl"> Visualizar NCMs</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aNcm" class="marcar" type="checkbox"
                                                        value="1" />
                                                    <span class="lbl"> Adicionar NCMs</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="eNcm" class="marcar" type="checkbox"
                                                        value="1" />
                                                    <span class="lbl"> Editar NCMs</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dNcm" class="marcar" type="checkbox"
                                                        value="1" />
                                                    <span class="lbl"> Excluir NCMs</span>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGNfecom"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-file-invoice icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">NFECom</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                                <div class="collapse accordion-body" id="collapseGNfecom">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vNfecom" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar NFECom</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aNfecom" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar NFECom</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="eNfecom" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Editar NFECom</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dNfecom" class="marcar"
                                                        type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir NFECom</span>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Certificados Digitais -->
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGCertificados"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-certification icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Certificados Digitais</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGCertificados">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vCertificado"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Certificados</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aCertificado"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar/Ativar
                                                        Certificados</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dCertificado"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Certificados</span>
                                                </label>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Configurações Fiscais -->
                        <?php if (permissao_habilitada('vConfigFiscal', $permissoes_habilitadas, $ten_id) || 
                                  permissao_habilitada('eConfigFiscal', $permissoes_habilitadas, $ten_id)): ?>
                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGConfigFiscais"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-cog icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Configurações Fiscais</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGConfigFiscais">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <?php if (permissao_habilitada('vConfigFiscal', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="vConfigFiscal"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Configurações</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                            <?php if (permissao_habilitada('eConfigFiscal', $permissoes_habilitadas, $ten_id)): ?>
                                            <td>
                                                <label>
                                                    <input name="eConfigFiscal"
                                                        class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Configurações</span>
                                                </label>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="accordion-group widget-box">
                            <div class="accordion-heading">
                                <div class="widget-title">
                                    <a data-parent="#collapse-group" href="#collapseGTiposClientesAdd"
                                        data-toggle="collapse">
                                        <span><i class='bx bx-user icon-cli'></i></span>
                                        <h5 style="padding-left: 28px">Tipos de Clientes</h5>
                                        <span><i class='bx bx-chevron-right icon-clic'></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse accordion-body" id="collapseGTiposClientesAdd">
                                <div class="widget-content">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input name="vTipoCliente" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Visualizar Tipo Cliente</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="aTipoCliente" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Adicionar Tipo Cliente</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="eTipoCliente" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Editar Tipo Cliente</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input name="dTipoCliente" class="marcar" type="checkbox" value="1" />
                                                    <span class="lbl"> Excluir Tipo Cliente</span>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="form-actions">
                            <div class="span12">
                                <div class="span6 offset3" style="display:flex;justify-content: center">
                                    <button type="submit" class="button btn btn-success">
                                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Confirmar</span></button>
                                    <a title="Voltar" class="button btn btn-mini btn-warning"
                                        href="<?php echo site_url() ?>/permissoes">
                                        <span class="button__icon"><i class="bx bx-undo"></i></span>
                                        <span class="button__text2">Voltar</span></a>
                                </div>
                            </div>
                        </div>
                                    </div>
                                </div>
                            </div>
    </form>
</div>

<script type="text/javascript" src="<?php echo base_url() ?>assets/js/validate.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#marcarTodos").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });
        $("#formPermissao").validate({
            rules: {
                nome: { required: true }
            },
            messages: {
                nome: { required: 'Campo obrigatório' }
            }
        });

        // Função de pesquisa
        $("#pesquisarPermissao").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".accordion-group").filter(function() {
                var $group = $(this);
                var text = $group.text().toLowerCase();
                var matches = text.indexOf(value) > -1;
                $group.toggle(matches);
            });
        });
    });
</script>
