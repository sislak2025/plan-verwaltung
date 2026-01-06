<?php

add_action('wp_ajax_pv_load_passwords_table', 'pv_load_passwords_table');
add_action('wp_ajax_nopriv_pv_load_passwords_table', 'pv_load_passwords_table');
function pv_load_passwords_table()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_load_passwords_table') {
        $passwords_class = new PV_Passwords();

        $current_url = $_SERVER['HTTP_REFERER'];
        $passwords = $passwords_class->get_all_passwords();

        $data = array();
        foreach ($passwords as $password) {
            $array = array();
            $array[] = $password['post_title'];

            if (!empty($password['pv_p_live_url'])) {
                $array[] = $password['pv_p_live_url'];
                $array[] = !empty($password['pv_p_live_username']) ? $password['pv_p_live_username'] : 'Kein Benutzer';
                $array[] = !empty($password['pv_p_live_passwort']) ? $password['pv_p_live_passwort'] : 'Kein Passwort';
                //$array[] = PV_Passwords::render_button($password['ID'], 'pv_p_live_passwort');
            } else if (!empty($password['pv_p_entwicklung_url'])) {
                $array[] = $password['pv_p_entwicklung_url'];
                $array[] = !empty($password['pv_p_entwicklung_username']) ? $password['pv_p_entwicklung_username'] : 'Kein Benutzer';
                $array[] = !empty($password['pv_p_entwicklung_passwort']) ? $password['pv_p_entwicklung_passwort'] : 'Kein Passwort';
                //$array[] = PV_Passwords::render_button($password['ID'], 'pv_p_entwicklung_passwort');
            } else {
                $array[] = 'Keine Domain';
                $array[] = 'Kein Benutzer';
                $array[] = 'Kein Passwort';
            }

            $array[] = $password['ID'];
            $data['data'][] = $array;
        }
        echo json_encode($data);
    }
    exit();
}

add_action('wp_ajax_pv_add_password', 'pv_add_password');
add_action('wp_ajax_nopriv_pv_add_password', 'pv_add_password');
function pv_add_password()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_add_password') {
        acf_form(array(
            'post_id' => 'new_post',
            'post_title' => true,
            'new_post' => array(
                'post_type' => 'passwords',
                'post_status' => 'publish'
            ),
            'submit_value' => __("Hinzufügen", 'acf'),
            'html_submit_button' => '<input type="submit" class="btn btn-primary" value="%s" />',
            'return' => $_POST['url']
        ));
        die();
    }
}

add_action('wp_ajax_pv_edit_password', 'pv_edit_password');
add_action('wp_ajax_nopriv_pv_edit_password', 'pv_edit_password');
function pv_edit_password()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_edit_password') {
        acf_form(array(
            'post_id' => $_POST['id'],
            'post_title' => true,
            'submit_value' => __("Aktualisieren", 'acf'),
            'html_submit_button' => '<input type="submit" class="btn btn-primary" value="%s" />',
            'return' => $_POST['url']
        ));
        die();
    }
}

add_filter('acf/prepare_field/name=_post_title', 'pv_prepare_title_password');
function pv_prepare_title_password($field)
{
    if (!empty($_POST['action'])) {
        if ($_POST['action'] == 'pv_add_password' || $_POST['action'] == 'pv_edit_password') {
            $field['label'] = 'Kunde';
        }
    }
    return $field;
}

#add_filter('acf/validate_value/name=pv_entwicklung_url', 'pv_validate_domain_already_exists', 10, 4);
add_filter('acf/validate_value/name=pv_p_live_url', 'pv_validate_domain_already_exists', 10, 4);
add_filter('acf/validate_value/name=pv_p_entwicklung_url', 'pv_validate_domain_already_exists', 10, 4);
function pv_validate_domain_already_exists($valid, $value, $field, $input)
{
    if (!$valid || $value == '') {
        return $valid;
    }

    global $post;
    $post_id = !empty($_POST['_acf_post_id']) ? $_POST['_acf_post_id'] : $post->ID;
    $field_key = ($field['key'] == 'pv_entwicklung_url' ? 'pv_p_entwicklung_url' : $field['key']);

    $args = array(
        'post_type' => 'passwords',
        'post__not_in' => array($post_id),
        'meta_query' => array(
            array(
                'key' => $field_key,
                'value' => $value
            )
        )
    );
    $posts = get_posts($args);

    // if (!empty($posts) && $field['key'] == 'pv_entwicklung_url') {
    //     $pv_p_entwicklung_url = get_field('pv_p_entwicklung_url', $posts[0]->ID);
    //     if ($value == $pv_p_entwicklung_url) {
    //         return $valid;
    //     }
    // }

    if (count($posts)) {
        $valid = 'Es gibt bereits einen Eintrag in der Passwortliste mit dieser Domain!';
    }
    return $valid;
}

