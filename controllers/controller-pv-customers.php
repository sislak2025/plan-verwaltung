<?php

add_action('wp_ajax_pv_load_customers_table', 'pv_load_customers_table');
add_action('wp_ajax_nopriv_pv_load_customers_table', 'pv_load_customers_table');
function pv_load_customers_table()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_load_customers_table') {
        $customers_class = new PV_Customers();

        $current_url = $_SERVER['HTTP_REFERER'];
        $customers = $customers_class->get_all_customers();

        $data = array();
        foreach ($customers as $customer) {
            $array = array();
            $array[] = $customer['post_title'];
            $array[] = $customer['pv_shortname'];
            $array[] = $customer['pv_prefix'] ?? '';
            $array[] = $customer['ID'];
            $array[] = get_permalink($customer['ID']);
            $data['data'][] = $array;
        }
        echo json_encode($data);
    }
    exit();
}

add_action('wp_ajax_pv_edit_customer', 'pv_edit_customer');
add_action('wp_ajax_nopriv_pv_edit_customer', 'pv_edit_customer');
function pv_edit_customer()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_edit_customer') {
        acf_form(array(
            'post_id' => $_POST['id'],
            'post_title' => true,
            'submit_value' => __("Aktualisieren", 'acf'),
            'html_submit_button' => '<input type="submit" class="btn btn-primary" value="%s" />',
            'return' => $_POST['url']
        ));
        die();
    }
}

add_filter('acf/prepare_field/name=_post_title', 'pv_prepare_title_customer');
function pv_prepare_title_customer($field)
{
    if (!empty($_POST['action'])) {
        if ($_POST['action'] == 'pv_add_customer' || $_POST['action'] == 'pv_edit_customer') {
            $field['label'] = 'Kunde';
        }
    }
    return $field;
}