<?php
// Wenn ein Job auf ACF Field Status -> Abgeschlossen gesetzt weird
add_action('acf/save_post', 'pv_on_post_save', 5, 1);
function pv_on_post_save($post_id)
{
    $users_class = new PV_Users();
    $users = $users_class->get_all_users(false);

    $post = get_post($post_id);
    $post_type = get_post_type($post_id);
    if ($post_type === 'bearbeitungen') {
        if (!empty($_POST['acf'])) {
            $old_val = get_field('pv_bearbeitung_status', $post_id);
            $new_val = $_POST['acf']['pv_bearbeitung_status'];
            if ($old_val != $new_val && $new_val == 'Geliefert - Abgeschlossen') {
                $emails = array();
                foreach ($users as $user) {
                    if (in_array('bearbeiter', (array) $user->roles)) {
                        $emails[] = $user->user_email;
                    }
                }
                if (!empty($emails)) {
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    $content = 'Hallo Administrator,<br><br>
                    es wurde ein neuer Job als abgeschlossen markiert!<br><br>
                    <strong>' . $post->post_title . '</strong><br><br>
                    Öffne die Job-Übersicht und melde dich an, um diesen fertigzustellen:<br><a href="' . get_site_url() . '/job-uebersicht/" target="_blank">' . get_site_url() . '/job-uebersicht/</a><br><br>
                    Diese Mitteilung wurde automatisch erstellt!<br>
                    Sislak Design Werbeagentur GmbH';
                    wp_mail($emails, 'Neuer Job abgeschlossen', $content, $headers);
                }
            }
        }
    }
}

add_action('transition_post_status', 'pv_on_post_publish', 10, 3);
function pv_on_post_publish($new_status, $old_status, $post)
{
    if ('publish' !== $new_status || 'publish' === $old_status || 'bearbeitungen' !== get_post_type($post))
        return;

    if (!empty($_POST)) {
        $bearbeiter = $_POST['acf']['pv_bearbeiter'];
        if (!empty($bearbeiter)) {
            foreach ($bearbeiter as $user_id) {
                $user_info = get_userdata($user_id);
                $user_email = $user_info->user_email;
                if (!empty($user_email)) {
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    $content = 'Hallo ' . $user_info->display_name . ',<br><br>
                    es wurde ein neuer Job in deinem Dashboard hinzugefügt!<br><br>
                    <strong>' . $post->post_title . '</strong><br><br>
                    Öffne das Dashboard und melde dich an, um diesen anzuzeigen:<br><a href="' . get_site_url() . '" target="_blank">' . get_site_url() . '</a><br><br>
                    Diese Mitteilung wurde automatisch erstellt!<br>
                    Sislak Design Werbeagentur GmbH';
                    wp_mail($user_email, 'Neuer Job für dich hinzugefügt', $content, $headers);
                }
            }
        }
    }
}

add_action('pv_daily_email_not_24h', 'pv_daily_email_not_24h');
function pv_daily_email_not_24h()
{
    $users_class = new PV_Users();
    $users = $users_class->get_all_users(false);

    $date = strtotime('16:00:00 Europe/Berlin');
    if (date('N', $date) < 6) {

        if (!empty($users)) {
            $emails = array();
            foreach ($users as $user) {
                if ((in_array('arbeitskraft', (array) $user->roles) || in_array('bearbeiter', (array) $user->roles)) && !in_array('inaktiv', (array) $user->roles)) {
                    $emails[] = array('email' => $user->user_email, 'name' => $user->display_name);
                }
            }

            if (!empty($emails)) {
                foreach ($emails as $email) {
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    $content = 'Hallo ' . $email['name'] . ',<br><br>
                        das ist eine Erinnerung für dich, deinen Wochenplan zu aktualisieren.<br>
                        Wenn du deine Jobs heute noch nicht aktualisiert hast, bitten wir dich dies jetzt zu tun.<br><br>
                        Öffne das Dashboard und melde dich an:<br><a href="' . get_site_url() . '" target="_blank">' . get_site_url() . '</a><br><br>
                        Diese Mitteilung wurde automatisch erstellt!<br>
                        Sislak Design Werbeagentur GmbH';
                    wp_mail($email['email'], 'Bitte aktualisiere deine Jobs', $content, $headers);
                }
            }
        }
    }
}