// Erstelle Passworteinträge beim Speichern von Bearbeitungen
add_action('save_post_bearbeitungen', 'pv_on_post_save_bearbeitungen', 10, 3);
function pv_on_post_save_bearbeitungen($post_id, $post, $update)
{
    $posttype_class = new PV_Posttype();

    if (!empty($_POST['acf']['pv_group_additional'])) {
        $acf_fields = $_POST['acf'];
        $additional = $acf_fields['pv_group_additional'];
        $entwicklungen = $additional['pv_job_entwicklungen'];

        if (!empty($entwicklungen)) {
            $find = array('pv_entwicklung_url', 'pv_entwicklung_username', 'pv_entwicklung_passwort', 'pv_entwicklung_ftp_server', 'pv_entwicklung_ftp_port', 'pv_entwicklung_ftp_username', 'pv_entwicklung_ftp_passwort', 'pv_entwicklung_hosting_hoster', 'pv_entwicklung_hosting_username', 'pv_entwicklung_hosting_passwort');
            $replace = array('p_entwicklung_url', 'p_entwicklung_username', 'p_entwicklung_passwort', 'p_ftp_server', 'p_ftp_port', 'p_ftp_username', 'p_ftp_passwort', 'p_hosting_hoster', 'p_hosting_username', 'p_hosting_passwort');

            // Hole und checke die hidden IDs der Entwicklungsumgebungen in der Bearbeitung
            $password_ids = array();
            foreach ($entwicklungen as $entwicklung_key => $entwicklung) {
                foreach ($entwicklung as $field_key => $field) {
                    if ($field_key == 'pv_entwicklung_id') {
                        if (!empty($field)) {
                            $password_ids[] = $field;
                        } else {
                            $password_ids[] = 'new';
                        }
                    }
                }
            }

            if (!empty($password_ids)) {
                // Lasse das Array bei 1 starten, damit es keinen 0 Key gibt
                array_unshift($password_ids, '');
                unset($password_ids[0]);
                // Finde die größte ID im Array
                $max_number = array_filter($password_ids, 'is_numeric');
                if (!empty($max_number)) {
                    $max_number = max($max_number);
                }

                $count = 1;
                foreach ($entwicklungen as $entwicklung_key => $entwicklung) {

                    // Baue ein Array, welches die Passwort-Daten enthält
                    $password_data = array();
                    foreach ($entwicklung as $field_key => $field) {
                        if (in_array($field_key, $find) && !empty($field)) {
                            $key = str_replace($find, $replace, $field_key);
                            $password_data[$key] = $field;
                        }
                    }

                    if (!empty($password_data)) {
                        // Stelle sicher, dass Entwicklungsumgebung Einträge eine neue eindeutige ID bekommen und alte Ihre ID behalten
                        if ($password_ids[$count] == 'new') {
                            if (!empty($max_number)) {
                                $_POST['acf']['pv_group_additional']['pv_job_entwicklungen'][$entwicklung_key]['pv_entwicklung_id'] = $max_number + 1;
                                $password_data['urno'] = $post_id . '_' . $max_number + 1; // Die ID von pv_id im Posttype Passwords
                                $max_number++;
                            } else {
                                $_POST['acf']['pv_group_additional']['pv_job_entwicklungen'][$entwicklung_key]['pv_entwicklung_id'] = $count;
                                $password_data['urno'] = $post_id . '_' . $count; // Die ID von pv_id im Posttype Passwords
                            }
                        } else {
                            $_POST['acf']['pv_group_additional']['pv_job_entwicklungen'][$entwicklung_key]['pv_entwicklung_id'] = $password_ids[$count];
                            $password_data['urno'] = $post_id . '_' . $password_ids[$count]; // Die ID von pv_id im Posttype Passwords
                        }
                        $count++;

                        // Generiere einen Post Titel für den Posttype Passwords
                        $title = $post->post_title;
                        if (!empty($acf_fields['pv_jobs'])) {
                            $job_title = get_the_title($acf_fields['pv_jobs'][0]);

                            $re = '/.+?(?=-\d)/';
                            preg_match_all($re, $job_title, $matches, PREG_SET_ORDER, 0);
                            $shortname = $matches[0][0];

                            $post_kunde = get_posts(array(
                                'numberposts'   => 1,
                                'post_type'     => 'kunden',
                                'meta_key'      => 'pv_prefix',
                                'meta_value'    => $shortname
                            ));
                            if (empty($post_kunde)) {
                                $post_kunde = get_posts(array(
                                    'numberposts'   => 1,
                                    'post_type'     => 'kunden',
                                    'meta_key'      => 'pv_shortname',
                                    'meta_value'    => $shortname
                                ));
                            }
                            if (!empty($post_kunde[0])) {
                                $title = $post_kunde[0]->post_title;
                            }
                            $password_data['p_kuerzel'] = $shortname;
                        } else if (!empty($entwicklung['pv_entwicklung_kunde'])) {
                            $title = $entwicklung['pv_entwicklung_kunde'];
                        }

                        $post_data['post_type'] = 'passwords';
                        $post_data['post_title'] = $title;
                        $post_data['post_content'] = ' ';
                        $post_data['post_date'] = date('Y-m-d H:i:s');
                        $post_data['post_fields'] = $password_data;
                        $post_data['post_excerpt'] = '';

                        $posttype_class->insert_post($post_data);
                    }
                }
            }
        }
    }
}

