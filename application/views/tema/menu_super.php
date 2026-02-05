<!--sidebar-menu - Super Admin (mesma estrutura do menu padrão)-->
<nav id="sidebar">
    <div id="newlog">
        <div class="icon2">
            <img src="<?= base_url() ?>assets/img/logo-two.png">
        </div>
        <div class="title1">
            <?= (isset($configuration['app_theme']) && ($configuration['app_theme'] == 'white' || $configuration['app_theme'] == 'whitegreen')) ? '<img src="' . base_url() . 'assets/img/logo-mapos.png">' : '<img src="' . base_url() . 'assets/img/logo-mapos-branco.png">'; ?>
        </div>
    </div>
    <a href="#" class="visible-phone">
        <div class="mode">
            <div class="moon-menu">
                <i class='bx bx-chevron-right iconX open-2'></i>
                <i class='bx bx-chevron-left iconX close-2'></i>
            </div>
        </div>
    </a>
    <li class="search-box" style="pointer-events: none; opacity: 0.7;">
        <form style="display: flex" action="<?= site_url('mapos/pesquisar') ?>">
            <button style="background:transparent;border:transparent" type="submit" class="tip-bottom" title=""><i class='bx bx-search iconX'></i></button>
            <input style="background:transparent;<?= (isset($configuration['app_theme']) && $configuration['app_theme'] == 'white') ? 'color:#313030;' : 'color:#fff;' ?>border:transparent" type="search" name="termo" placeholder="Super Admin">
            <span class="title-tooltip">Super Admin</span>
        </form>
    </li>

    <div class="menu-bar">
        <div class="menu">
            <ul class="menu-links" style="position: relative;">
                <li class="<?= ($this->uri->segment(2) == '' || $this->uri->segment(2) == 'index') ? 'active' : '' ?>">
                    <a class="tip-bottom" title="Dashboard" href="<?= base_url('index.php/super') ?>">
                        <i class='bx bx-home-alt iconX'></i>
                        <span class="title nav-title">Dashboard</span>
                        <span class="title-tooltip">Dashboard</span>
                    </a>
                </li>
                <li class="<?= in_array($this->uri->segment(2), ['gruposEmpresariais', 'empresas']) ? 'active' : '' ?>">
                    <a class="tip-bottom" title="Grupos Empresariais" href="<?= base_url('index.php/super/gruposEmpresariais') ?>">
                        <i class='bx bx-buildings iconX'></i>
                        <span class="title">Grupos Empresariais</span>
                        <span class="title-tooltip">Grupos Empresariais</span>
                    </a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'superUsuarios' ? 'active' : '' ?>">
                    <a class="tip-bottom" title="Super Usuários" href="<?= base_url('index.php/super/superUsuarios') ?>">
                        <i class='bx bx-user-check iconX'></i>
                        <span class="title">Super Usuários</span>
                        <span class="title-tooltip">Super Usuários</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="botton-content">
            <li class="">
                <a class="tip-bottom" title="Sair" href="<?= site_url('login/sair'); ?>">
                    <i class='bx bx-log-out-circle iconX'></i>
                    <span class="title">Sair</span>
                    <span class="title-tooltip">Sair</span>
                </a>
            </li>
        </div>
    </div>
</nav>
<!--End sidebar-menu-->
