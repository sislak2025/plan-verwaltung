<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PV_Posttype')) {
    class PV_Posttype
    {
        public function __construct()
        {
            add_action('before_delete_post', array($this, 'delete_attachment_with_post'), 10);
        }

        public function register_posttype($slug, $data)
        {
            if (!empty($slug) && !empty($data['post_type'])) {
                register_post_type($slug, $data['post_type']);
                if (!empty($data['custom_fields'])) {
                    $this->register_fieldgroups($data['custom_fields']);
                    #$this->register_custom_field('group_1');
                }
            }
        }

        public function register_category($slug, $posttype, $data)
        {
            if (!empty($slug) && !empty($data)) {
                register_taxonomy($slug, $posttype, $data);
            }
        }

        public function register_fieldgroups($data)
        {
            // https://www.advancedcustomfields.com/resources/register-fields-via-php/
            if (function_exists('acf_add_local_field_group')) {
                if (!empty($data)) {
                    acf_add_local_field_group($data);
                }
            }
        }

        public function register_custom_field($field_group, $data)
        {
            if (function_exists('acf_add_local_field_group')) {
                if (!empty($data)) {
                    acf_add_local_field($data);
                }
            }
        }

        public function delete_attachment_with_post($post_id)
        {
            if (get_post_type($post_id) == 'jobs') {
                if (has_post_thumbnail($post_id)) {
                    $attachment_id = get_post_thumbnail_id($post_id);
                    wp_delete_attachment($attachment_id, true);
                }
            }
        }

        public function insert_post($data)
        {
            $users_class = new PV_Users();
            #$gallery_class = new PV_Gallery();

            if (!empty($data)) {
                $post_data = array(
                    'post_title' => $data['post_title'],
                    'post_content' => $data['post_content'],
                    'post_status' => 'publish',
                    'post_date' => $data['post_date'],
                    'post_type' => $data['post_type'],
                    'post_excerpt' => $data['post_excerpt']
                );

                $post_id = '';
                $post = get_posts(array(
                    'numberposts'   => 1,
                    'post_status'   => array('publish', 'abgeschlossen'),
                    'post_type'     => $data['post_type'],
                    'meta_key'      => 'pv_id',
                    'meta_value'    => $data['post_fields']['urno']
                ));
                if (!empty($post)) {
                    $post_id = $post[0]->ID;
                    $post_data['ID'] = $post_id;
                    unset($post_data['post_date']);
                    wp_update_post($post_data);
                } else {
                    $post_id = wp_insert_post($post_data);
                    $users_class->insert_notification_to_users($post_id);
                }

                if (!empty($post_id)) {
                    if (!empty($data['post_import'])) {
                        update_post_meta($post_id, 'post_import', $data['post_import']);
                    }
                    if (!empty($data['post_fields'])) {
                        if (function_exists('update_field')) {
                            foreach ($data['post_fields'] as $field_key => $field_value) {
                                if ($field_key == 'urno') {
                                    $field_key = 'id';
                                }
                                if ($field_key == 'description') {
                                    $field_value = strip_tags($field_value);
                                }
                                update_field('pv_' . $field_key, $field_value, $post_id);
                            }
                        }
                    }
                    if (!empty($data['post_images'])) {
                        $gallery_imgs = array();
                        foreach ($data['post_images'] as $image_path) {
                            $image_name = explode('.', basename($image_path))[0];
                            if (strpos($image_name, '-') !== false) {
                                $image_place = explode('-', $image_name)[1];
                                if ($image_place == '1') {
                                    $this->generate_featured_image($image_path, $post_id, $data['post_title']);
                                } else {
                                    $gallery_imgs[] = $image_path;

                                    // Bilder direkt in die Galerie importieren schafft der Server nicht
                                    #$gallery_class->insert_gallery_item($post_id, $image_path);
                                }
                            } else {
                                $this->generate_featured_image($image_path, $post_id, $data['post_title']);
                            }
                        }
                        if (!empty($gallery_imgs)) {
                            $path = PV_UPLOAD_PATH . 'import-uploads/';
                            if (file_exists($path . 'gallery_tmp.json')) {
                                $json = file_get_contents($path . 'gallery_tmp.json');
                                $file_data = json_decode($json, true);
                            }
                            $file_data[$post_id] = $gallery_imgs;
                            file_put_contents($path . 'gallery_tmp.json', json_encode($file_data));
                        }
                    }
                }
                return $post_id;
            }
        }

        public function insert_category($taxonomy, $cat_name, $post_id = null)
        {
            $category = get_term_by('name', $cat_name, $taxonomy);

            if ($category == false) {
                $category = wp_insert_term($cat_name, $taxonomy);
                $cat_id = $category['term_id'];
            } else {
                $cat_id = $category->term_id;
            }
            if (!empty($post_id)) {
                $result = wp_set_post_terms($post_id, array($cat_id), $taxonomy, true);
            } else {
                $result = $cat_id;
            }
            return $result;
        }

        public function delete_posts($post_type, $type = 'all')
        {
            $args = array(
                'post_type' => $post_type,
                'posts_per_page' => -1,
                'fields' => 'ids',
            );

            if ($type == 'imported') {
                $args['meta_query'] = array(
                    array(
                        'key' => 'post_import',
                        'value' => date("Y-m-d H:i:s"),
                        'compare' => '<'
                    )
                );
            }

            $posts = get_posts($args);
            if (!empty($posts)) {
                foreach ($posts as $id) {
                    $attachments = get_attached_media('image', $id);
                    foreach ($attachments as $attachment) {
                        wp_delete_attachment($attachment->ID, true);
                    }
                    wp_delete_post($id, true);
                }
            }

            $path = PV_UPLOAD_PATH . 'import-uploads/';
            $file = $path . 'gallery_tmp.json';
            unlink($file);
        }

        public function update_post_meta($post_id, $field_name, $value = '')
        {
            if (empty($value) or !$value) {
                delete_post_meta($post_id, $field_name);
            } elseif (!get_post_meta($post_id, $field_name)) {
                add_post_meta($post_id, $field_name, $value);
            } else {
                update_post_meta($post_id, $field_name, $value);
            }
        }

        public function generate_featured_image($file, $post_id, $title = '', $desc = null)
        {
            preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
            if (!$matches) {
                return new WP_Error('image_sideload_failed', __('Invalid image URL'));
            }

            $file_array = array();
            $file_array['name_ext'] = basename($matches[0]);
            $file_array['name'] = !empty($title) ? $title . '.' . $matches[1] : $file_array['name_ext'];

            $file_array['tmp_name'] = $file;
            if (filter_var($file, FILTER_VALIDATE_URL) !== FALSE) {
                $file_array['tmp_name'] = download_url($file);
            }

            if (is_wp_error($file_array['tmp_name'])) {
                return $file_array['tmp_name'];
            }

            if (has_post_thumbnail($post_id)) {
                $id = get_post_thumbnail_id($post_id);
            } else {
                $id = media_handle_sideload($file_array, $post_id, $desc);
            }

            if (is_wp_error($id)) {
                @unlink($file_array['tmp_name']);
                return $id;
            }

            // $file_array['type'] = wp_check_filetype($file_array['name'], null);
            // $attachment = array(
            //     'post_mime_type' => $file_array['type']['type'],
            //     'post_title' => sanitize_file_name($file_array['name']),
            //     'post_content' => '',
            //     'post_status' => 'inherit'
            // );
            // $attach_id = wp_insert_attachment($attachment, $file, $post_id);
            // require_once(ABSPATH . 'wp-admin/includes/image.php');
            // $attach_data = wp_generate_attachment_metadata($attach_id, $file);
            // wp_update_attachment_metadata($attach_id, $attach_data);

            return set_post_thumbnail($post_id, $id);
        }

        public function get_projektstatus()
        {
            $status_array = array();
            if (!empty(get_option('pv_project_status'))) {
                $status_opt = get_option('pv_project_status');
                foreach ($status_opt as $status) {
                    $status_val = $status;
                    if (str_contains($status, ' - ')) {
                        $status_val = explode(' - ', $status)[1];
                    }
                    $status_array[$status] = $status_val;
                }
            }

            $status_array_with_groups = array();
            $status_array_with_groups['Wartend'] = array();
            $status_array_with_groups['Bearbeitung'] = array();
            $status_array_with_groups['Print'] = array();
            $status_array_with_groups['Web-Abteilung'] = array();
            $status_array_with_groups['Abschluss'] = array();

            if (!empty($status_array)) {
                foreach ($status_array as $status_value => $status_label) {
                    if (preg_match('(Layout|in Druck|RZ)', $status_label) === 1) {
                        $status_array_with_groups['Print'][$status_value] = $status_label;
                    } else if (preg_match('(Vorbereitung|bei Kunde|in Abstimmung mit Kontakter|bei Freelancer)', $status_label) === 1) {
                        $status_array_with_groups['Wartend'][$status_value] = $status_label;
                    } else if (preg_match('(in Bearbeitung|Korrektur)', $status_label) === 1) {
                        $status_array_with_groups['Bearbeitung'][$status_value] = $status_label;
                    } else if (preg_match('(Fertiggestellt|Abgeschlossen|Abgebrochen)', $status_label) === 1) {
                        $status_array_with_groups['Abschluss'][$status_value] = $status_label;
                    } else {
                        $status_array_with_groups['Allgemein'][$status_value] = $status_label;
                    }
                }
            }

            $status_array_with_groups['Web-Abteilung'] = array(
                'Custom - Vorbereitungsphase' => 'Vorbereitungsphase',
                'Custom - Gestaltungsphase' => 'Gestaltungsphase',
                'Custom - Präsentationsphase' => 'Präsentationsphase',
                'Custom - Migrations-Aufbauphase' => 'Migrations- / Aufbauphase',
                'Custom - Präsentationsphase 2' => 'Präsentationsphase 2',
                'Custom - Korrekturstufe 1' => 'Korrekturstufe 1',
                'Custom - Korrekturstufe 2' => 'Korrekturstufe 2',
                'Custom - Livegangprozess' => 'Livegangprozess',
            );

            return $status_array_with_groups;
        }

        public function get_service_codes()
        {
            $service_codes_array = array();
            if (!empty(get_option('pv_service_codes'))) {
                $service_codes_opt = get_option('pv_service_codes');
                foreach ($service_codes_opt as $service_code) {
                    $service_codes[$service_code['urno']] = $service_code['name'];
                }
            }
            return $service_codes;
        }
    }
    $PV_Posttype = new PV_Posttype();
}
