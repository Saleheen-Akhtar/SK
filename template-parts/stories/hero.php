<?php
/**
 * Shared Template Partial: Stories Hero Masthead
 */
defined('ABSPATH') || exit;

$hero_title = sk_option('stories_hero_title', 'Stories for the<br><em>soul</em>');
if ( ! str_contains( $hero_title, '<em>' ) ) {
    $hero_title = str_ireplace( 'soul', '<em>soul</em>', $hero_title );
}
$hero_sub   = sk_option('stories_hero_sub', 'Real stories from beautiful souls who chose themselves, followed their inner compass, and created meaningful change.');
$badge_raw  = sk_option('stories_badge_labels', 'Real Journeys, Heartfelt Transformations, Lasting Impact');
$badges     = array_filter(array_map('trim', explode(',', $badge_raw)));

$hero_img = get_option('options_sk_stories_page_hero_image', '') ?: sk_option('stories_page_hero_image', '');
?>
<header class="sk-spg-hero">
  <div class="wrap sk-spg-hero__wrap">
    <div class="sk-spg-hero__left">


      <h1 class="sk-spg-hero__title"><?php echo wp_kses_post($hero_title); ?></h1>
      <p class="sk-spg-hero__sub"><?php echo esc_html($hero_sub); ?></p>

      <?php if (!empty($badges)) : ?>
      <div class="sk-spg-hero__badges">
        <?php
        $badge_icons = [
            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>'
        ];
        foreach ($badges as $i => $badge) :
            $icon = $badge_icons[$i % count($badge_icons)];
        ?>
        <span class="sk-spg-badge">
          <?php echo $icon; ?>
          <?php echo esc_html($badge); ?>
        </span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div>
    <div class="sk-spg-hero__right">
      <?php if ( $hero_img ) : ?>
      <img src="<?php echo esc_url( $hero_img ); ?>" alt="<?php esc_attr_e( 'Stories of the Soul', 'sacred-kompass' ); ?>" class="sk-spg-hero__img" loading="eager" />
      <?php else : ?>
      <div class="sk-spg-hero__img-placeholder" aria-hidden="true"></div>
      <?php endif; ?>
    </div>
  </div>
</header>
