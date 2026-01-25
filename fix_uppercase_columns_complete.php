<?php
/**
 * Script completo para corrigir referências de colunas em maiúsculas para minúsculas no código
 * Baseado na lista completa de colunas do banco de dados
 * ⚠️ IMPORTANTE: Faça backup do código antes de executar!
 * 
 * Uso:
 * php fix_uppercase_columns_complete.php
 * ou acesse via navegador: http://localhost/mapos/fix_uppercase_columns_complete.php
 */

// Aumentar tempo limite de execução
set_time_limit(600); // 10 minutos
ini_set('max_execution_time', 600);

$codebase_path = __DIR__ . '/application';
$is_web = (php_sapi_name() !== 'cli');

// Lista completa de colunas do banco (já em minúsculas)
$columns_data = <<<DATA
aliquotas	id
aliquotas	uf_origem
aliquotas	uf_destino
aliquotas	aliquota_origem
aliquotas	aliquota_destino
aliquotas	created_at
aliquotas	updated_at
aliquotas	ten_id
anexos	idanexos
anexos	anexo
anexos	thumb
anexos	url
anexos	path
anexos	os_id
anexos	ten_id
anotacoes_os	idanotacoes
anotacoes_os	anotacao
anotacoes_os	data_hora
anotacoes_os	os_id
anotacoes_os	ten_id
bairros	bai_id
bairros	bai_nome
bairros	mun_id
bairros	bai_data_inclusao
bairros	bai_data_atualizacao
bairros	ten_id
categorias	idcategorias
categorias	categoria
categorias	cadastro
categorias	status
categorias	tipo
categorias	ten_id
certificados_digitais	cer_id
certificados_digitais	emp_id
certificados_digitais	cer_arquivo
certificados_digitais	cer_senha
certificados_digitais	cer_tipo
certificados_digitais	cer_cnpj
certificados_digitais	cer_validade_fim
certificados_digitais	cer_ativo
certificados_digitais	cer_data_upload
certificados_digitais	ten_id
ci_sessions	id
ci_sessions	ip_address
ci_sessions	timestamp
ci_sessions	data
classificacao_fiscal	clf_id
classificacao_fiscal	opc_id
classificacao_fiscal	tpc_id
classificacao_fiscal	clf_cst
classificacao_fiscal	clf_csosn
classificacao_fiscal	clf_natureza_contribuinte
classificacao_fiscal	clf_cfop
classificacao_fiscal	clf_destinacao
classificacao_fiscal	clf_objetivo_comercial
classificacao_fiscal	clf_finalidade
classificacao_fiscal	clf_tipo_tributacao
classificacao_fiscal	clf_mensagem
classificacao_fiscal	clf_cclasstrib
classificacao_fiscal	clf_cst_ibs
classificacao_fiscal	clf_aliq_ibs
classificacao_fiscal	clf_cst_cbs
classificacao_fiscal	clf_aliq_cbs
classificacao_fiscal	clf_data_inclusao
classificacao_fiscal	clf_data_alteracao
classificacao_fiscal	clf_situacao
classificacao_fiscal	ten_id
clientes	cln_id
clientes	pes_id
clientes	tpc_id
clientes	cln_limite_credito
clientes	cln_situacao
clientes	cln_data_cadastro
clientes	cln_lastupdate
clientes	cln_comprar_aprazo
clientes	cln_bloqueio_financeiro
clientes	cln_dias_carencia
clientes	cln_emitir_nfe
clientes	cln_data_inclusao
clientes	cln_data_alteracao
clientes	cln_objetivo_comercial
clientes	ten_id
clientes_	idclientes
clientes_	asaas_id
clientes_	nomecliente
clientes_	sexo
clientes_	pessoa_fisica
clientes_	documento
clientes_	natureza_contribuinte
clientes_	telefone
clientes_	celular
clientes_	email
clientes_	senha
clientes_	datacadastro
clientes_	rua
clientes_	numero
clientes_	bairro
clientes_	cidade
clientes_	estado
clientes_	cep
clientes_	objetivo_comercial
clientes_	inscricao
clientes_	ibge
clientes_	contato
clientes_	complemento
clientes_	fornecedor
clientes_	ten_id
clientes_vendedores	clv_id
clientes_vendedores	cln_id
clientes_vendedores	ven_id
clientes_vendedores	clv_padrao
clientes_vendedores	clv_data_inclusao
clientes_vendedores	clv_data_atualizacao
clientes_vendedores	ten_id
cobrancas	idcobranca
cobrancas	charge_id
cobrancas	conditional_discount_date
cobrancas	created_at
cobrancas	custom_id
cobrancas	expire_at
cobrancas	message
cobrancas	payment_method
cobrancas	payment_url
cobrancas	request_delivery_address
cobrancas	status
cobrancas	total
cobrancas	barcode
cobrancas	link
cobrancas	payment
cobrancas	pdf
cobrancas	vendas_id
cobrancas	os_id
cobrancas	clientes_id
cobrancas	payment_gateway
cobrancas	ten_id
configuracoes	idconfig
configuracoes	config
configuracoes	valor
configuracoes	ambiente
configuracoes	versao_nfe
configuracoes	tipo_impressao_danfe
configuracoes	orientacao_danfe
configuracoes	csc
configuracoes	csc_id
configuracoes	ten_id
configuracoes_fiscais	cfg_id
configuracoes_fiscais	emp_id
configuracoes_fiscais	cer_id
configuracoes_fiscais	cfg_tipo_documento
configuracoes_fiscais	cfg_ambiente
configuracoes_fiscais	cfg_serie
configuracoes_fiscais	cfg_numero_atual
configuracoes_fiscais	cfg_csc_id
configuracoes_fiscais	cfg_csc_token
configuracoes_fiscais	cfg_aliquota_iss
configuracoes_fiscais	cfg_regime_especial
configuracoes_fiscais	cfg_formato_impressao
configuracoes_fiscais	cfg_ativo
configuracoes_fiscais	cfg_data_cadastro
configuracoes_fiscais	cfg_data_atualizacao
configuracoes_fiscais	ten_id
configuracoes_nfce	id
configuracoes_nfce	tipo_documento
configuracoes_nfce	ambiente
configuracoes_nfce	versao_nfce
configuracoes_nfce	tipo_impressao_danfe
configuracoes_nfce	sequencia_nfce
configuracoes_nfce	csc
configuracoes_nfce	csc_id
configuracoes_nfce	preview_nfce
configuracoes_nfce	created_at
configuracoes_nfce	updated_at
configuracoes_nfce	ten_id
configuracoes_nfe	idconfiguracao
configuracoes_nfe	tipo_documento
configuracoes_nfe	ambiente
configuracoes_nfe	versao_nfe
configuracoes_nfe	tipo_impressao_danfe
configuracoes_nfe	orientacao_danfe
configuracoes_nfe	sequencia_nota
configuracoes_nfe	sequencia_nfce
configuracoes_nfe	csc
configuracoes_nfe	csc_id
configuracoes_nfe	imprimir_logo_nfe
configuracoes_nfe	preview_nfe
configuracoes_nfe	created_at
configuracoes_nfe	updated_at
configuracoes_nfe	ten_id
contas	idcontas
contas	conta
contas	banco
contas	numero
contas	saldo
contas	cadastro
contas	status
contas	tipo
contas	ten_id
contratos	ctr_id
contratos	pes_id
contratos	ctr_numero
contratos	ctr_data_inicio
contratos	ctr_data_fim
contratos	ctr_tipo_assinante
contratos	ctr_anexo
contratos	ctr_observacao
contratos	ctr_situacao
contratos	ctr_data_cadastro
contratos	ctr_data_alteracao
contratos	ten_id
contratos_itens	cti_id
contratos_itens	ctr_id
contratos_itens	pro_id
contratos_itens	cti_preco
contratos_itens	cti_quantidade
contratos_itens	cti_ativo
contratos_itens	cti_observacao
contratos_itens	cti_data_cadastro
contratos_itens	cti_data_atualizacao
contratos_itens	ten_id
documentos	doc_id
documentos	pes_id
documentos	doc_tipo_documento
documentos	end_id
documentos	doc_orgao_expedidor
documentos	doc_numero
documentos	doc_natureza_contribuinte
documentos	doc_data_inclusao
documentos	doc_data_atualizacao
documentos	ten_id
documentos_faturados	dcf_id
documentos_faturados	orv_id
documentos_faturados	pes_id
documentos_faturados	dcf_numero
documentos_faturados	dcf_serie
documentos_faturados	dcf_modelo
documentos_faturados	dcf_tipo
documentos_faturados	dcf_data_emissao
documentos_faturados	dcf_data_saida
documentos_faturados	dcf_valor_produtos
documentos_faturados	dcf_valor_desconto
documentos_faturados	dcf_valor_frete
documentos_faturados	dcf_valor_seguro
documentos_faturados	dcf_valor_outras_despesas
documentos_faturados	dcf_base_icms
documentos_faturados	dcf_valor_icms
documentos_faturados	dcf_valor_icms_desonerado
documentos_faturados	dcf_base_ipi
documentos_faturados	dcf_valor_ipi
documentos_faturados	dcf_base_pis
documentos_faturados	dcf_valor_pis
documentos_faturados	dcf_base_cofins
documentos_faturados	dcf_valor_cofins
documentos_faturados	dcf_base_ibs
documentos_faturados	dcf_valor_ibs
documentos_faturados	dcf_base_cbs
documentos_faturados	dcf_valor_cbs
documentos_faturados	dcf_retencao_irrf
documentos_faturados	dcf_retencao_pis
documentos_faturados	dcf_retencao_cofins
documentos_faturados	dcf_retencao_csll
documentos_faturados	dcf_valor_total
documentos_faturados	dcf_status
documentos_faturados	dcf_informacoes_adicionais
documentos_faturados	dcf_lastupdate
documentos_faturados	dcf_data_faturamento
documentos_faturados	ten_id
emails	eml_id
emails	pes_id
emails	eml_tipo
emails	eml_email
emails	eml_nome
emails	eml_data_inclusao
emails	eml_data_atualizacao
emails	ten_id
email_queue	id
email_queue	to
email_queue	cc
email_queue	bcc
email_queue	message
email_queue	status
email_queue	date
email_queue	headers
email_queue	ten_id
emitente	id
emitente	nome
emitente	cnpj
emitente	ie
emitente	rua
emitente	numero
emitente	bairro
emitente	cidade
emitente	uf
emitente	telefone
emitente	email
emitente	url_logo
emitente	cep
emitente	ten_id
empresas	emp_id
empresas	emp_razao_social
empresas	emp_nome_fantasia
empresas	emp_cnpj
empresas	emp_ie
empresas	emp_im
empresas	emp_cnae
empresas	emp_cep
empresas	emp_logradouro
empresas	emp_numero
empresas	emp_complemento
empresas	emp_bairro
empresas	emp_cidade
empresas	emp_uf
empresas	emp_ibge
empresas	emp_telefone
empresas	emp_celular
empresas	emp_email
empresas	emp_site
empresas	emp_regime_tributario
empresas	emp_aliq_cred_icms
empresas	emp_mensagem_simples
empresas	emp_logo_path
empresas	emp_mensagem_nota
empresas	emp_cor_primaria
empresas	emp_cor_secundaria
empresas	emp_ativo
empresas	emp_data_cadastro
empresas	emp_data_atualizacao
empresas	ten_id
enderecos	end_id
enderecos	pes_id
enderecos	est_id
enderecos	mun_id
enderecos	bai_id
enderecos	end_tipo_endenreco
enderecos	end_tipo_logradouro
enderecos	end_logradouro
enderecos	end_numero
enderecos	end_complemento
enderecos	end_cep
enderecos	end_zona
enderecos	end_observacao
enderecos	end_padrao
enderecos	end_situacao
enderecos	end_data_inclusao
enderecos	end_data_atualizacao
enderecos	ten_id
equipamentos	idequipamentos
equipamentos	equipamento
equipamentos	num_serie
equipamentos	modelo
equipamentos	cor
equipamentos	descricao
equipamentos	tensao
equipamentos	potencia
equipamentos	voltagem
equipamentos	data_fabricacao
equipamentos	marcas_id
equipamentos	clientes_id
equipamentos	ten_id
equipamentos_os	idequipamentos_os
equipamentos_os	defeito_declarado
equipamentos_os	defeito_encontrado
equipamentos_os	solucao
equipamentos_os	equipamentos_id
equipamentos_os	os_id
equipamentos_os	ten_id
estados	est_id
estados	est_nome
estados	est_uf
estados	est_codigo_uf
estados	est_data_inclusao
estados	est_data_alteracao
estados	ten_id
faturamento_entrada	id
faturamento_entrada	fornecedor_id
faturamento_entrada	transportadora_id
faturamento_entrada	modalidade_frete
faturamento_entrada	peso_bruto
faturamento_entrada	peso_liquido
faturamento_entrada	volume
faturamento_entrada	operacao_comercial_id
faturamento_entrada	data_emissao
faturamento_entrada	data_entrada
faturamento_entrada	numero_nota
faturamento_entrada	chave_acesso
faturamento_entrada	valor_total
faturamento_entrada	valor_produtos
faturamento_entrada	valor_icms
faturamento_entrada	total_base_icms_st
faturamento_entrada	total_icms_st
faturamento_entrada	valor_ipi
faturamento_entrada	valor_frete
faturamento_entrada	valor_outras_despesas
faturamento_entrada	observacoes
faturamento_entrada	data_cadastro
faturamento_entrada	status
faturamento_entrada	data_atualizacao
faturamento_entrada	usuario_id
faturamento_entrada	xml_conteudo
faturamento_entrada	desconto
faturamento_entrada	ten_id
faturamento_entrada_itens	fei_id
faturamento_entrada_itens	faturamento_entrada_id
faturamento_entrada_itens	pro_id
faturamento_entrada_itens	fei_quantidade
faturamento_entrada_itens	fei_valor_total
faturamento_entrada_itens	aliquota_icms
faturamento_entrada_itens	valor_icms
faturamento_entrada_itens	base_icms_st
faturamento_entrada_itens	valor_icms_st
faturamento_entrada_itens	aliquota_ipi
faturamento_entrada_itens	valor_ipi
faturamento_entrada_itens	desconto
faturamento_entrada_itens	base_calculo_icms_st
faturamento_entrada_itens	aliquota_icms_st
faturamento_entrada_itens	total_item
faturamento_entrada_itens	cst
faturamento_entrada_itens	cfop
faturamento_entrada_itens	base_calculo_icms
faturamento_entrada_itens	valor_unitario
faturamento_entrada_itens	ten_id
fornecedores	idfornecedores
fornecedores	nomefornecedor
fornecedores	cnpj
fornecedores	telefone
fornecedores	celular
fornecedores	email
fornecedores	rua
fornecedores	numero
fornecedores	bairro
fornecedores	cidade
fornecedores	estado
fornecedores	cep
fornecedores	fornecedor
fornecedores	ten_id
garantias	idgarantias
garantias	datagarantia
garantias	refgarantia
garantias	textogarantia
garantias	usuarios_id
garantias	ten_id
itens_de_vendas	itv_id
itens_de_vendas	itv_subtotal
itens_de_vendas	itv_quantidade
itens_de_vendas	itv_preco
itens_de_vendas	vendas_id
itens_de_vendas	pro_id
itens_de_vendas	ten_id
itens_faturados	itf_id
itens_faturados	itf_quantidade
itens_faturados	itf_valor_unitario
itens_faturados	itf_valor_total
itens_faturados	itf_desconto
itens_faturados	itf_unidade
itens_faturados	dcf_id
itens_faturados	pro_id
itens_faturados	ncm_id
itens_faturados	clf_id
itens_faturados	itf_pro_descricao
itens_faturados	itf_pro_ncm
itens_faturados	itf_ncm_cest
itens_faturados	itf_cfop
itens_faturados	itf_icms_cst
itens_faturados	itf_csosn
itens_faturados	itf_icms_aliquota
itens_faturados	itf_icms_valor_base
itens_faturados	itf_icms_valor
itens_faturados	itf_cod_beneficio
itens_faturados	itf_mot_desonerado
itens_faturados	itf_base_desonerado_icms
itens_faturados	itf_valor_desonerado_icms
itens_faturados	itf_pis_cst
itens_faturados	itf_pis_aliquota
itens_faturados	itf_pis_valor_base
itens_faturados	itf_pis_valor
itens_faturados	itf_cofins_cst
itens_faturados	itf_cofins_aliquota
itens_faturados	itf_cofins_valor_base
itens_faturados	itf_cofins_valor
itens_faturados	itf_ipi_cst
itens_faturados	itf_ipi_aliquota
itens_faturados	itf_ipi_valor_base
itens_faturados	itf_ipi_valor
itens_faturados	itf_cclass_trib
itens_faturados	itf_aliq_ibs
itens_faturados	itf_valor_ibs
itens_faturados	itf_aliq_cbs
itens_faturados	itf_valor_cbs
itens_faturados	itf_retencao_irrf
itens_faturados	itf_retencao_pis
itens_faturados	itf_retencao_cofins
itens_faturados	itf_retencao_csll
itens_faturados	itf_ibt
itens_faturados	itf_ibt_cst
itens_faturados	itf_ibt_aliquota
itens_faturados	itf_ibt_valor_base
itens_faturados	itf_ibt_valor
itens_faturados	itf_lastupdate
itens_faturados	ten_id
itens_pedido	itp_id
itens_pedido	pedido_id
itens_pedido	pro_id
itens_pedido	itp_quantidade
itens_pedido	itp_preco_unit
itens_pedido	itp_subtotal
itens_pedido	ten_id
itens_pedidos	itp_id
itens_pedidos	itp_subtotal
itens_pedidos	itp_quantidade
itens_pedidos	itp_preco
itens_pedidos	pds_id
itens_pedidos	pro_id
itens_pedidos	ten_id
lancamentos	idlancamentos
lancamentos	descricao
lancamentos	valor
lancamentos	desconto
lancamentos	valor_desconto
lancamentos	tipo_desconto
lancamentos	data_vencimento
lancamentos	data_pagamento
lancamentos	baixado
lancamentos	cliente_fornecedor
lancamentos	forma_pgto
lancamentos	tipo
lancamentos	anexo
lancamentos	clientes_id
lancamentos	categorias_id
lancamentos	contas_id
lancamentos	vendas_id
lancamentos	usuarios_id
lancamentos	observacoes
lancamentos	ten_id
logs	idlogs
logs	usuario
logs	tarefa
logs	data
logs	hora
logs	ip
logs	ten_id
marcas	mrc_id
marcas	mrc_nome
marcas	mrc_descricao
marcas	mrc_status
marcas	mrc_data_cadastro
marcas	mrc_data_alteracao
marcas	mrc_usuario_cadastro
marcas	mrc_usuario_alteracao
marcas	ten_id
marcas_equipamentos	idmarcas
marcas_equipamentos	marca
marcas_equipamentos	cadastro
marcas_equipamentos	situacao
marcas_equipamentos	ten_id
migrations	version
municipios	mun_id
municipios	est_id
municipios	mun_nome
municipios	mun_ibge
municipios	mun_data_inclusao
municipios	mun_data_atualizacao
municipios	ten_id
ncms	ncm_id
ncms	ncm_codigo
ncms	ncm_descricao
ncms	data_inicio
ncms	data_fim
ncms	tipo_ato
ncms	numero_ato
ncms	ano_ato
ncms	created_at
ncms	updated_at
ncms	ten_id
nfecom_capa	nfc_id
nfecom_capa	cln_id
nfecom_capa	nfc_cuf
nfecom_capa	nfc_tipo_ambiente
nfecom_capa	nfc_mod
nfecom_capa	nfc_serie
nfecom_capa	nfc_nnf
nfecom_capa	nfc_cnf
nfecom_capa	nfc_cdv
nfecom_capa	nfc_dhemi
nfecom_capa	nfc_tp_emis
nfecom_capa	nfc_n_site_autoriz
nfecom_capa	nfc_c_mun_fg
nfecom_capa	nfc_fin_nfcom
nfecom_capa	nfc_tp_fat
nfecom_capa	nfc_ver_proc
nfecom_capa	nfc_cnpj_emit
nfecom_capa	nfc_ie_emit
nfecom_capa	nfc_crt_emit
nfecom_capa	nfc_x_nome_emit
nfecom_capa	nfc_x_fant_emit
nfecom_capa	nfc_x_lgr_emit
nfecom_capa	nfc_nro_emit
nfecom_capa	nfc_x_cpl_emit
nfecom_capa	nfc_x_bairro_emit
nfecom_capa	nfc_c_mun_emit
nfecom_capa	nfc_x_mun_emit
nfecom_capa	nfc_cep_emit
nfecom_capa	nfc_uf_emit
nfecom_capa	nfc_fone_emit
nfecom_capa	nfc_x_nome_dest
nfecom_capa	nfc_cnpj_dest
nfecom_capa	nfc_ind_ie_dest
nfecom_capa	nfc_x_lgr_dest
nfecom_capa	nfc_nro_dest
nfecom_capa	nfc_x_bairro_dest
nfecom_capa	nfc_c_mun_dest
nfecom_capa	nfc_x_mun_dest
nfecom_capa	nfc_x_cpl_dest
nfecom_capa	nfc_cep_dest
nfecom_capa	nfc_uf_dest
nfecom_capa	nfc_i_cod_assinante
nfecom_capa	nfc_tp_assinante
nfecom_capa	nfc_tp_serv_util
nfecom_capa	nfc_n_contrato
nfecom_capa	nfc_d_contrato_ini
nfecom_capa	nfc_d_contrato_fim
nfecom_capa	nfc_v_prod
nfecom_capa	nfc_v_bc_icms
nfecom_capa	nfc_v_icms
nfecom_capa	nfc_v_icms_deson
nfecom_capa	nfc_v_fcp
nfecom_capa	nfc_v_cofins
nfecom_capa	nfc_v_pis
nfecom_capa	nfc_v_fust
nfecom_capa	nfc_v_funtel
nfecom_capa	nfc_v_ret_pis
nfecom_capa	nfc_v_ret_cofins
nfecom_capa	nfc_v_ret_csll
nfecom_capa	nfc_v_irrf
nfecom_capa	nfc_v_ret_trib_tot
nfecom_capa	nfc_v_desc
nfecom_capa	nfc_v_outro
nfecom_capa	nfc_v_nf
nfecom_capa	nfc_compet_fat
nfecom_capa	nfc_d_venc_fat
nfecom_capa	nfc_d_per_uso_ini
nfecom_capa	nfc_d_per_uso_fim
nfecom_capa	nfc_cod_barras
nfecom_capa	nfc_inf_cpl
nfecom_capa	nfc_status
nfecom_capa	nfc_ch_nfcom
nfecom_capa	nfc_n_prot
nfecom_capa	nfc_dh_recbto
nfecom_capa	nfc_c_stat
nfecom_capa	nfc_x_motivo
nfecom_capa	nfc_dig_val
nfecom_capa	nfc_xml
nfecom_capa	nfc_data_cadastro
nfecom_capa	nfc_data_atualizacao
nfecom_capa	nfc_chave_pix
nfecom_capa	nfc_linha_digitavel
nfecom_capa	opc_id
nfecom_capa	nfc_n_prot_canc
nfecom_capa	ten_id
nfecom_itens	nfi_id
nfecom_itens	nfc_id
nfecom_itens	nfi_n_item
nfecom_itens	nfi_c_prod
nfecom_itens	nfi_x_prod
nfecom_itens	nfi_c_class
nfecom_itens	nfi_cfop
nfecom_itens	nfi_u_med
nfecom_itens	nfi_q_faturada
nfecom_itens	nfi_v_item
nfecom_itens	nfi_v_desc
nfecom_itens	nfi_v_outro
nfecom_itens	nfi_v_prod
nfecom_itens	nfi_cst_icms
nfecom_itens	nfi_csosn
nfecom_itens	nfi_v_bc_icms
nfecom_itens	nfi_p_icms
nfecom_itens	nfi_v_icms
nfecom_itens	nfi_v_icms_deson
nfecom_itens	nfi_mot_des_icms
nfecom_itens	nfi_v_bc_icms_st
nfecom_itens	nfi_p_icms_st
nfecom_itens	nfi_v_icms_st
nfecom_itens	nfi_v_bc_st_ret
nfecom_itens	nfi_v_icms_st_ret
nfecom_itens	nfi_p_st
nfecom_itens	nfi_v_icms_subst
nfecom_itens	nfi_v_bc_fcp
nfecom_itens	nfi_p_fcp
nfecom_itens	nfi_v_fcp
nfecom_itens	nfi_v_fcp_st
nfecom_itens	nfi_v_fcp_st_ret
nfecom_itens	nfi_cst_pis
nfecom_itens	nfi_v_bc_pis
nfecom_itens	nfi_p_pis
nfecom_itens	nfi_v_pis
nfecom_itens	nfi_cst_cofins
nfecom_itens	nfi_v_bc_cofins
nfecom_itens	nfi_p_cofins
nfecom_itens	nfi_v_cofins
nfecom_itens	nfi_v_bc_fust
nfecom_itens	nfi_p_fust
nfecom_itens	nfi_v_fust
nfecom_itens	nfi_v_bc_funtel
nfecom_itens	nfi_p_funtel
nfecom_itens	nfi_v_funtel
nfecom_itens	nfi_v_bc_irrf
nfecom_itens	nfi_v_irrf
nfecom_itens	nfi_data_cadastro
nfecom_itens	nfi_data_atualizacao
nfecom_itens	ten_id
nfe_certificates	id
nfe_certificates	certificado_digital
nfe_certificates	senha_certificado
nfe_certificates	data_validade
nfe_certificates	nome_certificado
nfe_certificates	created_at
nfe_certificates	updated_at
nfe_certificates	ten_id
nfe_documentos	id
nfe_documentos	nfe_id
nfe_documentos	tipo
nfe_documentos	justificativa
nfe_documentos	protocolo
nfe_documentos	data_evento
nfe_documentos	status
nfe_documentos	xml
nfe_documentos	created_at
nfe_documentos	updated_at
nfe_documentos	ten_id
nfe_emitidas	id
nfe_emitidas	venda_id
nfe_emitidas	entrada_id
nfe_emitidas	cliente_id
nfe_emitidas	modelo
nfe_emitidas	numero_nfe
nfe_emitidas	chave_nfe
nfe_emitidas	status
nfe_emitidas	xml
nfe_emitidas	xml_protocolo
nfe_emitidas	protocolo
nfe_emitidas	motivo
nfe_emitidas	chave_retorno_evento
nfe_emitidas	valor_total
nfe_emitidas	created_at
nfe_emitidas	updated_at
nfe_emitidas	ten_id
operacao_comercial	opc_id
operacao_comercial	opc_sigla
operacao_comercial	opc_nome
operacao_comercial	opc_natureza_operacao
operacao_comercial	opc_tipo_movimento
operacao_comercial	opc_afeta_custo
operacao_comercial	opc_fato_fiscal
operacao_comercial	opc_gera_financeiro
operacao_comercial	opc_movimenta_estoque
operacao_comercial	opc_situacao
operacao_comercial	opc_finalidade_nfe
operacao_comercial	opc_data_inclusao
operacao_comercial	opc_data_alteracao
operacao_comercial	ten_id
operacao_comercial_old	id
operacao_comercial_old	created_at
operacao_comercial_old	updated_at
operacao_comercial_old	ten_id
ordem_servico	orv_id
ordem_servico	orv_data_inicial
ordem_servico	orv_data_final
ordem_servico	orv_garantia
ordem_servico	orv_descricao_produto
ordem_servico	orv_defeito
ordem_servico	orv_status
ordem_servico	orv_observacoes
ordem_servico	orv_laudo_tecnico
ordem_servico	orv_valor_total
ordem_servico	orv_desconto
ordem_servico	orv_valor_desconto
ordem_servico	orv_tipo_desconto
ordem_servico	orv_pess_id
ordem_servico	orv_usuarios_id
ordem_servico	orv_lancamento
ordem_servico	orv_faturado
ordem_servico	orv_garantias_id
ordem_servico	orv_opc_id
ordem_servico	ten_id
os	idos
os	datainicial
os	datafinal
os	garantia
os	descricaoproduto
os	defeito
os	status
os	observacoes
os	laudotecnico
os	valortotal
os	clientes_id
os	usuarios_id
os	lancamento
os	faturado
os	garantias_id
os	desconto
os	valor_desconto
os	tipo_desconto
os	ten_id
pedidos	pds_id
pedidos	pds_data
pedidos	pds_valor_total
pedidos	pds_desconto
pedidos	pds_valor_desconto
pedidos	pds_tipo_desconto
pedidos	pds_faturado
pedidos	pds_observacoes
pedidos	pds_observacoes_cliente
pedidos	pds_status
pedidos	pds_garantia
pedidos	pds_tipo
pedidos	pds_operacao_comercial
pedidos	pes_id
pedidos	cln_id
pedidos	usu_id
pedidos	lan_id
pedidos	ten_id
pedidos_compra	idpedido
pedidos_compra	data_pedido
pedidos_compra	fornecedor_id
pedidos_compra	usuario_id
pedidos_compra	status
pedidos_compra	observacoes
pedidos_compra	valor_total
pedidos_compra	data_aprovacao
pedidos_compra	ten_id
permissoes	idpermissao
permissoes	nome
permissoes	permissoes
permissoes	situacao
permissoes	data
permissoes	vempresa
permissoes	eempresa
permissoes	cempresa
permissoes	vclassificacaofiscal
permissoes	ten_id
pessoas	pes_id
pessoas	pes_cpfcnpj
pessoas	pes_nome
pessoas	pes_razao_social
pessoas	pes_codigo
pessoas	pes_fisico_juridico
pessoas	pes_nascimento_abertura
pessoas	pes_nacionalidades
pessoas	pes_rg
pessoas	pes_orgao_expedidor
pessoas	pes_sexo
pessoas	pes_estado_civil
pessoas	pes_escolaridade
pessoas	pes_profissao
pessoas	pes_observacao
pessoas	pes_situacao
pessoas	pes_data_inclusao
pessoas	pes_data_atualizacao
pessoas	ten_id
produtos	pro_id
produtos	pro_cod_barra
produtos	pro_descricao
produtos	pro_unid_medida
produtos	pro_ncm
produtos	ncm_id
produtos	mrc_id
produtos	pro_peso_bruto
produtos	pro_peso_liquido
produtos	pro_largura
produtos	pro_altura
produtos	pro_comprimento
produtos	tbp_id
produtos	pro_preco_compra
produtos	pro_preco_venda
produtos	pro_estoque
produtos	pro_origem
produtos	pro_estoque_minimo
produtos	pro_saida
produtos	pro_entrada
produtos	pro_tipo
produtos	pro_finalidade
produtos	pro_cclass_serv
produtos	ten_id
produtos_movimentados	pdm_id
produtos_movimentados	pdm_qtde
produtos_movimentados	pdm_tipo
produtos_movimentados	itf_id
produtos_movimentados	pdm_data
produtos_os	pro_os_id
produtos_os	pro_os_quantidade
produtos_os	pro_os_descricao
produtos_os	pro_os_preco
produtos_os	os_id
produtos_os	pro_id
produtos_os	pro_os_subtotal
produtos_os	ten_id
protocolos	prt_id
protocolos	nfc_id
protocolos	prt_numero_protocolo
protocolos	prt_tipo
protocolos	prt_motivo
protocolos	prt_data
protocolos	ten_id
resets_de_senha	id
resets_de_senha	email
resets_de_senha	token
resets_de_senha	data_expiracao
resets_de_senha	token_utilizado
resets_de_senha	ten_id
servicos	srv_id
servicos	srv_nome
servicos	srv_descricao
servicos	srv_codigo
servicos	srv_cclass
servicos	srv_unid_medida
servicos	srv_preco
servicos	ten_id
servicos_os	sos_id
servicos_os	servico
servicos_os	sos_quantidade
servicos_os	sos_preco
servicos_os	os_id
servicos_os	pro_id
servicos_os	sos_subtotal
servicos_os	ten_id
telefones	tel_id
telefones	pes_id
telefones	tel_tipo
telefones	tel_ddd
telefones	tel_numero
telefones	tel_observacao
telefones	tel_data_inclusao
telefones	tel_data_atualizacao
telefones	ten_id
tenants	ten_id
tenants	ten_nome
tenants	ten_cnpj
tenants	ten_email
tenants	ten_telefone
tenants	ten_data_cadastro
tenant_permissoes_menu	tpm_id
tenant_permissoes_menu	tpm_ten_id
tenant_permissoes_menu	tpm_menu_codigo
tenant_permissoes_menu	tpm_permissao
tenant_permissoes_menu	tpm_ativo
tenant_permissoes_menu	tpm_data_cadastro
tipos_clientes	tpc_id
tipos_clientes	tpc_nome
tipos_clientes	tpc_codigo_cliente
tipos_clientes	tpc_data_cadastro
tipos_clientes	ten_id
tipos_pessoa	id
tipos_pessoa	nome
tipos_pessoa	descricao
tipos_pessoa	ativo
tipos_pessoa	created_at
tipos_pessoa	updated_at
tipos_pessoa	ten_id
tributacao_estadual	tbe_id
tributacao_estadual	ncm_id
tributacao_estadual	tbe_uf
tributacao_estadual	tbe_tipo_tributacao
tributacao_estadual	tbe_aliquota_icms
tributacao_estadual	tbe_mva
tributacao_estadual	tbe_aliquota_icms_st
tributacao_estadual	tbe_percentual_reducao_icms
tributacao_estadual	tbe_percentual_reducao_st
tributacao_estadual	tbe_aliquota_fcp
tributacao_estadual	tbe_data_cadastro
tributacao_estadual	tbe_data_alteracao
tributacao_estadual	ten_id
tributacao_federal	tbf_id
tributacao_federal	ncm_id
tributacao_federal	tbf_cst_ipi_entrada
tributacao_federal	tbf_aliquota_ipi_entrada
tributacao_federal	tbf_cst_ipi_saida
tributacao_federal	tbf_aliquota_ipi_saida
tributacao_federal	tbf_cst_pis_cofins_entrada
tributacao_federal	tbf_aliquota_pis_entrada
tributacao_federal	tbf_aliquota_cofins_entrada
tributacao_federal	tbf_cst_pis_cofins_saida
tributacao_federal	tbf_aliquota_pis_saida
tributacao_federal	tbf_aliquota_cofins_saida
tributacao_federal	tbf_aliquota_ii
tributacao_federal	tbf_data_cadastro
tributacao_federal	tbf_data_alteracao
tributacao_federal	ten_id
tributacao_produto	tbp_id
tributacao_produto	tbp_descricao
tributacao_produto	tbp_cst_ipi_saida
tributacao_produto	aliq_ipi_saida
tributacao_produto	cst_pis_saida
tributacao_produto	aliq_pis_saida
tributacao_produto	cst_cofins_saida
tributacao_produto	aliq_cofins_saida
tributacao_produto	regime_fiscal_tributario
tributacao_produto	aliq_red_icms
tributacao_produto	aliq_iva
tributacao_produto	aliq_rd_icms_st
tributacao_produto	created_at
tributacao_produto	updated_at
tributacao_produto	ten_id
usuarios	idusuarios
usuarios	nome
usuarios	rg
usuarios	cpf
usuarios	rua
usuarios	numero
usuarios	bairro
usuarios	cidade
usuarios	estado
usuarios	email
usuarios	senha
usuarios	telefone
usuarios	celular
usuarios	situacao
usuarios	datacadastro
usuarios	permissoes_id
usuarios	dataexpiracao
usuarios	url_image_user
usuarios	cep
usuarios	ten_id
usuarios_super	uss_id
usuarios_super	uss_nome
usuarios_super	uss_rg
usuarios_super	uss_cpf
usuarios_super	uss_email
usuarios_super	uss_senha
usuarios_super	uss_telefone
usuarios_super	uss_celular
usuarios_super	uss_situacao
usuarios_super	uss_data_cadastro
usuarios_super	uss_data_expiracao
usuarios_super	uss_url_image_user
vendas	idvendas
vendas	datavenda
vendas	valortotal
vendas	desconto
vendas	valor_desconto
vendas	tipo_desconto
vendas	faturado
vendas	clientes_id
vendas	operacao_comercial_id
vendas	usuarios_id
vendas	lancamentos_id
vendas	status
vendas	emitida_nfe
vendas	garantia
vendas	observacoes
vendas	observacoes_cliente
vendas	ten_id
vendedores	ven_id
vendedores	pes_id
vendedores	ven_percentual_comissao
vendedores	ven_tipo_comissao
vendedores	ven_meta_mensal
vendedores	ven_situacao
vendedores	ven_data_cadastro
vendedores	ven_data_atualizacao
vendedores	ten_id
DATA;

