<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'unlocked' => false,
        'message' => 'Method not allowed.'
    ]);
    exit;
}

if (!empty($_SESSION['cult_unlocked'])) {
    echo json_encode([
        'unlocked' => true,
        'message' => 'The gate already knows your name.'
    ]);
    exit;
}

$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody, true);
$attempt = '';

if (is_array($payload) && isset($payload['answer'])) {
    $attempt = (string)$payload['answer'];
}

$attempt = strtolower($attempt);
$attempt = preg_replace('/[^a-z0-9\s]/', '', $attempt) ?? '';
$attempt = trim(preg_replace('/\s+/', ' ', $attempt) ?? '');

$acceptedAnswers = ['sigil', 'seal', 'mark'];

if (in_array($attempt, $acceptedAnswers, true)) {
    $_SESSION['cult_unlocked'] = true;

    echo json_encode([
        'unlocked' => true,
        'message' => 'The lock yields. You may enter the ritual.'
    ]);
    exit;
}

echo json_encode([
    'unlocked' => false,
    'message' => 'Wrong answer. The chamber stays closed.'
]);
