<?php
/**
 * Shared Template Partial: Stories Bottom Share CTA Banner
 */
defined('ABSPATH') || exit;

$cta_heading   = sk_option('stories_cta_heading', 'Your story can inspire change.');
$cta_sub       = sk_option('stories_cta_sub', "If our work together has made an impact, we'd be honored to share your journey (anonymously if you prefer).");
$cta_btn_label = sk_option('stories_cta_btn_label', 'Share Your Story');
?>
<div class="sk-spg-cta-wrap">
  <div class="wrap sk-spg-cta">
    <div class="sk-spg-cta__left">
      <div class="sk-spg-cta__leaf" aria-hidden="true">
        <svg viewBox="0 0 120 180" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M60 170 C60 170 10 120 10 70 C10 30 35 10 60 10 C85 10 110 30 110 70 C110 120 60 170 60 170Z" stroke="var(--gold)" stroke-width="1" fill="none" opacity="0.4"/>
          <path d="M60 170 L60 10" stroke="var(--gold)" stroke-width="0.8" opacity="0.3"/>
          <path d="M60 130 C40 110 10 90 10 70" stroke="var(--gold)" stroke-width="0.6" opacity="0.25"/>
          <path d="M60 130 C80 110 110 90 110 70" stroke="var(--gold)" stroke-width="0.6" opacity="0.25"/>
        </svg>
      </div>
      <div class="sk-spg-cta__text">
        <h2 class="sk-spg-cta__heading"><?php echo esc_html($cta_heading); ?></h2>
        <p class="sk-spg-cta__sub"><?php echo esc_html($cta_sub); ?></p>
      </div>
    </div>
    <div class="sk-spg-cta__right">
      <a href="<?php echo esc_url( sk_option('contact_url', home_url('/#contact')) ); ?>" class="sk-spg-cta__btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        <?php echo esc_html($cta_btn_label); ?>
      </a>
      <p class="sk-spg-cta__privacy">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <?php esc_html_e('Your privacy is always respected.', 'sacred-kompass'); ?>
      </p>
      <div class="sk-spg-cta__img" aria-hidden="true"></div>
    </div>
  </div>
</div>
