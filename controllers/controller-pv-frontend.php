<?php

add_shortcode('pv-frontend-plantable', 'pv_frontend_plantable');
function pv_frontend_plantable()
{
    $frontend = new PV_Frontend();
    $users_class = new PV_Users();

    $users = $users_class->get_all_users();

    if (!empty($users)) {

        $data = array(
            'template' => PV_ADMINISTRATION_PATH . 'views/view-pv-plantable.php',
            'users' => $users
        );
        $html = $frontend->generate_html_output($data);

        return $html;
    } else {
        return '';
    }
}

add_shortcode('pv-frontend-overview', 'pv_frontend_overview');
function pv_frontend_overview()
{
    $frontend = new PV_Frontend();
    $users_class = new PV_Users();

    $users = $users_class->get_all_users();

    if (!empty($users)) {

        $data = array(
            'template' => PV_ADMINISTRATION_PATH . 'views/view-pv-overview.php',
            'users' => $users
        );
        $html = $frontend->generate_html_output($data);

        return $html;
    } else {
        return '';
    }
}

add_shortcode('pv-frontend-listview', 'pv_frontend_listview');
function pv_frontend_listview()
{
    $frontend = new PV_Frontend();
    $users_class = new PV_Users();

    $users = $users_class->get_all_users();

    if (!empty($users)) {

        $data = array(
            'template' => PV_ADMINISTRATION_PATH . 'views/view-pv-listview.php',
            'users' => $users
        );
        $html = $frontend->generate_html_output($data);

        return $html;
    } else {
        return '';
    }
}

add_shortcode('pv-frontend-userview', 'pv_frontend_userview');
function pv_frontend_userview()
{
    $frontend = new PV_Frontend();
    $users_class = new PV_Users();
    $current_user = wp_get_current_user();

    $user = array();
    if (!empty($_GET['user_id']) && in_array('bearbeiter', (array) $current_user->roles)) {
        $user = $users_class->get_current_user($_GET['user_id']);
    } else if (is_user_logged_in()) {
        $user = $users_class->get_current_user();
    }

    if (!empty($user)) {

        $data = array(
            'template' => PV_ADMINISTRATION_PATH . 'views/view-pv-userview.php',
            'user' => $user
        );
        $html = $frontend->generate_html_output($data);

        return $html;
    } else {
        return '<div id="pv-overview" class="container-fluid"><div class="row g-2"><div class="col-12"><div id="message" class="warning"><p>Du musst eingeloggt sein um die Benutzer-Übersicht sehen zu können!</p></div></div></div></div>';
    }
}

add_shortcode('pv-frontend-customers', 'pv_frontend_customers');
function pv_frontend_customers()
{
    $frontend = new PV_Frontend();
    $customers_class = new PV_Customers();

    $customers = $customers_class->get_all_customers();

    if (!empty($customers)) {

        $data = array(
            'template' => PV_ADMINISTRATION_PATH . 'views/view-pv-customers.php',
            'customers' => $customers
        );
        $html = $frontend->generate_html_output($data);

        return $html;
    } else {
        return '';
    }
}

add_shortcode('pv-frontend-request', 'pv_frontend_request');
function pv_frontend_request()
{
    $frontend = new PV_Frontend();
    $requests = new PV_JobRequest();

    $job_requests = $requests->get_all_requests('publish');
    $edited_requests = $requests->get_all_requests(array('angenommen', 'abgelehnt'));
    $user_job_requests = $requests->get_user_requests(get_current_user_id());

    $data = array(
        'template' => PV_ADMINISTRATION_PATH . 'views/view-pv-request.php',
        'job_requests' => $job_requests,
        'edited_requests' => $edited_requests,
        'user_job_requests' => $user_job_requests
    );
    $html = $frontend->generate_html_output($data);

    return $html;
}

add_shortcode('pv-frontend-profile', 'pv_frontend_profile');
function pv_frontend_profile()
{
    $frontend = new PV_Frontend();

    $data = array(
        'template' => PV_ADMINISTRATION_PATH . 'views/view-pv-profile.php'
    );
    $html = $frontend->generate_html_output($data);

    return $html;
}

add_shortcode('pv-frontend-menu', 'pv_frontend_menu');
function pv_frontend_menu()
{
    $frontend = new PV_Frontend();
    $users_class = new PV_Users();

    $users = $users_class->get_all_users(true, false);

    $data = array(
        'template' => PV_ADMINISTRATION_PATH . 'views/view-pv-menu.php',
        'users' => $users
    );
    $html = $frontend->generate_html_output($data);

    return $html;
}

