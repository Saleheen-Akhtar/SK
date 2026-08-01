<?php
/**
 * FAQ Section (Podium Style)
 */
$faq_heading_1  = sk_option('faq_heading_1',   'Frequently Asked');
$faq_heading_em = sk_option('faq_heading_em',  'Questions');
$faq_sub        = sk_option('faq_sub',         'Find clarity on the journey ahead.');
$faq_cta_label  = sk_option('faq_cta_label',   'Contact Us');

$faq_query = new WP_Query([
    'post_type'              => 'sk_faq',
    'post_status'            => 'publish',
    'posts_per_page'         => 20,
    'orderby'                => 'menu_order',
    'order'                  => 'ASC',
    'no_found_rows'          => true,
]);
?>

<section id="faq" class="sk-podium-faq section-py" style="background-color:var(--color-surface-base); border-top:1px solid var(--color-surface-strong);">
  <div class="wrap stagger-children">
    <span class="eyebrow">FAQ</span>
    <h2 class="display-h3"><?php echo esc_html($faq_heading_1); ?> <?php echo esc_html($faq_heading_em); ?></h2>
    <p class="body-serif" style="margin-bottom:var(--space-2); max-width:600px;"><?php echo esc_html($faq_sub); ?></p>

    <div class="faq-accordion" style="display:flex; flex-direction:column; gap:1rem;">
      <?php if ($faq_query->have_posts()): while ($faq_query->have_posts()): $faq_query->the_post(); ?>
        <div class="faq-item" style="border-bottom:1px solid var(--color-surface-strong); padding-bottom:1rem;">
          <button class="faq-trigger body-ui" aria-expanded="false" aria-controls="faq-<?php the_ID(); ?>" style="width:100%; text-align:left; display:flex; justify-content:space-between; align-items:center; padding:1rem 0;">
            <span style="font-weight:var(--font-weight-base);"><?php the_title(); ?></span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div id="faq-<?php the_ID(); ?>" class="faq-body body-serif" style="display:none; padding-bottom:1rem;">
            <?php the_content(); ?>
          </div>
        </div>
      <?php endwhile; wp_reset_postdata(); endif; ?>
    </div>
  </div>
</section>

<script>
document.querySelectorAll('.sk-podium-faq .faq-trigger').forEach(trigger => {
  trigger.addEventListener('click', () => {
    const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
    trigger.setAttribute('aria-expanded', !isExpanded);
    const body = document.getElementById(trigger.getAttribute('aria-controls'));
    if (body) body.style.display = isExpanded ? 'none' : 'block';
  });
});
</script>
