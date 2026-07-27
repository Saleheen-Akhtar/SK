<?php
/**
 * Hero — v15: FROM top-left, TO large centered, locked positions, sequential fade.
 * All content via Sacred Kompass → Settings → Hero (CPT).
 */
$bg_img        = sk_option('hero_bg_image', '');
$bg_img_mobile = sk_option('hero_bg_image_mobile', '');
$bg_video      = sk_option('hero_bg_video', '');

$transform_pairs = sk_repeater('options_sk_hero_pairs_json') ?: sk_default_hero_pairs();
?>
<section class="hero hero--fullscreen" aria-label="<?php esc_attr_e('Welcome to Sacred Kompass','sacred-kompass'); ?>">
  <h1 class="sr-only">Sacred Kompass — Consciousness and Transformation</h1>

  <!-- Background -->
  <div class="hero-bg-layer" aria-hidden="true">
    <?php if (!empty($bg_video)): ?>
      <div class="hero-bg-video">
        <?php
        /**
         * WCAG 1.2.2 / 1.2.4 Video Captions Exception:
         * The background hero video has no audio or spoken dialogue. It is purely decorative/atmospheric.
         * A text track is intentionally omitted here as permitted by accessibility standards.
         */
        ?>
        <video
          autoplay
          muted
          loop
          playsinline
          preload="none"
          crossorigin="anonymous"
          disableRemotePlayback
          aria-hidden="true"
          tabindex="-1"
          data-src="<?php echo esc_url($bg_video); ?>"
          <?php if ($bg_img): ?>poster="<?php echo esc_url($bg_img); ?>"<?php endif; ?>
        >

        </video>
        <?php if ($bg_img): ?>
          <!-- Poster image also shown while video loads -->
          <noscript><img src="<?php echo esc_url($bg_img); ?>" alt="" /></noscript>
        <?php endif; ?>
      </div>
    <?php elseif ($bg_img): ?>
      <div class="hero-bg-image">
        <picture>
          <?php if ($bg_img_mobile): ?>
            <source media="(max-width: 1024px)" srcset="<?php echo esc_url($bg_img_mobile); ?>" />
          <?php endif; ?>
          <img src="<?php echo esc_url($bg_img); ?>" alt="" role="presentation" loading="eager" fetchpriority="high" />
        </picture>
      </div>
    <?php else: ?>
      <div class="hero-bg-gradient"></div>
    <?php endif; ?>
    <div class="hero-bg-overlay"></div>
  </div>


  <!-- FROM word — top-left, close to middle line but above it -->
  <div class="hero-from-wrap" aria-hidden="true">
    <span class="hcw-from" id="hero-from"></span>
  </div>

  <!-- TO word — large, absolutely centered slightly below middle -->
  <div class="hero-to-wrap" aria-live="polite" aria-atomic="true">
    <span class="hcw-to" id="hero-to"></span>
  </div>

</section>

<?php /* ══ SACRED DIVIDER — removed per v7 fix ══
       Hero divider/lotus/mist removed — clean transition via CSS.
       (Commented-out markup removed to avoid nested-comment rendering bug
        where inner --> tokens closed the outer comment prematurely.) */ ?>


<!-- Hero cycling script enqueued via setup.php -->
