<?php
/**
 * CTA / Contact Section (Podium Style)
 */
$email        = sk_option('footer_email',        'collective@sacredkompass.org');
$phone        = sk_option('footer_phone',        '+65 8434 3915');
$phone_clean  = preg_replace('/[^+0-9]/', '', $phone);
$eyebrow      = sk_option('cta_eyebrow',         'Connect');
$heading_raw  = sk_option('cta_heading',         'Get in Touch');
$sub          = sk_option('cta_sub',             "We're here to listen and support you on your journey of transformation.");
$form_id      = sk_option('forminator_form_id',  '');
?>

<section class="sk-podium-cta section-py" id="contact" style="background-color:var(--color-surface-strong); border-top:1px solid var(--color-surface-base);">
  <div class="wrap stagger-children">
    <div style="display:grid; grid-template-columns:1fr; gap:var(--space-2);">

      <div class="cta-left">
        <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
        <h2 class="display-h2" id="cta-heading-el" style="margin-bottom:var(--space-1);"><?php echo wp_kses_post($heading_raw); ?></h2>
        <p class="body-serif" style="margin-bottom:var(--space-2); max-width:600px;"><?php echo esc_html($sub); ?></p>

        <div class="body-ui" style="display:flex; flex-direction:column; gap:1rem;">
          <a href="mailto:<?php echo esc_attr($email); ?>" style="text-decoration:underline;"><?php echo esc_html($email); ?></a>
          <a href="tel:<?php echo esc_attr($phone_clean); ?>"><?php echo esc_html($phone); ?></a>
        </div>
      </div>

      <div class="cta-right">
        <?php if ($form_id && has_shortcode('[forminator_form id="' . $form_id . '"]', 'forminator_form')): ?>
          <?php echo do_shortcode('[forminator_form id="' . esc_attr($form_id) . '"]'); ?>
        <?php else: ?>
          <p class="body-ui">Contact form is currently unavailable. Please reach out via email.</p>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<style>
@media (min-width: 768px) {
  .sk-podium-cta > .wrap > div { grid-template-columns: 1fr 1fr; }
}
/* Override Forminator Base Styles to match Podium */
.forminator-custom-form { font-family: var(--font-family-primary) !important; }
.forminator-label { color: var(--color-text-tertiary) !important; font-size: var(--font-size-sm) !important; }
.forminator-input, .forminator-textarea { background: var(--color-surface-base) !important; border: 1px solid var(--color-text-tertiary) !important; color: var(--color-text-primary) !important; border-radius: 0 !important; }
.forminator-button-submit { background: var(--color-text-primary) !important; color: var(--color-surface-base) !important; border-radius: 0 !important; text-transform: uppercase; font-weight: bold; }
</style>