// Processar lista de colunas
$columns = [];
$lines = explode("\n", trim($columns_data));
foreach ($lines as $line) {
    $parts = explode("\t", trim($line));
    if (count($parts) === 2) {
        $table = trim($parts[0]);
        $column = trim($parts[1]);
        if (!empty($column)) {
            $columns[] = $column;
        }
    }
}

// Remover duplicatas e ordenar por tamanho (maior primeiro para evitar substituições parciais)
$columns = array_unique($columns);
usort($columns, function($a, $b) {
    return strlen($b) - strlen($a);
});

// Gerar todas as substituições possíveis
// Usar array associativo para strtr() que é muito mais rápido
$direct_replacements = [];

foreach ($columns as $column) {
    $column_upper = strtoupper($column);
    
    // Se a coluna já está em minúsculas, criar substituições para versões em maiúsculas
    if ($column !== $column_upper) {
        // Em backticks SQL
        $direct_replacements["`{$column_upper}`"] = "`{$column}`";
        
        // Em strings SQL com aspas simples
        $direct_replacements["'{$column_upper}'"] = "'{$column}'";
        
        // Em strings SQL com aspas duplas
        $direct_replacements["\"{$column_upper}\""] = "\"{$column}\"";
        
        // Em propriedades de objeto
        $direct_replacements["->{$column_upper}"] = "->{$column}";
        
        // Em arrays com aspas simples
        $direct_replacements["['{$column_upper}']"] = "['{$column}']";
        
        // Em arrays com aspas duplas
        $direct_replacements["[\"{$column_upper}\"]"] = "[\"{$column}\"]";
        
        // Em arrays associativos (chave => valor)
        $direct_replacements["'{$column_upper}' =>"] = "'{$column}' =>";
        $direct_replacements["\"{$column_upper}\" =>"] = "\"{$column}\" =>";
        $direct_replacements["{$column_upper} =>"] = "{$column} =>";
    }
}

