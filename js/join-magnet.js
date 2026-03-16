(function () {
  'use strict';

  var joinSection = document.querySelector('.joinSection');
  if (!joinSection) {
    return;
  }

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return;
  }

  // Keep touch scrolling natural; use magnetic snap for precise wheel/trackpad scroll.
  if (window.matchMedia('(pointer: coarse)').matches) {
    return;
  }

  var isSnapping = false;
  var lastSnapAt = 0;
  var lastY = window.scrollY || window.pageYOffset;
  var SNAP_COOLDOWN_MS = 1200;

  function isNearJoinSection(rect, direction) {
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;
    var nearFromTop = direction >= 0 && rect.top < viewportHeight * 0.36 && rect.top > -viewportHeight * 0.08;
    var nearFromBottom = direction < 0 && rect.bottom > viewportHeight * 0.64 && rect.bottom < viewportHeight * 1.08;

    return nearFromTop || nearFromBottom;
  }

  function snapToJoinSection() {
    var now = Date.now();

    if (isSnapping || now - lastSnapAt < SNAP_COOLDOWN_MS) {
      return;
    }

    isSnapping = true;
    lastSnapAt = now;

    joinSection.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });

    window.setTimeout(function () {
      isSnapping = false;
    }, 750);
  }

  function onScroll() {
    var currentY = window.scrollY || window.pageYOffset;
    var direction = currentY >= lastY ? 1 : -1;
    lastY = currentY;

    var rect = joinSection.getBoundingClientRect();
    if (isNearJoinSection(rect, direction)) {
      snapToJoinSection();
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
})();
