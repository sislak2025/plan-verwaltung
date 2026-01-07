<?php
global $wp;
$current_url = home_url($wp->request);
$current_user = wp_get_current_user();
acf_form(array('form' => false));

$html = '';
if (!is_user_logged_in()) {
    $html .= '<div id="pv-overview" class="container-fluid"><div class="row g-2"><div class="col-12"><div id="message" class="warning"><p>Du musst eingeloggt sein um eine Job-Anfrage senden zu können!</p></div></div></div></div>';
} else {
    $html .= '<div id="pv-overview" class="container-fluid p-1">';

    $html .= '<div class="row g-2">';
    $html .= '<div class="col-12 col-xxl-6">';

    $html .= '<div class="card pv-request-card">';
    $html .= '<div class="card-header h5">Meine Job-Anfragen</div>';
    $html .= '<div class="card-body h-80vh p-0"">';

    $html .= '<div class="card m-2 bg-light-subtle"><a href="javascript:void(0);" class="card-body p-2 text-center card-link text-primary text-decoration-none pv_jobrequest_action" data-action="new" data-userid="' . $current_user->ID . '">Job-Anfrage starten<span class="pv-spinner spinner-border ms-1"></span></a></div>';
    if (!empty($data['user_job_requests'])) {
        $html .= '<div class="list-group list-group-flush">';
        foreach ($data['user_job_requests'] as $user_job_requests) {
            $html .= '<div class="list-group-item pv_jobrequest_list_item" data-postid="' . $user_job_requests['ID'] . '">';
            $html .= '<div class="row align-items-center">';
            $html .= '<div class="col-7">';
            $html .= '<span>' . $user_job_requests['post_title'] . '</span>';
            $html .= '</div>';
            $html .= '<div class="col-5 text-end">';
            $html .= ($user_job_requests['post_status'] == 'publish' ? 'Angefragt' : ($user_job_requests['post_status'] == 'angenommen' ? 'Angenommen' : 'Abgelehnt'));
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
    } else {
        $html .= '<div class="p-2">Keine angefragten Jobs</div>';
    }

    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    if (in_array('bearbeiter', (array) $current_user->roles)) {
        $html .= '<div class="col-12 col-xxl-6">';

        $html .= '<div class="row g-2">';
        $html .= '<div class="col-12">';

        $html .= '<div class="card pv-request-card">';
        $html .= '<div class="card-header h5">Angefragte Jobs</div>';
        $html .= '<div class="card-body p-0" style="height: 40vh; overflow-y: auto;">';

        if (!empty($data['job_requests'])) {
            $html .= '<div class="list-group list-group-flush">';
            foreach ($data['job_requests'] as $job_request) {
                $html .= '<div class="list-group-item pv_jobrequest_list_item" data-postid="' . $job_request['ID'] . '">';
                $html .= '<div class="row align-items-center">';
                $html .= '<div class="col-7">';
                $html .= '<span>' . $job_request['post_title'] . '</span>';
                $html .= '</div>';
                $html .= '<div class="col-5 text-end">';
                $html .= '<button type="button" class="pv_jobrequest_action btn btn-sm btn-danger me-1" data-action="refuse" data-requestid="' . $job_request['ID'] . '">Ablehnen<span class="pv-spinner spinner-border ms-1"></span></button>';
                $html .= '<button type="button" class="pv_jobrequest_action btn btn-sm btn-primary" data-action="accept" data-requestid="' . $job_request['ID'] . '">Annehmen<span class="pv-spinner spinner-border ms-1"></span></button>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        } else {
            $html .= '<div class="p-2">Keine angefragten Jobs</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';
        $html .= '<div class="col-12">';

        $html .= '<div class="card pv-request-card">';
        $html .= '<div class="card-header h5">Bearbeitete Jobs</div>';
        $html .= '<div class="card-body p-0" style="height: 40vh; overflow-y: auto;">';

        if (!empty($data['edited_requests'])) {
            $html .= '<div class="list-group list-group-flush">';
            foreach ($data['edited_requests'] as $edited_request) {
                $html .= '<div class="list-group-item pv_jobrequest_list_item" data-postid="' . $edited_request['ID'] . '">';
                $html .= '<div class="row align-items-center">';
                $html .= '<div class="col-7">';
                $html .= '<span>' . $edited_request['post_title'] . '</span>';
                $html .= '</div>';
                $html .= '<div class="col-5 text-end">';
                $html .= $edited_request['post_status'] == 'angenommen' ? 'Angenommen' : 'Abgelehnt';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        } else {
            $html .= '<div class="p-2">Keine bearbeiteten Jobs</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';


        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="modal" id="pv_modal" tabindex="-1" data-bs-backdrop="static" data-bs-focus="false">';
    $html .= '<div class="modal-dialog modal-dialog-centered modal-lg">';
    $html .= '<div class="modal-content">';
    $html .= '<div class="modal-header">';
    $html .= '<h5 class="modal-title pv_modal_title">Job-Anfrage bearbeiten</h5>';
    $html .= '<button type="button" class="btn btn-link p-0 me-2 pv_share_job d-none" title="Link kopieren" aria-label="Link kopieren"><i class="bi bi-share"></i></button>';
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
    $html .= '</div>';
    $html .= '<div class="modal-body">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
}
echo $html;
