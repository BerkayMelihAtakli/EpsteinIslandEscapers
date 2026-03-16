(function () {
  'use strict';

  // Deterrent only: browser developer tools cannot be fully disabled client-side.
  var blockedCombos = [
    { key: 'F12' },
    { key: 'I', ctrl: true, shift: true },
    { key: 'J', ctrl: true, shift: true },
    { key: 'C', ctrl: true, shift: true },
    { key: 'U', ctrl: true }
  ];

  function matchesCombo(event, combo) {
    return (
      event.key.toUpperCase() === combo.key &&
      !!event.ctrlKey === !!combo.ctrl &&
      !!event.shiftKey === !!combo.shift
    );
  }

  document.addEventListener('contextmenu', function (event) {
    event.preventDefault();
  });

  document.addEventListener('keydown', function (event) {
    for (var i = 0; i < blockedCombos.length; i += 1) {
      if (matchesCombo(event, blockedCombos[i])) {
        event.preventDefault();
        return false;
      }
    }

    return true;
  });
})();
