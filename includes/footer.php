<?php
$modalReviews = [];
$canLeaveReview = !empty($_SESSION['canLeaveReview']);

if (!isset($db_connection)) {
  require_once __DIR__ . '/../database.php';
}
require_once __DIR__ . '/schema.php';
ensureProjectSchema($db_connection ?? null);

if (($db_connection ?? null) instanceof PDO) {
  $reviewsStmt = $db_connection->query(
    'SELECT r.rating, r.difficulty, r.feedback, r.created_at, t.team_name
     FROM reviews r
     LEFT JOIN teams t ON t.id = r.team_id
     ORDER BY r.id DESC
     LIMIT 8'
  );
  $modalReviews = $reviewsStmt ? $reviewsStmt->fetchAll(PDO::FETCH_ASSOC) : [];

  if (!$canLeaveReview && !empty($_SESSION['team_id'])) {
    $finishStmt = $db_connection->prepare('SELECT finished_at FROM teams WHERE id = :team_id LIMIT 1');
    $finishStmt->execute([':team_id' => (int)$_SESSION['team_id']]);
    $finishRow = $finishStmt->fetch(PDO::FETCH_ASSOC);
    $canLeaveReview = !empty($finishRow['finished_at']);
  }
}
?>

<footer class="footer">
  <div class="footer-container">
    <div class="footer-brand">
      <h2>EpsteinIslandEscapers</h2>
      <p>Secrets don’t stay buried forever.</p>
    </div>

    <div class="footer-links">
      <h4>Explore</h4>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Reviews</a></li>
        <li><a href="#">Join the Cult</a></li>
      </ul>
    </div>

    <div class="footer-contact">
      <h4>Contact</h4>
      <p>Email: Ihateepsteindisland@.com</p>
      <p>Location: Techniek College Rotterdam</p>
    </div>

    <div class="footer-social">
      <h4>Follow</h4>
      <a href="#">Instagram</a>
      <a href="#">Twitter</a>
      <a href="#">Discord</a>
    </div>
  </div>

  <div class="footer-bottom">
    <p>© 2026 EpsteinIslandEscapers. All rights reserved.</p>
  </div>
</footer>

<div id="create-team-modal" class="team-modal" aria-hidden="true">
  <div class="team-modal-overlay" data-close-team-modal></div>
  <div class="team-modal-panel" role="dialog" aria-modal="true" aria-labelledby="team-modal-title">
    <button type="button" class="team-modal-close" data-close-team-modal aria-label="Close create team popup">x</button>
    <h2 id="team-modal-title">Create your team</h2>
    <p class="team-modal-subtitle">Enter a team name and at least two team members.</p>

    <div id="team-modal-errors" class="form-error-box" hidden>
      <ul id="team-modal-error-list"></ul>
    </div>

    <form id="create-team-form" class="team-form" method="post" action="/EpsteinIslandEscapers/create_team.php" novalidate>
      <label for="modal_team_name">Team Name</label>
      <input id="modal_team_name" name="team_name" type="text" required>

      <label for="modal_member1">Member 1</label>
      <input id="modal_member1" name="member1" type="text" required>

      <label for="modal_member2">Member 2</label>
      <input id="modal_member2" name="member2" type="text" required>

      <label for="modal_member3">Member 3 (optional)</label>
      <input id="modal_member3" name="member3" type="text">

      <label for="modal_member4">Member 4 (optional)</label>
      <input id="modal_member4" name="member4" type="text">

      <button type="submit" class="submit-button" id="create-team-submit">Create Team</button>
    </form>
  </div>
</div>

<div id="review-modal" class="team-modal" aria-hidden="true">
  <div class="team-modal-overlay" data-close-review-modal></div>
  <div class="team-modal-panel" role="dialog" aria-modal="true" aria-labelledby="review-modal-title">
    <button type="button" class="team-modal-close" data-close-review-modal aria-label="Close reviews popup">x</button>
    <h2 id="review-modal-title">Reviews</h2>
    <p class="team-modal-subtitle">See what other teams said and leave your own review.</p>

    <div class="review-feed-box">
      <?php if (empty($modalReviews)): ?>
        <p class="review-empty">No reviews yet. Be the first to leave one.</p>
      <?php else: ?>
        <ul class="review-feed-list">
          <?php foreach ($modalReviews as $review): ?>
            <li class="review-feed-item">
              <p class="review-feed-meta">
                <strong><?php echo htmlspecialchars((string)($review['team_name'] ?? 'Unlinked Team'), ENT_QUOTES, 'UTF-8'); ?></strong>
                •
                Rating: <?php echo (int)$review['rating']; ?>/5
                •
                <?php echo htmlspecialchars((string)$review['difficulty'], ENT_QUOTES, 'UTF-8'); ?>
              </p>
              <p class="review-feed-text"><?php echo htmlspecialchars((string)$review['feedback'], ENT_QUOTES, 'UTF-8'); ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <?php if ($canLeaveReview): ?>
      <button type="button" class="submit-button" id="open-review-form" style="margin-bottom: 14px;">Give Review</button>
    <?php else: ?>
      <p class="review-lock-note">You can give a review only after solving all riddles.</p>
    <?php endif; ?>

    <div id="review-modal-errors" class="form-error-box" hidden>
      <ul id="review-modal-error-list"></ul>
    </div>
    <div id="review-modal-success" class="form-success-box" hidden>Review sent successfully.</div>

    <form id="review-form" class="team-form" method="post" action="/EpsteinIslandEscapers/submit_review.php" novalidate hidden>
      <label for="modal_rating">Rating (1-5)</label>
      <input id="modal_rating" name="rating" type="number" min="1" max="5" required>

      <label for="modal_difficulty">Difficulty</label>
      <input id="modal_difficulty" name="difficulty" type="text" placeholder="easy / medium / hard" required>

      <label for="modal_feedback">Feedback</label>
      <input id="modal_feedback" name="feedback" type="text" required>

      <button type="submit" class="submit-button" id="review-submit">Send Review</button>
    </form>
  </div>
</div>

<!-- page scripts -->
<script src="/EpsteinIslandEscapers/js/menu.js"></script>
<script src="/EpsteinIslandEscapers/js/join-riddle.js"></script>
<script src="/EpsteinIslandEscapers/js/create-team-modal.js"></script>
<script src="/EpsteinIslandEscapers/js/review-modal.js"></script>
</body>
</html>