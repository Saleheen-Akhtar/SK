<?php
/**
 * The Collective — Homepage Preview (Podium Style)
 */
$section_eyebrow    = sk_option('founders_eyebrow',    'The Collective');
$section_heading    = sk_option('founders_heading',    'The Collective Behind');
$section_heading_em = sk_option('founders_heading_em', 'Sacred Kompass');
$section_sub        = sk_option('founders_sub',        'Two souls, one vision. Uniting Eastern wisdom and Western heart in service of conscious living.');
$founders_cta_label = sk_option('founders_cta_label',  'Explore the Collective');

$collective_url = home_url('/collective/');
$_collective_page_id = sk_get_page_id_by_path('collective') ?: sk_get_page_id_by_path('the-collective');
if ($_collective_page_id) {
  $collective_url = get_permalink($_collective_page_id);
}
?>

<section id="founders" class="sk-podium-founders section-py" style="background-color:var(--color-surface-base); border-top:1px solid var(--color-surface-strong);">
  <div class="wrap stagger-children">
    <span class="eyebrow"><?php echo esc_html($section_eyebrow); ?></span>
    <h2 class="display-h3"><?php echo esc_html($section_heading); ?> <?php echo esc_html($section_heading_em); ?></h2>
    <p class="body-serif" style="margin-bottom:var(--space-2); max-width:600px;"><?php echo esc_html($section_sub); ?></p>

    <div style="margin-top:var(--space-2);">
      <a href="<?php echo esc_url($collective_url); ?>" class="btn-outline">
        <?php echo esc_html($founders_cta_label); ?>
      </a>
    </div>
  </div>
</section>
