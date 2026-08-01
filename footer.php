<?php
$email           = sk_option('footer_email',        'collective@sacredkompass.org');
$phone           = sk_option('footer_phone',        '+65 84343915');
$phone_c         = preg_replace('/[^+0-9]/', '', $phone);
$footer_location = sk_option('footer_location_bar', 'Bedok North, Singapore &nbsp;&middot;&nbsp; Online Worldwide');
while (str_contains($footer_location, '&amp;')) {
    $footer_location = html_entity_decode($footer_location, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// ── Dynamic nav items
$footer_nav_items = function_exists('sk_get_nav_items') ? sk_get_nav_items() : [];
?>
<footer class="footer sk-podium-footer" role="contentinfo" id="sk-footer">
  <div class="wrap">
    <div class="sk-footer-grid">

      <!-- Connect -->
      <div class="sk-footer-col">
        <h3 class="body-ui">Connect</h3>
        <ul class="sk-footer-links">
          <li><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></li>
          <li><a href="tel:<?php echo esc_attr($phone_c); ?>"><?php echo esc_html($phone); ?></a></li>
          <?php
          $ig = sk_option('social_instagram', '');
          if ($ig) :
            if (!preg_match('#^https?://#i', $ig)) {
              $ig = 'https://www.instagram.com/' . ltrim($ig, '/@');
            }
          ?>
            <li><a href="<?php echo esc_url($ig); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Instagram', 'sacred-kompass'); ?></a></li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Navigate -->
      <div class="sk-footer-col">
        <h3 class="body-ui">Navigate</h3>
        <ul class="sk-footer-links">
          <?php if (!empty($footer_nav_items)) : ?>
            <?php foreach ($footer_nav_items as $item):
              if (empty($item['desktop'])) continue;
              $href   = strpos($item['url'], 'http') === 0 ? esc_url($item['url']) : esc_url(home_url($item['url']));
              $target = ($item['target'] ?? '_self') === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '';
            ?>
            <li><a href="<?php echo $href; ?>"<?php echo $target; ?>><?php echo esc_html($item['label']); ?></a></li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Legal -->
      <div class="sk-footer-col">
        <h3 class="body-ui">Legal</h3>
        <ul class="sk-footer-links">
          <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'sacred-kompass'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/terms-of-use/'));   ?>"><?php esc_html_e('Terms of Use',   'sacred-kompass'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/disclaimer/'));      ?>"><?php esc_html_e('Disclaimer',     'sacred-kompass'); ?></a></li>
        </ul>
      </div>

    </div>

    <div class="sk-footer-bottom">
      <span class="body-small">&copy; <?php echo date('Y'); ?> Sacred Kompass. All rights reserved.</span>
      <span class="body-small sk-footer-location"><?php echo wp_kses($footer_location, ['strong'=>[],'em'=>[],'span'=>[]]); ?></span>
    </div>
  </div>
</footer>
</div> <!-- /#sk-site-wrap (opened in header.php) -->
<?php wp_footer(); ?>
</body>
</html>