add_shortcode('pv-frontend-notification', 'pv_frontend_sidebar');
function pv_frontend_sidebar()
{
    $frontend = new PV_Frontend();
    $users_class = new PV_Users();

    $notifications = $users_class->get_notifications_of_user();

    $data = array(
        'template' => PV_ADMINISTRATION_PATH . 'views/view-pv-sidebar.php',
        'notifications' => $notifications
    );
    $html = $frontend->generate_html_output($data);

    return $html;
}

add_action('login_form_middle', 'pv_add_lost_password_link');
function pv_add_lost_password_link()
{
    return '<a href="/wp-login.php?action=lostpassword">Passwort vergessen?</a>';
}

add_action('wp_ajax_pv_add_group', 'pv_add_group');
add_action('wp_ajax_nopriv_pv_add_group', 'pv_add_group');
function pv_add_group()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_add_group') {
        acf_form(array(
            'post_id' => 'new_post',
            'post_title' => true,
            'new_post' => array(
                'post_type' => 'bearbeitungsgruppen',
                'post_status' => 'publish'
            ),
            'submit_value' => __("Erstellen", 'acf'),
            'uploader' => 'basic',
            'html_submit_button' => '<input type="submit" class="btn btn-primary" value="%s" />',
            'return' => $_POST['url']
        ));
        wp_die();
    }
}

add_action('wp_ajax_pv_edit_group', 'pv_edit_group');
add_action('wp_ajax_nopriv_pv_edit_group', 'pv_edit_group');
function pv_edit_group()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_edit_group') {
        acf_form(array(
            'post_id' => $_POST['group_id'],
            'post_title' => true,
            'submit_value' => __("Aktualisieren", 'acf'),
            'uploader' => 'basic',
            'html_submit_button' => '<input type="submit" class="btn btn-primary" value="%s" />',
            'return' => $_POST['url']
        ));
        wp_die();
    }
}

add_action('wp_ajax_pv_older_filter', 'pv_filter_bearbeitungen');
add_action('wp_ajax_nopriv_pv_older_filter', 'pv_filter_bearbeitungen');
add_action('wp_ajax_pv_search_filter', 'pv_filter_bearbeitungen');
add_action('wp_ajax_nopriv_pv_search_filter', 'pv_filter_bearbeitungen');
function pv_filter_bearbeitungen()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_search_filter') {
        $formular = '<form method="get" class="mb-2">';
        $formular .= '<div class="input-group mb-2">';
        $formular .= '<input type="text" name="suche" class="form-control" placeholder="Suchbegriff" aria-label="Suche" value="' . (!empty($_POST['value']) ? esc_attr($_POST['value']) : '') . '">';
        $formular .= '<button class="btn btn-primary" type="submit">Suchen</button>';
        $formular .= '</div>';
        $formular .= '<div class="form-text mt-1"><strong>Tipp:</strong> Verwende <code>|Suchbegriff|</code> für exakte Wortsuche.</div>';
        $formular .= '</form>';
        echo $formular;
        wp_die();
    } else if (!empty($_POST['action']) && $_POST['action'] == 'pv_older_filter') {
        $formular = '<form method="get" class="mb-2">';
        $formular .= '<div class="input-group mb-2">';
        $formular .= '<input type="text" name="older" class="form-control" placeholder="Tage" aria-label="older" value="' . (!empty($_POST['value']) ? esc_attr($_POST['value']) : '') . '">';
        $formular .= '<button class="btn btn-primary" type="submit">Filtern</button>';
        $formular .= '</div>';
        $formular .= '<div class="form-text mt-1"><strong>Vewendung:</strong> Filtert alle Jobs, die seit <code>X Tagen</code> nicht mehr bearbeitet wurden.</div>';
        $formular .= '</form>';
        echo $formular;
        wp_die();
    }
}

add_action('wp_ajax_pv_add_job', 'pv_add_job');
add_action('wp_ajax_nopriv_pv_add_job', 'pv_add_job');
function pv_add_job()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_add_job') {
        acf_form(array(
            'post_id' => 'new_post',
            'post_title' => true,
            'new_post' => array(
                'post_type' => 'bearbeitungen',
                'post_status' => 'publish'
            ),
            'submit_value' => __("Hinzufügen", 'acf'),
            'uploader' => 'basic',
            'html_submit_button' => '<input type="submit" class="btn btn-primary" value="%s" />',
            'return' => $_POST['url']
        ));
        wp_die();
    }
}

