<script type="text/javascript">
    $(document).ready(function() {
        // Adiciona método personalizado para validar números decimais
        $.validator.addMethod("decimal", function(value, element) {
            // Remove pontos de milhar e substitui vírgula por ponto
            value = value.replace(/\./g, '').replace(',', '.');
            // Verifica se é um número válido
            return this.optional(element) || !isNaN(parseFloat(value)) && isFinite(value);
        }, "Por favor, insira um número decimal válido.");

        // Adiciona método personalizado para validar valor mínimo
        $.validator.addMethod("minDecimal", function(value, element, param) {
            // Remove pontos de milhar e substitui vírgula por ponto
            value = value.replace(/\./g, '').replace(',', '.');
            return this.optional(element) || parseFloat(value) >= param;
        }, "O valor não pode ser negativo.");

        $(".money").maskMoney();
        
        // Configuração dos campos decimais
        $(".decimal").maskMoney({
            thousands: '.',
            decimal: ',',
            allowZero: true,
            precision: 3,
            allowNegative: false,
            suffix: ''
        });

        // Carregar unidades
        $.getJSON('<?php echo base_url() ?>assets/json/tabela_medidas.json', function(data) {
            var select = $('#unidade');
            select.empty();
            select.append('<option value="">Selecione</option>');
            $.each(data.medidas, function(i, medida) {
                select.append($('<option></option>').val(medida.sigla).text(medida.descricao));
            });
        });

        // Validação dos campos decimais
        $('#formProduto').validate({
            rules: {
                descricao: {
                    required: true
                },
                unidade: {
                    required: true
                },
                precoCompra: {
                    required: true
                },
                precoVenda: {
                    required: true
                },
                estoque: {
                    required: true
                },
                peso_bruto: {
                    decimal: true,
                    minDecimal: 0
                },
                peso_liquido: {
                    decimal: true,
                    minDecimal: 0
                },
                largura: {
                    decimal: true,
                    minDecimal: 0
                },
                altura: {
                    decimal: true,
                    minDecimal: 0
                },
                comprimento: {
                    decimal: true,
                    minDecimal: 0
                }
            },
            messages: {
                descricao: {
                    required: 'Campo Requerido.'
                },
                unidade: {
                    required: 'Campo Requerido.'
                },
                precoCompra: {
                    required: 'Campo Requerido.'
                },
                precoVenda: {
                    required: 'Campo Requerido.'
                },
                estoque: {
                    required: 'Campo Requerido.'
                },
                peso_bruto: {
                    decimal: "O campo Peso Bruto deve conter um número decimal válido",
                    minDecimal: "O Peso Bruto não pode ser negativo"
                },
                peso_liquido: {
                    decimal: "O campo Peso Líquido deve conter um número decimal válido",
                    minDecimal: "O Peso Líquido não pode ser negativo"
                },
                largura: {
                    decimal: "O campo Largura deve conter um número decimal válido",
                    minDecimal: "A Largura não pode ser negativa"
                },
                altura: {
                    decimal: "O campo Altura deve conter um número decimal válido",
                    minDecimal: "A Altura não pode ser negativa"
                },
                comprimento: {
                    decimal: "O campo Comprimento deve conter um número decimal válido",
                    minDecimal: "O Comprimento não pode ser negativo"
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });

        // Melhorar a usabilidade dos campos decimais
        $('.decimal').on('focus', function() {
            $(this).select();
        }).on('click', function() {
            $(this).select();
        });

        // Converter valores para formato decimal antes do envio
        $('#formProduto').on('submit', function() {
            $('.decimal').each(function() {
                var value = $(this).val();
                if (value) {
                    value = value.replace(/\./g, '').replace(',', '.');
                    $(this).val(value);
                }
            });
        });
    });
</script> 