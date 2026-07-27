/**
 * About Section Tagline Animation — v16
 * Creates a wavy per-letter tagline animation on page load/font load.
 */
(function(){
  'use strict';

  function initWaveTagline() {
    var inner = document.getElementById('av16-tagline-inner');
    if (!inner || inner.dataset.wave) return;
    inner.dataset.wave = '1';
    var text = inner.textContent;
    if (!text.trim()) return;
    inner.textContent = '';
    text.split('').forEach(function(ch, i) {
      var span = document.createElement('span');
      var isSpace = ch === ' ';
      span.textContent = isSpace ? '\u00a0' : ch;
      span.className = 'av16-tagline-char' + (isSpace ? ' is-space' : '');
      if (!isSpace) {
        span.style.setProperty('animation-delay', (i * 0.09).toFixed(2) + 's', 'important');
      }
      inner.appendChild(span);
    });

    if (typeof gsap !== 'undefined') {
      gsap.fromTo(inner, 
        { opacity: 0, y: 50 },
        {
          opacity: 1,
          y: 0,
          duration: 1.2,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: inner.closest('.av16-tagline') || inner,
            start: 'top 95%',
            toggleActions: 'play none none none'
          },
          onComplete: function() {
            var parent = inner.closest('.av16-tagline');
            if (parent) {
              parent.classList.add('start-wave');
            }
          }
        }
      );
    }
  }

  function run() { initWaveTagline(); }

  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(run);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
  window.addEventListener('load', run);
})();
