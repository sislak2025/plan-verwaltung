<?php

defined('ABSPATH') or die('Are you ok?');

/**
 * One-time cleanup for removed password functionality.
 */
add_action('admin_init', 'pv_cleanup_password_feature');
function pv_cleanup_password_feature()
{
    if (get_option('pv_password_cleanup_done')) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;

    // Delete old password posts (former CPT "passwords")
    $password_post_ids = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", 'passwords'));
    if (!empty($password_post_ids)) {
        foreach ($password_post_ids as $post_id) {
            wp_delete_post((int) $post_id, true);
        }
    }

    // Remove legacy meta data from jobs/bearbeitungen related to development credentials
    $legacy_meta_keys = array(
        'pv_job_entwicklungen',
        '_pv_job_entwicklungen',
        'pv_entwicklung_kunde',
        '_pv_entwicklung_kunde',
        'pv_entwicklung_id',
        '_pv_entwicklung_id',
        'pv_entwicklung_url',
        '_pv_entwicklung_url',
        'pv_entwicklung_username',
        '_pv_entwicklung_username',
        'pv_entwicklung_passwort',
        '_pv_entwicklung_passwort',
        'pv_entwicklung_ftp_server',
        '_pv_entwicklung_ftp_server',
        'pv_entwicklung_ftp_port',
        '_pv_entwicklung_ftp_port',
        'pv_entwicklung_ftp_username',
        '_pv_entwicklung_ftp_username',
        'pv_entwicklung_ftp_passwort',
        '_pv_entwicklung_ftp_passwort',
        'pv_entwicklung_hosting_hoster',
        '_pv_entwicklung_hosting_hoster',
        'pv_entwicklung_hosting_username',
        '_pv_entwicklung_hosting_username',
        'pv_entwicklung_hosting_passwort',
        '_pv_entwicklung_hosting_passwort',
    );

    foreach ($legacy_meta_keys as $legacy_key) {
        delete_post_meta_by_key($legacy_key);
    }

    // Clean up any nested repeater/meta rows that were stored with numbered suffixes
    $wpdb->query(
        "DELETE FROM {$wpdb->postmeta} 
         WHERE meta_key LIKE 'pv_job_entwicklungen\_%' 
            OR meta_key LIKE '_pv_job_entwicklungen\_%' 
            OR meta_key LIKE 'pv_entwicklung\_%' 
            OR meta_key LIKE '_pv_entwicklung\_%'"
    );

    update_option('pv_password_cleanup_done', 1, false);
}
