/* Sacred Kompass — main.js v7.4 */
(function () {
  'use strict';

  /* ── Sidenav is now a bottom pill on all screen sizes — no left offset needed ── */
  (function initSidenavOffset() {
    document.documentElement.style.setProperty('--sidenav-offset', '0px');
  })();

  /* ── Scroll progress bar — owned exclusively by GSAP ScrollTrigger
        (gsap-animations.js initProgressBar). When GSAP loads (normal case),
        ScrollTrigger.onUpdate drives the bar on the GPU-optimised path.
        main.js only updates the bar as a fallback if GSAP failed to load. ── */

  /* ── Hero journey stages + scroll reveals are handled by
        gsap-animations.js. Fallback: if GSAP failed to load,
        add .no-gsap so CSS fallback makes elements visible,
        and run a simple scroll-based progress bar updater. ── */
  if (typeof gsap === 'undefined') {
    document.body.classList.add('no-gsap');
    document.querySelectorAll(
      '.journey-stage, .reveal, .reveal-left, .reveal-right, .reveal-scale, .section-enter, .stagger-children'
    ).forEach((el) => el.classList.add('visible'));

    /* Minimal progress bar fallback — only when GSAP is absent */
    const progressBar = document.querySelector('.sk-progress') || null;
    if (progressBar) {
      let ticking = false;
      function onScrollTick() {
        const docH = document.documentElement.scrollHeight - window.innerHeight;
        progressBar.style.width = (docH > 0 ? (window.scrollY / docH) * 100 : 0) + '%';
        ticking = false;
      }
      window.addEventListener('scroll', () => {
        if (!ticking) { requestAnimationFrame(onScrollTick); ticking = true; }
      }, { passive: true });
      onScrollTick();
    }
  }

  /* ── Mobile hamburger ─────────────────────────────────────────
     NOTE: The hamburger (#sk-hamburger) controls the sidenav pill
     drawer exclusively. The old #sk-mobile-menu overlay has been
     removed from the HTML — this stub only keeps closeMenu()
     available for FAQ / modal Escape handlers below.
  ── */
  const hamburger = document.getElementById('sk-hamburger');

  function closeMenu() {
    // Delegate to the sidenav IIFE's closePanel if available,
    // otherwise just clean up aria state.
    if (typeof window.skCloseSidenav === 'function') {
      window.skCloseSidenav();
    } else if (hamburger) {
      hamburger.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeMenu();
      closeAllModals();
    }
  });

  /* ── FAQ accordion ────────────────────────── */
  document.querySelectorAll('.faq-trigger').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const isOpen    = trigger.getAttribute('aria-expanded') === 'true';
      const controlId = trigger.getAttribute('aria-controls');
      const body      = document.getElementById(controlId);
      document.querySelectorAll('.faq-trigger').forEach((t) => {
        t.setAttribute('aria-expanded', 'false');
        const b = document.getElementById(t.getAttribute('aria-controls'));
        if (b) b.classList.remove('open');
      });
      if (!isOpen && body) {
        trigger.setAttribute('aria-expanded', 'true');
        body.classList.add('open');
      }
    });
  });

  /* ── Smooth anchor scroll ──────────────────────────────────
     Lenis (loaded via gsap-animations.js) owns all anchor clicks
     when GSAP is available. This fallback only activates when
     Lenis failed to load, preventing any double-scroll conflict. ── */
  function skEasedScrollTo(targetY, duration) {
    const startY  = window.scrollY;
    const dist    = targetY - startY;
    if (Math.abs(dist) < 2) return;
    let startTime = null;
    function ease(t) { return t < 0.5 ? 4*t*t*t : 1 - Math.pow(-2*t+2, 3)/2; }
    function step(now) {
      if (!startTime) startTime = now;
      const elapsed  = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      window.scrollTo(0, startY + dist * ease(progress));
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  // Only wire up fallback scroll if Lenis did NOT initialise
  // (gsap-animations.js sets window.skLenisReady = true when Lenis starts)
  if (!window.skLenisReady) {
    const nav = document.getElementById('sk-header-nav');
    const header = document.querySelector('.sk-header');
    document.querySelectorAll('a[href^="#"], a[href^="/#"], a[href*="/#"]').forEach((a) => {
      a.addEventListener('click', (e) => {
        const href = a.getAttribute('href') || '';
        const hashIndex = href.lastIndexOf('#');
        if (hashIndex === -1) return;
        const id = href.slice(hashIndex + 1);
        if (!id) return;
        let el = document.getElementById(id);
        if (!el && id === 'collective') el = document.getElementById('founders');
        if (!el && id === 'sk-philosophy-strip') el = document.getElementById('philosophy');
        if (!el) return;
        e.preventDefault();
        if (typeof closeMenu === 'function') closeMenu();
        const navH = header ? header.offsetHeight : 64;
        const top  = el.getBoundingClientRect().top + window.scrollY - navH;
        skEasedScrollTo(top, 480);
      });
    });
  }

  /* ── Active nav link ──────────────────────── */
  const sections     = ['about','offerings','philosophy','founders','journal-preview','faq','contact'];
  const navLinksAll  = document.querySelectorAll('.sk-header-links a');

  // Mark "The Collective" nav link active when on the collective page
  const isCollectivePage = document.body.classList.contains('page-template-page-collective') ||
                           window.location.pathname.includes('/collective');
  if (isCollectivePage) {
    navLinksAll.forEach((a) => {
      const href = a.getAttribute('href') || '';
      if (href.includes('/collective')) a.classList.add('active');
    });
  }

  let spyTicking = false;
  window.addEventListener('scroll', () => {
    if (spyTicking || isCollectivePage) return;
    // Set flag BEFORE requestAnimationFrame so it's true before the callback
    // could theoretically reset it — prevents the guard from being bypassed.
    spyTicking = true;
    requestAnimationFrame(() => {
      const scrollMid = window.scrollY + window.innerHeight / 2;
      let active = '';
      sections.forEach((id) => {
        const el = document.getElementById(id);
        if (el && el.offsetTop <= scrollMid) active = id;
      });
      navLinksAll.forEach((a) => {
        const href = a.getAttribute('href') || '';
        a.classList.toggle('active',
          href === `#${active}` ||
          href.endsWith(`/#${active}`)
        );
      });
      spyTicking = false;
    });
  }, { passive: true });

  /* ── Founder Modals ───────────────────────── */
  const modals = document.querySelectorAll('.sk-founder-modal');

  function trapFocus(modal) {
    releaseFocus(modal);
    const focusable = modal.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    if (!focusable.length) return;
    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    modal._trapKeydown = (e) => {
      if (e.key !== 'Tab') return;
      if (e.shiftKey) {
        if (document.activeElement === first) {
          e.preventDefault();
          last.focus();
        }
      } else {
        if (document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    };
    modal.addEventListener('keydown', modal._trapKeydown);
  }

  function releaseFocus(modal) {
    if (modal._trapKeydown) {
      modal.removeEventListener('keydown', modal._trapKeydown);
      delete modal._trapKeydown;
    }
  }

  function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.removeAttribute('hidden');
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        modal.classList.add('is-open');
      });
    });
    const scrollY = window.scrollY;
    document.body.dataset.modalScrollY = scrollY;
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow  = 'hidden';
    document.body.style.position  = 'fixed';
    document.body.style.top       = '-' + scrollY + 'px';
    document.body.style.left      = '0';
    document.body.style.right     = '0';
    if (window.skLenisPause) window.skLenisPause();
    const closeEl = modal.querySelector('.sk-founder-modal-close');
    if (closeEl) setTimeout(() => closeEl.focus(), 50);

    trapFocus(modal);
  }

  function closeModal(modal) {
    releaseFocus(modal);
    modal.classList.remove('is-open');
    setTimeout(() => {
      modal.setAttribute('hidden', '');
    }, 320);
    const scrollY = parseInt(document.body.dataset.modalScrollY || '0', 10);
    document.documentElement.style.overflow = '';
    document.body.style.overflow  = '';
    document.body.style.position  = '';
    document.body.style.top       = '';
    document.body.style.left      = '';
    document.body.style.right     = '';
    window.scrollTo(0, scrollY);
    if (window.skLenisResume) window.skLenisResume();
  }

  function closeAllModals() {
    modals.forEach(m => {
      releaseFocus(m);
      m.classList.remove('is-open');
      setTimeout(() => m.setAttribute('hidden', ''), 320);
    });
    const scrollY = parseInt(document.body.dataset.modalScrollY || '0', 10);
    document.documentElement.style.overflow = '';
    document.body.style.overflow  = '';
    document.body.style.position  = '';
    document.body.style.top       = '';
    document.body.style.left      = '';
    document.body.style.right     = '';
    window.scrollTo(0, scrollY);
    if (window.skLenisResume) window.skLenisResume();
  }

  document.querySelectorAll('.sk-founder-trigger').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const id = trigger.dataset.modal;
      if (id) openModal(id);
    });
  });

  modals.forEach((modal) => {
    // Close button
    const closeEl = modal.querySelector('.sk-founder-modal-close');
    if (closeEl) closeEl.addEventListener('click', () => closeModal(modal));

    // Backdrop click
    const backdrop = modal.querySelector('.sk-founder-modal-backdrop');
    if (backdrop) backdrop.addEventListener('click', () => closeModal(modal));

    // Block wheel/touch from reaching Lenis — only while the modal is actually open.
    // Without the is-open guard these listeners fire on every scroll even when
    // the modal is hidden, which stalls the browser's scroll thread needlessly.
    const modalBox = modal.querySelector('.sk-founder-modal-box');
    modal.addEventListener('wheel', (e) => {
      if (!modal.classList.contains('is-open')) return;
      if (modalBox && modalBox.contains(e.target)) {
        e.stopPropagation(); // let box scroll, block Lenis
      } else {
        e.preventDefault();
        e.stopPropagation();
      }
    }, { passive: false });
    modal.addEventListener('touchmove', (e) => {
      if (!modal.classList.contains('is-open')) return;
      if (modalBox && modalBox.contains(e.target)) {
        e.stopPropagation();
      } else {
        e.preventDefault();
        e.stopPropagation();
      }
    }, { passive: false });

    // CTA redirect links — close modal first, then navigate after transition
    modal.querySelectorAll('.sk-modal-cta-redirect').forEach((link) => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const href = link.getAttribute('href');
        closeAllModals();
        // Wait for modal close transition (320ms) then navigate
        setTimeout(() => { window.location.href = href; }, 340);
      });
    });
  });

})();

