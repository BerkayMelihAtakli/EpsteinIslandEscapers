<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cultUnlocked = !empty($_SESSION['cult_unlocked']);

include 'includes/header.php';
include 'includes/nav.php';
?>
<section class="firstSection">

</section>
<section class="secondSection">
    <div class="secondSectionContainer">
        <h2 class="secondInfoTitle">Forgotten Whispers</h2>
        <p class="secondInfoText">A quiet village hides old secrets and strange lights beyond the shore.</p>
        <img src="/EpsteinIslandEscapers/assets/Misty island in a foggy sea.png" alt="Misty island in a foggy sea"
            class="secondInfoImage">
        <p class="secondInfoTextLong">Fog rolls over silent docks while distant bells echo through the night, and every
            narrow path leads wanderers toward forgotten ruins, hidden symbols, and unanswered names.</p>
    </div>
</section>
<section class="joinSection" id="cult-riddle">
    <div class="joinSectionContainer">
        <article class="captureFrame">
            <p class="captureLabel">ABDUCTION LOG // RED TIDE ISLAND</p>
            <h2 class="captureTitle">Kidnapped. Trapped. Watched.</h2>
            <p class="captureText">You were taken to an island ruled by psychopaths. Every corridor hides a riddle. Every mistake feeds a demonic ritual. There is only one rule left: move forward before they choose you for the circle.</p>
            <div class="captureChips" aria-hidden="true">
                <span>No rescue</span>
                <span>No mercy</span>
                <span>No way back</span>
            </div>
        </article>

        <aside class="joinRiddleCard" aria-label="Ritual riddle"
            data-unlocked="<?php echo $cultUnlocked ? '1' : '0'; ?>"
            data-join-url="/EpsteinIslandEscapers/rooms/room_1.php">
            <p class="riddleEyebrow">Ritual Lock</p>
            <h3 class="riddleTitle">One answer opens the gate.</h3>
            <p class="riddleRule">Answer the riddle correctly to unlock entry to the cult.</p>
            <p class="riddlePrompt">I am carved, not alive. I carry power, not blood. Psychopaths kneel when I appear. What am I?</p>
            <label for="riddleInput" class="riddleLabel">Your answer</label>
            <input id="riddleInput" class="riddleInput" type="text" autocomplete="off" spellcheck="false" placeholder="Type one word...">
            <button type="button" class="riddleSubmit">Unlock</button>
            <p class="riddleFeedback" aria-live="polite"></p>
        </aside>
    </div>
</section>


<?php include 'includes/footer.php'; ?>