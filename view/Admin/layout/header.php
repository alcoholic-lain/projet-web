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
        <!-- 🌙 / ☀️ SWITCH --- LE PLUS IMPORTANT -->
        <div class="theme-toggle">🌙</div>

        <a href="../../../Client/index.php">Home - Tunispace</a>
    </div>
</header>
