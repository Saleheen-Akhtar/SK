<?php
/**
 * Sacred Kompass — FAQ Section v17 (Forced Database Sync Engine & Luminous Sun Overlay)
 * Features:
 * 1. Sun-bright transparent golden orange wash with 8px backdrop blur
 * 2. Slanted (Italicized) Speech Bubbles with 2.5px Black Outlines
 * 3. Cinemascope Thick Black Top & Bottom Borders
 * 4. Top-Right Corner Cinematic Narrator Box ("FREQUENTLY ASKED")
 * 5. Direct Database Option Sync (wp_options) for 100% position parity
 */
$faq_heading_1  = sk_option('faq_heading_1',   '');
$faq_heading_em = sk_option('faq_heading_em',  '');
$faq_sub        = sk_option('faq_sub',         '');
$faq_cta_label  = sk_option('faq_cta_label',   '');
$faq_bg         = sk_option('faq_bg_image',    '');
$faq_bg_mobile  = sk_option('faq_bg_image_mobile', '');

$faq_styles = [];
if ($faq_bg) {
    $faq_styles[] = 'background-image:url(\'' . esc_url($faq_bg) . '\')';
    $faq_styles[] = '--faq-bg:url(\'' . esc_url($faq_bg) . '\')';
}
if ($faq_bg_mobile) {
    $faq_styles[] = '--faq-bg-mobile:url(\'' . esc_url($faq_bg_mobile) . '\')';
}
$faq_style = !empty($faq_styles) ? ' style="' . implode(';', $faq_styles) . ';"' : '';

$faq_query = new WP_Query([
    'post_type'              => 'sk_faq',
    'post_status'            => 'publish',
    'posts_per_page'         => 100,
    'orderby'                => 'menu_order',
    'order'                  => 'ASC',
    'no_found_rows'          => true,
    'update_post_meta_cache' => true,
    'update_post_term_cache' => true,
]);

$faq_categories = [];

if ($faq_query->have_posts()) {
    while ($faq_query->have_posts()) {
        $faq_query->the_post();
        $q     = get_the_title();
        $a     = get_post_meta(get_the_ID(), 'faq_answer', true);
        $group = get_post_meta(get_the_ID(), 'faq_group',  true);
        if (!$a) $a = get_the_content();

        if (!empty($q) && !empty($a)) {
            $item = [
                'id'       => get_the_ID(),
                'question' => $q,
                'answer'   => $a,
            ];

            // 1. Check dedicated Taxonomy sk_faq_category first
            $terms = get_the_terms(get_the_ID(), 'sk_faq_category');
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $t) {
                    $faq_categories[$t->name][] = $item;
                }
            } elseif (!empty($group)) {
                // 2. Fallback to custom text group field
                $faq_categories[$group][] = $item;
            } else {
                // 3. General category for uncategorized posts
                $faq_categories['General Inquiries'][] = $item;
            }
        }
    }
    wp_reset_postdata();
}

