<?php
global $wp;
$current_url = home_url($wp->request);
$current_user = wp_get_current_user();
acf_form(array('form' => false));

$html = '';
$html .= '<div id="pv-passwords" class="container">';
$html .= '<div class="row g-2">';
if (!is_user_logged_in()) {
    $html .= '<div class="col-12"><div id="message" class="warning"><p>Du musst eingeloggt sein um die Job-Übersicht sehen zu können!</p></div></div>';
} else if (is_array($data['passwords']) && !empty($data['passwords'])) {
    #print_r($data['passwords']);
    $html .= '<h1>Passwortliste</h1>';
    $html .= '<div class="d-flex"><button type="button" class="btn btn-primary pv_add_password">Neues Passwort<span class="pv-spinner spinner-border ms-1"></span></button></div>';
    $html .= '<div class="table-responsive">';
    $html .= '<table id="pv-passwords-table" class="table table-striped table-bordered nowrap" style="width:100%; display: none;">';
    $html .= '<thead><tr><th>Kunde</th><th>Domain</th><th>Benutzername</th><th>Passwort</th><th>Bearbeiten</th></tr></thead><tbody>';
    $html .= '</tbody></table>';
    $html .= '</div>';

    $html .= '<div class="modal" id="pv_modal" tabindex="-1" data-bs-backdrop="static">';
    $html .= '<div class="modal-dialog modal-dialog-centered modal-lg">';
    $html .= '<div class="modal-content">';
    $html .= '<div class="modal-header">';
    $html .= '<h5 class="modal-title">Job hinzufügen</h5>';
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
    $html .= '</div>';
    $html .= '<div class="modal-body">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
}
$html .= '</div>';
$html .= '</div>';
echo $html;
