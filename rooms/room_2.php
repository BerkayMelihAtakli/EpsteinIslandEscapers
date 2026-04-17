<?php
session_start();

if (empty($_SESSION['cult_unlocked'])) {
  header('Location: /EpsteinIslandEscapers/index.php#cult-riddle');
  exit;
}

require_once('../admin/question.php');


$room2Riddles = [];
if (isset($question) && is_array($question)) {
  $room2Riddles = array_values(
    array_filter($question, function ($item) {
      return isset($item['roomId']) && (string)$item['roomId'] === '2';
    })
  );
}

if (count($room2Riddles) === 0) {
  die('No riddles found for Room 2 in admin/question.php');
}


if (!isset($_SESSION['room2_phase'])) {
  $_SESSION['room2_phase'] = 'exploration'; 
  $_SESSION['room2_keys_found'] = [];
  $_SESSION['room2_lockers_solved'] = [];
  $_SESSION['room2_attempts'] = 0;
}


$lockers = [
  0 => [
    'name' => 'Rusted Metal Locker A',
    'riddle' => $room2Riddles[0]['riddle'] ?? 'I am light as a feather...',
    'answer' => $room2Riddles[0]['answer'] ?? 'breath',
    'hint' => $room2Riddles[0]['hint'] ?? 'You are doing it right now.',
    'keyColor' => 'bronze'
  ],
  1 => [
    'name' => 'Wooden Cabinet B',
    'riddle' => $room2Riddles[1]['riddle'] ?? 'What has keys but cannot open locks?',
    'answer' => $room2Riddles[1]['answer'] ?? 'piano',
    'hint' => $room2Riddles[1]['hint'] ?? 'You often find it in a living room.',
    'keyColor' => 'silver'
  ],
  2 => [
    'name' => 'Corroded Filing Cabinet C',
    'riddle' => $room2Riddles[2]['riddle'] ?? 'What gets wetter the more it dries?',
    'answer' => $room2Riddles[2]['answer'] ?? 'towel',
    'hint' => $room2Riddles[2]['hint'] ?? 'You use it after showering.',
    'keyColor' => 'gold'
  ],
  3 => [
    'name' => 'Padlocked Trunk D',
    'riddle' => 'I speak without a mouth and hear without ears. I have no body, but I come to life with wind. What am I?',
    'answer' => 'echo',
    'hint' => 'Something that repeats your words back to you.',
    'keyColor' => 'copper'
  ],
  4 => [
    'name' => 'Chained Ammunition Box E',
    'riddle' => 'The more you take, the more you leave behind. What am I?',
    'answer' => 'footsteps',
    'hint' => 'Think about walking or running.',
    'keyColor' => 'iron'
  ]
];

