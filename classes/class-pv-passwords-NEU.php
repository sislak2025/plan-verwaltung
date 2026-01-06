<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PV_Passwords')) {
    class PV_Passwords
    {
        /* === Konfiguration === */
        const CPT = 'passwords';
        const FIELDS = [
            'pv_p_live_passwort',
            'pv_p_entwicklung_passwort',
            'pv_p_ftp_passwort',
            'pv_p_hosting_passwort',
        ];
        const UNLOCK_COOKIE = 'sdpv_unlock';
        const UNLOCK_TTL    = 900; // 15 min
        const KEEP_SENTINEL = '__SDPV_KEEP__'; // „unverändert lassen“

        public function __construct()
        {
            foreach (self::FIELDS as $name) {
                add_filter("acf/update_value/name={$name}", [$this, 'acf_encrypt_on_update'], 10, 2);
                add_filter("acf/load_value/name={$name}",   [$this, 'acf_blank_on_load'], 10, 2);
                add_filter("acf/format_value/name={$name}", [$this, 'acf_mask_on_format'], 10, 3);

                add_filter("acf/prepare_field/name={$name}", [$this, 'acf_prepare_keep_sentinel'], 10, 1);
                add_action("acf/render_field/name={$name}",  [$this, 'acf_render_controls'], 20, 1);
            }

            add_action('wp_ajax_sdpv_master_unlock',        [$this, 'ajax_master_unlock']);
            add_action('wp_ajax_nopriv_sdpv_master_unlock', [$this, 'ajax_master_unlock']);
            add_action('wp_ajax_sdpv_reveal',               [$this, 'ajax_reveal']);
            add_action('wp_ajax_nopriv_sdpv_reveal',        [$this, 'ajax_reveal']);

            add_filter('register_post_type_args', [$this, 'harden_cpt'], 10, 2);
        }

        /* ===== deine bestehende Methode ===== */
        public function get_all_passwords()
        {
            $passwords = array();
            $args = array(
                'post_type'      => self::CPT,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'DESC'
            );
            $posts = get_posts($args);
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $password = array(
                        'ID'         => $post->ID,
                        'post_title' => $post->post_title
                    );
                    $password_fields = get_fields($post->ID);
                    if (!empty($password_fields)) {
                        foreach ($password_fields as $key => $value) {
                            $password[$key] = $value;
                        }
                    }
                    $passwords[] = $password;
                }
            }
            return $passwords;
        }

        /* ===== Crypto ===== */
        private function data_key()
        {
            $secret = defined('COMPANY_SECRETS_KEY') ? COMPANY_SECRETS_KEY : '';
            if (!$secret) return str_repeat("\0", SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
            return substr(hash('sha256', $secret, true), 0, SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        }
        private function encrypt_value($plaintext)
        {
            if ($plaintext === null || $plaintext === '') return '';
            $nonce  = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $cipher = sodium_crypto_secretbox($plaintext, $nonce, $this->data_key());
            return base64_encode($nonce . $cipher);
        }
        private function decrypt_value($packed)
        {
            if (!$packed) return '';
            $raw = base64_decode($packed, true);
            if ($raw === false || strlen($raw) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) return '';
            $nonce  = substr($raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $cipher = substr($raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $plain  = sodium_crypto_secretbox_open($cipher, $nonce, $this->data_key());
            return $plain === false ? '' : $plain;
        }

        /* ===== ACF Hooks ===== */
        public function acf_encrypt_on_update($value, $post_id)
        {
            if (get_post_type($post_id) !== self::CPT) return $value;
            if (is_string($value) && $value === self::KEEP_SENTINEL) {
                $field_name = str_replace('acf/update_value/name=', '', current_filter());
                return get_post_meta($post_id, $field_name, true);
            }
            $value = is_string($value) ? wp_unslash($value) : '';
            return $this->encrypt_value($value);
        }
        public function acf_blank_on_load($value, $post_id)
        {
            if (get_post_type($post_id) !== self::CPT) return $value;
            return '';
        }
        public function acf_mask_on_format($value, $post_id, $field)
        {
            if (get_post_type($post_id) !== self::CPT) return $value;
            if (defined('REST_REQUEST') && REST_REQUEST) return null;
            return $value ? '••••••' : '';
        }

        public function acf_prepare_keep_sentinel($field)
        {
            $post_id = function_exists('acf_get_form_data') ? acf_get_form_data('post_id') : 0;
            if (get_post_type($post_id) !== self::CPT) return $field;
            $field['type']  = 'password';
            $field['value'] = self::KEEP_SENTINEL;
            $field['autocomplete'] = 'new-password';
            $field['wrapper']['class'] = trim(($field['wrapper']['class'] ?? '') . ' sdpv-acf-wrap');
            return $field;
        }

        public function acf_render_controls($field)
        {
            $post_id = function_exists('acf_get_form_data') ? acf_get_form_data('post_id') : 0;
            if (get_post_type($post_id) !== self::CPT) return;
            $nonce = wp_create_nonce('sdpv_reveal_' . $post_id);
            $ajax  = admin_url('admin-ajax.php');
            $name  = esc_attr($field['name']);
            echo '<div class="sdpv-acf-controls" 
                      data-post="' . intval($post_id) . '" 
                      data-field="' . $name . '" 
                      data-nonce="' . esc_attr($nonce) . '" 
                      data-ajax="' . esc_attr($ajax) . '" 
                      data-sentinel="' . esc_attr(self::KEEP_SENTINEL) . '">
                    <a href="#" class="sdpv-acf-eye">
                      <i class="bi bi-eye"></i>
                      <span class="pv-spinner spinner-border" style="display:none;"></span>
                    </a>
                    <a href="#" class="sdpv-acf-hide" style="margin-left:6px; display:none;">
                      <i class="bi bi-eye-slash"></i>
                    </a>
                    <small class="text-muted" style="margin-left:6px;">Anzeigen / verbergen</small>
                  </div>';
        }

        /* ===== Master Passwort / Unlock Cookie ===== */
        private function master_hash()
        {
            return defined('COMPANY_MASTER_PASS_HASH') ? COMPANY_MASTER_PASS_HASH : '';
        }
        private function verify_master($plain)
        {
            $hash = $this->master_hash();
            return $hash && $plain && password_verify($plain, $hash);
        }
        private function hmac_key()
        {
            $salt = defined('AUTH_SALT') ? AUTH_SALT : (defined('SECURE_AUTH_SALT') ? SECURE_AUTH_SALT : 'sdpv');
            $dk   = defined('COMPANY_SECRETS_KEY') ? COMPANY_SECRETS_KEY : 'sdpv';
            return hash('sha256', $salt . '|' . $dk, true);
        }
        private function set_unlock_cookie()
        {
            $exp = time() + self::UNLOCK_TTL;
            $nonce = bin2hex(random_bytes(8));
            $payload = json_encode(['exp' => $exp, 'n' => $nonce]);
            $sig = hash_hmac('sha256', $payload, $this->hmac_key());
            $val = base64_encode($payload) . '.' . $sig;
            setcookie(self::UNLOCK_COOKIE, $val, $exp, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
        }
        private function is_unlocked()
        {
            if (empty($_COOKIE[self::UNLOCK_COOKIE])) return false;
            $parts = explode('.', $_COOKIE[self::UNLOCK_COOKIE], 2);
            if (count($parts) !== 2) return false;
            list($b64, $sig) = $parts;
            $payload = base64_decode($b64, true);
            if ($payload === false) return false;
            $calc = hash_hmac('sha256', $payload, $this->hmac_key());
            if (!hash_equals($calc, $sig)) return false;
            $data = json_decode($payload, true);
            return (is_array($data) && !empty($data['exp']) && time() <= intval($data['exp']));
        }

        private function ip_key()
        {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            return 'sdpv_fail_' . md5($ip);
        }
        private function rate_limit_check()
        {
            return (intval(get_transient($this->ip_key())) < 10);
        }
        private function rate_limit_fail()
        {
            $k = $this->ip_key();
            $c = intval(get_transient($k)) + 1;
            set_transient($k, $c, 5 * MINUTE_IN_SECONDS);
        }
        private function rate_limit_reset()
        {
            delete_transient($this->ip_key());
        }

        public function ajax_master_unlock()
        {
            if (!$this->rate_limit_check()) wp_send_json_error(['message' => 'Zu viele Versuche. Bitte kurz warten.'], 429);
            $master = isset($_POST['master']) ? (string) $_POST['master'] : '';
            if (!$this->verify_master($master)) {
                $this->rate_limit_fail();
                wp_send_json_error(['message' => 'Master-Passwort falsch.'], 403);
            }
            $this->rate_limit_reset();
            $this->set_unlock_cookie();
            wp_send_json_success(['message' => 'entsperrt', 'ttl' => self::UNLOCK_TTL]);
        }
        public function ajax_reveal()
        {
            if (!$this->is_unlocked()) wp_send_json_error(['message' => 'LOCKED'], 403);
            $post_id = absint($_POST['post_id'] ?? 0);
            $field   = sanitize_key($_POST['field'] ?? '');
            $nonce   = $_POST['_sdpv_nonce'] ?? '';
            if (!in_array($field, self::FIELDS, true)) wp_send_json_error(['message' => 'Feld ungültig.'], 400);
            if (!wp_verify_nonce($nonce, 'sdpv_reveal_' . $post_id)) wp_send_json_error(['message' => 'Nonce ungültig.'], 403);
            if (get_post_type($post_id) !== self::CPT) wp_send_json_error(['message' => 'Post ungültig.'], 400);

            $enc = get_post_meta($post_id, $field, true);
            $plain = $this->decrypt_value($enc);
            wp_send_json_success(['value' => $plain]);
        }

        public static function render_button($post_id, $field)
        {
            if (!in_array($field, self::FIELDS, true)) return '';
            $nonce = wp_create_nonce('sdpv_reveal_' . $post_id);
            $post_id = intval($post_id);
            $ajax  = admin_url('admin-ajax.php');
            return '
              <a href="javascript:void(0);" 
                 class="sdpv-mini-btn" 
                 data-post="' . $post_id . '" 
                 data-field="' . $field . '" 
                 data-nonce="' . $nonce . '" 
                 data-ajax="' . $ajax . '">
                  <i class="bi bi-eye"></i>
                  <span class="pv-spinner spinner-border" style="display:none;"></span>
              </a>
              <code class="sdpv-mini-out" style="margin-left:6px;">••••••</code>';
        }

        public function harden_cpt($args, $post_type)
        {
            if ($post_type === self::CPT) {
                $args['show_in_rest'] = false;
                $args['exclude_from_search'] = true;
                if (!empty($args['supports']) && is_array($args['supports'])) {
                    $args['supports'] = array_diff($args['supports'], ['revisions', 'trackbacks', 'custom-fields']);
                }
            }
            return $args;
        }
    }
    $PV_Passwords = new PV_Passwords();
}
