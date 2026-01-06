<?php

add_filter('cron_schedules', 'pv_cron_schedules');
function pv_cron_schedules($schedules)
{
    if (!isset($schedules['pv_15_min'])) {
        $schedules['pv_15_min'] = array(
            'interval' => 15 * 60,
            'display' => __('Alle 15 Minuten')
        );
    }
    if (!isset($schedules['pv_24_h'])) {
        $schedules['pv_24_h'] = array(
            'interval' => 86400,
            'display' => __('Alle 24 Stunden')
        );
    }
    return $schedules;
}

if (!wp_next_scheduled('pv_import_data_15min')) {
    wp_schedule_event(current_time('timestamp'), 'pv_15_min', 'pv_import_data_15min');
}

add_action('pv_import_data_15min', 'pv_import_data_15min');
function pv_import_data_15min()
{
    $importapi_class = new PV_Importapi();
    $importapi_class->insert_request_data('projekte');
    #wp_mail('j.feiler@sislakdesign.de', 'Import erfolgreich', 'Der 15 Minuten Import wurde erfolgreich durchgeführt!');
}

if (!wp_next_scheduled('pv_daily_email_not_24h')) {
    #date_default_timezone_set('Europe/London');
    $date = strtotime('16:00:00 Europe/Berlin');
    wp_schedule_event($date, 'pv_24_h', 'pv_daily_email_not_24h'); // in controller-pv-emails.php
}

if (!wp_next_scheduled('pv_import_data_24h')) {
    wp_schedule_event(current_time('timestamp'), 'pv_24_h', 'pv_import_data_24h');
}

add_action('pv_import_data_24h', 'pv_import_data_24h');
function pv_import_data_24h()
{
    $importapi_class = new PV_Importapi();
    $importapi_class->insert_request_data('kunden');
    #wp_mail('j.feiler@sislakdesign.de', 'Import erfolgreich', 'Der 24 Stunden Import wurde erfolgreich durchgeführt!');
}
