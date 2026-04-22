<?php
require_once __DIR__ . '/auth.php';

if (adminIsLoggedIn()) {
    header('Location: /EpsteinIslandEscapers/admin/');
    exit;
}

$errors = [];
$next = $_GET['next'] ?? '/EpsteinIslandEscapers/admin/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $postedNext = $_POST['next'] ?? '/EpsteinIslandEscapers/admin/';

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: ' . $postedNext);
        exit;
    }

    $errors[] = 'Invalid username or password.';
    $next = $postedNext;
}
?>
<?php include '../includes/header.php'; ?>

<main class="admin-main">
    <section class="admin-shell admin-shell-narrow">
        <div class="admin-head">
            <p class="admin-kicker">Restricted Area</p>
            <h1>Admin Login</h1>
            <p>Sign in to access the admin dashboard.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="form-error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="team-form">
            <input type="hidden" name="next" value="<?php echo htmlspecialchars((string)$next, ENT_QUOTES, 'UTF-8'); ?>">

            <label for="username">Username</label>
            <input id="username" name="username" type="text" autocomplete="username" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <button type="submit">Sign In</button>
        </form>
    </section>
</main>

</body>
</html>