/* ── v7.2: Contact form validation ──────────────────────── */
(function () {
  const form = document.querySelector('.sk-contact-fallback-form');
  if (!form) return;

  function showError(field, msg) {
    field.classList.add('sk-invalid');
    field.classList.remove('sk-valid');
    let err = field.parentElement.querySelector('.sk-field-error');
    if (!err) { err = document.createElement('span'); err.className = 'sk-field-error'; field.parentElement.appendChild(err); }
    err.textContent = msg;
  }
  function clearError(field) {
    field.classList.remove('sk-invalid');
    const err = field.parentElement.querySelector('.sk-field-error');
    if (err) err.textContent = '';
  }
  function markValid(field) {
    field.classList.remove('sk-invalid');
    field.classList.add('sk-valid');
    const err = field.parentElement.querySelector('.sk-field-error');
    if (err) err.textContent = '';
  }

  function validateField(field) {
    const val = field.value.trim();
    const name = field.name || field.id;

    if (field.required && !val) {
      showError(field, 'This field is required.'); return false;
    }
    if (field.type === 'email' && val) {
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
        showError(field, 'Please enter a valid email address.'); return false;
      }
    }
    if ((name === 'fname' || name === 'lname') && val && val.length < 2) {
      showError(field, 'Must be at least 2 characters.'); return false;
    }
    if (name === 'message' && val && val.length < 10) {
      showError(field, 'Please write at least 10 characters.'); return false;
    }
    if (field.tagName === 'SELECT' && !val) {
      showError(field, 'Please select an option.'); return false;
    }
    markValid(field); return true;
  }

  // Cache field references once — avoids repeated DOM queries on submit
  const fFname   = document.getElementById('sk-fname');
  const fLname   = document.getElementById('sk-lname');
  const fEmail   = document.getElementById('sk-email');
  const fService = document.getElementById('sk-service');
  const fMessage = document.getElementById('sk-message');
  const fHp      = document.getElementById('sk-hp');

  // Mark required fields
  [fFname, fEmail, fService, fMessage].forEach(f => { if (f) f.required = true; });

  // Live validation on blur
  form.querySelectorAll('input, textarea, select').forEach(field => {
    field.addEventListener('blur', () => validateField(field));
    field.addEventListener('input', () => {
      if (field.classList.contains('sk-invalid')) validateField(field);
    });
  });

  // Submit — validate then POST to WordPress AJAX
  const submitBtn = form.querySelector('button[type="submit"]');
  if (submitBtn) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      // Client-side validation
      const fields = form.querySelectorAll('input:not([name="website"]), textarea, select');
      let allValid = true;
      fields.forEach(f => { if (!validateField(f)) allValid = false; });
      if (!allValid) {
        const firstErr = form.querySelector('.sk-invalid');
        if (firstErr) firstErr.focus();
        return;
      }

      const original = submitBtn.textContent;
      submitBtn.textContent = 'Sending…';
      submitBtn.disabled = true;

      // Build FormData for AJAX — use cached field refs
      const data = new FormData();
      data.append('action', 'sk_contact_submit');
      data.append('nonce',   (window.skData && window.skData.nonce) || '');
      data.append('fname',   fFname   ? fFname.value   : '');
      data.append('lname',   fLname   ? fLname.value   : '');
      data.append('email',   fEmail   ? fEmail.value   : '');
      data.append('service', fService ? fService.value : '');
      data.append('message', fMessage ? fMessage.value : '');
      data.append('website', fHp      ? fHp.value      : ''); // honeypot

      const ajaxUrl = (window.skData && window.skData.ajaxurl) || '/wp-admin/admin-ajax.php';

      fetch(ajaxUrl, { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            const msg = (res.data && res.data.msg) ? res.data.msg : '✓ Message sent — we\'ll reply within 24 hours.';
            submitBtn.textContent = '✓ Sent';
            submitBtn.style.background = 'var(--sage)';
            form.querySelectorAll('input:not([name="website"]), textarea, select').forEach(f => {
              f.value = ''; f.classList.remove('sk-valid', 'sk-invalid');
            });
            let feedbackEl = form.querySelector('.sk-form-feedback');
            if (feedbackEl) {
              feedbackEl.textContent = msg;
              feedbackEl.className = 'sk-form-feedback sk-form-feedback--success';
              setTimeout(() => {
                submitBtn.textContent = original;
                submitBtn.disabled = false;
                submitBtn.style.background = '';
                feedbackEl.textContent = '';
                feedbackEl.className = 'sk-form-feedback';
              }, 6000);
            }
          } else {
            const errMsg = (res.data && res.data.msg) ? res.data.msg : 'Your message could not be sent right now. Please try again shortly.';
            submitBtn.textContent = original;
            submitBtn.disabled = false;
            // Show error in feedback zone
            let feedbackEl = form.querySelector('.sk-form-feedback');
            if (feedbackEl) {
              feedbackEl.textContent = errMsg;
              feedbackEl.className = 'sk-form-feedback sk-form-feedback--error';
              setTimeout(() => {
                feedbackEl.textContent = '';
                feedbackEl.className = 'sk-form-feedback';
              }, 8000);
            }
          }
        })
        .catch(function () {
          submitBtn.textContent = original;
          submitBtn.disabled = false;
          // Network / server unreachable — show clear feedback
          var feedback = form.querySelector('.sk-form-feedback');
          if (feedback) {
            feedback.textContent = 'Message not sent — please check your connection and try again. If the issue persists, email us directly.';
            feedback.className = 'sk-form-feedback sk-form-feedback--error';
            setTimeout(function () {
              feedback.textContent = '';
              feedback.className = 'sk-form-feedback';
            }, 8000);
          }
        });
    });
  }
})();

