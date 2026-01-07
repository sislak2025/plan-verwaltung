<?php
$current_user = wp_get_current_user();
acf_form(array('form' => false));

$users_class = new PV_Users();

$html = '';
if (is_array($data['users']) && !empty($data['users'])) {

    $html_users = '';
    $html_current_user = '';
    foreach ($data['users'] as $user) {

        $html_user = '<div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 col-xxl-2">';
        $html_user .= '<div class="card">';
        $html_user .= '<div class="card-header" style="white-space: nowrap; overflow: hidden;">';
        $html_user .= '<div class="pv_user_thumbnail"><img src="' . ((!empty($user->data->user_fields['pv_user_image']['url'])) ? $user->data->user_fields['pv_user_image']['sizes']['thumbnail'] : '/wp-content/uploads/2023/08/cropped-favicon-sislak.png') . '"></div>';
        $html_user .= '<span class="h6 align-middle">' . (in_array('bearbeiter', (array) $current_user->roles) ? '<a href="/benutzer-uebersicht/?user_id=' . $user->data->ID . '">' . $user->data->display_name . '</a>' : $user->data->display_name) . '<span class="pv_header_nickname">' . get_user_meta($user->data->ID, 'nickname', true) . '</span></span>';
        $html_user .= '</div>';
        $html_user .= '<div data-user_id="' . $user->data->ID . '" class="card-body h-80vh pv_job_col' . (in_array('bearbeiter', (array) $current_user->roles) || $user->data->ID == get_current_user_id() ? ' pv_job_col_sort' : '') . '">';

        $html_user_done = '';
        $html_user_hide = '';
        $bearbeitungen = $users_class->get_user_bearbeitungen($user->data->ID, true, ($_GET['suche'] ?? ''), ($_GET['older'] ?? ''));
        if (!empty($bearbeitungen)) {

            $html_user .= '<div class="pv_active_job" id="pv_active_jobs_' . $user->data->ID . '">';

            if (in_array('bearbeiter', (array) $current_user->roles) || $user->data->ID == $current_user->ID) {
                $html_user .= '<div class="btn-group btn-group-sm mb-2" role="group">';
                $html_user .= '<button type="button" class="btn btn-outline-primary pv_add_job" data-userid="' . $user->data->ID . '"><i class="bi bi-plus-lg"></i><span class="pv-spinner spinner-border ms-1"></span></button>';
                $html_user .= '<button type="button" class="btn btn-outline-primary pv_add_group" data-userid="' . $user->data->ID . '"><i class="bi bi-collection"></i><span class="pv-spinner spinner-border ms-1"></span></button>';
                $html_user .= '<button type="button" class="btn ' . (!empty($_GET['suche']) ? 'btn-primary' : 'btn-outline-primary') . ' pv_filter_button" data-userid="' . $user->data->ID . '" data-action="pv_search_filter"><i class="bi bi-search"></i><span class="pv-spinner spinner-border ms-1"></span></button>';
                $html_user .= '<button type="button" class="btn ' . (!empty($_GET['older']) ? 'btn-primary' : 'btn-outline-primary') . ' pv_filter_button" data-userid="' . $user->data->ID . '" data-action="pv_older_filter"><i class="bi bi-clock"></i><span class="pv-spinner spinner-border ms-1"></span></button>';
                $html_user .= '<button type="button" class="btn ' . (!empty($_COOKIE['pv_choose_filter_users']) ? 'btn-primary' : 'btn-outline-primary') . ' pv_filter_user" value="' . $user->data->ID . '"><i class="bi bi-funnel"></i><span class="pv-spinner spinner-border ms-1"></span></button>';
                $html_user .= '</div>';
            }

            foreach ($bearbeitungen as $index => $bearbeitung) {
                $card = '';

                // Spezieller Job in einer Gruppe
                if (str_starts_with($index, 'group_')) {
                    $group_id = str_replace('group_', '', $index);
                    $card .= '<div data-id="group_' . $group_id . '" class="card mb-2 bg-light-subtle pv_group_card">';
                    $card .= '<div class="card-header h6 fw-bold">' . $bearbeitung['gruppe']['post_title'] . (in_array('bearbeiter', (array) $current_user->roles) || $user->data->ID == $current_user->ID ? '<a href="javascript:void(0);" class="text-decoration-none float-end pv_edit_group px-2" style="font-size: 0.8rem;" data-userid="' . $user->data->ID . '" data-groupid="' . $group_id . '"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>' : '') . '</div>';
                    $card .= '<div class="card-body p-0">';

                    if (!empty($bearbeitung['gruppe']['fields']['pv_group_small_notiz'])) {
                        $card .= '<p class="card-subtitle m-2" style="line-height: 14px; font-size: 14px;"><small class="text-body-secondary">» <span class="text-decoration-underline">' . $bearbeitung['gruppe']['fields']['pv_group_small_notiz'] . '</span></small></p>';
                    }

                    $card .= '<div class="accordion accordion-flush" id="pv-accordion-group-' . $user->data->ID . '-' . $group_id . '">';
                    foreach ($bearbeitung['bearbeitungen'] as $bearbeitung_group) {
                        $private_job_show = false;
                        $private_job = !empty($bearbeitung_group['fields']['pv_private_visible']) ? $bearbeitung_group['fields']['pv_private_visible'] : array();
                        if (in_array('not_visible', $private_job)) {
                            foreach ($bearbeitung_group['fields']['pv_bearbeiter'] as $bearbeiter) {
                                if ($bearbeiter['ID'] == get_current_user_id()) {
                                    $private_job_show = true;
                                    break;
                                }
                            }
                        }

                        if (($bearbeitung_group['fields']['pv_bearbeitung_status'] != 'Fertiggestellt' && !in_array('not_visible', $private_job)) || $private_job_show) {
                            $card .= '<div class="accordion-item' . (!empty($bearbeitung_group['fields']['pv_finish_difference']) && $bearbeitung_group['fields']['pv_finish_difference'] < 2 ? ' pv_job_red' : (!empty($bearbeitung_group['fields']['pv_job_color']) ? ' pv_job_' . $bearbeitung_group['fields']['pv_job_color'] : '')) . '">';
                            $card .= ((in_array('bearbeiter', (array) $current_user->roles) || $user->ID == $current_user->ID) && ($count = count(array_filter(wp_get_post_revisions($bearbeitung_group['ID']), fn($rev) => (int)$rev->post_author !== get_current_user_id()))) > 0) ? '<div class="pv-notification-count-badge">❕</div>' : '';
                            $card .= '<div class="accordion-header"><button class="accordion-button collapsed p-2' . (!empty($bearbeitung_group['fields']['pv_finish_difference']) && $bearbeitung_group['fields']['pv_finish_difference'] < 2 ? ' pv_job_red' : (!empty($bearbeitung_group['fields']['pv_job_color']) ? ' pv_job_' . $bearbeitung_group['fields']['pv_job_color'] : '')) . '" type="button" style="font-size: 0.6rem;" data-bs-toggle="collapse" data-bs-target="#pv-accordion-group-item-' . $user->data->ID . '-' . $group_id . '-' . $bearbeitung_group['ID'] . '" aria-expanded="false" aria-controls="pv-accordion-group-item-' . $user->data->ID . '-' . $group_id . '-' . $bearbeitung_group['ID'] . '"><span>' . (!empty($bearbeitung_group['fields']['pv_jobs']) ? $bearbeitung_group['fields']['pv_jobs'][0]['post_title'] : $bearbeitung_group['post_title']) . '</span>' . (in_array('bearbeiter', (array) $current_user->roles) || $user->data->ID == $current_user->ID ? '<a href="javascript:void(0);" class="text-decoration-none float-end pv_edit_job px-2" style="font-size: 0.8rem;" data-userid="' . $user->data->ID . '" data-jobid="' . $bearbeitung_group['ID'] . '" data-status="' . (in_array('bearbeiter', (array) $current_user->roles) ? 'true' : 'false') . '"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>' : '') . '</button></div>';
                            $card .= '<div id="pv-accordion-group-item-' . $user->data->ID . '-' . $group_id . '-' . $bearbeitung_group['ID'] . '" class="accordion-collapse collapse" data-bs-parent="#pv-accordion-group-' . $user->data->ID . '-' . $group_id . '"><div class="accordion-body p-2">';
                            $card .= pv_card_body_content($bearbeitung_group, $user);
                            $card .= '</div></div>';
                            $card .= '</div>';
                        }
                    }
                    $card .= '</div>';

                    $card .= '</div>';
                    $card .= '</div>';

                    $html_user .= $card;
                }
                // Normaler Job ohne Gruppe
                else {
                    $private_job_show = false;
                    $private_job = !empty($bearbeitung['fields']['pv_private_visible']) ? $bearbeitung['fields']['pv_private_visible'] : array();
                    if (in_array('not_visible', $private_job)) {
                        foreach ($bearbeitung['fields']['pv_bearbeiter'] as $bearbeiter) {
                            if ($bearbeiter['ID'] == get_current_user_id()) {
                                $private_job_show = true;
                                break;
                            }
                        }
                    }

                    if (($bearbeitung['fields']['pv_bearbeitung_status'] != 'Fertiggestellt' && !in_array('not_visible', $private_job)) || $private_job_show) {
                        $card .= '<div data-id="' . $bearbeitung['ID'] . '" class="card mb-2 bg-light-subtle pv_job_card' . ($bearbeitung['fields']['pv_bearbeitung_status'] == 'Geliefert - Abgeschlossen' ? ' pv_job_done' : '') . (!empty($bearbeitung['fields']['pv_finish_difference']) && $bearbeitung['fields']['pv_finish_difference'] < 2 ? ' pv_job_red' : (!empty($bearbeitung['fields']['pv_job_color']) ? ' pv_job_' . $bearbeitung['fields']['pv_job_color'] : '')) . '">';
                        $card .= ((in_array('bearbeiter', (array) $current_user->roles) || $user->ID == $current_user->ID) && ($count = count(array_filter(wp_get_post_revisions($bearbeitung['ID']), fn($rev) => (int)$rev->post_author !== get_current_user_id()))) > 0) ? '<div class="pv-notification-count-badge">❕</div>' : '';
                        $card .= '<div class="card-header" style="font-size: 0.6rem;">' . (!empty($bearbeitung['fields']['pv_jobs']) ? $bearbeitung['fields']['pv_jobs'][0]['post_title'] : 'Individueller Job') . (in_array('bearbeiter', (array) $current_user->roles) || $user->data->ID == $current_user->ID ? '<a href="javascript:void(0);" class="text-decoration-none float-end pv_edit_job px-2" style="font-size: 0.8rem;" data-userid="' . $user->data->ID . '" data-jobid="' . $bearbeitung['ID'] . '" data-status="' . (in_array('bearbeiter', (array) $current_user->roles) ? 'true' : 'false') . '"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>' : '') . '</div>';
                        $card .= '<div class="card-body p-2">';
                        $card .= pv_card_body_content($bearbeitung, $user);
                        $card .= '</div>';
                        $card .= '</div>';

                        if ($bearbeitung['fields']['pv_bearbeitung_status'] == 'Durchführung - bei Kunde' && empty($_GET['suche']) && empty($_GET['older'])) {
                            $html_user_hide .= $card;
                        } else if ($bearbeitung['fields']['pv_bearbeitung_status'] == 'Geliefert - Abgeschlossen' && empty($_GET['suche']) && empty($_GET['older'])) {
                            $html_user_done .= $card;
                        } else {
                            $html_user .= $card;
                        }
                    }
                }
            }
            $html_user .= '</div>';
        }

        if (in_array('bearbeiter', (array) $current_user->roles) || $user->data->ID == $current_user->ID) {
            $html_user .= '<div class="card mb-2 bg-light-subtle">';
            $html_user .= '<a href="javascript:void(0);" class="card-body p-2 text-center card-link text-primary text-decoration-none pv_add_job" data-userid="' . $user->data->ID . '"  data-status="false">Job hinzufügen<span class="pv-spinner spinner-border ms-1"></span></a>';
            $html_user .= '</div>';
        }

        if (!empty($html_user_hide)) {
            $html_user .= '<p class="pt-2 mb-2 text-center"><a class="card-link text-decoration-none mb-2 pv_show_done_job" data-bs-toggle="collapse" href="#pv_hide_jobs_' . $user->data->ID . '" role="button" aria-expanded="false" aria-controls="pv_hide_jobs_' . $user->data->ID . '">Jobs bei Kunde anzeigen</a></p>';
            $html_user .= '<div class="collapse pv_done_job" id="pv_hide_jobs_' . $user->data->ID . '">';
            $html_user .= $html_user_hide;
            $html_user .= '</div>';
        }

        if (!empty($html_user_done)) {
            $html_user .= '<p class="pt-2 mb-2 text-center"><a class="card-link text-decoration-none mb-2 pv_show_done_job" data-bs-toggle="collapse" href="#pv_done_jobs_' . $user->data->ID . '" role="button" aria-expanded="false" aria-controls="pv_done_jobs_' . $user->data->ID . '">Abgeschlossene Jobs anzeigen</a></p>';
            $html_user .= '<div class="collapse pv_done_job" id="pv_done_jobs_' . $user->data->ID . '">';
            $html_user .= $html_user_done;
            $html_user .= '</div>';
        }

        $html_user .= '</div>';
        $html_user .= '</div>';
        $html_user .= '</div>';

        //if ($user->data->ID == $current_user->ID && !in_array('bearbeiter', (array) $current_user->roles)) {
        if ($user->data->ID == $current_user->ID) {
            $html_current_user = $html_user;
        } else {
            $html_users .= $html_user;
        }
    }

    $html .= '<div id="pv-plantable" class="container-fluid p-1">';
    $html .= '<div class="row g-2 flex-row flex-nowrap pv-keep-scroll-position">';

    $html .= $html_current_user;
    $html .= $html_users;

    $html .= '<div class="modal" id="pv_modal" tabindex="-1" data-bs-backdrop="static" data-bs-focus="true">';
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

    $html .= '<div class="modal" id="pv_tracking_modal" tabindex="-1" data-bs-backdrop="static" data-bs-focus="true">';
    $html .= '<div class="modal-dialog modal-dialog-centered modal-lg">';
    $html .= '<div class="modal-content">';
    $html .= '<div class="modal-header">';
    $html .= '<h5 class="modal-title pv_tracking_modal_title">Tracking hinzufügen</h5>';
    $html .= '<button type="button" class="btn-close" data-bs-target="#pv_modal" data-bs-toggle="modal" aria-label="Close"></button>';
    $html .= '</div>';
    $html .= '<div class="modal-body">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '</div>';
    $html .= '</div>';
}
echo $html;

