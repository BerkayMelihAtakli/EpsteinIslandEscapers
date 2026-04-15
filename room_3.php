<?php
session_start();

// Riddles
$riddles = [
    [
        "question" => "Ik spreek zonder mond en luister zonder oren. Ik heb geen lichaam, maar ik kom tot leven met de wind. Wat ben ik?",
        "answer" => "echo",
        "hint" => "Het is iets dat je hoort als het je woorden herhaalt."
    ],
    [
        "question" => "Hoe meer je van mij wegneemt, hoe groter ik word. Wat ben ik?",
        "answer" => "gat",
        "hint" => "Denk aan graven of lege ruimtes."
    ],
    [
        "question" => "Ik heb toetsen maar geen sloten. Ik heb een spatie maar geen kamer. Je kunt mij betreden, maar je kunt niet naar buiten. Wat ben ik?",
        "answer" => "toetsenbord",
        "hint" => "Je gebruikt mij om te typen."
    ]
];

// Init
if (!isset($_SESSION['current'])) $_SESSION['current'] = 0;
if (!isset($_SESSION['hint'])) $_SESSION['hint'] = "";
if (!isset($_SESSION['start_time'])) $_SESSION['start_time'] = time();

$current = $_SESSION['current'];
$hintText = $_SESSION['hint'];
$timeLimit = 30;

// Reset
if (isset($_POST['reset'])) {
    $_SESSION['current'] = 0;
    $_SESSION['hint'] = "";
    $_SESSION['start_time'] = time();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// FORM HANDLING (BELANGRIJK)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Hint knop
    if (isset($_POST['hint'])) {
        $_SESSION['hint'] = $riddles[$current]['hint'];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Submit knop (FIX!)
    if (isset($_POST['submit'])) {
        $input = strtolower(trim($_POST['answer']));

        if ($input === $riddles[$current]['answer']) {
            $_SESSION['current']++;
            $_SESSION['hint'] = "";
            $_SESSION['start_time'] = time();

            if ($_SESSION['current'] >= count($riddles)) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?status=win");
                exit;
            } else {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
        } else {
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=lost");
            exit;
        }
    }
}

// TIMER CHECK (NA submit!)
if (time() - $_SESSION['start_time'] > $timeLimit) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=lost");
    exit;
}

$status = $_GET['status'] ?? "";
$remaining = $timeLimit - (time() - $_SESSION['start_time']);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Escape Room</title>

<style>
body {
  margin: 0;
  font-family: Arial;
  background: black;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
.escape-room, .ending {
  background: #1a0505;
  padding: 25px;
  border-radius: 10px;
  text-align: center;
}
input, button {
  margin: 5px;
  padding: 10px;
}
#timer {
  color: red;
  font-size: 20px;
}
</style>

<script>
let timeLeft = <?php echo max(0, $remaining); ?>;

function updateTimer() {
    document.getElementById("timer").innerText = "Tijd: " + timeLeft + "s";

    if (timeLeft <= 0) {
        window.location.href = "?status=lost";
    }

    timeLeft--;
}
setInterval(updateTimer, 1000);
</script>

</head>
<body>

<?php if ($status === "win"): ?>
    <div class="ending">
        <h1>Gefeliciteerd!</h1>
        <form method="POST"><button name="reset">Opnieuw</button></form>
    </div>

<?php elseif ($status === "lost"): ?>
    <div class="ending">
        <h1>Helaas, je hebt verloren!</h1>
        <form method="POST"><button name="reset">Opnieuw</button></form>
    </div>

<?php else: ?>
    <div class="escape-room">
        <div id="timer"></div>

        <h2>Vraag <?php echo $current + 1; ?></h2>
        <p><?php echo $riddles[$current]['question']; ?></p>

        <form method="POST">
            <input type="text" name="answer" required>
            <br>
            <button type="submit" name="submit">Submit</button> <!-- FIX -->
            <button type="submit" name="hint">Hint</button>
            <button type="submit" name="reset">Reset</button>
        </form>

        <p>
        <?php
        if (!empty($hintText)) {
            echo "💡 Hint: " . $hintText;
        } else {
            echo "Klik op 'Hint' voor hulp";
        }
        ?>
        </p>
    </div>
<?php endif; ?>

</body>
</html>