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
    }
    $PV_Customers = new PV_Customers();
}
