<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * URLs do Web Service CadConsultaCadastro2 por UF (Consulta Cadastro de Contribuinte - IE/CNPJ).
 * Fonte: Manual da NFe / SEFAZ de cada estado. Para incluir mais UFs, consulte o manual da NFe.
 */
$config['consulta_cadastro_uf'] = [
    'AC' => 'https://nfe.sefaz.ac.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'AL' => 'https://nfe.sefaz.al.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'AM' => 'https://nfe.sefaz.am.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'AP' => 'https://nfe.sefaz.ap.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'BA' => 'https://nfe.sefaz.ba.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'CE' => 'https://nfe.sefaz.ce.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'DF' => 'https://nfe.sefaz.df.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'ES' => 'https://app.sefaz.es.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'GO' => 'https://nfe.sefaz.go.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'MA' => 'https://nfe.sefaz.ma.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'MG' => 'https://nfe.fazenda.mg.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'MS' => 'https://nfe.sefaz.ms.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'MT' => 'https://nfe.sefaz.mt.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'PA' => 'https://nfe.sefaz.pa.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'PB' => 'https://nfe.sefaz.pb.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'PE' => 'https://nfe.sefaz.pe.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'PI' => 'https://nfe.sefaz.pi.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'PR' => 'https://nfe.sefaz.pr.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'RJ' => 'https://nfe.fazenda.rj.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'RN' => 'https://nfe.set.rn.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'RO' => 'https://nfe.sefin.ro.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'RR' => 'https://nfe.sefaz.rr.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'RS' => 'https://cad.sefaz.rs.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'SC' => 'https://nfe.svrs.rs.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'SE' => 'https://nfe.sefaz.se.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'SP' => 'https://nfe.fazenda.sp.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
    'TO' => 'https://nfe.sefaz.to.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx',
];

/** Código numérico da UF (cUF) para o SOAP */
$config['consulta_cadastro_cuf'] = [
    'AC' => '12', 'AL' => '27', 'AM' => '13', 'AP' => '16', 'BA' => '29', 'CE' => '23', 'DF' => '53',
    'ES' => '32', 'GO' => '52', 'MA' => '21', 'MG' => '31', 'MS' => '50', 'MT' => '51', 'PA' => '15',
    'PB' => '25', 'PE' => '26', 'PI' => '22', 'PR' => '41', 'RJ' => '33', 'RN' => '24', 'RO' => '11',
    'RR' => '14', 'RS' => '43', 'SC' => '42', 'SE' => '28', 'SP' => '35', 'TO' => '17',
];
