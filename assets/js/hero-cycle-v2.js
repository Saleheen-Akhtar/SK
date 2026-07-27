/**
 * Hero word-cycle animation — v18 (Cinematic Masterpiece - Live Port)
 * Ported faithfully from the live site's premium v17 config:
 *  - FROM word: smooth blur-fade in & dissolve out
 *  - TO word: Cinematic star-zoom — spawns tiny from a truly random position
 *             within hero bounds, travels to center with scale(0.01)→scale(1),
 *             blur(30px)→blur(0), opacity 0→1. Animates as a single solid unit
 *             with dynamic transformOrigin mapping. Pure GSAP.
 *
 * Safe Updates:
 *  - Removed layout-deferred requestAnimationFrame from scaleFont to eliminate GSAP race conditions (coming twice).
 *  - Synchronous GSAP setup and execution for bulletproof thread-safety.
 *  - Closure execution guard to prevent multiple parallel loops on double DOM load.
 *  - Timeout safety cleanup at start of showPair.
 */
(function(){
  'use strict';

  function init() {
    if (window.skHeroCycleV2Initialized) return;
    window.skHeroCycleV2Initialized = true;

    var pairs  = (typeof skHeroData !== 'undefined' && skHeroData.pairs) ? skHeroData.pairs : [];
    var fromEl = document.getElementById('hero-from');
    var toEl   = document.getElementById('hero-to');
    if (!fromEl || !toEl || typeof gsap === 'undefined' || !pairs.length) return;

    var prefersReduced = false; // Forced false to ensure cinematic animation always plays

    /* ── Timing ── */
    var HOLD_MS    = 1500; /* Hold word in center for legibility */
    var TO_IN_DUR  = 1.80;  /* Cinematic glide and zoom-in */
    var TO_OUT_DUR = 0.80;  /* Dissolve-out */
    var FROM_DUR   = 1.20;  /* FROM word fade duration */
    var BLUR_AMT   = '10px';

    var idx        = 0;
    var cycleTimer = null;

    /* ── Scale the TO font to fill viewport (Synchronous to prevent animation race conditions) ── */
    function scaleFont(el, text) {
      el.textContent = text;
      el.style.fontSize = '';
      var vw        = window.innerWidth;
      var fillRatio = text.replace(/\s/g,'').length <= 5 ? 0.96 : 0.82;
      var target    = vw * fillRatio;
      
      var w = el.scrollWidth;
      if (w) {
        var cur  = parseFloat(getComputedStyle(el).fontSize) || 80;
        var next = Math.min(Math.max(cur * (target / w), 40), vw * 0.96);
        el.style.fontSize = next + 'px';
      }
    }

    /* ── Reveal TO: word spawns at a truly random position across full hero ── */
    function revealToWord(text, onDone) {
      gsap.killTweensOf(toEl);
      toEl.style.visibility = 'hidden';
      toEl.style.opacity = 0;
      toEl.innerHTML = '';

      scaleFont(toEl, text);

      var hero  = toEl.closest('.hero');
      var heroW = hero ? hero.offsetWidth  : window.innerWidth;
      var heroH = hero ? hero.offsetHeight : window.innerHeight;

      /* Fully random spawn: anywhere in the hero, but avoid a 30% dead-zone
         around the exact center so it always travels a visible distance */
      var minDist = Math.min(heroW, heroH) * 0.30; /* min pixels from center */
      var startX, startY;
      do {
        startX = (Math.random() - 0.5) * heroW;  /* -heroW/2 … +heroW/2 */
        startY = (Math.random() - 0.5) * heroH;  /* -heroH/2 … +heroH/2 */
      } while (Math.abs(startX) < minDist && Math.abs(startY) < minDist);

      /* transformOrigin: point away from spawn direction so scale grows toward center */
      var toX = startX < 0 ? '100%' : startX > 0 ? '0%' : '50%';
      var toY = startY < 0 ? '100%' : startY > 0 ? '0%' : '50%';

      gsap.set(toEl, {
        opacity:         0,
        scale:           0.18, // Start at 18% size so it is visible during glide
        x:               startX * 0.75, // Shift start slightly inside bounds
        y:               startY * 0.75,
        filter:          'blur(10px)', // Soft blur, not completely dissolved
        transformOrigin: toX + ' ' + toY
      });

      toEl.style.visibility = 'visible';

      gsap.to(toEl, {
        opacity:  1,
        scale:    1,
        x:        0,
        y:        0,
        filter:   'blur(0px)',
        duration: TO_IN_DUR,
        ease:     'power1.out', /* Beautifully smooth, gradual incoming easing */
        onComplete: function() { if (onDone) onDone(); }
      });
    }

    /* ── Fade OUT TO: slight scale-up + blur dissolve ── */
    function fadeOutToWord(onDone) {
      gsap.killTweensOf(toEl);
      gsap.to(toEl, {
        opacity:  0,
        scale:    1.07,
        filter:   prefersReduced ? 'blur(0px)' : 'blur(14px)',
        duration: TO_OUT_DUR,
        ease:     'power2.in', /* Smooth accelerating dissolve */
        onComplete: function() {
          gsap.set(toEl, { scale: 1, x: 0, y: 0 });
          if (onDone) onDone();
        }
      });
    }

    /* ── FROM word smooth blur-fade in ── */
    function revealFrom(text) {
      gsap.killTweensOf(fromEl);
      fromEl.style.visibility = 'hidden';
      fromEl.innerHTML = text === 'Business Failure' ? 'Business<br>Failure' : text;
      gsap.set(fromEl, { opacity: 0, filter: 'blur(' + BLUR_AMT + ')' });
      requestAnimationFrame(function() {
        fromEl.style.visibility = 'visible';
        gsap.to(fromEl, { opacity: 1, filter: 'blur(0px)', duration: FROM_DUR, ease: 'power2.out' });
      });
    }

    function fadeOutFrom(onDone) {
      gsap.to(fromEl, {
        opacity: 0, filter: 'blur(' + BLUR_AMT + ')',
        duration: FROM_DUR * 0.85, ease: 'power2.in',
        onComplete: function() { if (onDone) onDone(); }
      });
    }

    /* ── Cycle ── */
    function showPair(pair) {
      clearTimeout(cycleTimer);
      revealFrom(pair.from);
      revealToWord(pair.to, function() {
        cycleTimer = setTimeout(function() {
          var fadeOutTotal = (TO_OUT_DUR * 1000) + 120;
          fadeOutFrom(null);
          fadeOutToWord(null);
          cycleTimer = setTimeout(function() {
            idx = (idx + 1) % pairs.length;
            showPair(pairs[idx]);
          }, fadeOutTotal);
        }, HOLD_MS);
      });
    }

    showPair(pairs[0]);

    var resizeTimer;
    window.addEventListener('resize', function(){
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function(){
        if (toEl.textContent) scaleFont(toEl, toEl.textContent);
      }, 120);
    }, { passive: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    requestAnimationFrame(function(){ requestAnimationFrame(init); });
  }
})();

(function(){
  var vid = document.querySelector('.hero-bg-video video');
  if (!vid) return;

  function tryPlay() {
    var p = vid.play();
    if (p && p.catch) p.catch(function(){});
  }

  // IntersectionObserver to lazy load the video source on viewport entry
  if ('IntersectionObserver' in window && vid.dataset.src) {
    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (!entry.isIntersecting) return;
        var src = vid.dataset.src;
        var source = vid.querySelector('source');
        if (source) {
          source.setAttribute('src', src);
        } else {
          vid.src = src;
        }
        vid.load();
        tryPlay();
        io.disconnect();
      });
    }, { threshold: 0.01 });
    io.observe(vid);
  } else {
    if (vid.dataset.src) {
      var source = vid.querySelector('source');
      if (source) {
        source.setAttribute('src', vid.dataset.src);
      } else {
        vid.src = vid.dataset.src;
      }
      vid.load();
    }
    vid.addEventListener('canplay', tryPlay, { once: true });
  }

  vid.addEventListener('timeupdate', function(){
    if (!vid.duration || isNaN(vid.duration)) return;
    if (vid.currentTime >= vid.duration - 0.15) {
      vid.currentTime = 0;
      tryPlay();
    }
  });

  document.addEventListener('visibilitychange', function(){
    if (document.visibilityState === 'visible') tryPlay();
  });
})();
