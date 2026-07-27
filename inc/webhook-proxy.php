<?php
/**
 * Sacred Kompass — Forminator to Google Sheets Webhook Proxy
 *
 * Security hardening (v2):
 * - Requires a shared webhook_token (set in SK Settings) to authenticate callers.
 * - Rate-limited to 60 requests/minute per client IP.
 * - Sanitizes all forwarded field values.
 * - Validates the target URL is a Google Apps Script domain.
 */
defined('ABSPATH') || exit;

/* ── FORMINATOR → GOOGLE SHEETS WEBHOOK PROXY ── */
add_action('init', function(): void {
    if (!isset($_GET['gsheet_webhook'])) return;

    /* ── 1. Authenticate: require a shared secret token ── */
    $secret = sk_option('webhook_token', '');
    if (empty($secret)) {
        // No token configured — proxy is disabled for safety.
        http_response_code(503);
        echo json_encode(['status' => 'error', 'message' => 'Webhook proxy not configured.']);
        exit;
    }

    // Read raw body — Forminator sends JSON, not form fields
    $raw_body = file_get_contents('php://input');
    $decoded  = json_decode($raw_body, true);

    // Token may arrive via POST field or JSON body
    $caller_token = '';
    if (is_array($decoded) && isset($decoded['webhook_token'])) {
        $caller_token = $decoded['webhook_token'];
    } elseif (isset($_POST['webhook_token'])) {
        $caller_token = $_POST['webhook_token'];
    }

    if (!hash_equals($secret, (string) $caller_token)) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Forbidden.']);
        exit;
    }

    /* ── 2. Rate limiting: 60 requests/minute per IP ── */
    $ip     = function_exists('sk_get_client_ip') ? sk_get_client_ip() : ($_SERVER['REMOTE_ADDR'] ?? '');
    $rl_key = 'sk_webhook_rl_' . hash_hmac('sha1', $ip, wp_salt('nonce'));
    $hits   = (int) get_transient($rl_key);
    if ($hits >= 60) {
        http_response_code(429);
        echo json_encode(['status' => 'error', 'message' => 'Rate limit exceeded.']);
        exit;
    }
    set_transient($rl_key, $hits + 1, MINUTE_IN_SECONDS);

    /* ── 3. Validate target URL is a Google Apps Script endpoint ── */
    $google_script_url = sk_option('webhook_url', '');
    if (empty($google_script_url)) {
        http_response_code(503);
        echo json_encode(['status' => 'error', 'message' => 'Webhook URL not configured.']);
        exit;
    }
    $parsed_host = wp_parse_url($google_script_url, PHP_URL_HOST);
    $allowed_hosts = ['script.google.com', 'script.googleusercontent.com'];
    if (!$parsed_host || !in_array($parsed_host, $allowed_hosts, true)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid webhook target.']);
        exit;
    }

    /* ── 4. Build sanitized payload ── */
    $flat = [];
    if (is_array($decoded)) {
        foreach ($decoded as $key => $val) {
            if ($key === 'webhook_token') continue; // Don't forward the secret
            if (is_array($val) && isset($val['value'])) {
                $flat[sanitize_key($key)] = sanitize_text_field((string) $val['value']);
            } elseif (!is_array($val)) {
                $flat[sanitize_key($key)] = sanitize_text_field((string) $val);
            }
        }
    }

    // Append the token for Google Apps Script to verify origin
    $flat['webhook_token'] = $secret;
    $body         = http_build_query($flat);
    $content_type = 'application/x-www-form-urlencoded';

    wp_remote_post($google_script_url, [
        'body'    => $body,
        'timeout' => 15,
        'headers' => ['Content-Type' => $content_type],
    ]);

    echo json_encode(['status' => 'ok']);
    exit;
}, 5);

