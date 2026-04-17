<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (empty($_SESSION['cult_unlocked'])) {
  header('Location: /EpsteinIslandEscapers/index.php#cult-riddle');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Room 1 - Awakening</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body class="roomOneBody">
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
  <main class="roomOneMain">
    <div class="siteMorseDust" aria-hidden="true">
      <span>....-</span>
      <span>.....</span>
      <span>.----</span>
      <span>...--</span>
      <span>---..</span>
    </div>

    <header class="roomOneHeader">
      <p class="roomOneEyebrow">Room 1 // Shore of Red Tide</p>
      <h1 class="roomOneTitle">You wake up on the island.</h1>
      <p class="roomOneIntro">Cold tide. Red fog. A bell in the trees. Three ritual stages block your route. Solve each stage and survive the island.</p>
    </header>

    <section class="roomOnePanel roomOneEvidence" aria-label="Items in the area">
      <h2 class="roomOnePanelTitle">Field Evidence</h2>
      <div class="evidenceGrid">
        <article class="evidenceShard">
          <h3>Storm Totem</h3>
          <p>A weather strip carved into wet wood. The omen is hidden in plain sight.</p>
        </article>
        <article class="evidenceShard">
          <h3>Live Sigil Ring</h3>
          <p>The demonic symbol reacts to route memory. One wrong validation triggers a full ward lockdown.</p>
        </article>
        <article class="evidenceShard">
          <h3>Forge Dial</h3>
          <p>The final code is built by overlaying two visual rows and taking last-digit sums.</p>
        </article>
      </div>
    </section>

    <section class="roomOnePanel roomOneChamber" aria-label="Demonic trials">
      <h2 class="roomOnePanelTitle">Containment Chamber - Stage Sequence</h2>
      <div class="stageFlow">
        <section class="trialNode stageSection" data-trial="cipher" id="stage-cipher">
          <p class="stageBadge">Stage 1</p>
          <p class="stageLockNote" id="locknote-cipher">Stage active. Decode the omen to continue.</p>
          <p class="trialType">Enigma</p>
            <h3>Trial 1: Omen Cipher</h3>
          <div class="omenTiles" aria-hidden="true">
              <span class="omenWord">VIEPMXC</span>
              <span class="omenBreak">/</span>
              <span class="omenWord">WXEVXW</span>
              <span class="omenBreak">/</span>
              <span class="omenWord">XS</span>
              <span class="omenBreak">/</span>
              <span class="omenWord">FPIIH</span>
          </div>
            <p class="trialPrompt">Translate the omen signal.</p>
            <input type="text" class="trialInput" id="trial-cipher" placeholder="Decoded omen phrase">
          <button class="trialButton" data-action="solve-cipher" type="button">Decode Omen</button>
          <p class="trialFeedback" id="feedback-cipher" aria-live="polite"></p>
        </section>

        <section class="trialNode stageSection isLocked" data-trial="sigil" id="stage-sigil" aria-disabled="true">
          <p class="stageBadge">Stage 2</p>
          <p class="stageLockNote" id="locknote-sigil">Stage sealed. Complete Stage 1 to awaken this chamber.</p>
          <p class="trialType">Ritual Pattern</p>
          <h3>Trial 2: Sigil Disarm</h3>
          <p class="trialPrompt">Watch the pulse and repeat the route. Maximum previews: 3 total.</p>
          <div class="sigilLock" id="sigilLock" aria-label="Sigil pattern lock">
            <button class="sigilRune" data-rune="b1" type="button">✦</button>
            <button class="sigilRune" data-rune="b2" type="button">☾</button>
            <button class="sigilRune" data-rune="b3" type="button">ᛉ</button>
            <button class="sigilRune" data-rune="b4" type="button">⟁</button>
            <button class="sigilRune" data-rune="b5" type="button">◉</button>
            <button class="sigilRune" data-rune="b6" type="button">✶</button>
            <button class="sigilRune" data-rune="b7" type="button">ᚺ</button>
            <button class="sigilRune" data-rune="b8" type="button">☿</button>
            <button class="sigilRune" data-rune="b9" type="button">ᚨ</button>
          </div>
          <p class="sigilStatus" id="sigilStatus">Sigil stable | Failures: 0 | Input: 0/9 | Previews used: 0/3</p>
          <div class="sigilActions">
            <button class="trialButton" data-action="start-sigil" type="button">Start Ritual</button>
            <button class="trialButton" data-action="submit-sigil" type="button">Submit Order</button>
            <button class="trialButton trialButtonGhost" data-action="clear-sigil" type="button">Reset Input</button>
          </div>
          <p class="trialFeedback" id="feedback-sigil" aria-live="polite"></p>
        </section>

        <section class="trialNode stageSection isLocked" data-trial="password" id="stage-password" aria-disabled="true">
          <p class="stageBadge">Stage 3</p>
          <p class="stageLockNote" id="locknote-password">Stage sealed. Complete Stage 2 to unlock this forge.</p>
          <p class="trialType">Password</p>
          <h3>Trial 3: Final Seal</h3>
          <p class="trialPrompt">Enter the 5-digit forged seal.</p>
          <p class="morseBeacon" aria-label="strange signal">
            Signal drift: <span>....-</span> <span>.....</span> <span>.----</span> <span>...--</span> <span>---..</span>
          </p>
          <input type="text" class="trialInput" id="trial-password" inputmode="numeric" placeholder="#####">
          <button class="trialButton" data-action="solve-password" type="button">Unlock Code</button>
          <p class="trialFeedback" id="feedback-password" aria-live="polite"></p>
        </section>
      </div>
    </section>

    <section class="roomOneExit" id="roomOneExit" aria-live="polite">
      <p class="exitLocked" id="roomOneExitText">Complete all 3 trials to open the path to Room 2.</p>
      <a class="roomOneNextButton" id="roomOneNextButton" href="/EpsteinIslandEscapers/rooms/room_2.php">Enter Room 2</a>
    </section>
  </main>

  <script src="../js/room1-scene.js"></script>

</body>

</html>