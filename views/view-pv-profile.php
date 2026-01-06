<?php
global $current_user, $error;
wp_get_current_user();

$error = array();
$html = '<div id="pv-profile" class="container">';
$html .= '<div class="row g-2">';
$html .= '<div class="col-12">';
if (!is_user_logged_in()) {
    $html .= '<div id="message" class="warning"><p>Du musst eingeloggt sein um dein Profil sehen zu können!</p></div>';
} else {
    $html .= '<h1>Profil von ' . get_the_author_meta('first_name', $current_user->ID) . ' ' . get_the_author_meta('last_name', $current_user->ID) .  '</h1>';
    if (!empty($_GET['updated'])) {
        if ($_GET['updated'] == 'true') {
            $html .= '<div id="message" class="success"><p>Das Profil wurde aktualisiert!</p></div>';
        }
        if (count($error) > 0) {
            $html .= '<div id="message" class="error"><p>' . implode("<br />", $error) . '</p></div>';
        }
    }
    $html .= '<form method="post" id="pv-user-profile-form" action="' . esc_html(admin_url('admin-post.php')) . '">';

    $html .= '<div class="row mb-3"><div class="col">';
    $html .= '<label for="first-name" class="form-label">Vorname</label><input class="form-control" name="first-name" type="text" id="first-name" value="' . get_the_author_meta('first_name', $current_user->ID) . '" required />';
    $html .= '</div><div class="col">';
    $html .= '<label for="last-name" class="form-label">Nachname</label><input class="form-control" name="last-name" type="text" id="last-name" value="' . get_the_author_meta('last_name', $current_user->ID) . '" required />';
    $html .= '</div></div>';

    $html .= '<div class="mb-3"><label for="email" class="form-label">E-Mail</label><input class="form-control" name="email" type="email" id="email" value="' . get_the_author_meta('user_email', $current_user->ID) . '" required /></div>';

    $html .= '<div class="row mb-3"><div class="col">';
    $html .= '<label for="pass1" class="form-label">Passwort</label><input class="form-control" name="pass1" type="password" id="pass1" />';
    $html .= '</div><div class="col">';
    $html .= '<label for="pass2" class="form-label">Passwort wiederholen</label><input class="form-control" name="pass2" type="password" id="pass2" />';
    $html .= '</div></div>';

    $html .= '<div class="mb-3"><label for="description" class="form-label">Status</label><textarea class="form-control" name="description" id="description" rows="3" cols="50">' . get_the_author_meta('description', $current_user->ID) . '</textarea></div>';

    $options = array(
        'post_id' => 'user_' . $current_user->ID,
        'fields' => array('pv_user_image'),
        'form' => false,
        'updated_message' => '',
        'uploader' => 'wp',
        'honeypot' => false,
        'html_updated_message' => ''
    );
    ob_start();
    acf_form($options);
    $acf_form = ob_get_contents();
    ob_end_clean();
    $html .= '<div class="mb-3 pv-acf_profil_form">' . $acf_form . '</div>';

    $html .= '<div class="mb-3"><input name="updateuser" type="submit" id="updateuser" class="btn btn-primary" value="Speichern" /></div>';
    $html .= wp_nonce_field('update_user_informations', 'pv_update_user');
    $html .= '<input type="hidden" name="action" value="pv_update_user" />';
    $html .= '<input type="hidden" name="url" value="' . get_permalink() . '" />';

    $html .= '</form>';
}
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
echo $html;
