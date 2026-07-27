/**
 * Astrology Preview Carousel Slider
 * Handles desktop/mobile slider scrolling and touch swipe triggers.
 */
(function(){
  'use strict';

  var track   = document.getElementById('astrology-track') || document.querySelector('.sk-astrology-track');
  if (!track) return;
  var cards   = track.querySelectorAll('.sk-astrology-card');
  var btnPrev = document.querySelector('.sk-astrology-prev');
  var btnNext = document.querySelector('.sk-astrology-next');
  if (!cards.length) return;

  var total   = cards.length;
  var current = 0;

  function getVisible() {
    if (window.innerWidth <= 600) return 1;
    if (window.innerWidth <= 1024) return 2;
    return 3;
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
    
    /* Show/hide arrows based on position */
    if (btnPrev) btnPrev.style.opacity = current === 0 ? '0.35' : '1';
    if (btnNext) btnNext.style.opacity = current >= maxIdx ? '0.35' : '1';
  }

  if (btnPrev) btnPrev.addEventListener('click', function(){ go(current - 1); });
  if (btnNext) btnNext.addEventListener('click', function(){ go(current + 1); });

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
})();
