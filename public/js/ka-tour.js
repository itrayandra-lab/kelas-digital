/* Guided tours on top of Driver.js (same pattern as the RayLife store).
   - Only steps whose element is visible are shown, so one step list works on desktop and phone.
   - A tour opens automatically once per browser; ?tur=1 in the URL replays it.
   - KATour.page() also adds a floating "Panduan" button so the tour can be replayed any time. */
(function (w, d) {
  'use strict';

  var factory = w.driver && w.driver.js && w.driver.js.driver;
  var PREFIX = 'ka_tour_v1_';

  function seen(key) {
    try { return w.localStorage.getItem(PREFIX + key) === '1'; } catch (e) { return false; }
  }

  function mark(key) {
    try { w.localStorage.setItem(PREFIX + key, '1'); } catch (e) { /* storage unavailable */ }
  }

  function isVisible(el) {
    if (!el) { return false; }
    var rect = el.getBoundingClientRect();
    var style = w.getComputedStyle(el);
    return rect.width > 0 && rect.height > 0 && style.visibility !== 'hidden' && style.display !== 'none';
  }

  function usable(steps) {
    return steps.filter(function (step) {
      if (!step.element) { return true; }
      return isVisible(d.querySelector(step.element));
    });
  }

  function start(key, steps) {
    if (!factory) { return; }
    var list = usable(steps);
    if (!list.length) { return; }

    factory({
      steps: list,
      showProgress: true,
      progressText: '{{current}} dari {{total}}',
      nextBtnText: 'Lanjut',
      prevBtnText: 'Kembali',
      doneBtnText: 'Selesai',
      allowClose: true,
      overlayOpacity: 0.6,
      stagePadding: 6,
      stageRadius: 10,
      smoothScroll: true,
      popoverClass: 'ka-tour',
      onDestroyed: function () { mark(key); }
    }).drive();
  }

  function auto(key, steps, delay) {
    var forced = /[?&]tur=1(&|$)/.test(w.location.search);
    if (!forced && seen(key)) { return; }
    w.setTimeout(function () { start(key, steps); }, delay || 800);
  }

  function page(key, steps, opts) {
    var btn = d.createElement('button');
    btn.type = 'button';
    btn.className = 'ka-tour-btn';
    btn.innerHTML = '<span aria-hidden="true">?</span> Panduan';
    btn.addEventListener('click', function () { start(key, steps); });
    d.body.appendChild(btn);
    if (!opts || opts.auto !== false) { auto(key, steps); }
  }

  w.KATour = { start: start, auto: auto, page: page };
})(window, document);
