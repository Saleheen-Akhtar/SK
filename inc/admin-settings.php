<?php
/**
 * Sacred Kompass — Site Settings Page & Admin Submenus
 */
defined('ABSPATH') || exit;

add_action('admin_menu', 'sk_register_admin_menu', 9);
function sk_register_admin_menu(): void {
    add_menu_page('Sacred Kompass — Site Settings','★ Sacred Kompass','manage_options','sk-settings','sk_settings_page','dashicons-star-filled',25);
}

/* ── Admin notice: warn when no Nav Items are published ── */
add_action('admin_notices', 'sk_nav_empty_notice');
function sk_nav_empty_notice(): void {
    $screen = get_current_screen();
    if (!$screen) return;
    $relevant = in_array($screen->id, ['toplevel_page_sk-settings', 'edit-sk_nav'], true)
        || (isset($_GET['page']) && str_starts_with($_GET['page'] ?? '', 'sk-'));
    if (!$relevant) return;

    $has_nav = get_transient('sk_has_nav_items');
    if ($has_nav === false) {
        $count   = (int) wp_count_posts('sk_nav')->publish ?? 0;
        $has_nav = $count > 0 ? 'yes' : 'no';
        set_transient('sk_has_nav_items', $has_nav, 5 * MINUTE_IN_SECONDS);
    }
    if ($has_nav === 'yes') return;

    $url = admin_url('edit.php?post_type=sk_nav');
    echo '<div class="notice notice-warning"><p>';
    echo '<strong>Sacred Kompass — Navigation:</strong> ';
    echo 'No published Navigation Items found. The site is currently using the <strong>hardcoded fallback nav</strong>. ';
    echo '<a href="' . esc_url($url) . '">Add nav items →</a> to take full control of the menu.';
    echo '</p></div>';
}

add_action('save_post_sk_nav',    fn() => delete_transient('sk_has_nav_items'));
add_action('transition_post_status', function(string $new, string $old, WP_Post $post): void {
    if ($post->post_type === 'sk_nav') delete_transient('sk_has_nav_items');
}, 10, 3);

function sk_nest_cpt_menus(): void {
    $remove = ['sk_art', 'sk_faq', 'sk_team'];
    foreach ($remove as $pt) {
        remove_menu_page('edit.php?post_type=' . $pt);
    }
    add_submenu_page('sk-settings','Art for Peace','✦ Art for Peace','edit_posts','edit.php?post_type=sk_art');
    add_submenu_page('sk-settings','FAQ','✦ FAQ','edit_posts','edit.php?post_type=sk_faq');
    add_submenu_page('sk-settings','Team Members','✦ Team Members','edit_posts','edit.php?post_type=sk_team');
}
add_action('admin_menu', 'sk_nest_cpt_menus', 101);

