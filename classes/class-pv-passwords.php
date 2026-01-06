<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PV_Passwords')) {
    class PV_Passwords
    {
        public function __construct()
        {
            //
        }

        public function get_all_passwords()
        {
            $passwords = array();
            $password_posts = get_posts(array(
                'post_type' => 'passwords',
                'post_status' => 'publish',
                'numberposts' => -1
            ));

            if (!empty($password_posts)) {
                foreach ($password_posts as $password) {
                    $password = $password->to_array();
                    $password_fields = get_fields($password['ID']);

                    if (!empty($password_fields)) {
                        foreach ($password_fields as $key => $value) {
                            $password[$key] = $value;
                        }
                    }
                    $passwords[] = $password;
                }
            }
            return $passwords;
        }
    }
    $PV_Passwords = new PV_Passwords();
}
