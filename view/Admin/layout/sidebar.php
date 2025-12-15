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
            <a href="/projet-web/view/Admin/index.php"
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
                    <a href="/projet-web/view/Admin/Innovation/src/a_Category.php"
                       class="<?= $activeSub === 'categories_list' ? 'active-sub' : '' ?>">
                        Liste
                    </a>
                </li>

                <li>
                    <a href="/projet-web/view/Admin/Innovation/src/add_Category.php"
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
                    <a href="/projet-web/view/Admin/Innovation/src/a_Innovation.php"
                       class="<?= $activeSub === 'innovations_all' ? 'active-sub' : '' ?>">
                        Toutes
                    </a>
                </li>

                <li>
                    <a href="/projet-web/view/Admin/Innovation/src/a_Innovation.php?pending=1"
                       class="<?= $activeSub === 'innovations_pending' ? 'active-sub' : '' ?>">
                        En attente
                    </a>
                </li>
            </ul>
        </li>

        <!-- Administration -->
        <li>
            <a href="/projet-web/view/Admin/UserB/dashboard.php"
               class="menu-link <?= $activeMenu === 'users' ? 'active' : '' ?>">
                <span class="icon-large">👥</span>
                <span class="text">Gestion des utilisateurs</span>
            </a>
            <ul class="submenu">
                <li>
                    <a href="/projet-web/controller/UserC/create_role.php"
                       class="<?= $activeSub === 'innovations_all' ? 'active-sub' : '' ?>">
                        Créer un rôle
                    </a>
                </li>
            </ul>
        </li>
        <!-- Reclamation -->

        <p class="menu-title">Reclamation</p>

        <!-- reclamation -->
        <li class="menu-dropdown" class="menu-title ">
            <a class="menu-link">
                <span class="icon-large">🗂️</span>
                <span class="text">Reclamation</span>
                <i class="bi bi-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="/projet-web/view/Admin/reclamation/src/dashboard.php" class="menu-link">
                        Accueil
                    </a>
                </li>
                <li>
                    <a href="/projet-web/view/Admin/reclamation/src/liste.php" class="menu-link">
                        Liste
                    </a>
                </li>
                <li>
                    <a href="/projet-web/view/Admin/reclamation/src/reponse.php" class="menu-link">
                        Reponse
                    </a>
                </li>
            </ul>
        </li>
        <!-- Front Office -->
        <li>
            <a href="/projet-web/view/Client/F.html" class="menu-link">
                <span class="icon-large">🌍</span>
                <span class="text">Home - Tunispace</span>
            </a>
        </li>

    </ul>


    <p class="menu-title">Thème</p>
    <div class="theme-switcher" id="themeToggle">
        <span class="icon-large">☀️</span>
        <span class="icon-large">🌙</span>
    </div>
</aside>
