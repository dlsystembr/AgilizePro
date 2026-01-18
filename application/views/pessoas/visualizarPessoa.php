<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
} ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Pessoas
            <small>Visualizar</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/pessoas">Pessoas</a></li>
            <li class="active">Visualizar</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Detalhes da Pessoa</h3>
                        <div class="box-tools">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'ePessoa')) { ?>
                                <a href="<?php echo base_url() ?>index.php/pessoas/editar/<?php echo $result->idPessoa; ?>" class="btn btn-warning"><i class="fa fa-pencil"></i> Editar</a>
                            <?php } ?>
                            <a href="<?php echo base_url() ?>index.php/pessoas" class="btn btn-default"><i class="fa fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <p><?php echo $result->nome; ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo de Pessoa</label>
                                    <p><?php echo $result->tipoPessoa; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <p><?php echo $result->email; ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Telefone</label>
                                    <p><?php echo $result->telefone; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>CPF/CNPJ</label>
                                    <p><?php echo $result->cpf_cnpj; ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Situação</label>
                                    <p><?php echo $result->situacao == 1 ? 'Ativo' : 'Inativo'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Observações</label>
                                    <p><?php echo $result->observacoes; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 