<?php
/**
 * The Collective — Homepage Preview
 *
 * Left: large team card linking to /collective/
 * Right: editorial copy — clean, no awkward inline widths.
 * SEO: structured copy with eyebrow / heading / two punchy paras / name chips / CTA.
 */

$team_card = [
  'label'    => sk_option('founders_team_title',    __('The Collective','sacred-kompass')),
  'subtitle' => sk_option('founders_team_subtitle', __('Sacred Kompass Collective','sacred-kompass')),
  'image'    => sk_option('founders_team_image',''),
];

$section_eyebrow    = sk_option('founders_eyebrow',    'The Collective');
$section_heading    = sk_option('founders_heading',    'The Collective Behind');
$section_heading_em = sk_option('founders_heading_em', 'Sacred Kompass');
$section_sub        = sk_option('founders_sub',        'Two souls, one vision. Uniting Eastern wisdom and Western heart in service of conscious living.');
$founders_hover     = sk_option('founders_hover_hint', 'Meet the Collective');
$founders_cta_label = sk_option('founders_cta_label',  'Explore the Collective');
$founders_bg        = sk_option('founders_bg_image', '');
$founders_bg_mobile = sk_option('founders_bg_image_mobile', '');

$founders_styles = [];
if ($founders_bg) {
    $founders_styles[] = 'background-image:url(\'' . esc_url($founders_bg) . '\')';
    $founders_styles[] = '--founders-bg:url(\'' . esc_url($founders_bg) . '\')';
}
if ($founders_bg_mobile) {
    $founders_styles[] = '--founders-bg-mobile:url(\'' . esc_url($founders_bg_mobile) . '\')';
}
$founders_style = !empty($founders_styles) ? ' style="' . implode(';', $founders_styles) . ';"' : '';


$collective_url = home_url('/collective/');
$_collective_page_id = sk_get_page_id_by_path('collective') ?: sk_get_page_id_by_path('the-collective');
if ($_collective_page_id) {
  $collective_url = get_permalink($_collective_page_id);
}
?>

<section class="founders-section" id="founders" aria-labelledby="collective-heading"<?php echo $founders_style; ?>>
  <div class="wrap">
    <div class="founders-split-grid">

      <!-- LEFT: editorial copy -->
      <div class="founders-editorial-col">

        <div class="founders-mobile-title-wrap">
          <div class="eyebrow reveal"><?php echo esc_html($section_eyebrow); ?></div>

          <h2 class="display-h2 reveal d1" id="collective-heading">
            <?php echo esc_html($section_heading); ?><br>
            <em><?php echo esc_html($section_heading_em); ?></em>
          </h2>
        </div>

        <div class="founders-mobile-info-wrap">
          <p class="founders-editorial-lead reveal d2">
            <?php echo esc_html($section_sub); ?>
          </p>

          <p class="founders-editorial-body reveal d3">
            <?php
            $founders_body = sk_option('founders_body', 'From Vedic philosophy and sacred feminine wisdom to conscious leadership and non-violent communication — every guide brings a living practice, not just a credential.');
            echo esc_html($founders_body);
            ?>
          </p>

          <a href="<?php echo esc_url($collective_url); ?>" class="btn btn-primary btn-sm reveal d4">
            <?php echo esc_html($founders_cta_label); ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:.3rem"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>

      </div><!-- /founders-editorial-col -->

      <!-- RIGHT: collective image clipped to arch shape -->
      <div class="founders-arch-col reveal d1">
        <a class="founders-arch-link" href="<?php echo esc_url($collective_url); ?>" aria-label="<?php echo esc_attr($founders_hover); ?>">
          <div class="founders-arch-frame">
            <?php if (!empty($team_card['image'])): ?>
              <img class="founders-arch-img"
                   src="<?php echo esc_url($team_card['image']); ?>"
                   alt="<?php esc_attr_e('Sacred Kompass Collective','sacred-kompass'); ?>"
                   loading="lazy" />
            <?php else: ?>
              <div class="founders-arch-placeholder" aria-hidden="true">
                <span class="founder-placeholder-initial">SK</span>
              </div>
            <?php endif; ?>
          </div>
        </a>
      </div><!-- /founders-arch-col -->

    </div><!-- /founders-split-grid -->
  </div><!-- /wrap -->
</section>