// Überprüfe ob ein Job verknüpft wurde, wenn nein muss ein Kunde hinzugefügt werden
add_filter('acf/validate_value/name=pv_entwicklung_kunde', 'pv_validate_entwicklung_kunde', 10, 4);
function pv_validate_entwicklung_kunde($valid, $value, $field, $input)
{
    if (!$valid) {
        return $valid;
    }
    $pv_jobs = $_POST['acf']['pv_jobs'];
    $additional = $_POST['acf']['pv_group_additional'];
    unset($additional['pv_job_updates']);
    unset($additional['pv_job_bestandteile']);
    unset($additional['pv_job_dateien']);
    unset($additional['pv_job_abwicklungsinfos']);

    $input = true;
    foreach ($additional as $field) {
        if (!empty($field)) {
            $input = false;
        }
    }
    if (empty($pv_jobs)) {
        if (!$value && !$input) {
            $valid = __('Bitte Feld ausfüllen oder Job verknüpfen, wenn Daten eingetragen wurden.');
        }
    }
    return $valid;
}

// Wenn ein Passwort direkt in der Passwortliste hinzugefügt wird, erstelle interne Nummer
add_action('acf/save_post', 'pv_set_password_id');
function pv_set_password_id($post_id)
{
    $posttype = get_post_type($post_id);
    $id_field = get_field('pv_id', $post_id);

    if ('passwords' == $posttype && empty($id_field)) {
        update_field('pv_id', $post_id, $post_id);
    }
}

add_action('save_post_passwords', 'pv_on_post_save_passwords', 10, 3);
function pv_on_post_save_passwords($post_id, $post, $update)
{
    $find = array('pv_p_entwicklung_url', 'pv_p_entwicklung_username', 'pv_p_entwicklung_passwort', 'pv_p_ftp_server', 'pv_p_ftp_port', 'pv_p_ftp_username', 'pv_p_ftp_passwort', 'pv_p_hosting_hoster', 'pv_p_hosting_username', 'pv_p_hosting_passwort');
    $replace = array('pv_entwicklung_url', 'pv_entwicklung_username', 'pv_entwicklung_passwort', 'pv_entwicklung_ftp_server', 'pv_entwicklung_ftp_port', 'pv_entwicklung_ftp_username', 'pv_entwicklung_ftp_passwort', 'pv_entwicklung_hosting_hoster', 'pv_entwicklung_hosting_username', 'pv_entwicklung_hosting_passwort');

    $bearbeitung_id = get_field('pv_id', $post_id);
    $entwicklung_id = '';
    if (str_contains($bearbeitung_id, '_')) {
        $id_array = explode('_', $bearbeitung_id);
        $bearbeitung_id = $id_array[0];
        $entwicklung_id = $id_array[1];
    }

    if (!empty($bearbeitung_id) && !empty($entwicklung_id)) {
        if ('publish' === get_post_status($bearbeitung_id) && 'bearbeitungen' === get_post_type($bearbeitung_id)) {
            $password_fields = $_POST['acf'];
            $pv_additional_bearbeitung = get_field('pv_group_additional', $bearbeitung_id);
            $entwicklungen = $pv_additional_bearbeitung['pv_job_entwicklungen'];

            if (!empty($entwicklungen)) {
                foreach ($entwicklungen as $entwicklung_key => $entwicklung) {
                    if ($entwicklung['pv_entwicklung_id'] == $entwicklung_id) {
                        foreach ($password_fields as $field_key => $password_field) {
                            if (in_array($field_key, $find)) {
                                $key = str_replace($find, $replace, $field_key);
                                if ($entwicklungen[$entwicklung_key][$key] != $password_field) {
                                    $entwicklungen[$entwicklung_key][$key] = $password_field;
                                }
                            }
                        }
                    }
                }
                $sub_field = array('pv_job_entwicklungen' => $entwicklungen);
                update_field('pv_group_additional', $sub_field, $bearbeitung_id);
            }
        }
    }
}
