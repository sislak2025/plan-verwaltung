<?php
global $wp;
$current_url = home_url($wp->request);
$current_user = wp_get_current_user();
acf_form(array('form' => false));

$html = '';
if (!is_user_logged_in()) {
    $html .= '<div id="pv-overview" class="container-fluid"><div class="row g-2"><div class="col-12"><div id="message" class="warning"><p>Du musst eingeloggt sein um die Job-Übersicht sehen zu können!</p></div></div></div></div>';
} else if (!in_array('bearbeiter', (array) $current_user->roles)) {
    $html .= '<div id="pv-overview" class="container-fluid"><div class="row g-2"><div class="col-12"><div id="message" class="warning"><p>Du hast keine Berechtigung die Job-Übersicht sehen zu können!</p></div></div></div></div>';
} else if (is_array($data['users']) && !empty($data['users'])) {
    $html .= '<div id="pv-overview" class="container-fluid p-1">';

    $html .= '<div class="row g-2">';
    $html .= '<div class="col-12 col-xxl-4">';
    $html .= generate_status_card($data, 'Geliefert - Abgeschlossen');
    $html .= '</div>';
    $html .= '<div class="col-12 col-xxl-4">';
    $html .= generate_status_card($data, 'Durchführung - bei Kunde');
    $html .= '</div>';
    $html .= '<div class="col-12 col-xxl-4">';
    $html .= generate_status_card($data, 'Durchführung - in Abstimmung mit Kontakter');
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="modal" id="pv_modal" tabindex="-1" data-bs-backdrop="static">';
    $html .= '<div class="modal-dialog modal-dialog-centered modal-lg">';
    $html .= '<div class="modal-content">';
    $html .= '<div class="modal-header">';
    $html .= '<h5 class="modal-title">Job hinzufügen</h5>';
    $html .= '<button type="button" class="btn btn-link p-0 me-2 pv_share_job d-none" title="Link kopieren" aria-label="Link kopieren"><i class="bi bi-share"></i></button>';
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
    $html .= '</div>';
    $html .= '<div class="modal-body">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '</div>';
}
echo $html;

function generate_status_card($data, $status)
{
    $users_class = new PV_Users();

    $posttype_class = new PV_Posttype();
    $status_dorpdown_array = $posttype_class->get_projektstatus();

    $status_dorpdown = '';
    if (!empty($status_dorpdown_array)) {
        foreach ($status_dorpdown_array as $status_key => $status_value) {
            if (is_array($status_value)) {
                $status_dorpdown .= '<optgroup label="' . $status_key . '">';
                foreach ($status_value as $status_val => $status_label) {
                    $status_dorpdown .= '<option value="' . $status_val . '"' . ($status_val == $status ? 'selected' : '') . '>' . $status_label . '</option>';
                }
                $status_dorpdown .= '</optgroup>';
            } else {
                $status_dorpdown .= '<option value="' . $status_key . '"' . ($status_key == $status ? 'selected' : '') . '>' . $status_value . '</option>';
            }
        }
    }


    $html = '';
    $html .= '<div class="card pv-status-card">';
    $html .= '<div class="card-header h5">' . $status . '</div>';
    $html .= '<div class="card-body h-80vh">';
    $html .= '<div class="row g-2">';

    foreach ($data['users'] as $user) {
        $html_user = '<div class="col-12">';
        $html_user .= '<div class="card">';
        $html_user .= '<p class="card-header h5">' . $user->data->display_name . '</p>';

        $bearbeitungen = $users_class->get_user_bearbeitungen($user->data->ID);
        $bearbeitungen_html = '';
        if (!empty($bearbeitungen)) {
            foreach ($bearbeitungen as $bearbeitung) {
                #print_r($bearbeitung);
                $private_job = !empty($bearbeitung['fields']['pv_private_visible']) ? $bearbeitung['fields']['pv_private_visible'] : array();
                if (($bearbeitung['fields']['pv_bearbeitung_status'] == $status) && !in_array('not_visible', $private_job)) {
                    //if ((empty($bearbeitung['fields']['pv_bearbeitung_status']) || $bearbeitung['fields']['pv_bearbeitung_status'] == $status) && !in_array('not_visible', $private_job)) {
                    $bearbeitungen_html .= '<div class="list-group-item pv-job-list-item" data-postid="' . $bearbeitung['ID'] . '">';
                    $bearbeitungen_html .= '<div class="row"><div class="col-7">';
                    $bearbeitungen_html .= !empty($bearbeitung['fields']['pv_jobs'][0]['post_title']) ? $bearbeitung['fields']['pv_jobs'][0]['post_title'] : $bearbeitung['post_title'];
                    $bearbeitungen_html .= '</div>';
                    // $bearbeitungen_html .= '<div class="col-6">';
                    // $bearbeitungen_html .= !empty($bearbeitung['fields']['pv_bearbeitung_status']) ? $bearbeitung['fields']['pv_bearbeitung_status'] : 'Kein Status';
                    // $bearbeitungen_html .= '</div>';
                    $bearbeitungen_html .= '<div class="col-4">';
                    if ($status == 'Geliefert - Abgeschlossen') {
                        $bearbeitungen_html .= !empty($status_dorpdown) ? '<select class="pv_job_list_select" data-postid="' . $bearbeitung['ID'] . '" data-name="pv_bearbeitung_status">' . $status_dorpdown . '</select>' : '';
                    } else {
                        $bearbeitungen_html .= !empty($bearbeitung['fields']['pv_finish_date']) ? 'Frist: ' . DateTime::createFromFormat('d/m/Y', $bearbeitung['fields']['pv_finish_date'])->format('d.m.Y') : 'Keine Frist';
                    }
                    $bearbeitungen_html .= '</div>';
                    $bearbeitungen_html .= '<div class="col-1">';
                    $bearbeitungen_html .= '<a href="javascript:void(0);" class="text-decoration-none float-end pv_edit_job" data-userid="' . $user->data->ID . '" data-jobid="' . $bearbeitung['ID'] . '" data-status="true"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>';
                    $bearbeitungen_html .= '</div></div>';
                    $bearbeitungen_html .= '</div>';
                }
            }
            if (!empty($bearbeitungen_html)) {
                $html_user .= '<div class="list-group list-group-flush">';
                $html_user .= $bearbeitungen_html;
                $html_user .= '</div>';
            }
        }
        $html_user .= '</div>';
        $html_user .= '</div>';

        if (!empty($bearbeitungen_html)) {
            $html .= $html_user;
        }
    }
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}
