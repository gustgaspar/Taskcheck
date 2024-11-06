<div class="sidebar position-fixed min-vh-100" id="sidebar">
    <?php if (!urlIs('/professor')): ?>
        <a href="" id="<?= $sideBar1id ?>"> <i class="fas fa-plus-circle"></i> <?= $sideBar1 ?></a>
    <?php endif; ?>
    <a href="" id="<?= $sideBar2id ?>"> <i class="fas fa-list"></i> <?= $sideBar2 ?></a>
    <a href="" id="<?= urlIs('/aluno') ? $sideBar3id : ''?>"> <i class="<?= urlIs('/aluno') ? 'fas fa-bars-progress' : 'fas fa-cog'?>"></i> <?= $sideBar3 ?></a>
    <a href="" id="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
    <a id="user-info" class="d-flex align-items-center mt-auto" href="">
        <i class="fas fa-user"></i>
        <span class="ms-2"><?= $_SESSION['nome']; ?></span>
        <div id="user-info-tooltip">
            Tipo: <?= $_SESSION['tipo']; ?>
        </div>
    </a>
</div>
