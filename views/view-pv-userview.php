<?php
global $wp;
$current_url = home_url($wp->request);
acf_form(array('form' => false));

$html = '';
if (is_array($data['user']) && !empty($data['user'])) {
    $html .= '<div id="pv-overview" class="container-fluid p-1">';

    $html .= '<div class="row g-2 mb-2">';
    $html .= '<div class="col-12">';
    $html .= '<div class="card pv-timeline-card">';
    $html .= '<div class="card-header h5 d-flex align-items-center justify-content-between">Job-Timeline von ' . $data['user']['display_name'] . '<button type="button" class="btn btn-danger pv_reset_timeline" data-userid="' . $data['user']['ID'] . '">Zurücksetzen <span class="pv-spinner spinner-border"></span></button></div>';
    $html .= '<div class="card-body p-0">';
    $html .= '<div class="row g-2">';
    $html .= '<div class="col-12">';
    $html .= '<div id="pv-time-schedule" data-userid="' . $data['user']['ID'] . '" class="jq-schedule-plus"></div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="row g-2">';
    $html .= '<div class="col-12 col-xxl-4">';
    $html .= generate_status_card($data, array('Vorbereitung', 'Durchführung - in Bearbeitung', 'Durchführung - Layout', 'Durchführung - Korrektur'), 'Jobs in Durchführung');
    $html .= '</div>';
    $html .= '<div class="col-12 col-xxl-4">';
    $html .= generate_status_card($data, array('Durchführung - in Druck', 'Durchführung - bei Kunde', 'Durchführung - in Abstimmung mit Kontakter', 'Durchführung - bei Freelancer', 'Durchführung - RZ/Livegang'), 'Erledigte Jobs');
    $html .= '</div>';
    $html .= '<div class="col-12 col-xxl-4">';
    $html .= generate_status_card($data, array('Geliefert - Abgeschlossen'), 'Abgeschlossene Jobs');
    $html .= '</div>';
    $html .= '</div>';

    $html .= '</div>';

    $html .= '<div class="modal" id="pv_modal" tabindex="-1" data-bs-backdrop="static">';
    $html .= '<div class="modal-dialog modal-dialog-centered modal-lg">';
    $html .= '<div class="modal-content">';
    $html .= '<div class="modal-header">';
    $html .= '<h5 class="modal-title pv_modal_title">Job hinzufügen</h5>';
    $html .= '<button type="button" class="btn btn-link p-0 ms-2 pv_share_job d-none" title="Link kopieren" aria-label="Link kopieren"><i class="bi bi-share"></i></button>';
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
    $html .= '</div>';
    $html .= '<div class="modal-body">';
    $html .= '</div>';
    $html .= '<div class="modal-footer" style="display: none;">';
    $html .= '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbruch</button>';
    $html .= '<button type="button" class="btn btn-primary pv_reset_timeline_confirmation">Zurücksetzen <span class="pv-spinner spinner-border"></span></button>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '</div>';
}
echo $html;

function generate_status_card($data, $status, $status_headline)
{
    $users_class = new PV_Users();
    $current_user = wp_get_current_user();

    $html = '';
    $html .= '<div class="card pv-status-card">';
    $html .= '<div class="card-header h5">' . $status_headline . '</div>';
    $html .= '<div class="list-group list-group-flush h-80vh">';

    $bearbeitungen = $users_class->get_user_bearbeitungen($data['user']['ID']);
    if (!empty($bearbeitungen)) {
        foreach ($bearbeitungen as $bearbeitung) {
            $private_job = !empty($bearbeitung['fields']['pv_private_visible']) ? $bearbeitung['fields']['pv_private_visible'] : array();
            if (in_array($bearbeitung['fields']['pv_bearbeitung_status'], $status) && !in_array('not_visible', $private_job)) {
                //if ((empty($bearbeitung['fields']['pv_bearbeitung_status']) || $bearbeitung['fields']['pv_bearbeitung_status'] == $status) && !in_array('not_visible', $private_job)) {
                $html .= '<div class="list-group-item pv-job-list-item" data-postid="' . $bearbeitung['ID'] . '">';
                $html .= '<div class="row"><div class="col-7">';
                $html .= !empty($bearbeitung['fields']['pv_jobs'][0]['post_title']) ? $bearbeitung['fields']['pv_jobs'][0]['post_title'] : $bearbeitung['post_title'];
                $html .= '</div>';
                $html .= '<div class="col-4">';
                $html .= !empty($bearbeitung['fields']['pv_bearbeitung_status']) ? $bearbeitung['fields']['pv_bearbeitung_status'] : 'Kein Status';
                #$html .= !empty($bearbeitung['fields']['pv_finish_date']) ? 'Frist: ' . DateTime::createFromFormat('d/m/Y', $bearbeitung['fields']['pv_finish_date'])->format('d.m.Y') : 'Keine Frist';
                $html .= '</div>';
                $html .= '<div class="col-1">';
                $html .= '<a href="javascript:void(0);" class="text-decoration-none float-end pv_edit_job" data-userid="' . $data['user']['ID'] . '" data-jobid="' . $bearbeitung['ID'] . '"  data-status="' . (in_array('bearbeiter', (array) $current_user->roles) ? 'true' : 'false') . '"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>';
                $html .= '</div></div>';
                $html .= '</div>';
            }
        }
    }
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}
