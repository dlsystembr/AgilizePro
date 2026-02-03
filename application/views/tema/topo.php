<!DOCTYPE html>
<html lang="pt-br">

<head>
  <title><?= isset($configuration['app_name']) ? $configuration['app_name'] : 'AgilizePro' ?></title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token-name" content="<?= config_item("csrf_token_name") ?>">
  <meta name="csrf-cookie-name" content="<?= config_item("csrf_cookie_name") ?>">
  <link rel="shortcut icon" type="image/png" href="<?= base_url(); ?>assets/img/favicon.png" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/matrix-style.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/matrix-media.css" />
  <link href="<?= base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/fullcalendar.css" />
  <?php if (isset($configuration['app_theme']) && $configuration['app_theme'] == 'white') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-white.css" />
  <?php } ?>
  <?php if (isset($configuration['app_theme']) && $configuration['app_theme'] == 'puredark') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-pure-dark.css" />
  <?php } ?>
  <?php if (isset($configuration['app_theme']) && $configuration['app_theme'] == 'darkviolet') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-dark-violet.css" />
  <?php } ?>
  <?php if (isset($configuration['app_theme']) && $configuration['app_theme'] == 'darkorange') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-dark-orange.css" />
  <?php } ?>
  <?php if (isset($configuration['app_theme']) && $configuration['app_theme'] == 'whitegreen') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-white-green.css" />
  <?php } ?>
  <?php if (isset($configuration['app_theme']) && $configuration['app_theme'] == 'whiteblack') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-white-black.css" />
  <?php } ?>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;500;700&display=swap'
    rel='stylesheet' type='text/css'>
  <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/menu-scroll-fix.css" />
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery-1.12.4.min.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/maskmoney.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/shortcut.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/datatables.min.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/sweetalert.min.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery.validate.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script type="text/javascript">
    // Permissões do usuário
    var userPermissions = {
      vCliente: <?= $this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente') ? 'true' : 'false' ?>,
      vProduto: <?= $this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto') ? 'true' : 'false' ?>,
      vServico: <?= $this->permission->checkPermission($this->session->userdata('permissao'), 'vServico') ? 'true' : 'false' ?>,
      vOs: <?= $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs') ? 'true' : 'false' ?>,
      vVenda: <?= $this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda') ? 'true' : 'false' ?>,
      aVenda: <?= $this->permission->checkPermission($this->session->userdata('permissao'), 'aVenda') ? 'true' : 'false' ?>,
      vLancamento: <?= $this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento') ? 'true' : 'false' ?>
    };

    // F1 - Clientes
    shortcut.add("F1", function () {
      if (userPermissions.vCliente) {
        location.href = '<?= site_url('clientes'); ?>';
      } else {
        console.log('Sem permissão para acessar Clientes');
      }
    });

    // F2 - Produtos
    shortcut.add("F2", function () {
      if (userPermissions.vProduto) {
        location.href = '<?= site_url('produtos'); ?>';
      } else {
        console.log('Sem permissão para acessar Produtos');
      }
    });

    // F3 - Serviços
    shortcut.add("F3", function () {
      if (userPermissions.vServico) {
        location.href = '<?= site_url('servicos'); ?>';
      } else {
        console.log('Sem permissão para acessar Serviços');
      }
    });

    // F4 - Ordens de Serviço
    shortcut.add("F4", function () {
      if (userPermissions.vOs) {
        location.href = '<?= site_url('os'); ?>';
      } else {
        console.log('Sem permissão para acessar Ordens de Serviço');
      }
    });

    //shortcut.add("F5", function() {});

    // F6 - Adicionar Venda
    shortcut.add("F6", function () {
      if (userPermissions.aVenda) {
        location.href = '<?= site_url('vendas/adicionar'); ?>';
      } else {
        console.log('Sem permissão para adicionar Vendas');
      }
    });

    // F7 - Lançamentos Financeiros
    shortcut.add("F7", function () {
      if (userPermissions.vLancamento) {
        location.href = '<?= site_url('financeiro/lancamentos'); ?>';
      } else {
        console.log('Sem permissão para acessar Lançamentos');
      }
    });

    shortcut.add("F8", function () { });
    shortcut.add("F9", function () { });
    shortcut.add("F10", function () { });
    //shortcut.add("F11", function() {});
    shortcut.add("F12", function () { });
    window.BaseUrl = "<?= base_url() ?>";
  </script>
