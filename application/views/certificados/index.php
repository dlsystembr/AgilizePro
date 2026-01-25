<div class="span12">
    <div class="widget-box">
        <div class="widget-title">
            <span class="icon">
                <i class="fas fa-certificate"></i>
            </span>
            <h5>Certificados Digitais</h5>
            <div class="buttons">
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCertificado')) { ?>
                    <a href="<?php echo base_url(); ?>index.php/certificados/adicionar" class="btn btn-success">
                        <i class="fas fa-plus"></i> Novo Certificado
                    </a>
                <?php } ?>
            </div>
        </div>

        <div class="widget-content nopadding">
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>

            <?php if (empty($certificados)) { ?>
                <div class="alert alert-info" style="margin: 20px;">
                    <i class="fas fa-info-circle"></i> Nenhum certificado digital cadastrado.
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCertificado')) { ?>
                        <a href="<?php echo base_url(); ?>index.php/certificados/adicionar">Clique aqui para adicionar</a>.
                    <?php } ?>
                </div>
            <?php } else { ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Tipo</th>
                            <th width="20%">CNPJ</th>
                            <th width="15%">Validade</th>
                            <th width="15%">Status</th>
                            <th width="15%">Upload</th>
                            <th width="15%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificados as $cert) {
                            $vencido = strtotime($cert->cer_validade_fim) < strtotime(date('Y-m-d'));
                            $diasRestantes = floor((strtotime($cert->cer_validade_fim) - strtotime(date('Y-m-d'))) / (60 * 60 * 24));
                            $proximoVencer = $diasRestantes > 0 && $diasRestantes <= 30;
                            ?>
                            <tr>
                                <td>
                                    <?php echo $cert->cer_id; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <?php echo $cert->cer_tipo; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    if ($cert->cer_cnpj) {
                                        echo preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cert->cer_cnpj);
                                    } else {
                                        echo '<span class="text-muted">Não extraído</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($cert->cer_validade_fim)); ?>
                                </td>
                                <td>
                                    <?php if ($vencido) { ?>
                                        <span class="badge badge-important">
                                            <i class="fas fa-times-circle"></i> Vencido
                                        </span>
                                    <?php } elseif ($proximoVencer) { ?>
                                        <span class="badge badge-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Vence em
                                            <?php echo $diasRestantes; ?> dias
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> Válido
                                        </span>
                                    <?php } ?>

                                    <?php if ($cert->cer_ativo) { ?>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Ativo
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge">Inativo</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <small>
                                        <?php echo date('d/m/Y H:i', strtotime($cert->cer_data_upload)); ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCertificado') && !$cert->cer_ativo && !$vencido) { ?>
                                        <a href="<?php echo base_url(); ?>index.php/certificados/ativar/<?php echo $cert->cer_id; ?>"
                                            class="btn btn-mini btn-success tip-top" title="Ativar Certificado">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php } ?>

                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dCertificado')) { ?>
                                        <a href="#modal-excluir" role="button" data-toggle="modal"
                                            data-id="<?php echo $cert->cer_id; ?>" class="btn btn-mini btn-danger tip-top"
                                            title="Excluir Certificado">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Modal Excluir -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-excluir-label"
    aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/certificados/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="modal-excluir-label">Excluir Certificado</h3>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idCertificado" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este certificado digital?</h5>
            <p style="text-align: center; color: #999;">
                <i class="fas fa-exclamation-triangle"></i> Esta ação não pode ser desfeita.
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-danger">Excluir</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#modal-excluir').on('show', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            $('#idCertificado').val(id);
        });
    });
</script>

<?php $this->load->view('tema/rodape'); ?>