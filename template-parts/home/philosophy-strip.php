<?php
/**
 * Philosophy Strip (Podium Style)
 */
$pillars = sk_repeater('options_sk_philosophy_pillars_json');
if (empty($pillars)) {
    $pillars = sk_default_pillars();
}

$pillars_js = [];
foreach ($pillars as $i => $p) {
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
    ];
}
?>

<section id="philosophy" class="sk-podium-philosophy section-py" style="background-color:var(--color-surface-base); border-top:1px solid var(--color-surface-strong);">
  <div class="wrap stagger-children">
    <span class="eyebrow">Philosophy</span>
    <h2 class="display-h3" style="margin-bottom:var(--space-2);">Core Principles</h2>

    <div style="display:flex; flex-direction:column; gap:var(--space-2);">
       <?php foreach ($pillars_js as $pillar): ?>
         <div style="padding-bottom:var(--space-1); border-bottom:1px solid var(--color-surface-strong);">
            <div class="eyebrow" style="color:var(--color-text-tertiary); margin-bottom:0.5rem;"><?php echo esc_html($pillar['num']); ?></div>
            <h3 class="display-h3" style="margin-bottom:0.5rem;"><?php echo esc_html($pillar['title']); ?></h3>
            <p class="body-serif" style="max-width:800px;"><?php echo esc_html($pillar['desc']); ?></p>
         </div>
       <?php endforeach; ?>
    </div>
  </div>
</section>