</head>

<body>
  <!--top-Header-menu-->
  <div class="navebarn">
    <div id="user-nav" class="navbar navbar-inverse">
      <ul class="nav">
        <li class="dropdown">
          <a href="#" class="tip-right dropdown-toggle" data-toggle="dropdown" title="Perfis"><i
              class='bx bx-user-circle iconN'></i><span class="text"></span></a>
          <ul class="dropdown-menu">
            <li class=""><a title="Área do Cliente" href="<?= site_url(); ?>/mine" target="_blank"> <span
                  class="text">Área do Cliente</span></a></li>
            <li class=""><a title="Meu Perfil" href="<?= site_url('mapos/minhaConta'); ?>"><span class="text">Meu
                  Perfil</span></a></li>
            <li class="divider"></li>
            <li class=""><a title="Sair do Sistema" href="<?= site_url('login/sair'); ?>"><i
                  class='bx bx-log-out-circle'></i> <span class="text">Sair do Sistema</span></a></li>
          </ul>
        </li>
        <?php
        // Verificar se o usuário tem pelo menos uma permissão de relatório
        $temPermissaoRelatorio = $this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente') ||
          $this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto') ||
          $this->permission->checkPermission($this->session->userdata('permissao'), 'rServico') ||
          $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs') ||
          $this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda') ||
          $this->permission->checkPermission($this->session->userdata('permissao'), 'rContrato') ||
          $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro') ||
          $this->permission->checkPermission($this->session->userdata('permissao'), 'rNfe');
        if ($temPermissaoRelatorio) { ?>
          <li class="dropdown">
            <a href="#" class="tip-right dropdown-toggle" data-toggle="dropdown" title="Relatórios"><i
                class='bx bx-pie-chart-alt-2 iconN'></i><span class="text"></span></a>
            <ul class="dropdown-menu">
              <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente')) { ?>
                <li><a href="<?= site_url('relatorios/clientes') ?>">Clientes</a></li>
              <?php } ?>
              <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto')) { ?>
                <li><a href="<?= site_url('relatorios/produtos') ?>">Produtos</a></li>
              <?php } ?>
              <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rServico')) { ?>
                <li><a href="<?= site_url('relatorios/servicos') ?>">Serviços</a></li>
              <?php } ?>
              <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rOs')) { ?>
                <li><a href="<?= site_url('relatorios/os') ?>">Ordens de Serviço</a></li>
              <?php } ?>
              <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')) { ?>
                <li><a href="<?= site_url('relatorios/vendas') ?>">Vendas</a></li>
              <?php } ?>
              <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rContrato')) { ?>
                <li><a href="<?= site_url('relatorios/contratos') ?>">Contratos</a></li>
              <?php } ?>
              <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) { ?>
                <li><a href="<?= site_url('relatorios/financeiro') ?>">Financeiro</a></li>
              <?php } ?>
              <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda') && $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs')) { ?>
                <li><a href="<?= site_url('relatorios/sku') ?>">SKU</a></li>
              <?php } ?>
              <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) { ?>
                <li><a href="<?= site_url('relatorios/receitasBrutasMei') ?>">Receitas Brutas - MEI</a></li>
              <?php } ?>
              <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rNfe')) { ?>
                <li><a href="<?= site_url('relatorios/nfe_emitidas') ?>">Relatório NFe emitidas</a></li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>
        <li class="dropdown">
          <a href="#" class="tip-right dropdown-toggle" data-toggle="dropdown" title="Tributação"><i
              class='bx bx-calculator iconN'></i><span class="text"></span></a>
          <ul class="dropdown-menu">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
              <li><a href="<?= site_url('simuladortributacao') ?>">Simulador de Tributação</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTributacaoProduto')) { ?>
              <li><a href="<?= site_url('tributacaoproduto') ?>">Tributação Produto</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vClassificacaoFiscal')) { ?>
              <li><a href="<?= site_url('classificacaofiscal') ?>">Classificação Fiscal</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOperacaoComercial')) { ?>
              <li><a href="<?= site_url('operacaocomercial') ?>">Operação Comercial</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vAliquota')) { ?>
              <li><a href="<?= site_url('aliquotas') ?>">Alíquotas</a></li>
            <?php } ?>
            <?php
            $permissao = $this->session->userdata('permissao');
            if ($this->permission->checkPermission($permissao, 'vNcm') === true) {
              ?>
              <li><a href="<?= site_url('ncms') ?>">NCMs</a></li>
            <?php } ?>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="tip-right dropdown-toggle" data-toggle="dropdown" title="Configurações"><i
              class='bx bx-cog iconN'></i><span class="text"></span></a>
          <ul class="dropdown-menu">
            <li><a href="<?= site_url('mapos/configurar') ?>">Sistema</a></li>
            <li><a href="<?= site_url('usuarios') ?>">Usuários</a></li>
            <li><a href="<?= site_url('mapos/emitente') ?>">Emitente</a></li>
            <li><a href="<?= site_url('permissoes') ?>">Permissões</a></li>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vEmpresa')) { ?>
              <li><a href="<?= site_url('empresas') ?>">Empresas</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
              <li><a href="<?= site_url('certificados') ?>">Certificados</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vConfigFiscal')) { ?>
              <li><a href="<?= site_url('configuracoesfiscais') ?>">Configurações Fiscais</a></li>
            <?php } ?>
            <li><a href="<?= site_url('auditoria') ?>">Auditoria</a></li>
            <li><a href="<?= site_url('mapos/emails') ?>">Emails</a></li>
            <li><a href="<?= site_url('mapos/backup') ?>">Backup</a></li>
          </ul>
        </li>
      </ul>
    </div>

    <!-- New User -->
    <div id="userr"
      style="padding-right:45px;display:flex;flex-direction:column;align-items:flex-end;justify-content:center;">
      <div class="user-names userT0">
        <?php
        if (!function_exists('saudacao')) {
          function saudacao()
          {
            $hora = date('H');
            if ($hora >= 00 && $hora < 12) {
              return 'Bom dia, ';
            } elseif ($hora >= 12 && $hora < 18) {
              return 'Boa tarde, ';
            } else {
              return 'Boa noite, ';
            }
          }
        }
        $login = '';
        echo saudacao($login); // Irá retornar conforme o horário
        ?>
      </div>
      <div class="userT"><?= $this->session->userdata('nome_admin') ?></div>

      <section style="display:block;position:absolute;right:10px">
        <div class="profile">
          <div class="profile-img">
            <a href="<?= site_url('mapos/minhaConta'); ?>"><img
                src="<?= !is_file(FCPATH . "assets/userImage/" . $this->session->userdata('url_image_user_admin')) ? base_url() . "assets/img/User.png" : base_url() . "assets/userImage/" . $this->session->userdata('url_image_user_admin') ?>"
                alt=""></a>
          </div>
        </div>
      </section>

    </div>
  </div>
  <!-- End User -->

  <!--start-top-serch-->
  <div style="display: none" id="search">
    <form action="<?= site_url('mapos/pesquisar') ?>">
      <input type="text" name="termo" placeholder="Pesquisar..." />
      <button type="submit" class="tip-bottom" title="Pesquisar"><i class="fas fa-search fa-white"></i></button>
    </form>
  </div>
  <!--close-top-serch-->