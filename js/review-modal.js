(function () {
  'use strict';

  var modal = document.getElementById('review-modal');
  var form = document.getElementById('review-form');
  var submit = document.getElementById('review-submit');
  var successBox = document.getElementById('review-modal-success');
  var errorBox = document.getElementById('review-modal-errors');
  var errorList = document.getElementById('review-modal-error-list');
  var openFormButton = document.getElementById('open-review-form');
  var triggers = document.querySelectorAll('.reviewsTrigger');
  var closeButtons = document.querySelectorAll('[data-close-review-modal]');
  var joinCultLinks = document.querySelectorAll('a[href*="#cult-riddle"]');
  var createTeamTriggers = document.querySelectorAll('.createTeamTrigger');
  var createTeamModal = document.getElementById('create-team-modal');

  if (!modal || !form) {
    return;
  }

  function clearMessages() {
    if (errorList) {
      errorList.innerHTML = '';
    }
    if (errorBox) {
      errorBox.hidden = true;
    }
    if (successBox) {
      successBox.hidden = true;
    }
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
    clearMessages();

    if (createTeamModal && createTeamModal.classList.contains('is-open')) {
      createTeamModal.classList.remove('is-open');
      createTeamModal.setAttribute('aria-hidden', 'true');
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('team-modal-open');

    if (form) {
      form.hidden = true;
    }
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');

    if (!createTeamModal || !createTeamModal.classList.contains('is-open')) {
      document.body.classList.remove('team-modal-open');
    }
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

  for (var m = 0; m < createTeamTriggers.length; m += 1) {
    createTeamTriggers[m].addEventListener('click', function () {
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

  if (openFormButton) {
    openFormButton.addEventListener('click', function () {
      if (form) {
        form.hidden = false;
      }

      var firstField = document.getElementById('modal_rating');
      if (firstField) {
        window.setTimeout(function () {
          firstField.focus();
        }, 30);
      }
    });
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    clearMessages();

    if (submit) {
      submit.disabled = true;
      submit.textContent = 'Sending...';
    }

    var formData = new FormData(form);

    fetch('/EpsteinIslandEscapers/submit_review.php', {
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
          if (successBox) {
            successBox.hidden = false;
          }
          form.reset();
          form.hidden = true;
          return;
        }

        showErrors((payload && payload.errors) || ['Could not send review right now.']);
      })
      .catch(function () {
        showErrors(['Could not send review right now.']);
      })
      .finally(function () {
        if (submit) {
          submit.disabled = false;
          submit.textContent = 'Send Review';
        }
      });
  });
})();
