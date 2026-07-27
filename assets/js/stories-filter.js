/**
 * Client Stories filter logic.
 */
(function () {
  'use strict';

  function initFilterAndSearch() {
    var filterBtns = document.querySelectorAll('.sk-spg-filter-btn');
    var items      = document.querySelectorAll('[data-story-item]');
    var noResults  = document.getElementById('sk-spg-no-results');
    var searchInput = document.getElementById('sk-stories-search');

    if (!items.length) return;

    var activeFilter = 'all';

    function update() {
      var searchVal = searchInput ? searchInput.value.toLowerCase().trim() : '';
      var visible = 0;

      items.forEach(function (item) {
        var cat = item.getAttribute('data-category');
        var titleEl = item.querySelector('.sk-spg-card-title');
        var excerptEl = item.querySelector('.sk-spg-card-excerpt');
        var catEl = item.querySelector('.sk-spg-card-cat');

        var searchText = '';
        if (titleEl) searchText += titleEl.textContent.toLowerCase() + ' ';
        if (excerptEl) searchText += excerptEl.textContent.toLowerCase() + ' ';
        if (catEl) searchText += catEl.textContent.toLowerCase() + ' ';

        var matchesCategory = activeFilter === 'all' || cat === activeFilter;
        var matchesSearch = searchVal === '' || searchText.indexOf(searchVal) !== -1;
        var show = matchesCategory && matchesSearch;

        item.style.display = show ? '' : 'none';
        if (show) visible++;
      });

      if (noResults) {
        noResults.style.display = visible === 0 ? 'block' : 'none';
      }
    }

    if (filterBtns.length) {
      filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          filterBtns.forEach(function (b) { b.classList.remove('active'); });
          this.classList.add('active');
          activeFilter = this.getAttribute('data-filter');
          update();
        });
      });
    }

    if (searchInput) {
      searchInput.addEventListener('input', update);
    }
  }

  function initSort() {
    var trigger = document.getElementById('sort-trigger');
    var dropdown = document.getElementById('sort-dropdown');
    var label = document.getElementById('sort-label');
    var options = document.querySelectorAll('.sk-spg-sort-option');
    var grid = document.getElementById('sk-spg-grid');

    if (!trigger || !dropdown || !grid) return;

    trigger.addEventListener('click', function (e) {
      e.stopPropagation();
      var isExpanded = trigger.getAttribute('aria-expanded') === 'true';
      trigger.setAttribute('aria-expanded', !isExpanded);
      dropdown.style.display = isExpanded ? 'none' : 'block';
    });

    options.forEach(function (opt) {
      opt.addEventListener('click', function (e) {
        e.stopPropagation();
        var value = this.getAttribute('data-value');
        var text = this.textContent;

        options.forEach(function (o) {
          o.classList.remove('active');
          o.setAttribute('aria-selected', 'false');
        });
        this.classList.add('active');
        this.setAttribute('aria-selected', 'true');

        if (label) label.textContent = text;
        trigger.setAttribute('aria-expanded', 'false');
        dropdown.style.display = 'none';

        sortGrid(value);
      });
    });

    document.addEventListener('click', function () {
      trigger.setAttribute('aria-expanded', 'false');
      dropdown.style.display = 'none';
    });

    function sortGrid(sortType) {
      var cardsArray = Array.prototype.slice.call(grid.querySelectorAll('[data-story-item]'));
      cardsArray.sort(function (a, b) {
        var tA = parseInt(a.getAttribute('data-timestamp'), 10) || 0;
        var tB = parseInt(b.getAttribute('data-timestamp'), 10) || 0;
        return sortType === 'newest' ? (tB - tA) : (tA - tB);
      });

      cardsArray.forEach(function (card) {
        grid.appendChild(card);
      });
    }
  }

  function init() {
    initFilterAndSearch();
    initSort();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
