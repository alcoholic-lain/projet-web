<?php
// Démarrer la session SEULEMENT si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// adapte si besoin, mais vu ton projet ça devrait être bon
define('BASE_URL', '/projet-web/view');

function redirect($path) {
    header('Location: ' . BASE_URL . $path);
    exit;
}

/**
 * Redirige vers login si personne n'est connecté
 */
function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        redirect('/Client/login/login.html');
    }
}

/**
 * Redirige vers login si pas admin
 */

function requireAdmin() {
    requireLogin(); // déjà vérifie que la personne est connectée

    if (empty($_SESSION['role_id']) || (int)$_SESSION['role_id'] !== 1) {
        // user normal → on le renvoie au login (ou page d'accueil client si tu préfères)
        redirect('/Client/login/login.html');
    }
}
