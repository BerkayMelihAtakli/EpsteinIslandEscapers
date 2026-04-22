<?php
require_once '../dbcon.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $riddle = trim($_POST['riddle'] ?? '');
     $answer = trim($_POST['answer'] ?? '');
     $hint = trim($_POST['hint'] ?? '');
     $roomId = (int)($_POST['roomId'] ?? 0);

     if (!$db_connection instanceof PDO) {
          $message = 'Database connection failed. Make sure the database exists and import sql/riddles.sql. Technical error: ' . $db_error;
          $messageType = 'error';
     } elseif ($riddle === '' || $answer === '' || $roomId < 1) {
          $message = 'Please provide a riddle, an answer, and a valid room ID.';
          $messageType = 'error';
     } else {
          try {
               $stmt = $db_connection->prepare('INSERT INTO question (riddle, answer, hint, roomId) VALUES (:riddle, :answer, :hint, :roomId)');
               $stmt->execute([
                    ':riddle' => $riddle,
                    ':answer' => $answer,
                    ':hint' => $hint,
                    ':roomId' => $roomId,
               ]);

               $message = 'Riddle added successfully.';
               $messageType = 'success';
          } catch (PDOException $e) {
               $message = 'Save failed: ' . $e->getMessage();
               $messageType = 'error';
          }
     }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Admin - Add Riddle</title>
     <style>
          body {
               font-family: Arial, sans-serif;
               margin: 0;
               padding: 24px;
               background: #111;
               color: #f5f5f5;
          }

          .wrap {
               max-width: 720px;
               margin: 0 auto;
               background: #1b1b1b;
               border: 1px solid #333;
               border-radius: 8px;
               padding: 20px;
          }

          h1 {
               margin-top: 0;
          }

          form {
               display: grid;
               gap: 12px;
          }

          label {
               font-size: 14px;
          }

          input,
          textarea,
          select,
          button {
               font: inherit;
               padding: 10px;
               border-radius: 6px;
               border: 1px solid #444;
               background: #0d0d0d;
               color: #f5f5f5;
          }

          textarea {
               min-height: 110px;
               resize: vertical;
          }

          button {
               cursor: pointer;
               background: #7b1113;
               border-color: #9a2a2d;
          }

          .msg {
               padding: 10px;
               border-radius: 6px;
               margin-bottom: 12px;
               border: 1px solid transparent;
          }

          .msg.success {
               background: #122417;
               border-color: #2d8a46;
          }

          .msg.error {
               background: #2b1515;
               border-color: #b14f4f;
          }

          .links {
               margin-top: 14px;
          }

          .links a {
               color: #f6c5c6;
               margin-right: 14px;
          }
     </style>
</head>
<body>
     <main class="wrap">
          <h1>Add Riddle</h1>

          <?php if ($message !== ''): ?>
               <p class="msg <?php echo htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
               </p>
          <?php endif; ?>

          <form method="post" action="">
               <label for="riddle">Riddle</label>
               <textarea id="riddle" name="riddle" required><?php echo htmlspecialchars($_POST['riddle'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>

               <label for="answer">Answer</label>
               <input id="answer" name="answer" type="text" required value="<?php echo htmlspecialchars($_POST['answer'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

               <label for="hint">Hint</label>
               <input id="hint" name="hint" type="text" value="<?php echo htmlspecialchars($_POST['hint'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

               <label for="roomId">Room ID</label>
               <select id="roomId" name="roomId" required>
                    <option value="">Choose a room</option>
                    <option value="1" <?php echo (($_POST['roomId'] ?? '') === '1') ? 'selected' : ''; ?>>Room 1</option>
                    <option value="2" <?php echo (($_POST['roomId'] ?? '') === '2') ? 'selected' : ''; ?>>Room 2</option>
                    <option value="3" <?php echo (($_POST['roomId'] ?? '') === '3') ? 'selected' : ''; ?>>Room 3</option>
               </select>

               <button type="submit">Save</button>
          </form>

          <div class="links">
               <a href="show_all_riddles.php">View overview</a>
          </div>
     </main>
</body>
</html>