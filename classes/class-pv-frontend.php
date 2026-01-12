<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PV_Frontend')) {
    class PV_Frontend
    {
        public function __construct()
        {
            add_action('wp_enqueue_scripts', array($this, 'frontend_manage_assets'), 10);
            add_action('wp_head',  array($this, 'add_acf_form_head'), 1);
        }

        public function generate_html_output($data)
        {
            $output = '';
            if (!empty($data['template'])) {
                ob_start();
                require $data['template'];
                $output = ob_get_contents();
                ob_end_clean();
            }
            return $output;
        }

        public function frontend_manage_assets()
        {
            global $wp_styles;

            $page_id = get_queried_object_id();
            $page_object = get_post($page_id);

            if (strpos($page_object->post_content, '[pv-frontend') !== false || is_singular('kunden')) {
                add_filter('hello_elementor_enqueue_style', '__return_false');

                wp_register_script('script-pv-jqueryui', PV_ADMINISTRATION_URL . 'scripts/jquery-ui.min.js', array('jquery'), '1.13.1');
                wp_enqueue_script('script-pv-jqueryui');
                wp_register_style('style-pv-jqueryui', PV_ADMINISTRATION_URL . 'styles/jquery-ui.min.css', false, '1.13.1');
                wp_enqueue_style('style-pv-jqueryui');

                wp_register_script('script-pv-bootstrap', PV_ADMINISTRATION_URL . 'scripts/bootstrap.bundle.min.js', array('jquery'), '5.1.3');
                wp_enqueue_script('script-pv-bootstrap');
                wp_register_style('style-pv-bootstrap', PV_ADMINISTRATION_URL . 'styles/bootstrap.min.css', false, '5.1.3');
                wp_enqueue_style('style-pv-bootstrap');

                wp_register_script('script-rv-datatables', PV_ADMINISTRATION_URL . 'scripts/datatables.min.js', array('jquery'), '1.13.6');
                wp_enqueue_script('script-rv-datatables');
                wp_register_style('style-rv-datatables', PV_ADMINISTRATION_URL . 'styles/datatables.min.css', false, '1.13.6');
                wp_enqueue_style('style-rv-datatables');

                wp_register_script('script-rv-jqschedule', PV_ADMINISTRATION_URL . 'scripts/jq.schedule.js', array('jquery'), '3.1.0');
                wp_enqueue_script('script-rv-jqschedule');
                wp_register_style('style-rv-jqschedule', PV_ADMINISTRATION_URL . 'styles/jq.schedule.min.css', false, '3.1.0');
                wp_enqueue_style('style-rv-jqschedule');

                // wp_register_script('script-rv-jqscheduleplus', PV_ADMINISTRATION_URL . 'scripts/jq.schedule.plus.js', array('jquery'), '2.3.0');
                // wp_enqueue_script('script-rv-jqscheduleplus');
                // wp_register_style('style-rv-jqscheduleplus', PV_ADMINISTRATION_URL . 'styles/jq.schedule.plus.css', false, '2.3.0');
                // wp_enqueue_style('style-rv-jqscheduleplus');
            }

            wp_register_script('script-pv-frontend', PV_ADMINISTRATION_URL . 'scripts/frontend/script-pv-frontend.js', array('jquery'), PV_ADMINISTRATION_VERSION);
            wp_enqueue_script('script-pv-frontend');
            wp_register_style('style-pv-frontend', PV_ADMINISTRATION_URL . 'styles/frontend/style-pv-frontend.css', false, PV_ADMINISTRATION_VERSION);
            wp_enqueue_style('style-pv-frontend');

            wp_register_script('script-pv-schedule', PV_ADMINISTRATION_URL . 'scripts/frontend/script-pv-schedule.js', array('jquery'), PV_ADMINISTRATION_VERSION);
            wp_enqueue_script('script-pv-schedule');

            wp_localize_script('script-pv-frontend', 'pv_js_variables', array(
                'ajax_url' => admin_url('admin-ajax.php')
            ));
        }

        public function add_acf_form_head()
        {
            acf_form_head();
            acf_enqueue_uploader();
        }
    }
    $PV_Frontend = new PV_Frontend();
}
