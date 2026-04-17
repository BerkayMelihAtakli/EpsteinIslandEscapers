<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
  exit;
}

if (empty($_SESSION['cult_unlocked'])) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'message' => 'Cult lock still active']);
  exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
$token = is_array($payload) && isset($payload['token']) ? (string)$payload['token'] : '';

if (!isset($_SESSION['room1_token']) || !hash_equals((string)$_SESSION['room1_token'], $token)) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'message' => 'Invalid token']);
  exit;
}

$_SESSION['room1_completed'] = true;

echo json_encode(['ok' => true]);
