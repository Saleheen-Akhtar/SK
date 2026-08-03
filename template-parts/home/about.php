<?php
/**
 * About Section — v16
 *
 * Layout matches the wireframe sketch:
 *  LEFT  — brand name (SACRED KOMPASS) + tagline "Exploring Your Inner Journey"
 *  RIGHT — three stacked content rows:
 *            ④ Organisation descriptor
 *            ⑤ Bridge copy (main body)
 *            ⑥ Welcome line
 *
 * All text is admin-controlled via Settings › About.
 * No fallback strings — empty field = element not rendered.
 */

$bg_img       = sk_option('hero_bg_image', '');
$bg_video     = sk_option('hero_bg_video', '');
$brand_bg_img = sk_option('about_brand_bg_image', '');
$eyebrow      = sk_option('about_eyebrow', '');
$tagline      = sk_option('about_tagline', '');
$org_desc     = sk_option('about_org_descriptor', '');
$bridge       = sk_option('about_bridge_copy', '');
$welcome      = sk_option('about_welcome_strip', '');
$heading      = sk_option('about_heading', '');
$body         = sk_option('about_body', '');
$about_bg            = sk_option('about_bg_image', '');
$about_bg_mobile     = sk_option('about_bg_image_mobile', '');
$founders_bg         = sk_option('founders_bg_image', '');

if (!function_exists('sk_format_about_text')) {
    function sk_format_about_text($text, $base_class) {
        if (preg_match('/^\s*[\*\-]\s+/m', $text)) {
            $lines = preg_split('/\r\n|\r|\n/', $text);
            $list_items = '';
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (empty($trimmed)) continue;
                if (preg_match('/^[\*\-]\s+(.*)$/', $trimmed, $matches)) {
                    $list_items .= '<li>' . esc_html($matches[1]) . '</li>';
                } else {
                    $list_items .= '<li>' . esc_html($trimmed) . '</li>';
                }
            }
            return '<ul class="' . esc_attr($base_class) . ' av16-bullet-list">' . $list_items . '</ul>';
        }
        return '<p class="' . esc_attr($base_class) . '">' . nl2br(esc_html($text)) . '</p>';
    }
}
?>

<section id="about" class="sk-podium-about section-py" style="background-color:var(--color-surface-base); border-top:1px solid var(--color-surface-strong);">
  <div class="wrap stagger-children">
    <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
    <h2 class="display-h2">
      <?php echo wp_kses_post($welcome); ?>
    </h2>
    <div class="about-grid">
      <div class="about-text">
        <p class="body-serif">
          <?php echo wpautop(wp_kses_post($body)); ?>
        </p>
        <p class="body-serif" style="margin-top:var(--space-1);">
          <?php echo esc_html($org_desc); ?>
        </p>
      </div>
    </div>
  </div>
</section>
