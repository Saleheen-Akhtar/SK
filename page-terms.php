<?php
/**
 * Template Name: Terms of Use
 * Content is now editable via WP Admin → ★ Sacred Kompass → Legal Pages
 * (post slug: terms-of-use)
 */
get_header();

$legal = sk_get_legal_page('terms-of-use');
$eyebrow        = $legal['eyebrow']        ?? 'Sacred Kompass Collective';
$title          = $legal['title']          ?: 'Terms of Use';
$effective_date = $legal['effective_date'] ?? '24 March 2026';
$location       = $legal['location']       ?? 'Singapore';
$content        = $legal['content']        ?? '';
?>

<main class="sk-page-main" style="padding:var(--space-3) 0 var(--space-2);min-height:80vh;background-color:var(--color-surface-base);">
  <div class="wrap-narrow">
    <div class="legal-hero" style="margin-bottom:var(--space-2);">
      <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
      <h1 class="display-h2"><?php echo esc_html($title); ?></h1>
      <p class="body-small">
        <?php echo esc_html__('Effective Date:', 'sacred-kompass'); ?> <?php echo esc_html($effective_date); ?>
        &nbsp;·&nbsp; <?php echo esc_html($location); ?>
      </p>
    </div>

    <article class="legal-body body-ui">
      <?php echo wp_kses_post($content); ?>
    </article>
  </div>
</main>

<?php get_footer(); ?>
