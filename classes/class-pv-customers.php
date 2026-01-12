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

        public function get_customer_jobs($post_id)
        {
            $post_id = absint($post_id);
            if (!$post_id) {
                return array();
            }

            $jobs = get_field('pv_jobs_of_kunde', $post_id);
            if (empty($jobs)) {
                return array();
            }

            if (!is_array($jobs)) {
                $jobs = array($jobs);
            }

            $jobs_with_fields = array();
            foreach ($jobs as $job) {
                if (empty($job)) {
                    continue;
                }
                $job_id = is_object($job) ? $job->ID : (is_array($job) ? ($job['ID'] ?? 0) : 0);
                if (!$job_id) {
                    continue;
                }
                $job_post = get_post($job_id);
                if (empty($job_post)) {
                    continue;
                }
                $job_item = $job_post->to_array();
                $job_fields = get_fields($job_id);
                if (!empty($job_fields)) {
                    foreach ($job_fields as $key => $value) {
                        $job_item[$key] = $value;
                    }
                }
                $jobs_with_fields[] = $job_item;
            }

            return $jobs_with_fields;
        }

        public function get_customer_bearbeitungen($post_id)
        {
            $jobs = $this->get_customer_jobs($post_id);
            if (empty($jobs)) {
                return array();
            }

            $bearbeitungen = array();
            foreach ($jobs as $job) {
                $job_id = $job['ID'] ?? 0;
                if (!$job_id) {
                    continue;
                }
                $job_bearbeitungen = get_field('pv_jobs_bearbeitung', $job_id);
                if (empty($job_bearbeitungen)) {
                    continue;
                }
                if (!is_array($job_bearbeitungen)) {
                    $job_bearbeitungen = array($job_bearbeitungen);
                }
                foreach ($job_bearbeitungen as $bearbeitung) {
                    if (empty($bearbeitung)) {
                        continue;
                    }
                    $bearbeitung_id = is_object($bearbeitung) ? $bearbeitung->ID : (is_array($bearbeitung) ? ($bearbeitung['ID'] ?? 0) : 0);
                    if (!$bearbeitung_id) {
                        continue;
                    }
                    if (isset($bearbeitungen[$bearbeitung_id])) {
                        continue;
                    }
                    $bearbeitung_post = get_post($bearbeitung_id);
                    if (empty($bearbeitung_post)) {
                        continue;
                    }
                    $bearbeitung_item = $bearbeitung_post->to_array();
                    $bearbeitung_fields = get_fields($bearbeitung_id);
                    if (!empty($bearbeitung_fields)) {
                        foreach ($bearbeitung_fields as $key => $value) {
                            $bearbeitung_item[$key] = $value;
                        }
                    }
                    $bearbeitungen[$bearbeitung_id] = $bearbeitung_item;
                }
            }

            return array_values($bearbeitungen);
        }
    }
    $PV_Customers = new PV_Customers();
}
