<?php
/**
 * Template Name: Terms of Use
 * Content is now editable via WP Admin → ★ Sacred Kompass → Legal Pages
 * (post slug: terms)
 *
 * Falls back to empty state if no sk_legal post exists (run "Install Demo Content" in admin settings to seed default legal copy).
 */
get_header();

$legal = sk_get_legal_page('terms');

$eyebrow        = $legal['eyebrow']        ?? 'Sacred Kompass Collective';
$title          = $legal['title']          ?: 'Terms of Use';
$effective_date = $legal['effective_date'] ?? '24 March 2026';
$location       = $legal['location']       ?? 'Singapore';
$content        = $legal['content']        ?? '';

if (trim(wp_strip_all_tags($content)) === '') {
    $content = '<p>Welcome to Sacred Kompass Collective. By accessing or using this website, you agree to be bound by the following Terms of Use. Please read them carefully before proceeding. If you do not agree to these terms, please discontinue use of the site.</p>

  <h2>1. Acceptance of Terms</h2>
  <p>These Terms of Use govern your access to and use of the Sacred Kompass Collective website and any services, content, or information made available through it. They apply to all visitors, users, and others who access the site.</p>

  <h2>2. Nature of Services</h2>
  <p>Sacred Kompass Collective offers wellness consultancy, coaching, Vedic astrology (Jyotish), Nonviolent Communication (NVC) facilitation, meditation guidance, and women&apos;s empowerment programmes. Our offerings are rooted in ancient wisdom traditions and are intended to support personal growth, self-awareness, and inner clarity.</p>
  <p>Our services are not a substitute for professional medical, psychological, legal, or financial advice. If you are experiencing a medical or mental health emergency, please contact an appropriate healthcare professional immediately.</p>

  <h2>3. Intellectual Property</h2>
  <p>All content on this website, including text, images, graphics, logos, and design, is the property of Sacred Kompass Collective and is protected by applicable intellectual property laws. You may not reproduce, distribute, or create derivative works from any content on this site without our prior written permission.</p>

  <h2>4. Stories, Case Studies &amp; Anonymity</h2>
  <p>Content published in the Stories section may be used by Sacred Kompass Collective as testimonials, case studies, editorial stories, or portfolio material, including on this website and related marketing channels, unless we agree otherwise in writing.</p>
  <p>By submitting, sharing, or approving a story, you grant Sacred Kompass Collective a non-exclusive, worldwide, royalty-free licence to edit, publish, reproduce, adapt, and display the material in connection with our work. We may remove identifying details, change names, blur or replace images, and otherwise present the story anonymously to protect privacy and dignity.</p>
  <p>Unless we explicitly state otherwise in writing, we may reference the experience, outcomes, and transformation described in a story without using the original identity of the person involved.</p>

  <h2>5. Use of Website</h2>
  <p>By using this website, you agree to:</p>
  <ul>
    <li>Use the site only for lawful purposes</li>
    <li>Not attempt to gain unauthorised access to any part of the website or its systems</li>
    <li>Not transmit any harmful, offensive, or disruptive content through our contact form or communications</li>
    <li>Not use the site in any way that could damage or impair its availability or accessibility</li>
  </ul>

  <h2>6. Booking &amp; Payments</h2>
  <p>All session bookings are subject to our Booking &amp; Cancellation Policy, communicated at the time of engagement. Payment terms will be confirmed in writing prior to any paid engagement. Sacred Kompass Collective reserves the right to modify service offerings, pricing, and availability at any time.</p>

  <h2>7. Disclaimer of Warranties</h2>
  <p>This website and its content are provided on an &quot;as is&quot; and &quot;as available&quot; basis without warranties of any kind, either express or implied. We do not guarantee that the site will be error-free, uninterrupted, or free of viruses or other harmful components.</p>

  <h2>8. Limitation of Liability</h2>
  <p>To the fullest extent permitted by Singapore law, Sacred Kompass Collective shall not be liable for any direct, indirect, incidental, consequential, or punitive damages arising out of your access to or use of this website or our services.</p>

  <h2>9. Governing Law</h2>
  <p>These Terms of Use shall be governed by and construed in accordance with the laws of the Republic of Singapore. Any disputes arising from or in connection with these terms shall be subject to the exclusive jurisdiction of the courts of Singapore.</p>

  <h2>10. Changes to These Terms</h2>
  <p>We reserve the right to update or modify these Terms of Use at any time. Changes will be posted on this page with a revised effective date. Your continued use of the site after any modifications constitutes your acceptance of the updated terms.</p>

  <h2>11. Contact</h2>
  <p><strong>Sacred Kompass Collective</strong><br>Email: <a href="mailto:collective@sacredkompass.org">collective@sacredkompass.org</a><br>Phone: <a href="tel:+6584343915">+65 84343915</a><br>Singapore</p>';
}
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