$has_left_content = !empty($faq_heading_1) || !empty($faq_heading_em) || !empty($faq_sub) || !empty($faq_cta_label);
$saved_positions  = get_option('sk_faq_bubble_positions', []);
$is_admin         = current_user_can('manage_options');
$ajax_url         = admin_url('admin-ajax.php');
?>
<section class="faq-section" id="faq" aria-labelledby="faq-heading"<?php echo $faq_style; ?>>
  
  <!-- ── CINEMASCOPE OVERLAY LETTERBOX BARS (Crops background image without expanding section height) ── -->
  <div class="faq-cinemascope-bar top" aria-hidden="true"></div>
  <div class="faq-cinemascope-bar bottom" aria-hidden="true"></div>

  <!-- ── TOP-RIGHT CORNER NARRATOR BOX ── -->
  <div class="faq-narrator-box">
    <h2 class="narrator-title">Frequently Asked</h2>
    <p class="narrator-desc">
      <?php echo !empty($faq_sub) ? esc_html($faq_sub) : 'Explore frequently asked questions and spiritual inquiries below.'; ?>
    </p>
  </div>

  <div class="wrap"><div class="faq-layout<?php echo !$has_left_content ? ' no-left' : ''; ?>">
    
    <?php if ($has_left_content) : ?>
    <div class="faq-left reveal">
      <?php if ($faq_heading_1 || $faq_heading_em) : ?>
        <h2 class="display-h2" id="faq-heading">
          <?php if ($faq_heading_1) echo esc_html($faq_heading_1); ?>
          <?php if ($faq_heading_1 && $faq_heading_em) echo '<br>'; ?>
          <?php if ($faq_heading_em) : ?>
            <em><?php echo esc_html($faq_heading_em); ?></em>
          <?php endif; ?>
        </h2>
      <?php endif; ?>

      <?php if ($faq_sub) : ?>
        <p class="body-serif"><?php echo esc_html($faq_sub); ?></p>
      <?php endif; ?>

      <?php if ($faq_cta_label) : ?>
        <a href="<?php echo esc_url(sk_option('contact_url', home_url('/#contact'))); ?>" class="btn btn-outline"><?php echo esc_html($faq_cta_label); ?></a>
      <?php endif; ?>
    </div>
    <?php else : ?>
    <div class="faq-left-placeholder" aria-hidden="true"></div>
    <?php endif; ?>

    <!-- ── 100% DYNAMIC CATEGORY SPEECH BUBBLES ── -->
    <div class="faq-bubbles-container reveal d2">
      <?php if (!empty($faq_categories)) : ?>
        <div class="faq-snip-grid">
          <?php 
          $b_idx = 0;
          $y_defaults = [0, -16, 14, -20, 18];
          $x_defaults = [0, 15, -12, 22, -18];

          foreach ($faq_categories as $cat_name => $cat_items) :
            $slug      = sanitize_title($cat_name);
            $tail_dir  = ($b_idx % 2 === 0) ? 'left' : 'right';
            $anim_num  = ($b_idx % 3) + 1;
            $count     = count($cat_items);

            // Read saved X/Y position directly from WordPress database option
            $x_off = isset($saved_positions[$slug]['x']) ? intval($saved_positions[$slug]['x']) : ($x_defaults[$b_idx % count($x_defaults)]);
            $y_off = isset($saved_positions[$slug]['y']) ? intval($saved_positions[$slug]['y']) : ($y_defaults[$b_idx % count($y_defaults)]);
          ?>
            <figure class="snip1157 tail-<?php echo $tail_dir; ?> float-anim-<?php echo $anim_num; ?><?php echo $is_admin ? ' sk-admin-draggable' : ''; ?>" 
                    id="bubble-fig-<?php echo esc_attr($slug); ?>"
                    style="--bubble-x: <?php echo $x_off; ?>px; --bubble-y: <?php echo $y_off; ?>px; transform: translate(<?php echo $x_off; ?>px, <?php echo $y_off; ?>px);"
                    data-bubble-slug="<?php echo esc_attr($slug); ?>"
                    data-x="<?php echo $x_off; ?>"
                    data-y="<?php echo $y_off; ?>"
                    data-open-faq-modal="faq-modal-<?php echo esc_attr($slug); ?>"
                    role="button"
                    tabindex="0"
                    aria-label="Explore <?php echo esc_attr($cat_name); ?> FAQs">
              
              <?php if ($is_admin) : ?>
                <span class="sk-admin-drag-handle" title="Drag to reposition (Auto-saves to database on release)">🖐️ Drag Me</span>
              <?php endif; ?>

              <blockquote class="snip-bubble-box">
                <div class="snip-inner-content">
                  <h3 class="snip-cat-title"><?php echo esc_html($cat_name); ?></h3>
                  <span class="snip-cat-count"><?php echo $count; ?> <?php echo $count === 1 ? 'Question' : 'Questions'; ?> →</span>
                </div>
                <div class="arrow arrow-<?php echo $tail_dir; ?>"></div>
              </blockquote>

            </figure>
          <?php 
            $b_idx++;
          endforeach; 
          ?>
        </div>

      <?php else : ?>
        <div class="faq-empty-state">
          <p class="body-serif">No FAQ categories available yet. Please add FAQ items in WordPress Admin → FAQ.</p>
        </div>
      <?php endif; ?>
    </div>

  </div></div>
</section>

