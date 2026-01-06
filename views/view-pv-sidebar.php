<?php
$current_user = wp_get_current_user();

$html = '';
if (!empty($current_user->ID)) {
    $html .= '<a class="" data-bs-toggle="offcanvas" href="#pv-notification-sidebar" role="button" aria-controls="pv-notification-sidebar">';
    $html .= '<i class="bi bi-bell h3"></i>';
    if (count($data['notifications']) > 0) {
        $html .= '<span class="pv-notification-count-badge">' . count($data['notifications']) . '</span>';
    }
    $html .= '</a>';

    $html .= '<div class="offcanvas offcanvas-start" id="pv-notification-sidebar" aria-labelledby="pv-notification-sidebar-label">';

    $html .= '<div class="offcanvas-header">';
    $html .= '<h5 class="offcanvas-title" id="pv-notification-sidebar-label">Benachrichtigungen</h5>';
    $html .= '<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
    $html .= '</div>';

    $html .= '<div class="offcanvas-body px-0">';

    if (!empty($data['notifications'])) {
        $grouped = ['n' => [], 'u' => []];
        foreach ($data['notifications'] as $n) {
            if (isset($grouped[$n['action']])) {
                $grouped[$n['action']][] = $n;
            }
        }

        $labels = [
            'n' => 'Neue Jobs',
            'u' => 'Aktualisierte Jobs',
        ];

        foreach ($grouped as $type => $notifications) {
            #print_r($notifications);
            if (empty($notifications)) continue;

            // Sortieren
            usort($notifications, fn($a, $b) => strtotime($b['post']->post_date) - strtotime($a['post']->post_date));
            $preview = array_slice($notifications, 0, 3);
            $rest    = array_slice($notifications, 3);
            $collapseId = 'collapse-' . $type;

            $html .= '<div class="card border-start-0 border-end-0 rounded-0 mb-4 pv-card-wrapper">';
            $html .= '<div class="card-header d-flex justify-content-between align-items-center border-bottom-0">';
            $html .= '<div>' . $labels[$type];
            $html .= '<span class="badge bg-danger rounded-pill ms-2">' . count($notifications) . '</span>';
            $html .= '</div>';
            $html .= '<button type="button" class="pv-remove-not-button btn btn-sm btn-outline-danger p-0 px-1" data-action_key="remove" data-action_data="' . $type . '" data-userid="' . get_current_user_id() . '" title="Alle löschen">';
            $html .= '<i class="bi bi-x"></i><span class="pv-spinner spinner-border ms-1"></span>';
            $html .= '</button>';
            $html .= '</div>';

            $html .= '<div class="pv-list-group-not list-group list-group-flush border-top">';

            // Vorschau-Einträge
            foreach ($preview as $notification) {
                $html .= build_notification_item($notification, $type);
            }

            // Versteckte weiteren Einträge
            if (!empty($rest)) {
                $html .= '<div class="collapse" id="' . $collapseId . '">';
                foreach ($rest as $notification) {
                    $html .= build_notification_item($notification, $type);
                }
                $html .= '</div>';

                // "Alle anzeigen"-Link
                $html .= '<a class="list-group-item text-center text-primary text-decoration-underline small" 
                        data-bs-toggle="collapse" href="#' . $collapseId . '" role="button" 
                        aria-expanded="false" aria-controls="' . $collapseId . '">
                        Alle anzeigen
                      </a>';
            }

            $html .= '</div>'; // list-group
            $html .= '</div>'; // card
        }
    } else {
        $html .= '<div class="px-3">Du hast aktuell keine Benachrichtigungen.</div>';
    }

    $html .= '</div>';
    $html .= '</div>';
}
echo $html;

function build_notification_item($notification, $type)
{
    $post = $notification['post'];
    $title = esc_html($post->post_title);
    $time = time_elapsed_string($post->post_date);

    $html = '<div class="pv-not-list-item list-group-item py-3 lh-sm">';

    // Zeile 1: Headline + Zeit
    $html .= '<div class="d-flex justify-content-between align-items-start">';

    if ($type === 'n') {
        $html .= '<div class="fw-semibold">Neues Projekt hinzugefügt!</div>';
    } else {
        $user = get_user_by('ID', $notification['edited_by']);
        $name = esc_html($user->data->display_name ?? 'Unbekannt');
        $html .= '<div class="fw-semibold">' . $name . ' hat einen Job von dir bearbeitet!</div>';
        $time = time_elapsed_string($post->post_modified);
    }

    $html .= '<div class="text-nowrap small text-muted ms-3">' . $time . '</div>';
    $html .= '</div>'; // Zeile 1

    // Zeile 2: Titel & Buttons
    $html .= '<div class="d-flex justify-content-between align-items-center mt-1 flex-nowrap gap-2">';
    $html .= '<div class="small text-muted flex-grow-1" style="word-break: break-all; min-width: 0;">' . $title . '</div>';
    $html .= '<div class="btn-group btn-group-sm flex-shrink-0" role="group">';

    $post_id = '';
    if ($type === 'u') {
        $post_id = $post->ID;
    } else {
        if (!empty($notification['bearbeitung'])) {
            $post_id = $notification['bearbeitung']->ID;
        } else {
            $post_id = $post->ID;
        }
    }

    if (!empty($notification['bearbeitung']) || $type === 'u') {
        $html .= '<button type="button" class="btn btn-outline-primary p-0 px-1 pv_edit_job" data-userid="' . get_current_user_id() . '" data-jobid="' . $post_id . '" title="Job bearbeiten">';
        $html .= '<i class="bi bi-pencil"></i><span class="pv-spinner spinner-border ms-1"></span></button>';
    } else {
        $html .= '<button type="button" class="btn btn-outline-primary p-0 px-1 pv_add_job" data-userid="' . get_current_user_id() . '" data-projektid="' . $post_id . '" title="Neuen Job anlegen">';
        $html .= '<i class="bi bi-plus-lg"></i><span class="pv-spinner spinner-border ms-1"></span></button>';
    }

    $html .= '<button type="button" class="pv-remove-not-button btn btn-outline-danger p-0 px-1" data-action_key="post_id" data-action_data="' . $post->ID . '" data-userid="' . get_current_user_id() . '" title="Benachrichtigung löschen">';
    $html .= '<i class="bi bi-x"></i><span class="pv-spinner spinner-border ms-1"></span></button>';
    $html .= '</div>'; // btn-group
    $html .= '</div>'; // Zeile 2
    $html .= '</div>'; // list-group-item

    return $html;
}

function time_elapsed_string($datetime, $full = false)
{
    $tz = new DateTimeZone('Europe/Berlin');
    $now  = new DateTime('now', $tz);
    $then = new DateTime($datetime, $tz);

    if ($then > $now) {
        return 'Gerade eben';
    }

    $diff = $now->diff($then);

    $units = [
        'y' => ['Jahr',     'Jahren',    'einem'],
        'm' => ['Monat',    'Monaten',   'einem'],
        'd' => ['Tag',      'Tagen',     'einem'],
        'h' => ['Stunde',   'Stunden',   'einer'],
        'i' => ['Minute',   'Minuten',   'einer'],
        's' => ['Sekunde',  'Sekunden',  'einer'],
    ];

    $strings = [];

    foreach ($units as $k => [$singular, $plural, $article]) {
        $value = $diff->$k;
        if ($value > 0) {
            if ($value === 1) {
                $strings[] = "vor $article $singular";
            } else {
                $strings[] = "vor $value $plural";
            }
            if (!$full) break;
        }
    }

    return $strings ? implode(', ', $strings) : 'Gerade eben';
}
