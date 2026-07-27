/**
 * Auto-select service or pre-fill artwork from URL parameters (handles async forms)
 */
(function () {
  'use strict';

  function selectService() {
    var params = new URLSearchParams(window.location.search);
    
    // Check for artwork parameter
    var artwork = params.get('artwork');
    if (artwork) {
      var attempts = 0;
      var interval = setInterval(function () {
        var textarea = document.querySelector('#contact textarea[name="textarea-1"]') || 
                       document.querySelector('#contact textarea');
        var sel = document.getElementById('sk-service') || document.getElementById('sk-cf-service') || document.querySelector('#contact select');
        
        if (textarea) {
          textarea.value = 'Hello, I am interested in inquiring about the artwork: "' + artwork + '".';
          
          if (sel) {
            var notSureOpt = sel.querySelector('option[value^="Not-sure"]');
            if (notSureOpt) {
              sel.value = notSureOpt.value;
            }
          }
          clearInterval(interval);
        }
        
        attempts++;
        if (attempts > 50) { // Timeout after 5 seconds
          clearInterval(interval);
        }
      }, 100);

      var section = document.getElementById('contact');
      if (section) {
        setTimeout(function() {
          section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
      }
      
      history.replaceState(null, '', window.location.pathname + window.location.hash);
      return;
    }

    // Legacy service selection
    var svc = params.get('service');
    if (svc) {
      var attempts = 0;
      var interval = setInterval(function () {
        var sel = document.getElementById('sk-service') || document.getElementById('sk-cf-service');
        if (sel) {
          var opt = sel.querySelector('option[value="' + CSS.escape(svc) + '"]');
          if (opt) {
            sel.value = svc;
            sel.style.transition = 'box-shadow .4s';
            sel.style.boxShadow  = '0 0 0 2px rgba(184,98,63,.45)';
            setTimeout(function () { sel.style.boxShadow = ''; }, 2200);
            
            var section = document.getElementById('contact');
            if (section) section.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
          clearInterval(interval);
        }
        attempts++;
        if (attempts > 50) {
          clearInterval(interval);
        }
      }, 100);
      
      history.replaceState(null, '', window.location.pathname + window.location.hash);
    }
  }

  function injectResetButton() {
    var submitBtns = document.querySelectorAll('#contact .forminator-button-submit');
    submitBtns.forEach(function (submitBtn) {
      var container = submitBtn.parentNode;
      if (container.querySelector('.sk-form-reset')) return;

      var resetBtn = document.createElement('button');
      resetBtn.type = 'button';
      resetBtn.className = 'sk-form-reset';
      resetBtn.textContent = 'RESET';
      
      resetBtn.addEventListener('click', function () {
        var form = submitBtn.closest('form');
        if (form) {
          form.reset();
          // Clear standard selects & custom select2/Forminator dropdown selection styling
          var selects = form.querySelectorAll('select');
          selects.forEach(function(select) {
            select.value = '';
            var event = document.createEvent('HTMLEvents');
            event.initEvent('change', true, true);
            select.dispatchEvent(event);
          });
          // For Forminator select fields
          var forminatorSelects = form.querySelectorAll('.forminator-select2');
          forminatorSelects.forEach(function(fs) {
            try {
              jQuery(fs).val('').trigger('change');
            } catch(e) {}
          });
        }
      });

      submitBtn.before(resetBtn);
      container.classList.add('sk-button-group');
    });
  }

  function positionTextareaCounter() {
    var textareas = document.querySelectorAll('#contact textarea');
    textareas.forEach(function(ta) {
      var field = ta.closest('.forminator-field') || ta.closest('.forminator-col-12') || ta.parentNode;
      var desc = field ? field.querySelector('.forminator-description') : null;
      if (desc && !ta.parentNode.classList.contains('sk-textarea-wrap')) {
        var wrap = document.createElement('div');
        wrap.className = 'sk-textarea-wrap';
        ta.parentNode.insertBefore(wrap, ta);
        wrap.appendChild(ta);
        wrap.appendChild(desc);
      }
    });
  }

  function linkPrivacyPolicy() {
    var targets = document.querySelectorAll('#contact .forminator-checkbox, #contact .forminator-field-consent, #contact label, .cta-form-col .forminator-checkbox, .cta-form-col label, .forminator-field-consent');
    targets.forEach(function(el) {
      if (el.getAttribute('data-privacy-linked') === 'true') return;
      var html = el.innerHTML;
      if (!html) return;

      var modified = false;
      if (html.toLowerCase().indexOf('privacy policy') !== -1 && html.indexOf('/privacy-policy/') === -1) {
        html = html.replace(/privacy policy/gi, '<a href="https://sacredkompass.org/privacy-policy/" target="_blank" rel="noopener" class="sk-privacy-link">privacy policy</a>');
        modified = true;
      }
      if (html.toLowerCase().indexOf('terms and conditions') !== -1 && html.indexOf('/terms-and-conditions/') === -1) {
        html = html.replace(/terms and conditions/gi, '<a href="https://sacredkompass.org/terms-and-conditions/" target="_blank" rel="noopener" class="sk-privacy-link">terms and conditions</a>');
        modified = true;
      } else if (html.toLowerCase().indexOf('terms of use') !== -1 && html.indexOf('/terms-and-conditions/') === -1) {
        html = html.replace(/terms of use/gi, '<a href="https://sacredkompass.org/terms-and-conditions/" target="_blank" rel="noopener" class="sk-privacy-link">terms of use</a>');
        modified = true;
      }

      if (modified) {
        el.innerHTML = html;
        el.setAttribute('data-privacy-linked', 'true');
      }
    });
  }

  function colorServiceSelect() {
    var targets = document.querySelectorAll('#contact select, #contact .select2-selection--single, .cta-form-col select, .cta-form-col .select2-selection--single');
    targets.forEach(function(el) {
      el.style.setProperty('background-color', 'rgba(62, 107, 102, 0.14)', 'important');
      el.style.setProperty('background', 'rgba(62, 107, 102, 0.14)', 'important');
      el.style.setProperty('border-color', 'rgba(62, 107, 102, 0.38)', 'important');
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      selectService();
      colorServiceSelect();
      setInterval(function() {
        injectResetButton();
        positionTextareaCounter();
        linkPrivacyPolicy();
        colorServiceSelect();
      }, 400);
    });
  } else {
    selectService();
    colorServiceSelect();
    setInterval(function() {
      injectResetButton();
      positionTextareaCounter();
      linkPrivacyPolicy();
      colorServiceSelect();
    }, 400);
  }
})();
