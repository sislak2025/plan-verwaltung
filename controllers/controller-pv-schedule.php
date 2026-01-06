<?php
add_action('wp_ajax_pv_get_user_schedules', 'pv_get_user_schedules');
add_action('wp_ajax_nopriv_pv_get_user_schedules', 'pv_get_user_schedules');
function pv_get_user_schedules()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_get_user_schedules') {
        if (!empty($_POST['userid'])) {
            $schedule = array();

            $exclude_status = array(
                'Geliefert - Abgeschlossen',
                'Geliefert - Fertiggestellt',
                'Abgebrochen'
            );

            $user_schedules = get_user_meta($_POST['userid'], 'pv_user_schedules', true);
            if (empty($user_schedules)) {

                // Wenn in der Datenbank keine User Schedule vorhanden ist:
                $users_class = new PV_Users();
                $bearbeitungen = $users_class->get_user_bearbeitungen($_POST['userid']);

                $schedule_bearbeitungen = array();
                if (!empty($bearbeitungen)) {
                    $start_time = '07:00';
                    $end_time = '09:00';
                    foreach ($bearbeitungen as $bearbeitung) {
                        $private_job = !empty($bearbeitung['fields']['pv_private_visible']) ? $bearbeitung['fields']['pv_private_visible'] : array();
                        if (!in_array($bearbeitung['fields']['pv_bearbeitung_status'], $exclude_status) && !in_array('not_visible', $private_job)) {
                            $schedule_bearbeitungen[] = array(
                                'start' => $start_time,
                                'end' => $end_time,
                                'text' => $bearbeitung['post_title'],
                                'timeline' => 7,
                                'class' => 'pv_job_schedule_color pv_job_' . $bearbeitung['fields']['pv_job_color'],
                                'data' => array(
                                    'id' => $bearbeitung['ID'],
                                )
                            );
                            $start_time = date('H:i', strtotime($start_time . '+2 hour'));
                            $end_time = date('H:i', strtotime($end_time . '+2 hour'));
                            if (date('H', strtotime($start_time)) > 20 || date('H', strtotime($end_time)) > 20) {
                                $start_time = '07:00';
                                $end_time = '09:00';
                            }
                        }
                    }
                }

                $schedule[0] = array('title' => 'Montag');
                $schedule[1] = array('title' => 'Dienstag');
                $schedule[2] = array('title' => 'Mittwoch');
                $schedule[3] = array('title' => 'Donnerstag');
                $schedule[4] = array('title' => 'Freitag');
                $schedule[5] = array('title' => 'Samstag');
                $schedule[6] = array('title' => 'Sonntag');
                $schedule[7] = array(
                    'title' => 'Zugeteilte Jobs',
                    'schedule' => $schedule_bearbeitungen
                );
            } else {

                // Wenn in der Datenbank bereits eine User Schedule vorhanden ist:
                $schedule = $user_schedules;

                // Prüfen ob es bereits neue Jobs gibt, die noch nicht im User Schedule vorhanden sind:
                $schedule_ids_in_db = array();
                foreach ($schedule as $index => $day) {
                    if (!empty($day['schedule'])) {
                        foreach ($day['schedule'] as $day_schedule) {
                            $schedule_ids_in_db[] = $day_schedule['data']['id'];
                        }
                    }
                }

                $users_class = new PV_Users();
                $bearbeitungen = $users_class->get_user_bearbeitungen($_POST['userid']);

                // Neue Jobs identifizieren
                $bearbeitungen_neu = array();
                $bearbeitungs_ids = array();
                if (!empty($bearbeitungen)) {
                    foreach ($bearbeitungen as $bearbeitung) {
                        $private_job = !empty($bearbeitung['fields']['pv_private_visible']) ? $bearbeitung['fields']['pv_private_visible'] : array();
                        if (!in_array($bearbeitung['fields']['pv_bearbeitung_status'], $exclude_status) && !in_array('not_visible', $private_job)) {
                            if (!in_array($bearbeitung['ID'], $schedule_ids_in_db)) {
                                $bearbeitungen_neu[] = $bearbeitung;
                            }
                            $bearbeitungs_ids[] = $bearbeitung['ID'];
                        }
                    }
                }

                // Jobs, welche in der zwischenzeit abgeschlossen wurden entfernen
                foreach ($schedule as $index_day => $day) {
                    if (!empty($day['schedule'])) {
                        foreach ($day['schedule'] as $index_schedule => $day_schedule) {
                            if (!in_array($day_schedule['data']['id'], $bearbeitungs_ids)) {
                                unset($schedule[$index_day]['schedule'][$index_schedule]);
                            }
                        }
                    }
                }

                if (!empty($bearbeitungen_neu)) {
                    $start_time = '07:00';
                    $end_time = '09:00';
                    foreach ($bearbeitungen_neu as $bearbeitung) {
                        $schedule[7]['schedule'][] = array(
                            'start' => $start_time,
                            'end' => $end_time,
                            'text' => $bearbeitung['post_title'],
                            'timeline' => 7,
                            'data' => array('id' => $bearbeitung['ID'])
                        );
                        $start_time = date('H:i', strtotime($start_time . '+2 hour'));
                        $end_time = date('H:i', strtotime($end_time . '+2 hour'));
                        if (date('H', strtotime($start_time)) > 20 || date('H', strtotime($end_time)) > 20) {
                            $start_time = '07:00';
                            $end_time = '09:00';
                        }
                    }
                }
            }

            #update_user_meta($_POST['userid'], 'pv_user_schedules', $schedule);

            wp_send_json_success($schedule);
        }
    }
    wp_send_json_error();
}

add_action('wp_ajax_pv_set_user_schedules', 'pv_set_user_schedules');
add_action('wp_ajax_nopriv_pv_set_user_schedules', 'pv_set_user_schedules');
function pv_set_user_schedules()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_set_user_schedules') {
        if (!empty($_POST['userid']) && !empty($_POST['value'])) {
            #$schedule = json_decode($_POST['value'], true);
            update_user_meta($_POST['userid'], 'pv_user_schedules', $_POST['value']);
            wp_send_json_success();
        }
    }
    wp_send_json_error();
}

add_action('wp_ajax_pv_reset_timeline', 'pv_reset_timeline');
add_action('wp_ajax_nopriv_pv_reset_timeline', 'pv_reset_timeline');
function pv_reset_timeline()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_reset_timeline') {
        echo "Möchtest du die Job-Timeline wirklich zurücksetzen?<br>Alle Jobs müssen anschließend wieder neu zugeordnet werden.";
        die();
    }
}

add_action('wp_ajax_pv_reset_timeline_confirmation', 'pv_reset_timeline_confirmation');
add_action('wp_ajax_nopriv_pv_reset_timeline_confirmation', 'pv_reset_timeline_confirmation');
function pv_reset_timeline_confirmation()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_reset_timeline_confirmation') {
        if (!empty($_POST['userid'])) {
            update_user_meta($_POST['userid'], 'pv_user_schedules', '');
            wp_send_json_success(array('url' => $_POST['url']));
        }
    }
    wp_send_json_error();
}
