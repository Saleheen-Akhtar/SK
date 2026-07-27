/* Sacred Kompass — journal-filter.js v1.0
 * Live-as-you-type search filter for home.php (Journal) and category.php
 * -----------------------------------------------------------------------
 * • Filters .sk-home-featured and .sk-blog-card articles by their
 *   data-search attribute (title + category + excerpt, lowercased server-side)
 * • Shows/hides the .sk-blog-grid wrapper when all cards are hidden
 * • Shows #sk-no-results when nothing matches
 * • Debounced at 120ms for smooth feel without layout thrash
 * • Preserves the <form> and its IDs — server-side search still works
 *   when the user presses Enter or clicks the Search button
 * • Works in Chrome, Firefox, Safari, Edge — no dependencies
 */
(function () {
  'use strict';

  /* ── Wait for DOM ─────────────────────────────────────────── */
  function init() {
    var input    = document.querySelector('.sk-search-input');
    var noRes    = document.getElementById('sk-no-results');
    var postsWrap = document.querySelector('.sk-home-posts-section .wrap');

    if (!input || !postsWrap) return; /* not a journal page */

    /* Collect all filterable articles once */
    var articles = Array.prototype.slice.call(
      postsWrap.querySelectorAll('article[data-search]')
    );
    if (!articles.length) return;

    /* The grid wrapper (cards 2+) — we need to show/hide it as a unit
       so the featured article gap doesn't collapse weirdly */
    var grid = postsWrap.querySelector('.sk-blog-grid');

    /* ── Debounce helper ─────────────────────────────────────── */
    var timer;
    function debounce(fn, ms) {
      return function () {
        clearTimeout(timer);
        timer = setTimeout(fn, ms);
      };
    }

    /* ── Core filter ─────────────────────────────────────────── */
    function applyFilter() {
      var query = input.value.trim().toLowerCase();

      /* Empty query → restore everything instantly */
      if (!query) {
        articles.forEach(function (a) {
          a.style.display = '';
          a.removeAttribute('data-hidden');
        });
        if (grid) grid.style.display = '';
        if (noRes) noRes.classList.remove('visible');
        return;
      }

      /* Split query into words so "meditation women" matches both terms */
      var words = query.split(/\s+/).filter(Boolean);

      var visibleCount = 0;

      articles.forEach(function (a) {
        var haystack = (a.getAttribute('data-search') || '').toLowerCase();
        /* Article must contain ALL query words */
        var matches = words.every(function (w) { return haystack.indexOf(w) !== -1; });

        if (matches) {
          a.style.display = '';
          a.removeAttribute('data-hidden');
          visibleCount++;
        } else {
          a.style.display = 'none';
          a.setAttribute('data-hidden', 'true');
        }
      });

      /* Show/hide grid wrapper — if every card inside is hidden, collapse it */
      if (grid) {
        var gridCards = Array.prototype.slice.call(grid.querySelectorAll('article[data-search]'));
        var anyVisibleInGrid = gridCards.some(function (c) { return c.getAttribute('data-hidden') !== 'true'; });
        grid.style.display = anyVisibleInGrid ? '' : 'none';
      }

      /* No-results message */
      if (noRes) {
        if (visibleCount === 0) {
          noRes.classList.add('visible');
        } else {
          noRes.classList.remove('visible');
        }
      }
    }

    /* ── Wire up events ──────────────────────────────────────── */
    var debouncedFilter = debounce(applyFilter, 120);

    input.addEventListener('input',   debouncedFilter);
    input.addEventListener('keyup',   debouncedFilter);  /* IE/Edge fallback */
    input.addEventListener('search',  applyFilter);       /* clear button (×) on type=search */

    /* If the user navigated back with a pre-filled query, filter immediately */
    if (input.value.trim()) applyFilter();

    /* ── Prevent form submit if input is empty (UX) ──────────── */
    var form = input.closest('form');
    if (form) {
      form.addEventListener('submit', function (e) {
        /* If there's a query, let the native WordPress search run normally.
           If empty, block submit to avoid loading an empty results page. */
        if (!input.value.trim()) {
          e.preventDefault();
          input.focus();
        }
        /* else: natural form submit → WordPress search results page */
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
