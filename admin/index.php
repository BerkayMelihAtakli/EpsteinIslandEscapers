<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';
adminRequireLogin();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<main class="admin-main">
    <section class="admin-shell">
        <div class="admin-head">
            <p class="admin-kicker">Control Center</p>
            <h1>Admin Dashboard</h1>
            <p>Manage riddles, teams and reviews from one place.</p>
        </div>

        <p class="admin-actions">
            <a href="/EpsteinIslandEscapers/admin/login.php">Switch account</a>
            <a href="/EpsteinIslandEscapers/admin/logout.php">Logout</a>
        </p>

        <div class="admin-grid">
            <a href="/EpsteinIslandEscapers/admin/show_all_riddles.php" class="admin-link-card">
                <span class="admin-link-title">Show All Riddles</span>
                <span class="admin-link-text">View all riddles, answers and hints.</span>
            </a>
            <a href="/EpsteinIslandEscapers/admin/add_riddle.php" class="admin-link-card">
                <span class="admin-link-title">Add Riddle</span>
                <span class="admin-link-text">Create a new riddle for a room.</span>
            </a>
            <a href="/EpsteinIslandEscapers/admin/show_all_teams.php" class="admin-link-card">
                <span class="admin-link-title">Show All Teams</span>
                <span class="admin-link-text">Inspect teams, scores and escape times.</span>
            </a>
            <a href="/EpsteinIslandEscapers/admin/add_team.php" class="admin-link-card">
                <span class="admin-link-title">Add Team</span>
                <span class="admin-link-text">Create a team manually.</span>
            </a>
            <a href="/EpsteinIslandEscapers/admin/show_all_reviews.php" class="admin-link-card">
                <span class="admin-link-title">Show All Reviews</span>
                <span class="admin-link-text">Read all player feedback.</span>
            </a>
            <a href="/EpsteinIslandEscapers/admin/add_review.php" class="admin-link-card">
                <span class="admin-link-title">Add Review</span>
                <span class="admin-link-text">Insert a review manually.</span>
            </a>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
