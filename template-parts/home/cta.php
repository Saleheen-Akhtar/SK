<?php
/**
 * Sacred Kompass — CTA / Contact Section
 * Redesigned v3: full-bleed background image (celestial art baked in).
 * No SVG decorations in markup — they live in the background photo.
 * No card wrapper — form fields float directly over the background.
 * Left: eyebrow + heading + divider + tagline + icon contact links.
 * Right: bare Forminator form (or fallback), no box.
 */
$email        = sk_option('footer_email',        'collective@sacredkompass.org');
$phone        = sk_option('footer_phone',        '+65 8434 3915');
$phone_clean  = preg_replace('/[^+0-9]/', '', $phone);
$eyebrow      = sk_option('cta_eyebrow',         'Connect');
$heading_raw  = sk_option('cta_heading',         '');
$sub          = sk_option('cta_sub',             "We're here to listen and support you on your journey of transformation.");
$form_id      = sk_option('forminator_form_id',  '');
$ff_submit    = sk_option('cta_ff_submit_label', 'Send Message');
$ff_name_label  = sk_option('cta_ff_name_label', 'Your Name');
$ff_email_label = sk_option('cta_ff_email_label', 'Email Address');
$ff_msg_label   = sk_option('cta_ff_msg_label', 'Your Message');
$ff_note      = sk_option('cta_ff_note',         '');
$cta_bg_image        = sk_option('cta_bg_image', '');
$cta_bg_image_mobile = sk_option('cta_bg_image_mobile', '');

$cta_styles = [];
if ($cta_bg_image) {
    $cta_styles[] = 'background-image:url(\'' . esc_url($cta_bg_image) . '\')';
    $cta_styles[] = '--cta-bg:url(\'' . esc_url($cta_bg_image) . '\')';
}
if ($cta_bg_image_mobile) {
    $cta_styles[] = '--cta-bg-mobile:url(\'' . esc_url($cta_bg_image_mobile) . '\')';
}
$section_style = !empty($cta_styles) ? ' style="' . implode(';', $cta_styles) . ';"' : '';

?>

<section class="cta-section" id="contact" aria-labelledby="cta-heading-el"<?php echo $section_style; ?>>
  <div class="wrap">
    <div class="cta-layout">

      <!-- ── Left: Text Column ── -->
      <div class="cta-text-col reveal-cta">

        <?php if ( $eyebrow ) : ?>
          <div class="cta-eyebrow-row">
            <p class="cta-eyebrow-label"><?php echo esc_html($eyebrow); ?></p>
          </div>
        <?php endif; ?>

        <h2 class="cta-h2" id="cta-heading-el">
          <?php if ($heading_raw) :
            echo wp_kses_post($heading_raw);
          else : ?>
            Contact <em>us</em>
          <?php endif; ?>
        </h2>

        <!-- Decorative divider with 4-point star and brush stroke line -->
        <div class="cta-divider" aria-hidden="true">
          <svg class="cta-divider__brush" width="80" height="8" viewBox="0 0 80 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2 4.5 C 15 2.5, 30 2, 45 3.5 C 60 5, 70 4, 78 3.5 C 68 4.2, 55 4.8, 42 3.8 C 28 2.8, 15 5.5, 2 4.5 Z" fill="#B8623F" opacity="0.75"/>
          </svg>
          <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 0 L5.7 3.7 L9.5 5 L5.7 6.3 L5 10 L4.3 6.3 L0.5 5 L4.3 3.7Z" fill="#B8623F" opacity="0.75"/>
          </svg>
        </div>

      </div><!-- /cta-text-col -->

      <!-- ── Right: Form Column — no card wrapper, fields float over bg ── -->
      <div class="cta-form-col reveal-cta">

        <?php if ($form_id && shortcode_exists('forminator_form')) :
          echo do_shortcode('[forminator_form id="' . absint($form_id) . '"]');
        else : ?>

          <!-- Fallback form — mirrors Forminator markup for CSS parity -->
          <form class="sk-contact-fallback-form" method="POST" action="">

            <div class="forminator-row">
              <div class="forminator-col-12">
                <label for="sk-fname" class="forminator-label"><?php echo esc_html($ff_name_label); ?></label>
                <input type="text" id="sk-fname" name="fname" placeholder="<?php echo esc_attr($ff_name_label); ?>" />
              </div>
            </div>

            <div class="forminator-row">
              <div class="forminator-col-12">
                <label for="sk-email" class="forminator-label"><?php echo esc_html($ff_email_label); ?></label>
                <input type="email" id="sk-email" name="email" placeholder="your@email.com" />
              </div>
            </div>



            <div class="forminator-row forminator-row-last">
              <div class="forminator-col-12">
                <label for="sk-message" class="forminator-label"><?php echo esc_html($ff_msg_label); ?></label>
                <textarea id="sk-message" name="message" placeholder="Share a little about where you are and what you're seeking…"></textarea>
              </div>
            </div>

            <!-- Honeypot -->
            <input type="text" id="sk-hp" name="website" style="display:none;" tabindex="-1" autocomplete="off" />

            <div class="sk-form-submit">
              <button type="submit" class="forminator-button-submit">
                <?php echo esc_html($ff_submit); ?>
              </button>
              <?php if ( $ff_note ) : ?>
                <span class="sk-form-note"><?php echo esc_html($ff_note); ?></span>
              <?php endif; ?>
            </div>

            <div class="sk-form-feedback"></div>

          </form><!-- /sk-contact-fallback-form -->

        <?php endif; ?>

      </div><!-- /cta-form-col -->

    </div><!-- /cta-layout -->
  </div><!-- /wrap-wide -->
</section>


<!-- CTA helper script enqueued via setup.php -->
