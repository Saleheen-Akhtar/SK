<?php
/**
 * Sacred Kompass — Custom Post Type: Legal Pages
 */
defined('ABSPATH') || exit;

add_action('init', 'sk_register_legal_cpt', 10);
function sk_register_legal_cpt(): void {
    register_post_type('sk_legal', [
        'labels' => [
            'name'          => 'Legal Pages',
            'singular_name' => 'Legal Page',
            'edit_item'     => 'Edit Legal Page',
            'menu_name'     => 'Legal Pages',
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => false,            // registered via sk_nest_legal_menu — prevents duplicate entry
        'supports'        => ['title'],
        'rewrite'         => false,
        'capability_type' => 'post',
        'has_archive'     => false,
        'show_in_rest'    => false,
        'menu_icon'       => 'dashicons-media-document',
    ]);
}

add_action('admin_menu', 'sk_nest_legal_menu', 100);
function sk_nest_legal_menu(): void {
    add_submenu_page(
        'sk-settings',
        'Legal Pages',
        '✦ Legal Pages',
        'edit_posts',
        'edit.php?post_type=sk_legal'
    );
}

add_action('add_meta_boxes', 'sk_register_legal_meta_boxes');
function sk_register_legal_meta_boxes(): void {
    add_meta_box('sk_legal_content', '★ Page Content (HTML allowed)', 'sk_legal_meta_box_cb',        'sk_legal', 'normal', 'high');
    add_meta_box('sk_legal_meta',    '★ Header Details',             'sk_legal_header_meta_box_cb', 'sk_legal', 'normal', 'high');
}

function sk_legal_header_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('sk_legal_header_save', 'sk_legal_header_nonce');
    $effective_date = get_post_meta($post->ID, 'legal_effective_date', true) ?: __('24 March 2026', 'sacred-kompass');
    $location       = get_post_meta($post->ID, 'legal_location',       true) ?: __('Singapore', 'sacred-kompass');
    $eyebrow        = get_post_meta($post->ID, 'legal_eyebrow',        true) ?: __('Sacred Kompass Collective', 'sacred-kompass');

    echo '<table class="form-table" style="width:100%">';
    echo '<tr><th style="width:180px"><label>Eyebrow Text</label></th><td><input type="text" name="legal_eyebrow" value="' . esc_attr($eyebrow) . '" style="width:100%" /></td></tr>';
    echo '<tr><th><label>Effective Date</label></th><td><input type="text" name="legal_effective_date" value="' . esc_attr($effective_date) . '" placeholder="e.g. 24 March 2026" style="width:100%" /></td></tr>';
    echo '<tr><th><label>Location</label></th><td><input type="text" name="legal_location" value="' . esc_attr($location) . '" placeholder="e.g. Singapore" style="width:100%" /></td></tr>';
    echo '</table>';
    echo '<p style="font-size:12px;color:#666;margin-top:8px">These appear in the page header banner.</p>';
}

function sk_legal_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('sk_legal_save', 'sk_legal_nonce');
    $content = get_post_meta($post->ID, 'legal_content', true) ?: '';

    echo '<p style="font-size:12px;color:#666;margin:0 0 10px">Use the editor below to write and format the page content. '
       . 'Switch to <strong>Text</strong> mode (top-right of editor) to paste or edit raw HTML. '
       . 'The <code>&lt;div class="legal-note"&gt;&lt;p&gt;…&lt;/p&gt;&lt;/div&gt;</code> wrapper creates the gold-bordered callout box at the bottom.</p>';

    wp_editor(
        $content,
        'legal_content',
        [
            'textarea_name' => 'legal_content',
            'textarea_rows' => 24,
            'media_buttons' => false,
            'teeny'         => false,
            'tinymce'       => [
                'toolbar1' => 'formatselect bold italic | bullist numlist | link unlink | undo redo',
                'toolbar2' => 'blockquote hr removeformat | pastetext',
                'block_formats' => 'Paragraph=p; Heading 2=h2; Heading 3=h3',
            ],
            'quicktags'     => ['buttons' => 'strong,em,link,ul,ol,li,close'],
        ]
    );
}

