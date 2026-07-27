<?php
/**
 * Kerala Tantric Pujas Section
 *
 * Sri Vidya tradition temple rituals and pujas.
 * Separated from Philosophy Strip to enable individual on/off controls.
 */

$philosophy_bg = sk_option('philosophy_bg_image', '');
if (is_array($philosophy_bg) && isset($philosophy_bg['url'])) {
    $philosophy_bg = $philosophy_bg['url'];
} elseif (is_numeric($philosophy_bg)) {
    $philosophy_bg = wp_get_attachment_image_url($philosophy_bg, 'full');
}
?>
<div class="sk-pujas-section" id="kerala-pujas">
  <div class="wrap"> <!-- inner wrap to keep content centered -->
    <div class="sk-pujas-grid-v2">
      
      <!-- LEFT SIDE: Header and CTA actions -->
      <div class="sk-pujas-left-col">
        <?php 
        $pujas_eyebrow = sk_option('pujas_eyebrow', 'Next Step... The Solution'); 
        if ($pujas_eyebrow === 'TEMPLE RITUALS & DEVI WORSHIP') {
            $pujas_eyebrow = 'Next Step... The Solution';
        }
        
        $pujas_gold_image = sk_option('pujas_gold_image', 'http://sacredkompass.local/wp-content/uploads/2026/06/gold-texture.jpeg');
        if (is_array($pujas_gold_image) && isset($pujas_gold_image['url'])) {
            $pujas_gold_image = $pujas_gold_image['url'];
        } elseif (is_numeric($pujas_gold_image)) {
            $pujas_gold_image = wp_get_attachment_image_url($pujas_gold_image, 'full');
        }
        
        $pujas_scroll_image = sk_option('pujas_scroll_image', '');
        if (is_array($pujas_scroll_image) && isset($pujas_scroll_image['url'])) {
            $pujas_scroll_image = $pujas_scroll_image['url'];
        } elseif (is_numeric($pujas_scroll_image)) {
            $pujas_scroll_image = wp_get_attachment_image_url($pujas_scroll_image, 'full');
        }
        
        $local_gold_fallback = get_template_directory_uri() . '/assets/images/gold-texture.jpg';
        
        // Multiple background fallback hierarchy: Dynamic URL -> Local Theme Image -> Solid Terra color gradient
        $pujas_gold_style = ' style="background-image: ' . 
            ($pujas_gold_image ? 'url(\'' . esc_url($pujas_gold_image) . '\'), ' : '') . 
            'url(\'' . esc_url($local_gold_fallback) . '\'), ' . 
            'linear-gradient(var(--terra), var(--terra));"';

        // Gradient style that is transparent on the left (showing raw texture) and fades into a rich gold highlight wash on the right (lightening the last letters)
        $gradient_gold_style = ' style="background-image: linear-gradient(to right, rgba(245, 205, 95, 0) 30%, rgba(245, 205, 95, 0.65) 100%), ' . 
            ($pujas_gold_image ? 'url(\'' . esc_url($pujas_gold_image) . '\'), ' : '') . 
            'url(\'' . esc_url($local_gold_fallback) . '\'), ' . 
            'linear-gradient(var(--terra), var(--terra));"';

        // Eyebrow-specific background style with a 28% brand-ink overlay to darken and increase readability while keeping the gold texture visible
        $eyebrow_gold_style = ' style="background-image: linear-gradient(rgba(26, 22, 18, 0.28), rgba(26, 22, 18, 0.28)), ' . 
            ($pujas_gold_image ? 'url(\'' . esc_url($pujas_gold_image) . '\'), ' : '') . 
            'url(\'' . esc_url($local_gold_fallback) . '\'), ' . 
            'linear-gradient(var(--terra), var(--terra));"';
        ?>
        <div class="sk-pujas-scroll-container<?php echo !empty($pujas_scroll_image) ? ' has-scroll-bg' : ''; ?>"<?php echo !empty($pujas_scroll_image) ? ' style="background-image: url(\'' . esc_url($pujas_scroll_image) . '\');"' : ''; ?>>
          <?php if ($pujas_eyebrow) : ?>
          <p class="sk-pujas-eyebrow"<?php echo $eyebrow_gold_style; ?>><?php echo esc_html($pujas_eyebrow); ?></p>
          <?php endif; ?>
          
          <?php
          $pujas_cutout_style = $philosophy_bg ? ' style="background-image: url(\'' . esc_url($philosophy_bg) . '\');"' : '';
          ?>
          <h2 class="sk-pujas-display-h2">
            <span class="sk-pujas-heading-clip"<?php echo $pujas_cutout_style; ?>>
              <span class="sk-pujas-title-kerala">Kerala</span>
              <span class="sk-pujas-title-tantric">Tantric Pujas</span>
            </span>
            <?php
            $em_text = sk_option('pujas_heading_em', 'Sri Vidya Tradition');
            $em_words = explode(' ', trim($em_text));
            if (count($em_words) >= 3) {
                $tradition = array_pop($em_words);
                $srividya = implode(' ', $em_words);
            } elseif (count($em_words) === 2) {
                $srividya = $em_words[0];
                $tradition = $em_words[1];
            } else {
                $srividya = $em_text;
                $tradition = '';
            }
            ?>
            <em>
              <span class="sk-pujas-em-srividya"<?php echo $gradient_gold_style; ?>><?php echo esc_html($srividya); ?></span>
              <?php if ($tradition) : ?>
              <?php
              $first_char = mb_substr($tradition, 0, 1);
              $has_letter_t = (strcasecmp($first_char, 'T') === 0);
              $tradition_class = 'sk-pujas-em-tradition' . ($has_letter_t ? ' has-letter-t' : '');
              ?>
              <span class="<?php echo esc_attr($tradition_class); ?>">
                <?php
                if ($has_letter_t) {
                    $rest_tradition = mb_substr($tradition, 1);
                    $letter_t_url = get_template_directory_uri() . '/assets/images/letter-t.png?v=1.4';
                    echo '<img src="' . esc_url($letter_t_url) . '" class="sk-pujas-letter-t" alt="T" />';
                    echo '<span class="sk-pujas-tradition-text"' . $gradient_gold_style . '>' . esc_html($rest_tradition) . '</span>';
                } else {
                    echo '<span class="sk-pujas-tradition-text"' . $gradient_gold_style . '>' . esc_html($tradition) . '</span>';
                }
                ?>
              </span>
              <?php endif; ?>
            </em>
          </h2>
          
          <!-- Left-aligned Compass star divider -->
          <div class="sk-pujas-divider-left" aria-hidden="true">
            <span></span>
            <svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="2" fill="currentColor"/></svg>
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin: 0 4px;">
              <!-- 4 long cardinal points (thickened) -->
              <path d="M14 0 C11.5 7 11.5 10 14 14 C16.5 10 16.5 7 14 0Z" fill="currentColor"/>
              <path d="M14 28 C11.5 21 11.5 18 14 14 C16.5 18 16.5 21 14 28Z" fill="currentColor"/>
              <path d="M0 14 C7 11.5 10 11.5 14 14 C10 16.5 7 16.5 0 14Z" fill="currentColor"/>
              <path d="M28 14 C21 11.5 18 11.5 14 14 C18 16.5 21 16.5 28 14Z" fill="currentColor"/>
              <!-- 4 shorter diagonal points (thickened) -->
              <path d="M14 4 C12.0 9 12.0 11 14 14 C16.0 11 16.0 9 14 4Z" fill="currentColor" opacity="0.55" transform="rotate(45 14 14)"/>
              <path d="M14 4 C12.0 9 12.0 11 14 14 C16.0 11 16.0 9 14 4Z" fill="currentColor" opacity="0.55" transform="rotate(135 14 14)"/>
              <path d="M14 4 C12.0 9 12.0 11 14 14 C16.0 11 16.0 9 14 4Z" fill="currentColor" opacity="0.55" transform="rotate(225 14 14)"/>
              <path d="M14 4 C12.0 9 12.0 11 14 14 C16.0 11 16.0 9 14 4Z" fill="currentColor" opacity="0.55" transform="rotate(315 14 14)"/>
            </svg>
            <svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="2" fill="currentColor"/></svg>
            <span></span>
          </div>
        </div>
      </div> <!-- closes sk-pujas-left-col -->

      <!-- RIGHT SIDE: Two vertically stacked cards and actions -->
      <div class="sk-pujas-right-col reveal">
        <?php
        $founders_bg  = sk_option('founders_bg_image', '');
        $card1_bg_style = $founders_bg ? ' style="background-image: linear-gradient(rgba(255, 251, 243, 0.93), rgba(255, 251, 243, 0.93)), url(\'' . esc_url($founders_bg) . '\'); background-size: 300%; background-position: center;"' : '';
        $card2_bg_style = $founders_bg ? ' style="background-image: linear-gradient(rgba(255, 251, 243, 0.93), rgba(255, 251, 243, 0.93)), url(\'' . esc_url($founders_bg) . '\'); background-size: 300%; background-position: center 75%;"' : '';
        ?>
        
        <!-- Card 1: Narrative Intro -->
        <div class="sk-pujas-content-card reveal d1"<?php echo $card1_bg_style; ?>>
          <p class="sk-pujas-card-text">
            <?php echo esc_html( sk_option('pujas_intro_text', 'Kerala Tantric Pujas of the Sri Vidya tradition are sacred rituals rooted in temple, mantra and Devi worship practices passed down through traditional lineages. These rites work with mantra, yantra, flame, and divine invocation to restore balance, protection, clarity and spiritual alignment.') ); ?>
          </p>
        </div>
        
        <!-- Card 2: Editorial Callout -->
        <div class="sk-pujas-content-card reveal d2"<?php echo $card2_bg_style; ?>>
          <p class="sk-pujas-card-text font-italic">
            <?php echo esc_html( sk_option('pujas_callout_text', 'In the Sri Vidya tradition, rituals are performed with deep reverence to the Divine Feminine and are believed to soften karmic influences, strengthen spiritual energy and bring harmony into one’s life journey. At Sacred Kompass, these practices are conducted with sincerity, traditional understanding and spiritual care.') ); ?>
          </p>
        </div>

        <!-- Left-aligned Actions (sitting below cards) -->
        <div class="sk-pujas-actions-left reveal d3">
          <?php
          $pujas_cta_text  = sk_option('pujas_cta_text',  'Enquire about a puja');
          $pujas_cta_url   = sk_option('pujas_cta_url',   '/#contact');
          if (empty($pujas_cta_url)) {
              $pujas_cta_url = sk_option('contact_url', '/#contact');
          }
          $pujas_cta2_text = sk_option('pujas_cta2_text', 'Learn how it works');
          $pujas_cta2_url  = sk_option('pujas_cta2_url',  '/offerings/');
          
          $pujas_cta_href  = (str_starts_with($pujas_cta_url, 'http') || str_starts_with($pujas_cta_url, '#')) ? esc_url($pujas_cta_url) : esc_url(home_url($pujas_cta_url));
          $pujas_cta2_href = (str_starts_with($pujas_cta2_url, 'http') || str_starts_with($pujas_cta2_url, '#')) ? esc_url($pujas_cta2_url) : esc_url(home_url($pujas_cta2_url));
          ?>
          <?php if ($pujas_cta_text && $pujas_cta_url) : ?>
          <a href="<?php echo $pujas_cta_href; ?>" class="btn btn-primary btn-sm"><?php echo esc_html($pujas_cta_text); ?></a>
          <?php endif; ?>
          <?php if ($pujas_cta2_text && $pujas_cta2_url) : ?>
          <a href="<?php echo $pujas_cta2_href; ?>" class="btn btn-outline btn-sm"><?php echo esc_html($pujas_cta2_text); ?></a>
          <?php endif; ?>
        </div>

      </div>

    </div>
  </div> <!-- closes inner .wrap -->
</div> <!-- closes .sk-pujas-section -->
