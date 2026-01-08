<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PV_MCP')) {
    class PV_MCP
    {
        const PROTOCOL_VERSION = '2024-11-05';
        const MAX_PER_PAGE = 100;

        public function register_routes()
        {
            register_rest_route('plan-verwaltung/v1', '/mcp', array(
                'methods' => 'POST',
                'callback' => array($this, 'handle_request'),
                'permission_callback' => array($this, 'permission_check'),
            ));
        }

        public function permission_check($request)
        {
            if (is_user_logged_in() && current_user_can('edit_posts')) {
                return true;
            }

            return new WP_Error('pv_mcp_forbidden', 'Zugriff verweigert.', array('status' => 401));
        }

        public function handle_request($request)
        {
            $payload = $request->get_json_params();
            if (empty($payload)) {
                $raw_body = $request->get_body();
                if (!empty($raw_body)) {
                    $decoded = json_decode($raw_body, true);
                    if (is_array($decoded)) {
                        $payload = $decoded;
                    }
                }
            }

            if (empty($payload)) {
                $method = $request->get_param('method');
                if (!empty($method)) {
                    $payload = array(
                        'jsonrpc' => '2.0',
                        'id' => $request->get_param('id'),
                        'method' => $method,
                        'params' => $request->get_param('params'),
                    );
                }
            }

            if (empty($payload) || empty($payload['method'])) {
                return $this->error_response(null, -32600, 'Invalid Request');
            }

            $method = $payload['method'];
            $id = array_key_exists('id', $payload) ? $payload['id'] : null;

            switch ($method) {
                case 'initialize':
                    return $this->success_response($id, array(
                        'protocolVersion' => self::PROTOCOL_VERSION,
                        'serverInfo' => array(
                            'name' => 'plan-verwaltung',
                            'version' => PV_ADMINISTRATION_VERSION,
                        ),
                        'capabilities' => array(
                            'tools' => array(
                                'listChanged' => false,
                            ),
                        ),
                    ));
                case 'tools/list':
                    return $this->success_response($id, array(
                        'tools' => $this->get_tools_definition(),
                    ));
                case 'tools/call':
                    return $this->handle_tool_call($id, $payload);
                default:
                    return $this->error_response($id, -32601, 'Method not found');
            }
        }

        private function handle_tool_call($id, $payload)
        {
            if (empty($payload['params']['name'])) {
                return $this->error_response($id, -32602, 'Missing tool name');
            }

            $tool_name = $payload['params']['name'];
            $arguments = array();
            if (!empty($payload['params']['arguments']) && is_array($payload['params']['arguments'])) {
                $arguments = $payload['params']['arguments'];
            }

            switch ($tool_name) {
                case 'pv_list_cpt_posts':
                    return $this->success_response($id, array(
                        'content' => $this->format_tool_result($this->list_cpt_posts($arguments)),
                    ));
                case 'pv_get_cpt_post':
                    return $this->success_response($id, array(
                        'content' => $this->format_tool_result($this->get_cpt_post($arguments)),
                    ));
                case 'pv_list_cpt_types':
                    return $this->success_response($id, array(
                        'content' => $this->format_tool_result($this->list_cpt_types($arguments)),
                    ));
                default:
                    return $this->error_response($id, -32601, 'Tool not found');
            }
        }

        private function get_tools_definition()
        {
            return array(
                array(
                    'name' => 'pv_list_cpt_posts',
                    'description' => 'Listet Einträge eines CPT inkl. Custom Fields.',
                    'inputSchema' => array(
                        'type' => 'object',
                        'properties' => array(
                            'post_type' => array(
                                'type' => 'string',
                                'description' => 'Slug des CPT (z.B. jobs).',
                            ),
                            'per_page' => array(
                                'type' => 'integer',
                                'minimum' => 1,
                                'maximum' => self::MAX_PER_PAGE,
                                'description' => 'Anzahl Einträge pro Seite.',
                            ),
                            'page' => array(
                                'type' => 'integer',
                                'minimum' => 1,
                                'description' => 'Seitenzahl für Pagination.',
                            ),
                            'search' => array(
                                'type' => 'string',
                                'description' => 'Suchbegriff für Titel/Inhalt.',
                            ),
                            'include_meta' => array(
                                'type' => 'boolean',
                                'description' => 'Custom Fields in die Antwort aufnehmen.',
                            ),
                        ),
                        'required' => array('post_type'),
                    ),
                ),
                array(
                    'name' => 'pv_get_cpt_post',
                    'description' => 'Lädt einen CPT-Eintrag inkl. Custom Fields anhand der ID.',
                    'inputSchema' => array(
                        'type' => 'object',
                        'properties' => array(
                            'post_id' => array(
                                'type' => 'integer',
                                'description' => 'ID des Beitrags.',
                            ),
                            'include_meta' => array(
                                'type' => 'boolean',
                                'description' => 'Custom Fields in die Antwort aufnehmen.',
                            ),
                        ),
                        'required' => array('post_id'),
                    ),
                ),
                array(
                    'name' => 'pv_list_cpt_types',
                    'description' => 'Listet registrierte CPTs, die im Backend sichtbar sind.',
                    'inputSchema' => array(
                        'type' => 'object',
                        'properties' => array(
                            'include_builtin' => array(
                                'type' => 'boolean',
                                'description' => 'Auch WordPress Core Post Types ausgeben.',
                            ),
                        ),
                    ),
                ),
            );
        }

        private function list_cpt_posts($arguments)
        {
            $post_type = !empty($arguments['post_type']) ? sanitize_key($arguments['post_type']) : '';
            if (empty($post_type)) {
                return $this->tool_error('post_type ist erforderlich.');
            }

            $available_types = get_post_types(array('show_ui' => true));
            if (!in_array($post_type, $available_types, true)) {
                return $this->tool_error('Unbekannter post_type: ' . $post_type);
            }

            $per_page = isset($arguments['per_page']) ? absint($arguments['per_page']) : 20;
            if ($per_page < 1) {
                $per_page = 1;
            }
            if ($per_page > self::MAX_PER_PAGE) {
                $per_page = self::MAX_PER_PAGE;
            }

            $page = isset($arguments['page']) ? absint($arguments['page']) : 1;
            if ($page < 1) {
                $page = 1;
            }

            $search = !empty($arguments['search']) ? sanitize_text_field($arguments['search']) : '';
            $include_meta = array_key_exists('include_meta', $arguments) ? (bool) $arguments['include_meta'] : true;

            $query_args = array(
                'post_type' => $post_type,
                'post_status' => 'any',
                'posts_per_page' => $per_page,
                'paged' => $page,
                's' => $search,
            );

            $query = new WP_Query($query_args);
            $posts = array();

            if (!empty($query->posts)) {
                foreach ($query->posts as $post) {
                    $posts[] = $this->build_post_payload($post, $include_meta);
                }
            }

            return array(
                'post_type' => $post_type,
                'page' => $page,
                'per_page' => $per_page,
                'total' => (int) $query->found_posts,
                'total_pages' => (int) $query->max_num_pages,
                'posts' => $posts,
            );
        }

        private function get_cpt_post($arguments)
        {
            $post_id = isset($arguments['post_id']) ? absint($arguments['post_id']) : 0;
            if (!$post_id) {
                return $this->tool_error('post_id ist erforderlich.');
            }

            $post = get_post($post_id);
            if (!$post) {
                return $this->tool_error('Beitrag nicht gefunden.');
            }

            $include_meta = array_key_exists('include_meta', $arguments) ? (bool) $arguments['include_meta'] : true;

            return $this->build_post_payload($post, $include_meta, true);
        }

        private function list_cpt_types($arguments)
        {
            $include_builtin = !empty($arguments['include_builtin']);
            $args = array('show_ui' => true);
            if (!$include_builtin) {
                $args['public'] = true;
                $args['builtin'] = false;
            }

            $post_types = get_post_types($args, 'objects');
            $types = array();
            foreach ($post_types as $type) {
                $types[] = array(
                    'name' => $type->name,
                    'label' => $type->label,
                    'description' => $type->description,
                    'public' => (bool) $type->public,
                    'has_archive' => (bool) $type->has_archive,
                );
            }

            return array(
                'post_types' => $types,
            );
        }

        private function build_post_payload($post, $include_meta, $include_content = false)
        {
            $payload = array(
                'id' => (int) $post->ID,
                'title' => get_the_title($post),
                'slug' => $post->post_name,
                'status' => $post->post_status,
                'type' => $post->post_type,
                'date' => $post->post_date_gmt ? $post->post_date_gmt : $post->post_date,
                'modified' => $post->post_modified_gmt ? $post->post_modified_gmt : $post->post_modified,
                'author' => array(
                    'id' => (int) $post->post_author,
                    'name' => get_the_author_meta('display_name', $post->post_author),
                ),
                'link' => get_permalink($post),
            );

            if ($include_content) {
                $payload['content'] = $post->post_content;
                $payload['excerpt'] = $post->post_excerpt;
            }

            if ($include_meta) {
                $payload['custom_fields'] = $this->get_custom_fields($post->ID);
            }

            return $payload;
        }

        private function get_custom_fields($post_id)
        {
            $acf_fields = array();
            if (function_exists('get_fields')) {
                $acf_data = get_fields($post_id);
                if (is_array($acf_data)) {
                    $acf_fields = $acf_data;
                }
            }

            $meta = get_post_meta($post_id);
            $filtered_meta = array();
            if (!empty($meta)) {
                foreach ($meta as $key => $values) {
                    if (strpos($key, '_') === 0) {
                        continue;
                    }
                    if (is_array($values) && count($values) === 1) {
                        $filtered_meta[$key] = $values[0];
                    } else {
                        $filtered_meta[$key] = $values;
                    }
                }
            }

            return array(
                'acf' => $acf_fields,
                'meta' => $filtered_meta,
            );
        }

        private function format_tool_result($data)
        {
            if (!empty($data['is_error'])) {
                return array(
                    array(
                        'type' => 'text',
                        'text' => $data['message'],
                    ),
                );
            }

            return array(
                array(
                    'type' => 'text',
                    'text' => wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                ),
            );
        }

        private function tool_error($message)
        {
            return array(
                'is_error' => true,
                'message' => $message,
            );
        }

        private function success_response($id, $result)
        {
            return rest_ensure_response(array(
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => $result,
            ));
        }

        private function error_response($id, $code, $message)
        {
            return rest_ensure_response(array(
                'jsonrpc' => '2.0',
                'id' => $id,
                'error' => array(
                    'code' => $code,
                    'message' => $message,
                ),
            ));
        }
    }
}
