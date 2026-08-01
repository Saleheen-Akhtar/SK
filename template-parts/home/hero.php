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
<section class="hero sk-podium-hero" aria-label="<?php esc_attr_e('Welcome','sacred-kompass'); ?>">
  <h1 class="sr-only">Sacred Kompass — Consciousness and Transformation</h1>

  <?php if (!empty($bg_video)): ?>
  <div class="hero-bg-video-minimal" aria-hidden="true">
    <video autoplay muted loop playsinline disableRemotePlayback>
      <source src="<?php echo esc_url($bg_video); ?>" type="video/mp4">
    </video>
  </div>
  <?php elseif ($bg_img): ?>
    <div class="hero-bg-video-minimal" aria-hidden="true">
        <img src="<?php echo esc_url($bg_img); ?>" alt="" role="presentation" loading="eager" fetchpriority="high" />
    </div>
  <?php endif; ?>

  <div class="wrap">
    <div class="hero-content reveal">
      <h2 class="display-impact">
        <?php echo esc_html(sk_option('options_sk_hero_eyebrow', 'Sacred Kompass')); ?>
      </h2>
      <p class="body-ui">
        <?php echo esc_html(sk_option('options_sk_about_expression_line', 'Consciousness and transformation.')); ?>
      </p>
    </div>
  </div>
</section>

<?php /* ══ SACRED DIVIDER — removed per v7 fix ══
       Hero divider/lotus/mist removed — clean transition via CSS.
       (Commented-out markup removed to avoid nested-comment rendering bug
        where inner --> tokens closed the outer comment prematurely.) */ ?>


<!-- Hero cycling script enqueued via setup.php -->