add_action('save_post_sk_legal', 'sk_save_legal_meta');
function sk_save_legal_meta(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['sk_legal_nonce']) && wp_verify_nonce($_POST['sk_legal_nonce'], 'sk_legal_save')) {
        $allowed_html = wp_kses_allowed_html('post');
        $content = wp_kses(stripslashes($_POST['legal_content'] ?? ''), $allowed_html);
        update_post_meta($post_id, 'legal_content', $content);
    }

    if (isset($_POST['sk_legal_header_nonce']) && wp_verify_nonce($_POST['sk_legal_header_nonce'], 'sk_legal_header_save')) {
        update_post_meta($post_id, 'legal_effective_date', sanitize_text_field($_POST['legal_effective_date'] ?? ''));
        update_post_meta($post_id, 'legal_location',       sanitize_text_field($_POST['legal_location']       ?? ''));
        update_post_meta($post_id, 'legal_eyebrow',        sanitize_text_field($_POST['legal_eyebrow']        ?? ''));
    }
}

/* ── Helper: read legal page content by slug (privacy-policy, terms, disclaimer) ── */
function sk_get_default_legal_content(string $slug): string {
    $email = sk_option('footer_email', 'collective@sacredkompass.org');
    $phone = sk_option('footer_phone', '+65 84343915');
    $phone_clean = preg_replace('/[^+0-9]/', '', $phone);
    $address = sk_option('footer_address', '557 Bedok North St. 3, Singapore');
    $domain = esc_html(str_replace(['https://', 'http://', 'www.'], '', home_url()));

    if ($slug === 'privacy-policy') {
        return '<p>Sacred Kompass Collective ("we", "our", or "us") is committed to protecting your personal data in accordance with the Singapore Personal Data Protection Act 2012 (PDPA). This Privacy Policy explains how we collect, use, disclose, and protect your personal information when you visit ' . $domain . ' or engage with our services.</p>

  <h2>1. Data We Collect</h2>
  <p>When you submit our contact form or enquire about our services, we may collect:</p>
  <ul>
    <li>Full name (family name and first name)</li>
    <li>Email address</li>
    <li>Phone number (if provided voluntarily)</li>
    <li>Message content and any other information you choose to share</li>
  </ul>
  <p>We may also automatically collect standard website usage data such as IP address, browser type, and pages visited via analytics tools.</p>

  <h2>2. How We Use Your Data</h2>
  <p>We use the personal data you provide solely to:</p>
  <ul>
    <li>Respond to your enquiries and schedule discovery calls</li>
    <li>Deliver the services you have engaged us for</li>
    <li>Send relevant updates or follow-up communications (with your consent)</li>
    <li>Improve our website and service offerings</li>
  </ul>
  <p>We do not sell, rent, or trade your personal data to any third parties.</p>

  <h2>3. Data Storage</h2>
  <p>Your contact form submissions are processed via Forminator (a WordPress plugin) and may be saved to a secured Google Sheet accessible only to the Sacred Kompass team. Data is retained only as long as necessary to fulfil the purpose for which it was collected, or as required by law.</p>

  <h2>4. Cookies &amp; Analytics</h2>
  <p>Our website may use cookies and third-party analytics tools (such as Google Analytics) to understand how visitors engage with our content. You may disable cookies through your browser settings. By continuing to use the site with cookies enabled, you consent to their use.</p>

  <h2>5. Your Rights Under PDPA</h2>
  <p>Under the Singapore PDPA, you have the right to:</p>
  <ul>
    <li>Request access to the personal data we hold about you</li>
    <li>Request correction of any inaccurate personal data</li>
    <li>Withdraw your consent to our use of your data at any time</li>
  </ul>
  <p>To exercise any of these rights, please contact us at <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>.</p>

  <h2>6. Third-Party Links</h2>
  <p>Our website may contain links to external websites. We are not responsible for the privacy practices or content of those sites. We encourage you to review their privacy policies independently.</p>

  <h2>7. Contact Us</h2>
  <p>
    <strong>Sacred Kompass Collective</strong><br>
    Email: <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a><br>
    Phone: <a href="tel:' . esc_attr($phone_clean) . '">' . esc_html($phone) . '</a><br>
    ' . esc_html($address) . '
  </p>

  <div class="legal-note">
    <p>This Privacy Policy may be updated from time to time. The most current version will always be available at ' . $domain . '/privacy-policy/.</p>
  </div>';
    }

    if ($slug === 'terms') {
        return '<p>Welcome to ' . $domain . '. By accessing or using this website, you agree to be bound by the following Terms of Use. Please read them carefully before proceeding. If you do not agree to these terms, please discontinue use of the site.</p>

  <h2>1. Acceptance of Terms</h2>
  <p>These Terms of Use govern your access to and use of the Sacred Kompass Collective website and any services, content, or information made available through it. They apply to all visitors, users, and others who access the site.</p>

  <h2>2. Nature of Services</h2>
  <p>Sacred Kompass Collective offers wellness consultancy, coaching, Vedic astrology (Jyotish), Nonviolent Communication (NVC) facilitation, meditation guidance, and women\'s empowerment programmes. Our offerings are rooted in ancient wisdom traditions and are intended to support personal growth, self-awareness, and inner clarity.</p>
  <p>Our services are not a substitute for professional medical, psychological, legal, or financial advice. If you are experiencing a medical or mental health emergency, please contact an appropriate healthcare professional immediately.</p>

  <h2>3. Intellectual Property</h2>
  <p>All content on this website — including text, images, graphics, logos, and design — is the property of Sacred Kompass Collective and is protected by applicable intellectual property laws. You may not reproduce, distribute, or create derivative works from any content on this site without our prior written permission.</p>

  <h2>4. Use of Website</h2>
  <p>By using this website, you agree to:</p>
  <ul>
    <li>Use the site only for lawful purposes</li>
    <li>Not attempt to gain unauthorised access to any part of the website or its systems</li>
    <li>Not transmit any harmful, offensive, or disruptive content through our contact form or communications</li>
    <li>Not use the site in any way that could damage or impair its availability or accessibility</li>
  </ul>

  <h2>5. Booking &amp; Payments</h2>
  <p>All session bookings are subject to our Booking &amp; Cancellation Policy, communicated at the time of engagement. Payment terms will be confirmed in writing prior to any paid engagement. Sacred Kompass Collective reserves the right to modify service offerings, pricing, and availability at any time.</p>

  <h2>6. Disclaimer of Warranties</h2>
  <p>This website and its content are provided on an "as is" and "as available" basis without warranties of any kind, either express or implied. We do not guarantee that the site will be error-free, uninterrupted, or free of viruses or other harmful components.</p>

  <h2>7. Limitation of Liability</h2>
  <p>To the fullest extent permitted by Singapore law, Sacred Kompass Collective shall not be liable for any direct, indirect, incidental, consequential, or punitive damages arising out of your access to or use of this website or our services.</p>

  <h2>8. Governing Law</h2>
  <p>These Terms of Use shall be governed by and construed in accordance with the laws of the Republic of Singapore. Any disputes arising from or in connection with these terms shall be subject to the exclusive jurisdiction of the courts of Singapore.</p>

  <h2>9. Changes to These Terms</h2>
  <p>We reserve the right to update or modify these Terms of Use at any time. Changes will be posted on this page with a revised effective date. Your continued use of the site after any modifications constitutes your acceptance of the updated terms.</p>

  <h2>10. Contact</h2>
  <p>
    <strong>Sacred Kompass Collective</strong><br>
    Email: <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a><br>
    Phone: <a href="tel:' . esc_attr($phone_clean) . '">' . esc_html($phone) . '</a><br>
    ' . esc_html($address) . '
  </p>

  <div class="legal-note">
    <p>These Terms of Use may be updated from time to time. The most current version will always be available at ' . $domain . '/terms-of-use/.</p>
  </div>';
    }

    if ($slug === 'disclaimer') {
        return '<h2>Wellness &amp; Holistic Services Disclaimer</h2>
  <p>The information, sessions, programmes, and guidance offered by Sacred Kompass Collective — including Vedic Jyotish astrology, meditation, breathwork, energy healing, Nonviolent Communication (NVC), women\'s wellness, and coaching — are intended for educational, self-development, and personal growth purposes only.</p>

  <h2>Not a Substitute for Professional Advice</h2>
  <p>The services provided by Sacred Kompass Collective are not a substitute for professional medical, psychological, psychiatric, legal, or financial advice. We strongly encourage you to seek appropriate licensed professionals for any medical, mental health, or legal concerns.</p>
  <p>If you are experiencing a medical emergency, a mental health crisis, or thoughts of self-harm, please contact emergency services or a qualified healthcare professional immediately.</p>

  <h2>Astrology &amp; Jyotish</h2>
  <p>Vedic Jyotish astrology is offered as a traditional system of wisdom and self-reflection. Astrological consultations and insights are based on ancient interpretive traditions and are meant to support your own reflection and decision-making — not as predictive guarantees. Individual outcomes may vary. You retain full responsibility for your own choices and actions.</p>

  <h2>Results &amp; Outcomes</h2>
  <p>Personal transformation and wellness results vary significantly from person to person. Sacred Kompass Collective makes no guarantees regarding specific outcomes, improvements, or results arising from engagement with our services, programmes, or content.</p>

  <h2>Third-Party Resources</h2>
  <p>This website may reference or link to third-party resources, books, articles, or practitioners for informational purposes. Such references do not constitute endorsements, and Sacred Kompass Collective is not responsible for the accuracy, content, or practices of any third-party resource.</p>

  <h2>Testimonials</h2>
  <p>Testimonials shared on this website reflect individual experiences and are not representative of all clients. Individual results will differ from person to person.</p>

  <h2>Contact</h2>
  <p>
    <strong>Sacred Kompass Collective</strong><br>
    Email: <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a><br>
    Phone: <a href="tel:' . esc_attr($phone_clean) . '">' . esc_html($phone) . '</a><br>
    ' . esc_html($address) . '
  </p>

  <div class="legal-note">
    <p>This Disclaimer may be updated from time to time. Continued use of ' . $domain . ' constitutes your acceptance of the current version.</p>
  </div>';
    }

    return '';
}