// Padrões regex para colunas que podem estar em maiúsculas
$column_patterns = [
    // Padrão geral: COLUNA_MAIUSCULA -> coluna_maiuscula (apenas se não for palavra reservada)
    '/\b([A-Z]{2,}_[A-Z_]+)\b/' => function($matches) use ($columns) {
        $match = strtolower($matches[1]);
        // Verificar se é uma coluna conhecida
        if (in_array($match, $columns)) {
            return $match;
        }
        return $matches[1]; // Manter original se não for coluna conhecida
    },
];

if ($is_web) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Correção Completa de Colunas</title>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .warning { background-color: #ffeb3b; padding: 10px; margin: 10px 0; }
        .success { background-color: #4CAF50; color: white; padding: 10px; margin: 10px 0; }
        .info { background-color: #2196F3; color: white; padding: 10px; margin: 10px 0; }
        pre { background-color: #f5f5f5; padding: 10px; overflow-x: auto; }
        ul { max-height: 400px; overflow-y: auto; }
    </style></head><body>";
    echo "<h1>Correção Completa de Referências de Colunas</h1>";
    echo "<div class='info'>";
    echo "<strong>📊 Estatísticas:</strong><br>";
    echo "Total de colunas processadas: " . count($columns) . "<br>";
    echo "Total de substituições geradas: " . count($direct_replacements) . "<br>";
    echo "</div>";
    echo "<div class='warning'>";
    echo "<strong>⚠️ MODO DE VISUALIZAÇÃO</strong><br>";
    echo "Este script está em modo de visualização. Para realmente fazer as substituições, execute via linha de comando:<br>";
    echo "<code>php fix_uppercase_columns_complete.php</code>";
    echo "</div>";
}

$files_updated = [];
$total_replacements = 0;
$replacements_by_file = [];

// Buscar arquivos PHP
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($codebase_path),
    RecursiveIteratorIterator::SELF_FIRST
);

// Contador de arquivos processados
$file_count = 0;
$total_files = 0;

// Contar arquivos primeiro (para progresso)
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $total_files++;
    }
}

