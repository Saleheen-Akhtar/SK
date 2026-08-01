<?php
/**
 * Kerala Tantric Pujas Section (Podium Style)
 */
?>
<section id="kerala-pujas" class="sk-podium-pujas section-py" style="background-color:var(--color-surface-base); border-top:1px solid var(--color-surface-strong);">
  <div class="wrap stagger-children">
    <div style="display:grid; grid-template-columns:1fr; gap:var(--space-2);">
      
      <div class="sk-pujas-left-col">
        <?php 
        $pujas_eyebrow = sk_option('pujas_eyebrow', 'Next Step... The Solution'); 
        if ($pujas_eyebrow === 'TEMPLE RITUALS & DEVI WORSHIP') {
            $pujas_eyebrow = 'Next Step... The Solution';
        }
        ?>
        <div class="sk-pujas-header-v2" style="margin-bottom:var(--space-2);">
          <span class="eyebrow"><?php echo esc_html($pujas_eyebrow); ?></span>
          <h2 class="display-h3" style="margin-bottom:var(--space-1);">
             <?php echo esc_html(sk_option('pujas_heading', 'Kerala Tantric Pujas')); ?>
          </h2>
          <p class="body-serif" style="max-width:600px;">
             <?php echo esc_html(sk_option('pujas_subheading', 'Authentic remedies performed by traditional practitioners in Kerala to pacify astrological afflictions, clear subtle blockages, and harmonize energy fields.')); ?>
          </p>
        </div>

        <div class="sk-pujas-actions-v2">
          <?php
          $pujas_cta_text = sk_option('pujas_cta_text', 'Learn More About Remedies');
          $pujas_cta_link = sk_option('pujas_cta_link', home_url('/contact/'));
          if ($pujas_cta_text && $pujas_cta_link):
          ?>
            <a href="<?php echo esc_url($pujas_cta_link); ?>" class="btn-outline">
              <?php echo esc_html($pujas_cta_text); ?>
            </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- RIGHT SIDE: Grid of cards -->
      <div class="sk-pujas-right-col" style="display:grid; grid-template-columns:1fr; gap:var(--space-1);">
        <?php
        $pujas_cards = sk_repeater('pujas_cards_repeater');

        if (empty($pujas_cards)) {
            $pujas_cards = [
                [
                    'title' => 'Graha Shanti',
                    'desc'  => 'Specific rituals dedicated to pacifying afflicted planets in your natal chart.',
                ],
                [
                    'title' => 'Dosha Nivarana',
                    'desc'  => 'Remedies for karmic doshas such as Kalasarpa, Mangal, and Pitru dosha.',
                ],
                [
                    'title' => 'Ayush Homa',
                    'desc'  => 'Vedic fire ceremony performed for longevity, vitality, and health recovery.',
                ],
                [
                    'title' => 'Sudarshana Homa',
                    'desc'  => 'Powerful protective ritual to clear negative energy and psychic interference.',
                ]
            ];
        }

        foreach ($pujas_cards as $idx => $card):
            $title = $card['title'] ?? '';
            $desc  = $card['desc'] ?? '';
        ?>
        <div class="sk-puja-card-v2" style="border:1px solid var(--color-surface-strong); padding:var(--space-1);">
          <h3 class="body-ui" style="margin-bottom:0.5rem;"><?php echo esc_html($title); ?></h3>
          <p class="body-serif" style="font-size:var(--font-size-sm); color:var(--color-text-tertiary);"><?php echo esc_html($desc); ?></p>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</section>

<style>
@media (min-width: 768px) {
  .sk-podium-pujas > .wrap > div { grid-template-columns: 1fr 1fr; }
  .sk-pujas-right-col { grid-template-columns: repeat(2, 1fr) !important; }
}
</style>
