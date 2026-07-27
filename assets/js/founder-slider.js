/**
 * Founder Slider pagination and navigation script.
 */
(function () {
  'use strict';

  var total = (typeof skFounderData !== 'undefined' && skFounderData.total) ? parseInt(skFounderData.total, 10) : 0;
  if (!total) return;

  var current   = 0;
  var animating = false;
  var animTimer = null;

  var thumbs = document.querySelectorAll('#sk-fts-thumbs .sk-fts-thumb');
  var photos = document.querySelectorAll('#sk-fts-photo-stage .sk-fts-photo-frame');
  var panels = document.querySelectorAll('#sk-fts-text-stage .sk-fts-panel');
  var curEl  = document.getElementById('sk-fts-cur');

  function unlock() {
    animating = false;
    clearTimeout(animTimer);
  }

  function goTo(idx, dir) {
    if (animating) return;
    idx = ((idx % total) + total) % total;
    if (idx === current) return;

    animating = true;
    clearTimeout(animTimer);
    animTimer = setTimeout(unlock, 400);

    var leaving = current;
    current = idx;

    if (thumbs[leaving]) thumbs[leaving].classList.remove('is-active');
    if (thumbs[current]) thumbs[current].classList.add('is-active');

    if (curEl) curEl.textContent = String(current + 1).padStart(2, '0');

    var outPhoto = photos[leaving];
    if (outPhoto) {
      outPhoto.classList.add(dir === 'next' ? 'is-leaving-up' : 'is-leaving-down');
      outPhoto.addEventListener('animationend', function onEnd() {
        outPhoto.removeEventListener('animationend', onEnd);
        outPhoto.classList.remove('is-active', 'is-leaving-up', 'is-leaving-down');
      }, { once: true });
    }

    var inPhoto = photos[current];
    if (inPhoto) {
      inPhoto.classList.add(dir === 'next' ? 'is-entering-up' : 'is-entering-down');
      inPhoto.classList.add('is-active');
      inPhoto.addEventListener('animationend', function onEnd() {
        inPhoto.removeEventListener('animationend', onEnd);
        inPhoto.classList.remove('is-entering-up', 'is-entering-down');
        unlock();
      }, { once: true });
    } else {
      unlock();
    }

    var outPanel = panels[leaving];
    if (outPanel) {
      outPanel.classList.add('is-exiting');
      setTimeout(function () {
        outPanel.classList.remove('is-active', 'is-exiting');
      }, 320);
    }

    var inPanel = panels[current];
    if (inPanel) {
      setTimeout(function () {
        inPanel.classList.add('is-active');
        inPanel.classList.remove('is-entering');
      }, 60);
    }
  }

  thumbs.forEach(function (thumb) {
    thumb.addEventListener('click', function () {
      var idx = parseInt(thumb.dataset.slide, 10);
      goTo(idx, idx > current ? 'next' : 'prev');
    });
  });

  var touchStartX = 0;
  var touchStartY = 0;

  function onTouchStart(e) {
    touchStartX = e.touches[0].clientX;
    touchStartY = e.touches[0].clientY;
  }

  function onTouchMove(e) { }

  function onTouchEnd(e) {
    var dx = e.changedTouches[0].clientX - touchStartX;
    var dy = e.changedTouches[0].clientY - touchStartY;
    if (Math.abs(dx) > 30 && Math.abs(dx) > Math.abs(dy)) {
      goTo(dx < 0 ? current + 1 : current - 1, dx < 0 ? 'next' : 'prev');
    }
  }

  var swipeEls = [
    document.getElementById('sk-fts'),
    document.getElementById('sk-fts-photo-stage')
  ].filter(Boolean);

  swipeEls.forEach(function (el) {
    el.addEventListener('touchstart',  onTouchStart,  { passive: true });
    el.addEventListener('touchmove',   onTouchMove,   { passive: true });
    el.addEventListener('touchend',    onTouchEnd,    { passive: true });
  });

  document.addEventListener('keydown', function (e) {
    var fts = document.getElementById('sk-fts');
    if (!fts) return;
    var r = fts.getBoundingClientRect();
    if (r.bottom < 0 || r.top > window.innerHeight) return;
    if (e.key === 'ArrowLeft')  goTo(current - 1, 'prev');
    if (e.key === 'ArrowRight') goTo(current + 1, 'next');
  });
})();
