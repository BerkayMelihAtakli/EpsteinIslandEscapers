<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
adminRequireLogin();
require_once '../database.php';
require_once '../includes/schema.php';

ensureProjectSchema($db_connection);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_riddle_id'])) {
    $deleteId = (int)($_POST['delete_riddle_id'] ?? 0);

    if (!($db_connection instanceof PDO)) {
        $errors[] = 'Database is not available right now.';
    } elseif ($deleteId <= 0) {
        $errors[] = 'Invalid riddle ID.';
    } else {
        $deleteStmt = $db_connection->prepare('DELETE FROM question WHERE id = :id LIMIT 1');
        $deleteStmt->execute([':id' => $deleteId]);

        if ($deleteStmt->rowCount() > 0) {
            $success = 'Riddle deleted successfully.';
        } else {
            $errors[] = 'Riddle not found or already deleted.';
        }
    }
}

$riddles = $db_connection->query(
    'SELECT id, riddle, answer, hint, roomId
     FROM question
     ORDER BY roomId ASC, id ASC'
)->fetchAll(PDO::FETCH_ASSOC);

function esc(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<main class="admin-main">
    <section class="admin-shell admin-shell-wide">
        <h1>Admin: Riddles Overview</h1>
        <p>All riddles, answers, hints and room IDs.</p>
        <p class="admin-actions">
            <a href="/EpsteinIslandEscapers/admin/">Dashboard</a>
            <a href="/EpsteinIslandEscapers/admin/add_riddle.php">Add riddle</a>
            <a href="/EpsteinIslandEscapers/admin/logout.php">Logout</a>
        </p>

        <?php if (!empty($errors)): ?>
            <div class="form-error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo esc((string)$error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success !== ''): ?>
            <div class="form-success-box"><?php echo esc($success); ?></div>
        <?php endif; ?>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Riddle</th>
                        <th>Answer</th>
                        <th>Hint</th>
                        <th>Room ID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riddles)): ?>
                        <tr><td colspan="6">No riddles found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($riddles as $riddle): ?>
                            <tr>
                                <td><?php echo (int)$riddle['id']; ?></td>
                                <td><?php echo esc((string)$riddle['riddle']); ?></td>
                                <td><?php echo esc((string)$riddle['answer']); ?></td>
                                <td><?php echo esc((string)($riddle['hint'] ?? '-')); ?></td>
                                <td><?php echo (int)$riddle['roomId']; ?></td>
                                <td>
                                    <form method="post" class="admin-inline-form" onsubmit="return confirm('Delete this riddle?');">
                                        <input type="hidden" name="delete_riddle_id" value="<?php echo (int)$riddle['id']; ?>">
                                        <button type="submit" class="admin-delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>