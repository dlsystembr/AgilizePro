<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Adiciona SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<!-- Meta tags CSRF -->
<meta name="csrf-token-name" content="<?php echo $csrf['name']; ?>">
<meta name="csrf-token" content="<?php echo $csrf['hash']; ?>">

    <div class="new122">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-barcode"></i>
            </span>
            <h5>NCMs</h5>
        </div>
        <div class="span12" style="margin-left: 0">
            <form method="get" action="<?php echo base_url(); ?>index.php/ncms" style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <a href="#modalImportar" data-toggle="modal" class="button btn btn-mini btn-success" style="max-width: 160px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                        <span class="button__text2">Importar NCMs</span>
                    </a>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 300px;">
                        <input type="text" name="pesquisa" id="pesquisa" placeholder="Buscar por Código ou Descrição" class="span12" value="<?php echo $search; ?>">
                    </div>
                    <div style="width: 200px;">
                        <select name="tipo" class="span12">
                            <option value="">Todos</option>
                            <option value="analitico" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'analitico' ? 'selected' : ''; ?>>Analítico</option>
                            <option value="sintetico" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'sintetico' ? 'selected' : ''; ?>>Sintético</option>
                            <option value="configurados" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'configurados' ? 'selected' : ''; ?>>Somente Configurados</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 5px;">
                        <button class="button btn btn-mini btn-warning" style="min-width: 30px;">
                            <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                        </button>
                        <?php if($search || isset($_GET['tipo'])): ?>
                            <a href="<?php echo base_url() ?>index.php/ncms" class="button btn btn-mini btn-danger" style="min-width: 30px;">
                                <span class="button__icon"><i class='bx bx-x'></i></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="widget-box">
            <h5 style="padding: 3px 0"></h5>
            <div class="widget-content nopadding tab-content">
            <table id="" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>Configuração</th>
                            <th style="text-align:center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($ncms == null) { ?>
                            <tr>
                            <td colspan="7">Nenhum NCM encontrado</td>
                            </tr>
                    <?php } else {
                        foreach ($ncms as $ncm) { 
                            $tipo = strlen($ncm->NCM_CODIGO) == 8 ? 'Analítico' : 'Sintético';
                            $corTipo = $tipo == 'Analítico' ? '#436eee' : '#B266FF';
                            $configuracao = '';
                            if ($ncm->tributacao_federal) {
                                $configuracao .= '<span class="label label-info">Federal</span> ';
                            }
                            if ($ncm->tributacao_estadual) {
                                $configuracao .= '<span class="label label-success">Estadual</span>';
                            }
                            if (!$configuracao) {
                                $configuracao = '<span class="label label-default">Não Configurado</span>';
                            }
                        ?>
                                <tr>
                                    <td><?php echo $ncm->NCM_CODIGO; ?></td>
                                    <td><?php echo $ncm->NCM_DESCRICAO; ?></td>
                                    <td><span class="badge" style="background-color: <?php echo $corTipo; ?>; border-color: <?php echo $corTipo; ?>"><?php echo $tipo; ?></span></td>
                                    <td><?php echo $ncm->data_inicio ? date('d/m/Y', strtotime($ncm->data_inicio)) : ''; ?></td>
                                    <td><?php echo $ncm->data_fim ? date('d/m/Y', strtotime($ncm->data_fim)) : ''; ?></td>
                                    <td><?php echo $configuracao; ?></td>
                                    <td style="text-align:left">
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNcm')) { ?>
                                        <a style="margin-right: 1%" href="<?php echo base_url() ?>index.php/ncms/visualizarTributacao/<?php echo $ncm->NCM_ID; ?>" class="btn-nwe" title="Visualizar NCM">
                                                <i class="bx bx-show bx-xs"></i>
                                            </a>
                                        <?php } ?>
                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNcm')) { ?>
                                        <a style="margin-right: 1%" href="<?php echo base_url() ?>index.php/ncms/tributacao/<?php echo $ncm->NCM_ID; ?>" class="btn-nwe6" title="Configurar Tributação">
                                            <i class="bx bx-list-plus "></i>
                                            </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php echo $this->pagination->create_links(); ?>

<!-- Modal Importar -->
<div class="modal hide fade" id="modalImportar" tabindex="-1" role="dialog" aria-labelledby="modalImportarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
                <h5 class="modal-title" id="modalImportarLabel">Importar NCMs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formImportar" action="<?php echo base_url(); ?>ncms/importar" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="arquivo">Arquivo JSON</label>
                        <input type="file" class="form-control-file" id="arquivo" name="arquivo" accept=".json">
                        <small class="form-text text-muted">
                            O arquivo deve estar no formato JSON com a seguinte estrutura:<br>
                            <pre>{
    "Data_Ultima_Atualizacao_NCM": "DD/MM/YYYY",
    "Ato": "Número do Ato",
    "Nomenclaturas": [
        {
            "Codigo": "00000000",
            "Descricao": "Descrição do NCM",
            "Data_Inicio": "DD/MM/YYYY",
            "Data_Fim": "DD/MM/YYYY",
            "Tipo_Ato_Ini": "Tipo do Ato",
            "Numero_Ato_Ini": "Número do Ato",
            "Ano_Ato_Ini": "Ano do Ato"
        }
    ]
}</pre>
                        </small>
                    </div>

                    <!-- Container de Progresso -->
            <div id="progressContainer" style="display: none;">
                        <div class="progress mb-2">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%" 
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small id="progressText" class="form-text text-muted">Aguardando início da importação...</small>
                </div>

                    <!-- Container de Erros -->
                    <div id="errorContainer" style="display: none;" class="mt-3">
                        <div class="alert alert-warning">
                            <h6>Erros encontrados durante a importação:</h6>
                            <ul id="errorList" class="mb-0"></ul>
                </div>
            </div>
        </div>
        <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" id="btnImportar">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Inicializa o DataTable com configurações básicas
    var table = $('#tabela').DataTable({
        "ordering": false,
        "info": false,
        "lengthChange": false,
        "pageLength": 20,
        "language": {
            "url": "<?php echo base_url(); ?>assets/js/dataTable_pt-br.json",
        },
        "dom": '<"top"<"span6"l>>rt<"bottom"<"span6"i><"span6"p>><"clear">',
        "bAutoWidth": false,
        "responsive": true,
        "scrollX": true,
        "columnDefs": [
            { "width": "10%", "targets": 0 }, // Código
            { "width": "40%", "targets": 1 }, // Descrição
            { "width": "10%", "targets": 2 }, // Tipo
            { "width": "15%", "targets": 3 }, // Data Início
            { "width": "15%", "targets": 4 }, // Data Fim
            { "width": "10%", "targets": 5 }, // Configuração
            { "width": "10%", "targets": 6 }  // Ações
        ]
    });

    // Limpa o campo de pesquisa quando clicar no X
    $('.btn-danger').click(function() {
        $('#pesquisa').val('');
    });

    // Função para importar NCMs
    $('#formImportar').on('submit', function(e) {
        e.preventDefault();
        
        var fileInput = $('#arquivo')[0];
        if (!fileInput.files.length) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Por favor, selecione um arquivo para importar.'
            });
            return;
        }

        var file = fileInput.files[0];
        if (file.size > 100 * 1024 * 1024) { // 100MB
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'O arquivo é muito grande. O tamanho máximo permitido é 100MB.'
            });
            return;
        }

        // Mostra o container de progresso
        $('#progressContainer').show();
        $('#errorContainer').hide();
        $('#progressBar').css('width', '0%');
        $('#progressText').text('Iniciando importação...');

        var formData = new FormData(this);
        var xhr = new XMLHttpRequest();
        var eventSource;

        // Configura o upload do arquivo
        xhr.open('POST', '<?php echo base_url(); ?>ncms/importar', true);
        
        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                var percentComplete = (e.loaded / e.total) * 100;
                $('#progressBar').css('width', percentComplete + '%');
                $('#progressText').text('Enviando arquivo: ' + Math.round(percentComplete) + '%');
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                        try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Inicia o SSE para receber atualizações
                        eventSource = new EventSource('<?php echo base_url(); ?>ncms/importar?sse=true');
                        
                        eventSource.onmessage = function(e) {
                            var data = JSON.parse(e.data);
                            
                            if (data.success) {
                                $('#progressBar').css('width', data.progress + '%');
                                $('#progressText').text(
                                    'Processando: ' + data.current + ' de ' + data.total + 
                                    ' (' + data.progress + '%) - ' +
                                    'Importados: ' + data.imported + 
                                    ' - Erros: ' + data.errors
                                );

                                if (data.progress === 100) {
                                    eventSource.close();
                                    
                                    if (data.errors > 0) {
                                        var errorList = '';
                                        if (data.details && data.details.length > 0) {
                                            errorList = '<ul><li>' + data.details.join('</li><li>') + '</li></ul>';
                                        }
                                        
                        Swal.fire({
                            icon: 'warning',
                                            title: 'Importação Concluída',
                                            html: data.message + errorList,
                                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso',
                                            text: data.message,
                                            confirmButtonText: 'OK'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.reload();
                                            }
                                        });
                                    }
                                }
                            } else {
                                eventSource.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: data.message
                                });
                            }
                        };

                        eventSource.onerror = function() {
                            eventSource.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro de Conexão',
                                text: 'A conexão com o servidor foi perdida. Por favor, tente novamente.'
                            });
                        };
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro ao processar a resposta do servidor: ' + e.message
                    });
                        }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao enviar o arquivo. Status: ' + xhr.status
                });
            }
        };

        xhr.onerror = function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro de Conexão',
                text: 'Não foi possível conectar ao servidor. Por favor, tente novamente.'
            });
        };

        xhr.send(formData);
    });
});
</script> 