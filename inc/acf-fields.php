<?php
/**
 * Sacred Kompass — ACF Programmatic Local Field Groups
 */
defined('ABSPATH') || exit;

add_action('acf/init', 'sk_register_acf_field_groups');

function sk_register_acf_field_groups(): void {
    if (!function_exists('acf_add_local_field_group')) return;

    // 1. Add Options Page in admin if active
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title'    => '★ Sacred Kompass Settings',
            'menu_title'    => '★ Sacred Kompass Settings',
            'menu_slug'     => 'sk-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false,
            'position'      => 25,
            'icon_url'      => 'dashicons-star-filled',
        ]);
    }

    // ── FIELD GROUP: THEME SETTINGS ────────────────────────
    acf_add_local_field_group([
        'key' => 'group_sk_theme_settings',
        'title' => '★ Sacred Kompass Settings',
        'fields' => [
            // Tab: Hero
            [
                'key' => 'field_sk_tab_hero',
                'label' => 'Hero Section',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_hero_eyebrow',
                'label' => 'Eyebrow Text',
                'name' => 'sk_hero_eyebrow',
                'type' => 'text',
                'default_value' => 'Sacred Kompass · Transformation',
            ],
            [
                'key' => 'field_sk_hero_label_from',
                'label' => '"From" Label',
                'name' => 'sk_hero_label_from',
                'type' => 'text',
                'default_value' => 'from',
            ],
            [
                'key' => 'field_sk_hero_label_to',
                'label' => '"To" Label',
                'name' => 'sk_hero_label_to',
                'type' => 'text',
                'default_value' => 'to',
            ],
            [
                'key' => 'field_sk_hero_cta1_text',
                'label' => 'CTA 1 Text',
                'name' => 'sk_hero_cta1_text',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_hero_cta1_url',
                'label' => 'CTA 1 URL',
                'name' => 'sk_hero_cta1_url',
                'type' => 'text',
                'default_value' => '/#contact',
            ],
            [
                'key' => 'field_sk_hero_cta2_text',
                'label' => 'CTA 2 Text',
                'name' => 'sk_hero_cta2_text',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_hero_cta2_url',
                'label' => 'CTA 2 URL',
                'name' => 'sk_hero_cta2_url',
                'type' => 'text',
                'default_value' => '/#contact',
            ],
            [
                'key' => 'field_sk_hero_bg_image',
                'label' => 'Background Image URL',
                'name' => 'sk_hero_bg_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_hero_bg_image_mobile',
                'label' => 'Background Image URL (Mobile)',
                'name' => 'sk_hero_bg_image_mobile',
                'type' => 'text',
            ],

            [
                'key' => 'field_sk_hero_bg_video',
                'label' => 'Background Video URL (MP4)',
                'name' => 'sk_hero_bg_video',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_hero_pairs',
                'label' => 'Transformation Pairs',
                'name' => 'sk_hero_pairs',
                'type' => 'repeater',
                'layout' => 'table',
                'sub_fields' => [
                    [
                        'key' => 'field_sk_hero_pair_from',
                        'label' => 'From (Struggle)',
                        'name' => 'from',
                        'type' => 'text',
                        'required' => 1,
                    ],
                    [
                        'key' => 'field_sk_hero_pair_to',
                        'label' => 'To (Transformation)',
                        'name' => 'to',
                        'type' => 'text',
                        'required' => 1,
                    ],
                ],
            ],

            // Tab: About
            [
                'key' => 'field_sk_tab_about',
                'label' => 'About Section',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_about_eyebrow',
                'label' => 'Eyebrow',
                'name' => 'sk_about_eyebrow',
                'type' => 'text',
                'default_value' => 'Who We Are',
            ],
            [
                'key' => 'field_sk_about_tagline',
                'label' => 'Tagline',
                'name' => 'sk_about_tagline',
                'type' => 'text',
                'default_value' => 'Exploring Your Inner Journey',
            ],
            [
                'key' => 'field_sk_about_org_descriptor',
                'label' => 'Organisation Descriptor',
                'name' => 'sk_about_org_descriptor',
                'type' => 'text',
                'default_value' => 'An Organisation for Consciousness and Transformation',
            ],
            [
                'key' => 'field_sk_about_heading',
                'label' => 'Section Heading',
                'name' => 'sk_about_heading',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_about_bridge_copy',
                'label' => 'Main Body Copy',
                'name' => 'sk_about_bridge_copy',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'We bridge ancient wisdom and modern living through Vedic Astrology, Meditative Journeys, and Events on Well-being',
            ],
            [
                'key' => 'field_sk_about_body',
                'label' => 'Supporting Paragraph',
                'name' => 'sk_about_body',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'key' => 'field_sk_about_welcome_strip',
                'label' => 'Welcome Line',
                'name' => 'sk_about_welcome_strip',
                'type' => 'text',
                'default_value' => 'Welcome to Sacred Kompass where your next chapter begins',
            ],
            [
                'key' => 'field_sk_about_brand_bg_image',
                'label' => 'Brand Name Background Image URL',
                'name' => 'sk_about_brand_bg_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_about_bg_image',
                'label' => 'Section Background Image URL',
                'name' => 'sk_about_bg_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_about_bg_image_mobile',
                'label' => 'Section Background Image URL (Mobile)',
                'name' => 'sk_about_bg_image_mobile',
                'type' => 'text',
            ],


            // Tab: Philosophy Strip
            [
                'key' => 'field_sk_tab_philosophy',
                'label' => 'Philosophy Strip',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_philosophy_heading',
                'label' => 'Heading',
                'name' => 'sk_philosophy_heading',
                'type' => 'text',
                'default_value' => 'How We Work',
            ],
            [
                'key' => 'field_sk_philosophy_heading_em',
                'label' => 'Heading Italic',
                'name' => 'sk_philosophy_heading_em',
                'type' => 'text',
                'default_value' => 'With You',
            ],
            [
                'key' => 'field_sk_philosophy_intro',
                'label' => 'Intro Paragraph',
                'name' => 'sk_philosophy_intro',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'Every pathway begins with a single question: what is ready to be seen? These are the lenses we bring.',
            ],
            [
                'key' => 'field_sk_philosophy_bg_image',
                'label' => 'Background Image URL',
                'name' => 'sk_philosophy_bg_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_philosophy_bg_image_mobile',
                'label' => 'Background Image URL (Mobile)',
                'name' => 'sk_philosophy_bg_image_mobile',
                'type' => 'text',
            ],

            [
                'key' => 'field_sk_philosophy_bg_video',
                'label' => 'Background Video URL',
                'name' => 'sk_philosophy_bg_video',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_philosophy_title_image',
                'label' => 'Vedic Astrology Title Image URL',
                'name' => 'sk_philosophy_title_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_philosophy_pillars',
                'label' => 'Pillars',
                'name' => 'sk_philosophy_pillars',
                'type' => 'repeater',
                'sub_fields' => [
                    [
                        'key' => 'field_sk_pillar_num',
                        'label' => 'Number',
                        'name' => 'pillar_num',
                        'type' => 'text',
                        'required' => 1,
                    ],
                    [
                        'key' => 'field_sk_pillar_title',
                        'label' => 'Title',
                        'name' => 'pillar_title',
                        'type' => 'text',
                        'required' => 1,
                    ],
                    [
                        'key' => 'field_sk_pillar_desc',
                        'label' => 'Description',
                        'name' => 'pillar_desc',
                        'type' => 'textarea',
                        'rows' => 2,
                    ],
                    [
                        'key' => 'field_sk_pillar_image',
                        'label' => 'Image URL',
                        'name' => 'pillar_image',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_sk_pillar_img_position',
                        'label' => 'Image Position (CSS)',
                        'name' => 'pillar_img_position',
                        'type' => 'text',
                        'placeholder' => 'e.g. center top, center 20%',
                    ],
                ],
            ],

            // Tab: Quote Band
            [
                'key' => 'field_sk_tab_quote',
                'label' => 'Quote Band',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_quote_eyebrow',
                'label' => 'Eyebrow',
                'name' => 'sk_quote_eyebrow',
                'type' => 'text',
                'default_value' => 'Our Vision',
            ],
            [
                'key' => 'field_sk_quote_impact_phrase',
                'label' => 'Impact Phrase',
                'name' => 'sk_quote_impact_phrase',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_quote_text',
                'label' => 'Quote',
                'name' => 'sk_quote_text',
                'type' => 'textarea',
                'rows' => 4,
            ],
            [
                'key' => 'field_sk_quote_highlight',
                'label' => 'Highlight Phrase',
                'name' => 'sk_quote_highlight',
                'type' => 'text',
                'default_value' => 'inner compass',
            ],
            [
                'key' => 'field_sk_quote_attr',
                'label' => 'Attribution',
                'name' => 'sk_quote_attr',
                'type' => 'text',
            ],

            // Tab: Founders
            [
                'key' => 'field_sk_tab_founders',
                'label' => 'Founders',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_founders_eyebrow',
                'label' => 'Eyebrow',
                'name' => 'sk_founders_eyebrow',
                'type' => 'text',
                'default_value' => 'The Founders',
            ],
            [
                'key' => 'field_sk_founders_heading',
                'label' => 'Heading',
                'name' => 'sk_founders_heading',
                'type' => 'text',
                'default_value' => 'The Guides Behind',
            ],
            [
                'key' => 'field_sk_founders_heading_em',
                'label' => 'Heading Italic',
                'name' => 'sk_founders_heading_em',
                'type' => 'text',
                'default_value' => 'Sacred Kompass',
            ],
            [
                'key' => 'field_sk_founders_sub',
                'label' => 'Sub-text',
                'name' => 'sk_founders_sub',
                'type' => 'textarea',
                'rows' => 2,
            ],
            [
                'key' => 'field_sk_founders_body',
                'label' => 'Editorial Body',
                'name' => 'sk_founders_body',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'From Vedic philosophy and sacred feminine wisdom to conscious leadership and non-violent communication — every guide brings a living practice, not just a credential.',
            ],
            [
                'key' => 'field_sk_founders_team_image',
                'label' => 'Team Card Image URL',
                'name' => 'sk_founders_team_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_founders_team_title',
                'label' => 'Team Card Title',
                'name' => 'sk_founders_team_title',
                'type' => 'text',
                'default_value' => 'Our Team',
            ],
            [
                'key' => 'field_sk_founders_team_subtitle',
                'label' => 'Team Card Subtitle',
                'name' => 'sk_founders_team_subtitle',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_founders_bg_image',
                'label' => 'Section Background Image URL',
                'name' => 'sk_founders_bg_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_founders_bg_image_mobile',
                'label' => 'Section Background Image URL (Mobile)',
                'name' => 'sk_founders_bg_image_mobile',
                'type' => 'text',
            ],


            // Tab: Art for Peace
            [
                'key' => 'field_sk_tab_art',
                'label' => 'Art for Peace Info',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_art_eyebrow',
                'label' => 'Eyebrow',
                'name' => 'sk_art_eyebrow',
                'type' => 'text',
                'default_value' => 'Art for Peace',
            ],
            [
                'key' => 'field_sk_art_heading',
                'label' => 'Heading',
                'name' => 'sk_art_heading',
                'type' => 'text',
                'default_value' => 'Exhibition of',
            ],
            [
                'key' => 'field_sk_art_heading_em',
                'label' => 'Heading Italic',
                'name' => 'sk_art_heading_em',
                'type' => 'text',
                'default_value' => 'Healing',
            ],
            [
                'key' => 'field_sk_art_sub',
                'label' => 'Sub-description',
                'name' => 'sk_art_sub',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'Artworks created as part of therapeutic healing, mindfulness and transformation. Explore our active collections below.',
            ],
            [
                'key' => 'field_sk_art_cta_url',
                'label' => 'CTA URL',
                'name' => 'sk_art_cta_url',
                'type' => 'text',
                'default_value' => '/#contact',
            ],
            [
                'key' => 'field_sk_art_bg_image',
                'label' => 'Section Background Image URL',
                'name' => 'sk_art_bg_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_art_bg_image_mobile',
                'label' => 'Section Background Image URL (Mobile)',
                'name' => 'sk_art_bg_image_mobile',
                'type' => 'text',
            ],


            // Tab: Contact
            [
                'key' => 'field_sk_tab_contact',
                'label' => 'Contact/CTA',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_cta_eyebrow',
                'label' => 'Eyebrow',
                'name' => 'sk_cta_eyebrow',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_cta_heading',
                'label' => 'Heading (HTML allowed)',
                'name' => 'sk_cta_heading',
                'type' => 'textarea',
                'rows' => 2,
            ],
            [
                'key' => 'field_sk_cta_default_heading_l1',
                'label' => 'Default Heading — Line 1',
                'name' => 'sk_cta_default_heading_l1',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_cta_default_heading_l2',
                'label' => 'Default Heading — Line 2',
                'name' => 'sk_cta_default_heading_l2',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_cta_default_heading_em',
                'label' => 'Default Heading — Italic',
                'name' => 'sk_cta_default_heading_em',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_cta_sub',
                'label' => 'Sub-text',
                'name' => 'sk_cta_sub',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'key' => 'field_sk_cta_card_eyebrow',
                'label' => 'Form Card Eyebrow',
                'name' => 'sk_cta_card_eyebrow',
                'type' => 'text',
                'default_value' => 'Connect',
            ],
            [
                'key' => 'field_sk_cta_card_subheading_1',
                'label' => 'Form Card Sub-heading',
                'name' => 'sk_cta_card_subheading_1',
                'type' => 'text',
                'default_value' => 'Begin a',
            ],
            [
                'key' => 'field_sk_cta_card_subheading_em',
                'label' => 'Form Card Sub-heading Italic',
                'name' => 'sk_cta_card_subheading_em',
                'type' => 'text',
                'default_value' => 'Conversation',
            ],
            [
                'key' => 'field_sk_cta_ff_name_label',
                'label' => 'Fallback Name Label',
                'name' => 'sk_cta_ff_name_label',
                'type' => 'text',
                'default_value' => 'Your Name',
            ],
            [
                'key' => 'field_sk_cta_ff_email_label',
                'label' => 'Fallback Email Label',
                'name' => 'sk_cta_ff_email_label',
                'type' => 'text',
                'default_value' => 'Email Address',
            ],
            [
                'key' => 'field_sk_cta_ff_msg_label',
                'label' => 'Fallback Message Label',
                'name' => 'sk_cta_ff_msg_label',
                'type' => 'text',
                'default_value' => 'Your Message',
            ],
            [
                'key' => 'field_sk_cta_ff_submit_label',
                'label' => 'Fallback Submit Label',
                'name' => 'sk_cta_ff_submit_label',
                'type' => 'text',
                'default_value' => 'Send',
            ],
            [
                'key' => 'field_sk_cta_ff_note',
                'label' => 'Fallback Response Note',
                'name' => 'sk_cta_ff_note',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_cta_bg_image',
                'label' => 'Background Image URL',
                'name' => 'sk_cta_bg_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_cta_bg_image_mobile',
                'label' => 'Background Image URL (Mobile)',
                'name' => 'sk_cta_bg_image_mobile',
                'type' => 'text',
            ],

            [
                'key' => 'field_sk_forminator_form_id',
                'label' => 'Forminator Form ID',
                'name' => 'sk_forminator_form_id',
                'type' => 'text',
                'default_value' => '412',
            ],
            [
                'key' => 'field_sk_webhook_url',
                'label' => 'GSheets Webhook URL',
                'name' => 'sk_webhook_url',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_webhook_token',
                'label' => 'Webhook Verification Token',
                'name' => 'sk_webhook_token',
                'type' => 'text',
            ],

            // Tab: Kerala Pujas
            [
                'key' => 'field_sk_tab_pujas',
                'label' => 'Kerala Pujas',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_pujas_eyebrow',
                'label' => 'Eyebrow',
                'name' => 'sk_pujas_eyebrow',
                'type' => 'text',
                'default_value' => 'Next Step... The Solution',
            ],
            [
                'key' => 'field_sk_pujas_heading_em',
                'label' => 'Heading Italic (Sri Vidya Tradition)',
                'name' => 'sk_pujas_heading_em',
                'type' => 'text',
                'default_value' => 'Sri Vidya Tradition',
            ],
            [
                'key' => 'field_sk_pujas_gold_image',
                'label' => 'Gold Texture Image URL (for Heading Italic)',
                'name' => 'sk_pujas_gold_image',
                'type' => 'text',
                'default_value' => '',
            ],
            [
                'key' => 'field_sk_pujas_scroll_image',
                'label' => 'Scroll Paper Image URL',
                'name' => 'sk_pujas_scroll_image',
                'type' => 'text',
                'default_value' => 'https://sacredkompass.org/wp-content/uploads/2026/06/Scroll-paper.webp',
            ],
            [
                'key' => 'field_sk_pujas_intro_text',
                'label' => 'Intro Text (Card 1)',
                'name' => 'sk_pujas_intro_text',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'Kerala Tantric Pujas of the Sri Vidya tradition are sacred rituals rooted in temple, mantra and Devi worship practices passed down through traditional lineages. These rites work with mantra, yantra, flame, and divine invocation to restore balance, protection, clarity and spiritual alignment.',
            ],
            [
                'key' => 'field_sk_pujas_callout_text',
                'label' => 'Callout Text (Card 2)',
                'name' => 'sk_pujas_callout_text',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'In the Sri Vidya tradition, rituals are performed with deep reverence to the Divine Feminine and are believed to soften karmic influences, strengthen spiritual energy and bring harmony into one’s life journey. At Sacred Kompass, these practices are conducted with sincerity, traditional understanding and spiritual care.',
            ],
            [
                'key' => 'field_sk_pujas_cta_text',
                'label' => 'Primary CTA Text',
                'name' => 'sk_pujas_cta_text',
                'type' => 'text',
                'default_value' => 'Enquire about a puja',
            ],
            [
                'key' => 'field_sk_pujas_cta_url',
                'label' => 'Primary CTA URL',
                'name' => 'sk_pujas_cta_url',
                'type' => 'text',
                'default_value' => '/#contact',
            ],
            [
                'key' => 'field_sk_pujas_cta2_text',
                'label' => 'Secondary CTA Text',
                'name' => 'sk_pujas_cta2_text',
                'type' => 'text',
                'default_value' => 'Learn how it works',
            ],
            [
                'key' => 'field_sk_pujas_cta2_url',
                'label' => 'Secondary CTA URL',
                'name' => 'sk_pujas_cta2_url',
                'type' => 'text',
                'default_value' => '/#contact',
            ],

            // Tab: Stories Preview
            [
                'key' => 'field_sk_tab_stories_preview',
                'label' => 'Stories Preview',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_stories_preview_eyebrow',
                'label' => 'Eyebrow',
                'name' => 'sk_stories_preview_eyebrow',
                'type' => 'text',
                'default_value' => 'REAL JOURNEYS.  REAL TRANSFORMATION.',
            ],
            [
                'key' => 'field_sk_stories_preview_heading',
                'label' => 'Heading',
                'name' => 'sk_stories_preview_heading',
                'type' => 'text',
                'default_value' => 'Stories for the <em> Soul </em>',
            ],
            [
                'key' => 'field_sk_stories_preview_sub',
                'label' => 'Description Sub-text',
                'name' => 'sk_stories_preview_sub',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'Some stories arrive as medicine. They carry wisdom of lived experience, which can awaken courage in another.',
            ],
            [
                'key' => 'field_sk_stories_preview_bg_image',
                'label' => 'Background Image URL',
                'name' => 'sk_stories_preview_bg_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_stories_preview_bg_image_mobile',
                'label' => 'Background Image URL (Mobile)',
                'name' => 'sk_stories_preview_bg_image_mobile',
                'type' => 'text',
            ],


            // Tab: Journal Preview
            [
                'key' => 'field_sk_tab_journal_preview',
                'label' => 'Journal Preview',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_journal_preview_eyebrow',
                'label' => 'Eyebrow',
                'name' => 'sk_journal_preview_eyebrow',
                'type' => 'text',
                'default_value' => 'Journal',
            ],
            [
                'key' => 'field_sk_journal_preview_heading',
                'label' => 'Heading',
                'name' => 'sk_journal_preview_heading',
                'type' => 'text',
                'default_value' => 'From the Journal',
            ],
            [
                'key' => 'field_sk_journal_preview_see_all',
                'label' => 'See All Link Label',
                'name' => 'sk_journal_preview_see_all',
                'type' => 'text',
                'default_value' => 'See all posts',
            ],

            // Tab: FAQ Section
            [
                'key' => 'field_sk_tab_faq_section',
                'label' => 'FAQ Section',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_faq_heading_1',
                'label' => 'Heading Line 1',
                'name' => 'sk_faq_heading_1',
                'type' => 'text',
                'default_value' => 'Frequently',
            ],
            [
                'key' => 'field_sk_faq_heading_em',
                'label' => 'Heading Italic',
                'name' => 'sk_faq_heading_em',
                'type' => 'text',
                'default_value' => 'Asked',
            ],
            [
                'key' => 'field_sk_faq_sub',
                'label' => 'Description Sub-text',
                'name' => 'sk_faq_sub',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'If you have more questions, we warmly invite you to reach out. Every journey begins with a conversation.',
            ],
            [
                'key' => 'field_sk_faq_cta_label',
                'label' => 'CTA Button Label',
                'name' => 'sk_faq_cta_label',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_faq_bg_image',
                'label' => 'Background Image URL',
                'name' => 'sk_faq_bg_image',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_faq_bg_image_mobile',
                'label' => 'Background Image URL (Mobile)',
                'name' => 'sk_faq_bg_image_mobile',
                'type' => 'text',
            ],


            // Tab: Footer
            [
                'key' => 'field_sk_tab_footer',
                'label' => 'Footer',
                'type' => 'tab',
            ],
            [
                'key' => 'field_sk_footer_email',
                'label' => 'Email Address',
                'name' => 'sk_footer_email',
                'type' => 'text',
                'default_value' => 'collective@sacredkompass.org',
            ],
            [
                'key' => 'field_sk_footer_phone',
                'label' => 'Phone Number',
                'name' => 'sk_footer_phone',
                'type' => 'text',
                'default_value' => '+65 84343915',
            ],
            [
                'key' => 'field_sk_footer_address',
                'label' => 'Office Address',
                'name' => 'sk_footer_address',
                'type' => 'text',
                'default_value' => '557 Bedok North St. 3, Singapore',
            ],
            [
                'key' => 'field_sk_footer_tagline',
                'label' => 'Tagline',
                'name' => 'sk_footer_tagline',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'Ancient wisdom for the modern soul.',
            ],
            [
                'key' => 'field_sk_footer_copyright',
                'label' => 'Copyright Text',
                'name' => 'sk_footer_copyright',
                'type' => 'text',
                'default_value' => 'Sacred Kompass Collective · Singapore',
            ],
            [
                'key' => 'field_sk_newsletter_disclaimer',
                'label' => 'Newsletter Disclaimer',
                'name' => 'sk_newsletter_disclaimer',
                'type' => 'text',
                'default_value' => 'Sacred Kompass respects your privacy. Unsubscribe anytime.',
            ],
            [
                'key' => 'field_sk_footer_location_bar',
                'label' => 'Location Bar (Bottom)',
                'name' => 'sk_footer_location_bar',
                'type' => 'text',
                'default_value' => 'Bedok North, Singapore &nbsp;&middot;&nbsp; Online Worldwide',
            ],
            [
                'key' => 'field_sk_social_instagram',
                'label' => 'Instagram URL',
                'name' => 'sk_social_instagram',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_social_facebook',
                'label' => 'Facebook URL',
                'name' => 'sk_social_facebook',
                'type' => 'text',
            ],
            [
                'key' => 'field_sk_social_whatsapp',
                'label' => 'WhatsApp Link',
                'name' => 'sk_social_whatsapp',
                'type' => 'text',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'sk-settings',
                ],
            ],
        ],
    ]);

    // ── FIELD GROUP: ART FOR PEACE CPT ────────────────────────
    acf_add_local_field_group([
        'key' => 'group_sk_art_meta',
        'title' => '★ Artwork Details',
        'fields' => [
            [
                'key' => 'field_sk_art_tag',
                'label' => 'Theme Tag',
                'name' => 'art_tag',
                'type' => 'text',
                'placeholder' => 'e.g. Reflection · Healing · Peace · Transformation',
            ],
            [
                'key' => 'field_sk_art_desc',
                'label' => 'Therapeutic Meaning / Creation Story',
                'name' => 'art_desc',
                'type' => 'textarea',
                'rows' => 6,
            ],
            [
                'key' => 'field_sk_art_medium',
                'label' => 'Medium',
                'name' => 'art_medium',
                'type' => 'text',
                'placeholder' => 'e.g. Acrylic on linen',
            ],
            [
                'key' => 'field_sk_art_dimensions',
                'label' => 'Dimensions',
                'name' => 'art_dimensions',
                'type' => 'text',
                'placeholder' => 'e.g. 80 x 100 cm',
            ],
            [
                'key' => 'field_sk_art_price',
                'label' => 'Price / Value (optional)',
                'name' => 'art_price',
                'type' => 'text',
                'placeholder' => 'e.g. SGD 450',
            ],
            [
                'key' => 'field_sk_art_image_id',
                'label' => 'Artwork Image ID',
                'name' => 'art_image_id',
                'type' => 'text',
                'wrapper' => ['style' => 'display:none;'],
            ],
            [
                'key' => 'field_sk_art_image',
                'label' => 'Artwork Image URL',
                'name' => 'art_image',
                'type' => 'text',
                'wrapper' => ['style' => 'display:none;'],
            ],
            [
                'key' => 'field_sk_art_form_slug',
                'label' => 'Contact Form Slug',
                'name' => 'art_form_slug',
                'type' => 'text',
                'placeholder' => 'e.g. contact',
            ],
            [
                'key' => 'field_sk_art_cta_url',
                'label' => 'Custom CTA URL',
                'name' => 'art_cta_url',
                'type' => 'url',
                'placeholder' => 'https://...',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'sk_art',
                ],
            ],
        ],
    ]);

    // ── FIELD GROUP: LEGAL PAGES CPT ────────────────────────
    acf_add_local_field_group([
        'key' => 'group_sk_legal_meta',
        'title' => '★ Legal Details',
        'fields' => [
            [
                'key' => 'field_sk_legal_eyebrow',
                'label' => 'Eyebrow Text',
                'name' => 'legal_eyebrow',
                'type' => 'text',
                'default_value' => 'Sacred Kompass Collective',
            ],
            [
                'key' => 'field_sk_legal_effective_date',
                'label' => 'Effective Date',
                'name' => 'legal_effective_date',
                'type' => 'text',
                'default_value' => '24 March 2026',
            ],
            [
                'key' => 'field_sk_legal_location',
                'label' => 'Location',
                'name' => 'legal_location',
                'type' => 'text',
                'default_value' => 'Singapore',
            ],
            [
                'key' => 'field_sk_legal_content',
                'label' => 'Page Content',
                'name' => 'legal_content',
                'type' => 'wysiwyg',
                'toolbar' => 'full',
                'media_upload' => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'sk_legal',
                ],
            ],
        ],
    ]);

    // ── FIELD GROUP: FAQ CPT ────────────────────────
    acf_add_local_field_group([
        'key' => 'group_sk_faq_meta',
        'title' => '★ FAQ Details',
        'fields' => [
            [
                'key' => 'field_sk_faq_answer',
                'label' => 'Answer',
                'name' => 'faq_answer',
                'type' => 'textarea',
                'rows' => 4,
                'required' => 1,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'sk_faq',
                ],
            ],
        ],
    ]);

    // ── FIELD GROUP: TEAM CPT ────────────────────────
    acf_add_local_field_group([
        'key' => 'group_sk_team_meta',
        'title' => '★ Guide Details',
        'fields' => [
            [
                'key' => 'field_sk_team_first_name',
                'label' => 'First Name',
                'name' => 'team_first_name',
                'type' => 'text',
                'required' => 1,
            ],
            [
                'key' => 'field_sk_team_last_name',
                'label' => 'Last Name',
                'name' => 'team_last_name',
                'type' => 'text',
                'required' => 1,
            ],
            [
                'key' => 'field_sk_team_role',
                'label' => 'Role / Designation',
                'name' => 'team_role',
                'type' => 'text',
                'required' => 1,
            ],
            [
                'key' => 'field_sk_team_origin',
                'label' => 'Origin Location',
                'name' => 'team_origin',
                'type' => 'text',
                'placeholder' => 'e.g. Singapore / France',
            ],
            [
                'key' => 'field_sk_team_bio',
                'label' => 'Bio Description',
                'name' => 'team_bio',
                'type' => 'textarea',
                'rows' => 4,
            ],
            [
                'key' => 'field_sk_team_tags',
                'label' => 'Traditions / Focus Tags',
                'name' => 'team_tags',
                'type' => 'textarea',
                'rows' => 3,
                'placeholder' => 'One tag per line',
            ],
            [
                'key' => 'field_sk_team_is_founder',
                'label' => 'Is Founder?',
                'name' => 'team_is_founder',
                'type' => 'true_false',
                'ui' => 1,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'sk_team',
                ],
            ],
        ],
    ]);
}
