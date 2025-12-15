<header>
    <div class="header-left">
        <button id="sidebarToggle">☰</button>
        <div class="header-text">
            <h1>
                <?= isset($pageTitleIcon) ? $pageTitleIcon . ' ' : '' ?>
                <?= $pageTitle ?? 'Espace Administrateur' ?>
            </h1>
            <p><?= $pageSubtitle ?? 'Backoffice ' ?></p>
        </div>
    </div>

    <div class="header-right">
        <a href="../../logout.php">LOG OUT</a>
    </div>
</header>
