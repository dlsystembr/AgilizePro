$(document).ready(function() {
    $('#formImportar').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var $progressContainer = $('#progressContainer');
        var $progressBar = $progressContainer.find('.progress-bar');
        var $progressText = $('#progressText');
        var $progressDetails = $('#progressDetails');
        
        // Mostra o container de progresso
        $progressContainer.show();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        var percent = Math.round((e.loaded / e.total) * 100);
                        $progressBar.css('width', percent + '%');
                        $progressText.text(percent + '%');
                        $progressDetails.text('Enviando arquivo...');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                if (response.success) {
                    // Atualiza a barra de progresso com o progresso da importação
                    var updateProgress = function() {
                        if (response.progress < 100) {
                            $progressBar.css('width', response.progress + '%');
                            $progressText.text(response.progress + '%');
                            $progressDetails.text('Processando registro ' + response.current + ' de ' + response.total);
                            
                            // Faz uma nova requisição para verificar o progresso
                            setTimeout(function() {
                                $.get($('#formImportar').attr('action') + '/progress', function(newResponse) {
                                    if (newResponse.success) {
                                        response = newResponse;
                                        updateProgress();
                                    }
                                });
                            }, 1000);
                        } else {
                            // Importação concluída
                            $progressBar.css('width', '100%');
                            $progressText.text('100%');
                            $progressDetails.text('Importação concluída!');
                            
                            // Mostra mensagem de sucesso
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message,
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        }
                    };
                    
                    updateProgress();
                } else {
                    // Mostra mensagem de erro
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: response.message
                    });
                    
                    // Esconde o container de progresso
                    $progressContainer.hide();
                }
            },
            error: function(xhr, status, error) {
                // Mostra mensagem de erro
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Ocorreu um erro durante o upload. Por favor, tente novamente.'
                });
                
                // Esconde o container de progresso
                $progressContainer.hide();
            }
        });
    });
}); 