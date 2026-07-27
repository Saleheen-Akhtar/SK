<?php
/**
 * Philosophy Strip — Faithful port of CircularTestimonials component.
 * 3 stacked images (left/centre/right) with 3D perspective + word-blur animated text.
 */
$pillars = sk_repeater('options_sk_philosophy_pillars_json');
if (empty($pillars)) {
    $pillars = sk_default_pillars();
}

$pillars_js = [];
foreach ($pillars as $i => $p) {
    $src = !empty($p['pillar_image']) ? esc_url_raw($p['pillar_image']) : '';
    $is_fallback = false;
    if ($src) {
        $attach_id = attachment_url_to_postid($src);
        if ($attach_id) {
            $src = wp_get_attachment_image_url($attach_id, 'sk-pillar') ?: $src;
        }
    } else {
        $src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E";
        $is_fallback = true;
    }
            $title = $p['pillar_title'] ?? '';
            while (str_contains($title, '&amp;')) {
                $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            $desc = $p['pillar_desc'] ?? '';
            while (str_contains($desc, '&amp;')) {
                $desc = html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            $pillars_js[] = [
                'num'   => $p['pillar_num']   ?? '0'.($i+1),
                'title' => $title,
                'desc'  => $desc,
                'src'   => $src,
                'is_fallback' => $is_fallback,
                'img_position' => $p['pillar_img_position'] ?? '',
            ];
}
$philosophy_bg = sk_option('philosophy_bg_image', '');
if (is_array($philosophy_bg) && isset($philosophy_bg['url'])) {
    $philosophy_bg = $philosophy_bg['url'];
} elseif (is_numeric($philosophy_bg)) {
    $philosophy_bg = wp_get_attachment_image_url($philosophy_bg, 'full');
}

$philosophy_bg_mobile = sk_option('philosophy_bg_image_mobile', '');
if (is_array($philosophy_bg_mobile) && isset($philosophy_bg_mobile['url'])) {
    $philosophy_bg_mobile = $philosophy_bg_mobile['url'];
} elseif (is_numeric($philosophy_bg_mobile)) {
    $philosophy_bg_mobile = wp_get_attachment_image_url($philosophy_bg_mobile, 'full');
}

$philosophy_video = sk_option('philosophy_bg_video', '');

?>
<?php if ($philosophy_bg || $philosophy_bg_mobile) : ?>
<style>
  #philosophy {
    <?php if ($philosophy_bg): ?>
    background-image: url('<?php echo esc_url($philosophy_bg); ?>');
    --philosophy-bg: url('<?php echo esc_url($philosophy_bg); ?>');
    <?php endif; ?>
    <?php if ($philosophy_bg_mobile): ?>
    --philosophy-bg-mobile: url('<?php echo esc_url($philosophy_bg_mobile); ?>');
    <?php endif; ?>
  }
</style>
<?php endif; ?>

<div class="strip strip--circular strip--no-overlay<?php echo $philosophy_bg ? ' has-bg-image' : ''; ?>" aria-label="<?php esc_attr_e('Core Pillars','sacred-kompass'); ?>" id="philosophy">
  <?php if (!empty($philosophy_video)): ?>
    <div class="philosophy-bg-video" aria-hidden="true">
      <video autoplay muted loop playsinline preload="auto">
        <source src="<?php echo esc_url($philosophy_video); ?>" type="video/mp4" />
      </video>
    </div>
  <?php endif; ?>
  <div class="sk-philosophy-overlay-wrap sk-philosophy-overlay-wrap--no-overlay">
    <div class="wrap">

      <!-- New Premium layout using Stories Preview card design and horizontal slider -->
      <div class="sk-astrology-bottom reveal">
        
        <div class="sk-astrology-left-col">
          <?php 
          $title_image = sk_option('philosophy_title_image', '');
          if ($title_image) :
          ?>
          <div class="sk-philosophy-title-wrap">
            <img src="<?php echo esc_url($title_image); ?>" alt="Vedic Astrology" class="sk-philosophy-title-img" />
          </div>
          <?php endif; ?>
        </div>

        <div class="sk-astrology-right-col">
          <!-- Arrow prev -->
          <button class="sk-sp-v4-arrow sk-astrology-arrow sk-astrology-prev" aria-label="Previous pillars">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M11 4L6 9L11 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>

          <!-- Track — overflow hidden, shows 2 cards -->
          <div class="sk-astrology-track-wrap">
            <div class="sk-astrology-track" id="astrology-track">
              <?php foreach ($pillars_js as $p) : ?>
              <div class="sk-sp-v4-card sk-astrology-card">
                <!-- Cover photo at the top -->
                <div class="sk-sp-v4-card-photo">
                  <?php if ( $p['src'] && !$p['is_fallback'] ) : 
                    $img_style = '';
                    if (!empty($p['img_position'])) {
                        $img_style = ' style="object-position: ' . esc_attr($p['img_position']) . ';"';
                    }
                  ?>
                  <img class="sk-sp-v4-card-photo-fg" src="<?php echo esc_url( $p['src'] ); ?>" alt="<?php echo esc_attr( $p['title'] ); ?>" loading="lazy" width="320" height="240"<?php echo $img_style; ?> />
                  <?php else : ?>
                  <div class="sk-sp-v4-photo-placeholder" aria-hidden="true">
                    <span><?php echo esc_html( mb_substr( $p['title'], 0, 1 ) ); ?></span>
                  </div>
                  <?php endif; ?>
                </div>
                
                <div class="sk-sp-v4-card-text">
                  <!-- Compass star icon -->
                  <div class="sk-sp-v4-qicon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M14 0 C11.5 7 11.5 10 14 14 C16.5 10 16.5 7 14 0Z" fill="currentColor"/>
                      <path d="M14 28 C11.5 21 11.5 18 14 14 C16.5 18 16.5 21 14 28Z" fill="currentColor"/>
                      <path d="M0 14 C7 11.5 10 11.5 14 14 C10 16.5 7 16.5 0 14Z" fill="currentColor"/>
                      <path d="M28 14 C21 11.5 18 11.5 14 14 C18 16.5 21 16.5 28 14Z" fill="currentColor"/>
                      <path d="M14 4 C12.0 9 12.0 11 14 14 C16.0 11 16.0 9 14 4Z" fill="currentColor" opacity="0.55" transform="rotate(45 14 14)"/>
                      <path d="M14 4 C12.0 9 12.0 11 14 14 C16.0 11 16.0 9 14 4Z" fill="currentColor" opacity="0.55" transform="rotate(135 14 14)"/>
                      <path d="M14 4 C12.0 9 12.0 11 14 14 C16.0 11 16.0 9 14 4Z" fill="currentColor" opacity="0.55" transform="rotate(225 14 14)"/>
                      <path d="M14 4 C12.0 9 12.0 11 14 14 C16.0 11 16.0 9 14 4Z" fill="currentColor" opacity="0.55" transform="rotate(315 14 14)"/>
                    </svg>
                  </div>
                  <!-- Description (no em wrapper for clean line-clamping) -->
                  <p class="sk-sp-v4-qtext"><?php echo esc_html($p['desc']); ?></p>
                  
                  <!-- Number + Title Info -->
                  <div class="sk-sp-v4-author">
                    <div class="sk-sp-v4-ainfo">
                      <span class="sk-sp-v4-aname"><?php echo esc_html($p['title']); ?></span>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Arrow next -->
          <button class="sk-sp-v4-arrow sk-astrology-arrow sk-astrology-next" aria-label="Next pillars">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M7 4L12 9L7 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>


    </div>

  </div> <!-- closes wrap -->
</div> <!-- closes sk-philosophy-overlay-wrap -->
<?php get_template_part('template-parts/home/kerala-pujas'); ?>
</div> <!-- closes #philosophy -->
<div id="philosophy-live-region" class="sr-only" aria-live="polite" aria-atomic="true"></div>
<!-- Philosophy strip script enqueued via setup.php -->
