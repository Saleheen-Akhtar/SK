/**
 * Philosophy Strip (Vedic Astrology) Carousel Slider
 * Handles horizontal slider scrolling and touch swipe triggers.
 */
(function(){
  'use strict';

  function initSlider() {
    var track   = document.getElementById('astrology-track');
    if (!track) return;
    var cards   = track.querySelectorAll('.sk-astrology-card');
    var dots    = document.querySelectorAll('.sk-astrology-dot');
    var btnPrev = document.querySelector('.sk-astrology-prev');
    var btnNext = document.querySelector('.sk-astrology-next');
    if (!cards.length) return;

    var total   = cards.length;
    var current = 0;

    function getVisible() {
      if (window.innerWidth <= 600) return 1;
      return 2;
    }

    function cardW() {
      var gap = parseInt(getComputedStyle(track).gap || getComputedStyle(track).columnGap || 0);
      return cards[0].offsetWidth + gap;
    }

    function go(idx) {
      var visible = getVisible();
      var maxIdx = Math.max(0, total - visible);
      idx = Math.max(0, Math.min(idx, maxIdx));
      current = idx;
      track.style.transform = 'translateX(-' + (current * cardW()) + 'px)';
      
      var page = Math.floor(current / visible);
      dots = document.querySelectorAll('.sk-astrology-dot');
      dots.forEach(function(d, i){
        var on = i === page;
        d.classList.toggle('active', on);
        d.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      
      /* Show/hide arrows based on position */
      if (btnPrev) btnPrev.style.opacity = current === 0 ? '0.35' : '1';
      if (btnNext) btnNext.style.opacity = current >= maxIdx ? '0.35' : '1';
    }

    if (btnPrev) btnPrev.addEventListener('click', function(){ go(current - 1); });
    if (btnNext) btnNext.addEventListener('click', function(){ go(current + 1); });
    
    function initDotListeners() {
      document.querySelectorAll('.sk-astrology-dot').forEach(function(dot){
        if (dot.dataset.listenerAttached) return;
        dot.dataset.listenerAttached = '1';
        dot.addEventListener('click', function(){
          go(parseInt(dot.dataset.dot) * getVisible());
        });
      });
    }

    initDotListeners();

    /* Touch swipe support */
    var touchStartX = 0;
    track.addEventListener('touchstart', function(e){ touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', function(e){
      var dx = e.changedTouches[0].clientX - touchStartX;
      if (Math.abs(dx) > 40) go(dx < 0 ? current + 1 : current - 1);
    }, { passive: true });

    /* Reset on resize */
    window.addEventListener('resize', function(){ go(0); }, { passive: true });

    go(0);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSlider);
  } else {
    initSlider();
  }
})();
