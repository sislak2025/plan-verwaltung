<?php
$html = '';
$html .= '<div id="pv-customer-detail" class="container">';
$html .= '<div class="row g-2">';

if (!is_user_logged_in()) {
    $html .= '<div class="col-12"><div id="message" class="warning"><p>Du musst eingeloggt sein um den Kunden sehen zu können!</p></div></div>';
} else if (!empty($data['customer'])) {
    $customer = $data['customer'];
    $customer_jobs = $customer['pv_jobs_of_kunde'] ?? array();

    if (!is_array($customer_jobs) && !empty($customer_jobs)) {
        $customer_jobs = array($customer_jobs);
    }

    $html .= '<div class="col-12">';
    $html .= '<h1>Kunde: ' . esc_html($customer['post_title']) . '</h1>';
    $html .= '<div class="table-responsive">';
    $html .= '<table class="table table-striped table-bordered">';
    $html .= '<tbody>';
    $html .= '<tr><th scope="row">Kundennummer</th><td>' . esc_html($customer['pv_id'] ?? '') . '</td></tr>';
    $html .= '<tr><th scope="row">Kürzel</th><td>' . esc_html($customer['pv_shortname'] ?? '') . '</td></tr>';
    $html .= '<tr><th scope="row">Prefix</th><td>' . esc_html($customer['pv_prefix'] ?? '') . '</td></tr>';
    $html .= '<tr><th scope="row">Jobs</th><td>';

    if (!empty($customer_jobs)) {
        $job_links = array();
        foreach ($customer_jobs as $job) {
            if (empty($job)) {
                continue;
            }
            $job_id = is_object($job) ? $job->ID : (is_array($job) ? ($job['ID'] ?? 0) : 0);
            if (!$job_id) {
                continue;
            }
            $job_links[] = '<a href="' . esc_url(get_permalink($job_id)) . '">' . esc_html(get_the_title($job_id)) . '</a>';
        }
        $html .= !empty($job_links) ? implode('<br>', $job_links) : 'Keine Jobs verknüpft';
    } else {
        $html .= 'Keine Jobs verknüpft';
    }

    $html .= '</td></tr>';
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</div>';
    $html .= '</div>';
} else {
    $html .= '<div class="col-12"><div id="message" class="warning"><p>Kein Kunde gefunden.</p></div></div>';
}

$html .= '</div>';
$html .= '</div>';
echo $html;
