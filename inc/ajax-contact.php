<?php
/**
 * Sacred Kompass — Fallback Contact Form AJAX Handler & Database Schema
 */
defined('ABSPATH') || exit;

/**
 * Resolve the real client IP behind reverse proxies (Cloudflare, Nginx, LBs).
 *
 * Checks trusted proxy headers in priority order before falling back
 * to REMOTE_ADDR. Only accepts publicly routable IPs from headers to
 * prevent LAN-spoofing; falls back to REMOTE_ADDR if nothing validates.
 *
 * NOTE: For maximum security, prefer Cloudflare Turnstile or a honeypot
 * over IP-based rate limiting. Proxy headers can be spoofed if the
 * reverse proxy doesn't strip them.
 */
function sk_get_client_ip(): string {
    $candidates = [
        $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '',   // Cloudflare
        $_SERVER['HTTP_X_REAL_IP']        ?? '',   // Nginx proxy_set_header
        $_SERVER['HTTP_X_FORWARDED_FOR']  ?? '',   // Generic proxy (may be comma-list)
        $_SERVER['REMOTE_ADDR']           ?? '',
    ];
    foreach ( $candidates as $candidate ) {
        $ip = trim( explode( ',', $candidate )[0] );
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
            return $ip;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

add_action('wp_ajax_nopriv_sk_contact_submit','sk_handle_contact_submit');
add_action('wp_ajax_sk_contact_submit','sk_handle_contact_submit');
function sk_handle_contact_submit(): void {
    check_ajax_referer('sk_contact_nonce','nonce');
    if (!empty($_POST['website'])) wp_send_json_success(['msg'=>__('Your message has been received.','sacred-kompass')]);
    
    $ip = sk_get_client_ip();
    $rl = 'sk_rl_' . hash_hmac('sha1', $ip, wp_salt('nonce')); 
    $hits = (int) get_transient($rl);
    if ($hits >= 3) {
        wp_send_json_error(['msg'=>__("You've sent several messages recently. Please wait.","sacred-kompass")]);
    }
    set_transient($rl, $hits + 1, HOUR_IN_SECONDS);
    
    $fname   = sanitize_text_field(wp_unslash($_POST['fname'] ?? '')); 
    $lname   = sanitize_text_field(wp_unslash($_POST['lname'] ?? ''));
    $email   = sanitize_email(wp_unslash($_POST['email'] ?? '')); 
    $service = sanitize_text_field(wp_unslash($_POST['service'] ?? ''));
    $message = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));
    
    if (!$fname || !$email || !$service || !$message) {
        wp_send_json_error(['msg'=>__('Please fill in all required fields.','sacred-kompass')]);
    }
    if (!is_email($email)) {
        wp_send_json_error(['msg'=>__('Please enter a valid email address.','sacred-kompass')]);
    }
    
    global $wpdb;
    $prior = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}sk_contact_leads WHERE email=%s", $email));
    
    $wpdb->insert($wpdb->prefix.'sk_contact_leads', [
        'fname'      => $fname,
        'lname'      => $lname,
        'email'      => $email,
        'service'    => $service,
        'message'    => $message,
        'ip_hash'    => hash_hmac('sha1', $ip, wp_salt('nonce')),
        'created_at' => current_time('mysql')
    ], ['%s','%s','%s','%s','%s','%s','%s']);
    
    wp_mail(
        sk_option('footer_email', get_option('admin_email')),
        sprintf('[Sacred Kompass] New enquiry from %s %s', $fname, $lname),
        sprintf("Name: %s %s\nEmail: %s\nService: %s\n\nMessage:\n%s", $fname, $lname, $email, $service, $message),
        [
            'Content-Type: text/plain; charset=UTF-8',
            'Reply-To: ' . sanitize_email($email),
        ]
    );
    
    wp_send_json_success([
        'msg' => $prior > 0 
            ? __("Welcome back — thank you for reaching out again.",'sacred-kompass') 
            : __('Your message has been received. We will connect with you soon.','sacred-kompass')
    ]);
}

function sk_create_leads_table(): void {
    global $wpdb; 
    $table = $wpdb->prefix . 'sk_contact_leads'; 
    $charset = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta("CREATE TABLE IF NOT EXISTS {$table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        fname VARCHAR(100) NOT NULL DEFAULT '',
        lname VARCHAR(100) NOT NULL DEFAULT '',
        email VARCHAR(200) NOT NULL DEFAULT '',
        service VARCHAR(100) NOT NULL DEFAULT '',
        message TEXT NOT NULL,
        ip_hash VARCHAR(64) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY email (email),
        KEY created_at (created_at)
    ) {$charset};");
}
