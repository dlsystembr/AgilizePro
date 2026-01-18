<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Importar NCMs</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Importar NCMs</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($this->session->flashdata('error')) { ?>
                                <div class="alert alert-danger">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo $this->session->flashdata('error'); ?>
                                </div>
                            <?php } ?>
                            <?php if ($this->session->flashdata('success')) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo $this->session->flashdata('success'); ?>
                                </div>
                            <?php } ?>

                            <form action="<?php echo base_url() ?>index.php/ncms/importar" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="arquivo">Arquivo JSON</label>
                                    <input type="file" class="form-control-file" id="arquivo" name="arquivo" accept=".json" required>
                                    <small class="form-text text-muted">Selecione um arquivo JSON contendo os dados dos NCMs.</small>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Importar
                                </button>
                                <a href="<?php echo base_url() ?>index.php/ncms" class="btn btn-default">
                                    <i class="fas fa-arrow-left"></i> Voltar
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 