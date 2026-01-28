<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = 'mapos';
$route['404_override'] = '';

// Rota para Configurações Fiscais (garante compatibilidade case-sensitive)
$route['configuracoesfiscais'] = 'ConfiguracoesFiscais';
$route['configuracoesfiscais/(:any)'] = 'ConfiguracoesFiscais/$1';

// Rotas para Classificação Fiscal (garante compatibilidade case-sensitive - várias variações)
$route['classificacaofiscal'] = 'ClassificacaoFiscal';
$route['classificacaofiscal/(:any)'] = 'ClassificacaoFiscal/$1';
$route['classificacaofiscal/(:any)/(:any)'] = 'ClassificacaoFiscal/$1/$2';
$route['classificacaoFiscal'] = 'ClassificacaoFiscal';
$route['classificacaoFiscal/(:any)'] = 'ClassificacaoFiscal/$1';
$route['classificacaoFiscal/(:any)/(:any)'] = 'ClassificacaoFiscal/$1/$2';
$route['classiFicacaofiscal'] = 'ClassificacaoFiscal';
$route['classiFicacaofiscal/(:any)'] = 'ClassificacaoFiscal/$1';
$route['classiFicacaofiscal/(:any)/(:any)'] = 'ClassificacaoFiscal/$1/$2';

// Rotas para NFCom (garante compatibilidade case-sensitive)
$route['nfecom'] = 'Nfecom';
$route['nfecom/(:any)'] = 'Nfecom/$1';
$route['nfecom/(:any)/(:any)'] = 'Nfecom/$1/$2';
$route['nfecom/(:any)/(:any)/(:any)'] = 'Nfecom/$1/$2/$3';

// Rotas para Simulador de Tributação (garante compatibilidade case-sensitive)
$route['simuladortributacao'] = 'SimuladorTributacao';
$route['simuladortributacao/(:any)'] = 'SimuladorTributacao/$1';
$route['simuladortributacao/(:any)/(:any)'] = 'SimuladorTributacao/$1/$2';

// Rotas para API de Cálculo de Tributação (garante compatibilidade case-sensitive)
$route['calculotributacaoapi'] = 'CalculoTributacaoApi';
$route['calculotributacaoapi/(:any)'] = 'CalculoTributacaoApi/$1';
$route['calculotributacaoapi/(:any)/(:any)'] = 'CalculoTributacaoApi/$1/$2';

// Rotas da API
if (filter_var($_ENV['API_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
    require APPPATH . 'config/routes_api.php';
}

/* End of file routes.php */
/* Location: ./application/config/routes.php */
