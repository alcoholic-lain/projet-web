<?php
if (!isset($activeMenu)) $activeMenu = '';
if (!isset($activeSub)) $activeSub = '';
?>
<aside class="sidebar" id="sidebar">

    <div class="sidebar-top">
        <div class="user-info">
            <h4>Espace Administrateur</h4>
        </div>
    </div>

    <p class="menu-title">Navigation</p>

    <ul class="menu">
        <!-- Dashboard -->
        <li>
            <a href="../../index.php"
               class="menu-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>">
                <span class="icon-large">📊</span>
                <span class="text">Dashboard</span>
            </a>
        </li>

        <!-- Catégories -->
        <li class="menu-dropdown <?= $activeMenu === 'categories' ? 'open' : '' ?>">
            <a class="menu-link">
                <span class="icon-large">🗂️</span>
                <span class="text">Catégories</span>
                <i class="bi bi-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="../src/a_Category.php"
                       class="<?= $activeSub === 'categories_list' ? 'active-sub' : '' ?>">
                        Liste
                    </a>
                </li>
                <li>
                    <a href="../src/add_Category.php"
                       class="<?= $activeSub === 'categories_add' ? 'active-sub' : '' ?>">
                        Ajouter
                    </a>
                </li>
            </ul>
        </li>

        <!-- Innovations -->
        <li class="menu-dropdown <?= $activeMenu === 'innovations' ? 'open' : '' ?>">
            <a class="menu-link">
                <span class="icon-large">🚀</span>
                <span class="text">Innovations</span>
                <i class="bi bi-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="../src/a_Innovation.php"
                       class="<?= $activeSub === 'innovations_all' ? 'active-sub' : '' ?>">
                        Toutes
                    </a>
                </li>
                <li class="innovation-pending">
                    <a href="../src/a_Innovation.php?pending=1"
                       class="<?= $activeSub === 'innovations_pending' ? 'active-sub' : '' ?>">
                        En attente
                    </a>
                </li>
            </ul>
        </li>

        <!-- Front Office -->
        <li>
            <a href="../../../Client/index.php" class="menu-link">
                <span class="icon-large">🌍</span>
                <span class="text">Front Office</span>
            </a>
        </li>
    </ul>

    <p class="menu-title">Thème</p>
    <div class="theme-switcher" id="themeToggle">
        <span class="icon-large">☀️</span>
        <span class="text">Mode Jour / Nuit</span>
        <span class="icon-large">🌙</span>
    </div>
</aside>
