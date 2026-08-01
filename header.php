<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
// Load nav items from CPT
$sk_nav_items   = function_exists('sk_get_nav_items') ? sk_get_nav_items() : [];
$sk_nav_cta_label = sk_option('nav_cta_label', '');
$sk_nav_cta_url   = sk_option('nav_cta_url',   sk_option('contact_url', home_url('/#contact')));

function sk_nav_link_url(string $raw): string {
  if (strpos($raw, 'http') === 0) return esc_url($raw);
  return esc_url(home_url(ltrim($raw, '/')));
}
?>

<!-- Hamburger trigger (mobile only) -->
<button class="sk-hamburger" id="sk-hamburger"
        aria-label="<?php esc_attr_e('Toggle menu','sacred-kompass'); ?>"
        aria-expanded="false"
        aria-controls="sk-header-nav">
  <span></span>
  <span></span>
</button>

<header class="sk-header" role="banner">
  <div class="sk-header-inner wrap">

    <!-- Logo -->
    <a class="sk-header-logo" href="<?php echo esc_url(home_url('/')); ?>"
       aria-label="<?php esc_attr_e('Home','sacred-kompass'); ?>">
      <?php $logo = sk_logo_html('sk-header-logo-img'); ?>
      <?php if ($logo) : echo $logo; ?>
        <span class="sr-only">Home</span>
      <?php else : ?>
        <span class="sk-header-logo-name">Sacred Kompass</span>
      <?php endif; ?>
    </a>

    <!-- Top Navigation -->
    <nav class="sk-header-nav" id="sk-header-nav"
         role="navigation"
         aria-label="<?php esc_attr_e('Main navigation','sacred-kompass'); ?>">
      <ul class="sk-header-links">
        <?php if (!empty($sk_nav_items)) : ?>
          <?php foreach ($sk_nav_items as $item):
            $href = sk_nav_link_url($item['url']);
            $is_active = false;
            $label_lower = strtolower($item['label']);
            if ($label_lower === 'journal' && (is_home() || is_singular('post') || is_category() || is_tag())) $is_active = true;
            elseif ($label_lower === 'the collective' && is_page('collective')) $is_active = true;
          ?>
          <li>
            <a href="<?php echo $href; ?>"
               target="<?php echo esc_attr($item['target']); ?>"
               class="<?php echo $is_active ? 'is-active' : ''; ?>"
               <?php echo ($item['target'] === '_blank') ? 'rel="noopener noreferrer"' : ''; ?>>
              <span><?php echo esc_html($item['label']); ?></span>
            </a>
          </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>

      <!-- CTA contact button -->
      <div class="sk-header-cta">
        <a href="<?php echo esc_url( $sk_nav_cta_url ); ?>" class="btn btn-outline">
          <?php echo esc_html( $sk_nav_cta_label ? $sk_nav_cta_label : 'Contact' ); ?>
        </a>
      </div>
    </nav>

  </div>
</header>

<!-- Site Wrapper (needed for smooth scrolling / GSAP structure) -->
<div id="sk-site-wrap" class="sk-site-wrap">