<!-- ── SACRED CATEGORY MODAL OVERLAYS ── -->
<?php if (!empty($faq_categories)) : ?>
<div id="sk-faq-modals-holder">
  <?php foreach ($faq_categories as $cat_name => $cat_items) : 
    $slug = sanitize_title($cat_name);
  ?>
  <div class="faq-modal-overlay" id="faq-modal-<?php echo esc_attr($slug); ?>" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="faq-modal-backdrop" data-close-faq-modal></div>
    <div class="faq-modal-card">
      <button type="button" class="faq-modal-close" data-close-faq-modal aria-label="Close modal">✕</button>
      <div class="faq-modal-header">
        <span class="faq-modal-ornament">✦ ✧ ✦</span>
        <p class="faq-modal-eyebrow">Category Archive</p>
        <h3 class="faq-modal-heading"><?php echo esc_html($cat_name); ?></h3>
      </div>
      <div class="faq-modal-body">
        <div class="faq-modal-accordion">
          <?php foreach ($cat_items as $q_idx => $item) : ?>
            <div class="faq-modal-item">
              <button type="button" 
                      class="faq-modal-trigger" 
                      aria-expanded="<?php echo $q_idx === 0 ? 'true' : 'false'; ?>" 
                      aria-controls="faq-ans-<?php echo esc_attr($slug . '-' . $q_idx); ?>">
                <span class="faq-modal-q"><?php echo esc_html($item['question']); ?></span>
                <span class="faq-modal-icon" aria-hidden="true"><?php echo $q_idx === 0 ? '−' : '+'; ?></span>
              </button>
              <div class="faq-modal-answer<?php echo $q_idx === 0 ? ' is-open' : ''; ?>" id="faq-ans-<?php echo esc_attr($slug . '-' . $q_idx); ?>">
                <div class="faq-modal-ans-inner">
                  <p><?php echo esc_html($item['answer']); ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ── FAQ SCRIPT: MODALS TO BODY & SEAMLESS AUTHENTICATED AUTO-SAVE DRAG & DROP ENGINE ── -->
