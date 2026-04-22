<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
adminRequireLogin();
require_once '../database.php';
require_once '../includes/schema.php';

ensureProjectSchema($db_connection);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review_id'])) {
	$deleteId = (int)($_POST['delete_review_id'] ?? 0);

	if (!($db_connection instanceof PDO)) {
		$errors[] = 'Database is not available right now.';
	} elseif ($deleteId <= 0) {
		$errors[] = 'Invalid review ID.';
	} else {
		$deleteStmt = $db_connection->prepare('DELETE FROM reviews WHERE id = :id LIMIT 1');
		$deleteStmt->execute([':id' => $deleteId]);

		if ($deleteStmt->rowCount() > 0) {
			$success = 'Review deleted successfully.';
		} else {
			$errors[] = 'Review not found or already deleted.';
		}
	}
}

$reviews = [];

if ($db_connection instanceof PDO) {
	$reviews = $db_connection->query(
		'SELECT r.id, r.team_id, r.rating, r.difficulty, r.feedback, r.created_at, t.team_name
		 FROM reviews r
		 LEFT JOIN teams t ON t.id = r.team_id
		 ORDER BY r.id DESC'
	)->fetchAll(PDO::FETCH_ASSOC);
}

function esc(string $value): string {
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<main class="admin-main">
	<section class="admin-shell admin-shell-wide">
		<h1>Admin: Reviews Overview</h1>
		<p>All reviews left by players, including linked team.</p>
		<p class="admin-actions">
			<a href="/EpsteinIslandEscapers/admin/">Dashboard</a>
			<a href="/EpsteinIslandEscapers/admin/add_review.php">Add review</a> |
			<a href="/EpsteinIslandEscapers/admin/show_all_teams.php">Teams overview</a>
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
						<th>Team</th>
						<th>Rating</th>
						<th>Difficulty</th>
						<th>Feedback</th>
						<th>Created At</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($reviews)): ?>
						<tr><td colspan="7">No reviews yet.</td></tr>
					<?php else: ?>
						<?php foreach ($reviews as $review): ?>
							<tr>
								<td><?php echo (int)$review['id']; ?></td>
								<td><?php echo esc((string)($review['team_name'] ?? 'Unlinked')); ?></td>
								<td><?php echo (int)$review['rating']; ?></td>
								<td><?php echo esc((string)$review['difficulty']); ?></td>
								<td><?php echo esc((string)$review['feedback']); ?></td>
								<td><?php echo esc((string)$review['created_at']); ?></td>
								<td>
									<form method="post" class="admin-inline-form" onsubmit="return confirm('Delete this review?');">
										<input type="hidden" name="delete_review_id" value="<?php echo (int)$review['id']; ?>">
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