function sk_get_legal_page(string $slug): array {
    $q = new WP_Query([
        'post_type'      => 'sk_legal',
        'post_status'    => ['publish','draft'],
        'posts_per_page' => 1,
        'name'           => $slug,
        'no_found_rows'  => true,
    ]);
    if ($q->have_posts()) {
        $p = $q->posts[0];
        $content = get_post_meta($p->ID, 'legal_content', true);
        if (empty($content)) {
            $content = sk_get_default_legal_content($slug);
            update_post_meta($p->ID, 'legal_content', $content);
        }
        return [
            'eyebrow'        => get_post_meta($p->ID, 'legal_eyebrow',        true) ?: __('Sacred Kompass Collective', 'sacred-kompass'),
            'title'          => get_the_title($p),
            'effective_date' => get_post_meta($p->ID, 'legal_effective_date', true) ?: __('24 March 2026', 'sacred-kompass'),
            'location'       => get_post_meta($p->ID, 'legal_location',       true) ?: __('Singapore', 'sacred-kompass'),
            'content'        => $content,
        ];
    }

    $default_content = sk_get_default_legal_content($slug);
    if (!empty($default_content)) {
        $title_map = [
            'privacy-policy' => __('Privacy Policy', 'sacred-kompass'),
            'terms'          => __('Terms of Use', 'sacred-kompass'),
            'disclaimer'     => __('Disclaimer', 'sacred-kompass'),
        ];
        $title = $title_map[$slug] ?? ucfirst(str_replace('-', ' ', $slug));
        $id = wp_insert_post([
            'post_title'  => $title,
            'post_name'   => $slug,
            'post_type'   => 'sk_legal',
            'post_status' => 'publish',
        ]);
        if ($id && !is_wp_error($id)) {
            $eyebrow = __('Sacred Kompass Collective', 'sacred-kompass');
            $effective_date = __('24 March 2026', 'sacred-kompass');
            $location = __('Singapore', 'sacred-kompass');
            update_post_meta($id, 'legal_eyebrow',        $eyebrow);
            update_post_meta($id, 'legal_effective_date', $effective_date);
            update_post_meta($id, 'legal_location',       $location);
            update_post_meta($id, 'legal_content',        $default_content);
            return [
                'eyebrow'        => $eyebrow,
                'title'          => $title,
                'effective_date' => $effective_date,
                'location'       => $location,
                'content'        => $default_content,
            ];
        }
    }
    return [];
}
