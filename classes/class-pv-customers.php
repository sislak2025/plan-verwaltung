<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PV_Customers')) {
    class PV_Customers
    {
        public function __construct()
        {
            //
        }

        public function get_all_customers()
        {
            $customers = array();
            $customer_posts = get_posts(array(
                'post_type' => 'kunden',
                'post_status' => 'publish',
                'numberposts' => -1
            ));

            if (!empty($customer_posts)) {
                foreach ($customer_posts as $customer) {
                    $customer = $customer->to_array();
                    $customer_fields = get_fields($customer['ID']);

                    if (!empty($customer_fields)) {
                        foreach ($customer_fields as $key => $value) {
                            $customer[$key] = $value;
                        }
                    }
                    $customers[] = $customer;
                }
            }
            return $customers;
        }

        public function get_customer($post_id)
        {
            $post_id = absint($post_id);
            if (!$post_id) {
                return array();
            }

            $customer_post = get_post($post_id);
            if (empty($customer_post) || $customer_post->post_type !== 'kunden') {
                return array();
            }

            $customer = $customer_post->to_array();
            $customer_fields = get_fields($customer['ID']);

            if (!empty($customer_fields)) {
                foreach ($customer_fields as $key => $value) {
                    $customer[$key] = $value;
                }
            }

            return $customer;
        }
    }
    $PV_Customers = new PV_Customers();
}
