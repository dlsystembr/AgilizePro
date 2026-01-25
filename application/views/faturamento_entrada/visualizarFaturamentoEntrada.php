<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Faturamento de Entrada
            <small>Visualizar</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/faturamentoEntrada">Faturamento de Entrada</a></li>
            <li class="active">Visualizar</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Visualizar Faturamento de Entrada</h3>
                        <div class="box-tools">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturamentoEntrada')) { ?>
                                <?php 
                                // Verificar se já existe documento_faturado com status ABERTO
                                $pes_id = null;
                                
                                // Tentar buscar pes_id através da tabela clientes (nova estrutura)
                                if ($this->db->table_exists('clientes')) {
                                    $cliente_novo = $this->db->where('cln_id', $faturamento->fornecedor_id)->get('clientes')->row();
                                    if ($cliente_novo) {
                                        $pes_id = $cliente_novo->pes_id;
                                    }
                                }
                                
                                // Se não encontrou, tentar pela tabela antiga clientes_
                                if (!$pes_id && $this->db->table_exists('clientes_')) {
                                    $fornecedor = $this->db->where('idClientes', $faturamento->fornecedor_id)->get('clientes_')->row();
                                    if ($fornecedor) {
                                        $documento_limpo = preg_replace('/\D/', '', $fornecedor->documento);
                                        $pessoa = $this->db->where('pes_cpfcnpj', $documento_limpo)->get('pessoas')->row();
                                        if ($pessoa) {
                                            $pes_id = $pessoa->pes_id;
                                        }
                                    }
                                }
                                
                                $dcf_aberto = null;
                                if ($pes_id) {
                                    // Buscar pela data de entrada primeiro
                                    $dcf_aberto = $this->db->where('pes_id', $pes_id)
                                                          ->where('dcf_tipo', 'E')
                                                          ->where('dcf_status', 'ABERTO')
                                                          ->where('dcf_data_saida', $faturamento->data_entrada)
                                                          ->get('documentos_faturados')
                                                          ->row();
                                    
                                    // Se não encontrar, tentar pelo número da nota
                                    if (!$dcf_aberto && $faturamento->numero_nota) {
                                        $dcf_aberto = $this->db->where('pes_id', $pes_id)
                                                              ->where('dcf_tipo', 'E')
                                                              ->where('dcf_numero', $faturamento->numero_nota)
                                                              ->where('dcf_status', 'ABERTO')
                                                              ->get('documentos_faturados')
                                                              ->row();
                                    }
                                }
                                if ($dcf_aberto && (empty($faturamento->status) || $faturamento->status != 'fechado')) {
                                ?>
                                    <button type="button" class="btn btn-success btn-xs" onclick="finalizarEntrada(<?php echo $faturamento->id; ?>)">
                                        <i class="fa fa-check-circle"></i> Finalizar Entrada
                                    </button>
                                <?php } ?>
                                <a href="<?php echo base_url() ?>index.php/faturamentoEntrada/editar/<?php echo $faturamento->id; ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Editar</a>
                            <?php } ?>
                            <a href="<?php echo base_url() ?>index.php/faturamentoEntrada" class="btn btn-default btn-xs"><i class="fa fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Operação Comercial</label>
                                    <p><?php echo $faturamento->nome_operacao; ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Chave de Acesso</label>
                                    <p><?php echo $faturamento->chave_acesso; ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Número da NFe</label>
                                    <p><?php echo $faturamento->numero_nfe; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Data de Entrada</label>
                                    <p><?php echo date('d/m/Y', strtotime($faturamento->data_entrada)); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Data de Emissão</label>
                                    <p><?php echo date('d/m/Y', strtotime($faturamento->data_emissao)); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fornecedor</label>
                                    <p><?php echo $faturamento->nome_fornecedor; ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Itens</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" style="width: 100%; white-space: nowrap;">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 200px;">Produto</th>
                                                    <th style="min-width: 100px;">Quantidade</th>
                                                    <th style="min-width: 120px;">Valor Unitário</th>
                                                    <th style="min-width: 100px;">Desconto</th>
                                                    <th style="min-width: 120px;">Base ICMS</th>
                                                    <th style="min-width: 120px;">Alíquota ICMS</th>
                                                    <th style="min-width: 120px;">Valor ICMS</th>
                                                    <th style="min-width: 120px;">Base ICMS ST</th>
                                                    <th style="min-width: 120px;">Alíquota ICMS ST</th>
                                                    <th style="min-width: 120px;">Valor ICMS ST</th>
                                                    <th style="min-width: 120px;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($itens as $i) { ?>
                                                    <tr>
                                                        <td style="min-width: 200px;"><?php echo $i->nome_produto; ?></td>
                                                        <td style="min-width: 100px;"><?php echo number_format($i->quantidade, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->valor_unitario, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 100px;">R$ <?php echo number_format($i->desconto, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->base_calculo_icms, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;"><?php echo number_format($i->aliquota_icms, 2, ',', '.'); ?>%</td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->valor_icms, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->base_calculo_icms_st, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;"><?php echo number_format($i->aliquota_icms_st, 2, ',', '.'); ?>%</td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->valor_icms_st, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->total_item, 2, ',', '.'); ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Despesas</label>
                                    <p>R$ <?php echo number_format($faturamento->despesas, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Frete</label>
                                    <p>R$ <?php echo number_format($faturamento->frete, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Total Base ICMS</label>
                                    <p>R$ <?php echo number_format($faturamento->total_base_icms, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Total ICMS</label>
                                    <p>R$ <?php echo number_format($faturamento->total_icms, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Total Base ICMS ST</label>
                                    <p>R$ <?php echo number_format($faturamento->total_base_icms_st, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Total ICMS ST</label>
                                    <p>R$ <?php echo number_format($faturamento->total_icms_st, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Total da Nota</label>
                                    <p>R$ <?php echo number_format($faturamento->total_nota, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    function finalizarEntrada(id) {
        Swal.fire({
            title: 'Finalizar Entrada',
            text: 'Deseja realmente finalizar esta entrada? Isso irá atualizar o estoque dos produtos.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, finalizar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/finalizarEntrada',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON || {};
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message || 'Erro ao finalizar entrada. Tente novamente.'
                        });
                    }
                });
            }
        });
    }
</script> 