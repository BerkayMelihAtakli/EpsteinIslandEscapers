<?php
require_once __DIR__ . '/auth.php';

unset($_SESSION['admin_logged_in'], $_SESSION['admin_username']);
header('Location: /EpsteinIslandEscapers/admin/login.php');
exit;
