<?php
$current_user = wp_get_current_user();

$html = '';
$html .= '<div id="pv_menu">';
if (!empty($current_user->ID)) {

    $options[] = array('value' => 'all', 'text' => 'Alle anzeigen');
    $options[] = array('value' => 'web', 'text' => 'Web-Abteilung');
    $options[] = array('value' => 'print', 'text' => 'Kreation');
    foreach ($data['users'] as $user) {
        $options[] = array('value' => $user->data->ID, 'text' => $user->data->display_name);
    }
    $html .= '<select id="pv_choose_filter_users" class="form-select">';
    foreach ($options as $option) {
        $html .= '<option value="' . $option['value'] . '"' . ((!empty($_COOKIE['pv_choose_filter_users']) && $_COOKIE['pv_choose_filter_users'] == $option['value']) ? ' selected' : '') . '>' . $option['text'] . '</option>';
    }
    $html .= '</select>';

    $html .= '<div id="pv_user_menu">';
    $html .= '<button id="pv_menu_close_button" class="pv_menu_mobile_button"><img src="' . PV_ADMINISTRATION_URL . 'assets/close-menu.svg' . '" /></button>';
    $html .= '<ul>';
    $html .= '<li>';
    $html .= '<a href="/">Job-Tabelle</a>';
    $html .= '</li>';
    if (in_array('bearbeiter', (array) $current_user->roles)) {
        $html .= '<li>';
        $html .= '<a href="/job-uebersicht/">Job-Übersicht</a>';
        $html .= '</li>';
    }
    $html .= '<li>';
    $html .= '<a href="/job-liste/">Job-Liste</a>';
    $html .= '</li>';
    $html .= '<li>';
    $html .= '<a href="/job-anfrage/">Job-Anfrage</a>';
    $html .= '</li>';
    $html .= '<li>';
    $html .= '<a href="/benutzer-uebersicht/">Benutzer-Übersicht</a>';
    $html .= '</li>';
    $html .= '<li>';
    $html .= '<a href="/profil/">Mein Profil</a>';
    $html .= '</li>';
    $html .= '</ul>';
    $html .= '</div>';
} else {
    $html .= '<div id="pv_user_menu">';
    $html .= '<ul>';
    $html .= '<li>';
    $html .= '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pv_login_modal">Einloggen</button>';
    $html .= '</li>';
    $html .= '</ul>';
    $html .= '</div>';

    $html .= '<div class="modal" id="pv_login_modal" tabindex="-1">';
    $html .= '<div class="modal-dialog modal-dialog-centered">';
    $html .= '<div class="modal-content">';
    $html .= '<div class="modal-header">';
    $html .= '<h5 class="modal-title">Einloggen</h5>';
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
    $html .= '</div>';
    $html .= '<div class="modal-body">';
    $html .= wp_login_form(array('echo' => false, 'remember' => false));
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
}

$html .= '<button id="pv_menu_open_button" class="pv_menu_mobile_button"><img src="' . PV_ADMINISTRATION_URL . 'assets/burger-menu.svg' . '" /></button>';

$html .= '<div id="pv_user_image">';
$user_image = !empty(get_field('pv_user_image', 'user_' . $current_user->ID)) ? get_field('pv_user_image', 'user_' . $current_user->ID)['url'] : '';
$html .= '<img src="' . (!empty($user_image) ? $user_image : '/wp-content/uploads/2023/08/cropped-favicon-sislak.png') . '">';
$html .= '</div>';

$html .= '</div>';

echo $html;
