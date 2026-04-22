<?php
session_start();

require_once '../dbcon.php';

$stmt = $db_connection->prepare('SELECT id, riddle, answer, hint FROM question WHERE roomId = :roomId ORDER BY id ASC');
$stmt->execute([':roomId' => 3]);
$riddles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($riddles) === 0) {
  die('Geen raadsels gevonden voor Room 3. Voeg ze toe via admin/add_riddle.php.');
}


if (isset($_POST['reset'])) {
    $_SESSION['current'] = 0;
    header("Refresh:0");
    exit;
}

// Init current riddle
if (!isset($_SESSION['current'])) {
    $_SESSION['current'] = 0;
}
$current = $_SESSION['current'];
$feedback = "";
$hintText = "";

// Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Hint knop
    if (isset($_POST['hint'])) {
      $hintText = $riddles[$current]['hint'] ?? '';
    }

    // Submit knop
    if (isset($_POST['answer'])) {
        $input = strtolower(trim($_POST['answer']));
      if ($input === strtolower(trim($riddles[$current]['answer']))) {
            $_SESSION['current']++;
            if ($_SESSION['current'] >= count($riddles)) {
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
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Escape Room</title>
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
</style>
</head>
<body>

<?php if ($status === "win"): ?>
  <div class="ending">
      <h1>Gefeliciteerd!</h1>
      <p>Je hebt het gehaald.</p>
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
          <p id="riddleText"><?php echo htmlspecialchars($riddles[$current]['riddle'], ENT_QUOTES, 'UTF-8'); ?></p>

          <form method="POST">
              <input type="text" name="answer" placeholder="Type je antwoord..." required>
              <br>
              <button type="submit">Submit</button>
              <button type="submit" name="hint">Hint</button>
              <button type="submit" name="reset">Reset</button>
          </form>

          <p id="feedback"><?php echo $feedback; ?></p>
            <p id="hintText"><?php echo htmlspecialchars($hintText, ENT_QUOTES, 'UTF-8'); ?></p>
          <p id="progress">Vraag <?php echo $current + 1; ?> / <?php echo count($riddles); ?></p>
      </div>
  </div>
<?php endif; ?>

</body>
</html>