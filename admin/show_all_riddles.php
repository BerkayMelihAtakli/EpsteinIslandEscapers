<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../dbcon.php';

$riddles = [];
$errorMessage = '';
$message = '';
$roomFilter = (int)($_GET['room'] ?? 0);

if (!isset($_SESSION['riddle_csrf'])) {
    $_SESSION['riddle_csrf'] = bin2hex(random_bytes(16));
}

$csrfToken = $_SESSION['riddle_csrf'];

if (!$db_connection instanceof PDO) {
    $errorMessage = 'Database connection failed. Make sure the database exists and import sql/riddles.sql. Technical error: ' . $db_error;
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
        $postedToken = $_POST['csrf_token'] ?? '';
        $deleteId = (int)($_POST['delete_id'] ?? 0);

        if (!hash_equals($csrfToken, $postedToken)) {
            $errorMessage = 'Invalid request token. Please refresh and try again.';
        } elseif ($deleteId < 1) {
            $errorMessage = 'Invalid riddle id.';
        } else {
            try {
                $deleteStmt = $db_connection->prepare('DELETE FROM question WHERE id = :id');
                $deleteStmt->execute([':id' => $deleteId]);

                if ($deleteStmt->rowCount() > 0) {
                    $message = 'Riddle deleted successfully.';
                } else {
                    $errorMessage = 'Riddle not found or already deleted.';
                }
            } catch (PDOException $e) {
                $errorMessage = 'Could not delete riddle: ' . $e->getMessage();
            }
        }
    }

    try {
        if (in_array($roomFilter, [1, 2, 3], true)) {
            $stmt = $db_connection->prepare('SELECT id, riddle, answer, hint, roomId FROM question WHERE roomId = :roomId ORDER BY roomId ASC, id ASC');
            $stmt->execute([':roomId' => $roomFilter]);
        } else {
            $stmt = $db_connection->query('SELECT id, riddle, answer, hint, roomId FROM question ORDER BY roomId ASC, id ASC');
        }

        $riddles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errorMessage = 'Could not fetch riddles: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Riddle Overview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 24px;
            background: #101010;
            color: #f4f4f4;
        }

        .wrap {
            max-width: 1080px;
            margin: 0 auto;
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 8px;
            padding: 18px;
            overflow-x: auto;
        }

        h1 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #272727;
        }

        tr:nth-child(even) {
            background: #161616;
        }

        .error {
            background: #2b1515;
            border: 1px solid #b14f4f;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 12px;
        }

        .success {
            background: #122417;
            border: 1px solid #2d8a46;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 12px;
        }

        .links {
            margin: 12px 0 16px;
        }

        .links a {
            color: #f6c5c6;
            margin-right: 12px;
        }

        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 16px;
        }

        .toolbar select,
        .toolbar button {
            font: inherit;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #444;
            background: #0d0d0d;
            color: #f5f5f5;
        }

        .dangerBtn {
            background: #4a1316;
            border-color: #8c2b31;
            color: #ffd9da;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <main class="wrap">
        <h1>Riddle Overview</h1>

        <div class="links">
            <a href="add_riddle.php">Add new riddle</a>
        </div>

        <form class="toolbar" method="get" action="">
            <label for="room">Room filter:</label>
            <select id="room" name="room">
                <option value="0" <?php echo $roomFilter === 0 ? 'selected' : ''; ?>>All rooms</option>
                <option value="1" <?php echo $roomFilter === 1 ? 'selected' : ''; ?>>Room 1</option>
                <option value="2" <?php echo $roomFilter === 2 ? 'selected' : ''; ?>>Room 2</option>
                <option value="3" <?php echo $roomFilter === 3 ? 'selected' : ''; ?>>Room 3</option>
            </select>
            <button type="submit">Apply</button>
        </form>

        <?php if ($errorMessage !== ''): ?>
            <p class="error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <?php if ($message !== ''): ?>
            <p class="success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <?php if (count($riddles) === 0): ?>
            <p>No riddles found for this filter. Add one from the admin page.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room ID</th>
                        <th>Room</th>
                        <th>Riddle</th>
                        <th>Answer</th>
                        <th>Hint</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riddles as $riddle): ?>
                        <tr>
                            <td><?php echo (int)$riddle['id']; ?></td>
                            <td><?php echo (int)$riddle['roomId']; ?></td>
                            <td><?php echo 'Room ' . (int)$riddle['roomId']; ?></td>
                            <td><?php echo htmlspecialchars($riddle['riddle'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($riddle['answer'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($riddle['hint'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form method="post" action="?room=<?php echo (int)$roomFilter; ?>" onsubmit="return confirm('Delete this riddle?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="delete_id" value="<?php echo (int)$riddle['id']; ?>">
                                    <button class="dangerBtn" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>