function pv_card_body_content($bearbeitung, $user)
{
    $card = "";
    $card .= '<p class="card-title h6 fw-bold mb-1">' . $bearbeitung['post_title'] . '</p>';
    if (!empty($bearbeitung['fields']['pv_bearbeitung_status'])) {
        $card .= '<p class="card-subtitle mt-1 mb-2 d-inline" style="line-height: 14px; font-size: 14px;"><small class="text-body-secondary">' . (str_contains($bearbeitung['fields']['pv_bearbeitung_status'], ' - ') ? explode(' - ', $bearbeitung['fields']['pv_bearbeitung_status'])[1] : $bearbeitung['fields']['pv_bearbeitung_status']) . '</small></p>';
    }
    if (!empty($bearbeitung['fields']['pv_finish_date'])) {
        $card .= '<p class="card-subtitle d-inline" style="line-height: 14px; font-size: 14px;"><small class="text-body-secondary"> bis <mark>' . DateTime::createFromFormat('d/m/Y', $bearbeitung['fields']['pv_finish_date'])->format('d.m.Y') . '</mark></small></p>';
    }
    if (!empty($bearbeitung['fields']['pv_small_notiz'])) {
        $card .= '<p class="card-subtitle mt-0 mb-2" style="line-height: 14px; font-size: 14px;"><small class="text-body-secondary">» <span class="text-decoration-underline">' . $bearbeitung['fields']['pv_small_notiz'] . '</span></small></p>';
    }
    if (!empty($bearbeitung['fields']['pv_bearbeiter'])) {
        $card .= '<div class="pv_mitbearbeiter_section">';
        foreach ($bearbeitung['fields']['pv_bearbeiter'] as $bearbeiter) {
            if ($bearbeiter['ID'] != $user->data->ID) {
                $user_image_url = get_field('pv_user_image', 'user_' . $bearbeiter['ID']);
                if (!empty($user_image_url)) {
                    $user_image_url = $user_image_url['sizes']['thumbnail'];
                } else {
                    $user_image_url = '/wp-content/uploads/2023/08/cropped-favicon-sislak.png';
                }
                $card .= '<div class="pv_user_thumbnail"><img src="' . $user_image_url . '"></div>';
                $card .= '<p class="card-subtitle mt-1 mb-1 me-2 d-inline" style="line-height: 14px; font-size: 14px;"><small class="text-body-secondary">' . $bearbeiter['display_name'] . '</small></p>';
            }
        }
        $card .= '</div>';
    }
    if (!empty($bearbeitung['fields']['pv_job_bestandteile'])) {
        foreach ($bearbeitung['fields']['pv_job_bestandteile'] as $bestandteil) {
            $card .= '<p class="card-subtitle mt-1 mb-1" style="line-height: 14px; font-size: 14px;"><small class="text-body-secondary">' . ($bestandteil['pv_job_bestandteile_done'] ? '✓' : '☓') . ' ' . $bestandteil['pv_job_bestandteile_text'] . '</small></p>';
        }
    }
    return $card;
}
