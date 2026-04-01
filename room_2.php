<?php
session_start();

$riddles = [
  [
    "question" => "I have cities, but no houses. I have mountains, but no trees. What am I?",
    "answer" => "map",
    "hint" => "Look at the paper on the wall."
  ],
  [
    "question" => "The more of me there is, the less you see. What am I?",
    "answer" => "darkness",
    "hint" => "Try turning off the lights."
  ],
  [
    "question" => "I always run, but never walk. I have a mouth, but never talk. What am I?",
    "answer" => "river",
    "hint" => "Think of flowing water."
  ]
];

// current riddle index
if (!isset($_SESSION['current'])) {
    $_SESSION['current'] = 0;
}

$current = $_SESSION['current'];
$feedback = "";
$hintText = "";

// answer check
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['answer'])) {
        $input = strtolower(trim($_POST['answer']));

        if ($input === $riddles[$current]['answer']) {
            $feedback = "✔ Correct!";
            $_SESSION['current']++;

            if ($_SESSION['current'] >= count($riddles)) {
                $current = $_SESSION['current'];
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

    <?php if ($current < count($riddles)): ?>

      <p id="riddleText"><?php echo $riddles[$current]['question']; ?></p>

      <form method="POST">
        <input type="text" name="answer" placeholder="Type your answer..." required>
        <br>
        <button type="submit">Submit</button>
        <button type="submit" name="hint">Hint</button>
      </form>

      <p id="feedback"><?php echo $feedback; ?></p>
      <p id="hintText"><?php echo $hintText; ?></p>
      <p id="progress">
        Riddle <?php echo $current + 1; ?> / <?php echo count($riddles); ?>
      </p>

    <?php else: ?>

      <h2 style="color:#ffe4d1">Door Unlocked 🔓</h2>
      <?php session_destroy(); ?>

    <?php endif; ?>

  </div>
</div>

</body>
</html>