/* ══════════════════════════════════════════════════════════
   SACRED KOMPASS — SHARE JS
   Copy-link share button (used by single.php)
   ══════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  /* ── Copy-link share button ────────────────────────────── */
  document.querySelectorAll('.sk-share-copy').forEach(btn => {
    btn.addEventListener('click', function () {
      const url = btn.dataset.url || window.location.href;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
          btn.setAttribute('aria-label', 'Link copied!');
          btn.style.background = 'var(--sage)';
          btn.style.color = '#fff';
          setTimeout(() => {
            btn.setAttribute('aria-label', 'Copy link');
            btn.style.background = '';
            btn.style.color = '';
          }, 2000);
        });
      }
    });
  });

})();

/* ══════════════════════════════════════════════════════════
   COLLECTIVE CARDS — Touch tap-to-reveal overlay
   ══════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  const isTouchDevice = () => window.matchMedia('(hover: none)').matches;

  document.querySelectorAll('.sk-collective-card').forEach(card => {
    card.addEventListener('click', function (e) {
      if (!isTouchDevice()) return;
      const isRevealed = card.classList.contains('is-revealed');
      // Close all others
      document.querySelectorAll('.sk-collective-card.is-revealed').forEach(c => {
        c.classList.remove('is-revealed');
      });
      if (!isRevealed) {
        card.classList.add('is-revealed');
        e.preventDefault(); // prevent link navigation on first tap
      }
    });
  });

})();


/* ── Header visibility on scroll & Mobile Nav (Podium Style) ── */
(function () {
  'use strict';

  const header = document.querySelector('.sk-header');
  let lastScrollY = window.scrollY;
  let ticking = false;

  function updateHeader() {
    if (!header) return;
    const currentScrollY = window.scrollY;
    
    if (currentScrollY > 64 && currentScrollY > lastScrollY) {
      header.classList.add('is-hidden');
    } else {
      header.classList.remove('is-hidden');
    }

    lastScrollY = currentScrollY;
    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(updateHeader);
      ticking = true;
    }
  }, { passive: true });

  const hamburger = document.getElementById('sk-hamburger');
  const mobileNav = document.getElementById('sk-header-nav');

  function openMobileMenu() {
      if (!hamburger || !mobileNav) return;
      hamburger.classList.add('is-open');
      mobileNav.classList.add('is-open');
      document.body.style.overflow = 'hidden';
  }

  function closeMobileMenu() {
      if (!hamburger || !mobileNav) return;
      hamburger.classList.remove('is-open');
      mobileNav.classList.remove('is-open');
      document.body.style.overflow = '';
  }

  window.skCloseSidenav = closeMobileMenu;

  if (hamburger && mobileNav) {
    hamburger.addEventListener('click', () => {
      const isOpen = hamburger.classList.contains('is-open');
      isOpen ? closeMobileMenu() : openMobileMenu();
    });

    mobileNav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', closeMobileMenu);
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeMobileMenu();
    });
  }
})();

