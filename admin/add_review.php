<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
adminRequireLogin();
require_once '../database.php';
require_once '../includes/schema.php';

ensureProjectSchema($db_connection);

$teams = $db_connection->query('SELECT id, team_name FROM teams ORDER BY team_name ASC')->fetchAll(PDO::FETCH_ASSOC);
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $teamIdRaw = $_POST['team_id'] ?? '';
     $rating = (int)($_POST['rating'] ?? 0);
     $difficulty = trim($_POST['difficulty'] ?? '');
     $feedback = trim($_POST['feedback'] ?? '');

     $teamId = $teamIdRaw === '' ? null : (int)$teamIdRaw;

     if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5.';
     if ($difficulty === '') $errors[] = 'Difficulty is required.';
     if ($feedback === '') $errors[] = 'Feedback is required.';

     if (empty($errors)) {
          $insert = $db_connection->prepare(
               'INSERT INTO reviews (team_id, rating, difficulty, feedback)
                VALUES (:team_id, :rating, :difficulty, :feedback)'
          );

          $insert->execute([
               ':team_id' => $teamId,
               ':rating' => $rating,
               ':difficulty' => $difficulty,
               ':feedback' => $feedback,
          ]);

          $success = 'Review created successfully.';
     }
}

function esc(string $value): string {
     return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<main class="admin-main">
     <section class="admin-shell admin-shell-narrow">
          <h1>Admin: Add Review</h1>
          <p>Create a review manually for a team.</p>
          <p class="admin-actions">
               <a href="/EpsteinIslandEscapers/admin/">Dashboard</a>
               <a href="/EpsteinIslandEscapers/admin/show_all_reviews.php">View all reviews</a>
               <a href="/EpsteinIslandEscapers/admin/logout.php">Logout</a>
          </p>

          <?php if (!empty($errors)): ?>
               <div class="form-error-box">
                    <ul>
                         <?php foreach ($errors as $error): ?>
                              <li><?php echo esc($error); ?></li>
                         <?php endforeach; ?>
                    </ul>
               </div>
          <?php endif; ?>

          <?php if ($success !== ''): ?>
               <div class="form-success-box"><?php echo esc($success); ?></div>
          <?php endif; ?>

          <form method="post" class="team-form">
               <label for="team_id">Team (optional)</label>
               <select id="team_id" name="team_id" style="width:100%; padding:12px; margin-bottom:16px; border-radius:8px;">
                    <option value="">No team linked</option>
                    <?php foreach ($teams as $team): ?>
                         <option value="<?php echo (int)$team['id']; ?>"><?php echo esc((string)$team['team_name']); ?></option>
                    <?php endforeach; ?>
               </select>

               <label for="rating">Rating (1-5)</label>
               <input id="rating" name="rating" type="number" min="1" max="5" required>

               <label for="difficulty">Difficulty</label>
               <input id="difficulty" name="difficulty" type="text" placeholder="easy / medium / hard" required>

               <label for="feedback">Feedback</label>
               <input id="feedback" name="feedback" type="text" required>

               <button type="submit">Add Review</button>
          </form>
     </section>
</main>

<?php include '../includes/footer.php'; ?>