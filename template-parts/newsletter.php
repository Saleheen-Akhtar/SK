<?php
/**
 * Sacred Kompass — Shared Newsletter Section
 * Unique Framer Motion Inspired Spotlight Layout (2-Column Asymmetric)
 */
defined('ABSPATH') || exit;
?>
<section class="sk-newsletter-section reveal">
  <div class="wrap-wide">
    <div class="sk-newsletter-card">
      
      <!-- Ethereal background aurora blobs -->
      <div class="sk-newsletter-aurora" aria-hidden="true">
        <div class="sk-aurora-blob sk-aurora-blob--1"></div>
        <div class="sk-aurora-blob sk-aurora-blob--2"></div>
      </div>
      
      <!-- Border Beam effect (Framer Motion style) -->
      <div class="sk-newsletter-border-beam" aria-hidden="true"></div>
      
      <div class="sk-newsletter-grid">
        
        <!-- Left side: bold modern typography and message -->
        <div class="sk-newsletter-col--info">
          <div class="sk-newsletter-badge-wrap">
            <span class="sk-newsletter-badge">
              <span class="sk-newsletter-badge-dot"></span>
              <?php esc_html_e('Stay Connected', 'sacred-kompass'); ?>
            </span>
          </div>
          <h2 class="sk-newsletter-heading"><?php printf( __('Join the %sInner Circle%s', 'sacred-kompass'), '<em>', '</em>' ); ?></h2>
          <p class="sk-newsletter-body">
            <?php esc_html_e('Gentle reminders of stillness, ancient wisdom, and our latest journal entries — delivered quietly to your inbox.', 'sacred-kompass'); ?>
          </p>
        </div>
        
        <!-- Right side: the elegant glassmorphic form -->
        <div class="sk-newsletter-col--form">
          <div class="sk-newsletter-form-container">
            <div class="sk-newsletter-form-wrap">
              <?php echo do_shortcode('[forminator_form id="929"]'); ?>
            </div>
            <p class="sk-newsletter-disclaimer"><?php echo esc_html(sk_option('newsletter_disclaimer', __('Sacred Kompass respects your privacy. Unsubscribe anytime.', 'sacred-kompass'))); ?></p>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</section>