/* ── NEWSLETTER SPOTLIGHT EFFECT (Framer Motion Style) ── */
(function () {
  'use strict';
  
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReduced) return;

  document.addEventListener('mousemove', function (e) {
    var card = e.target.closest('.sk-newsletter-card');
    if (!card) return;
    var rect = card.getBoundingClientRect();
    var x = e.clientX - rect.left;
    var y = e.clientY - rect.top;
    card.style.setProperty('--mouse-x', x + 'px');
    card.style.setProperty('--mouse-y', y + 'px');
  });
})();

/* ── v7.5: Select2 Accessible Name Binding ── */
(function () {
  'use strict';
  
  function bindSelect2Labels() {
    // Forminator select2 inputs are initialized dynamically
    document.querySelectorAll('.forminator-select2').forEach(function(select) {
      var label = document.querySelector('label[for="' + select.id + '"]');
      if (label && !select.getAttribute('aria-label') && !select.getAttribute('aria-labelledby')) {
        var labelId = label.id || ('sk-label-' + select.id);
        label.id = labelId;
        select.setAttribute('aria-labelledby', labelId);
      }
    });
  }

  // Run on load and whenever Forminator might dynamically render forms
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindSelect2Labels);
  } else {
    bindSelect2Labels();
  }
  
  // Also observe DOM changes for Forminator ajax loads
  if ('MutationObserver' in window) {
    var observer = new MutationObserver(function(mutations) {
      var shouldBind = false;
      mutations.forEach(function(m) {
        if (m.addedNodes && m.addedNodes.length > 0) {
          for (var i = 0; i < m.addedNodes.length; i++) {
            var node = m.addedNodes[i];
            if (node.nodeType === Node.ELEMENT_NODE && (
              node.classList.contains('forminator-select2') ||
              node.querySelector('.forminator-select2')
            )) {
              shouldBind = true;
              break;
            }
          }
        }
      });
      if (shouldBind) {
        bindSelect2Labels();
      }
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }
})();

