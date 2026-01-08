<?php

/**
 * Plan Verwaltung
 *
 * Plugin Name: Plan Verwaltung
 * Plugin URI:  https://sislakdesign.de/
 * Description: Verwalten Sie Wochenpläne und Jobs
 * Version:     1.0.0
 * Author:      Jan Feiler
 * Author URI:  https://janfeiler.de/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: plan-verwaltung
 * Domain Path: /languages
 * Requires at least: 4.9
 * Tested up to: 5.9
 * Requires PHP: 5.2.4
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

defined('ABSPATH') or die('Are you ok?');
#register_activation_hook(__FILE__, 'plan_verwaltung');
#date_default_timezone_set('Europe/Berlin');

global $wpdb;
global $pv_db_version;
$pv_db_version = '1.0';

define('PV_ADMINISTRATION_VERSION', $pv_db_version);
define('PV_ADMINISTRATION_PATH', plugin_dir_path(__FILE__));
define('PV_ADMINISTRATION_URL', plugin_dir_url(__FILE__));
define('PV_DATABASE_PREFIX', $wpdb->prefix . 'jf_pv_');

$upload = wp_upload_dir();
$upload_dir = $upload['basedir'];
$upload_url = $upload['baseurl'];
$ajax_url = admin_url('admin-ajax.php');

define('PV_UPLOAD_PATH', $upload_dir . '/plan-verwaltung/');
define('PV_UPLOAD_URL', $upload_url . '/plan-verwaltung/');
define('PV_AJAX_URL', $ajax_url);
define('PV_PLUGIN_FOLDER', 'plan-verwaltung');

// Define path and URL to the ACF plugin.
define('PV_ACF_PATH', PV_ADMINISTRATION_PATH . '/includes/acf-pro/');
define('PV_ACF_URL', PV_ADMINISTRATION_URL . '/includes/acf-pro/');

// Include the ACF plugin.
include_once(PV_ACF_PATH . 'acf.php');
add_filter('acf/settings/url', 'pv_acf_settings_url');
function pv_acf_settings_url($url)
{
    return PV_ACF_URL;
}
add_filter('acf/settings/show_admin', 'pv_acf_settings_show_admin');
function pv_acf_settings_show_admin($show_admin)
{
    return true;
}

// Nur für einen Post Type Revisionen behalten:
add_filter('wp_revisions_to_keep', function ($num, $post) {
    if ($post instanceof WP_Post && $post->post_type === 'bearbeitungen') {
        return 20; // Anzahl Revisionen
    }
    return 0;
}, 10, 2);


/* classes */
include_once PV_ADMINISTRATION_PATH . 'classes/class-pv-posttype.php';
include_once PV_ADMINISTRATION_PATH . 'classes/class-pv-customers.php';
include_once PV_ADMINISTRATION_PATH . 'classes/class-pv-users.php';
include_once PV_ADMINISTRATION_PATH . 'classes/class-pv-importapi.php';
include_once PV_ADMINISTRATION_PATH . 'classes/class-pv-frontend.php';
include_once PV_ADMINISTRATION_PATH . 'classes/class-pv-jobrequest.php';

/* controllers */
include_once PV_ADMINISTRATION_PATH . 'controllers/controller-pv-posttype.php';
include_once PV_ADMINISTRATION_PATH . 'controllers/controller-pv-admin.php';
include_once PV_ADMINISTRATION_PATH . 'controllers/controller-pv-frontend.php';
include_once PV_ADMINISTRATION_PATH . 'controllers/controller-pv-emails.php';
include_once PV_ADMINISTRATION_PATH . 'controllers/controller-pv-customers.php';
include_once PV_ADMINISTRATION_PATH . 'controllers/controller-pv-cron.php';
include_once PV_ADMINISTRATION_PATH . 'controllers/controller-pv-schedule.php';
