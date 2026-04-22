(function () {
  'use strict';

  var SIGIL_PLAY_STORAGE_KEY = 'room1_sigil_preview_count_v1';

  var state = {
    cipher: false,
    sigil: false,
    password: false
  };

  var nextButton = document.getElementById('roomOneNextButton');
  var exitSection = document.getElementById('roomOneExit');
  var exitText = document.getElementById('roomOneExitText');
  var cipherHintPanel = document.getElementById('cipherHintPanel');
  var morseHintPanel = document.getElementById('morseHintPanel');
  var room1Token = document.body && document.body.dataset ? document.body.dataset.room1Token : '';
  var room1CompletionSent = false;
  var sigilStatus = document.getElementById('sigilStatus');
  var stageNodes = {
    cipher: document.getElementById('stage-cipher'),
    sigil: document.getElementById('stage-sigil'),
    password: document.getElementById('stage-password')
  };
  var stageLockNotes = {
    cipher: document.getElementById('locknote-cipher'),
    sigil: document.getElementById('locknote-sigil'),
    password: document.getElementById('locknote-password')
  };
  var sigilRunes = Array.prototype.slice.call(document.querySelectorAll('.sigilRune'));
  var sigilPattern = ['b4', 'b1', 'b7', 'b2', 'b9', 'b3', 'b8', 'b5', 'b6'];
  var sigilQueue = [];
  var sigilFailures = 0;
  var sigilPlayback = false;
  var sigilPlayCount = getPersistedSigilPlayCount();

  function getPersistedSigilPlayCount() {
    var raw = window.localStorage.getItem(SIGIL_PLAY_STORAGE_KEY);
    var parsed = Number(raw);

    if (!Number.isFinite(parsed) || parsed < 0) {
      return 0;
    }

    return Math.floor(parsed);
  }

  function persistSigilPlayCount() {
    window.localStorage.setItem(SIGIL_PLAY_STORAGE_KEY, String(sigilPlayCount));
  }

  function normalize(value) {
    return String(value || '')
      .toLowerCase()
      .replace(/[^a-z0-9\s]/g, '')
      .replace(/\s+/g, ' ')
      .trim();
  }

  function setFeedback(id, message, ok) {
    var node = document.getElementById(id);
    if (!node) {
      return;
    }

    node.textContent = message;
    node.style.color = ok ? '#b8f5c6' : '#f0c3a8';
  }

  function updateSigilStatus() {
    if (!sigilStatus) {
      return;
    }

    var integrity = Math.max(0, 100 - sigilFailures * 15);
    sigilStatus.textContent =
      'Sigil integrity: ' + integrity + '% | Failures: ' + sigilFailures + ' | Input: ' + sigilQueue.length + '/9 | Previews used: ' + sigilPlayCount;
  }

  function markSolved(trialKey) {
    state[trialKey] = true;
    var node = document.querySelector('.trialNode[data-trial="' + trialKey + '"]');
    if (node) {
      node.classList.add('trialSolved');
    }

    if (trialKey === 'cipher') {
      setStageLocked('sigil', false);
    }

    if (trialKey === 'sigil') {
      setStageLocked('password', false);
    }

    refreshStageStates();
    updateExit();
  }

  function setStageLocked(stageKey, locked) {
    var node = stageNodes[stageKey];
    if (!node) {
      return;
    }

    node.classList.toggle('isLocked', locked);
    node.setAttribute('aria-disabled', locked ? 'true' : 'false');

    refreshStageStates();
  }

  function isStageLocked(stageKey) {
    var node = stageNodes[stageKey];
    return !!(node && node.classList.contains('isLocked'));
  }

  function defaultLockedText(stageKey) {
    if (stageKey === 'sigil') {
      return 'Stage sealed. Complete Stage 1 to awaken this chamber.';
    }

    if (stageKey === 'password') {
      return 'Stage sealed. Complete Stage 2 to unlock this forge.';
    }

    return 'Stage active. Decode the omen to continue.';
  }

  function refreshStageStates() {
    var stageKeys = ['cipher', 'sigil', 'password'];

    for (var i = 0; i < stageKeys.length; i += 1) {
      var key = stageKeys[i];
      var node = stageNodes[key];
      if (!node) {
        continue;
      }

      var progressLocked = isStageLocked(key);
      var controls = node.querySelectorAll('input, button');
      var shouldDisable = progressLocked || state[key] === true;

      for (var j = 0; j < controls.length; j += 1) {
        controls[j].disabled = shouldDisable;
      }

      var lockNote = stageLockNotes[key];
      if (!lockNote) {
        continue;
      }

      if (state[key] === true) {
        lockNote.textContent = 'Stage complete. Seal neutralized.';
      } else if (progressLocked) {
        lockNote.textContent = defaultLockedText(key);
      } else {
        lockNote.textContent = key === 'cipher'
          ? 'Stage active. Decode the omen to continue.'
          : 'Stage active. The seal is listening.';
      }
    }

    updateSigilStatus();
  }

  function allSolved() {
    return state.cipher && state.sigil && state.password;
  }

  function updateExit() {
    if (!nextButton || !exitSection) {
      return;
    }

    if (allSolved()) {
      nextButton.classList.add('isReady');
      nextButton.setAttribute('aria-disabled', 'false');
      exitSection.classList.add('isReady');
      if (exitText) {
        exitText.textContent = 'The containment field is down. The corridor to Room 2 is open.';
      }
      if (!room1CompletionSent && room1Token) {
        room1CompletionSent = true;
        fetch('/EpsteinIslandEscapers/rooms/complete_room1.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ token: room1Token })
        }).catch(function () {
          room1CompletionSent = false;
        });
      }
      return;
    }

    nextButton.classList.remove('isReady');
    nextButton.setAttribute('aria-disabled', 'true');
    exitSection.classList.remove('isReady');
    if (exitText) {
      exitText.textContent = 'Complete all 3 trials to open the path to Room 2.';
    }
  }

  function tryCipher() {
    if (state.cipher) {
      setFeedback('feedback-cipher', 'Cipher already decoded.', true);
      return;
    }

    var value = normalize(document.getElementById('trial-cipher').value);

    if (value === 'reality starts to bleed' || value === 'realitystartstobleed') {
      setFeedback('feedback-cipher', 'Cipher accepted. Omen identified.', true);
      markSolved('cipher');
      return;
    }

    setFeedback('feedback-cipher', 'Wrong omen interpretation. Try again.', false);
  }

  function toggleCipherHint() {
    var hintButton = document.querySelector('[data-action="toggle-cipher-hint"]');

    toggleHintPanel(cipherHintPanel, hintButton);
  }

  function toggleMorseHint() {
    var hintButton = document.querySelector('[data-action="toggle-morse-hint"]');
    toggleHintPanel(morseHintPanel, hintButton);
  }

  function toggleHintPanel(panel, button) {
    if (!panel || !button) {
      return;
    }

    var shouldOpen = panel.hasAttribute('hidden');

    if (shouldOpen) {
      panel.removeAttribute('hidden');
      button.textContent = 'Hide Hint';
      button.setAttribute('aria-expanded', 'true');
      return;
    }

    panel.setAttribute('hidden', 'hidden');
    button.textContent = 'Show Hint';
    button.setAttribute('aria-expanded', 'false');
  }

  function clearSigilQueue(message, ok) {
    sigilQueue = [];

    for (var i = 0; i < sigilRunes.length; i += 1) {
      sigilRunes[i].classList.remove('sigilActive');
    }

    if (message) {
      setFeedback('feedback-sigil', message, ok);
    }

    updateSigilStatus();
  }

  function handleSigilClick(rune) {
    if (state.sigil) {
      return;
    }

    if (isStageLocked('sigil')) {
      setFeedback('feedback-sigil', 'Stage 2 is locked. Complete Stage 1 first.', false);
      return;
    }

    if (sigilPlayback) {
      setFeedback('feedback-sigil', 'Ritual pulse active. Watch first.', false);
      return;
    }

    if (sigilQueue.length >= sigilPattern.length) {
      clearSigilQueue('', false);
      setFeedback('feedback-sigil', 'Over-input detected. Start ritual again.', false);
      return;
    }

    sigilQueue.push(rune);

    var currentRuneButton = eventTargetRuneButton(rune);
    if (currentRuneButton) {
      currentRuneButton.classList.add('sigilActive');
    }

    setFeedback('feedback-sigil', 'Input recorded. Submit order when ready.', true);
    updateSigilStatus();
  }

  function submitSigilOrder() {
    if (state.sigil) {
      setFeedback('feedback-sigil', 'Sigil already disarmed.', true);
      return;
    }

    if (isStageLocked('sigil')) {
      setFeedback('feedback-sigil', 'Stage 2 is locked. Complete Stage 1 first.', false);
      return;
    }

    if (sigilPlayback) {
      setFeedback('feedback-sigil', 'Wait for ritual pulse to end.', false);
      return;
    }

    if (sigilQueue.length !== sigilPattern.length) {
      setFeedback('feedback-sigil', 'You must submit exactly 9 blocks.', false);
      return;
    }

    for (var i = 0; i < sigilPattern.length; i += 1) {
      if (sigilQueue[i] !== sigilPattern[i]) {
        sigilFailures += 1;
        clearSigilQueue('', false);
        setFeedback('feedback-sigil', 'Wrong order. Ritual reset. Build from zero.', false);
        return;
      }
    }

    setFeedback('feedback-sigil', 'Route repeated perfectly. Sigil disarmed.', true);
    markSolved('sigil');
  }

  function eventTargetRuneButton(rune) {
    return document.querySelector('.sigilRune[data-rune="' + rune + '"]');
  }

  function flashRune(rune, delayMs) {
    var button = eventTargetRuneButton(rune);
    if (!button) {
      return;
    }

    window.setTimeout(function () {
      button.classList.add('sigilFlash');
      window.setTimeout(function () {
        button.classList.remove('sigilFlash');
      }, 170);
    }, delayMs);
  }

  function startSigilPlayback() {
    if (state.sigil) {
      return;
    }

    if (isStageLocked('sigil')) {
      setFeedback('feedback-sigil', 'Stage 2 is locked. Complete Stage 1 first.', false);
      return;
    }

    clearSigilQueue('', true);
    sigilPlayback = true;
    sigilPlayCount += 1;
    persistSigilPlayCount();
    setFeedback('feedback-sigil', 'Observe the pulse.', true);
    updateSigilStatus();

    for (var i = 0; i < sigilPattern.length; i += 1) {
      flashRune(sigilPattern[i], 230 * i);
    }

    window.setTimeout(function () {
      sigilPlayback = false;
      setFeedback('feedback-sigil', 'Repeat the route now.', true);
    }, 230 * sigilPattern.length + 150);
  }

  function buildForgedCode() {
    var sigilNames = ['horn', 'eye', 'thorn', 'moon', 'veil'];
    var baseDigits = [4, 3, 5, 4, 4];
    var omenWord = 'storm';
    var omenShift = [];

    for (var i = 0; i < omenWord.length; i += 1) {
      var alpha = omenWord.charCodeAt(i) - 96;
      var reduced = Math.floor(alpha / 10) + (alpha % 10);
      omenShift.push(reduced % 10);
    }

    var forged = [];
    for (var j = 0; j < baseDigits.length; j += 1) {
      forged.push((baseDigits[j] + omenShift[j]) % 10);
    }

    return {
      sigilNames: sigilNames,
      baseDigits: baseDigits,
      omenShift: omenShift,
      code: forged.join('')
    };
  }

  function tryPassword() {
    if (state.password) {
      setFeedback('feedback-password', 'Code lock already opened.', true);
      return;
    }

    if (isStageLocked('password')) {
      setFeedback('feedback-password', 'Stage 3 is locked. Complete Stage 2 first.', false);
      return;
    }

    var raw = String(document.getElementById('trial-password').value || '');
    var digits = raw.replace(/[^0-9]/g, '');
    var forged = buildForgedCode();

    if (digits === forged.code) {
      setFeedback('feedback-password', 'Iron lock released. Trial complete.', true);
      markSolved('password');
      return;
    }

    setFeedback('feedback-password', 'Wrong forge code. Try again.', false);
  }

  function handleButton(event) {
    var action = event.target && event.target.getAttribute('data-action');

    if (action === 'solve-cipher') {
      tryCipher();
      return;
    }

    if (action === 'toggle-cipher-hint') {
      toggleCipherHint();
      return;
    }

    if (action === 'toggle-morse-hint') {
      toggleMorseHint();
      return;
    }

    if (action === 'clear-sigil') {
      clearSigilQueue('Queue cleared.', false);
      return;
    }

    if (action === 'start-sigil') {
      startSigilPlayback();
      return;
    }

    if (action === 'submit-sigil') {
      submitSigilOrder();
      return;
    }

    if (action === 'solve-password') {
      tryPassword();
    }
  }

  document.addEventListener('click', function (event) {
    if (event.target && event.target.classList.contains('trialButton')) {
      handleButton(event);
    }

    if (event.target && event.target.classList.contains('sigilRune')) {
      handleSigilClick(event.target.getAttribute('data-rune'));
    }

    if (event.target === nextButton && !allSolved()) {
      event.preventDefault();
      setFeedback('feedback-password', 'Containment still active. Solve all trials first.', false);
    }
  });

  setStageLocked('sigil', true);
  setStageLocked('password', true);
  refreshStageStates();
  updateExit();
})();