add_action('wp_ajax_pv_edit_job', 'pv_edit_job');
add_action('wp_ajax_nopriv_pv_edit_job', 'pv_edit_job');
function pv_edit_job()
{
    if (($_POST['action'] ?? '') !== 'pv_edit_job') return;

    $post_id = (int) ($_POST['job_id'] ?? 0);
    $uid     = get_current_user_id();

    $raw  = get_field('pv_bearbeiter', $post_id);
    $list = is_array($raw) ? $raw : (empty($raw) ? [] : [$raw]);

    $ids = array_map(static function ($u) {
        if (is_array($u))   return (int) ($u['ID'] ?? $u[0] ?? 0);
        if (is_object($u))  return (int) ($u->ID ?? 0);
        return (int) $u; // falls ACF direkt eine ID liefert
    }, $list);

    // Nur löschen, wenn aktueller User als Bearbeiter eingetragen ist
    if (in_array($uid, $ids, true)) {
        foreach (get_children(['post_parent' => $post_id, 'post_type' => 'revision', 'numberposts' => -1]) as $rev) {
            if ((int)$rev->post_author !== $uid) {
                wp_delete_post($rev->ID, true);
            }
        }
    }

    acf_form([
        'post_id'            => $post_id,
        'post_title'         => true,
        'submit_value'       => __('Aktualisieren', 'acf'),
        'uploader'           => 'basic',
        'html_submit_button' => '<input type="submit" class="btn btn-primary" value="%s" />',
        'return'             => $_POST['url'] ?? ''
    ]);
    wp_die();
}



add_action('wp_ajax_pv_add_tracking', 'pv_add_tracking');
add_action('wp_ajax_nopriv_pv_add_tracking', 'pv_add_tracking');
function pv_add_tracking()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_add_tracking') {
        acf_form(array(
            'post_id' => 'new_post',
            'post_title' => false,
            'new_post' => array(
                'post_type' => 'trackings',
                'post_status' => 'publish'
            ),
            'submit_value' => __("Hinzufügen", 'acf'),
            'uploader' => 'basic',
            'html_submit_button' => '<input type="submit" class="btn btn-primary" value="%s" />',
            'return' => $_POST['url']
        ));
        wp_die();
    }
}

add_action('wp_ajax_pv_jobrequest_action', 'pv_jobrequest_action');
add_action('wp_ajax_nopriv_pv_jobrequest_action', 'pv_jobrequest_action');
function pv_jobrequest_action()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_jobrequest_action') {
        if ($_POST['aktion'] == 'new') {
            acf_form(array(
                'post_id' => 'new_post',
                'post_title' => true,
                'new_post' => array(
                    'post_type' => 'jobanfragen',
                    'post_status' => 'publish'
                ),
                'submit_value' => __("Anfragen", 'acf'),
                'uploader' => 'basic',
                'html_submit_button' => '<input type="submit" class="btn btn-primary" value="%s" />',
                'return' => $_POST['url']
            ));
            wp_die();
        } else if ($_POST['aktion'] == 'accept') {
            acf_form(array(
                'post_id' => $_POST['requestid'],
                'post_title' => true,
                'submit_value' => __("Hinzufügen", 'acf'),
                'uploader' => 'basic',
                'html_submit_button' => '<input type="submit" class="btn btn-primary" value="%s" />',
                'return' => $_POST['url']
            ));
            wp_die();
        } else if ($_POST['aktion'] == 'refuse') {
            $html = '<p>Wenn dieser Job abgelehnt wird, verschindet er aus der Liste der angefragten Jobs und wird nicht automatisch angelegt.</p>';
            $html .= '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="post">';
            $html .= '<input type="hidden" name="action" value="pv_jobrequest_action_refuse">';
            $html .= '<input type="hidden" name="aktion" value="refuse">';
            $html .= '<input type="hidden" name="requestid" value="' . $_POST['requestid'] . '">';
            $html .= '<input type="hidden" name="redirect_to" value="' . esc_url($_SERVER['REQUEST_URI']) . '">';
            $html .= wp_nonce_field('pv_jobrequest_action_refuse', 'pv_jobrequest_action_refuse_nonce');
            $html .= '<button type="submit" class="btn btn-danger">Ablehnen<span class="pv-spinner spinner-border ms-1"></span></button>';
            $html .= '</form>';
            echo $html;
            wp_die();
        }
    }
}

add_action('wp_ajax_pv_remove_notification', 'pv_remove_notification');
add_action('wp_ajax_nopriv_pv_remove_notification', 'pv_remove_notification');
function pv_remove_notification()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_remove_notification') {
        $users_class = new PV_Users();

        if (!empty($_POST['action_key']) && !empty($_POST['action_data']) && !empty($_POST['user_id'])) {
            $result = $users_class->remove_notifications_of_user($_POST['action_key'], $_POST['action_data'], $_POST['user_id']);
            wp_send_json_success();
        }
    }
    wp_send_json_error();
}

