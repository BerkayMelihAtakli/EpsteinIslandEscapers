<?php
session_start();

if (empty($_SESSION['cult_unlocked'])) {
  header('Location: /EpsteinIslandEscapers/index.php#cult-riddle');
  exit;
}

require_once('../admin/question.php');

$riddles = [];
if (isset($question) && is_array($question)) {
  $riddles = array_values(
    array_filter($question, function ($item) {
      return isset($item['roomId']) && (string)$item['roomId'] === '2';
    })
  );
}

if (count($riddles) === 0) {
  die('No riddles found for Room 2 in admin/question.php');
}


if (!isset($_SESSION['room2_current'])) {
  $_SESSION['room2_current'] = 0;
}

$current = (int)$_SESSION['room2_current'];
$totalRiddles = count($riddles);

if ($current < 0) {
  $current = 0;
  $_SESSION['room2_current'] = 0;
}

if ($current > $totalRiddles) {
  $current = $totalRiddles;
  $_SESSION['room2_current'] = $totalRiddles;
}

$feedback = "";
$hintText = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['answer'])) {
        $input = strtolower(trim($_POST['answer']));
        $correct = strtolower(trim($riddles[$current]['answer']));

        if ($input === $correct) {
            $feedback = "✔ Correct!";
          $_SESSION['room2_current']++;

          if ($_SESSION['room2_current'] >= $totalRiddles) {
            $current = $_SESSION['room2_current'];
            } else {
                header("Refresh:0");
                exit;
            }

        } else {
            $feedback = "✖ Wrong answer";
        }
    }

    if (isset($_POST['hint'])) {
        $hintText = $riddles[$current]['hint'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Room 2 - Escape Room</title>

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

.escape-room {
  text-align: center;
}

.room-title {
  font-size: 48px;
  color: #ffe4d1;
  margin-bottom: 20px;
  text-shadow: 0 0 20px #b11f1f;
}

.riddle-card {
  background: #1a0505;
  border: 1px solid #8f1010;
  padding: 25px;
  border-radius: 12px;
  width: 320px;
  box-shadow: 0 10px 30px #000;
}

#riddleText {
  color: #f0d2bf;
  margin-bottom: 15px;
  line-height: 1.5;
}

#progress {
  margin-top: 10px;
  color: #c9a8a8;
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

#feedback {
  margin-top: 10px;
  color: #ffb3b3;
}

#hintText {
  margin-top: 10px;
  color: #f2d9b0;
  font-size: 14px;
}
</style>

</head>
<body>

<div class="escape-room">
  <h1 class="room-title">Room 2</h1>

  <div class="riddle-card">

    <?php if ($current < $totalRiddles): ?>

      <p id="riddleText">
        <?php echo htmlspecialchars($riddles[$current]['riddle']); ?>
      </p>

      <form method="POST">
        <input type="text" name="answer" placeholder="Type your answer..." required>
        <br>
        <button type="submit">Submit</button>
        <button type="submit" name="hint">Hint</button>
      </form>

      <p id="feedback"><?php echo $feedback; ?></p>
      <p id="hintText"><?php echo $hintText; ?></p>

      <p id="progress">
        Riddle <?php echo $current + 1; ?> / <?php echo $totalRiddles; ?>
      </p>

    <?php else: ?>

      <h2 style="color:#ffe4d1">You can go to Room 3</h2>
      <a href="room_3.php"><button style="background: #5c0b0b; border: 1px solid #dcbcbc; color: #fff; padding: 10px 20px; cursor: pointer; margin-top: 20px;">Enter Room 3</button></a>
      <?php unset($_SESSION['room2_current']); ?>

    <?php endif; ?>

  </div>
</div>

</body>
</html>