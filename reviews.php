<?php
require_once('./database.php');
require_once('./includes/schema.php');

ensureProjectSchema($db_connection);

$reviews = [];

if ($db_connection instanceof PDO) {
  $reviewsQuery = $db_connection->query(
    'SELECT r.rating, r.difficulty, r.feedback, r.created_at, t.team_name
     FROM reviews r
     LEFT JOIN teams t ON t.id = r.team_id
     ORDER BY r.id DESC
     LIMIT 100'
  );

  if ($reviewsQuery !== false) {
    $reviews = $reviewsQuery->fetchAll(PDO::FETCH_ASSOC);
  }
}

include('./includes/header.php');
include('./includes/nav.php');
?>

<main class="admin-main" style="max-width: 980px; margin: 2rem auto;">
  <section class="admin-shell">
    <h1 class="admin-title">Player Reviews</h1>
    <p class="admin-subtitle">Latest feedback from teams that escaped the island.</p>

    <?php if (!($db_connection instanceof PDO)): ?>
      <div class="admin-error">Database is currently unavailable. Reviews cannot be loaded.</div>
    <?php elseif (count($reviews) === 0): ?>
      <div class="admin-empty">No reviews yet.</div>
    <?php else: ?>
      <div style="overflow-x: auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Team</th>
              <th>Rating</th>
              <th>Difficulty</th>
              <th>Feedback</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reviews as $review): ?>
              <tr>
                <td><?php echo htmlspecialchars((string)($review['team_name'] ?? 'Unknown')); ?></td>
                <td><?php echo (int)($review['rating'] ?? 0); ?>/5</td>
                <td><?php echo htmlspecialchars((string)($review['difficulty'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string)($review['feedback'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string)($review['created_at'] ?? '')); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php include('./includes/footer.php'); ?>
