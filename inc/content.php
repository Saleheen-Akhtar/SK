<?php
/**
 * Sacred Kompass — Default Content Seeder
 * Uses post_meta directly — no ACF required.
 */
defined('ABSPATH') || exit;

function sk_get_post_by_title(string $title, string $post_type): ?WP_Post {
    $q = new WP_Query([
        'post_type'              => $post_type,
        'title'                  => $title,
        'posts_per_page'         => 1,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'post_status'            => ['publish','draft','pending'],
    ]);
    return $q->have_posts() ? $q->posts[0] : null;
}

function sk_insert_default_content(): void {

    /* ── Art for Peace ── */
    $artworks = [
        [
            'title'      => 'Silence of the Self',
            'tag'        => 'Reflection',
            'desc'       => 'A visual study of stillness and pure consciousness. Drawn from internal meditative experiences, this artwork represents the transition from cognitive noise to the quiet expanse of the self.',
            'medium'     => 'Oil on canvas',
            'dimensions' => '90 x 120 cm',
            'price'      => '$1,200 SGD',
            'order'      => 1
        ],
        [
            'title'      => 'The Sacred Within',
            'tag'        => 'Healing',
            'desc'       => 'A circular motif celebrating the cyclical nature of energy, intuition, and creation. Warm earth tones meet gold leaf accents, reflecting the light of the inner sun.',
            'medium'     => 'Acrylic on raw linen',
            'dimensions' => '80 x 100 cm',
            'price'      => '$950 SGD',
            'order'      => 2
        ],
        [
            'title'      => 'Vedic Sky',
            'tag'        => 'Transformation',
            'desc'       => 'An abstract cartography of planetary alignments and cosmic order. Deep indigo and copper gradients evoke the vastness of Jyotish astronomy, offering a point of calm alignment for the viewer.',
            'medium'     => 'Mixed media on panel',
            'dimensions' => '100 x 100 cm',
            'price'      => '$1,500 SGD',
            'order'      => 3
        ]
    ];
    foreach ($artworks as $a) {
        if (sk_get_post_by_title($a['title'], 'sk_art')) continue;
        $id = wp_insert_post(['post_title'=>$a['title'],'post_type'=>'sk_art','post_status'=>'publish','menu_order'=>$a['order']]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'art_tag',        $a['tag']);
            update_post_meta($id, 'art_desc',       $a['desc']);
            update_post_meta($id, 'art_medium',     $a['medium']);
            update_post_meta($id, 'art_dimensions', $a['dimensions']);
            update_post_meta($id, 'art_price',      $a['price']);
            update_post_meta($id, 'art_image',      '');
        }
    }

    /* ── Founders (sk_team posts marked as founders) ── */
    $founders_seed = [
        [
            'title'      => 'Kalai Somoo',
            'first'      => 'Kalai',
            'last'       => 'Somoo',
            'origin'     => 'Singapore',
            'role'       => 'Founder and Lead Guide',
            'bio'        => "Kalai founded Sacred Kompass with a vision to reconnect people to their inner wisdom. With deep roots in Vedic philosophy, sacred feminine practices, Jyotish astrology, and women's empowerment, she guides individuals and organisations through transformative, inside-out growth.",
            'tags'       => "Women's Wellness\nVedic Philosophy\nJyotish Astrology\nSacred Feminine\nCoaching",
            'order'      => 1,
        ],
        [
            'title'      => 'Christophe Grigri',
            'first'      => 'Christophe',
            'last'       => 'Grigri',
            'origin'     => 'France',
            'role'       => 'International Coordination and Communication',
            'bio'        => "Christophe brings decades of international experience bridging cultures through compassionate dialogue and conscious leadership. Trained in Gandhian non-violence and NVC, he coordinates Sacred Kompass's global outreach and shapes the communicative heart of the collective.",
            'tags'       => "NVC\nGandhian Non-Violence\nInternational Coordination\nConscious Leadership",
            'order'      => 2,
        ],
    ];
    foreach ($founders_seed as $f) {
        if (sk_get_post_by_title($f['title'], 'sk_team')) continue;
        $id = wp_insert_post(['post_title'=>$f['title'],'post_type'=>'sk_team','post_status'=>'publish','menu_order'=>$f['order']]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'team_first_name', $f['first']);
            update_post_meta($id, 'team_last_name',  $f['last']);
            update_post_meta($id, 'team_origin',     $f['origin']);
            update_post_meta($id, 'team_role',       $f['role']);
            update_post_meta($id, 'team_bio',        $f['bio']);
            update_post_meta($id, 'team_tags',       $f['tags']);
            update_post_meta($id, 'team_is_founder', '1'); // marks as founder card
        }
    }

    /* ── FAQ ── */
    $faqs = [
        ['question'=>'What is Jyotish astrology?',                     'answer'=>"Jyotish is the ancient Vedic science of light — a system of astrology that predates Western traditions by thousands of years. It maps the soul's journey through planetary cycles, helping us understand our dharma, karmic patterns, and the auspicious timing of major life decisions.",'order'=>1],
        ['question'=>'Do I need prior experience to attend?',           'answer'=>'No experience is needed for any of our offerings. We welcome complete beginners alongside seasoned practitioners. Our guides meet you exactly where you are, with patience, warmth, and deep respect for your unique path.',                                                               'order'=>2],
        ['question'=>'Are sessions available online?',                  'answer'=>'Yes. Most private sessions are available online via video call. In-person sessions are held at our space in Bedok North, Singapore. Please contact us to confirm the format when booking.',                                                                                                   'order'=>3],
        ['question'=>'What is Nonviolent Communication (NVC)?',         'answer'=>"Developed by Marshall Rosenberg, NVC is a language of the heart — a framework for expressing ourselves honestly and listening to others with deep empathy. We use it as both a practical communication tool and a spiritual practice of compassion.",                                        'order'=>4],
        ['question'=>'How do I know which service is right for me?',    'answer'=>"We offer a free 20-minute discovery call to understand where you are and what you're seeking. From there, our team will lovingly suggest which offering, format, and guide feels most aligned with your current chapter of life.",                                                           'order'=>5],
        ['question'=>'Do you offer corporate or organisational programmes?','answer'=>'Yes. We design bespoke workshops and consulting engagements for teams and organisations seeking to integrate conscious leadership, emotional resilience, and compassionate culture. Please reach out directly to discuss your needs.',                                                    'order'=>6],
    ];
    foreach ($faqs as $f) {
        if (sk_get_post_by_title($f['question'], 'sk_faq')) continue;
        $id = wp_insert_post(['post_title'=>$f['question'],'post_type'=>'sk_faq','post_status'=>'publish','menu_order'=>$f['order']]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'faq_answer', $f['answer']);
        }
    }
    /* ── Legal Pages (sk_legal CPT) ── */
    $email = sk_option('footer_email', 'collective@sacredkompass.org');
    $phone = sk_option('footer_phone', '+65 84343915');
    $phone_clean = preg_replace('/[^+0-9]/', '', $phone);
    $address = sk_option('footer_address', '557 Bedok North St. 3, Singapore');
    $domain = esc_html(str_replace(['https://', 'http://', 'www.'], '', home_url()));

    $legal_pages = [
        [
            'title' => 'Privacy Policy',
            'slug'  => 'privacy-policy',
            'eyebrow' => 'Sacred Kompass Collective',
            'effective_date' => '24 March 2026',
            'location' => 'Singapore',
            'content' => '<p>Sacred Kompass Collective (\"we\", \"our\", or \"us\") is committed to protecting your personal data in accordance with the Singapore Personal Data Protection Act 2012 (PDPA). This Privacy Policy explains how we collect, use, disclose, and protect your personal information when you visit ' . $domain . ' or engage with our services.</p>

  <h2>1. Data We Collect</h2>
  <p>When you submit our contact form or enquire about our services, we may collect your full name, email address, phone number (if provided voluntarily), message content, and any other information you choose to share. We may also automatically collect standard website usage data such as IP address, browser type, and pages visited via analytics tools.</p>

  <h2>2. How We Use Your Data</h2>
  <p>We use the personal data you provide solely to respond to your enquiries and schedule discovery calls, deliver the services you have engaged us for, send relevant updates or follow-up communications (with your consent), and improve our website and service offerings. We do not sell, rent, or trade your personal data to any third parties.</p>

  <h2>3. Data Storage</h2>
  <p>Your contact form submissions are processed via Forminator (a WordPress plugin) and may be saved to a secured Google Sheet accessible only to the Sacred Kompass team. Data is retained only as long as necessary to fulfil the purpose for which it was collected, or as required by law.</p>

  <h2>4. Cookies &amp; Analytics</h2>
  <p>Our website may use cookies and third-party analytics tools (such as Google Analytics) to understand how visitors engage with our content. You may disable cookies through your browser settings. By continuing to use the site with cookies enabled, you consent to their use.</p>

  <h2>5. Your Rights Under PDPA</h2>
  <p>Under the Singapore PDPA, you have the right to request access to the personal data we hold about you, request correction of any inaccurate personal data, and withdraw your consent to our use of your data at any time. To exercise any of these rights, please contact us at <a href=\"mailto:' . esc_attr($email) . '\">' . esc_html($email) . '</a>.</p>

  <h2>6. Third-Party Links</h2>
  <p>Our website may contain links to external websites. We are not responsible for the privacy practices or content of those sites. We encourage you to review their privacy policies independently.</p>

  <h2>7. Contact Us</h2>
  <p><strong>Sacred Kompass Collective</strong><br>Email: <a href=\"mailto:' . esc_attr($email) . '\">' . esc_html($email) . '</a><br>Phone: <a href=\"tel:' . esc_attr($phone_clean) . '\">' . esc_html($phone) . '</a><br>' . esc_html($address) . '</p>

  <div class=\"legal-note\"><p>This Privacy Policy may be updated from time to time. The most current version will always be available at ' . $domain . '/privacy-policy/.</p></div>',
        ],
        [
            'title' => 'Terms of Use',
            'slug'  => 'terms',
            'eyebrow' => 'Sacred Kompass Collective',
            'effective_date' => '24 March 2026',
            'location' => 'Singapore',
            'content' => '<p>Welcome to ' . $domain . '. By accessing or using this website, you agree to be bound by the following Terms of Use. Please read them carefully before proceeding. If you do not agree to these terms, please discontinue use of the site.</p>

  <h2>1. Acceptance of Terms</h2>
  <p>These Terms of Use govern your access to and use of the Sacred Kompass Collective website and any services, content, or information made available through it. They apply to all visitors, users, and others who access the site.</p>

  <h2>2. Nature of Services</h2>
  <p>Sacred Kompass Collective offers wellness consultancy, coaching, Vedic astrology (Jyotish), Nonviolent Communication (NVC) facilitation, meditation guidance, and women\'s empowerment programmes. Our offerings are rooted in ancient wisdom traditions and are intended to support personal growth, self-awareness, and inner clarity. Our services are not a substitute for professional medical, psychological, legal, or financial advice.</p>

  <h2>3. Intellectual Property</h2>
  <p>All content on this website — including text, images, graphics, logos, and design — is the property of Sacred Kompass Collective and is protected by applicable intellectual property laws. You may not reproduce, distribute, or create derivative works from any content on this site without our prior written permission.</p>

  <h2>4. Governing Law</h2>
  <p>These Terms of Use shall be governed by and construed in accordance with the laws of the Republic of Singapore. Any disputes arising from or in connection with these terms shall be subject to the exclusive jurisdiction of the courts of Singapore.</p>

  <h2>5. Contact</h2>
  <p><strong>Sacred Kompass Collective</strong><br>Email: <a href=\"mailto:' . esc_attr($email) . '\">' . esc_html($email) . '</a><br>Phone: <a href=\"tel:' . esc_attr($phone_clean) . '\">' . esc_html($phone) . '</a><br>' . esc_html($address) . '</p>

  <div class=\"legal-note\"><p>These Terms of Use may be updated from time to time. The most current version will always be available at ' . $domain . '/terms-of-use/.</p></div>',
        ],
        [
            'title' => 'Disclaimer',
            'slug'  => 'disclaimer',
            'eyebrow' => 'Sacred Kompass Collective',
            'effective_date' => '24 March 2026',
            'location' => 'Singapore',
            'content' => '<h2>Wellness &amp; Holistic Services Disclaimer</h2>
  <p>The information, sessions, programmes, and guidance offered by Sacred Kompass Collective — including Vedic Jyotish astrology, meditation, breathwork, energy healing, Nonviolent Communication (NVC), women\'s wellness, and coaching — are intended for educational, self-development, and personal growth purposes only.</p>

  <h2>Not a Substitute for Professional Advice</h2>
  <p>The services provided by Sacred Kompass Collective are not a substitute for professional medical, psychological, psychiatric, legal, or financial advice. We strongly encourage you to seek appropriate licensed professionals for any medical, mental health, or legal concerns. If you are experiencing a medical emergency, a mental health crisis, or thoughts of self-harm, please contact emergency services or a qualified healthcare professional immediately.</p>

  <h2>Astrology &amp; Jyotish</h2>
  <p>Vedic Jyotish astrology is offered as a traditional system of wisdom and self-reflection. Astrological consultations and insights are based on ancient interpretive traditions and are meant to support your own reflection and decision-making — not as predictive guarantees. Individual outcomes may vary. You retain full responsibility for your own choices and actions.</p>

  <h2>Results &amp; Outcomes</h2>
  <p>Personal transformation and wellness results vary significantly from person to person. Sacred Kompass Collective makes no guarantees regarding specific outcomes, improvements, or results arising from engagement with our services, programmes, or content.</p>

  <h2>Contact</h2>
  <p><strong>Sacred Kompass Collective</strong><br>Email: <a href=\"mailto:' . esc_attr($email) . '\">' . esc_html($email) . '</a><br>Phone: <a href=\"tel:' . esc_attr($phone_clean) . '\">' . esc_html($phone) . '</a><br>' . esc_html($address) . '</p>

  <div class=\"legal-note\"><p>This Disclaimer may be updated from time to time. Continued use of ' . $domain . ' constitutes your acceptance of the current version.</p></div>',
        ],
    ];

    foreach ($legal_pages as $lp) {
        /* Check by slug (post_name) in sk_legal CPT */
        $existing_q = new WP_Query([
            'post_type'      => 'sk_legal',
            'post_status'    => ['publish','draft'],
            'posts_per_page' => 1,
            'name'           => $lp['slug'],
            'no_found_rows'  => true,
        ]);
        if ($existing_q->have_posts()) continue;

        $id = wp_insert_post([
            'post_title'  => $lp['title'],
            'post_name'   => $lp['slug'],
            'post_type'   => 'sk_legal',
            'post_status' => 'publish',
        ]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'legal_eyebrow',        $lp['eyebrow']);
            update_post_meta($id, 'legal_effective_date', $lp['effective_date']);
            update_post_meta($id, 'legal_location',       $lp['location']);
            update_post_meta($id, 'legal_content',        $lp['content']);
        }
    }

    /* ── Pujas (sk_puja CPT) ── */
    $pujas_seed = [
        [
            'title' => 'Prosperity & abundance',
            'num'   => '01',
            'desc'  => 'Invoking Lakshmi and Sri Vidya energies to align your work with the cosmic flow, dissolving financial obstacles and cultivating spiritual and material wealth.',
            'order' => 1,
        ],
        [
            'title' => 'Protection & cleansing',
            'num'   => '02',
            'desc'  => 'Traditional protective rites working with flame and mantra to clear negative energy fields, purify your home/aura, and create a resilient seal of spiritual boundaries.',
            'order' => 2,
        ],
        [
            'title' => 'Removal of obstacles',
            'num'   => '03',
            'desc'  => 'Dedicated Ganesh and Devi worship designed to soften deep-seated karmic friction, clearing the path for new projects, clarity, and ease in transitions.',
            'order' => 3,
        ],
        [
            'title' => 'Relationship & family harmony',
            'num'   => '04',
            'desc'  => 'Bringing divine balance to domestic life by invocation of unifying energies, restoring communication, mutual respect, and warm compassion in families.',
            'order' => 4,
        ],
        [
            'title' => 'Business & life growth',
            'num'   => '05',
            'desc'  => 'Strategic spiritual alignment for leadership, corporate ventures, and personal missions—fostering strategic focus, authentic clarity, and sustainable expansion.',
            'order' => 5,
        ],
        [
            'title' => 'Spiritual awakening & transformation',
            'num'   => '06',
            'desc'  => 'The ultimate purpose of the Sri Vidya tradition. Working with deep mantra, Devi visualization, and yantra worship to awaken consciousness and your true self.',
            'order' => 6,
        ],
    ];
    if (post_type_exists('sk_puja')) {
        foreach ($pujas_seed as $p) {
            if (sk_get_post_by_title($p['title'], 'sk_puja')) continue;
            $id = wp_insert_post([
                'post_title'  => $p['title'],
                'post_type'   => 'sk_puja',
                'post_status' => 'publish',
                'menu_order'  => $p['order'],
            ]);
            if ($id && !is_wp_error($id)) {
                update_post_meta($id, 'puja_num',  $p['num']);
                update_post_meta($id, 'puja_desc', $p['desc']);
            }
        }
    }

    /* ── Navigation (sk_nav CPT) ── */
    $nav_seed = [
        [
            'title'     => 'About',
            'url'       => '/#about',
            'highlight' => 'none',
            'target'    => '_self',
            'desktop'   => '1',
            'mobile'    => '1',
            'icon'      => '',
            'order'     => 1,
        ],
        [
            'title'     => 'Art for Peace',
            'url'       => '/#art',
            'highlight' => 'none',
            'target'    => '_self',
            'desktop'   => '1',
            'mobile'    => '1',
            'icon'      => '',
            'order'     => 2,
        ],
        [
            'title'     => 'Journal',
            'url'       => '/#journal-preview',
            'highlight' => 'none',
            'target'    => '_self',
            'desktop'   => '1',
            'mobile'    => '1',
            'icon'      => '',
            'order'     => 3,
        ],
        [
            'title'     => 'FAQ',
            'url'       => '/#faq',
            'highlight' => 'none',
            'target'    => '_self',
            'desktop'   => '1',
            'mobile'    => '1',
            'icon'      => '',
            'order'     => 4,
        ],
        [
            'title'     => 'The Collective',
            'url'       => '/collective/',
            'highlight' => 'none',
            'target'    => '_self',
            'desktop'   => '1',
            'mobile'    => '1',
            'icon'      => '',
            'order'     => 5,
        ],
    ];
    foreach ($nav_seed as $n) {
        if (sk_get_post_by_title($n['title'], 'sk_nav')) continue;
        $id = wp_insert_post([
            'post_title'  => $n['title'],
            'post_type'   => 'sk_nav',
            'post_status' => 'publish',
            'menu_order'  => $n['order'],
        ]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'nav_url',          $n['url']);
            update_post_meta($id, 'nav_highlight',    $n['highlight']);
            update_post_meta($id, 'nav_target',       $n['target']);
            update_post_meta($id, 'nav_show_desktop', $n['desktop']);
            update_post_meta($id, 'nav_show_mobile',  $n['mobile']);
            update_post_meta($id, 'nav_icon',         $n['icon']);
        }
    }

}
