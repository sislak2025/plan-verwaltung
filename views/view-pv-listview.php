<?php
global $wp;
$current_url = home_url($wp->request);
acf_form(array('form' => false));

$html = '';
if (!is_user_logged_in()) {
    $html .= '<div id="pv-overview" class="container-fluid"><div class="row g-2"><div class="col-12"><div id="message" class="warning"><p>Du musst eingeloggt sein um die Job-Liste sehen zu können!</p></div></div></div></div>';
} else if (is_array($data['users']) && !empty($data['users'])) {
    $html .= '<div id="pv-overview" class="container-fluid p-1">';

    $html .= '<div class="row g-2">';
    $html .= '<div class="col-12">';
    $html .= generate_status_card($data);
    $html .= '</div>';
    $html .= '</div>';

$html .= '<div class="modal" id="pv_modal" tabindex="-1" data-bs-backdrop="static" data-bs-focus="false">';
$html .= '<div class="modal-dialog modal-dialog-centered modal-lg">';
$html .= '<div class="modal-content">';
$html .= '<div class="modal-header">';
$html .= '<h5 class="modal-title pv_modal_title">Job hinzufügen</h5>';
$html .= '<button type="button" class="btn btn-link p-0 ms-2 pv_share_job d-none" title="Link kopieren" aria-label="Link kopieren"><i class="bi bi-share"></i></button>';
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

function generate_status_card($data)
{
    $users_class = new PV_Users();
    $current_user = wp_get_current_user();

    $exclude_status = array(
        'Geliefert - Fertiggestellt',
        'Abgebrochen'
    );

    $html = '';
    $html = '<div class="card pv-status-card">';
    $html .= '<div class="card-header h5">Job-Übersicht als Liste</div>';
    $html .= '<div class="card-body h-80vh pv-keep-scroll-position">';
    $html .= '<div class="row g-2">';

    if (!in_array('bearbeiter', (array) $current_user->roles)) {
        foreach ($data['users'] as $key => $user) {
            if ($user->data->ID != get_current_user_id()) {
                unset($data['users'][$key]);
            }
        }
    }

    $html_users = '';
    foreach ($data['users'] as $user) {
        $html_user = '<div class="col-12">';
        $html_user .= '<div class="card pv-listview-user" data-user_id="' . $user->data->ID . '">';
        $html_user .= '<p class="card-header h5">' . (in_array('bearbeiter', (array) $current_user->roles) ? '<a href="/benutzer-uebersicht/?user_id=' . $user->data->ID . '">' . $user->data->display_name . '</a>' : $user->data->display_name) . '</p>';

        $html_user_done = '';
        $html_user_hide = '';
        $bearbeitungen = $users_class->get_user_bearbeitungen($user->data->ID, true, ($_GET['suche'] ?? ''), ($_GET['older'] ?? ''));
        $bearbeitungen_html = '';
        if (!empty($bearbeitungen)) {

            foreach ($bearbeitungen as $index => $bearbeitung) {
                // Gruppierte Jobs
                if (str_starts_with($index, 'group_')) {
                    $group_id = str_replace('group_', '', $index);
                    $gruppe = $bearbeitung['gruppe'];

                    $bearbeitungen_html .= '<div class="list-group-item pv-job-list-item bg-light-subtle pv_group_card" data-postid="group_' . $group_id . '" style="background-color: #f8f8f8 !important">';

                    $bearbeitungen_html .= '<div class="pb-2">';
                    $bearbeitungen_html .= '<div class="row">';
                    $bearbeitungen_html .= '<div class="col-12 col-xl-4">' . $gruppe['post_title'] . '</div>';
                    $bearbeitungen_html .= '<div class="col-6 col-xl-2"></div>';
                    $bearbeitungen_html .= '<div class="col-6 col-xl-2"></div>';
                    $bearbeitungen_html .= '<div class="col-9 col-xl-3">';
                    $bearbeitungen_html .= "<input type='text' id='pv_group_small_notiz_inline_" . $group_id . "' data-postid='" . $group_id . "' data-name='pv_group_small_notiz' class='pv_group_small_notiz_inline' value='" . ($gruppe['fields']['pv_group_small_notiz'] ?? '') . "' maxlength='60' style='width:100%; height:22px;'>";
                    $bearbeitungen_html .= '</div>';
                    $bearbeitungen_html .= '<div class="col-3 col-xl-1">';
                    $bearbeitungen_html .= '<a href="javascript:void(0);" class="text-decoration-none float-end pv_edit_group" data-userid="' . $user->data->ID . '" data-groupid="' . $group_id . '"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>';
                    $bearbeitungen_html .= '</div>';
                    $bearbeitungen_html .= '</div>';
                    $bearbeitungen_html .= '</div>';

                    foreach ($bearbeitung['bearbeitungen'] as $group_job) {
                        $private_job_show = false;
                        $private_job = !empty($group_job['fields']['pv_private_visible']) ? $group_job['fields']['pv_private_visible'] : array();
                        if (in_array('not_visible', $private_job)) {
                            foreach ($group_job['fields']['pv_bearbeiter'] as $bearbeiter) {
                                if ($bearbeiter['ID'] == get_current_user_id()) {
                                    $private_job_show = true;
                                    break;
                                }
                            }
                        }

                        if (($group_job['fields']['pv_bearbeitung_status'] != 'Fertiggestellt' && !in_array('not_visible', $private_job)) || $private_job_show) {
                            $bearbeitungen_html .= '<div class="list-group-item p-0 border-start-0 border-end-0' . (!empty($group_job['fields']['pv_job_color']) ? ' pv_job_' . $group_job['fields']['pv_job_color'] : '') . '" data-postid="' . $group_job['ID'] . '">';
                            $bearbeitungen_html .= '<div class="row">';
                            $bearbeitungen_html .= '<div class="col-12 col-xl-4 ps-0">» ' . $group_job['post_title'] . '</div>';
                            $bearbeitungen_html .= '<div class="col-6 col-xl-2">';
                            $bearbeitungen_html .= !empty(get_status_dropdown_options()) ? '<select class="pv_job_list_select" data-postid="' . $group_job['ID'] . '" data-name="pv_bearbeitung_status" style="width:100%;">' . get_status_dropdown_options($group_job['fields']['pv_bearbeitung_status']) . '</select>' : '';
                            $bearbeitungen_html .= '</div>';
                            $bearbeitungen_html .= '<div class="col-6 col-xl-2">';
                            $bearbeitungen_html .= !empty($group_job['fields']['pv_finish_date']) ? 'Frist: ' . DateTime::createFromFormat('d/m/Y', $group_job['fields']['pv_finish_date'])->format('d.m.Y') : 'Keine Frist';
                            $bearbeitungen_html .= '</div>';
                            $bearbeitungen_html .= '<div class="col-9 col-xl-3">';
                            $bearbeitungen_html .= "<input type='text' id='pv_job_small_notiz_inline_" . $group_job['ID'] . "' data-postid='" . $group_job['ID'] . "' data-name='pv_small_notiz' class='pv_job_small_notiz_inline' value='" . ($group_job['fields']['pv_small_notiz'] ?? '') . "' maxlength='60' style='width:100%; height:22px;'>";
                            $bearbeitungen_html .= '</div>';
                            $bearbeitungen_html .= '<div class="col-3 col-xl-1">';
                            $bearbeitungen_html .= '<a href="javascript:void(0);" class="text-decoration-none float-end pv_edit_job" data-userid="' . $user->data->ID . '" data-jobid="' . $group_job['ID'] . '" data-status="' . (in_array('bearbeiter', (array) $current_user->roles) ? 'true' : 'false') . '"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>';
                            $popover_farbe = "<select class='pv_job_color_select' data-postid='" . $group_job['ID'] . "' data-name='pv_job_color'>" . get_color_dropdown_options($group_job['fields']['pv_job_color']) . "</select>";
                            $bearbeitungen_html .= '<button type="button" class="btn btn-link text-decoration-none pv_popover_link float-end pv_edit_farbe" style="padding-right:9px;" data-bs-toggle="popover" data-bs-placement="left" data-bs-html="true" data-bs-content="' . $popover_farbe . '"><i class="bi bi-palette"></i></button>';
                            $bearbeitungen_html .= '</div>';
                            $bearbeitungen_html .= '</div>';
                            $bearbeitungen_html .= '</div>';
                        }
                    }

                    $bearbeitungen_html .= '</div>';
                }
                // Einzelner Job
                else {
                    $private_job = !empty($bearbeitung['fields']['pv_private_visible']) ? $bearbeitung['fields']['pv_private_visible'] : array();
                    if (!in_array($bearbeitung['fields']['pv_bearbeitung_status'], $exclude_status) && !in_array('not_visible', $private_job)) {
                        //if ((empty($bearbeitung['fields']['pv_bearbeitung_status']) || $bearbeitung['fields']['pv_bearbeitung_status'] == $status) && !in_array('not_visible', $private_job)) {
                        $bearbeitungen_html_item = '<div class="list-group-item pv-job-list-item' . (!empty($bearbeitung['fields']['pv_job_color']) ? ' pv_job_' . $bearbeitung['fields']['pv_job_color'] : '') . '" data-postid="' . $bearbeitung['ID'] . '">';
                        $bearbeitungen_html_item .= '<div class="row"><div class="col-12 col-xl-4">';
                        $bearbeitungen_html_item .= $bearbeitung['post_title'];
                        $bearbeitungen_html_item .= '</div>';

                        $bearbeitungen_html_item .= '<div class="col-6 col-xl-2">';
                        $bearbeitungen_html_item .= !empty(get_status_dropdown_options()) ? '<select class="pv_job_list_select" data-postid="' . $bearbeitung['ID'] . '" data-name="pv_bearbeitung_status" style="width:100%;">' . get_status_dropdown_options($bearbeitung['fields']['pv_bearbeitung_status']) . '</select>' : '';
                        $bearbeitungen_html_item .= '</div>';

                        $bearbeitungen_html_item .= '<div class="col-6 col-xl-2">';
                        $bearbeitungen_html_item .= !empty($bearbeitung['fields']['pv_finish_date']) ? 'Frist: ' . DateTime::createFromFormat('d/m/Y', $bearbeitung['fields']['pv_finish_date'])->format('d.m.Y') : 'Keine Frist';
                        $bearbeitungen_html_item .= '</div>';

                        $bearbeitungen_html_item .= '<div class="col-9 col-xl-3">';
                        $bearbeitungen_html_item .= "<input type='text' id='pv_job_small_notiz_inline_" . $bearbeitung['ID'] . "' data-postid='" . $bearbeitung['ID'] . "' data-name='pv_small_notiz' class='pv_job_small_notiz_inline' value='" . $bearbeitung['fields']['pv_small_notiz'] . "' maxlength='60' style='width:100%; height:22px;'>";
                        $bearbeitungen_html_item .= '</div>';

                        $popover_farbe = "<select class='pv_job_color_select' data-postid='" . $bearbeitung['ID'] . "' data-name='pv_job_color'>" . get_color_dropdown_options($bearbeitung['fields']['pv_job_color']) . "</select>";
                        $bearbeitungen_html_item .= '<div class="col-3 col-xl-1">';
                        $bearbeitungen_html_item .= '<a href="javascript:void(0);" class="text-decoration-none float-end pv_edit_job" data-userid="' . $user->data->ID . '" data-jobid="' . $bearbeitung['ID'] . '" data-status="' . (in_array('bearbeiter', (array) $current_user->roles) ? 'true' : 'false') . '"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>';
                        $bearbeitungen_html_item .= '<button type="button" class="btn btn-link text-decoration-none pv_popover_link float-end pv_edit_farbe" style="padding-right:9px;" data-bs-toggle="popover" data-bs-placement="left" data-bs-html="true" data-bs-content="' . $popover_farbe . '"><i class="bi bi-palette"></i></button>';
                        $bearbeitungen_html_item .= '</div></div>';
                        $bearbeitungen_html_item .= '</div>';

                        if ($bearbeitung['fields']['pv_bearbeitung_status'] == 'Durchführung - bei Kunde' && empty($_GET['suche']) && empty($_GET['older'])) {
                            $html_user_hide .= $bearbeitungen_html_item;
                        } else if ($bearbeitung['fields']['pv_bearbeitung_status'] == 'Geliefert - Abgeschlossen' && empty($_GET['suche']) && empty($_GET['older'])) {
                            $html_user_done .= $bearbeitungen_html_item;
                        } else {
                            $bearbeitungen_html .= $bearbeitungen_html_item;
                        }
                    }
                }
            }

            if (!empty($bearbeitungen_html)) {
                $html_user .= '<div class="list-group list-group-flush pv-job-list-of-user">';
                $html_user .= '<div class="pv_active_job" id="pv_active_jobs_' . $user->data->ID . '">';
                $html_user .= $bearbeitungen_html;
                $html_user .= '</div>';

                if (!empty($html_user_hide)) {
                    $html_user .= '<div class="list-group-item"><a class="card-link text-decoration-none mb-2 pv_show_done_job" data-bs-toggle="collapse" href="#pv_hide_jobs_' . $user->data->ID . '" role="button" aria-expanded="false" aria-controls="pv_hide_jobs_' . $user->data->ID . '">Jobs bei Kunde anzeigen</a></div>';
                    $html_user .= '<div class="collapse pv_done_job" id="pv_hide_jobs_' . $user->data->ID . '">';
                    $html_user .= $html_user_hide;
                    $html_user .= '</div>';
                }

                if (!empty($html_user_done)) {
                    $html_user .= '<div class="list-group-item"><a class="card-link text-decoration-none mb-2 pv_show_done_job" data-bs-toggle="collapse" href="#pv_done_jobs_' . $user->data->ID . '" role="button" aria-expanded="false" aria-controls="pv_done_jobs_' . $user->data->ID . '">Abgeschlossene Jobs anzeigen</a></div>';
                    $html_user .= '<div class="collapse pv_done_job" id="pv_done_jobs_' . $user->data->ID . '">';
                    $html_user .= $html_user_done;
                    $html_user .= '</div>';
                }

                if (in_array('bearbeiter', (array) $current_user->roles) || $user->data->ID == $current_user->ID) {
                    $html_user .= '<div class="list-group-item pv-job-list-item_add-new">';
                    $html_user .= '<a href="javascript:void(0);" class="text-primary text-decoration-none pv_add_job" data-userid="' . $user->data->ID . '" data-status="false">Job hinzufügen<span class="pv-spinner spinner-border ms-1"></span></a>';
                    $html_user .= '</div>';
                }
                
                $html_user .= '</div>';
            }
        }
        $html_user .= '</div>';
        $html_user .= '</div>';

        if (!empty($bearbeitungen_html)) {
            if ($user->data->ID == $current_user->ID) {
                $html_users = $html_user . $html_users;
            } else {
                $html_users .= $html_user;
            }
        }
    }
    $html .= $html_users;
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}

function get_status_dropdown_options($status = '')
{
    $posttype_class = new PV_Posttype();
    $status_dorpdown_array = $posttype_class->get_projektstatus();

    $exclude = array();
    $current_user = wp_get_current_user();
    if (!in_array('bearbeiter', (array) $current_user->roles)) {
        $exclude = array(
            'Geliefert - Fertiggestellt',
        );
    }

    $output = '';
    if (!empty($status_dorpdown_array)) {
        foreach ($status_dorpdown_array as $status_key => $status_value) {
            if (is_array($status_value)) {
                $output .= '<optgroup label="' . $status_key . '">';
                foreach ($status_value as $status_val => $status_label) {
                    if (!in_array($status_val, $exclude)) {
                        $output .= '<option value="' . $status_val . '"' . ($status_val == $status ? 'selected' : '') . '>' . $status_label . '</option>';
                    }
                }
                $output .= '</optgroup>';
            } else if (!in_array($status_key, $exclude)) {
                $output .= '<option value="' . $status_key . '"' . ($status_key == $status ? 'selected' : '') . '>' . $status_value . '</option>';
            }
        }
    }
    return $output;
}

function get_color_dropdown_options($color = '')
{
    $color_dorpdown_array = array(
        'keine' => 'In Pipeline',
        'red' => 'Prio Job',
        'orange' => 'Standard Job',
        'green' => 'Bei Kunde, in Druck, Fertiggestellt für Termin',
        'blue' => 'Warten auf Infos vom Kunden',
        'purple' => 'In Abstimmung mit Kontakter'
    );

    $color_dorpdown = array();
    if (!empty($color_dorpdown_array)) {
        foreach ($color_dorpdown_array as $key => $value) {
            $color_dorpdown[] = "<option class='pv_job_" . $key . "' value='" . $key . "'" . ($key == $color ? 'selected' : '') . ">" . $value . "</option>";
        }
    }
    return implode('', $color_dorpdown);
}
