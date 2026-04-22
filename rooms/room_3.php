<?php
session_start();
require_once('../database.php');
require_once('../includes/schema.php');

ensureProjectSchema($db_connection);

function formatDuration(int $seconds): string {
  $hours = intdiv($seconds, 3600);
  $minutes = intdiv($seconds % 3600, 60);
  $secs = $seconds % 60;

  if ($hours > 0) {
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
  }

  return sprintf('%02d:%02d', $minutes, $secs);
}

function finalizeTeamEscape(?PDO $db_connection): void {
  if (empty($_SESSION['team_id'])) {
    return;
  }

  if (!$db_connection instanceof PDO) {
    return;
  }

  $teamId = (int)$_SESSION['team_id'];

  $update = $db_connection->prepare(
    'UPDATE teams
     SET finished_at = IFNULL(finished_at, NOW()),
       elapsed_seconds = IFNULL(elapsed_seconds, TIMESTAMPDIFF(SECOND, created_at, NOW()))
     WHERE id = :team_id'
  );
  $update->execute([':team_id' => $teamId]);

  $read = $db_connection->prepare('SELECT finished_at, elapsed_seconds FROM teams WHERE id = :team_id LIMIT 1');
  $read->execute([':team_id' => $teamId]);
  $teamData = $read->fetch(PDO::FETCH_ASSOC);

  if ($teamData) {
    $_SESSION['team_finished_at'] = $teamData['finished_at'] ?? null;
    $_SESSION['team_elapsed_seconds'] = isset($teamData['elapsed_seconds']) ? (int)$teamData['elapsed_seconds'] : null;
  }
}

// Room 3 riddles
$riddles = [
    [
    "question" => "I speak without a mouth and hear without ears. I have no body, but I come to life with wind. What am I?",
        "answer" => "echo",
    "hint" => "It is something that repeats your words back to you."
    ],
    [
    "question" => "The more you take away from me, the bigger I become. What am I?",
    "answer" => "hole",
    "hint" => "Think about digging or empty spaces."
    ],
    [
    "question" => "I have keys but no locks. I have space but no room. You can enter, but you cannot go outside. What am I?",
    "answer" => "keyboard",
    "hint" => "You use me to type."
    ]
];

if ($db_connection instanceof PDO) {
  $room3Query = $db_connection->prepare(
    'SELECT riddle AS question, answer, hint
     FROM question
     WHERE roomId = :roomId
     ORDER BY id DESC
     LIMIT 3'
  );

  $room3Query->execute([
    ':roomId' => 3,
  ]);

  $room3Rows = $room3Query->fetchAll(PDO::FETCH_ASSOC);

  if (count($room3Rows) === 3) {
    $riddles = array_reverse(array_map(function ($row) {
      return [
        'question' => (string)$row['question'],
        'answer' => (string)$row['answer'],
        'hint' => (string)$row['hint'],
      ];
    }, $room3Rows));
  }
}

$reviewMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
  $rating = (int)($_POST['rating'] ?? 0);
  $difficulty = trim($_POST['difficulty'] ?? '');
  $feedbackInput = trim($_POST['feedback'] ?? '');

  if ($rating < 1 || $rating > 5) {
    $reviewMessage = 'Kies een rating tussen 1 en 5.';
  } elseif ($difficulty === '') {
    $reviewMessage = 'Kies een difficulty.';
  } elseif ($feedbackInput === '') {
    $reviewMessage = 'Feedback mag niet leeg zijn.';
  } elseif (!$db_connection instanceof PDO) {
    $reviewMessage = 'Review kon niet worden opgeslagen: database niet beschikbaar.';
  } else {
    $insertReview = $db_connection->prepare(
      'INSERT INTO reviews (team_id, rating, difficulty, feedback) VALUES (:team_id, :rating, :difficulty, :feedback)'
    );

    $insertReview->execute([
      ':team_id' => !empty($_SESSION['team_id']) ? (int)$_SESSION['team_id'] : null,
      ':rating' => $rating,
      ':difficulty' => $difficulty,
      ':feedback' => $feedbackInput,
    ]);

    $reviewMessage = 'Review opgeslagen. Dank je!';
  }
}


if (isset($_POST['reset'])) {
  $_SESSION['room3_current'] = 0;
  header('Location: /EpsteinIslandEscapers/rooms/room_3.php');
    exit;
}

// Init current riddle
if (!isset($_SESSION['room3_current'])) {
  $_SESSION['room3_current'] = 0;
}
$current = (int)$_SESSION['room3_current'];
$feedback = "";
$hintText = "";

// Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Hint knop
    if (isset($_POST['hint'])) {
        $hintText = $riddles[$current]['hint'];
    }

    // Submit knop
    if (isset($_POST['answer'])) {
        $input = strtolower(trim($_POST['answer']));
        if ($input === $riddles[$current]['answer']) {
        $_SESSION['room3_current']++;
        if ($_SESSION['room3_current'] >= count($riddles)) {
          $_SESSION['canLeaveReview'] = true;
          finalizeTeamEscape($db_connection);
                header("Location: ?status=win");
                exit;
            } else {
                header("Refresh:0");
                exit;
            }
        } else {
            header("Location: ?status=lost");
            exit;
        }
    }
}

$status = isset($_GET['status']) ? $_GET['status'] : "";

