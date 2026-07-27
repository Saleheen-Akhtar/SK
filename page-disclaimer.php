<?php
/**
 * Template Name: Disclaimer
 * Content is now editable via WP Admin → ★ Sacred Kompass → Legal Pages
 * (post slug: disclaimer)
 *
 * Falls back to empty state if no sk_legal post exists (run "Install Demo Content" in admin settings to seed default legal copy).
 */
get_header();

$legal = sk_get_legal_page('disclaimer');

$eyebrow        = $legal['eyebrow']        ?? 'Sacred Kompass Collective';
$title          = $legal['title']          ?: 'Disclaimer';
$effective_date = $legal['effective_date'] ?? '24 March 2026';
$location       = $legal['location']       ?? 'Singapore';
$content        = $legal['content']        ?? '';
?>


<section class="legal-hero">
  <div class="wrap">
    <div class="eyebrow eyebrow-center"><?php echo esc_html($eyebrow); ?></div>
    <h1><?php echo esc_html($title); ?></h1>
    <p class="legal-meta">
      <?php echo esc_html__('Effective Date:', 'sacred-kompass'); ?> <?php echo esc_html($effective_date); ?>
      &nbsp;·&nbsp; <?php echo esc_html($location); ?>
    </p>
  </div>
</section>

<article class="legal-body">
  <?php echo wp_kses_post($content); ?>
</article>

<?php get_footer(); ?>
