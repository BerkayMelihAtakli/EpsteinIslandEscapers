<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD = 'admin123';

function adminIsLoggedIn(): bool
{
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function adminRequireLogin(): void
{
    if (adminIsLoggedIn()) {
        return;
    }

    $currentUri = $_SERVER['REQUEST_URI'] ?? '/EpsteinIslandEscapers/admin/';
    $next = urlencode($currentUri);
    header('Location: /EpsteinIslandEscapers/admin/login.php?next=' . $next);
    exit;
}
