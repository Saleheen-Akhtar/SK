/* Sacred Kompass — transitions.js v4.0
 *
 * Four cinematic scroll/page transition effects:
 *
 *  1. Page overlay     — ink veil fades out on load; fades in on nav away.
 *                        Zero-dependency; works on every internal link click.
 *
 *  2. Heading ascent   — Words of key section headings slide up from a
 *                        clipped mask container (SplitType, words only).
 *                        Only targets headings NOT already handled by
 *                        initScrollReveals() in gsap-animations.js to
 *                        prevent double-animation conflicts.
 *                        Targets: .av16-heading (About), .sk-sp-v4-heading (Stories)
 *
 *  3. Curtain gold line — A 72px gold-to-terra gradient bar is injected
 *                        after every .display-h2 and animates scaleX 0→1
 *                        on scroll enter, giving each section heading a
 *                        ceremonial "opening" gesture.
 *
 *  4. Section pulse bar — 2px fixed bar on the right edge shifts colour
 *                        as you scroll between sections — ambient spatial
 *                        feedback without cluttering the layout.
 *
 * Dependencies: GSAP + ScrollTrigger (via gsap-animations.js)
 *               SplitType (optional; heading ascent gracefully skips if absent)
 * Load order: after sk-gsap-animations (setup.php dependency declared there)
 */
