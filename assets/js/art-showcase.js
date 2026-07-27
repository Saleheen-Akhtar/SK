/**
 * Sacred Kompass — Art for Peace Gallery Animations & Pagination
 */
(function () {
  'use strict';

  function initArtShowcase() {
    const grid = document.querySelector('.sk-art-grid');
    if (!grid) return;

    const cards = Array.from(grid.querySelectorAll('.sk-art-card'));
    if (cards.length === 0) return;

    const prevBtn = document.querySelector('.sk-art-prev');
    const nextBtn = document.querySelector('.sk-art-next');
    const dotsContainer = document.querySelector('.sk-art-nav-dots');

    const cardsPerPage = 3;
    const totalPages = Math.ceil(cards.length / cardsPerPage);
    let currentPage = 0;
    let isTransitioning = false;

    // Create Aceternity-style sliding hover backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'sk-art-hover-backdrop';
    grid.appendChild(backdrop);

    function updateBackdrop(card) {
      const padding = 12;
      const top = card.offsetTop - padding;
      const left = card.offsetLeft - padding;
      const width = card.offsetWidth + (padding * 2);
      const height = card.offsetHeight + (padding * 2);

      backdrop.style.width = `${width}px`;
      backdrop.style.height = `${height}px`;
      backdrop.style.transform = `translate3d(${left}px, ${top}px, 0)`;
      backdrop.style.opacity = '1';
    }

    cards.forEach(card => {
      card.addEventListener('mouseenter', () => {
        if (card.style.display !== 'none') {
          updateBackdrop(card);
        }
      });
    });

    grid.addEventListener('mouseleave', () => {
      backdrop.style.opacity = '0';
    });

    // Set up initial visibility
    function updateCardVisibility(instant) {
      // Hide backdrop on transition
      backdrop.style.opacity = '0';

      const start = currentPage * cardsPerPage;
      const end = start + cardsPerPage;

      const outgoing = [];
      const incoming = [];

      cards.forEach((card, idx) => {
        const isCurrentlyVisible = card.style.display !== 'none';
        const shouldBeVisible = idx >= start && idx < end;

        if (shouldBeVisible) {
          incoming.push(card);
        } else if (isCurrentlyVisible) {
          outgoing.push(card);
        }
      });

      // Update button disabled states
      if (prevBtn) prevBtn.disabled = currentPage === 0;
      if (nextBtn) nextBtn.disabled = currentPage === totalPages - 1;

      // Update dots
      if (dotsContainer) {
        const dots = dotsContainer.querySelectorAll('.sk-art-nav-dot');
        dots.forEach((dot, idx) => {
          if (idx === currentPage) {
            dot.classList.add('active');
          } else {
            dot.classList.remove('active');
          }
        });
      }

      if (instant || outgoing.length === 0 || typeof gsap === 'undefined') {
        cards.forEach((card, idx) => {
          if (idx >= start && idx < end) {
            card.style.display = '';
            card.style.opacity = '1';
            card.style.transform = 'none';
          } else {
            card.style.display = 'none';
          }
        });
      } else {
        isTransitioning = true;
        
        // 1. Fade out outgoing cards with a slight upwards slide
        gsap.to(outgoing, {
          opacity: 0,
          y: -15,
          duration: 0.25,
          ease: 'power2.in',
          stagger: 0.04,
          onComplete: () => {
            outgoing.forEach(card => {
              card.style.display = 'none';
            });

            // 2. Prepare incoming cards (hidden and shifted down)
            incoming.forEach(card => {
              card.style.display = '';
              gsap.set(card, { opacity: 0, y: 15 });
            });

            // 3. Fade in incoming cards with stagger
            gsap.to(incoming, {
              opacity: 1,
              y: 0,
              duration: 0.45,
              stagger: 0.06,
              ease: 'power2.out',
              clearProps: 'transform',
              onComplete: () => {
                isTransitioning = false;
              }
            });
          }
        });
      }
    }

    if (cards.length > cardsPerPage) {
      // Create dots
      if (dotsContainer) {
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalPages; i++) {
          const dot = document.createElement('button');
          dot.className = `sk-art-nav-dot${i === 0 ? ' active' : ''}`;
          dot.setAttribute('data-page', i);
          dot.setAttribute('aria-label', `Go to artworks page ${i + 1}`);
          dotsContainer.appendChild(dot);

          dot.addEventListener('click', function () {
            if (currentPage !== i && !isTransitioning) {
              currentPage = i;
              updateCardVisibility(false);
            }
          });
        }
      }

      // Arrow events
      if (prevBtn) {
        prevBtn.addEventListener('click', function () {
          if (currentPage > 0 && !isTransitioning) {
            currentPage--;
            updateCardVisibility(false);
          }
        });
      }

      if (nextBtn) {
        nextBtn.addEventListener('click', function () {
          if (currentPage < totalPages - 1 && !isTransitioning) {
            currentPage++;
            updateCardVisibility(false);
          }
        });
      }
    }

    // Initialize visibility instantly on load
    updateCardVisibility(true);

    // Stagger animation on scroll for the first page cards
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
      const visibleCards = cards.slice(0, cardsPerPage);
      gsap.from(visibleCards, {
        scrollTrigger: {
          trigger: '#art',
          start: 'top 75%',
          toggleActions: 'play none none none'
        },
        y: 24,
        opacity: 0,
        duration: 0.7,
        stagger: 0.08,
        ease: 'power2.out',
        clearProps: 'all'
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initArtShowcase);
  } else {
    initArtShowcase();
  }
})();