<script>
(function() {
  'use strict';

  function initFaqSystem() {
    var holder = document.getElementById('sk-faq-modals-holder');
    if (holder && holder.parentNode !== document.body) {
      document.body.appendChild(holder);
    }

    var isDraggingHappened = false;

    // Open modal handler
    document.querySelectorAll('[data-open-faq-modal]').forEach(function(btn) {
      function triggerModal(e) {
        if (isDraggingHappened) {
          isDraggingHappened = false;
          return;
        }
        e.preventDefault();
        var modalId = btn.getAttribute('data-open-faq-modal');
        var modal = document.getElementById(modalId);
        if (modal) {
          document.querySelectorAll('.faq-modal-overlay.is-active').forEach(function(m) {
            m.classList.remove('is-active');
            m.setAttribute('aria-hidden', 'true');
          });
          modal.classList.add('is-active');
          modal.setAttribute('aria-hidden', 'false');
          document.body.style.overflow = 'hidden';
        }
      }
      btn.addEventListener('click', triggerModal);
    });

    // Close modal handler
    document.querySelectorAll('[data-close-faq-modal]').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        var modal = btn.closest('.faq-modal-overlay');
        if (modal) {
          modal.classList.remove('is-active');
          modal.setAttribute('aria-hidden', 'true');
          document.body.style.overflow = '';
        }
      });
    });

    // ESC key listener
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        document.querySelectorAll('.faq-modal-overlay.is-active').forEach(function(modal) {
          modal.classList.remove('is-active');
          modal.setAttribute('aria-hidden', 'true');
          document.body.style.overflow = '';
        });
      }
    });

    // Accordion inside modal
    document.querySelectorAll('.faq-modal-trigger').forEach(function(trigger) {
      trigger.addEventListener('click', function(e) {
        e.preventDefault();
        var isExpanded = trigger.getAttribute('aria-expanded') === 'true';
        var answerEl = trigger.nextElementSibling;
        var parentAccordion = trigger.closest('.faq-modal-accordion');

        if (parentAccordion) {
          parentAccordion.querySelectorAll('.faq-modal-trigger').forEach(function(t) {
            t.setAttribute('aria-expanded', 'false');
            var icon = t.querySelector('.faq-modal-icon');
            if (icon) icon.textContent = '+';
          });
          parentAccordion.querySelectorAll('.faq-modal-answer').forEach(function(a) {
            a.classList.remove('is-open');
          });
        }

        if (!isExpanded && answerEl) {
          trigger.setAttribute('aria-expanded', 'true');
          answerEl.classList.add('is-open');
          var icon = trigger.querySelector('.faq-modal-icon');
          if (icon) icon.textContent = '−';
        }
      });
    });

    // ── ADMIN AUTO-SAVE DRAG AND DROP ENGINE (ADMIN ONLY) ──
    var draggables = document.querySelectorAll('.sk-admin-draggable');
    if (draggables.length > 0) {
      draggables.forEach(function(el) {
        var startX = 0, startY = 0, initialX = 0, initialY = 0;
        var isPointerDown = false;

        function getPos(e) {
          if (e.touches && e.touches.length > 0) {
            return { x: e.touches[0].clientX, y: e.touches[0].clientY };
          }
          return { x: e.clientX, y: e.clientY };
        }

        function onPointerDown(e) {
          if (e.button && e.button !== 0) return;
          isPointerDown = true;
          isDraggingHappened = false;

          var p = getPos(e);
          startX = p.x;
          startY = p.y;
          initialX = parseInt(el.getAttribute('data-x') || '0', 10);
          initialY = parseInt(el.getAttribute('data-y') || '0', 10);

          el.classList.add('is-dragging');

          window.addEventListener('pointermove', onPointerMove, { passive: false });
          window.addEventListener('pointerup', onPointerUp);
          window.addEventListener('touchmove', onPointerMove, { passive: false });
          window.addEventListener('touchend', onPointerUp);

          if (e.cancelable) e.preventDefault();
        }

        function onPointerMove(e) {
          if (!isPointerDown) return;
          var p = getPos(e);
          var deltaX = p.x - startX;
          var deltaY = p.y - startY;

          if (Math.abs(deltaX) > 3 || Math.abs(deltaY) > 3) {
            isDraggingHappened = true;
          }

          var nextX = initialX + deltaX;
          var nextY = initialY + deltaY;

          el.style.setProperty('--bubble-x', nextX + 'px');
          el.style.setProperty('--bubble-y', nextY + 'px');
          el.style.transform = 'translate(' + nextX + 'px, ' + nextY + 'px)';
          el.setAttribute('data-x', nextX);
          el.setAttribute('data-y', nextY);

          if (e.cancelable) e.preventDefault();
        }

        function onPointerUp(e) {
          if (!isPointerDown) return;
          isPointerDown = false;
          el.classList.remove('is-dragging');

          window.removeEventListener('pointermove', onPointerMove);
          window.removeEventListener('pointerup', onPointerUp);
          window.removeEventListener('touchmove', onPointerMove);
          window.removeEventListener('touchend', onPointerUp);

          // Automatically save positions via authenticated AJAX when drag stops!
          if (isDraggingHappened) {
            autoSaveBubblePositions(el);
          }
        }

        var handle = el.querySelector('.sk-admin-drag-handle');
        if (handle) {
          handle.addEventListener('pointerdown', onPointerDown);
          handle.addEventListener('touchstart', onPointerDown, { passive: false });
        }
        var box = el.querySelector('.snip-bubble-box');
        if (box) {
          box.addEventListener('pointerdown', onPointerDown);
          box.addEventListener('touchstart', onPointerDown, { passive: false });
        }
      });

      // Authenticated Background Auto-Save Handler
      function autoSaveBubblePositions(draggedEl) {
        var handle = draggedEl ? draggedEl.querySelector('.sk-admin-drag-handle') : null;
        if (handle) handle.textContent = '⏳ Saving...';

        var positions = {};
        document.querySelectorAll('.snip1157[data-bubble-slug]').forEach(function(fig) {
          var slug = fig.getAttribute('data-bubble-slug');
          var x = fig.getAttribute('data-x') || '0';
          var y = fig.getAttribute('data-y') || '0';
          if (slug) {
            positions[slug] = { x: x, y: y };
          }
        });

        var formData = new FormData();
        formData.append('action', 'sk_save_bubble_positions');
        for (var s in positions) {
          formData.append('positions[' + s + '][x]', positions[s].x);
          formData.append('positions[' + s + '][y]', positions[s].y);
        }

        fetch('<?php echo esc_js($ajax_url); ?>', {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
          console.log('AJAX Auto-Save Result:', data);
          if (data.success && handle) {
            handle.textContent = '✓ Saved!';
            setTimeout(function() { handle.textContent = '🖐️ Drag Me'; }, 1500);
          } else if (handle) {
            handle.textContent = '❌ Fail';
            setTimeout(function() { handle.textContent = '🖐️ Drag Me'; }, 2000);
          }
        })
        .catch(function(err) {
          console.error('AJAX Auto-Save Error:', err);
          if (handle) {
            handle.textContent = '❌ Error';
            setTimeout(function() { handle.textContent = '🖐️ Drag Me'; }, 2000);
          }
        });
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFaqSystem);
  } else {
    initFaqSystem();
  }
})();
</script>