/* ── v7.6: Dynamic Lenis Scroll Prevention for Dropdowns ── */
(function () {
  'use strict';
  
  function preventDropdownHijack() {
    document.querySelectorAll('.select2-dropdown, .forminator-select-dropdown, .select2-container, .select2-results__options').forEach(function(el) {
      if (!el.hasAttribute('data-lenis-prevent')) {
        el.setAttribute('data-lenis-prevent', 'true');
        el.setAttribute('data-lenis-prevent-touch', 'true');
        el.setAttribute('data-lenis-prevent-wheel', 'true');
      }
      
      var optionsList = el.classList.contains('select2-results__options') ? el : el.querySelector('.select2-results__options');
      if (optionsList && !optionsList._hasScrollHandler) {
        optionsList._hasScrollHandler = true;
        optionsList.addEventListener('wheel', function(e) {
          e.stopPropagation();
        }, { passive: false });
        optionsList.addEventListener('touchmove', function(e) {
          e.stopPropagation();
        }, { passive: false });
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', preventDropdownHijack);
  } else {
    preventDropdownHijack();
  }

  if ('MutationObserver' in window) {
    var observer = new MutationObserver(function(mutations) {
      var needsCheck = false;
      mutations.forEach(function(m) {
        if (m.addedNodes && m.addedNodes.length > 0) {
          for (var i = 0; i < m.addedNodes.length; i++) {
            var node = m.addedNodes[i];
            if (node.nodeType === Node.ELEMENT_NODE && (
              node.classList.contains('select2-dropdown') ||
              node.classList.contains('select2-container') ||
              node.classList.contains('select2-results__options') ||
              node.classList.contains('forminator-select-dropdown') ||
              node.querySelector('.select2-dropdown, .select2-container, .select2-results__options, .forminator-select-dropdown')
            )) {
              needsCheck = true;
              break;
            }
          }
        }
      });
      if (needsCheck) {
        preventDropdownHijack();
      }
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }
})();

/* ══════════════════════════════════════════════════════════
   PREMIUM CUSTOM CURSOR & MAGNETIC BUTTONS
   ══════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  // Only init if device has a fine pointer (mouse)
  if (!window.matchMedia('(pointer: fine)').matches) return;

  const cursor = document.createElement('div');
  cursor.className = 'sk-cursor';
  document.body.appendChild(cursor);

  let mouseX = window.innerWidth / 2;
  let mouseY = window.innerHeight / 2;
  let cursorX = mouseX;
  let cursorY = mouseY;
  let isHovering = false;

  // Track mouse movement
  document.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
  });

  // Smooth cursor follow
  function render() {
    cursorX += (mouseX - cursorX) * 0.15;
    cursorY += (mouseY - cursorY) * 0.15;
    // Use CSS vars for position so CSS transforms (like hover rotation/scale) aren't continually overwritten by JS
    cursor.style.left = `${cursorX}px`;
    cursor.style.top = `${cursorY}px`;
    requestAnimationFrame(render);
  }
  requestAnimationFrame(render);

  // Hover states for links and buttons
  const interactives = document.querySelectorAll('a, button, input, select, textarea, .sk-magnetic');
  interactives.forEach(el => {
    el.addEventListener('mouseenter', () => {
      cursor.classList.add('is-hovering');
    });
    el.addEventListener('mouseleave', () => {
      cursor.classList.remove('is-hovering');
    });
  });

  // Magnetic Effect
  const magnetics = document.querySelectorAll('.btn, .sk-magnetic, .founder-card, .forminator-button-submit, .sk-contact-fallback-form button');
  magnetics.forEach(btn => {
    btn.addEventListener('mousemove', function(e) {
      const rect = this.getBoundingClientRect();
      const h = rect.width / 2;

      const x = e.clientX - rect.left - h;
      const y = e.clientY - rect.top - (rect.height / 2);

      this.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
    });

    btn.addEventListener('mouseleave', function() {
      this.style.transform = 'translate(0px, 0px)';
      // Reset transition smoothly
      this.style.transition = 'transform 0.4s var(--ease-spring)';
      setTimeout(() => {
        this.style.transition = '';
      }, 400);
    });
  });
})();
