(function () {
  'use strict';

  var modal = document.getElementById('create-team-modal');
  var form = document.getElementById('create-team-form');
  var submit = document.getElementById('create-team-submit');
  var errorBox = document.getElementById('team-modal-errors');
  var errorList = document.getElementById('team-modal-error-list');
  var triggers = document.querySelectorAll('.createTeamTrigger');
  var closeButtons = document.querySelectorAll('[data-close-team-modal]');
  var joinCultLinks = document.querySelectorAll('a[href*="#cult-riddle"]');
  var reviewsTriggers = document.querySelectorAll('.reviewsTrigger');
  var reviewModal = document.getElementById('review-modal');

  if (!modal || !form) {
    return;
  }

  function clearErrors() {
    if (!errorBox || !errorList) {
      return;
    }

    errorList.innerHTML = '';
    errorBox.hidden = true;
  }

  function showErrors(errors) {
    if (!errorBox || !errorList) {
      return;
    }

    errorList.innerHTML = '';

    for (var i = 0; i < errors.length; i += 1) {
      var item = document.createElement('li');
      item.textContent = errors[i];
      errorList.appendChild(item);
    }

    errorBox.hidden = false;
  }

  function openModal() {
    clearErrors();

    if (reviewModal && reviewModal.classList.contains('is-open')) {
      reviewModal.classList.remove('is-open');
      reviewModal.setAttribute('aria-hidden', 'true');
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('team-modal-open');

    var firstField = document.getElementById('modal_team_name');
    if (firstField) {
      window.setTimeout(function () {
        firstField.focus();
      }, 60);
    }
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('team-modal-open');
  }

  for (var i = 0; i < triggers.length; i += 1) {
    triggers[i].addEventListener('click', function (event) {
      event.preventDefault();

      if (modal.classList.contains('is-open')) {
        closeModal();
        return;
      }

      openModal();
    });
  }

  for (var j = 0; j < closeButtons.length; j += 1) {
    closeButtons[j].addEventListener('click', function () {
      closeModal();
    });
  }

  for (var k = 0; k < joinCultLinks.length; k += 1) {
    joinCultLinks[k].addEventListener('click', function () {
      if (modal.classList.contains('is-open')) {
        closeModal();
      }
    });
  }

  for (var m = 0; m < reviewsTriggers.length; m += 1) {
    reviewsTriggers[m].addEventListener('click', function () {
      if (modal.classList.contains('is-open')) {
        closeModal();
      }
    });
  }

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && modal.classList.contains('is-open')) {
      closeModal();
    }
  });

  if (window.location.hash === '#create-team') {
    openModal();
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    clearErrors();

    if (submit) {
      submit.disabled = true;
      submit.textContent = 'Creating...';
    }

    var formData = new FormData(form);

    fetch('/EpsteinIslandEscapers/create_team.php', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: formData
    })
      .then(function (response) {
        return response.json().catch(function () {
          return { success: false, errors: ['Unexpected server response.'] };
        });
      })
      .then(function (payload) {
        if (payload && payload.success) {
          window.location.href = payload.redirect || '/EpsteinIslandEscapers/rooms/room_1.php';
          return;
        }

        showErrors((payload && payload.errors) || ['Could not create team right now.']);
      })
      .catch(function () {
        showErrors(['Could not create team right now.']);
      })
      .finally(function () {
        if (submit) {
          submit.disabled = false;
          submit.textContent = 'Create Team';
        }
      });
  });
})();
