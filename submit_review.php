<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'database.php';
require_once __DIR__ . '/includes/schema.php';

function isAjaxRequest(): bool {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        return true;
    }

    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    return stripos($accept, 'application/json') !== false;
}

function respondJson(array $payload, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload);
    exit;
}

function canLeaveReview(?PDO $db_connection): bool {
    if (!empty($_SESSION['canLeaveReview'])) {
        return true;
    }

    if (!$db_connection instanceof PDO || empty($_SESSION['team_id'])) {
        return false;
    }

    $read = $db_connection->prepare('SELECT finished_at FROM teams WHERE id = :team_id LIMIT 1');
    $read->execute([':team_id' => (int)$_SESSION['team_id']]);
    $row = $read->fetch(PDO::FETCH_ASSOC);

    return !empty($row['finished_at']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /EpsteinIslandEscapers/admin/show_all_reviews.php');
    exit;
}

$errors = [];
$rating = (int)($_POST['rating'] ?? 0);
$difficulty = trim($_POST['difficulty'] ?? '');
$feedback = trim($_POST['feedback'] ?? '');
$teamId = !empty($_SESSION['team_id']) ? (int)$_SESSION['team_id'] : null;

if ($rating < 1 || $rating > 5) {
    $errors[] = 'Rating must be between 1 and 5.';
}
if ($difficulty === '') {
    $errors[] = 'Difficulty is required.';
}
if ($feedback === '') {
    $errors[] = 'Feedback is required.';
}
if (!$db_connection instanceof PDO) {
    $errors[] = 'Database is not available right now.';
}
if (!canLeaveReview($db_connection)) {
    $errors[] = 'Review is unlocked only after solving all riddles.';
}

if (!empty($errors)) {
    if (isAjaxRequest()) {
        respondJson(['success' => false, 'errors' => $errors], 422);
    }

    header('Location: /EpsteinIslandEscapers/admin/show_all_reviews.php');
    exit;
}

ensureProjectSchema($db_connection);

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

if (isAjaxRequest()) {
    respondJson([
        'success' => true,
        'redirect' => '/EpsteinIslandEscapers/admin/show_all_reviews.php'
    ]);
}

header('Location: /EpsteinIslandEscapers/admin/show_all_reviews.php');
exit;
