<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'database.php';
require_once __DIR__ . '/includes/schema.php';

$errors = [];

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_name = trim($_POST['team_name'] ?? '');
    $member1 = trim($_POST['member1'] ?? '');
    $member2 = trim($_POST['member2'] ?? '');
    $member3 = trim($_POST['member3'] ?? '');
    $member4 = trim($_POST['member4'] ?? '');

    if ($team_name === '') {
        $errors[] = 'Please enter a team name.';
    }
    if ($member1 === '') {
        $errors[] = 'Please enter the name of member 1.';
    }
    if ($member2 === '') {
        $errors[] = 'Please enter the name of member 2.';
    }

    if (empty($errors)) {
        ensureProjectSchema($db_connection);

        $insert = $db_connection->prepare(
            'INSERT INTO teams (team_name, member1, member2, member3, member4) VALUES (:team_name, :member1, :member2, :member3, :member4)'
        );

        $insert->execute([
            ':team_name' => $team_name,
            ':member1' => $member1,
            ':member2' => $member2,
            ':member3' => $member3 ?: null,
            ':member4' => $member4 ?: null,
        ]);

        $_SESSION['team_id'] = $db_connection->lastInsertId();
        $_SESSION['team_name'] = $team_name;
    $_SESSION['team_started_at'] = time();
    $_SESSION['room3_current'] = 0;

        if (isAjaxRequest()) {
            respondJson([
                'success' => true,
                'redirect' => '/EpsteinIslandEscapers/rooms/room_1.php'
            ]);
        }

        header('Location: /EpsteinIslandEscapers/rooms/room_1.php');
        exit;
    }

    if (isAjaxRequest()) {
        respondJson([
            'success' => false,
            'errors' => $errors
        ], 422);
    }

    header('Location: /EpsteinIslandEscapers/index.php#create-team');
    exit;
}

header('Location: /EpsteinIslandEscapers/index.php#create-team');
exit;
?>
