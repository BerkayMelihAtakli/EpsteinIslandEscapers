(function () {
  'use strict'; 

  var riddleCard = document.querySelector('.joinRiddleCard');
  var riddleSection = document.getElementById('cult-riddle');
  if (!riddleCard) {
    return;
  }

  var input = riddleCard.querySelector('.riddleInput');
  var submit = riddleCard.querySelector('.riddleSubmit');
  var feedback = riddleCard.querySelector('.riddleFeedback');
  var joinUrl = riddleCard.dataset.joinUrl || '/EpsteinIslandEscapers/rooms/room_1.php';
  var defaultSubmitText = 'Unlock';
  var unlockedSubmitText = 'Join the Cult';

  if (!input || !submit || !feedback) {
    return;
  }

  var isUnlocked = riddleCard.dataset.unlocked === '1';
  submit.textContent = isUnlocked ? unlockedSubmitText : defaultSubmitText;

  var unlockEndpoint = '/EpsteinIslandEscapers/unlock_cult.php';

  function easeInOutCubic(t) {
    return t < 0.5
      ? 4 * t * t * t
      : 1 - Math.pow(-2 * t + 2, 3) / 2;
  }

  function slowScrollToRiddle(duration) {
    if (!riddleSection) {
      return;
    }

    var nav = document.querySelector('nav');
    var navOffset = nav ? nav.offsetHeight : 0;
    var startY = window.scrollY || window.pageYOffset;
    var targetY = Math.max(
      0,
      riddleSection.getBoundingClientRect().top + startY - navOffset + 4
    );
    var distance = targetY - startY;

    if (Math.abs(distance) < 2) {
      return;
    }

    var startTime = null;

    function step(timestamp) {
      if (startTime === null) {
        startTime = timestamp;
      }

      var elapsed = timestamp - startTime;
      var progress = Math.min(elapsed / duration, 1);
      var eased = easeInOutCubic(progress);

      window.scrollTo(0, startY + distance * eased);

      if (progress < 1) {
        window.requestAnimationFrame(step);
      }
    }

    window.requestAnimationFrame(step);
  }

  function bindSlowAnchorScroll() {
    var links = document.querySelectorAll('a[href*="#cult-riddle"]');

    for (var i = 0; i < links.length; i += 1) {
      links[i].addEventListener('click', function (event) {
        var link = event.currentTarget;
        var parsed = new URL(link.href, window.location.href);

        if (parsed.pathname !== window.location.pathname || parsed.hash !== '#cult-riddle') {
          return;
        }

        event.preventDefault();
        slowScrollToRiddle(1900);

        if (window.location.hash !== '#cult-riddle') {
          window.history.replaceState(null, '', '#cult-riddle');
        }
      });
    }
  }

  function normalize(value) {
    return value
      .toLowerCase()
      .replace(/[^a-z0-9\s]/g, '')
      .replace(/\s+/g, ' ')
      .trim();
  }

  function unlockIfCorrect() {
    if (isUnlocked) {
      window.location.href = joinUrl;
      return;
    }

    var attempt = normalize(input.value);

    if (!attempt) {
      feedback.textContent = 'Type your answer first.';
      feedback.style.color = '#f0c3a8';
      return;
    }

    submit.disabled = true;
    submit.textContent = '...';

    fetch(unlockEndpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ answer: attempt })
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('unlock request failed');
        }

        return response.json();
      })
      .then(function (payload) {
        if (payload && payload.unlocked === true) {
          isUnlocked = true;
          riddleCard.dataset.unlocked = '1';
          feedback.textContent = payload.message || 'The lock yields. You may enter the ritual.';
          feedback.style.color = '#b8f5c6';
          submit.textContent = 'Entering...';
          submit.disabled = true;
          window.setTimeout(function () {
            window.location.href = joinUrl;
          }, 450);
          return;
        }

        feedback.textContent = payload && payload.message
          ? payload.message
          : 'Wrong answer. The chamber stays closed.';
        feedback.style.color = '#f0c3a8';
      })
      .catch(function () {
        feedback.textContent = 'Could not verify answer right now. Try again.';
        feedback.style.color = '#f0c3a8';
      })
      .finally(function () {
        submit.disabled = false;
        submit.textContent = isUnlocked ? unlockedSubmitText : defaultSubmitText;
      });
  }

  submit.addEventListener('click', unlockIfCorrect);
  input.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      unlockIfCorrect();
    }
  });

  bindSlowAnchorScroll();
})();
