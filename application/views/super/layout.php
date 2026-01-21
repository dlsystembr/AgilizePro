<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Painel Super - Map-OS</title>
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
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery-1.12.4.min.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/maskmoney.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/sweetalert.min.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>
  <style>
    .super-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 15px 0;
      margin-bottom: 20px;
    }
    .super-menu {
      background: #f8f9fa;
      padding: 10px 0;
      margin-bottom: 20px;
    }
    .super-menu a {
      margin: 0 15px;
      color: #333;
      text-decoration: none;
      font-weight: 500;
    }
    .super-menu a:hover {
      color: #667eea;
    }
  </style>
</head>
<body>
  <div class="super-header">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12">
          <h2><i class="icon-cog"></i> Painel Super Administrador</h2>
          <p>Bem-vindo, <?= $this->session->userdata('nome_admin') ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="super-menu">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12">
          <a href="<?= base_url('index.php/super') ?>"><i class="icon-home"></i> Dashboard</a>
          <a href="<?= base_url('index.php/super/tenants') ?>"><i class="icon-building"></i> Tenants</a>
          <a href="<?= base_url('index.php/super/superUsuarios') ?>"><i class="icon-user"></i> Super Usuários</a>
          <a href="<?= base_url('index.php/login/sair') ?>" style="float: right;"><i class="icon-off"></i> Sair</a>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <?php if ($this->session->flashdata('success')): ?>
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('success') ?>
          </div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('error')): ?>
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('error') ?>
          </div>
        <?php endif; ?>

        <?php if (isset($view)): ?>
          <?php $this->load->view($view, isset($data) ? $data : []); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="<?= base_url(); ?>assets/js/bootstrap.min.js"></script>
</body>
</html>

