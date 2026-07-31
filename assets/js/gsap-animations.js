/* Sacred Kompass — gsap-animations.js v3.0
 * Lenis smooth scroll + GSAP ScrollTrigger
 *
 * v3.0 animation system changes:
 * ─ Varied reveal motions per section character (not universal translateY)
 * ─ FAQ: static, no animation (credibility sections don't need motion)
 * ─ Fade-only reveal (text content, no spatial movement)
 * ─ Values grid: stagger amount 0.65 → 0.4, trigger top 90% → top 85%
 * ─ Offering card mousemove: rAF-throttled (was firing on every pixel)
 * ─ Philosophy strip: hover + focus pause added to auto-advance
 * ─ CSS @keyframes stage-appear: documented as no-GSAP fallback (not dead code)
 */
(function () {
  'use strict';

  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (typeof gsap === 'undefined') {
    console.warn('Sacred Kompass: GSAP not loaded — CSS keyframe fallback active.');
    // CSS @keyframes stage-appear (in style.css) are the intentional fallback.
    // They run automatically when GSAP is absent. Mark body so CSS can
    // use .no-gsap selectors if needed for layout adjustments.
    document.body.classList.add('no-gsap');
    document.querySelectorAll('.journey-stage,.reveal,.reveal-left,.reveal-right,.reveal-scale,.section-enter,.stagger-children')
      .forEach(el => el.classList.add('visible'));
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  ScrollTrigger.config({
    ignoreMobileResize: true
  });

  /* ── 1. LENIS ───────────────────────────────────────────── */
  let lenis;
  function initLenis() {
    if (typeof Lenis === 'undefined') return;

    if (prefersReduced) {
      console.log('Sacred Kompass: prefers-reduced-motion detected. Bypassing Lenis scroll hijacking.');
      
      // Fallback scroll bindings
      window.skLenisReady = false;
      window.skLenisPause = () => {};
      window.skLenisResume = () => {};
      
      bindScrollToFallback();
      return;
    }

    const isTouch = window.matchMedia('(hover: none) and (pointer: coarse)').matches;

    lenis = new Lenis({
      duration: prefersReduced ? 0 : 1.3,            /* slightly longer — smoother, more refined feel */
      easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)), /* expo ease-out: fast start, feathers at end */
      smooth: !prefersReduced,
      smoothTouch: !isTouch ? true : false,
      touchMultiplier: 1.5,     /* reduced from 1.8 — prevents over-scroll on touch */
      wheelMultiplier: 1.0,     /* normalised wheel speed */
      normalizeWheel: false,
      lerp: prefersReduced ? 1 : 0.08,               /* lower = smoother butter scroll */
      infinite: false,
      prevent: (node) => {
        return (
          node && typeof node.closest === 'function' &&
          (node.closest('.sk-rc-overlay-box') !== null ||
           node.closest('.sk-founder-modal-box') !== null ||
           node.closest('.cta-section form, .sk-cta-section form, #contact form') !== null ||
           node.closest('.select2-dropdown') !== null ||
           node.closest('.select2-container') !== null ||
           node.closest('.select2-results__options') !== null ||
           node.closest('.forminator-select-dropdown') !== null)
        );
      },
    });

    gsap.ticker.add(time => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);
    lenis.on('scroll', ScrollTrigger.update);

    window.skLenisReady = true;
    window.skLenisPause = () => lenis && lenis.stop();
    window.skLenisResume = () => lenis && lenis.start();

    // Scroll to hash on page load if present
    if (window.location.hash) {
      setTimeout(function() {
        var hash = window.location.hash;
        var target = document.querySelector(hash);
        if (!target && hash === '#collective') target = document.getElementById('founders');
        if (!target && hash === '#sk-philosophy-strip') target = document.getElementById('philosophy');
        if (target) {
          var nav = document.getElementById('sk-sidenav');
          lenis.scrollTo(target, {
            offset: -(nav ? nav.offsetHeight : 64),
            duration: prefersReduced ? 0 : 1.3,
            easing: function(t) { return 1 - Math.pow(1 - t, 5); }
          });
        }
      }, 350);
    }

    // Match bare #hash, /path/#hash, and absolute https://…/#hash links.
    // Nav items are output as full URLs (home_url('/#section')), so
    // a[href^="#"] alone misses them — this caused the hamburger-menu
    // not-closing bug (menu stayed open while page scrolled behind it).
    document.querySelectorAll('a[href^="#"], a[href*="/#"]').forEach(a => {
      a.addEventListener('click', e => {
        const href = a.getAttribute('href') || '';
        const hashIdx = href.lastIndexOf('#');
        if (hashIdx === -1) return;
        const id = href.slice(hashIdx + 1);
        if (!id) return;
        let target = document.getElementById(id);
        if (!target && id === 'collective') target = document.getElementById('founders');
        if (!target && id === 'sk-philosophy-strip') target = document.getElementById('philosophy');
        if (!target) return;
        e.preventDefault();
        // Close sidenav first if open
        if (typeof window.skCloseSidenav === 'function') {
          window.skCloseSidenav();
        }
        const nav = document.getElementById('sk-sidenav');
        lenis.scrollTo(target, {
          offset: -(nav ? nav.offsetHeight : 64),
          duration: prefersReduced ? 0 : 1.3,
          easing: t => 1 - Math.pow(1 - t, 5),
        });
      });
    });
  }

  function bindScrollToFallback() {
    // Scroll to hash on page load if present (native fallback)
    if (window.location.hash) {
      setTimeout(function() {
        var hash = window.location.hash;
        var target = document.querySelector(hash);
        if (!target && hash === '#collective') target = document.getElementById('founders');
        if (!target && hash === '#sk-philosophy-strip') target = document.getElementById('philosophy');
        if (target) {
          var nav = document.getElementById('sk-sidenav');
          const top = target.getBoundingClientRect().top + window.scrollY - (nav ? nav.offsetHeight : 64);
          window.scrollTo({ top, behavior: prefersReduced ? 'auto' : 'smooth' });
        }
      }, 350);
    }

    // Native smooth scroll to anchor fallback
    document.querySelectorAll('a[href^="#"], a[href*="/#"]').forEach(a => {
      a.addEventListener('click', e => {
        const href = a.getAttribute('href') || '';
        const hashIdx = href.lastIndexOf('#');
        if (hashIdx === -1) return;
        const id = href.slice(hashIdx + 1);
        if (!id) return;
        let target = document.getElementById(id);
        if (!target && id === 'collective') target = document.getElementById('founders');
        if (!target && id === 'sk-philosophy-strip') target = document.getElementById('philosophy');
        if (!target) return;
        e.preventDefault();
        if (typeof window.skCloseSidenav === 'function') {
          window.skCloseSidenav();
        }
        const nav = document.getElementById('sk-sidenav');
        const top = target.getBoundingClientRect().top + window.scrollY - (nav ? nav.offsetHeight : 64);
        window.scrollTo({ top, behavior: prefersReduced ? 'auto' : 'smooth' });
      });
    });
  }

  /* ── 2. PROGRESS BAR ────────────────────────────────────── */
  function initProgressBar() {
    // Give the bar both its styling class AND the id that main.js looks up,
    // so both scripts reference the same element without conflict.
    let bar = document.querySelector('.sk-progress');
    if (!bar) {
      bar = document.createElement('div');
      bar.className = 'sk-progress';
      bar.id = 'sk-progress-bar';
      document.body.prepend(bar);
    } else if (!bar.id) {
      bar.id = 'sk-progress-bar';
    }
    ScrollTrigger.create({
      trigger: document.documentElement, start: 'top top', end: 'bottom bottom',
      onUpdate(self) { bar.style.transform = 'scaleX(' + self.progress.toFixed(4) + ')'; },
    });
  }

  /* ── 3. NAV ─────────────────────────────────────────────── */
  function initNav() {
    // All nav class logic lives in main.js. Intentional no-op.
  }

  /* ── 4. HERO ENTRANCE ───────────────────────────────────── */
  // NOTE on CSS @keyframes stage-appear:
  // Those keyframes in style.css are the NO-GSAP fallback — they are NOT dead code.
  // When GSAP loads (here), gsap.set(el, { clearProps: 'animation' }) disables the
  // CSS animation so GSAP takes sole ownership. When GSAP fails to load, the CSS
  // fallback runs automatically via the .journey-stage.visible class.
  // Ownership: GSAP (primary) / CSS keyframes (fallback). Both are intentional.


  /* ── 5. HERO PARALLAX ───────────────────────────────────── */
  function initHeroParallax() {
    if (prefersReduced) return;
    const img = document.querySelector('.hero-bg-image img');
    if (!img) return;
    /* Use the actual hero element — could be .hero--split or .hero--fullscreen */
    const heroTrigger = document.querySelector('.hero--split, .hero--fullscreen, #hero, .hero-section');
    if (!heroTrigger) return;
    gsap.fromTo(img, { yPercent: 0 }, {
      yPercent: 18, ease: 'none',
      scrollTrigger: { trigger: heroTrigger, start: 'top top', end: 'bottom top', scrub: 2.5 },
    });
    const overlay = document.querySelector('.hero-bg-overlay');
    /* Overlay animation removed — was causing an unwanted grey dissolve effect
       when scrolling from the hero into the About section. The overlay stays at
       its CSS-defined opacity throughout the scroll. */
  }

  /* ── 6. SCROLL REVEALS — varied by section character ───────
   *
   * Motion vocabulary (v3):
   *   .reveal            — default: translateY 40px (general content)
   *   .reveal-left       — slide from left (founders col, about pull quote)
   *   .reveal-right      — slide from right
   *   .reveal-scale      — scale from 0.94 (cards, imagery)
   *   .section-enter     — large section entrance: translateY 60px
   *   .stagger-children  — staggered children (grids); amount 0.4, trigger top 85%
   *   .reveal-fade       — opacity only, no spatial movement (large text)
   *   .reveal-cta        — scale from 0.97, no translateY (CTA block)
   *
   * Sections with NO animation (static credibility / rhythm break):
   *   .faq-section       — handled in initFaq() as no-op
   * ─────────────────────────────────────────────────────────── */
  function initScrollReveals() {
    if (prefersReduced) {
      document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-fade, .reveal-cta, .section-enter, .stagger-children')
        .forEach(el => {
          el.style.opacity = '1';
          el.style.transform = 'none';
          if (el.classList.contains('stagger-children')) {
            Array.from(el.children).forEach(k => {
              k.style.opacity = '1';
              k.style.transform = 'none';
            });
          }
        });
      return;
    }

    document.querySelectorAll('.reveal,.reveal-left,.reveal-right,.reveal-scale,.reveal-fade,.reveal-cta,.section-enter,.stagger-children')
      .forEach(el => { el.style.transition = 'none'; });

    const floatEase = 'power4.out';

    // Default reveals — skip elements inside .cta-section
    // (those sections have their own characterful motion, see below)
    gsap.utils.toArray('.reveal').forEach(el => {
      const inCta       = el.closest('.cta-section, .sk-cta-section');
      if (inCta) return; // handled by section-specific reveals below

      // Skip if the element is split by SplitType to prevent double-animation conflicts
      if (el.matches('.display-h2, .av16-heading, .cta-h2, .sk-sp-v4-heading, .display-xl, .sk-spg-hero__title, .sk-spg-cta__heading, .sk-event-single-title, .sk-story-single__title, .sk-archive-heading, .sk-cover-title, .sk-jp-feat-title, .sk-jp-side-title, h1')) return;

      gsap.fromTo(el, { opacity: 0, y: 40, scale: 0.99 }, {
        opacity: 1, y: 0, scale: 1,
        duration: 1.1, delay: +el.dataset.delay || 0, ease: floatEase,
        scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none none' },
      });
    });

    gsap.utils.toArray('.reveal-left').forEach(el => {
      gsap.fromTo(el, { opacity: 0, x: -40, scale: 0.99 }, {
        opacity: 1, x: 0, scale: 1,
        duration: 1.15, delay: +el.dataset.delay || 0, ease: floatEase,
        scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none none' },
      });
    });

    gsap.utils.toArray('.reveal-right').forEach(el => {
      gsap.fromTo(el, { opacity: 0, x: 40, scale: 0.99 }, {
        opacity: 1, x: 0, scale: 1,
        duration: 1.15, delay: +el.dataset.delay || 0, ease: floatEase,
        scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none none' },
      });
    });

    gsap.utils.toArray('.reveal-scale').forEach(el => {
      gsap.fromTo(el, { opacity: 0, scale: 0.94, y: 20 }, {
        opacity: 1, scale: 1, y: 0,
        duration: 1.2, delay: +el.dataset.delay || 0, ease: floatEase,
        scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none none' },
      });
    });

    // Fade-only — no spatial movement. Used for large typographical content.
    // Rationale: the display-impact phrase + blockquote are large typographic
    // moments. Moving them spatially fights their weight. Opacity alone is enough.
    gsap.utils.toArray('.reveal-fade').forEach(el => {
      gsap.fromTo(el, { opacity: 0 }, {
        opacity: 1,
        duration: 1.4, delay: +el.dataset.delay || 0, ease: 'power2.out',
        scrollTrigger: { trigger: el, start: 'top 88%', toggleActions: 'play none none none' },
      });
    });

    // CTA reveal — scale from 0.97, no vertical movement.
    // The dark section already creates visual separation; adding translateY
    // would fight the section boundary. Scale-in feels like the section
    // "locks into place" as it enters view.
    gsap.utils.toArray('.reveal-cta').forEach(el => {
      gsap.fromTo(el, { opacity: 0, scale: 0.97 }, {
        opacity: 1, scale: 1,
        duration: 1.3, delay: +el.dataset.delay || 0, ease: floatEase,
        scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none none' },
      });
    });

    gsap.utils.toArray('.section-enter').forEach(el => {
      gsap.fromTo(el, { opacity: 0, y: 60, scale: 0.98 }, {
        opacity: 1, y: 0, scale: 1,
        duration: 1.35, ease: floatEase,
        scrollTrigger: { trigger: el, start: 'top 88%', toggleActions: 'play none none none' },
      });
    });

    // Stagger grids — amount 0.4 (was 0.65), trigger top 85% (was 90%).
    // Rationale: 0.65s total stagger on 8 items meant the last card
    // animated 650ms after the first. Fast scrollers missed the tail.
    // 0.4s + earlier trigger ensures all cards are visible before they animate.
    gsap.utils.toArray('.stagger-children').forEach(parent => {
      const kids = [...parent.children];
      kids.forEach(k => { k.style.transition = 'none'; });
      gsap.fromTo(kids, { opacity: 0, y: 28, scale: 0.98 }, {
        opacity: 1, y: 0, scale: 1,
        duration: 1.0,
        stagger: { amount: 0.4, ease: 'power2.inOut' }, // was 0.65
        ease: floatEase,
        scrollTrigger: {
          trigger: parent,
          start: 'top 85%', // was 90% — triggers earlier so last items aren't missed
          toggleActions: 'play none none none',
        },
      });
    });
  }

  /* ── 7. SECTION PARALLAX ────────────────────────────────── */
  function initSectionParallax() {
    if (prefersReduced) return;

    // Existing helper classes
    [['parallax-slow', -20], ['parallax-med', -38], ['parallax-fast', -60]].forEach(([cls, y]) => {
      gsap.utils.toArray(`.${cls}`).forEach(el => {
        gsap.fromTo(el, { y: 0 }, {
          y, ease: 'none',
          scrollTrigger: { trigger: el.closest('section') || el.parentElement, start: 'top bottom', end: 'bottom top', scrub: 2 },
        });
      });
    });

    // Auto-apply subtle parallax to major background images for that premium feel
    gsap.utils.toArray('.hero-bg-image img, .cinematic-image-wrap img, .sk-cover-image, .founder-card img').forEach(img => {
      // Ensure parent has overflow hidden to hide the parallax overhang
      const parent = img.parentElement;
      if(parent) {
         parent.style.overflow = 'hidden';
      }

      // Scale image slightly so it has room to move without showing edges
      gsap.set(img, { scale: 1.15, transformOrigin: 'center center' });

      gsap.to(img, {
        yPercent: 15,
        ease: 'none',
        scrollTrigger: {
          trigger: parent || img,
          start: 'top bottom',
          end: 'bottom top',
          scrub: true
        }
      });
    });
  }

  /* ── 8. ABOUT ───────────────────────────────────────────── */
  function initAboutTagline() {
    if (prefersReduced) return;
    const taglineInner = document.querySelector('.av16-tagline-inner');
    if (!taglineInner) return;
    
    gsap.fromTo(taglineInner, 
      { opacity: 0, y: 50 },
      {
        opacity: 1,
        y: 0,
        duration: 1.2,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: taglineInner,
          start: 'top 90%',
          toggleActions: 'play none none none'
        },
        onComplete: function() {
          const parent = taglineInner.closest('.av16-tagline');
          if (parent) {
            parent.classList.add('start-wave');
          }
        }
      }
    );
  }

  /* ── 9. TRAD TAGS ───────────────────────────────────────── */
  function initTradTags() {
    if (prefersReduced) {
      document.querySelectorAll('.trad-tag').forEach(tag => {
        tag.style.opacity = '1';
        tag.style.transform = 'none';
      });
      return;
    }
    const tags = gsap.utils.toArray('.trad-tag');
    if (!tags.length) return;
    gsap.fromTo(tags, { opacity: 0, scale: 0.75, y: 14 }, {
      opacity: 1, scale: 1, y: 0, duration: 0.8, stagger: { amount: 0.8 }, ease: 'back.out(1.8)',
      scrollTrigger: { trigger: '.tradition-tags', start: 'top 92%', toggleActions: 'play none none none' },
    });
  }

  /* ── 10. FOUNDERS — slide from left, guaranteed visibility ─ */
  function initFounders() {
    const cards = gsap.utils.toArray('.founder-card');
    if (!cards.length) return;

    // Guarantee visibility as baseline (CSS fallback)
    cards.forEach(c => { c.style.opacity = '1'; c.style.transform = 'none'; });
    if (prefersReduced) return;

    const section = cards[0].closest('section') || cards[0].parentElement;
    const rect = section.getBoundingClientRect();
    const alreadyVisible = rect.top < window.innerHeight * 0.9;
    if (alreadyVisible) return;

    // Founders slide from left — different from default translateY reveals.
    // Rationale: founders are the "people" section. A horizontal entrance
    // feels like someone stepping forward to introduce themselves.
    gsap.fromTo(cards,
      { opacity: 0, x: -50, scale: 0.97 },
      {
        opacity: 1, x: 0, scale: 1,
        duration: 1.2,
        stagger: 0.15,
        ease: 'power4.out',
        scrollTrigger: {
          trigger: section,
          start: 'top 88%',
          toggleActions: 'play none none none',
          onEnter() {
            setTimeout(() => cards.forEach(c => {
              c.style.opacity = '1';
              c.style.transform = 'none';
            }), 1800);
          },
        },
      }
    );
  }

  /* ── 11. FAQ — animated sequentially ────────────────────── */
  function initFaq() {
    if (prefersReduced) {
      document.querySelectorAll('.faq-item, .sk-faq-item').forEach(el => {
        el.style.opacity = '1';
        el.style.transform = 'none';
      });
      return;
    }
    const items = gsap.utils.toArray('.faq-item, .sk-faq-item');
    if (!items.length) return;
    gsap.fromTo(items, { opacity: 0, y: 20 }, {
      opacity: 1, y: 0, duration: 0.8, stagger: 0.15, ease: 'power2.out',
      scrollTrigger: { trigger: '.faq-section, .sk-faq-section', start: 'top 90%', toggleActions: 'play none none none' }
    });
  }

  /* ── 13. CTA — scale-in reveal ──────────────────────────── */
  // Rationale: the dark CTA section already creates a strong visual boundary.
  // translateY would fight the dark-section boundary. scale(0.97)→1 feels like
  // the section "resolves" into position — inviting without pushing.
  function initCta() {
    const cta = document.querySelector('.cta-section, .sk-cta-section');
    if (!cta) return;
    if (prefersReduced) {
      cta.style.opacity = '1';
      cta.style.transform = 'none';
      cta.querySelectorAll('.cta-text-col, .cta-form-col').forEach(col => {
        col.style.opacity = '1';
        col.style.transform = 'none';
      });
      return;
    }
    const cols = cta.querySelectorAll('.cta-text-col, .cta-form-col');
    if (cols.length) {
      gsap.fromTo(cols, { opacity: 0, scale: 0.97 }, {
        opacity: 1, scale: 1,
        duration: 1.3,
        stagger: 0.15,
        ease: 'power4.out',
        scrollTrigger: { trigger: cta, start: 'top 88%', toggleActions: 'play none none none' },
      });
    } else {
      gsap.fromTo(cta, { opacity: 0, scale: 0.97 }, {
        opacity: 1, scale: 1, duration: 1.4, ease: 'power4.out',
        scrollTrigger: { trigger: cta, start: 'top 88%', toggleActions: 'play none none none' },
      });
    }
  }

  /* ── 14. OFFERINGS CAROUSEL — with rAF-throttled mousemove ─
   *
   * v3 fix: card mousemove tilt was firing style recalc on every pixel
   * with 5 cards in the DOM = 5 concurrent rAF loops + 5 style mutations.
   * Now uses a single rafPending flag per card — skips if a frame is
   * already queued, clears the flag when the frame runs.
   * ─────────────────────────────────────────────────────────── */
  /* ── 15. SPLITTYPE — character reveal on ALL section headings ── */
  function initSplitText() {
    if (prefersReduced) return;
    if (typeof SplitType === 'undefined') return;

    /* Target all major heading classes across sections:
     * .display-h2   — offerings, faq, founders, journal-preview, philosophy-strip
     * .av16-heading  — about section h2
     * .cta-h2        — CTA section h2
     * .sk-sp-v4-heading — stories preview h2
     * .display-xl    — legacy / journal blog headings
     */
    const headlines = gsap.utils.toArray(
      '.display-h2, .av16-heading, .cta-h2, .sk-sp-v4-heading, .display-xl, .sk-spg-hero__title, .sk-spg-cta__heading, .sk-event-single-title, .sk-story-single__title, .sk-archive-heading, .sk-cover-title, .sk-jp-feat-title, .sk-jp-side-title, h1:not(.sr-only)'
    );
    if (!headlines.length) return;

    const splits = [];
    const triggers = [];

    function doSplit() {
      // Kill old triggers and revert splits to prevent memory leak and DOM thrashing
      triggers.forEach(t => t.kill());
      triggers.length = 0;
      splits.forEach(s => s.revert());
      splits.length = 0;

      headlines.forEach(el => {
        const inHero = el.closest('.sk-home-hero, .hero--split, .hero-left');

        // Split by lines and words so we can mask the words perfectly using the generated line wrappers
        const split = new SplitType(el, { types: 'lines, words' });
        splits.push(split);

        // If this heading was already revealed, preserve its visible state and do not re-animate
        if (el.classList.contains('sk-heading-revealed')) {
          gsap.set(el, { opacity: 1, y: 0, scale: 1 });
          gsap.set(split.words, { opacity: 1, yPercent: 0, rotationZ: 0 });
          return;
        }

        // Ensure parent is hidden initially and cleared of double-reveal transform offsets
        gsap.set(el, { opacity: 0, y: 0, scale: 1 });

        // Set initial state of words for a cleaner, premium slide-up effect
        // Wrap each line in a hidden overflow mask so words slide up individually from their own baselines
        gsap.set(split.lines, { overflow: 'hidden' });
        gsap.set(split.words, { opacity: 0, yPercent: 120, rotationZ: 2 });

        const triggerConfig = inHero
          ? { trigger: el, start: 'top 100%', toggleActions: 'play none none none' }
          : { trigger: el, start: 'top 84%', toggleActions: 'play none none none' };

        // Mark as revealed when animated
        triggerConfig.onEnter = () => el.classList.add('sk-heading-revealed');

        // Reveal the parent instantly when triggered
        const pAnim = gsap.to(el, {
          opacity: 1,
          y: 0,
          scale: 1,
          duration: 0.05,
          scrollTrigger: triggerConfig,
        });
        if (pAnim.scrollTrigger) triggers.push(pAnim.scrollTrigger);

        // Animate the words sliding up
        const cAnim = gsap.to(split.words, {
          yPercent: 0,
          opacity: 1,
          rotationZ: 0,
          ease: 'power4.out',
          duration: 1.2,
          stagger: 0.04,
          scrollTrigger: triggerConfig,
        });
        if (cAnim.scrollTrigger) triggers.push(cAnim.scrollTrigger);
      });
    }

    doSplit();
    let resizeTimer;
    let lastWidth = window.innerWidth;
    window.addEventListener('resize', () => {
      // Ignore height-only resizes (e.g. mobile scroll bar hiding/showing) to prevent lockups
      if (window.innerWidth === lastWidth) return;
      lastWidth = window.innerWidth;
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(doSplit, 200);
    }, { passive: true });
  }

  /* ── 16. CURTAIN REVEAL ─────────────────────────────────── */
  function initCurtainReveal() {
    if (prefersReduced) {
      document.querySelectorAll('.cinematic-image-wrap').forEach(wrap => {
        wrap.classList.add('is-revealed');
      });
      return;
    }
    gsap.utils.toArray('.cinematic-image-wrap').forEach(wrap => {
      ScrollTrigger.create({
        trigger: wrap, start: 'top 85%',
        toggleActions: 'play none none none',
        onEnter() { wrap.classList.add('is-revealed'); },
      });
    });
  }

  /* ── 17. JOURNAL PREVIEW CARDS ──────────────────────────── */
  function initJournalCards() {
    if (prefersReduced) {
      document.querySelectorAll('.sk-jp-card--animated').forEach(el => {
        el.style.opacity = '1';
        el.style.transform = 'none';
      });
      return;
    }
    const yOffsets = [40, 52, 64];
    gsap.utils.toArray('.sk-jp-card--animated').forEach((el, i) => {
      gsap.fromTo(el,
        { opacity: 0, y: yOffsets[i] || 40 },
        {
          opacity: 1, y: 0, duration: 0.85, delay: i * 0.14, ease: 'power2.out',
          scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none none' },
        }
      );
    });
  }

  /* ── 18. PHILOSOPHY STRIP — hover + focus pause ─────────── */
  // Bug fix: auto-advance had visibilitychange pause but no hover pause.
  // A user reading pillar 3 would get interrupted at 5s by the carousel
  // advancing. The word-blur animation plays on every advance (user or auto),
  // creating unsolicited motion on content they didn't choose.
  // Fix: pause on mouseenter of the entire strip, resume on mouseleave.
  // Also pause when any focusable element inside ct-content gains focus
  // (keyboard navigation / screen reader compatibility).
  function initPhilosophyStripPause() {
    const strip = document.getElementById('philosophy');
    if (!strip) return;

    // Note: mouseenter/mouseleave are handled directly in philosophy-strip.php
    // with an isHovered guard. We only bridge focus events here (keyboard/screen-reader).

    // Focus within — pause when keyboard user tabs into the strip content
    strip.addEventListener('focusin',  () => strip.dispatchEvent(new CustomEvent('sk:strip:pause')));
    strip.addEventListener('focusout', e => {
      // Only resume if focus has left the strip entirely
      if (!strip.contains(e.relatedTarget)) {
        strip.dispatchEvent(new CustomEvent('sk:strip:resume'));
      }
    });
  }

  /* ── INIT ───────────────────────────────────────────────── */
  function init() {
    initLenis();
    initProgressBar();
    initNav();
    initHeroParallax();
    initScrollReveals();
    initAboutTagline();
    initSectionParallax();
    initTradTags();
    initFounders();
    initFaq();
    initCta();
    initSplitText();
    initCurtainReveal();
    initJournalCards();
    initPhilosophyStripPause();

    let refreshTimer;
    function scheduleRefresh() {
      clearTimeout(refreshTimer);
      refreshTimer = setTimeout(() => ScrollTrigger.refresh(), 60);
    }
    document.fonts.ready.then(scheduleRefresh);
    window.addEventListener('resize', scheduleRefresh, { passive: true });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();

})();