/* ── SETTINGS PAGE RENDER ── */
function sk_settings_page(): void {
    if (!current_user_can('manage_options')) wp_die('Access denied.');

    
    if (isset($_POST['sk_settings_nonce']) && wp_verify_nonce($_POST['sk_settings_nonce'], 'sk_save_settings')) {
        $text_fields = ['sk_hero_eyebrow','sk_hero_label_from','sk_hero_label_to','sk_hero_cta1_text','sk_hero_cta1_url','sk_hero_cta2_text','sk_hero_cta2_url','sk_hero_bg_image','sk_hero_bg_image_mobile','sk_hero_bg_video','sk_hero_right_image','sk_about_eyebrow','sk_about_tagline','sk_about_org_descriptor','sk_about_heading','sk_about_brand_bg_image','sk_about_brand_domain','sk_about_bg_image','sk_about_bg_image_mobile','sk_about_bridge_copy','sk_about_body','sk_about_welcome_strip','sk_quote_eyebrow','sk_quote_impact_phrase','sk_quote_text','sk_quote_highlight','sk_quote_attr','sk_founders_eyebrow','sk_founders_heading','sk_founders_heading_em','sk_founders_sub','sk_founders_body','sk_founders_team_image','sk_founders_team_title','sk_founders_team_subtitle','sk_founders_hover_hint','sk_founders_cta_label','sk_founders_bg_image','sk_founders_bg_image_mobile','sk_art_eyebrow','sk_art_heading','sk_art_heading_em','sk_art_sub','sk_art_cta_url','sk_art_bg_image','sk_art_bg_image_mobile','sk_stories_eyebrow','sk_stories_heading','sk_stories_heading_em','sk_stories_sub','sk_stories_preview_eyebrow','sk_stories_preview_heading','sk_stories_preview_heading_em','sk_stories_preview_sub','sk_stories_preview_see_all','sk_stories_preview_bg_image','sk_stories_preview_bg_image_mobile','sk_philosophy_heading','sk_philosophy_heading_em','sk_philosophy_intro','sk_philosophy_bg_image','sk_philosophy_bg_image_mobile','sk_philosophy_bg_video','sk_philosophy_title_image','sk_pujas_eyebrow','sk_pujas_heading','sk_pujas_heading_em','sk_pujas_gold_image','sk_pujas_scroll_image','sk_pujas_intro_text','sk_pujas_circumstances_heading','sk_pujas_callout_text','sk_pujas_cta_text','sk_pujas_cta_url','sk_pujas_cta2_text','sk_pujas_cta2_url','sk_cta_eyebrow','sk_cta_heading','sk_cta_sub','sk_cta_default_heading_l1','sk_cta_default_heading_l2','sk_cta_default_heading_em','sk_cta_card_eyebrow','sk_cta_card_subheading_1','sk_cta_card_subheading_em','sk_cta_ff_name_label','sk_cta_ff_email_label','sk_cta_ff_msg_label','sk_cta_ff_submit_label','sk_cta_ff_note','sk_cta_bg_image','sk_cta_bg_image_mobile','sk_forminator_form_id','sk_webhook_url','sk_webhook_token','sk_journal_preview_heading','sk_journal_preview_eyebrow','sk_journal_preview_see_all','sk_faq_heading_1','sk_faq_heading_em','sk_faq_sub','sk_faq_cta_label','sk_faq_bg_image','sk_faq_bg_image_mobile','sk_nav_cta_label','sk_nav_cta_url','sk_footer_email','sk_footer_phone','sk_footer_address','sk_footer_tagline','sk_footer_copyright','sk_newsletter_disclaimer','sk_footer_location_bar','sk_footer_col_navigate','sk_footer_col_art','sk_footer_col_connect','sk_footer_col_legal','sk_social_instagram','sk_social_facebook','sk_social_whatsapp','sk_collective_hero_eyebrow','sk_collective_hero_sub','sk_collective_founders_eyebrow','sk_collective_founder_badge','sk_collective_founder_cta','sk_collective_team_eyebrow','sk_collective_cta_eyebrow','sk_collective_cta_heading_1','sk_collective_cta_heading_em','sk_collective_cta_body','sk_collective_cta_button','sk_seo_home_title','sk_seo_home_desc','sk_seo_og_image','sk_logo_url','sk_stories_page_hero_image','sk_stories_hero_title','sk_stories_hero_sub','sk_stories_badge_labels','sk_stories_cta_heading','sk_stories_cta_sub','sk_stories_cta_btn_label','sk_stories_no_results','sk_stories_read_more','sk_contact_url'];
        foreach ($text_fields as $k) {
            update_option('options_'.$k, isset($_POST[$k]) ? wp_kses_post(stripslashes($_POST[$k])) : '', false);
        }
        $pillars = [];
        if (!empty($_POST['pillar_num']) && is_array($_POST['pillar_num'])) {
            foreach ($_POST['pillar_num'] as $i => $num) {
                $pillars[] = [
                    'pillar_num' => sanitize_text_field($num),
                    'pillar_title' => sanitize_text_field($_POST['pillar_title'][$i] ?? ''),
                    'pillar_desc' => sanitize_textarea_field($_POST['pillar_desc'][$i] ?? ''),
                    'pillar_image' => esc_url_raw($_POST['pillar_image'][$i] ?? ''),
                    'pillar_img_position' => sanitize_text_field($_POST['pillar_img_position'][$i] ?? ''),
                ];
            }
        }
        update_option('options_sk_philosophy_pillars_json', wp_json_encode($pillars), false);
        if (function_exists('update_field')) {
            update_field('sk_philosophy_pillars', $pillars, 'option');
        }
        
        $hero_pairs = [];
        if (!empty($_POST['hero_pair_from']) && is_array($_POST['hero_pair_from'])) {
            foreach ($_POST['hero_pair_from'] as $i => $from) {
                $f = sanitize_text_field($from);
                $t_val = sanitize_text_field($_POST['hero_pair_to'][$i] ?? '');
                if ($f || $t_val) $hero_pairs[] = ['from' => $f, 'to' => $t_val];
            }
        }
        update_option('options_sk_hero_pairs_json', wp_json_encode($hero_pairs), false);
        if (function_exists('update_field')) {
            update_field('sk_hero_pairs', $hero_pairs, 'option');
        }
        delete_transient('sk_stories_preview_data_v4');
        echo '<div class="notice notice-success is-dismissible" style="margin:10px 0 20px"><p><strong>Sacred Kompass:</strong> Settings saved.</p></div>';
    }
    $o = fn(string $k, string $fb='') => esc_attr((string)get_option('options_'.$k, $fb));
    $t = fn(string $k, string $fb='') => esc_textarea((string)get_option('options_'.$k, $fb));
    $pillars = sk_repeater('options_sk_philosophy_pillars_json') ?: sk_default_pillars();
    ?>
    <div class="wrap"><h1>★ Sacred Kompass — Site Settings</h1>
    <form method="post" action="" id="sk-settings-form">
    <?php wp_nonce_field('sk_save_settings','sk_settings_nonce'); ?>
    <style>#sk-settings-form{max-width:900px}.sk-save-bar{position:sticky;top:32px;z-index:99;background:#f0f0f1;padding:10px 0;margin:0 0 20px;border-bottom:1px solid #ddd}.sk-section{background:#fff;border:1px solid #c3c4c7;border-radius:4px;margin:0 0 20px;padding:20px 24px}.sk-section>h2{font-size:14px;font-weight:700;margin:0 0 16px;padding:0 0 10px;border-bottom:1px solid #f0f0f1;color:#1d2327;text-transform:uppercase;letter-spacing:.05em}.sk-row{display:grid;grid-template-columns:200px 1fr;gap:6px 16px;align-items:start;margin:0 0 12px}.sk-row label{font-size:13px;font-weight:500;padding-top:7px;color:#3c434a}.sk-row input[type=text],.sk-row textarea{width:100%;box-sizing:border-box}.sk-hint{color:#646970;font-size:11px;margin:3px 0 0}.sk-rep-row{background:#f9f9f9;border:1px solid #dcdcde;border-radius:3px;padding:14px 16px;margin:0 0 10px;position:relative}.sk-rep-row h4{margin:0 0 12px;font-size:13px;color:#1d2327;font-weight:600}.sk-btn-del{position:absolute;top:10px;right:10px;background:#dc3232;color:#fff;border:none;border-radius:3px;padding:3px 10px;font-size:11px;cursor:pointer}.sk-btn-add{background:#2271b1;color:#fff;border:none;border-radius:3px;padding:7px 16px;font-size:13px;cursor:pointer;margin-top:4px}</style>
    <div class="sk-save-bar"><input type="submit" class="button button-primary button-large" value="Save All Changes" /></div>
    <div class="sk-section"><h2>✦ Hero</h2>
    <?php sk_row('Eyebrow text (above animation)','sk_hero_eyebrow',$o('sk_hero_eyebrow','Sacred Kompass · Transformation'),'Small uppercase line above the From→To animation. Leave blank to hide it completely.'); ?>
    <?php sk_row('"From" label','sk_hero_label_from',$o('sk_hero_label_from','from'),'The italic label to the left of the struggle word. Leave blank to hide the label.'); ?>
    <?php sk_row('"To" label','sk_hero_label_to',$o('sk_hero_label_to','to'),'The italic label to the left of the transformation word. Leave blank to hide the label.'); ?>
    <?php sk_row('CTA 1 Text','sk_hero_cta1_text',$o('sk_hero_cta1_text','')); ?>
    <?php sk_row('CTA 1 URL','sk_hero_cta1_url',$o('sk_hero_cta1_url','/#contact')); ?>
    <?php sk_row('CTA 2 Text','sk_hero_cta2_text',$o('sk_hero_cta2_text','')); ?>
    <?php sk_row('CTA 2 URL','sk_hero_cta2_url',$o('sk_hero_cta2_url','#offerings')); ?>
    <?php sk_row('Background Image URL','sk_hero_bg_image',$o('sk_hero_bg_image'),'Full URL of the hero background photo (e.g. ' . esc_url(home_url('/wp-content/uploads/2026/05/photo.jpg')) . '). Upload via Media Library, copy the URL, paste here. This image also fills the SACRED KOMPASS letterform in the About section. Without it both areas show a plain gradient fallback.'); ?>
    <?php sk_row('Background Image URL (Mobile)','sk_hero_bg_image_mobile',$o('sk_hero_bg_image_mobile'),'Optional mobile background image for the Hero section.'); ?>

    <?php sk_row('Background Video URL (MP4)','sk_hero_bg_video',$o('sk_hero_bg_video'),'Optional. Full URL of an MP4 video to use as the hero background (e.g. ' . esc_url(home_url('/wp-content/uploads/2026/05/hero.mp4')) . '). Upload via Media Library → copy URL → paste here. When set, the video plays instead of the background image. The background image above is used as the poster frame shown while the video loads.'); ?>
    <div class="sk-row"><label>Transformation Pairs</label><div>
      <p class="sk-hint" style="margin-bottom:10px">Each row is a "From → To" pair cycling in the hero. <strong>From</strong> = the struggle (italic, muted). <strong>To</strong> = the transformation (bold, white).</p>
      <div id="hero-pairs-wrap">
        <?php
        $pairs_saved = sk_repeater('options_sk_hero_pairs_json');
        $pairs_display = !empty($pairs_saved) ? $pairs_saved : [
          ['from'=>'Despair','to'=>'Hope'],
          ['from'=>'Business Failure','to'=>'Profitability'],
          ['from'=>'Resentment','to'=>'Forgiveness'],
          ['from'=>'Adversity','to'=>'Opportunity'],
          ['from'=>'Hatred','to'=>'Peace'],
          ['from'=>'Lonely','to'=>'Couplehood'],
          ['from'=>'Impulsive','to'=>'Aligned'],
          ['from'=>'Confusion','to'=>'Clarity'],
          ['from'=>'Stagnant','to'=>'Evolving'],
          ['from'=>'Mistrust','to'=>'Faith'],
          ['from'=>'Lethargy','to'=>'Vitality'],
        ];
        foreach ($pairs_display as $pi => $pair): ?>
        <div class="sk-rep-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:8px;align-items:center;padding:10px 14px">
          <input type="text" name="hero_pair_from[]" value="<?php echo esc_attr($pair['from']); ?>" placeholder="From (struggle)" style="width:100%;box-sizing:border-box" />
          <input type="text" name="hero_pair_to[]"   value="<?php echo esc_attr($pair['to']); ?>"   placeholder="To (transformation)" style="width:100%;box-sizing:border-box" />
          <button type="button" class="sk-btn-del" style="position:static;font-size:10px;padding:4px 8px">✕</button>
        </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="sk-btn-add" id="sk-add-hero-pair" style="margin-top:8px;font-size:12px;padding:5px 14px">+ Add Pair</button>
    </div></div>
    </div>
    <div class="sk-section"><h2>✦ About</h2>
    <p class="description" style="padding:8px 12px;background:#f9f6f0;border-left:3px solid #c4a02a;border-radius:2px;margin-bottom:14px;font-size:12px">
      <strong>Layout:</strong> Left column — SACRED KOMPASS brand name + tagline. Right column — three rows: organisation descriptor (top), main body copy (middle), welcome line (bottom).
    </p>
    <?php sk_row('Eyebrow','sk_about_eyebrow',$o('sk_about_eyebrow','Who We Are'),'Small uppercase label shown above the section heading — same style as other sections.'); ?>
    <?php sk_row('Tagline','sk_about_tagline',$o('sk_about_tagline','Exploring Your Inner Journey'),'Shown beneath the brand name on the left. Keep it short — one line.'); ?>
    <?php sk_row('Organisation Descriptor','sk_about_org_descriptor',$o('sk_about_org_descriptor','An Organisation for Consciousness and Transformation'),'Top row on the right. One-line descriptor of what Sacred Kompass is.'); ?>
    <?php sk_row('Section Heading','sk_about_heading',$o('sk_about_heading',''),'Optional bold heading above the main body paragraph.'); ?>
    <?php sk_row_ta('Main Body Copy','sk_about_bridge_copy',$t('sk_about_bridge_copy','We bridge ancient wisdom and modern living through Vedic Astrology, Meditative Journeys, and Events on Well-being'),3,'Middle row on the right. Main descriptive paragraph.'); ?>
    <?php sk_row_ta('Supporting Paragraph','sk_about_body',$t('sk_about_body'),3,'Optional second paragraph below the main body. Good for SEO. Leave blank to hide.'); ?>
    <?php sk_row('Welcome Line','sk_about_welcome_strip',$o('sk_about_welcome_strip','Welcome to Sacred Kompass where your next chapter begins'),'Bottom row on the right. Welcome message or short CTA.'); ?>
    <?php sk_row('Brand Domain to Auto-Link','sk_about_brand_domain',$o('sk_about_brand_domain','sacredkompass.org'),'The domain to be auto-linked inside the welcome line back to the home page (e.g. sacredkompass.org).'); ?>
    <?php sk_row('Brand Name Background Image URL','sk_about_brand_bg_image',$o('sk_about_brand_bg_image'),'The image used as fill texture inside the large SACRED KOMPASS letters.'); ?>
    <?php sk_row('Section Background Image URL','sk_about_bg_image',$o('sk_about_bg_image'),'Optional background image for the About section.'); ?>
    <?php sk_row('Section Background Image URL (Mobile)','sk_about_bg_image_mobile',$o('sk_about_bg_image_mobile'),'Optional mobile background image for the About section.'); ?>

    </div>
    <div class="sk-section"><h2>✦ Philosophy Strip</h2>
    <?php sk_row('Heading','sk_philosophy_heading',$o('sk_philosophy_heading','How We Work')); ?>
    <?php sk_row('Heading italic','sk_philosophy_heading_em',$o('sk_philosophy_heading_em','With You')); ?>
    <?php sk_row_ta('Intro paragraph','sk_philosophy_intro',$t('sk_philosophy_intro','Every pathway begins with a single question: what is ready to be seen? These are the lenses we bring.'),2,'Shown below the heading, above the pillar carousel.'); ?>
    <?php sk_row('Background Image URL','sk_philosophy_bg_image',$o('sk_philosophy_bg_image',''),'Full URL or relative path to the section background image. Leave blank for no background image.'); ?>
    <?php sk_row('Background Image URL (Mobile)','sk_philosophy_bg_image_mobile',$o('sk_philosophy_bg_image_mobile',''),'Optional mobile background image for the Philosophy section.'); ?>

    <?php sk_row('Background Video URL','sk_philosophy_bg_video',$o('sk_philosophy_bg_video',''),'Full URL or relative path to the section background MP4 video. Leave blank for no background video.'); ?>
    <?php sk_row('Vedic Astrology Title Image URL','sk_philosophy_title_image',$o('sk_philosophy_title_image',''),'Full URL of the title image to show at the top of the cards section. Upload via Media Library, copy the URL, and paste here.'); ?>
    <?php sk_row('Kerala Section Eyebrow','sk_pujas_eyebrow',$o('sk_pujas_eyebrow','TEMPLE RITUALS & DEVI WORSHIP'),'Eyebrow above the Kerala Pujas section title.'); ?>
    <?php sk_row('Kerala Section Heading','sk_pujas_heading',$o('sk_pujas_heading','Kerala Tantric Pujas'),'Heading for the Kerala Pujas section.'); ?>
    <?php sk_row('Kerala Section Heading Italic','sk_pujas_heading_em',$o('sk_pujas_heading_em','Sri Vidya Tradition'),'Italicized line below the heading.'); ?>
    <?php sk_row('Kerala Gold Image URL','sk_pujas_gold_image',$o('sk_pujas_gold_image','http://sacredkompass.local/wp-content/uploads/2026/06/gold-texture.jpeg'),'URL to the gold leaf texture image used as fill inside the italicized "Sri Vidya Tradition" heading text.'); ?>
    <?php sk_row('Kerala Scroll Image URL','sk_pujas_scroll_image',$o('sk_pujas_scroll_image',''),'URL to the scroll paper image used as background for the titles.'); ?>
    <?php sk_row_ta('Kerala Section Intro','sk_pujas_intro_text',$t('sk_pujas_intro_text','Kerala Tantric Pujas of the Sri Vidya tradition are sacred rituals rooted in temple, mantra and Devi worship practices passed down through traditional lineages. These rites work with mantra, yantra, flame, and divine invocation to restore balance, protection, clarity and spiritual alignment.'),4,'Introductory narrative paragraph.'); ?>
    <?php sk_row('Kerala Circumstances Title','sk_pujas_circumstances_heading',$o('sk_pujas_circumstances_heading','Explore circumstances these pujas are performed for'),'Small heading above the circumstances cards.'); ?>
    <?php sk_row_ta('Kerala Callout Text','sk_pujas_callout_text',$t('sk_pujas_callout_text','In the Sri Vidya tradition, rituals are performed with deep reverence to the Divine Feminine and are believed to soften karmic influences, strengthen spiritual energy and bring harmony into one’s life journey. At Sacred Kompass, these practices are conducted with sincerity, traditional understanding and spiritual care.'),4,'Editorial callout block text.'); ?>
    <?php sk_row('Kerala CTA 1 Text','sk_pujas_cta_text',$o('sk_pujas_cta_text','Enquire about a puja'),'Text label for the primary action button.'); ?>
    <?php sk_row('Kerala CTA 1 URL','sk_pujas_cta_url',$o('sk_pujas_cta_url','/#contact'),'Target page or anchor for the primary button.'); ?>
    <?php sk_row('Kerala CTA 2 Text','sk_pujas_cta2_text',$o('sk_pujas_cta2_text','Learn how it works'),'Text label for the secondary outline button.'); ?>
    <?php sk_row('Kerala CTA 2 URL','sk_pujas_cta2_url',$o('sk_pujas_cta2_url','/offerings/'),'Target page or anchor for the secondary button.'); ?>
    <div id="pillars-wrap">
    <?php foreach ($pillars as $pi=>$p): ?><div class="sk-rep-row"><h4>Pillar <?php echo $pi+1;?></h4><button type="button" class="sk-btn-del">Remove</button><?php sk_sub_row('No.','pillar_num[]',esc_attr($p['pillar_num']??''));sk_sub_row('Title','pillar_title[]',esc_attr($p['pillar_title']??''));sk_sub_row_ta('Desc','pillar_desc[]',esc_textarea($p['pillar_desc']??''),2);sk_sub_row('Image URL','pillar_image[]',esc_attr($p['pillar_image']??''));sk_sub_row('Image Position (CSS)','pillar_img_position[]',esc_attr($p['pillar_img_position']??''));?></div><?php endforeach;?>
    </div><button type="button" class="sk-btn-add" id="sk-add-pillar">+ Add Pillar</button></div>
    <div class="sk-section"><h2>✦ Quote Band</h2>
    <?php sk_row('Eyebrow','sk_quote_eyebrow',$o('sk_quote_eyebrow','Our Vision')); ?>
    <?php sk_row('Impact Phrase','sk_quote_impact_phrase',$o('sk_quote_impact_phrase'),'Optional large-format phrase rendered above the quote (e.g. "Remember." or "Come home."). Leave blank to hide.'); ?>
    <?php sk_row_ta('Quote','sk_quote_text',$t('sk_quote_text'),4); ?>
    <?php sk_row('Highlight Phrase','sk_quote_highlight',$o('sk_quote_highlight','inner compass')); ?>
    <?php sk_row('Attribution','sk_quote_attr',$o('sk_quote_attr')); ?>
    </div>
    <div class="sk-section"><h2>✦ Founders</h2>
    <?php sk_row('Eyebrow','sk_founders_eyebrow',$o('sk_founders_eyebrow','The Founders')); ?>
    <?php sk_row('Heading','sk_founders_heading',$o('sk_founders_heading','The Guides Behind')); ?>
    <?php sk_row('Heading italic','sk_founders_heading_em',$o('sk_founders_heading_em','Sacred Kompass')); ?>
    <?php sk_row_ta('Sub-text','sk_founders_sub',$t('sk_founders_sub'),2); ?>
    <?php sk_row_ta('Editorial Body','sk_founders_body',$t('sk_founders_body','From Vedic philosophy and sacred feminine wisdom to conscious leadership and non-violent communication — every guide brings a living practice, not just a credential.'),3,'The second paragraph in the Founders section editorial copy.'); ?>
    <?php sk_row('Team Card Image URL','sk_founders_team_image',$o('sk_founders_team_image')); ?>
    <?php sk_row('Team Card Title','sk_founders_team_title',$o('sk_founders_team_title','Our Team')); ?>
    <?php sk_row('Team Card Subtitle','sk_founders_team_subtitle',$o('sk_founders_team_subtitle')); ?>
    <?php sk_row('Section Background Image URL','sk_founders_bg_image',$o('sk_founders_bg_image'),'Optional background image for the Founders section.'); ?>
    <?php sk_row('Section Background Image URL (Mobile)','sk_founders_bg_image_mobile',$o('sk_founders_bg_image_mobile'),'Optional mobile background image for the Founders section.'); ?>

    </div>
    <div class="sk-section"><h2>✦ Art for Peace</h2>
    <?php sk_row('Eyebrow','sk_art_eyebrow',$o('sk_art_eyebrow','Art for Peace')); ?>
    <?php sk_row('Heading','sk_art_heading',$o('sk_art_heading','Exhibition of')); ?>
    <?php sk_row('Heading italic','sk_art_heading_em',$o('sk_art_heading_em','Healing')); ?>
    <?php sk_row_ta('Sub-description','sk_art_sub',$t('sk_art_sub', 'Artworks created as part of therapeutic healing, mindfulness and transformation. Explore our active collections below.'),2); ?>
    <?php sk_row('CTA URL','sk_art_cta_url',$o('sk_art_cta_url','/#contact'),'Default inquiry page / link when custom URL is not specified.'); ?>
    <?php sk_row('Section Background Image URL','sk_art_bg_image',$o('sk_art_bg_image'),'Optional background image for the Art for Peace section.'); ?>
    <?php sk_row('Section Background Image URL (Mobile)','sk_art_bg_image_mobile',$o('sk_art_bg_image_mobile'),'Optional mobile background image for the Art for Peace section.'); ?>

    </div>
    <div class="sk-section"><h2>✦ Client Stories</h2>
    <p style="font-size:12px;color:#646970;margin:0 0 14px">The carousel of testimonial cards below this heading. Individual testimonials are managed via <strong>Testimonials</strong> in the sidebar.</p>
    <?php sk_row('Eyebrow','sk_stories_eyebrow',$o('sk_stories_eyebrow','Client Stories')); ?>
    <?php sk_row('Heading','sk_stories_heading',$o('sk_stories_heading','Words from the')); ?>
    <?php sk_row('Heading italic','sk_stories_heading_em',$o('sk_stories_heading_em','Journey')); ?>
    <?php sk_row_ta('Sub-description','sk_stories_sub',$t('sk_stories_sub',"From clarity seekers to conscious leaders — here's how the pathways have moved people."),2); ?>
    </div>
    <div class="sk-section"><h2>✦ Stories Preview &amp; Stories Page</h2>
    <p style="font-size:12px;color:#646970;margin:0 0 14px">The homepage stories grid showing sk_story posts. The background image is optional — upload a photo via <strong>Media Library</strong>, copy its URL, paste below.</p>
    <?php sk_row('Eyebrow','sk_stories_preview_eyebrow',$o('sk_stories_preview_eyebrow','')); ?>
    <?php sk_row('Heading','sk_stories_preview_heading',$o('sk_stories_preview_heading',''), 'Supports HTML tags like &lt;em&gt; to style the blood-red italicized text.'); ?>
    <?php sk_row_ta('Sub-description','sk_stories_preview_sub',$t('sk_stories_preview_sub',''),2); ?>
    <?php sk_row('See all label','sk_stories_preview_see_all',$o('sk_stories_preview_see_all','Read all stories')); ?>
    <?php sk_row('Background Image URL','sk_stories_preview_bg_image',$o('sk_stories_preview_bg_image'),'Optional. Full URL of a background image for this section. Upload to Media Library, copy URL, paste here. Leave blank for plain ivory background.'); ?>
    <?php sk_row('Background Image URL (Mobile)','sk_stories_preview_bg_image_mobile',$o('sk_stories_preview_bg_image_mobile'),'Optional mobile background image for the Stories Preview section.'); ?>

    <?php sk_row('Stories Page Hero Image','sk_stories_page_hero_image',$o('sk_stories_page_hero_image'),'Full URL of the woman/nature photo shown on the right side of the /stories/ page hero. Upload to Media Library → copy URL → paste here. Recommended: portrait orientation, 800×600px+.'); ?>
    <?php sk_row('Hero Title','sk_stories_hero_title',$o('sk_stories_hero_title','Stories for the<br>soul'),'Heading for the /stories/ page. Use &lt;br&gt; for line breaks.'); ?>
    <?php sk_row_ta('Hero Subtitle','sk_stories_hero_sub',$t('sk_stories_hero_sub','Real stories from beautiful souls who chose themselves, followed their inner compass, and created meaningful change.'),2,'Subtitle in the stories page hero.'); ?>
    <?php sk_row('Badge Labels (comma separated)','sk_stories_badge_labels',$o('sk_stories_badge_labels','Real Journeys, Heartfelt Transformations, Lasting Impact'),'List of badges shown in the stories page hero.'); ?>
    <?php sk_row('CTA Heading','sk_stories_cta_heading',$o('sk_stories_cta_heading','Your story can inspire change.'),'Heading of the bottom share CTA banner.'); ?>
    <?php sk_row_ta('CTA Sub-description','sk_stories_cta_sub',$t('sk_stories_cta_sub',"If our work together has made an impact, we'd be honored to share your journey (anonymously if you prefer)."),2,'Description of the bottom share CTA banner.'); ?>
    <?php sk_row('CTA Button Label','sk_stories_cta_btn_label',$o('sk_stories_cta_btn_label','Share Your Story')); ?>
    <?php sk_row('No Results Message','sk_stories_no_results',$o('sk_stories_no_results','No stories in this category yet.')); ?>
    <?php sk_row('Card Read More Label','sk_stories_read_more',$o('sk_stories_read_more','Read Her Story')); ?>
    </div>
    <div class="sk-section"><h2>✦ Journal Preview</h2>
    <p style="font-size:12px;color:#646970;margin:0 0 14px">Controls the heading of the homepage journal preview block. Individual posts are managed from <strong>Posts</strong> in the sidebar.</p>
    <?php sk_row('Heading','sk_journal_preview_heading',$o('sk_journal_preview_heading','From the Journal')); ?>
    <?php sk_row('Eyebrow','sk_journal_preview_eyebrow',$o('sk_journal_preview_eyebrow','Journal')); ?>
    <?php sk_row('"See all posts" label','sk_journal_preview_see_all',$o('sk_journal_preview_see_all','See all posts')); ?>
    </div>
    <div class="sk-section"><h2>✦ FAQ</h2>
    <?php sk_row('Heading line 1','sk_faq_heading_1',$o('sk_faq_heading_1','Frequently')); ?>
    <?php sk_row('Heading italic','sk_faq_heading_em',$o('sk_faq_heading_em','Asked')); ?>
    <?php sk_row_ta('Sub-copy','sk_faq_sub',$t('sk_faq_sub','If you have more questions, we warmly invite you to reach out. Every journey begins with a conversation.'),2); ?>
    <?php sk_row('CTA button label','sk_faq_cta_label',$o('sk_faq_cta_label','')); ?>
    <?php sk_row('Section Background Image URL','sk_faq_bg_image',$o('sk_faq_bg_image'),'Optional background image for the FAQ section.'); ?>
    <?php sk_row('Section Background Image URL (Mobile)','sk_faq_bg_image_mobile',$o('sk_faq_bg_image_mobile'),'Optional mobile background image for the FAQ section.'); ?>

    </div>
    <div class="sk-section"><h2>✦ Founders / Collective Preview</h2>
    <?php sk_row('Hover hint text','sk_founders_hover_hint',$o('sk_founders_hover_hint','Meet the Collective')); ?>
    <?php sk_row('CTA button label','sk_founders_cta_label',$o('sk_founders_cta_label','Explore the Collective')); ?>
    </div>
    <div class="sk-section"><h2>✦ Contact</h2>
    <?php sk_row('Eyebrow','sk_cta_eyebrow',$o('sk_cta_eyebrow','')); ?>
    <?php sk_row_ta('Heading (HTML ok)','sk_cta_heading',$t('sk_cta_heading'),2,'Optional. If blank the default three-line layout below is used.'); ?>
    <?php sk_row('Default heading — line 1','sk_cta_default_heading_l1',$o('sk_cta_default_heading_l1','')); ?>
    <?php sk_row('Default heading — line 2','sk_cta_default_heading_l2',$o('sk_cta_default_heading_l2','')); ?>
    <?php sk_row('Default heading — italic','sk_cta_default_heading_em',$o('sk_cta_default_heading_em','')); ?>
    <?php sk_row_ta('Sub-text','sk_cta_sub',$t('sk_cta_sub'),3); ?>
    <?php sk_row('Form card eyebrow','sk_cta_card_eyebrow',$o('sk_cta_card_eyebrow','Connect')); ?>
    <?php sk_row('Form card sub-heading line 1','sk_cta_card_subheading_1',$o('sk_cta_card_subheading_1','Begin a')); ?>
    <?php sk_row('Form card sub-heading italic','sk_cta_card_subheading_em',$o('sk_cta_card_subheading_em','Conversation')); ?>
    <?php sk_row('Fallback — Name label','sk_cta_ff_name_label',$o('sk_cta_ff_name_label','Your Name')); ?>
    <?php sk_row('Fallback — Email label','sk_cta_ff_email_label',$o('sk_cta_ff_email_label','Email Address')); ?>
    <?php sk_row('Fallback — Message label','sk_cta_ff_msg_label',$o('sk_cta_ff_msg_label','Your Message')); ?>
    <?php sk_row('Fallback — Submit button','sk_cta_ff_submit_label',$o('sk_cta_ff_submit_label','Begin a Conversation')); ?>
    <?php sk_row('Fallback — Response note','sk_cta_ff_note',$o('sk_cta_ff_note','We respond within 24 hours')); ?>
    <?php sk_row('Contact section — background image URL','sk_cta_bg_image',$o('sk_cta_bg_image',''),'Full URL to the background photo for the Contact/CTA section. Leave blank to use the default warm ivory background.'); ?>
    <?php sk_row('Contact section — background image URL (Mobile)','sk_cta_bg_image_mobile',$o('sk_cta_bg_image_mobile',''),'Optional mobile background image for the Contact/CTA section.'); ?>

    <?php sk_row('Forminator Form ID','sk_forminator_form_id',$o('sk_forminator_form_id')); ?>
    <?php sk_row('GSheets Webhook URL','sk_webhook_url',$o('sk_webhook_url')); ?>
    <?php sk_row('Webhook Verification Token','sk_webhook_token',$o('sk_webhook_token')); ?>
    <div class="sk-section"><h2>✦ Navigation</h2>
    <?php sk_row('CTA button label','sk_nav_cta_label',$o('sk_nav_cta_label','')); ?>
    <?php sk_row('CTA button URL','sk_nav_cta_url',$o('sk_nav_cta_url','/#contact')); ?>
    <?php sk_row('Central Contact URL','sk_contact_url',$o('sk_contact_url','/#contact'),'Used across the theme on CTA links, buttons, and footers (e.g. /#contact or /contact/).'); ?>
    </div>
    <div class="sk-section"><h2>✦ SEO &amp; Sharing</h2>
    <?php sk_row('Home page title','sk_seo_home_title',$o('sk_seo_home_title','Sacred Kompass — Where the Sacred Meets the Everyday'),'The &lt;title&gt; tag shown in browser tabs and Google results for the home page. Keep under 60 characters for best display.'); ?>
    <?php sk_row_ta('Home meta description','sk_seo_home_desc',$t('sk_seo_home_desc','Sacred Kompass is a transformative wellness and consciousness-based consultancy weaving Vedic astrology, meditation, and emotional resilience into modern life.'),3,'Shown in Google search snippets. Aim for 140–160 characters. If Rank Math SEO is active, also set this in Rank Math › Titles & Meta › Home Page.'); ?>
    <?php sk_row('OG / sharing image URL','sk_seo_og_image',$o('sk_seo_og_image'),'Image shown when the home page is shared on WhatsApp, Facebook, Twitter etc. Recommended: 1200×630px. Leave blank to use the site logo.'); ?>
    <?php sk_row('Site logo URL','sk_logo_url',$o('sk_logo_url'),'Used as the publisher logo in JSON-LD structured data and as the fallback OG image.'); ?>
    </div>
    <div class="sk-section"><h2>✦ Footer &amp; Social</h2>
    <?php sk_row('Email','sk_footer_email',$o('sk_footer_email','collective@sacredkompass.org')); ?>
    <?php sk_row('Phone','sk_footer_phone',$o('sk_footer_phone','+65 84343915')); ?>
    <?php sk_row('Office Address','sk_footer_address',$o('sk_footer_address','557 Bedok North St. 3, Singapore')); ?>
    <?php sk_row_ta('Tagline','sk_footer_tagline',$t('sk_footer_tagline'),2); ?>
    <?php sk_row('Copyright','sk_footer_copyright',$o('sk_footer_copyright','Sacred Kompass Collective · Singapore')); ?>
    <?php sk_row('Newsletter Disclaimer','sk_newsletter_disclaimer',$o('sk_newsletter_disclaimer','Sacred Kompass respects your privacy. Unsubscribe anytime.')); ?>
    <?php sk_row('Location bar (bottom bar)','sk_footer_location_bar',$o('sk_footer_location_bar','Bedok North, Singapore &nbsp;&middot;&nbsp; Online Worldwide'),'Shown in the very bottom bar of the footer.'); ?>
    <?php sk_row('Column label — Navigate','sk_footer_col_navigate',$o('sk_footer_col_navigate','Navigate')); ?>
    <?php sk_row('Column label — Art for Peace','sk_footer_col_art',$o('sk_footer_col_art','Art for Peace')); ?>
    <?php sk_row('Column label — Connect','sk_footer_col_connect',$o('sk_footer_col_connect','Connect')); ?>
    <?php sk_row('Column label — Legal','sk_footer_col_legal',$o('sk_footer_col_legal','Legal')); ?>
    <?php sk_row('Instagram URL','sk_social_instagram',$o('sk_social_instagram')); ?>
    <?php sk_row('Facebook URL','sk_social_facebook',$o('sk_social_facebook')); ?>
    <?php sk_row('WhatsApp Link','sk_social_whatsapp',$o('sk_social_whatsapp')); ?>
    </div>
    <div class="sk-section"><h2>✦ Collective Page</h2>
    <?php sk_row('Hero eyebrow','sk_collective_hero_eyebrow',$o('sk_collective_hero_eyebrow','Sacred Kompass')); ?>
    <?php sk_row_ta('Hero sub-copy','sk_collective_hero_sub',$t('sk_collective_hero_sub','Guides, teachers, and practitioners united by one vision: to help individuals, leaders, and organisations reconnect with their inner compass.'),2); ?>
    <?php sk_row('Founders section eyebrow','sk_collective_founders_eyebrow',$o('sk_collective_founders_eyebrow','The Founders')); ?>
    <?php sk_row('Founder badge label','sk_collective_founder_badge',$o('sk_collective_founder_badge','Founder')); ?>
    <?php sk_row('Founder CTA button','sk_collective_founder_cta',$o('sk_collective_founder_cta','Book a Session')); ?>
    <?php sk_row('Team section eyebrow','sk_collective_team_eyebrow',$o('sk_collective_team_eyebrow','The Team')); ?>
    <?php sk_row('CTA band eyebrow','sk_collective_cta_eyebrow',$o('sk_collective_cta_eyebrow','Ready to begin?')); ?>
    <?php sk_row('CTA band heading','sk_collective_cta_heading_1',$o('sk_collective_cta_heading_1','Work with')); ?>
    <?php sk_row('CTA band heading italic','sk_collective_cta_heading_em',$o('sk_collective_cta_heading_em','our Guides')); ?>
    <?php sk_row_ta('CTA band body copy','sk_collective_cta_body',$t('sk_collective_cta_body','Every guide in the Collective offers sessions tailored to your journey. Reach out and we\'ll match you with the right person.'),2); ?>
    <?php sk_row('CTA band button label','sk_collective_cta_button',$o('sk_collective_cta_button','Book a Discovery Call')); ?>
    </div>

    <div class="sk-save-bar" style="position:static;margin-top:0"><input type="submit" class="button button-primary button-large" value="Save All Changes" /></div>
    </form></div>
    <?php
}

