<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PV_Users')) {
    class PV_Users
    {
        public function __construct()
        {
            add_action('after_setup_theme', array($this, 'disable_admin_bar'));
            add_action('init', array($this, 'add_user_role_caps'));
            add_filter('ajax_query_attachments_args', array($this, 'only_show_user_images'));
        }

        public function add_user_role_caps()
        {
            add_role('bearbeiter', 'Bearbeiter');
            add_role('arbeitskraft', 'Arbeitskraft');
            add_role('inaktiv', 'Inaktiv');

            $bearbeiter = get_role('bearbeiter');
            $bearbeiter->add_cap('upload_files');
            // $bearbeiter->add_cap('read');
            // $bearbeiter->add_cap('edit_posts');
            // $bearbeiter->add_cap('edit_pages');
            // $bearbeiter->add_cap('publish_posts');
            // $bearbeiter->add_cap('publish_pages');

            $arbeitskraft = get_role('arbeitskraft');
            $arbeitskraft->add_cap('upload_files');
            // $arbeitskraft->add_cap('read');
            // $arbeitskraft->add_cap('edit_posts');
            // $arbeitskraft->add_cap('edit_pages');
            // $arbeitskraft->add_cap('publish_posts');
            // $arbeitskraft->add_cap('publish_pages');

            $inaktiv = get_role('inaktiv');
            $inaktiv->add_cap('upload_files');
            // $inaktiv->add_cap('read');
            // $inaktiv->add_cap('edit_posts');
            // $inaktiv->add_cap('edit_pages');
            // $inaktiv->add_cap('publish_posts');
            // $inaktiv->add_cap('publish_pages');
        }

        public function insert_user($data)
        {
            if (!empty($data)) {
                $user_data = array(
                    'user_login' => $data['user_user_login'],
                    'user_email' => $data['user_user_email'],
                    'display_name' => $data['user_display_name'],
                    'nickname' => $data['user_nickname'],
                    'first_name' => $data['user_first_name'],
                    'last_name' => $data['user_last_name'],
                    'role' => $data['user_role'],
                    'user_urno' => $data['user_urno'],
                    'abteilung' => $data['user_abteilung'],
                    'show_admin_bar_front' => false
                );
            }

            if (email_exists($data['user_user_email'])) {
                $user_id = email_exists($data['user_user_email']);
                $user_data['ID'] = $user_id;

                $user = get_userdata($user_id);
                $user_roles = $user->roles;
                if (in_array('administrator', $user_roles, true) || in_array('inaktiv', $user_roles, true) || in_array('bearbeiter', $user_roles, true)) {
                    unset($user_data['role']);
                }

                wp_update_user($user_data);
                update_field('pv_urno_person', $user_data['user_urno'], 'user_' . $user_id);
                update_field('pv_abteilung', $user_data['abteilung'], 'user_' . $user_id);

                if (in_array('administrator', $user_roles, true) && !in_array('bearbeiter', $user_roles, true)) {
                    $user->add_role('bearbeiter');
                }
                if (get_field('pv_user_disabled', 'user_' . $user_id) && !in_array('inaktiv', $user_roles, true)) {
                    $user->add_role('inaktiv');
                }
            } else {
                $user_id = wp_insert_user($user_data);
                if (!is_wp_error($user_id)) {
                    update_field('pv_urno_person', $user_data['user_urno'], 'user_' . $user_id);
                    update_field('pv_abteilung', $user_data['abteilung'], 'user_' . $user_id);
                }
            }
        }

        public function get_all_users($exclude = true, $showall = true, $include_inactive = false)
        {
            $users = array();
            $args = array(
                'role__in' => array('bearbeiter', 'arbeitskraft'),
                'orderby' => 'user_nicename',
                'order' => 'ASC'
            );
            $users = get_users($args);
            if (!empty($users) && $exclude) {
                foreach ($users as $key => $user) {
                    $user_fields = get_fields('user_' . $user->data->ID);
                    $users[$key]->user_fields = $user_fields;

                    $user_active = get_field('pv_user_disabled', 'user_' . $user->data->ID);
                    if ($user_active && !$include_inactive) {
                        unset($users[$key]);
                    }

                    if (!empty($_COOKIE['pv_choose_filter_users']) && $showall) {
                        $abteilung = $users[$key]->user_fields['pv_abteilung'] ?? '';
                        if (!empty($abteilung) && $_COOKIE['pv_choose_filter_users'] == 'web') {
                            if ($abteilung != 'web') {
                                unset($users[$key]);
                            }
                        } else if (!empty($abteilung) && $_COOKIE['pv_choose_filter_users'] == 'print') {
                            if ($abteilung != 'print') {
                                unset($users[$key]);
                            }
                        } else {
                            if ($_COOKIE['pv_choose_filter_users'] != $user->data->ID) {
                                unset($users[$key]);
                            }
                        }
                    }
                }
            }
            return $users;
        }

        public function get_current_user($userid = null)
        {
            $userid = !empty($userid) ? $userid : get_current_user_id();
            if (!empty($userid)) {
                $user = get_user_by('ID', $userid);
                $user_fields = get_fields('user_' . $user->data->ID);
                $user->user_fields = $user_fields;
                $user = $user->to_array();

                return $user;
            }
            return null;
        }

        public function get_user_bearbeitungen($user_id, $grouped = false, $search = '', $min_days_old = 0)
        {
            $allowed_keys = ['post_title', 'pv_notiz'];
            $bearbeitungen = get_field('pv_user_bearbeitung', 'user_' . $user_id);

            if (!empty($bearbeitungen)) {

                // Sortieren
                if (get_option('pv_job_sort_' . $user_id)) {
                    $order = get_option('pv_job_sort_' . $user_id);
                    $sorted = array_flip($order);
                    foreach ($bearbeitungen as $bearbeitung) {
                        $sorted[$bearbeitung->ID] = $bearbeitung;
                    }
                    $bearbeitungen = $sorted;

                    foreach ($sorted as $bearbeitungs_id => $bearbeitung) {
                        if (!is_object($bearbeitungen[$bearbeitungs_id])) {
                            if (!str_starts_with($bearbeitungs_id, 'group_')) {
                                unset($bearbeitungen[$bearbeitungs_id]);
                            }
                            if (str_starts_with($bearbeitungs_id, 'group_') && !is_array($bearbeitungen[$bearbeitungs_id])) {
                                $bearbeitungen[$bearbeitungs_id] = array();
                            }
                        }
                    }
                }

                foreach ($bearbeitungen as $key => $bearbeitung) {
                    if (is_object($bearbeitung)) {
                        $bearbeitung = $bearbeitung->to_array();
                    }

                    if (is_array($bearbeitung) && !empty($bearbeitung)) {
                        $bearbeitungen[$key] = $bearbeitung;
                        $bearbeitungen[$key]['fields'] = get_fields($bearbeitung['ID']);

                        // Erweiterte Gruppenfelder einfügen
                        $group_additional = get_field('pv_group_additional', $bearbeitung['ID']);
                        foreach ($group_additional as $group_additional_key => $group_additional_value) {
                            if (!empty($group_additional_key)) {
                                $bearbeitungen[$key]['fields'][$group_additional_key] = $group_additional_value;
                            }
                        }

                        // Feldstrukturen aufbereiten + Differenz berechnen
                        foreach ($bearbeitungen[$key]['fields'] as $key_fields => $bearbeitung_field) {
                            if (is_array($bearbeitung_field)) {
                                foreach ($bearbeitung_field as $key_field => $field) {
                                    if (is_object($field)) {
                                        $field = $field->to_array();
                                        $bearbeitungen[$key]['fields'][$key_fields][$key_field] = $field;
                                    }

                                    if (is_array($field) && !empty($field['ID'])) {
                                        $bearbeitungen[$key]['fields'][$key_fields][$key_field]['fields'] = get_fields($field['ID']);
                                    } else {
                                        $bearbeitungen[$key]['fields'][$key_fields][$key_field] = $field;
                                    }
                                }
                            } else {
                                $bearbeitungen[$key]['fields'][$key_fields] = $bearbeitung_field;
                            }

                            if (!empty($bearbeitungen[$key]['fields']['pv_finish_date'])) {
                                $current_date = new DateTime('now');
                                $finish_date = DateTime::createFromFormat('d/m/Y', $bearbeitungen[$key]['fields']['pv_finish_date']);
                                $difference = $current_date->diff($finish_date);
                                $bearbeitungen[$key]['fields']['pv_finish_difference'] = $difference->format('%r%a');
                            }
                        }

                        // Filter: Nur anzeigen, wenn seit min_days_old keine Änderung
                        if ($min_days_old > 0 && !empty($bearbeitung['post_modified'])) {
                            $modified = new DateTime($bearbeitung['post_modified']);
                            $now = new DateTime('now');
                            $diff_days = (int)$now->diff($modified)->format('%a');
                            if ($diff_days < $min_days_old) {
                                unset($bearbeitungen[$key]);
                                continue;
                            }
                        }

                        // Suche anwenden
                        $match = empty($search) ? true : $this->matches_search($bearbeitungen[$key], $search, $allowed_keys);
                        if (!$match) {
                            unset($bearbeitungen[$key]);
                            continue;
                        }

                        // Gruppieren
                        if (!empty($bearbeitungen[$key]['fields']['pv_job_gruppe']) && $grouped === true) {
                            if ($bearbeitungen[$key]['fields']['pv_bearbeitung_status'] != 'Durchführung - bei Kunde' && $bearbeitungen[$key]['fields']['pv_bearbeitung_status'] != 'Geliefert - Abgeschlossen') {
                                foreach ($bearbeitungen[$key]['fields']['pv_job_gruppe'] as $gruppe) {
                                    if (is_object($gruppe)) {
                                        $gruppe = $gruppe->to_array();
                                    }
                                    $bearbeitungen['group_' . $gruppe['ID']]['gruppe'] = $gruppe;
                                    $bearbeitungen['group_' . $gruppe['ID']]['bearbeitungen'][] = $bearbeitungen[$key];
                                }
                                unset($bearbeitungen[$key]);
                                continue;
                            }
                        }

                        // Legacy
                        $bearbeitungen[$key]['fields']['pv_small_notiz'] = get_post_meta($bearbeitung['ID'], 'pv_small_notiz', true);
                    }
                }

                // Leere Gruppen entfernen
                foreach ($bearbeitungen as $key => $bearbeitung) {
                    if (str_starts_with($key, 'group_') && empty($bearbeitung)) {
                        unset($bearbeitungen[$key]);
                    }
                }
            }

            return $bearbeitungen;
        }


        /* SUCHFUNKTIONEN START */
        private function matches_search($item, $search, $allowed_keys = [])
        {
            if (empty($search)) return true;
            $is_exact = $this->is_exact_search($search);
            $search_term = $this->strip_search_wrappers($search);
            foreach ($allowed_keys as $key) {
                // Top-Level: string
                if (isset($item[$key]) && is_string($item[$key]) && $this->value_matches($item[$key], $search_term, $is_exact)) {
                    return true;
                }
                // Top-Level: array
                if (isset($item[$key]) && is_array($item[$key]) && $this->deep_search($item[$key], $search_term, $is_exact)) {
                    return true;
                }
                // fields: string
                if (isset($item['fields'][$key]) && is_string($item['fields'][$key]) && $this->value_matches($item['fields'][$key], $search_term, $is_exact)) {
                    return true;
                }
                // fields: array
                if (isset($item['fields'][$key]) && is_array($item['fields'][$key]) && $this->deep_search($item['fields'][$key], $search_term, $is_exact)) {
                    return true;
                }
            }
            return false;
        }

        private function value_matches($value, string $search, bool $exact): bool
        {
            if ($exact) {
                return preg_match('/\b' . preg_quote($search, '/') . '\b/i', $value);
            }
            return stripos($value, $search) !== false;
        }

        private function deep_search(array $array, string $search, bool $exact = false): bool
        {
            foreach ($array as $value) {
                if (is_string($value) && $this->value_matches($value, $search, $exact)) {
                    return true;
                }
                if (is_numeric($value) && !$exact && stripos((string)$value, $search) !== false) {
                    return true;
                }
                if (is_array($value) && $this->deep_search($value, $search, $exact)) {
                    return true;
                }
                if (is_object($value)) {
                    $value = json_decode(json_encode($value), true);
                    if (is_array($value) && $this->deep_search($value, $search, $exact)) {
                        return true;
                    }
                }
            }
            return false;
        }

        private function is_exact_search(string $search): bool
        {
            return preg_match('/^\|.*\|$/', $search);
        }

        private function strip_search_wrappers(string $search): string
        {
            return trim($search, '|');
        }
        /* SUCHFUNKTIONEN ENDE */

        public function insert_notification_to_users($post_id, $action = 'n', $data = array(), $user_id = null)
        {
            $users = array();
            if (!empty($user_id)) {
                $users[] = get_user_by('ID', $user_id);
            } else {
                $users = $this->get_all_users(true, true, true);
            }

            if (!empty($users)) {
                foreach ($users as $user) {
                    $notifications = array();
                    if (get_field('pv_user_notifications', 'user_' . $user->data->ID)) {
                        $notifications = get_field('pv_user_notifications', 'user_' . $user->data->ID);
                        $notifications = explode(',', $notifications);
                    }

                    if (!in_array($post_id, $notifications)) {
                        $notification = $post_id . ':';
                        $notification .= $action;
                        if (!empty($data['edited_by'])) {
                            $notification .= ':' . $data['edited_by'];
                        }
                        array_unshift($notifications, $notification);
                        $notifications = implode(',', $notifications);
                        update_field('pv_user_notifications', $notifications, 'user_' . $user->data->ID);
                    }
                }
            }
        }

        // public function get_notifications_of_user($userid = null)
        // {
        //     $user = $this->get_current_user($userid);
        //     if (empty($user) || empty($user['ID'])) return [];

        //     $user_id    = (int) $user['ID'];
        //     $post_types = ['jobs', 'bearbeitungen'];

        //     // 1) Alle relevanten Parent-Posts finden, bei denen der User in pv_bearbeiter steht.
        //     //    Wir holen grob vorgefiltert per meta_query (EXISTS) und filtern anschließend sauber in PHP,
        //     //    weil ACF das Feld je nach Return-Format (IDs vs. User-Arrays) unterschiedlich speichert.
        //     $candidates = get_posts([
        //         'post_type'      => $post_types,
        //         'post_status'    => 'publish',
        //         'posts_per_page' => -1,
        //         'no_found_rows'  => true,
        //         'fields'         => 'ids',
        //         'meta_query'     => [
        //             [
        //                 'key'     => 'pv_bearbeiter',
        //                 'compare' => 'EXISTS',
        //             ],
        //         ],
        //     ]);

        //     if (empty($candidates)) return [];

        //     $parent_ids = [];
        //     foreach ($candidates as $pid) {
        //         $bearbeiter = get_field('pv_bearbeiter', $pid); // ACF: kann IDs ODER User-Arrays liefern
        //         if (empty($bearbeiter) || !is_array($bearbeiter)) continue;

        //         // Robust normalisieren → immer zu einer Liste von Integer-User-IDs
        //         $ids = [];
        //         foreach ($bearbeiter as $entry) {
        //             if (is_array($entry)) {
        //                 // Beispiel wie von dir: ['ID'=>31, 'display_name'=>...]
        //                 if (!empty($entry['ID'])) $ids[] = (int) $entry['ID'];
        //                 elseif (!empty($entry['user_id'])) $ids[] = (int) $entry['user_id']; // Fallback
        //             } else {
        //                 // ACF "User" kann auch direkt eine ID liefern
        //                 $ids[] = (int) $entry;
        //             }
        //         }
        //         $ids = array_filter(array_unique($ids));

        //         if (in_array($user_id, $ids, true)) {
        //             $parent_ids[] = (int) $pid;
        //         }
        //     }
        //     $parent_ids = array_values(array_unique($parent_ids));
        //     if (empty($parent_ids)) return [];

        //     // 2) Revisions zu diesen Parent-Posts laden – aber NICHT vom aktuellen User.
        //     $revisions_q = new WP_Query([
        //         'post_type'       => 'revision',
        //         'post_status'     => 'inherit',
        //         'post_parent__in' => $parent_ids,
        //         'author__not_in'  => [$user_id],
        //         'orderby'         => 'date',
        //         'order'           => 'DESC',
        //         'posts_per_page'  => 200,     // ggf. anpassen
        //         'no_found_rows'   => true,
        //     ]);
        //     if (empty($revisions_q->posts)) return [];

        //     // 3) Pro Parent nur die neueste Revision behalten (reduziert Spam)
        //     $latest_by_parent = [];
        //     foreach ($revisions_q->posts as $rev) {
        //         $parent_id = (int) $rev->post_parent;
        //         if (!isset($latest_by_parent[$parent_id])) {
        //             $latest_by_parent[$parent_id] = $rev; // Query ist DESC, erste ist die neueste
        //         }
        //     }

        //     // 4) In dein Notification-Format mappen
        //     $output = [];
        //     foreach ($latest_by_parent as $parent_id => $rev) {
        //         $parent_post = get_post($parent_id);
        //         if (
        //             !$parent_post ||
        //             get_post_status($parent_post) !== 'publish' ||
        //             !in_array(get_post_type($parent_post), $post_types, true)
        //         ) {
        //             continue;
        //         }

        //         $output[$parent_id] = [
        //             'post'          => $parent_post,
        //             'bearbeitung'   => '',                 // bleibt leer (nur für 'n' relevant)
        //             'action'        => 'u',                // Revisions = Update
        //             'edited_by'     => (int) $rev->post_author,
        //             'revision_id'   => (int) $rev->ID,
        //             'revision_date' => $rev->post_date,    // für exakte Zeitangabe in UI
        //         ];
        //     }

        //     return $output;
        // }

        public function get_notifications_of_user($userid = null)
        {
            $user = $this->get_current_user($userid);

            $notifications = array();
            $output = array();

            if (!empty($user)) {
                if (get_field('pv_user_notifications', 'user_' . $user['ID'])) {
                    $notifications = get_field('pv_user_notifications', 'user_' . $user['ID']);
                    $notifications = explode(',', $notifications);
                }

                if (!empty($notifications)) {
                    foreach ($notifications as $index => $post) {
                        $post = explode(':', $post);
                        $post_id = $post[0];
                        $action = $post[1];
                        $edited_by = !empty($post[2]) ? $post[2] : '';
                        $post_types = array('jobs', 'bearbeitungen');
                        $bearbeitung = '';
                        if (get_post_status($post_id) == 'publish' && in_array(get_post_type($post_id), $post_types)) {
                            if ($action == 'n') {
                                $bearbeitung = get_posts(array(
                                    'posts_per_page'    => 1,
                                    'post_type'     => 'bearbeitungen',
                                    'meta_query'    => array(
                                        array(
                                            'key'       => 'pv_jobs',
                                            'value'     => '"' . $post_id . '"',
                                            'compare'   => 'LIKE',
                                        )
                                    )
                                ));
                                if (!empty($bearbeitung)) {
                                    $bearbeitung = $bearbeitung[0];
                                }
                            }

                            $output[$post_id]['post'] = get_post($post_id);
                            $output[$post_id]['bearbeitung'] = $bearbeitung;
                            $output[$post_id]['action'] = $action;
                            $output[$post_id]['edited_by'] = $edited_by;
                        } else {
                            // Wenn zwischenzeitlich schon ein Job abgeschlossen wurde, lösche Benachrichtigung.
                            unset($notifications[$index]);
                            update_field('pv_user_notifications', implode(',', $notifications), 'user_' . $user['ID']);
                        }
                    }
                }
            }
            return $output;
        }

        public function remove_notifications_of_user($search_key, $search_data, $userid)
        {
            $user = $this->get_current_user($userid);

            if (!empty($user)) {
                if (get_field('pv_user_notifications', 'user_' . $user['ID'])) {
                    $notifications = get_field('pv_user_notifications', 'user_' . $user['ID']);
                    $notifications = explode(',', $notifications);

                    if (!empty($notifications)) {
                        foreach ($notifications as $index => $post) {
                            $post = explode(':', $post);
                            $post_id = $post[0];
                            $action = $post[1];

                            if ($search_key == 'post_id' && $search_data == $post_id) {
                                unset($notifications[$index]);
                            } else if ($search_key == 'remove' && $search_data == $action) {
                                unset($notifications[$index]);
                            }
                        }
                    }
                    update_field('pv_user_notifications', implode(',', $notifications), 'user_' . $user['ID']);
                }
            }
        }

        public function only_show_user_images($query)
        {
            $current_userID = get_current_user_id();
            if ($current_userID && !current_user_can('manage_options')) {
                $query['author'] = $current_userID;
            }
            return $query;
        }

        public function disable_admin_bar()
        {
            if (current_user_can('administrator')) {
                show_admin_bar(true);
            } else {
                show_admin_bar(false);
            }
        }
    }
    $PV_Users = new PV_Users();
}