$finishedAtText = '';
$elapsedText = '';
if ($status === 'win' && !empty($_SESSION['team_id'])) {
  if ($db_connection instanceof PDO) {
    $teamRead = $db_connection->prepare('SELECT finished_at, elapsed_seconds FROM teams WHERE id = :team_id LIMIT 1');
    $teamRead->execute([':team_id' => (int)$_SESSION['team_id']]);
    $teamRow = $teamRead->fetch(PDO::FETCH_ASSOC);

    if ($teamRow) {
      if (!empty($teamRow['finished_at'])) {
        $finishedAtText = (string)$teamRow['finished_at'];
      }
      if (isset($teamRow['elapsed_seconds']) && $teamRow['elapsed_seconds'] !== null) {
        $elapsedText = formatDuration((int)$teamRow['elapsed_seconds']);
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Escape Room</title>
<link rel="icon" type="image/png" href="/EpsteinIslandEscapers/assets/logo.png">
<link rel="shortcut icon" type="image/png" href="/EpsteinIslandEscapers/assets/logo.png">
<style>
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background: linear-gradient(180deg, #050101 0%, #020000 60%, #070101 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
}

.escape-room, .ending {
  text-align: center;
  background: #1a0505;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 10px 30px #000;
  color: #fff;
}

.room-title {
  font-size: 48px;
  color: #ffe4d1;
  margin-bottom: 20px;
  text-shadow: 0 0 20px #b11f1f;
}

input {
  width: 100%;
  padding: 10px;
  background: #000;
  border: 1px solid #b11f1f;
  color: #fff;
  margin-bottom: 10px;
  outline: none;
}

button {
  background: #5c0b0b;
  border: 1px solid #dcbcbc;
  color: #fff;
  padding: 8px 14px;
  cursor: pointer;
  margin: 5px;
}

button:hover {
  background: #7a1a1a;
}

#feedback { color: #ffb3b3; margin-top: 10px; }
#hintText { color: #f2d9b0; margin-top: 10px; font-size: 14px; }
.home-button { background:#5c0b0b; padding:10px 20px; margin-top:20px; display:inline-block; }

.team-badge-container {
  position: fixed;
  top: 20px;
  right: 24px;
  display: flex;
  flex-direction: row;
  gap: 12px;
  align-items: center;
  justify-content: flex-end;
  z-index: 1500;
  white-space: nowrap;
}

.quit-button {
  padding: 10px 14px;
  background: rgba(177, 31, 31, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 10px;
  color: #f3e8d7;
  font-family: ShareTech, Arial, Helvetica, sans-serif;
  font-size: 0.9rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.2s ease, border-color 0.2s ease;
  display: inline-block;
}

.quit-button:hover {
  background: rgba(177, 31, 31, 0.6);
  border-color: rgba(240, 195, 168, 0.7);
}

.team-badge-only {
  padding: 10px 14px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 10px;
  color: #f3e8d7;
  font-family: ShareTech, Arial, Helvetica, sans-serif;
  font-size: 0.9rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  pointer-events: none;
}
</style>
</head>
<body>
<?php 
$current_file = basename($_SERVER['PHP_SELF']);
$is_room = in_array($current_file, ['room_1.php', 'room_2.php', 'room_3.php']);
if (!empty($_SESSION['team_name']) && $is_room): 
?>
  <div class="team-badge-container">
    <a href="/EpsteinIslandEscapers/index.php" class="quit-button">Quit</a>
    <div class="team-badge-only">Team: <?php echo htmlspecialchars($_SESSION['team_name']); ?></div>
  </div>
<?php endif; ?>

<?php if ($status === "win"): ?>
  <div class="ending">
      <h1>Gefeliciteerd!</h1>
      <p>Je hebt het gehaald.</p>
      <?php if ($elapsedText !== ''): ?>
        <p>Jullie eindtijd: <?php echo htmlspecialchars($elapsedText); ?></p>
      <?php endif; ?>
      <?php if ($finishedAtText !== ''): ?>
        <p>Ontsnapt op: <?php echo htmlspecialchars($finishedAtText); ?></p>
      <?php endif; ?>

      <form method="POST" style="margin-top: 12px;">
          <input type="number" name="rating" min="1" max="5" placeholder="Rating 1-5" required>
          <input type="text" name="difficulty" placeholder="Difficulty (easy/medium/hard)" required>
          <input type="text" name="feedback" placeholder="Leave a review" required>
          <button type="submit" name="submit_review">Submit review</button>
      </form>
      <?php if ($reviewMessage !== ''): ?>
        <p id="feedback"><?php echo htmlspecialchars($reviewMessage); ?></p>
      <?php endif; ?>

      <a href="/EpsteinIslandEscapers/reviews.php"><button class="home-button">View Reviews</button></a>

      <form method="POST"><button type="submit" name="reset" class="home-button">Opnieuw beginnen</button></form>
      <a href="/EpsteinIslandEscapers/index.php"><button class="home-button">Home</button></a>
  </div>

<?php elseif ($status === "lost"): ?>
  <div class="ending">
      <h1>Verloren</h1>
      <p>Je hebt het niet gehaald.</p>
      <form method="POST"><button type="submit" name="reset" class="home-button">Opnieuw proberen</button></form>
      <a href="/EpsteinIslandEscapers/index.php"><button class="home-button">Home</button></a>
  </div>

<?php else: ?>
  <div class="escape-room">
      <h1 class="room-title">Room 3</h1>
      <div class="riddle-card">
          <p id="riddleText"><?php echo $riddles[$current]['question']; ?></p>

          <form method="POST">
              <input type="text" name="answer" placeholder="Type je antwoord..." required>
              <br>
              <button type="submit">Submit</button>
              <button type="submit" name="hint">Hint</button>
              <button type="submit" name="reset">Reset</button>
          </form>

          <p id="feedback"><?php echo $feedback; ?></p>
          <p id="hintText"><?php echo $hintText; ?></p>
          <p id="progress">Vraag <?php echo $current + 1; ?> / <?php echo count($riddles); ?></p>
      </div>
  </div>
<?php endif; ?>

</body>
</html>