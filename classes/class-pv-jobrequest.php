<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PV_JobRequest')) {
    class PV_JobRequest
    {
        protected $api;

        public function __construct()
        {
            $this->api = new PV_Importapi();

            add_action('acf/save_post', array($this, 'on_request_form_submit'), 10);
            add_action('admin_post_pv_jobrequest_action_refuse', array($this, 'handle_refuse_action'));
            add_action('admin_post_nopriv_pv_jobrequest_action_refuse', array($this, 'handle_refuse_action'));
        }

        public function get_all_requests($status)
        {
            $requests = array();
            $requests_posts = get_posts(array(
                'post_type' => 'jobanfragen',
                'post_status' => $status,
                'numberposts' => -1
            ));

            if (!empty($requests_posts)) {
                foreach ($requests_posts as $request) {
                    $request = $request->to_array();
                    $request_fields = get_fields($request['ID']);

                    if (!empty($request_fields)) {
                        foreach ($request_fields as $key => $value) {
                            $request[$key] = $value;
                        }
                    }
                    $requests[] = $request;
                }
            }
            return $requests;
        }

        public function get_user_requests($userid = null)
        {
            $requests = array();
            $requests_posts = get_posts(array(
                'post_type' => 'jobanfragen',
                'post_status' => array('publish', 'angenommen', 'abgelehnt'),
                'numberposts' => -1,
                'author' => $userid
            ));

            if (!empty($requests_posts)) {
                foreach ($requests_posts as $request) {
                    $request = $request->to_array();
                    $request_fields = get_fields($request['ID']);

                    if (!empty($request_fields)) {
                        foreach ($request_fields as $key => $value) {
                            $request[$key] = $value;
                        }
                    }
                    $requests[] = $request;
                }
            }
            return $requests;
        }

        public function on_request_form_submit($post_id)
        {
            // Nur für CPT jobanfragen
            if (get_post_type($post_id) !== 'jobanfragen') {
                return;
            }

            // Prüfe, ob ACF-Daten vorhanden sind
            if (empty($_POST['acf']) || !is_array($_POST['acf'])) {
                return;
            }

            $acf = $_POST['acf'];
            $pv_requestid   = intval($acf['pv_requestid']);
            if (empty($pv_requestid)) {
                $users_class = new PV_Users();
                $users = $users_class->get_all_users(false);

                $emails = array();
                foreach ($users as $user) {
                    if (in_array('bearbeiter', (array) $user->roles)) {
                        // oder andere Rolle(n), die benachrichtigt werden sollen
                        $emails[] = $user->user_email;
                    }
                }

                if (!empty($emails)) {
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    $content = 'Hallo Administrator,<br><br>
                    es wurde ein neue Jobanfrage hinzugefügt!<br><br>
                    Öffne die Job-Anfrage und melde dich an, um diese Anfrage zu bearbeiten:<br><a href="' . get_site_url() . '/job-anfrage/" target="_blank">' . get_site_url() . '/job-anfrage/</a><br><br>
                    Diese Mitteilung wurde automatisch erstellt!<br>
                    Sislak Design Werbeagentur GmbH';
                    wp_mail($emails, 'Neue Jobanfrage', $content, $headers);
                }
                return;
            }

            $project_name    = sanitize_text_field($acf['pv_anfrage_projektname'] ?? '');
            $kunde_post_id   = intval($acf['pv_anfrage_kunde'] ?? 0);
            $mitarbeiter_id  = intval($acf['pv_anfrage_mitarbeiter'] ?? 0);
            $frist_raw       = $acf['pv_anfrage_frist'] ?? '';
            $notiz           = wp_strip_all_tags($acf['pv_anfrage_notiz'] ?? '');

            // URNOs aus WP-Objekten laden
            $urno_company = $kunde_post_id ? get_field('pv_id', $kunde_post_id) : null;
            $urno_manager = $mitarbeiter_id ? get_field('pv_urno_person', 'user_' . $mitarbeiter_id) : null;

            if (!$project_name || !$urno_company || !$urno_manager) {
                error_log('[PV_JobRequest] Fehlende Pflichtwerte.');
                return;
            }

            $delivery_date = $frist_raw ? date('Y-m-d', strtotime($frist_raw)) : null;

            // API-Daten vorbereiten
            $api_data = array(
                'project_name'   => $project_name,
                'urno_company'   => (int)$urno_company,
                'urno_manager'   => (int)$urno_manager,
                'delivery_date'  => $delivery_date,
                'description'    => $notiz
            );

            // API-Aufruf
            $response = $this->post_data('projekte', $api_data);

            if (is_wp_error($response)) {
                error_log('[PV_JobRequest] API Fehler: ' . $response->get_error_message());
            } else {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => 'angenommen'
                ));
            }
        }

        public function handle_refuse_action()
        {
            if (
                !isset($_POST['pv_jobrequest_action_refuse_nonce']) ||
                !wp_verify_nonce($_POST['pv_jobrequest_action_refuse_nonce'], 'pv_jobrequest_action_refuse')
            ) {
                wp_die('Ungültige Anfrage (Nonce-Fehler)');
            }

            $request_id = intval($_POST['requestid'] ?? 0);
            $aktion     = sanitize_text_field($_POST['aktion'] ?? '');

            if ($aktion === 'refuse' && $request_id > 0) {
                wp_update_post(array(
                    'ID' => $request_id,
                    'post_status' => 'abgelehnt'
                ));
            }

            $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw($_POST['redirect_to']) : home_url();
            wp_redirect($redirect_to);
            wp_die();
        }

        public function post_data($type, $data, $defaults = true)
        {
            return $this->api->post_data($type, $data, $defaults);
        }
    }
    $PV_JobRequest = new PV_JobRequest();
}