$feedback = "";
$hintText = "";
$currentLocker = isset($_POST['locker_id']) ? (int)$_POST['locker_id'] : -1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
 
  if (isset($_POST['answer']) && $currentLocker >= 0 && $currentLocker < count($lockers)) {
    $input = strtolower(trim($_POST['answer']));
    $correct = strtolower(trim($lockers[$currentLocker]['answer']));
    
    if (!in_array($currentLocker, $_SESSION['room2_lockers_solved'])) {
      if ($input === $correct) {
        $feedback = "✔ Correct! You found a " . $lockers[$currentLocker]['keyColor'] . " key!";
        $_SESSION['room2_lockers_solved'][] = $currentLocker;
        $_SESSION['room2_keys_found'][] = $currentLocker;
        
        // Check if all lockers solved
        if (count($_SESSION['room2_lockers_solved']) === count($lockers)) {
          $_SESSION['room2_phase'] = 'trial';
          $feedback = "✔ All containers unlocked! You have " . count($lockers) . " keys. Now find the right one for the exit door.";
        }
      } else {
        $feedback = "✖ Wrong answer. Try again or ask for a hint.";
      }
    }
  }
  
 
  if (isset($_POST['hint']) && $currentLocker >= 0 && $currentLocker < count($lockers)) {
    $hintText = $lockers[$currentLocker]['hint'];
  }
  
 
  if (isset($_POST['key_attempt']) && isset($_POST['key_choice'])) {
    $keyChoice = (int)$_POST['key_choice'];
    $correctKey = 2; // Gold key (from Corroded Filing Cabinet C) is the correct one
    $_SESSION['room2_attempts']++;
    
    if ($keyChoice === $correctKey && in_array($correctKey, $_SESSION['room2_keys_found'])) {
      $feedback = "✔✔✔ CORRECT! The door opens! The path to deeper truth awaits...";
      $_SESSION['room2_escaped'] = true;
    } elseif (!in_array($keyChoice, $_SESSION['room2_keys_found'])) {
      $feedback = "✖ That key doesn't exist in your collection.";
    } else {
      $feedback = "✖ The key doesn't fit. Try another. (Attempt: " . $_SESSION['room2_attempts'] . ")";
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Room 2 - Island Containment</title>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  min-height: 100vh;
  min-height: 100dvh;
  background:
    radial-gradient(circle at 14% 16%, #8f10103b 0%, transparent 38%),
    radial-gradient(circle at 84% 76%, #f0c3a81a 0%, transparent 46%),
    linear-gradient(170deg, #070202 0%, #130808 52%, #1a110f 100%);
  color: #f3dfcf;
  font-family: Arial, sans-serif;
  padding: 20px;
  overflow-x: hidden;
}

.room-container {
  width: min(100%, 1200px);
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 30px;
}

/* Header Section */
.room-header {
  text-align: center;
  padding: 30px 20px;
  background: linear-gradient(125deg, #190707cf 0%, #100404c2 60%, #0a0303bf 100%);
  border: 1px solid #b11f1f4d;
  border-radius: 8px;
  position: relative;
}

.room-header::before {
  content: '';
  position: absolute;
  inset: -1px;
  background: repeating-linear-gradient(
    90deg,
    transparent,
    transparent 2px,
    rgba(139, 69, 19, 0.2) 2px,
    rgba(139, 69, 19, 0.2) 4px
  );
  border-radius: 8px;
  pointer-events: none;
}

.eyebrow {
  font-size: 12px;
  letter-spacing: 2px;
  color: #f0c3a8;
  text-transform: uppercase;
  margin-bottom: 8px;
}

h1.room-title {
  font-size: 3.5rem;
  color: #ffe7d6;
  text-shadow: 0 0 18px #8f101054;
  margin-bottom: 12px;
  letter-spacing: 1px;
}

.room-intro {
  font-size: 1.1rem;
  color: #f2d6c3;
  line-height: 1.6;
  max-width: 800px;
  margin: 0 auto;
}

/* Exploration Phase */
.exploration-phase {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.locker {
  background: linear-gradient(170deg, #170707f2 0%, #110404f0 64%, #0a0303eb 100%);
  border: 1px solid #8e4c4c80;
  border-radius: 0;
  clip-path: polygon(0 8px, 8px 0, calc(100% - 8px) 0, 100% 8px, 100% calc(100% - 8px), calc(100% - 8px) 100%, 8px 100%, 0 calc(100% - 8px));
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  min-height: 320px;
  display: flex;
  flex-direction: column;
}

.locker:hover {
  background: linear-gradient(170deg, #2a0a0af2 0%, #1a0505f0 64%, #0f0000eb 100%);
  border-color: #f0c3a8;
  box-shadow: 0 0 18px #f0c3a84e;
  transform: translateY(-5px);
}

.locker.solved {
  border-color: #6a8f73;
  background: linear-gradient(170deg, #0a3a1af0 0%, #040a08eb 64%, #020503e8 100%);
  box-shadow: inset 0 0 0 1px #6a8f7342;
}

.locker-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.locker-name {
  font-size: 1.1rem;
  font-weight: bold;
  color: #f0c3a8;
}

.locker-status {
  font-size: 0.85rem;
  padding: 4px 8px;
  border-radius: 3px;
  background: rgba(177, 31, 31, 0.3);
  color: #ffe8d6;
}

.locker.solved .locker-status {
  background: rgba(106, 143, 115, 0.3);
  color: #d6f0dc;
}

.locker-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.riddle-text {
  color: #f0d2bf;
  margin-bottom: 15px;
  line-height: 1.6;
  font-size: 0.95rem;
}

.locker-form {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: auto;
}

.locker-form input {
  padding: 8px 12px;
  background: #1a0505;
  border: 1px solid #b11f1f7f;
  color: #ffe8d6;
  border-radius: 3px;
  font-size: 0.95rem;
}

.locker-form input:focus {
  outline: none;
  border-color: #ffe4d1a8;
  box-shadow: 0 0 0 2px #b11f1f45;
}

.locker-buttons {
  display: flex;
  gap: 8px;
}

button {
  flex: 1;
  padding: 10px;
  background: #2a0707;
  border: 1px solid #8e4c4c;
  color: #efdccc;
  border-radius: 3px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}

button:hover {
  background: #1b0505;
  border-color: #b11f1f;
}

button:active {
  transform: scale(0.98);
}

.locker-feedback {
  margin-top: 10px;
  font-size: 0.9rem;
  min-height: 20px;
  color: #f0d2bf;
}

.locker-feedback.success {
  color: #d6f0dc;
}

.hint-text {
  margin-top: 8px;
  font-size: 0.85rem;
  color: #f0c3a8;
  font-style: italic;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}

.hint-text.show {
  max-height: 100px;
}

.locker.solved {
  pointer-events: none;
  opacity: 0.8;
}

/* Trial Phase - Key Lock */
.trial-phase {
  display: none;
}

.trial-phase.active {
  display: block;
  animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.lock-container {
  max-width: 600px;
  margin: 0 auto;
  background: linear-gradient(125deg, #190707f2 0%, #100404c2 60%, #0a0303bf 100%);
  border: 1px solid #b11f1f4d;
  padding: 40px 30px;
  border-radius: 2px;
  text-align: center;
}

.lock-icon {
  font-size: 3.5rem;
  margin-bottom: 20px;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.lock-title {
  font-size: 2rem;
  color: #ffe3cf;
  margin-bottom: 15px;
  text-transform: uppercase;
  letter-spacing: 2px;
}

.lock-description {
  color: #f0d2bf;
  margin-bottom: 25px;
  line-height: 1.6;
}

.keys-selector {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  gap: 12px;
  margin-bottom: 25px;
  max-height: 300px;
  overflow-y: auto;
}

.key-option {
  padding: 15px;
  background: linear-gradient(180deg, #210808 0%, #150606 100%);
  border: 1px solid #b11f1f70;
  border-radius: 3px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
  color: #f7dac7;
  font-weight: bold;
}

.key-option:hover {
  background: linear-gradient(180deg, #2b0a0a 0%, #190707 100%);
  border-color: #f0c3a8;
  box-shadow: 0 0 18px #f0c3a86e;
}

.key-option input[type="radio"] {
  margin-top: 8px;
}

.lock-feedback {
  margin-top: 20px;
  padding: 15px;
  border-radius: 3px;
  font-size: 1.1rem;
  min-height: 24px;
  color: #f0d2bf;
}

.lock-feedback.success {
  background: linear-gradient(125deg, #0a3a1acf 0%, #050a08c2 60%, #020503bf 100%);
  color: #d6f0dc;
  border: 1px solid #6a8f73;
}

.attempts {
  color: #f0c3a8;
  font-size: 0.9rem;
  margin-top: 15px;
}

.escape-button {
  background: #2a0707 !important;
  border: 1px solid #8e4c4c !important;
  padding: 15px 40px !important;
  font-size: 1rem !important;
  width: 100% !important;
  margin-top: 20px !important;
  color: #efdccc !important;
}

.escape-button:hover {
  background: #1b0505 !important;
  border-color: #b11f1f !important;
  box-shadow: 0 0 12px rgba(177, 31, 31, 0.4) !important;
}

.status-bar {
  background: linear-gradient(125deg, #190707cf 0%, #100404c2 60%, #0a0303bf 100%);
  border: 1px solid #b11f1f4d;
  border-radius: 2px;
  padding: 15px;
  margin-bottom: 20px;
  text-align: center;
  color: #f0c3a8;
}

.phase-indicator {
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.next-room-button {
  background: #2a0707 !important;
  border: 1px solid #8e4c4c !important;
  color: #efdccc !important;
  padding: 15px 40px !important;
  font-size: 1.1rem !important;
  width: 100% !important;
  margin-top: 20px !important;
  text-decoration: none !important;
  display: inline-block !important;
  cursor: pointer !important;
  text-align: center !important;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.next-room-button:hover {
  background: #1b0505 !important;
  border-color: #b11f1f !important;
  box-shadow: 0 0 12px rgba(177, 31, 31, 0.4) !important;
}

@media (max-width: 768px) {
  h1.room-title {
    font-size: 2.5rem;
  }
  
  .exploration-phase {
    grid-template-columns: 1fr;
  }
}

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

<div class="room-container">

  <header class="room-header">
    <p class="eyebrow">Room 2 // Containment Ward</p>
    <h1 class="room-title">The Facility</h1>
    <p class="room-intro">A abandoned containment facility on the island. Rusted lockers hold secrets. Find all the keys hidden within locked containers. Somewhere among them lies the one key that opens the exit door. Stay sharp—time is a luxury you don't have.</p>
  </header>

  <!-- Status Bar -->
  <div class="status-bar">
    <p class="phase-indicator">
      <?php echo ($_SESSION['room2_phase'] === 'exploration') 
        ? '🔓 EXPLORATION PHASE: Unlock ' . count($lockers) . ' containers to find all keys'
        : '🔑 TRIAL PHASE: ' . count($_SESSION['room2_keys_found']) . ' keys collected. Find the correct one for the exit door. (' . $_SESSION['room2_attempts'] . ' attempts)';
      ?>
    </p>
  </div>

  
  <?php if ($_SESSION['room2_phase'] === 'exploration'): ?>
  <div class="exploration-phase">
    <?php foreach ($lockers as $id => $locker): ?>
      <div class="locker <?php echo in_array($id, $_SESSION['room2_lockers_solved']) ? 'solved' : ''; ?>">
        <div class="locker-header">
          <span class="locker-name">📦 <?php echo htmlspecialchars($locker['name']); ?></span>
          <span class="locker-status">
            <?php echo in_array($id, $_SESSION['room2_lockers_solved']) ? '✓ UNLOCKED' : '🔒 LOCKED'; ?>
          </span>
        </div>

        <div class="locker-content">
          <?php if (!in_array($id, $_SESSION['room2_lockers_solved'])): ?>
            <div class="riddle-text"><?php echo htmlspecialchars($locker['riddle']); ?></div>
            
            <form method="POST" class="locker-form">
              <input type="hidden" name="locker_id" value="<?php echo $id; ?>">
              <input type="text" name="answer" placeholder="Your answer...">
              <div class="locker-buttons">
                <button type="submit">Submit Answer</button>
                <button type="submit" name="hint">Hint</button>
              </div>
            </form>
            
            <?php if ($currentLocker === $id): ?>
              <div class="locker-feedback <?php echo (strpos($feedback, '✔') === 0) ? 'success' : ''; ?>">
                <?php echo htmlspecialchars($feedback); ?>
              </div>
              <?php if (!empty($hintText)): ?>
                <div class="hint-text show">💡 Hint: <?php echo htmlspecialchars($hintText); ?></div>
              <?php endif; ?>
            <?php endif; ?>
          <?php else: ?>
            <div style="color: #90ee90; text-align: center; margin-top: 20px;">
              <p style="font-size: 2rem;">✓</p>
              <p>A <strong><?php echo htmlspecialchars($locker['keyColor']); ?></strong> key inside</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  
  <?php if ($_SESSION['room2_phase'] === 'trial'): ?>
  <div class="trial-phase active">
    <div class="lock-container">
      <div class="lock-icon">🔐</div>
      <h2 class="lock-title">EXIT LOCK</h2>
      <p class="lock-description">
        You've opened all the containers and found <?php echo count($_SESSION['room2_keys_found']); ?> keys. Now select which key unlocks the exit door.
      </p>

      <form method="POST">
        <div class="keys-selector">
          <?php foreach ($_SESSION['room2_keys_found'] as $index => $lockerIndex): ?>
            <label class="key-option">
              <input type="radio" name="key_choice" value="<?php echo $lockerIndex; ?>" required>
              <div><?php echo $lockers[$lockerIndex]['keyColor']; ?></div>
              <div style="font-size: 0.8rem; color: #a8956f;">Key #<?php echo $lockerIndex + 1; ?></div>
            </label>
          <?php endforeach; ?>
        </div>

        <button class="escape-button" type="submit" name="key_attempt">Try Key</button>
      </form>

      <?php if (!empty($feedback)): ?>
        <div class="lock-feedback <?php echo (strpos($feedback, '✔') === 0) ? 'success' : ''; ?>">
          <?php echo htmlspecialchars($feedback); ?>
        </div>
      <?php endif; ?>

      <div class="attempts">
        Keys tried: <?php echo $_SESSION['room2_attempts']; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>


  <?php if (!empty($_SESSION['room2_escaped'])): ?>
  <div class="lock-container">
    <div style="font-size: 3rem; margin-bottom: 20px;">✓✓✓</div>
    <h2 class="lock-title">DOOR OPEN</h2>
    <p class="lock-description">
      The heavy door swings open with a metallic groan. You've survived the containment ward. What horrors await in Room 3?
    </p>
    <a href="/EpsteinIslandEscapers/room_3.php" class="next-room-button">Enter Room 3</a>
    <?php unset($_SESSION['room2_phase']); unset($_SESSION['room2_escaped']); ?>
  </div>
  <?php endif; ?>

</div>

</body>
</html>