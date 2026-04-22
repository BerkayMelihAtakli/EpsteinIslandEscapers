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
     $teamName = trim($_POST['team_name'] ?? '');
     $member1 = trim($_POST['member1'] ?? '');
     $member2 = trim($_POST['member2'] ?? '');
     $member3 = trim($_POST['member3'] ?? '');
     $member4 = trim($_POST['member4'] ?? '');
     $score = (int)($_POST['score'] ?? 0);

     if ($teamName === '') $errors[] = 'Team name is required.';
     if ($member1 === '') $errors[] = 'Member 1 is required.';
     if ($member2 === '') $errors[] = 'Member 2 is required.';

     if (empty($errors)) {
          $insert = $db_connection->prepare(
               'INSERT INTO teams (team_name, member1, member2, member3, member4, score)
                VALUES (:team_name, :member1, :member2, :member3, :member4, :score)'
          );

          $insert->execute([
               ':team_name' => $teamName,
               ':member1' => $member1,
               ':member2' => $member2,
               ':member3' => $member3 !== '' ? $member3 : null,
               ':member4' => $member4 !== '' ? $member4 : null,
               ':score' => $score,
          ]);

          $success = 'Team created successfully.';
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
          <h1>Admin: Add Team</h1>
          <p>Create a new team manually from the admin panel.</p>

          <p class="admin-actions">
               <a href="/EpsteinIslandEscapers/admin/">Dashboard</a>
               <a href="/EpsteinIslandEscapers/admin/show_all_teams.php">View all teams</a>
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
               <label for="team_name">Team Name</label>
               <input id="team_name" name="team_name" type="text" required>

               <label for="member1">Member 1</label>
               <input id="member1" name="member1" type="text" required>

               <label for="member2">Member 2</label>
               <input id="member2" name="member2" type="text" required>

               <label for="member3">Member 3 (optional)</label>
               <input id="member3" name="member3" type="text">

               <label for="member4">Member 4 (optional)</label>
               <input id="member4" name="member4" type="text">

               <label for="score">Score</label>
               <input id="score" name="score" type="number" min="0" value="0">

               <button type="submit">Create Team</button>
          </form>
     </section>
</main>

<?php include '../includes/footer.php'; ?>