// Resetar iterator
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($codebase_path),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $file_count++;
        $file_path = $file->getPathname();
        
        if (!$is_web && $file_count % 10 == 0) {
            echo "Processando arquivo $file_count de $total_files...\n";
        }
        
        $content = file_get_contents($file_path);
        $original = $content;
        $file_replacements = 0;
        
        // Usar strtr() que é muito mais rápido para múltiplas substituições
        // Aplicar todas as substituições de uma vez
        $content = strtr($content, $direct_replacements);
        
        // Contar substituições feitas (apenas se houve mudança)
        if ($content !== $original) {
            // Contar apenas as substituições que realmente ocorreram
            // Usar uma abordagem mais eficiente: comparar tamanhos ou usar diff
            foreach ($direct_replacements as $old => $new) {
                $count = substr_count($original, $old);
                if ($count > 0) {
                    $file_replacements += $count;
                    $total_replacements += $count;
                }
            }
        }
        
        // Aplicar padrões regex (mais cuidadoso)
        foreach ($column_patterns as $pattern => $callback) {
            $new_content = preg_replace_callback($pattern, $callback, $content);
            if ($new_content !== $content) {
                $content = $new_content;
            }
        }
        
        // Se houve mudanças, salvar
        if ($content !== $original) {
            if ($is_web) {
                $files_updated[] = [
                    'file' => str_replace(__DIR__ . '\\', '', $file_path),
                    'replacements' => $file_replacements
                ];
            } else {
                file_put_contents($file_path, $content);
                $files_updated[] = [
                    'file' => str_replace(__DIR__ . '\\', '', $file_path),
                    'replacements' => $file_replacements
                ];
                echo "✓ Atualizado: " . str_replace(__DIR__ . '\\', '', $file_path) . " ({$file_replacements} substituições)\n";
            }
        }
    }
}

if ($is_web) {
    echo "<h2>Arquivos que seriam atualizados: " . count($files_updated) . "</h2>";
    echo "<ul>";
    foreach ($files_updated as $item) {
        echo "<li>{$item['file']} <strong>({$item['replacements']} substituições)</strong></li>";
    }
    echo "</ul>";
    
    echo "<div class='success'>";
    echo "<strong>Total de substituições:</strong> $total_replacements";
    echo "</div>";
    
    echo "<p><strong>⚠️ IMPORTANTE:</strong> Faça backup do código antes de executar em modo CLI!</p>";
    echo "</body></html>";
} else {
    echo "\nConcluído!\n";
    echo "Arquivos atualizados: " . count($files_updated) . "\n";
    echo "Total de substituições: $total_replacements\n";
}
?>
