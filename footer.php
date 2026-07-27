<?php
$tagline         = sk_option('footer_tagline',      'Ancient wisdom for the modern soul.');
while (str_contains($tagline, '&amp;')) {
    $tagline = html_entity_decode($tagline, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
$copyright       = sk_option('footer_copyright',    'Sacred Kompass Collective &middot; Singapore');
while (str_contains($copyright, '&amp;')) {
    $copyright = html_entity_decode($copyright, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
$email           = sk_option('footer_email',        'collective@sacredkompass.org');
$phone           = sk_option('footer_phone',        '+65 84343915');
$phone_c         = preg_replace('/[^+0-9]/', '', $phone);
$footer_location = sk_option('footer_location_bar', 'Bedok North, Singapore &nbsp;&middot;&nbsp; Online Worldwide');
while (str_contains($footer_location, '&amp;')) {
    $footer_location = html_entity_decode($footer_location, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

$footer_col_navigate  = sk_option('footer_col_navigate',  'Navigate');
while (str_contains($footer_col_navigate, '&amp;')) {
    $footer_col_navigate = html_entity_decode($footer_col_navigate, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
$footer_col_art = sk_option('footer_col_art', 'Art for Peace');
while (str_contains($footer_col_art, '&amp;')) {
    $footer_col_art = html_entity_decode($footer_col_art, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
$footer_col_connect   = sk_option('footer_col_connect',   'Connect');
while (str_contains($footer_col_connect, '&amp;')) {
    $footer_col_connect = html_entity_decode($footer_col_connect, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
$footer_col_legal     = sk_option('footer_col_legal',     'Legal');
while (str_contains($footer_col_legal, '&amp;')) {
    $footer_col_legal = html_entity_decode($footer_col_legal, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// ── Dynamic nav items — same source as header, already filtered by section state
$footer_nav_items = function_exists('sk_get_nav_items') ? sk_get_nav_items() : [];

// ── Dynamic art pieces (titles + links from sk_art CPT)
$footer_art = get_posts([
    'post_type'      => 'sk_art',
    'post_status'    => 'publish',
    'posts_per_page' => 8,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'no_found_rows'  => true,
]);
?>
<footer class="footer sk-footer-v2" role="contentinfo">

  <!-- Top: large brand wordmark -->
  <div class="sk-foot-top wrap">
    <div class="sk-foot-brand-row">
      <div class="sk-foot-wordmark-wrap">
        <?php
          $foot_logo = sk_logo_html('sk-foot-logo-img');
          if ($foot_logo) echo $foot_logo;
        ?>
        <span class="sk-foot-wordmark">Sacred <em>Kompass</em></span>
      </div>
    </div>
    <p class="sk-foot-tagline"><?php echo esc_html($tagline); ?></p>
  </div>

  <div class="sk-foot-divider"></div>

  <!-- Mid: nav columns -->
  <div class="sk-foot-mid wrap">

    <div class="sk-foot-col">
      <p class="sk-foot-col-label"><?php echo esc_html($footer_col_navigate); ?></p>
      <ul>
        <?php if (empty($footer_nav_items)) : ?>
          <?php if (current_user_can('manage_options')) : ?>
            <li><a href="<?php echo esc_url(admin_url('edit.php?post_type=sk_nav')); ?>" style="color: var(--gold); font-size: 11px;"><?php esc_html_e('Navigation not configured', 'sacred-kompass'); ?></a></li>
          <?php endif; ?>
        <?php else : ?>
          <?php foreach ($footer_nav_items as $item):
            if (empty($item['desktop'])) continue; // respect desktop visibility flag
            $href   = strpos($item['url'], 'http') === 0 ? esc_url($item['url']) : esc_url(home_url($item['url']));
            $target = ($item['target'] ?? '_self') === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '';
          ?>
          <li><a href="<?php echo $href; ?>"<?php echo $target; ?>><?php echo esc_html($item['label']); ?></a></li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>

    <div class="sk-foot-col">
      <p class="sk-foot-col-label"><?php echo esc_html($footer_col_art); ?></p>
      <ul>
        <?php if (!empty($footer_art)):
          foreach ($footer_art as $art):
            $cta = get_post_meta($art->ID, 'art_cta_url', true) ?: get_permalink($art->ID);
        ?>
        <li><a href="<?php echo esc_url($cta); ?>"><?php echo esc_html(get_the_title($art)); ?></a></li>
        <?php endforeach;
        else:
          $default_art = function_exists('sk_get_default_art') ? sk_get_default_art() : [];
          foreach ($default_art as $o):
        ?>
        <li><a href="<?php echo esc_url(home_url('/#art')); ?>"><?php echo esc_html($o['title']); ?></a></li>
        <?php endforeach;
        endif; ?>
      </ul>
    </div>

    <div class="sk-foot-col">
      <p class="sk-foot-col-label"><?php echo esc_html($footer_col_connect); ?></p>
      <ul>
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
        <?php
        $fb = sk_option('social_facebook', '');
        if ($fb) :
          if (!preg_match('#^https?://#i', $fb)) {
            $fb = 'https://www.facebook.com/' . ltrim($fb, '/@');
          }
        ?>
          <li><a href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Facebook', 'sacred-kompass'); ?></a></li>
        <?php endif; ?>
        <?php
        $wa = sk_option('social_whatsapp', '');
        if ($wa) :
          if (!preg_match('#^https?://#i', $wa)) {
            $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $wa);
          }
        ?>
          <li><a href="<?php echo esc_url($wa); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('WhatsApp', 'sacred-kompass'); ?></a></li>
        <?php endif; ?>
      </ul>
    </div>

    <div class="sk-foot-col">
      <p class="sk-foot-col-label"><?php echo esc_html($footer_col_legal); ?></p>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'sacred-kompass'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/terms-of-use/'));   ?>"><?php esc_html_e('Terms of Use',   'sacred-kompass'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/disclaimer/'));      ?>"><?php esc_html_e('Disclaimer',     'sacred-kompass'); ?></a></li>
      </ul>
    </div>

  </div>

  <div class="sk-foot-divider"></div>

  <!-- Bottom bar -->
  <div class="sk-foot-bottom wrap">
    <span class="sk-foot-copy">
      &copy; <?php echo date('Y'); ?> <?php echo wp_kses($copyright, ['em'=>[],'strong'=>[],'br'=>[],'span'=>[]]); ?>
    </span>
    <span class="sk-foot-location"><?php echo wp_kses($footer_location, ['strong'=>[],'em'=>[],'span'=>[]]); ?></span>
  </div>

</footer>
<?php wp_footer(); ?>
</body>
</html>
