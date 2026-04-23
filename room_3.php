<?php
session_start();


if (isset($_GET['restart'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


$totalTime = 60;

if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time();
}


if (!isset($_SESSION['status'])) {
    $_SESSION['status'] = 'playing';
}


if (!isset($_SESSION['reviews'])) {
    $_SESSION['reviews'] = [];
}


$riddles = [
 [
    "question" => "Ik ben altijd bij je, maar je kunt mij nooit aanraken. Wat ben ik?",
    "answer" => "schaduw",
    "hint" => "Ik volg je als de zon schijnt."
],
[
    "question" => "Ik heb een gezicht maar geen ogen, handen maar geen armen. Wat ben ik?",
    "answer" => "klok",
    "hint" => "Ik hang vaak aan de muur en geef tijd aan."
],
[
    "question" => "Wat wordt natter terwijl het droogt?",
    "answer" => "handdoek",
    "hint" => "Je gebruikt mij na het douchen."
]
];


if (!isset($_SESSION['current'])) {
    $_SESSION['current'] = 0;
}

$current = $_SESSION['current'];


$hintText = $_SESSION['hint'] ?? "";


$elapsed = time() - $_SESSION['start_time'];
$remaining = $totalTime - $elapsed;

if ($remaining <= 0) {
    $_SESSION['status'] = 'lost';
    $remaining = 0;
}

if ($remaining <= 0) {
    $_SESSION['status'] = 'lost';
}

$status = $_SESSION['status'];

$feedback = "";


if ($status === 'playing' && isset($_POST['answer'])) {

    $input = strtolower(trim($_POST['answer']));

    if ($input === $riddles[$current]['answer']) {

        $_SESSION['current']++;
        unset($_SESSION['hint']); // 🔥 reset hint

        if ($_SESSION['current'] >= count($riddles)) {
            $_SESSION['status'] = 'won';
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } else {
        $feedback = "✖ Wrong answer";
    }
}


if (isset($_POST['hint']) && $status === 'playing') {
    $_SESSION['hint'] = $riddles[$current]['hint'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


if (isset($_POST['review_submit'])) {
    $_SESSION['reviews'][] = [
        "rating" => $_POST['rating'],
        "difficulty" => $_POST['difficulty'],
        "feedback" => $_POST['feedback']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Room 3 - Escape Room</title>

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
}

#timer {
  color: #ffb3b3;
  font-size: 18px;
}

input {
  width: 100%;
  padding: 10px;
  background: #000;
  border: 1px solid #b11f1f;
  color: #fff;
  margin-bottom: 10px;
}

button {
  background: #5c0b0b;
  border: 1px solid #dcbcbc;
  color: #fff;
  padding: 8px;
  cursor: pointer;
}

button:hover {
  background: #7a1a1a;
}

a {
  display: inline-block;
  background: #5c0b0b;
  border: 1px solid #dcbcbc;
  color: #fff;
  padding: 8px 14px;
  text-decoration: none;
  margin-top: 10px;
  cursor: pointer;
}

a:hover {
  background: #7a1a1a;
}
h2, h3 {
  color: #ffe4d1;
}
p {
  color: #f0d2bf;
}
div{
  color: #f0d2bf;
}
</style>

<script>
let timeLeft = <?php echo $remaining; ?>;

let timer = setInterval(() => {

    document.getElementById("timer").innerText = "Tijd: " + timeLeft + "s";

  if (timeLeft <= 0) {
    clearInterval(timer);
    window.location.href = window.location.pathname;
}

    timeLeft--;

}, 1000);
</script>

</head>
<body>

<div class="escape-room">
  <h1 class="room-title">Room 3</h1>

  <div class="riddle-card">

    <?php if ($status === 'playing'): ?>

        <h2 id="timer"></h2>

        <p id="riddleText"><?php echo $riddles[$current]['question']; ?></p>

        <form method="POST">
            <input type="text" name="answer" placeholder="Type your answer..." required>
            <button type="submit">Submit</button>
            <button type="submit" name="hint">Hint</button>
        </form>

        <p><?php echo $feedback; ?></p>
        <p><?php echo $hintText; ?></p>

        <p>Riddle <?php echo $current + 1; ?> / <?php echo count($riddles); ?></p>

    <?php elseif ($status === 'won'): ?>

        <h2>🎉 Gewonnen!</h2>

        <form method="POST">
            <h3>Leave a review</h3>

            <input type="number" name="rating" min="1" max="5" required placeholder="Rating (1-5)">
            <input type="text" name="difficulty" required placeholder="Difficulty">
            <input type="text" name="feedback" required placeholder="Feedback">

            <button type="submit" name="review_submit">Submit review</button>
        </form>

        <?php foreach ($_SESSION['reviews'] as $r): ?>
            <div style="border:1px solid #fff; margin:5px; padding:5px;">
                ⭐ <?php echo $r['rating']; ?>/5 <br>
                🎯 <?php echo $r['difficulty']; ?> <br>
                💬 <?php echo $r['feedback']; ?>
            </div>
        <?php endforeach; ?>

        <a href="?restart=1">Restart</a>

    <?php else: ?>

        <h2>💀 Game Over</h2>
        <a href="?restart=1">Restart</a>

    <?php endif; ?>

  </div>
</div>

</body>
</html>