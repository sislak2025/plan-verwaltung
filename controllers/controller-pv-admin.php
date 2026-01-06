<?php

add_action('admin_menu', 'pv_admin_menu');
function pv_admin_menu()
{
    add_menu_page('Verwaltung', 'Verwaltung', 'edit_pages', 'plan-verwaltung', 'pv_admin_settings_page', 'dashicons-database');
}

function pv_admin_settings_page()
{
    ob_start();
    include_once(PV_ADMINISTRATION_PATH . '/views/view-pv-admin.php');
    echo ob_get_clean();
}

add_action('admin_post_pv_importapi_admin_form', 'pv_importapi_admin_form');
add_action('admin_post_nopriv_pv_importapi_admin_form', 'pv_importapi_admin_form');
function pv_importapi_admin_form()
{
    $importapi_class = new PV_Importapi();
    $url = $_POST['_wp_http_referer'];
    wp_verify_nonce($_POST['admin_form_nonce'], 'pv_importapi_admin_form');

    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] = 'pv_importapi_admin_form') {
        if (!empty($_POST['pv_import_type'])) {
            $importapi_class->insert_request_data($_POST['pv_import_type']);
        }
    }
    wp_redirect($url);
}

add_action('admin_post_pv_delete_all_records', 'pv_delete_all_records');
add_action('admin_post_nopriv_pv_delete_all_records', 'pv_delete_all_records');
function pv_delete_all_records()
{
    $posttype_class = new PV_Posttype();
    $url = $_POST['_wp_http_referer'];
    wp_verify_nonce($_POST['admin_form_nonce'], 'pv_delete_all_records');

    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] = 'pv_delete_all_records') {
        if (!empty($_POST['pv_delete_type']) && !empty($_POST['pv_delete_source'])) {

            $type = $_POST['pv_delete_type'];
            if ($type == 'projekte') {
                $type = 'jobs';
            }
            if ($_POST['pv_delete_source'] == 'imported') {
                $posttype_class->delete_posts($type, $_POST['pv_delete_source']);
            } else {
                $posttype_class->delete_posts($type);
            }
        }
        wp_redirect($url);
    }
}
