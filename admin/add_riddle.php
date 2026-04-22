<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
adminRequireLogin();
require_once '../database.php';
require_once '../includes/schema.php';

ensureProjectSchema($db_connection);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $riddle = trim($_POST['riddle'] ?? '');
     $answer = trim($_POST['answer'] ?? '');
     $hint = trim($_POST['hint'] ?? '');
     $roomId = (int)($_POST['roomId'] ?? 0);

     if ($riddle === '') $errors[] = 'Riddle is required.';
     if ($answer === '') $errors[] = 'Answer is required.';
     if (!in_array($roomId, [2, 3], true)) $errors[] = 'Please choose Room 2 or Room 3.';

     if (empty($errors)) {
          $insert = $db_connection->prepare(
               'INSERT INTO question (riddle, answer, hint, roomId)
                VALUES (:riddle, :answer, :hint, :roomId)'
          );

          $insert->execute([
               ':riddle' => $riddle,
               ':answer' => $answer,
               ':hint' => $hint !== '' ? $hint : null,
               ':roomId' => $roomId,
          ]);

          $success = 'Riddle added successfully.';
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
          <h1>Admin: Add Riddle</h1>
          <p>Create riddle, answer and hint for a room.</p>
          <p class="admin-actions">
               <a href="/EpsteinIslandEscapers/admin/">Dashboard</a>
               <a href="/EpsteinIslandEscapers/admin/show_all_riddles.php">View all riddles</a>
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
               <label for="riddle">Riddle</label>
               <input id="riddle" name="riddle" type="text" required>

               <label for="answer">Answer</label>
               <input id="answer" name="answer" type="text" required>

               <label for="hint">Hint</label>
               <input id="hint" name="hint" type="text">

               <label for="roomId">Room</label>
               <select id="roomId" name="roomId" required style="width:100%; padding:12px; margin-bottom:16px; border-radius:8px;">
                    <option value="">Choose a room</option>
                    <option value="2">Room 2</option>
                    <option value="3">Room 3</option>
               </select>

               <button type="submit">Add Riddle</button>
          </form>
     </section>
</main>

<?php include '../includes/footer.php'; ?>