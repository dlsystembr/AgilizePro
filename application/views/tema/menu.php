<!--sidebar-menu-->
<nav id="sidebar">
    <div id="newlog">
        <div class="icon2">
            <img src="<?php echo base_url() ?>assets/img/logo-two.png">
        </div>
        <div class="title1">
            <?= (isset($configuration['app_theme']) && ($configuration['app_theme'] == 'white' || $configuration['app_theme'] == 'whitegreen')) ? '<img src="' . base_url() . 'assets/img/logo-mapos.png">' : '<img src="' . base_url() . 'assets/img/logo-mapos-branco.png">'; ?>
        </div>
    </div>
    <!-- Debug Permissions -->
    <?php
    $user_permissions = $this->session->userdata('permissao');
    echo "<!-- User Permissions: " . print_r($user_permissions, true) . " -->";
    ?>
    <!-- End Debug -->
    <a href="#" class="visible-phone">
        <div class="mode">
            <div class="moon-menu">
                <i class='bx bx-chevron-right iconX open-2'></i>
                <i class='bx bx-chevron-left iconX close-2'></i>
            </div>
        </div>
    </a>
    <!-- Start Pesquisar-->
    <li class="search-box">
        <form style="display: flex" action="<?= site_url('mapos/pesquisar') ?>">
            <button style="background:transparent;border:transparent" type="submit" class="tip-bottom" title="">
                <i class='bx bx-search iconX'></i></button>
            <input
                style="background:transparent;<?= (isset($configuration['app_theme']) && $configuration['app_theme'] == 'white') ? 'color:#313030;' : 'color:#fff;' ?>border:transparent"
                type="search" name="termo" placeholder="Pesquise aqui...">
            <span class="title-tooltip">Pesquisar</span>
        </form>
    </li>
    <!-- End Pesquisar-->

    <div class="menu-bar">
        <div class="menu">

            <ul class="menu-links" style="position: relative;">
                <li class="<?php if (isset($menuPainel)) {
                    echo 'active';
                }
                ; ?>">
                    <a class="tip-bottom" title="" href="<?= base_url() ?>"><i class='bx bx-home-alt iconX'></i>
                        <span class="title nav-title">Home</span>
                        <span class="title-tooltip">Início</span>
                    </a>
                </li>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPessoa')) { ?>
                    <li class="<?php if (isset($menuPessoas)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('pessoas') ?>"><i class='bx bx-group iconX'></i>
                            <span class="title">Pessoas</span>
                            <span class="title-tooltip">Pessoas</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vContrato')) { ?>
                    <li class="<?php if (isset($menuContratos)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('contratos') ?>"><i class='bx bx-file-blank iconX'></i>
                            <span class="title">Contratos</span>
                            <span class="title-tooltip">Contratos</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
                    <li class="<?php if (isset($menuProdutos)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('produtos') ?>"><i class='bx bx-basket iconX'></i>
                            <span class="title">Produtos / Serviços</span>
                            <span class="title-tooltip">Produtos / Serviços</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) { ?>
                    <li class="<?php if (isset($menuVendas)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('vendas') ?>"><i
                                class='bx bx-cart-alt iconX'></i></span>
                            <span class="title">Vendas</span>
                            <span class="title-tooltip">Vendas</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                    <li class="<?php if (isset($menuOs)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('os') ?>"><i class='bx bx-file iconX'></i>
                            <span class="title">Ordens de Serviço</span>
                            <span class="title-tooltip">Ordens</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturamentoEntrada')) { ?>
                    <li class="<?php if (isset($menuFaturamentoEntrada)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('faturamento_entrada') ?>"><i class='bx bx-receipt iconX'></i>
                            <span class="title">Faturamento Entrada</span>
                            <span class="title-tooltip">Faturamento Entrada</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) { ?>
                    <li class="<?php if (isset($menuNfe)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('nfe') ?>"><i class='bx bx-file-blank iconX'></i>
                            <span class="title">Emissor de Notas</span>
                            <span class="title-tooltip">NFE</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) { ?>
                    <li class="<?php if (isset($menuNfecom)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('nfecom') ?>"><i class='bx bx-notepad iconX'></i>
                            <span class="title">NFCOM</span>
                            <span class="title-tooltip">NFCOM</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                    <li class="<?php if (isset($menuLancamentos)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('financeiro/lancamentos') ?>"><i
                                class="bx bx-bar-chart-alt-2 iconX"></i>
                            <span class="title">Lançamentos</span>
                            <span class="title-tooltip">Lançamentos</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vGarantia')) { ?>
                    <li class="<?php if (isset($menuGarantias)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('garantias') ?>"><i class='bx bx-certification iconX'></i>
                            <span class="title">Garantias</span>
                            <span class="title-tooltip">Garantias</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuario')) { ?>
                    <li class="<?php if (isset($menuUsuarios)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('usuarios') ?>"><i class='bx bx-user iconX'></i>
                            <span class="title">Usuários</span>
                            <span class="title-tooltip">Usuários</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPermissao')) { ?>
                    <li class="<?php if (isset($menuPermissoes)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('permissoes') ?>"><i class='bx bx-shield iconX'></i>
                            <span class="title">Permissões</span>
                            <span class="title-tooltip">Permissões</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vConfiguracao')) { ?>
                    <li class="<?php if (isset($menuConfiguracoes)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('mapos/configuracoes') ?>"><i class='bx bx-cog iconX'></i>
                            <span class="title">Configurações</span>
                            <span class="title-tooltip">Configurações</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vAuditoria')) { ?>
                    <li class="<?php if (isset($menuAuditoria)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('auditoria') ?>"><i class='bx bx-history iconX'></i>
                            <span class="title">Auditoria</span>
                            <span class="title-tooltip">Auditoria</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
                    <li class="<?php if (isset($menuArquivos)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('arquivos') ?>"><i class='bx bx-folder iconX'></i>
                            <span class="title">Arquivos</span>
                            <span class="title-tooltip">Arquivos</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vBackup')) { ?>
                    <li class="<?php if (isset($menuBackup)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('mapos/backup') ?>"><i class='bx bx-data iconX'></i>
                            <span class="title">Backup</span>
                            <span class="title-tooltip">Backup</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vEmitente')) { ?>
                    <li class="<?php if (isset($menuEmitente)) {
                        echo 'active';
                    }
                    ; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('mapos/emitente') ?>"><i class='bx bx-building iconX'></i>
                            <span class="title">Emitente</span>
                            <span class="title-tooltip">Emitente</span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="botton-content">
            <li class="">
                <a class="tip-bottom" title="" href="<?= site_url('login/sair'); ?>">
                    <i class='bx bx-log-out-circle iconX'></i>
                    <span class="title">Sair</span>
                    <span class="title-tooltip">Sair</span>
                </a>
            </li>
        </div>
    </div>
</nav>
<!--End sidebar-menu-->