/* Field helpers */
function sk_row(string $l,string $n,string $v,string $h=''): void{echo '<div class="sk-row"><label for="'.esc_attr($n).'">'.esc_html($l).'</label><div><input type="text" id="'.esc_attr($n).'" name="'.esc_attr($n).'" value="'.esc_attr($v).'" />'.($h?'<p class="sk-hint">'.esc_html($h).'</p>':'').'</div></div>';}
function sk_row_ta(string $l,string $n,string $v,int $r=3,string $h=''): void{echo '<div class="sk-row"><label for="'.esc_attr($n).'">'.esc_html($l).'</label><div><textarea id="'.esc_attr($n).'" name="'.esc_attr($n).'" rows="'.intval($r).'">'.esc_textarea($v).'</textarea>'.($h?'<p class="sk-hint">'.esc_html($h).'</p>':'').'</div></div>';}
function sk_sub_row(string $l,string $n,string $v): void{echo '<div class="sk-row"><label>'.esc_html($l).'</label><input type="text" name="'.esc_attr($n).'" value="'.esc_attr($v).'" /></div>';}
function sk_sub_row_ta(string $l,string $n,string $v,int $r=3): void{echo '<div class="sk-row"><label>'.esc_html($l).'</label><textarea name="'.esc_attr($n).'" rows="'.intval($r).'" style="width:100%;box-sizing:border-box">'.esc_textarea($v).'</textarea></div>';}

/* ── ADMIN RESEED ── */
add_action('admin_init','sk_maybe_reseed');
function sk_maybe_reseed(): void {
    if (empty($_GET['sk_reseed'])||!current_user_can('manage_options')) return;
    sk_on_activation();
    add_action('admin_notices',fn()=>print('<div class="notice notice-success is-dismissible"><p><strong>Sacred Kompass:</strong> Re-seeded. <a href="'.admin_url().'">Dashboard</a></p></div>'));
}
