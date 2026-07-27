/**
 * Announcement bar countdown and session-dismiss logic.
 */
(function(){
  'use strict';

  var barId = (typeof skAnnData !== 'undefined' && skAnnData.id) ? skAnnData.id : '';
  var endISO = (typeof skAnnData !== 'undefined' && skAnnData.countdown) ? skAnnData.countdown : '';
  if (!barId) return;

  var bar = document.getElementById('sk-ann-' + barId);
  if (!bar) return;

  /* ── Countdown timer ── */
  if (endISO) {
    var end = new Date(endISO);
    var hEl = bar.querySelector('[data-unit="h"]');
    var mEl = bar.querySelector('[data-unit="m"]');
    var sEl = bar.querySelector('[data-unit="s"]');

    if (hEl && mEl && sEl) {
      var pad = function(n){ return String(n).padStart(2,'0'); };
      var tick = function(){
        var diff = end - Date.now();
        if (diff <= 0) {
          bar.style.display = 'none';
          clearInterval(timer);
          return;
        }
        var totalSecs = Math.floor(diff / 1000);
        var h = Math.floor(totalSecs / 3600);
        var m = Math.floor((totalSecs % 3600) / 60);
        var s = totalSecs % 60;
        hEl.textContent = pad(h);
        mEl.textContent = pad(m);
        sEl.textContent = pad(s);
      };
      tick();
      var timer = setInterval(tick, 1000);
    }
  }

  /* ── Dismiss logic ── */
  var STORE_KEY = 'sk_ann_dismissed';
  function ssGet(k)  { try { return sessionStorage.getItem(k);  } catch(e){ return null; } }
  function ssSet(k,v){ try { sessionStorage.setItem(k, v);      } catch(e){} }

  if (ssGet(STORE_KEY) === String(barId)) {
    bar.style.display = 'none';
    return;
  }

  var btn = bar.querySelector('.sk-ann-close[data-ann-id]');
  if (btn) {
    btn.addEventListener('click', function(){
      bar.style.display = 'none';
      ssSet(STORE_KEY, String(barId));
    });
  }
})();