(function () {
  'use strict';

  /* Guard — GSAP must be present (loaded by gsap-animations.js first). */
  if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReduced) return;

  /* ── 1. PAGE OVERLAY — ink veil enter / exit ─────────────── */
  function initPageOverlay() {
    /* Create and prepend overlay element if not already present */
    let overlay = document.getElementById('sk-page-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.id = 'sk-page-overlay';
      overlay.setAttribute('aria-hidden', 'true');
      document.body.prepend(overlay);
    }

    // Create the parallax wrapper immediately on load so we don't break fixed elements by transforming body
    let siteWrap = document.getElementById('sk-site-wrap');
    if (!siteWrap) {
      siteWrap = document.createElement('div');
      siteWrap.id = 'sk-site-wrap';

      // Move all children except the overlay, progress bar, side nav, and cursor into the wrapper
      Array.from(document.body.childNodes).forEach(node => {
          // Explicitly exclude script tags, standard fixed UI, and WordPress admin bar
          if (node.nodeName === 'SCRIPT' || node.id === 'wpadminbar') return;

          if (node.id === 'sk-page-overlay' ||
              node.id === 'sk-sidenav' ||
              node.id === 'sk-hamburger' ||
              node.id === 'sk-sidenav-backdrop' ||
              node.id === 'sk-footer' || // Exclude footer so it stays at the body level for sticky parallax reveal
              (node.classList && (node.classList.contains('sk-progress') || node.classList.contains('sk-cursor')))) {
              return; // keep these at body level
          }
          siteWrap.appendChild(node);
      });
      // Insert siteWrap before the footer if footer exists, otherwise append
      const footer = document.getElementById('sk-footer');
      if (footer) {
          document.body.insertBefore(siteWrap, footer);
      } else {
          document.body.appendChild(siteWrap);
      }
    }

    // Set wrapper margin bottom to match footer height for parallax reveal
    function updateFooterMargin() {
      const footer = document.getElementById('sk-footer');
      // If mobile layout, footer is relative, so no margin is needed
      if (window.innerWidth <= 768) {
          if (siteWrap) siteWrap.style.marginBottom = '0px';
          if (footer) footer.style.visibility = 'visible';
          return;
      }

      if (footer && siteWrap) {
        siteWrap.style.marginBottom = `${footer.offsetHeight}px`;
        // Make footer visible only after layout is stable
        footer.style.visibility = 'visible';
      }
    }

    // Ensure accurate height measurement by waiting a moment after DOM builds
    setTimeout(updateFooterMargin, 50);
    window.addEventListener('resize', updateFooterMargin, { passive: true });

    /* — ENTRY: page just loaded — fade the overlay OUT — */

    // Animate the internal content parallaxing in, leaving the body tag untouched
    gsap.fromTo(siteWrap,
       { y: 50, scale: 0.98, opacity: 0 },
       { y: 0, scale: 1, opacity: 1, duration: 1.2, ease: 'power4.out', delay: 0.1, clearProps: 'transform,opacity' }
    );

    gsap.fromTo(overlay,
      { opacity: 1 },
      {
        opacity: 0,
        duration: 0.85,
        ease: 'power2.out',
        delay: 0.08,          /* tiny delay so browser has painted first frame */
        onStart()    { overlay.style.pointerEvents = 'all'; },
        onComplete() { overlay.style.pointerEvents = 'none'; },
      }
    );

    /* — EXIT: intercept same-origin internal link clicks — */
    if (!window.skPageOverlayClickBound) {
      window.skPageOverlayClickBound = true;
      document.addEventListener('click', e => {
        const a = e.target.closest('a[href]');
        if (!a) return;

        const raw = a.getAttribute('href') || '';

        /* Skip: hash / anchor links */
        if (!raw || raw.startsWith('#') || raw.includes('/#') || raw.startsWith('?')) return;
        /* Skip: special protocols and new-tab */
        if (a.target === '_blank') return;
        if (/^(mailto:|tel:|javascript:|data:)/i.test(raw)) return;
        /* Skip: downloads */
        if (a.hasAttribute('download')) return;

        /* Resolve URL and ensure same origin */
        let url;
        try { url = new URL(raw, location.href); } catch { return; }
        if (url.origin !== location.origin) return;

        /* Skip WP admin and login */
        if (/^\/(wp-admin|wp-login)/i.test(url.pathname)) return;

        e.preventDefault();
        const dest = url.href;

        /* Pause Lenis so it doesn't fight the overlay */
        if (typeof window.skLenisPause === 'function') window.skLenisPause();

        overlay.style.pointerEvents = 'all';

        // Parallax Fade Out Transition
        const tl = gsap.timeline({
          onComplete() { window.location.href = dest; }
        });

        tl.to(siteWrap, {
          y: -50,
          scale: 0.98,
          opacity: 0,
          duration: 0.6,
          ease: 'power3.inOut'
        }, 0)
        .fromTo(overlay,
          { opacity: 0 },
          { opacity: 1, duration: 0.5, ease: 'power2.in' },
          0.1
        );
      });
    }
  }

  /* ── 3. CURTAIN GOLD LINE — sweep beneath section headings ─ */
  /* A gold-to-terra 72px gradient bar injected after each      */
  /* .display-h2 and section heading. Animates scaleX 0→1 on   */
  /* scroll enter. Entirely a sibling element — zero conflict   */
  /* with any existing heading animations.                      */
  function initCurtainLines() {
    /*
     * Target all major section headings. .display-h2 covers
     * offerings, founders, stories. We also target .av16-heading
     * (About) and .sk-sp-v4-heading (Stories Preview) explicitly
     * in case they aren't marked display-h2 in the PHP output.
     */
    const selectors = '.av16-heading';

    gsap.utils.toArray(selectors).forEach(el => {
      /* Deduplicate — skip if already processed */
      if (el.dataset.skCurtain) return;
      el.dataset.skCurtain = '1';

      /* Clean up existing sibling curtain line first to prevent accumulation */
      const existingLine = el.nextElementSibling;
      if (existingLine && existingLine.classList.contains('sk-heading-curtain-line')) {
        existingLine.remove();
      }

      const line = document.createElement('span');
      line.className = 'sk-heading-curtain-line';
      line.setAttribute('aria-hidden', 'true');
      el.insertAdjacentElement('afterend', line);

      /* GSAP owns the initial state (CSS also sets it as fallback) */
      gsap.set(line, { scaleX: 0, opacity: 0, transformOrigin: 'left center' });

      gsap.to(line, {
        scaleX: 1,
        opacity: 1,
        duration: 1.3,
        ease: 'power4.inOut',
        delay: 0.22,           /* small lag so line follows heading reveal */
        scrollTrigger: {
          trigger: el,
          start: 'top 88%',
          toggleActions: 'play none none none',
        },
      });
    });
  }

  /* ── 4. SECTION AMBIENT PULSE BAR ───────────────────────── */
  /* 2px fixed bar on the right edge. As sections scroll into  */
  /* the center of the viewport it shifts to a section-matched */
  /* brand colour — subtle spatial orientation cue.            */
  function initSectionPulse() {
    let bar = document.getElementById('sk-section-pulse');
    if (!bar) {
      bar = document.createElement('div');
      bar.id = 'sk-section-pulse';
      bar.setAttribute('aria-hidden', 'true');
      document.body.appendChild(bar);
    }

    /* Map section selectors → brand colours */
    const map = [
      { sel: '.hero--fullscreen, .hero--split',       col: 'rgba(212,175,106,0.55)' },
      { sel: '.about-section',                         col: 'rgba(196,144,42,0.70)'  },
      { sel: '.sk-rc-section',                         col: 'rgba(184,98,63,0.70)'   },
      { sel: '.strip--circular',                       col: 'rgba(228,189,160,0.70)' },
      { sel: '.founders-section',                      col: 'rgba(196,144,42,0.72)'  },
      { sel: '.sk-stories-preview-section',            col: 'rgba(184,98,63,0.70)'   },
      { sel: '.faq-section',                           col: 'rgba(122,149,121,0.70)' },
      { sel: '.cta-section, .sk-cta-section',          col: 'rgba(212,175,106,0.80)' },
    ];

    map.forEach(({ sel, col }) => {
      const el = document.querySelector(sel);
      if (!el) return;

      ScrollTrigger.create({
        trigger: el,
        start: 'top 55%',
        end:   'bottom 45%',
        onEnter()     { gsap.to(bar, { backgroundColor: col, opacity: 0.8, duration: 0.65, ease: 'power2.inOut' }); },
        onLeave()     { gsap.to(bar, { opacity: 0.22, duration: 0.5, ease: 'power2.out' }); },
        onEnterBack() { gsap.to(bar, { backgroundColor: col, opacity: 0.8, duration: 0.65, ease: 'power2.inOut' }); },
        onLeaveBack() { gsap.to(bar, { opacity: 0.22, duration: 0.5, ease: 'power2.out' }); },
      });
    });
  }

  /* ── INIT ────────────────────────────────────────────────── */
  function init() {
    initPageOverlay();
    initCurtainLines();
    initSectionPulse();

    /* Refresh ScrollTrigger after fonts are ready so heading
       split positions are calculated with correct font metrics. */
    document.fonts.ready.then(() => {
      setTimeout(() => ScrollTrigger.refresh(), 150);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