add_action('wp_ajax_pv_sort_user_jobs', 'pv_sort_user_jobs');
add_action('wp_ajax_nopriv_pv_sort_user_jobs', 'pv_sort_user_jobs');
function pv_sort_user_jobs()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_sort_user_jobs') {
        if (!empty($_POST['ids']) && !empty($_POST['user_id'])) {
            if (get_option('pv_job_sort_' . $_POST['user_id'])) {
                update_option('pv_job_sort_' . $_POST['user_id'], explode(',', $_POST['ids']));
                wp_send_json_success();
            } else {
                add_option('pv_job_sort_' . $_POST['user_id'], explode(',', $_POST['ids']));
                wp_send_json_success();
            }
        }
        // if (!empty($_POST['ids'])) {
        //     $posts = get_posts(array(
        //         'numberposts'   => -1,
        //         'post_type' => 'bearbeitungen',
        //         'orderby' => 'post__in',
        //         'post__in' => explode(',', $_POST['ids'])
        //     ));
        //     if (!empty($posts)) {
        //         $number = 1;
        //         foreach ($posts as $post) {
        //             wp_update_post(array('ID' => $post->ID, 'menu_order' => $number));
        //             $number++;
        //         }
        //         wp_send_json_success();
        //     }
        // }
    }
    wp_send_json_error();
}

add_action('admin_post_pv_update_user', 'pv_update_user');
add_action('admin_post_nopriv_pv_update_user', 'pv_update_user');
function pv_update_user()
{
    global $current_user;

    wp_get_current_user();
    wp_verify_nonce($_POST['pv_update_user'], 'update_user_informations');

    $url = $_POST['url'];
    $error = array();

    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] = 'pv_update_user') {

        /* Update user password. */
        if (!empty($_POST['pass1']) && !empty($_POST['pass2'])) {
            if ($_POST['pass1'] == $_POST['pass2'])
                wp_update_user(array('ID' => $current_user->ID, 'user_pass' => esc_attr($_POST['pass1'])));
            else
                $error[] = 'Die Passwörter stimmen nicht überein.';
        }

        /* Update user information. */
        if (!empty($_POST['email'])) {
            if (!is_email(esc_attr($_POST['email'])))
                $error[] = 'Die E-Mail ist nicht zulässig.';
            elseif (email_exists(esc_attr($_POST['email'])) != $current_user->id)
                $error[] = 'Ein Profil mit dieser E-Mail existiert bereits.';
            else {
                wp_update_user(array('ID' => $current_user->ID, 'user_email' => esc_attr($_POST['email'])));
            }
        }
        if (!empty($_POST['first-name'])) {
            update_user_meta($current_user->ID, 'first_name', esc_attr($_POST['first-name']));
        }
        if (!empty($_POST['last-name'])) {
            update_user_meta($current_user->ID, 'last_name', esc_attr($_POST['last-name']));
        }
        if (!empty($_POST['description'])) {
            update_user_meta($current_user->ID, 'description', esc_attr($_POST['description']));
        }
        if (!empty($_POST['acf'])) {
            foreach ($_POST['acf'] as $field => $field_value) {
                update_field($field, $field_value, 'user_' . $current_user->ID);
            }
        }

        if (count($error) == 0) {
            //action hook for plugins and extra fields saving
            do_action('edit_user_profile_update', $current_user->ID);
            wp_redirect($url . '?updated=true');
            exit;
        }
        wp_redirect($url);
        exit;
    }
}

add_filter('acf/prepare_field/name=_post_title', 'pv_prepare_title_bearbeitungen');
function pv_prepare_title_bearbeitungen($field)
{
    if (!empty($_POST['action'])) {
        if ($_POST['action'] == 'pv_add_job' || $_POST['action'] == 'pv_edit_job') {
            $field['label'] = 'Bezeichnung';
        }
    }
    return $field;
}

add_filter('acf/prepare_field/name=_post_title', 'pv_prepare_title_groups');
function pv_prepare_title_groups($field)
{
    if (!empty($_POST['action'])) {
        if ($_POST['action'] == 'pv_add_group' || $_POST['action'] == 'pv_edit_group') {
            $field['label'] = 'Gruppenname';
        }
    }
    return $field;
}

add_action('wp_ajax_pv_choose_filter_users', 'pv_choose_filter_users');
add_action('wp_ajax_nopriv_pv_choose_filter_users', 'pv_choose_filter_users');
function pv_choose_filter_users()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_choose_filter_users') {
        if (!empty($_POST['value'])) {
            if ($_POST['value'] == 'all') {
                setcookie('pv_choose_filter_users', '', time() - 3600, '/');
            } else {
                setcookie('pv_choose_filter_users', $_POST['value'], time() + 31556926, '/');
            }
            wp_send_json_success();
        }
    }
    wp_send_json_error();
}
