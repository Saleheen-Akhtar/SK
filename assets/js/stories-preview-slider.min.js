/**
 * Stories Preview Carousel Slider — v18
 * Handles independent desktop/mobile slider containers and touch swipe triggers.
 */
(function(){
  'use strict';

  // Find all slider containers
  var containers = document.querySelectorAll('.sk-sp-v4-slider-container');
  if (!containers.length) return;

  function initSlider(container) {
    var track = container.querySelector('.sk-sp-v4-track');
    if (!track) return;
    var cards = track.querySelectorAll('.sk-sp-v4-card');
    var btnPrev = container.querySelector('.sk-sp-v4-prev');
    var btnNext = container.querySelector('.sk-sp-v4-next');
    if (!cards.length) return;

    var total = cards.length;
    // Set starting index from data-start-index, defaulting to 0
    var current = parseInt(container.getAttribute('data-start-index') || 0, 10);
    if (current >= total) current = 0;

    function getVisible() {
      if (window.innerWidth <= 1024) return 1; // Tablets/mobiles have 76% wide cards, so 1 card at a time
      if (window.innerWidth <= 1120) return 2;
      // On desktop, each container shows exactly 1 card at a time
      return 1;
    }

    function cardW() {
      var gap = parseInt(getComputedStyle(track).gap || getComputedStyle(track).columnGap || 0, 10);
      return cards[0].offsetWidth + gap;
    }

    function go(idx) {
      var visible = getVisible();
      var maxIdx = Math.max(0, total - visible);
      idx = Math.max(0, Math.min(idx, maxIdx));
      current = idx;
      
      track.style.transform = 'translateX(-' + (current * cardW()) + 'px)';
      
      /* Show/hide arrows based on position */
      if (btnPrev) {
        btnPrev.style.opacity = current === 0 ? '0.35' : '1';
        btnPrev.style.pointerEvents = current === 0 ? 'none' : 'auto';
      }

      if (btnNext) {
        btnNext.style.opacity = current >= maxIdx ? '0.35' : '1';
        btnNext.style.pointerEvents = current >= maxIdx ? 'none' : 'auto';
      }

      // Update dots if this is the active mobile slider (sk-sp-v4-slider-left is the only one visible on mobile)
      if (container.classList.contains('sk-sp-v4-slider-left')) {
        var dots = document.querySelectorAll('.sk-sp-v4-dot');
        dots.forEach(function(dot, i) {
          var on = i === current;
          dot.classList.toggle('active', on);
          dot.setAttribute('aria-selected', on ? 'true' : 'false');
        });
      }
    }

    if (btnPrev) {
      btnPrev.addEventListener('click', function(e){
        e.preventDefault();
        go(current - 1);
      });
    }

    if (btnNext) {
      btnNext.addEventListener('click', function(e){
        e.preventDefault();
        go(current + 1);
      });
    }

    // Connect dots to slider (click listeners)
    if (container.classList.contains('sk-sp-v4-slider-left')) {
      var dots = document.querySelectorAll('.sk-sp-v4-dot');
      dots.forEach(function(dot) {
        if (dot.dataset.listenerAttached) return;
        dot.dataset.listenerAttached = '1';
        dot.addEventListener('click', function(e) {
          e.preventDefault();
          go(parseInt(dot.dataset.dot, 10));
        });
      });
    }

    /* Touch swipe support */
    var touchStartX = 0;
    track.addEventListener('touchstart', function(e){ touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', function(e){
      var dx = e.changedTouches[0].clientX - touchStartX;
      if (Math.abs(dx) > 40) go(dx < 0 ? current + 1 : current - 1);
    }, { passive: true });

    /* Reset on resize */
    window.addEventListener('resize', function(){ go(0); }, { passive: true });

    // Initial positioning
    go(current);
  }

  containers.forEach(initSlider);
})();
