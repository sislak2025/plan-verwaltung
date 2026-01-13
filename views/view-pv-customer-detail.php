<?php
acf_form(array('form' => false));

$html = '';

if (!is_user_logged_in()) {
    $html .= '<div id="pv-customer-detail" class="container-fluid p-1"><div class="row g-2"><div class="col-12"><div id="message" class="warning"><p>Du musst eingeloggt sein um den Kunden sehen zu können!</p></div></div></div></div>';
} else if (!empty($data['customer'])) {
    $customer = $data['customer'];
    $customer_jobs = $data['customer_jobs'] ?? array();
    $customer_bearbeitungen = $data['customer_bearbeitungen'] ?? array();

    $html .= '<div id="pv-customer-detail" class="container-fluid p-1">';
    $html .= '<div class="row g-2 mb-2">';
    $html .= '<div class="col-12">';
    $html .= '<div class="card">';
    $html .= '<div class="card-header h5">Kundendaten</div>';
    $html .= '<div class="card-body">';
    $html .= '<form class="row g-3">';
    $html .= '<div class="col-12 col-md-6 col-xxl-4">';
    $html .= '<label class="form-label">Kunde</label>';
    $html .= '<input type="text" class="form-control" value="' . esc_attr($customer['post_title'] ?? '') . '" readonly>';
    $html .= '</div>';
    $html .= '<div class="col-6 col-xxl-2">';
    $html .= '<label class="form-label">Kundennummer</label>';
    $html .= '<input type="text" class="form-control" value="' . esc_attr($customer['pv_id'] ?? '') . '" readonly>';
    $html .= '</div>';
    $html .= '<div class="col-6 col-xxl-3">';
    $html .= '<label class="form-label">Kürzel</label>';
    $html .= '<input type="text" class="form-control" value="' . esc_attr($customer['pv_shortname'] ?? '') . '" readonly>';
    $html .= '</div>';
    $html .= '<div class="col-12 col-md-6 col-xxl-3">';
    $html .= '<label class="form-label">Prefix</label>';
    $html .= '<input type="text" class="form-control" value="' . esc_attr($customer['pv_prefix'] ?? '') . '" readonly>';
    $html .= '</div>';
    $html .= '<div class="col-12">';
    $html .= '<label class="form-label">Notizen</label>';
    $html .= '<textarea class="form-control" rows="8" readonly>' . esc_textarea($customer['post_content'] ?? '') . '</textarea>';
    $html .= '</div>';
    $html .= '</form>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="row g-2">';
    $html .= '<div class="col-12 col-xxl-4">';
    $html .= generate_customer_job_card($customer_jobs, 'Jobs vom Kunden');
    $html .= '</div>';
    $html .= '<div class="col-12 col-xxl-4">';
    $html .= generate_customer_status_card($customer_bearbeitungen, array('Vorbereitung', 'Durchführung - in Bearbeitung', 'Durchführung - Layout', 'Durchführung - Korrektur'), 'Jobs in Durchführung');
    $html .= '</div>';
    $html .= '<div class="col-12 col-xxl-4">';
    $html .= generate_customer_status_card($customer_bearbeitungen, array('Geliefert - Abgeschlossen'), 'Abgeschlossene Jobs');
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
} else {
    $html .= '<div id="pv-customer-detail" class="container-fluid p-1"><div class="row g-2"><div class="col-12"><div id="message" class="warning"><p>Kein Kunde gefunden.</p></div></div></div></div>';
}

echo $html;

function generate_customer_job_card($jobs, $headline)
{
    $html = '';
    $html .= '<div class="card pv-status-card">';
    $html .= '<div class="card-header h5">' . esc_html($headline) . '</div>';
    $html .= '<div class="list-group list-group-flush h-80vh">';

    if (!empty($jobs)) {
        foreach ($jobs as $job) {
            $job_id = $job['ID'] ?? 0;
            if (!$job_id) {
                continue;
            }
            $job_title = $job['post_title'] ?? get_the_title($job_id);
            $html .= '<div class="list-group-item pv-job-list-item">';
            $html .= '<a class="text-decoration-none" href="' . esc_url(get_permalink($job_id)) . '">';
            $html .= esc_html($job_title);
            $html .= '</a>';
            $html .= '</div>';
        }
    } else {
        $html .= '<div class="list-group-item text-muted">Keine Jobs verknüpft</div>';
    }

    $html .= '</div>';
    $html .= '</div>';
    return $html;
}

function generate_customer_status_card($bearbeitungen, $status, $headline)
{
    $current_user = wp_get_current_user();
    $html = '';
    $html .= '<div class="card pv-status-card">';
    $html .= '<div class="card-header h5">' . esc_html($headline) . '</div>';
    $html .= '<div class="list-group list-group-flush h-80vh">';

    $has_items = false;
    if (!empty($bearbeitungen)) {
        foreach ($bearbeitungen as $bearbeitung) {
            $private_job = !empty($bearbeitung['pv_private_visible']) ? $bearbeitung['pv_private_visible'] : array();
            $current_status = $bearbeitung['pv_bearbeitung_status'] ?? '';
            if (!in_array($current_status, $status, true) || in_array('not_visible', (array) $private_job, true)) {
                continue;
            }

            $job_title = $bearbeitung['post_title'] ?? '';
            if (!empty($bearbeitung['pv_jobs'][0])) {
                $job_item = $bearbeitung['pv_jobs'][0];
                if (is_object($job_item) && !empty($job_item->post_title)) {
                    $job_title = $job_item->post_title;
                } else if (is_array($job_item) && !empty($job_item['post_title'])) {
                    $job_title = $job_item['post_title'];
                }
            }

            $has_items = true;
            $html .= '<div class="list-group-item pv-job-list-item" data-postid="' . esc_attr($bearbeitung['ID'] ?? 0) . '">';
            $html .= '<div class="row">';
            $html .= '<div class="col-7">' . esc_html($job_title) . '</div>';
            $html .= '<div class="col-4 text-end">' . esc_html($current_status ?: 'Kein Status') . '</div>';
            $html .= '<div class="col-1">';
            $html .= '<a href="javascript:void(0);" class="text-decoration-none float-end pv_edit_job" data-userid="' . esc_attr($current_user->ID) . '" data-jobid="' . esc_attr($bearbeitung['ID'] ?? 0) . '" data-status="' . (in_array('bearbeiter', (array) $current_user->roles, true) ? 'true' : 'false') . '"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
    }

    if (!$has_items) {
        $html .= '<div class="list-group-item text-muted">Keine Jobs in diesem Status</div>';
    }

    $html .= '</div>';
    $html .= '</div>';
    return $html;
}
