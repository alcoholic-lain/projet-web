<header>
    <div class="header-left">
        <button id="sidebarToggle">☰</button>
        <div class="header-text">
            <h1><?= isset($pageTitleIcon) ? $pageTitleIcon . ' ' : '' ?><?= $pageTitle ?? 'Espace Administrateur' ?></h1>
            <p><?= $pageSubtitle ?? 'Backoffice - Innovation DB' ?></p>
        </div>
    </div>
    <div class="header-right">
        <a href="../../../Client/index.php">Front Office</a>
    </div>
</header>
