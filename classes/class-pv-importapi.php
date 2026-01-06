<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PV_Importapi')) {
    class PV_Importapi
    {
        public $url;
        public $api_key;
        public $args;

        public function __construct()
        {
            $this->url = 'https://proad.sislak24.de/api/v5/';
            $this->api_key = '7573d945a40b4c9da1095c70b456c58131004d0586e115b7a94791cd68ba9b6300aef306fd189e6cc03241c2540dde29e0030670a92394adf342883e0df6e514';
            $this->args = array(
                'sslverify' => false,
                'timeout' => 2400,
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'apikey' => $this->api_key,
                )
            );
        }

        public function get_api_data($url)
        {
            $request = wp_remote_get($url, $this->args);
            if (!empty($request)) {
                return json_decode($request['body'], ARRAY_A);
            }
            return '';
        }

        public function post_api_data($url, $method, $data)
        {
            $options = $this->args;
            $options['method'] = $method;
            $options['body'] = json_encode($data);
            $result = wp_remote_post($url, $options);
            if (!empty($result)) {
                return $result;
            }
            return '';
        }

        public function prepare_request_filters($url, $type)
        {
            $return = array('url' => $url);
            if ($type == 'projekte') {
                $project_status_arr = $this->get_api_data($this->url . 'preset_data/project_status');
                if (!empty($project_status_arr['preset_list'])) {
                    $status = array();
                    foreach ($project_status_arr['preset_list'] as $project_status) {
                        // Ignore Abgerechnet + Abgebrochen
                        if ($project_status['key'] != '500' && $project_status['key'] != '600') {
                            $status[] = array('key' => $project_status['key'], 'value' => $project_status['value_de']);
                        }
                    }
                    $return['status'] = $status;
                }
            } else if ($type == 'benutzer') {
                $return['active'] = 'true';
            }
            return $return;
        }

        public function insert_request_data($type)
        {
            $posttype_class = new PV_Posttype();
            $users_class = new PV_Users();
            $endpoint = $this->endpoint_converter($type);
            $request = $this->prepare_request_filters($this->url . $endpoint, $type);

            if ($type == 'projekte') {
                $projekte = array();
                if (array_key_exists('status', $request)) {
                    foreach ($request['status'] as $status) {
                        $projekte_result = $this->get_api_data($request['url'] . '?status=' . $status['key']);
                        if (!empty($projekte_result['project_list'])) {
                            foreach ($projekte_result['project_list'] as $projekt) {
                                $projekt = $this->record_converter($projekt, $request);
                                $projekte[] = $projekt;
                            }
                        }
                    }
                }

                if (!empty($projekte)) {
                    foreach ($projekte as $record) {
                        $order_date = date('Y-m-d', strtotime($record['order_date']));
                        $current_time = current_time('H:i:s');

                        $post_data['post_type'] = $this->value_converter($type);
                        $post_data['post_title'] = $record['projectno'] . '_' . $record['project_name'];
                        $post_data['post_content'] = ' ';
                        $post_data['post_date'] = date('Y-m-d H:i:s', strtotime($order_date . ' ' . $current_time));
                        $post_data['post_import'] = date('Y-m-d H:i:s');
                        $post_data['post_fields'] = $record;
                        $post_data['post_excerpt'] = '';

                        $post_id = $posttype_class->insert_post($post_data);
                    }

                    $this->check_removed_posts($type, $projekte);
                }
            } else if ($type == 'benutzer') {
                $users = array();
                $users_result = $this->get_api_data($request['url'] . '?active=' . $request['active']);
                if (!empty($users_result['person_list'])) {
                    foreach ($users_result['person_list'] as $user) {
                        $users[] = $user;
                    }
                }

                if (!empty($users)) {
                    foreach ($users as $record) {

                        $user_data['user_user_login'] = strstr($record['private_main_address']['email'], '@', true);
                        $user_data['user_user_email'] = $record['private_main_address']['email'];
                        $user_data['user_display_name'] = $record['firstname'] . ' ' . $record['lastname'];
                        $user_data['user_nickname'] = $record['shortname'];
                        $user_data['user_first_name'] = $record['firstname'];
                        $user_data['user_last_name'] = $record['lastname'];
                        $user_data['user_role'] = 'arbeitskraft';
                        $user_data['user_urno'] = $record['urno'];
                        $user_data['user_abteilung'] = $this->value_converter(strtolower($record['department']));

                        $user_id = $users_class->insert_user($user_data);
                    }
                }
            } else if ($type == 'status') {
                $status = array();
                $status_complete = array();
                $status_result = $this->get_api_data($this->url . 'preset_data/project_status');

                if (!empty($status_result['preset_list'])) {
                    foreach ($status_result['preset_list'] as $status_item) {
                        if ($status_item['value_de'] != 'Angebot' && $status_item['value_de'] != 'Durchführung' && $status_item['value_de'] != 'Geliefert' && $status_item['value_de'] != 'Abgerechnet') {
                            $status[] = $status_item['value_de'];
                            $status_complete[$status_item['value_de']] = array('id' => $status_item['key'], 'value' => $status_item['value_de']);
                        }
                    }
                    if (!empty(get_option('pv_project_status'))) {
                        update_option('pv_project_status', $status);
                    } else {
                        add_option('pv_project_status', $status);
                    }

                    if (!empty(get_option('pv_project_status_complete'))) {
                        update_option('pv_project_status_complete', $status_complete);
                    } else {
                        add_option('pv_project_status_complete', $status_complete);
                    }
                }
            } else if ($type == 'service_codes') {
                $service_codes = array();
                $service_codes_result = $this->get_api_data($this->url . 'service_codes');

                if (!empty($service_codes_result['service_code_list'])) {
                    foreach ($service_codes_result['service_code_list'] as $service_code_item) {
                        if ($service_code_item['useintimeregistration']) {
                            $service_codes[] = array('urno' => $service_code_item['urno'], 'name' => $service_code_item['name']);
                        }
                    }
                    if (!empty(get_option('pv_service_codes'))) {
                        update_option('pv_service_codes', $service_codes);
                    } else {
                        add_option('pv_service_codes', $service_codes);
                    }
                }
            } else if ($type == 'kunden') {
                $kunden = array();
                $kunden_result = $this->get_api_data($request['url']);
                if (!empty($kunden_result['company_list'])) {
                    foreach ($kunden_result['company_list'] as $kunde) {
                        $kunden[] = $this->record_converter($kunde, $request);
                    }
                }

                if (!empty($kunden)) {
                    foreach ($kunden as $record) {
                        $post_data['post_type'] = $type;
                        $post_data['post_title'] = $record['name'];
                        $post_data['post_content'] = ' ';
                        $post_data['post_date'] = date('Y-m-d H:i:s');
                        $post_data['post_import'] = date('Y-m-d H:i:s');
                        $post_data['post_fields'] = $record;
                        $post_data['post_excerpt'] = '';

                        $post_id = $posttype_class->insert_post($post_data);

                        if (empty(get_field('pv_prefix', $post_id))) {
                            $kunden_projects = $this->get_api_data($request['url'] . '/' . $record['urno'] . '/projects');
                            if (!empty($kunden_projects['project_list'])) {

                                $re = '/.+?(?=-\d)/';
                                $str = $kunden_projects['project_list'][0]['projectno'];
                                preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
                                update_field('pv_prefix', $matches[0][0], $post_id);
                            }
                        }
                    }
                }
            } else if ($type == 'trackings') {
                // $user_endpoint = $this->endpoint_converter('benutzer');
                // $user_request = $this->prepare_request_filters($this->url . $user_endpoint, $type);

                // $users = array();
                // $users_result = $this->get_api_data($user_request['url'] . '?active=' . $user_request['active']);
                // if (!empty($users_result['person_list'])) {
                //     foreach ($users_result['person_list'] as $user) {
                //         $users[] = $user;
                //     }
                // }

                $posts = get_posts(array(
                    'numberposts'   => -1,
                    'post_type'     => 'bearbeitungen'
                ));

                if (!empty($posts)) {
                    $trackings = array();
                    foreach ($posts as $post) {
                        $jobs = get_field('pv_jobs', $post->ID);
                        if (!empty($jobs)) {
                            foreach ($jobs as $job) {
                                $job_id = get_field('pv_id', $job->ID);
                                $trackings[$job->ID] = $this->get_api_data($request['url'] . '?project=' . $job_id);
                            }
                        }
                    }
                    print_r($trackings);
                    exit();
                    // $trackings = array();
                    // foreach ($users as $user) {
                    //     $trackings[$user['urno']] = $this->get_api_data($request['url'] . '/' . $user['urno'] . '/timeregs');
                    // }
                    // print_r($trackings);
                    // exit();
                }



                #print_r($posts);
                // if (!empty($posts)) {
                //     foreach ($posts as $post) {
                //         $jobs = get_field('pv_jobs', $post->ID);
                //         if (!empty($jobs)) {
                //             foreach ($jobs as $job) {
                //                 $job_id = get_field('pv_id', $job->ID);
                //                 print_r($job);
                //                 print_r($job_id);
                //                 exit();
                //             }
                //         }
                //     }
                // }
            }
        }

        public function check_removed_posts($type, $data)
        {
            $type = $this->value_converter($type);

            $insert_ids = array();
            foreach ($data as $insert_post) {
                $insert_ids[] = $insert_post['urno'];
            }

            $posts = get_posts(array(
                'numberposts' => -1,
                'post_type' => $type,
                'meta_query' => array(
                    array(
                        'key' => 'pv_id',
                        'value' => $insert_ids,
                        'compare' => 'NOT IN',
                    ),
                ),
            ));
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    wp_update_post(array(
                        'ID' => $post->ID,
                        'post_status' => $this->status_converter($type)
                    ));
                }
            }
        }

        public function put_data($type, $data)
        {
            $put_data = array();
            $endpoint = $this->endpoint_converter($type);

            if ($type == 'projekte') {
                $request = $this->url . $endpoint . '/' . $data['id'];
                if (!empty($data['beschreibung'])) {
                    $put_data['description'] = $data['beschreibung'];
                } else if (!empty($data['status'])) {
                    $put_data['status'] = $data['status'];
                }
            }

            $result = $this->post_api_data($request, 'PUT', $put_data);
            return $result;
        }

        public function post_data($type, $data, $defaults = true)
        {
            $put_data = array();
            $endpoint = $this->endpoint_converter($type);

            if ($type == 'projekte') {
                $request = $this->url . $endpoint;
                if (!empty($data)) {
                    $put_data = $data;
                }
                if ($defaults) {
                    $put_data['status'] = '100';
                }
            }

            $result = $this->post_api_data($request, 'POST', $put_data);
            return $result;
        }

        public function record_converter($record, $data = array())
        {
            if (!empty($record)) {
                foreach ($record as $key => $value) {
                    if ($key == 'status' && !empty($data['status'])) {
                        foreach ($data['status'] as $status) {
                            if ($value == $status['key']) {
                                $record[$key] = $status['value'];
                            }
                        }
                    }
                    if ($key == 'type') {
                        $record[$key] = $this->value_converter($value);
                    }
                    if ($key == 'order_date' || $key == 'delivery_date') {
                        $date = new DateTime($value);
                        $date = $date->format('d.m.Y');
                        $record[$key] = $date;
                    }
                    if ($key == 'name') {
                        $record[$key] = str_replace(array("\n", "\r"), ' ', $value);
                    }
                }
            }
            return $record;
        }

        public function value_converter($value)
        {
            if (!empty($value)) {
                switch ($value) {
                    case 'projekte':
                        return 'jobs';
                    case 'standard':
                        return 'Standard';
                    case 'intern':
                        return 'Intern';
                    case 'ongoing':
                        return 'Fortlaufend';
                    case 'kreation':
                        return 'print';
                }
            }
            return $value;
        }

        public function endpoint_converter($value)
        {
            if (!empty($value)) {
                switch ($value) {
                    case 'projekte':
                        return 'projects';
                    case 'benutzer':
                        return 'staffs';
                    case 'trackings':
                        return 'timeregs';
                    case 'kunden':
                        return 'clients';
                }
            }
            return $value;
        }

        public function status_converter($value)
        {
            if (!empty($value)) {
                switch ($value) {
                    case 'jobs':
                        return 'abgeschlossen';
                }
            }
            return $value;
        }
    }
    $PV_Importapi = new PV_Importapi();
}
