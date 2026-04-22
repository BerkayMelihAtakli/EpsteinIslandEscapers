<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
adminRequireLogin();
require_once '../database.php';
require_once '../includes/schema.php';

ensureProjectSchema($db_connection);

$teams = $db_connection->query(
     'SELECT
          t.id,
          t.team_name,
          t.member1,
          t.member2,
          t.member3,
          t.member4,
          t.score,
          t.created_at,
          t.finished_at,
          t.elapsed_seconds,
          COUNT(r.id) AS review_count,
          ROUND(AVG(r.rating), 2) AS avg_rating
      FROM teams t
      LEFT JOIN reviews r ON r.team_id = t.id
      GROUP BY t.id
      ORDER BY t.id DESC'
)->fetchAll(PDO::FETCH_ASSOC);

function esc(string $value): string {
     return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatElapsed($seconds): string {
     if ($seconds === null) {
          return '-';
     }

     $seconds = (int)$seconds;
     $hours = intdiv($seconds, 3600);
     $minutes = intdiv($seconds % 3600, 60);
     $secs = $seconds % 60;

     if ($hours > 0) {
          return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
     }

     return sprintf('%02d:%02d', $minutes, $secs);
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<main class="admin-main">
     <section class="admin-shell admin-shell-wide">
          <h1>Admin: Teams Overview</h1>
          <p>All teams, their score, escape end time and reviews.</p>
          <p class="admin-actions">
               <a href="/EpsteinIslandEscapers/admin/">Dashboard</a>
               <a href="/EpsteinIslandEscapers/admin/add_team.php">Add team</a> |
               <a href="/EpsteinIslandEscapers/admin/show_all_reviews.php">All reviews</a>
               <a href="/EpsteinIslandEscapers/admin/logout.php">Logout</a>
          </p>

          <div class="admin-table-wrap">
               <table class="admin-table">
                    <thead>
                         <tr>
                              <th>ID</th>
                              <th>Team</th>
                              <th>Members</th>
                              <th>Score</th>
                              <th>Created At</th>
                              <th>End Time</th>
                              <th>Elapsed</th>
                              <th>Reviews</th>
                              <th>Avg Rating</th>
                         </tr>
                    </thead>
                    <tbody>
                         <?php if (empty($teams)): ?>
                              <tr><td colspan="9">No teams yet.</td></tr>
                         <?php else: ?>
                              <?php foreach ($teams as $team): ?>
                                   <?php
                                        $members = array_filter([
                                             $team['member1'] ?? '',
                                             $team['member2'] ?? '',
                                             $team['member3'] ?? '',
                                             $team['member4'] ?? '',
                                        ]);
                                   ?>
                                   <tr>
                                        <td><?php echo (int)$team['id']; ?></td>
                                        <td><?php echo esc((string)$team['team_name']); ?></td>
                                        <td><?php echo esc(implode(', ', $members)); ?></td>
                                        <td><?php echo (int)$team['score']; ?></td>
                                        <td><?php echo esc((string)$team['created_at']); ?></td>
                                        <td><?php echo esc((string)($team['finished_at'] ?? '-')); ?></td>
                                        <td><?php echo esc(formatElapsed($team['elapsed_seconds'])); ?></td>
                                        <td><?php echo (int)$team['review_count']; ?></td>
                                        <td><?php echo $team['avg_rating'] !== null ? esc((string)$team['avg_rating']) : '-'; ?></td>
                                   </tr>
                              <?php endforeach; ?>
                         <?php endif; ?>
                    </tbody>
               </table>
          </div>
     </section>
</main>

<?php include '../includes/footer.php'; ?>