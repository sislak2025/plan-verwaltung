<?php

add_action('init', 'pv_register_post_type');
function pv_register_post_type()
{
    register_jobs_posttype();
    register_jobanfragen_posttype();
    register_bearbeitungen_posttype();
    register_bearbeitungsgruppen_posttype();
    register_users_fields();
    register_kunden_posttype();
    register_trackings_posttype();
}

/*** JOBS START ***/

function register_jobs_posttype()
{
    $posttype_class = new PV_Posttype();

    $post_slug = 'jobs';
    $post_data['post_type'] = array(
        'label' => __($post_slug),
        'description' => __('Jobs'),
        'labels' => array(
            'name' => __('Jobs'),
            'singular_name' => __('Job'),
            'menu_name' => __('Jobs'),
            'parent_item' => __('Eltern Job'),
            'parent_item_colon' => __('Eltern Job:'),
            'all_items' => __('Alle Jobs'),
            'view_item' => __('Job ansehen'),
            'add_new_item' => __('Neuer Job'),
            'add_new' => __('Neuer Job'),
            'edit_item' => __('Job bearbeiten'),
            'update_item' => __('Job aktualisieren'),
            'search_items' => __('Job suchen'),
            'not_found' => __('Nichts gefunden'),
            'not_found_in_trash' => __('Nichts gefunden im Papierkorb'),
            'archive_title' => __('Jobs')
        ),
        'supports' => array('title', 'author', 'thumbnail', 'revisions', 'custom-fields'),
        'public' => true,
        'hierarchical' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'has_archive' => false,
        'can_export' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => false,
        'capability_type' => 'page',
        'rewrite' => array('slug' => $post_slug)
    );
    $post_data['custom_fields'] = array(
        'key' => 'job_informationen',
        'title' => 'Job Informationen',
        'fields' => array(
            array(
                'key' => 'pv_id',
                'label' => 'Interne Nummer',
                'name' => 'pv_id',
                'type' => 'number',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => true,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_projectno',
                'label' => 'Projektbezeichnung',
                'name' => 'pv_projectno',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_project_name',
                'label' => 'Projektname',
                'name' => 'pv_project_name',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_type',
                'label' => 'Typ',
                'name' => 'pv_type',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_status',
                'label' => 'Status',
                'name' => 'pv_status',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_orderno',
                'label' => 'Bestellnummer',
                'name' => 'pv_orderno',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_order_date',
                'label' => 'Bestelldatum',
                'name' => 'pv_order_date',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_delivery_date',
                'label' => 'Lieferdatum',
                'name' => 'pv_delivery_date',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_description',
                'label' => 'Beschreibung',
                'name' => 'pv_description',
                'type' => 'wysiwyg',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'tabs' => 'visual',
                'toolbar' => 'basic',
                'media_upload' => 0
            ),
            array(
                'key' => 'pv_jobs_bearbeitung',
                'label' => 'Bearbeitung verknüpfen',
                'name' => 'pv_jobs_bearbeitung',
                'type' => 'relationship',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'post_type' => 'bearbeitungen',
                'taxonomy' => '',
                'filters' => array('search'),
                'elements' => array(),
                'min' => 0,
                'max' => 1,
                'return_format' => 'object',
                'bidirectional' => true,
                'bidirectional_target' => array('pv_jobs')
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_slug,
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
    );
    $posttype_class->register_posttype($post_slug, $post_data);

    register_post_status('abgeschlossen', array(
        'label'  => _x('Abgeschlossen', $post_slug),
        'public' => true,
        'exclude_from_search' => true,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'protected' => true,
        '_builtin' => true,
        'label_count' => _n_noop('Abgeschlossen <span class="count">(%s)</span>', 'Abgeschlossen <span class="count">(%s)</span>'),
    ));

    // add_rewrite_rule('^jobs/([^/]+)/?$', 'index.php?taxonomy=Tierarten&Tierarten=$matches[1]', 'top');
    // add_rewrite_rule('^jobs/([^/]+)/?$', 'index.php?post_type=jobs&jobs=$matches[1]', 'top');
    // add_rewrite_rule('^jobs/([^/]+)/([^/]+)/?$', 'index.php?post_type=jobs&jobs=$matches[2]', 'top');
}

add_action('admin_footer-post.php', 'pv_jobs_append_status_list');
function pv_jobs_append_status_list()
{
    global $post;
    $complete = '';
    $label = '';
    if ($post->post_type == 'jobs') {
        if ($post->post_status == 'abgeschlossen') {
            $complete = ' selected=\"selected\"';
            $label = 'Abgeschlossen';
        } else {
            $label = get_post_status_object($post->post_status)->label;
        }
        echo '<script>
          jQuery(document).ready(function($){
                $("select#post_status").append("<option value=\"abgeschlossen\" ' . $complete . '>Abgeschlossen</option>");
                $("#post-status-display").text("' . $label . '");
          });
          </script>';
    }
}

add_filter('manage_edit-jobs_columns', 'pv_jobs_edit_admin_columns');
function pv_jobs_edit_admin_columns($columns)
{
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Title'),
        'project_name' => __('Projektname'),
        'type' => __('Typ'),
        'status' => __('Status'),
        'orderno' => __('Bestellnummer'),
        'order_date' => __('Bestelldatum'),
        'delivery_date' => __('Lieferdatum')
    );
    return $columns;
}

add_action('manage_jobs_posts_custom_column', 'pv_jobs_post_custom_columns');
function pv_jobs_post_custom_columns($column)
{
    global $post;
    switch ($column) {
        case 'project_name':
            echo get_field('pv_project_name');
            break;
        case 'type':
            echo get_field('pv_type');
            break;
        case 'status':
            echo get_field('pv_status');
            break;
        case 'orderno':
            echo get_field('pv_orderno');
            break;
        case 'order_date':
            echo get_the_date('d.m.Y', $post->ID);
            break;
        case 'order_date':
            if (empty(get_field('pv_order_date'))) break;
            $order_date = new DateTime(get_field('pv_order_date'));
            echo $order_date->format('d.m.Y');
            break;
        case 'delivery_date':
            if (empty(get_field('pv_delivery_date'))) break;
            $delivery_date = new DateTime(get_field('pv_delivery_date'));
            echo $delivery_date->format('d.m.Y');
            break;
    }
}

add_filter('manage_edit-jobs_sortable_columns', 'pv_jobs_sortierbare_columns');
function pv_jobs_sortierbare_columns($columns)
{
    $columns['project_name'] = 'pv_project_name';
    $columns['type'] = 'pv_type';
    $columns['status'] = 'pv_status';
    $columns['orderno'] = 'pv_orderno';
    $columns['order_date'] = 'pv_order_date';
    $columns['delivery_date'] = 'pv_delivery_date';

    return $columns;
}

/*** JOBS END ***/

/*** JOBANFRAGEN START ***/

function register_jobanfragen_posttype()
{
    $posttype_class = new PV_Posttype();

    $post_slug = 'jobanfragen';
    $post_data['post_type'] = array(
        'label' => __($post_slug),
        'description' => __('Jobanfragen'),
        'labels' => array(
            'name' => __('Jobanfragen'),
            'singular_name' => __('Jobanfrage'),
            'menu_name' => __('Jobanfragen'),
            'parent_item' => __('Eltern Jobanfrage'),
            'parent_item_colon' => __('Eltern Jobanfrage:'),
            'all_items' => __('Alle Jobanfragen'),
            'view_item' => __('Jobanfrage ansehen'),
            'add_new_item' => __('Neuer Jobanfrage'),
            'add_new' => __('Neuer Jobanfrage'),
            'edit_item' => __('Jobanfrage bearbeiten'),
            'update_item' => __('Jobanfrage aktualisieren'),
            'search_items' => __('Jobanfrage suchen'),
            'not_found' => __('Nichts gefunden'),
            'not_found_in_trash' => __('Nichts gefunden im Papierkorb'),
            'archive_title' => __('Jobanfragen')
        ),
        'supports' => array('title', 'author', 'thumbnail', 'revisions', 'custom-fields'),
        'public' => true,
        'hierarchical' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'has_archive' => false,
        'can_export' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => false,
        'capability_type' => 'page',
        'rewrite' => array('slug' => $post_slug)
    );
    $post_data['custom_fields'] = array(
        'key' => 'jobanfrage_informationen',
        'title' => 'Jobanfrage Informationen',
        'fields' => array(
            array(
                'key' => 'pv_requestid',
                'label' => 'Anfrage-ID',
                'name' => 'pv_requestid',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => 'pv_hidden_field',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => true,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_anfrage_projektname',
                'label' => 'Projektname',
                'name' => 'pv_anfrage_projektname',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'pv_requestid',
                            'operator' => '!=empty',
                        ),
                    ),
                ),
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'pv_anfrage_kunde',
                'label' => 'Kunde',
                'name' => 'pv_anfrage_kunde',
                'aria-label' => '',
                'type' => 'post_object',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'kunden',
                ),
                'post_status' => array(
                    0 => 'publish',
                ),
                'taxonomy' => '',
                'return_format' => 'object',
                'multiple' => 0,
                'allow_null' => 0,
                'bidirectional' => 0,
                'ui' => 1,
                'bidirectional_target' => array()
            ),
            array(
                'key' => 'pv_anfrage_mitarbeiter',
                'label' => 'Mitarbeiter',
                'name' => 'pv_anfrage_mitarbeiter',
                'type' => 'user',
                'prefix' => '',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => get_current_user_id(),
                'role' => array('bearbeiter', 'arbeitskraft'),
                'allow_null' => 0,
                'multiple' => false,
                'bidirectional' => false,
                'bidirectional_target' => array()
            ),
            array(
                'key' => 'pv_anfrage_frist',
                'label' => 'Frist',
                'name' => 'pv_anfrage_frist',
                'type' => 'date_picker',
                'prefix' => '',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_anfrage_notiz',
                'label' => 'Bemerkung',
                'name' => 'pv_anfrage_notiz',
                'type' => 'wysiwyg',
                'prefix' => '',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'tabs' => 'visual',
                'toolbar' => 'basic',
                'media_upload' => 0,
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_slug,
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
    );
    $posttype_class->register_posttype($post_slug, $post_data);

    register_post_status('angenommen', array(
        'label'  => _x('Angenommen', $post_slug),
        'public' => true,
        'exclude_from_search' => true,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'protected' => true,
        '_builtin' => true,
        'label_count' => _n_noop('Angenommen <span class="count">(%s)</span>', 'Angenommen <span class="count">(%s)</span>'),
    ));
    register_post_status('abgelehnt', array(
        'label'  => _x('Abgelehnt', $post_slug),
        'public' => true,
        'exclude_from_search' => true,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'protected' => true,
        '_builtin' => true,
        'label_count' => _n_noop('Abgelehnt <span class="count">(%s)</span>', 'Abgelehnt <span class="count">(%s)</span>'),
    ));
}

add_action('admin_footer-post.php', 'pv_jobanfragen_append_status_list');
function pv_jobanfragen_append_status_list()
{
    global $post;
    $complete = '';
    $state = '';
    $label = '';
    if ($post->post_type == 'jobanfragen') {
        if ($post->post_status == 'angenommen') {
            $complete = ' selected=\"selected\"';
            $state = 'angenommen';
            $label = 'Angenommen';
        } else if ($post->post_status == 'abgelehnt') {
            $complete = ' selected=\"selected\"';
            $state = 'abgelehnt';
            $label = 'Abgelehnt';
        } else {
            $label = get_post_status_object($post->post_status)->label;
        }
        echo '<script>
          jQuery(document).ready(function($){
                $("select#post_status").append("<option value=\"' . $state . '\" ' . $complete . '>' . $label . '</option>");
                $("#post-status-display").text("' . $label . '");
          });
          </script>';
    }
}

/*** JOBANFRAGEN END ***/

/*** BEARBEITUNGEN START ***/

function register_bearbeitungen_posttype()
{
    $posttype_class = new PV_Posttype();

    $druck_default = 'Druckerei: 
Material: 
Verarbeitung: ';
    $bilder_default = 'Platform: Shutterstock
Anzahl: ';
    $externe_default = 'Korrektorat: 
Texter: ';

    $post_slug = 'bearbeitungen';
    $post_data['post_type'] = array(
        'label' => __($post_slug),
        'description' => __('Bearbeitungen'),
        'labels' => array(
            'name' => __('Bearbeitungen'),
            'singular_name' => __('Bearbeitung'),
            'menu_name' => __('Bearbeitungen'),
            'parent_item' => __('Eltern Bearbeitung'),
            'parent_item_colon' => __('Eltern Bearbeitung:'),
            'all_items' => __('Alle Bearbeitungen'),
            'view_item' => __('Bearbeitung ansehen'),
            'add_new_item' => __('Neue Bearbeitung'),
            'add_new' => __('Neue Bearbeitung'),
            'edit_item' => __('Bearbeitung bearbeiten'),
            'update_item' => __('Bearbeitung aktualisieren'),
            'search_items' => __('Bearbeitung suchen'),
            'not_found' => __('Nichts gefunden'),
            'not_found_in_trash' => __('Nichts gefunden im Papierkorb'),
            'archive_title' => __('Bearbeitungen')
        ),
        'supports' => array('title', 'author', 'revisions', 'custom-fields', 'page-attributes'),
        'public' => true,
        'hierarchical' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'has_archive' => false,
        'can_export' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => false,
        'capability_type' => 'page',
        'rewrite' => array('slug' => $post_slug)
    );
    $post_data['custom_fields'] = array(
        'key' => 'bearbeitung_informationen',
        'title' => 'Bearbeitung Informationen',
        'fields' => array(
            array(
                'key' => 'pv_jobs',
                'label' => 'Job verknüpfen',
                'name' => 'pv_jobs',
                'type' => 'relationship',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'post_type' => 'jobs',
                'taxonomy' => '',
                'filters' => array('search'),
                'elements' => array(),
                'min' => 0,
                'max' => 1,
                'return_format' => 'object',
                'bidirectional' => true,
                'bidirectional_target' => array('pv_jobs_bearbeitung')
            ),
            array(
                'key' => 'pv_accordion_job_gruppierung',
                'label' => 'Job mit anderen Jobs gruppieren',
                'type' => 'accordion',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => 'pv_accordion_job_gruppierung',
                ),
                'open' => false
            ),
            array(
                'key' => 'pv_job_gruppe',
                'label' => 'Job einer Gruppe hinzufügen',
                'name' => 'pv_job_gruppe',
                'aria-label' => '',
                'type' => 'relationship',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'bearbeitungsgruppen',
                ),
                'post_status' => array(
                    0 => 'publish',
                ),
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                ),
                'return_format' => 'object',
                'min' => '',
                'max' => '1',
                'elements' => '',
                'bidirectional' => 1,
                'bidirectional_target' => array(
                    0 => 'pv_group_jobs',
                )
            ),
            array(
                'key' => 'pv_accordion_job_gruppierung_end',
                'label' => 'Job mit anderen Jobs gruppierenEnde',
                'type' => 'accordion',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'endpoint' => true
            ),
            array(
                'key' => 'pv_bearbeiter',
                'label' => 'Mitarbeiter verknüpfen',
                'name' => 'pv_bearbeiter',
                'type' => 'user',
                'prefix' => '',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'role' => array('bearbeiter', 'arbeitskraft'),
                'allow_null' => 0,
                'multiple' => true,
                'bidirectional' => true,
                'bidirectional_target' => array('pv_user_bearbeitung')
            ),
            array(
                'key' => 'pv_bearbeitung_status',
                'label' => 'Status',
                'name' => 'pv_bearbeitung_status',
                'type' => 'select',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'choices' => array(),
                'allow_null' => true,
                'multiple' => 0,
                'ui' => 0,
                'ajax' => 0,
                'placeholder' => '',
            ),
            array(
                'key' => 'pv_finish_date',
                'label' => 'Frist',
                'name' => 'pv_finish_date',
                'type' => 'date_picker',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_job_color',
                'label' => 'Farbe',
                'name' => 'pv_job_color',
                'type' => 'radio',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => 'pv_acf_hide_title',
                    'id' => '',
                ),
                'default_value' => '',
                'choices' => array(
                    'keine'   => '<span class="pv_job_color_keine">In Pipeline</span>',
                    'red'   => '<span class="pv_job_color_red">Prio Job</span>',
                    'orange'   => '<span class="pv_job_color_orange">Standard Job</span>',
                    'green'   => '<span class="pv_job_color_green">Bei Kunde, in Druck, Fertiggestellt für Termin</span>',
                    'blue'   => '<span class="pv_job_color_blue">Warten auf Infos vom Kunden</span>',
                    'purple'   => '<span class="pv_job_color_purple">In Abstimmung mit Kontakter</span>'
                ),
                'other_choice' => 0,
                'save_other_choice' => 0,
                'layout' => 0,
            ),
            array(
                'key' => 'pv_group_additional',
                'label' => 'Zusätzliche Details',
                'type' => 'group',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'sub_fields' => array(
                    array(
                        'key' => 'pv_tab_updates',
                        'label' => 'Updates',
                        'type' => 'tab',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        )
                    ),
                    array(
                        'key' => 'pv_job_updates',
                        'label' => 'Zwischenstände',
                        'name' => 'pv_job_updates',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'table',
                        'button_label' => 'Zwischenstand hinzufügen',
                        'sub_fields' => array(
                            array(
                                'key' => 'pv_job_updates_date',
                                'label' => 'Datum',
                                'name' => 'pv_job_updates_date',
                                'type' => 'date_picker',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '20',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => date('Ymd'),
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                                'readonly' => false,
                                'disabled' => false,
                            ),
                            array(
                                'key' => 'pv_job_updates_text',
                                'label' => 'Beschreibung',
                                'name' => 'pv_job_updates_text',
                                'type' => 'textarea',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '65',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => '1',
                                'new_lines' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),
                            array(
                                'key' => 'pv_job_updates_kuerzel',
                                'label' => 'Kürzel',
                                'name' => 'pv_job_updates_kuerzel',
                                'type' => 'text',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '15',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => wp_get_current_user()->nickname,
                                'placeholder' => ''
                            )
                        )
                    ),
                    array(
                        'key' => 'pv_tab_bestandteile',
                        'label' => 'Bestandteile',
                        'type' => 'tab',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        )
                    ),
                    array(
                        'key' => 'pv_job_bestandteile',
                        'label' => 'Bestandteile',
                        'name' => 'pv_job_bestandteile',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'table',
                        'button_label' => 'Bestandteil hinzufügen',
                        'sub_fields' => array(
                            array(
                                'key' => 'pv_job_bestandteile_done',
                                'label' => 'Erledigt',
                                'name' => 'pv_job_bestandteile_done',
                                'type' => 'true_false',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '20',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'ui' => true
                            ),
                            array(
                                'key' => 'pv_job_bestandteile_text',
                                'label' => 'Beschreibung',
                                'name' => 'pv_job_bestandteile_text',
                                'type' => 'textarea',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '65',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => '1',
                                'new_lines' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),
                            array(
                                'key' => 'pv_job_bestandteile_kuerzel',
                                'label' => 'Kürzel',
                                'name' => 'pv_job_bestandteile_kuerzel',
                                'type' => 'text',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '15',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => wp_get_current_user()->nickname,
                                'placeholder' => ''
                            )
                        )
                    ),
                    array(
                        'key' => 'pv_tab_dateien',
                        'label' => 'Dateien',
                        'type' => 'tab',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        )
                    ),
                    array(
                        'key' => 'pv_job_dateien',
                        'label' => 'Dateien / Dokumente',
                        'name' => 'pv_job_dateien',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'table',
                        'button_label' => 'Datei hinzufügen',
                        'sub_fields' => array(
                            array(
                                'key' => 'pv_job_datei',
                                'label' => 'Datei',
                                'name' => 'pv_job_datei',
                                'type' => 'file',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'return_format' => 'array',
                                'preview_size' => 'thumbnail',
                                'library' => 'uploadedTo',
                                'min_size' => 0,
                                'max_size' => 0,
                                'mime_types' => '',
                            ),
                            array(
                                'key' => 'pv_job_datei_kuerzel',
                                'label' => 'Kürzel',
                                'name' => 'pv_job_datei_kuerzel',
                                'type' => 'text',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => 'pv_hidden_field',
                                    'id' => '',
                                ),
                                'default_value' => wp_get_current_user()->nickname,
                                'placeholder' => ''
                            )
                        )
                    ),
                    array(
                        'key' => 'pv_tab_abwicklungsinfos',
                        'label' => 'Abwicklungsinfos',
                        'type' => 'tab',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        )
                    ),
                    array(
                        'key' => 'pv_job_abwicklungsinfos',
                        'label' => 'Abwicklungsinformationen',
                        'name' => 'pv_job_abwicklungsinfos',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'block',
                        'button_label' => 'Information hinzufügen',
                        'sub_fields' => array(
                            array(
                                'key' => 'pv_job_abwicklung_typ',
                                'label' => 'Typ',
                                'name' => 'pv_job_abwicklung_typ',
                                'type' => 'select',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => 'pv_acf_hide_title',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'choices' => array(
                                    'Druck' => 'Druck',
                                    'Bilder' => 'Bilder',
                                    'Externe' => 'Externe',
                                    'Sonstiges' => 'Sonstiges'
                                ),
                                'allow_null' => true,
                                'multiple' => 0,
                                'ui' => 0,
                                'ajax' => 0,
                                'placeholder' => '',
                            ),
                            array(
                                'key' => 'pv_job_abwicklung_druck',
                                'label' => 'Druck',
                                'name' => 'pv_job_abwicklung_druck',
                                'type' => 'textarea',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'pv_job_abwicklung_typ',
                                            'operator' => '==',
                                            'value' => 'Druck',
                                        ),
                                    )
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => 'pv_acf_hide_title',
                                    'id' => '',
                                ),
                                'default_value' => $druck_default,
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => '3',
                                'new_lines' => 'br',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),
                            array(
                                'key' => 'pv_job_abwicklung_bilder',
                                'label' => 'Bilder',
                                'name' => 'pv_job_abwicklung_bilder',
                                'type' => 'textarea',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'pv_job_abwicklung_typ',
                                            'operator' => '==',
                                            'value' => 'Bilder',
                                        ),
                                    )
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => 'pv_acf_hide_title',
                                    'id' => '',
                                ),
                                'default_value' => $bilder_default,
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => '3',
                                'new_lines' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),
                            array(
                                'key' => 'pv_job_abwicklung_externe',
                                'label' => 'Externe',
                                'name' => 'pv_job_abwicklung_externe',
                                'type' => 'textarea',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'pv_job_abwicklung_typ',
                                            'operator' => '==',
                                            'value' => 'Externe',
                                        ),
                                    )
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => 'pv_acf_hide_title',
                                    'id' => '',
                                ),
                                'default_value' => $externe_default,
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => '3',
                                'new_lines' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),
                            array(
                                'key' => 'pv_job_abwicklung_sonstiges',
                                'label' => 'Sonstiges',
                                'name' => 'pv_job_abwicklung_sonstiges',
                                'type' => 'textarea',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => true,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'pv_job_abwicklung_typ',
                                            'operator' => '==',
                                            'value' => 'Sonstiges',
                                        ),
                                    )
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => 'pv_acf_hide_title',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => '3',
                                'new_lines' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),
                            array(
                                'key' => 'pv_job_abwicklung_kuerzel',
                                'label' => 'Kürzel',
                                'name' => 'pv_job_abwicklung_kuerzel',
                                'type' => 'text',
                                'prefix' => '',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => 'pv_hidden_field',
                                    'id' => '',
                                ),
                                'default_value' => wp_get_current_user()->nickname,
                                'placeholder' => ''
                            )
                        )
                    )
                )
            ),
            array(
                'key' => 'pv_small_notiz',
                'label' => 'Hinweis & Kurzupdate',
                'name' => 'pv_small_notiz',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'Erscheint in der Job-Tabelle und Job-Liste',
                'prepend' => '',
                'append' => '',
                'maxlength' => '60',
                'readonly' => false,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_notiz',
                'label' => 'Bemerkung',
                'name' => 'pv_notiz',
                'type' => 'wysiwyg',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'tabs' => 'visual',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ),
            array(
                'key' => 'pv_private_visible',
                'label' => 'Sichtbarkeit',
                'name' => 'pv_private_visible',
                'type' => 'checkbox',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => 'pv_private_visible',
                    'id' => '',
                ),
                'default_value' => '',
                'choices' => array(
                    'not_visible' => 'Eintrag nur für Mitarbeiter sichtbar'
                ),
                'layout' => 'vertical',
                'allow_custom' => false,
                'save_custom' => false,
                'toggle' => false,
                'return_format' => 'value',
            ),
            array(
                'key' => 'pv_add_tracking',
                'label' => 'Tracking hinzufügen',
                'name' => 'pv_add_tracking',
                'type' => 'message',
                'message' => '<a href="/wp-admin/post-new.php?post_type=trackings" class="btn btn-primary pv_add_tracking_button" target="_blank">Tracking hinzufügen <span class="pv-spinner spinner-border"></span></a>',
                'required' => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'pv_jobs',
                            'operator' => '!=empty',
                            'value' => '',
                        ),
                    )
                ),
                'wrapper' => array(
                    'width' => '50',
                    'class' => 'pv_add_tracking',
                    'id' => '',
                ),
                'new_lines' => ''
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_slug,
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
    );
    $posttype_class->register_posttype($post_slug, $post_data);

    register_post_status('fertiggestellt', array(
        'label'  => _x('Fertiggestellt', $post_slug),
        'public' => true,
        'exclude_from_search' => true,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'protected' => true,
        '_builtin' => true,
        'label_count' => _n_noop('Fertiggestellt <span class="count">(%s)</span>', 'Fertiggestellt <span class="count">(%s)</span>'),
    ));
    register_post_status('abgebrochen', array(
        'label'  => _x('Abgebrochen', $post_slug),
        'public' => true,
        'exclude_from_search' => true,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'protected' => true,
        '_builtin' => true,
        'label_count' => _n_noop('Abgebrochen <span class="count">(%s)</span>', 'Abgebrochen <span class="count">(%s)</span>'),
    ));
}

add_action('admin_footer-post.php', 'pv_bearbeitungen_append_status_list');
function pv_bearbeitungen_append_status_list()
{
    global $post;
    $complete = '';
    $state = '';
    $label = '';
    if ($post->post_type == 'bearbeitungen') {
        if ($post->post_status == 'fertiggestellt') {
            $complete = ' selected=\"selected\"';
            $state = 'fertiggestellt';
            $label = 'Fertiggestellt';
        } else if ($post->post_status == 'abgebrochen') {
            $complete = ' selected=\"selected\"';
            $state = 'abgebrochen';
            $label = 'Abgebrochen';
        } else {
            $label = get_post_status_object($post->post_status)->label;
        }
        echo '<script>
          jQuery(document).ready(function($){
                $("select#post_status").append("<option value=\"' . $state . '\" ' . $complete . '>' . $label . '</option>");
                $("#post-status-display").text("' . $label . '");
          });
          </script>';
    }
}

add_filter('manage_edit-bearbeitungen_columns', 'pv_bearbeitungen_edit_admin_columns');
function pv_bearbeitungen_edit_admin_columns($columns)
{
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Title'),
        'bearbeiter' => __('Mitarbeiter'),
        'status' => __('Status'),
        'create_date' => __('Anlagedatum'),
        'finish_date' => __('Fristdatum')
    );
    return $columns;
}

add_action('manage_bearbeitungen_posts_custom_column', 'pv_bearbeitungen_post_custom_columns');
function pv_bearbeitungen_post_custom_columns($column)
{
    global $post;
    switch ($column) {
        case 'bearbeiter':
            if (empty(get_field('pv_bearbeiter'))) break;
            $bearbeiter = array();
            foreach (get_field('pv_bearbeiter') as $user) {
                $bearbeiter[] = $user['display_name'];
            }
            echo implode(', ', $bearbeiter);
            break;
        case 'status':
            echo get_field('pv_bearbeitung_status');
            break;
        case 'create_date':
            echo get_the_date('d.m.Y', $post->ID);
            break;
        case 'finish_date':
            if (empty(get_field('pv_finish_date'))) break;
            $date = DateTime::createFromFormat('d/m/Y', get_field('pv_finish_date'));
            echo $date->format('d.m.Y');
            break;
    }
}

add_filter('manage_edit-bearbeitungen_sortable_columns', 'pv_bearbeitungen_sortierbare_columns');
function pv_bearbeitungen_sortierbare_columns($columns)
{
    $columns['bearbeiter'] = 'pv_bearbeiter';
    $columns['status'] = 'pv_status';
    $columns['create_date'] = 'pv_create_date';
    $columns['finish_date'] = 'pv_finish_date';

    return $columns;
}

/*** BEARBEITUNGEN END ***/

/*** BEARBEITUNGSGRUPPEN START ***/

function register_bearbeitungsgruppen_posttype()
{
    $posttype_class = new PV_Posttype();

    $post_slug = 'bearbeitungsgruppen';
    $post_data['post_type'] = array(
        'label' => __($post_slug),
        'description' => __('Bearbeitungsgruppen'),
        'labels' => array(
            'name' => __('Bearbeitungsgruppen'),
            'singular_name' => __('Bearbeitung'),
            'menu_name' => __('Bearbeitungsgruppen'),
            'parent_item' => __('Eltern Bearbeitungsgruppe'),
            'parent_item_colon' => __('Eltern Bearbeitungsgruppe:'),
            'all_items' => __('Alle Bearbeitungsgruppen'),
            'view_item' => __('Bearbeitungsgruppe ansehen'),
            'add_new_item' => __('Neue Bearbeitungsgruppe'),
            'add_new' => __('Neue Bearbeitungsgruppe'),
            'edit_item' => __('Bearbeitungsgruppe bearbeiten'),
            'update_item' => __('Bearbeitungsgruppe aktualisieren'),
            'search_items' => __('Bearbeitungsgruppe suchen'),
            'not_found' => __('Nichts gefunden'),
            'not_found_in_trash' => __('Nichts gefunden im Papierkorb'),
            'archive_title' => __('Bearbeitungsgruppen')
        ),
        'supports' => array('title', 'author', 'revisions', 'custom-fields', 'page-attributes'),
        'public' => true,
        'hierarchical' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'has_archive' => false,
        'can_export' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => false,
        'capability_type' => 'page',
        'rewrite' => array('slug' => $post_slug)
    );
    $post_data['custom_fields'] = array(
        'key' => 'bearbeitungsgruppe_informationen',
        'title' => 'Bearbeitungsgruppe Informationen',
        'fields' => array(
            array(
                'key' => 'pv_group_jobs',
                'label' => 'Jobs auswählen',
                'name' => 'pv_group_jobs',
                'aria-label' => '',
                'type' => 'relationship',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'bearbeitungen',
                ),
                'post_status' => array(
                    0 => 'publish',
                ),
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                ),
                'return_format' => 'object',
                'min' => '2',
                'max' => '',
                'elements' => '',
                'bidirectional' => 1,
                'bidirectional_target' => array(
                    0 => 'pv_job_gruppe',
                )
            ),
            array(
                'key' => 'pv_group_small_notiz',
                'label' => 'Hinweis & Kurzupdate',
                'name' => 'pv_group_small_notiz',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'Erscheint in der Job-Tabelle und Job-Liste',
                'prepend' => '',
                'append' => '',
                'maxlength' => '60',
                'readonly' => false,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_group_description',
                'label' => 'Beschreibung',
                'name' => 'pv_group_description',
                'type' => 'wysiwyg',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'tabs' => 'visual',
                'toolbar' => 'basic',
                'media_upload' => 0
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_slug,
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
    );
    $posttype_class->register_posttype($post_slug, $post_data);
}

/*** BEARBEITUNGSGRUPPEN END ***/

/*** BENUTZER START ***/

function register_users_fields()
{
    $posttype_class = new PV_Posttype();

    $data = array(
        'key' => 'benutzer_informationen',
        'title' => 'Benutzer Informationen',
        'fields' => array(
            array(
                'key' => 'pv_urno_person',
                'label' => 'Benutzernummer',
                'name' => 'pv_urno_person',
                'type' => 'number',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => true,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_abteilung',
                'label' => 'Abteilung',
                'name' => 'pv_abteilung',
                'type' => 'select',
                'prefix' => '',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'choices' => array(
                    'web' => 'Web-Abteilung',
                    'print' => 'Kreation'
                ),
                'allow_null' => true,
                'multiple' => 0,
                'ui' => 0,
                'ajax' => 0,
                'placeholder' => '',
            ),
            array(
                'key' => 'pv_user_bearbeitung',
                'label' => 'Bearbeitung verknüpfen',
                'name' => 'pv_user_bearbeitung',
                'type' => 'relationship',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'post_type' => 'bearbeitungen',
                'taxonomy' => '',
                'filters' => array('search'),
                'elements' => array(),
                'min' => 0,
                'max' => 1,
                'return_format' => 'object',
                'bidirectional' => true,
                'bidirectional_target' => array('pv_bearbeiter')
            ),
            array(
                'key' => 'pv_user_disabled',
                'label' => 'Benutzerstatus',
                'name' => 'pv_user_disabled',
                'type' => 'true_false',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'message' => 'Benutzer ausblenden',
            ),
            array(
                'key' => 'pv_user_image',
                'label' => 'Benutzerfoto',
                'name' => 'pv_user_image',
                'type' => 'image',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'library' => 'uploadedTo',
                'min_width' => 0,
                'min_height' => 0,
                'min_size' => 0,
                'max_width' => 0,
                'max_height' => 0,
                'max_size' => 0,
                'mime_types' => ''
            ),
            array(
                'key' => 'pv_user_notifications',
                'label' => 'Offene Benachrichtigungen',
                'name' => 'pv_user_notifications',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => false,
                'disabled' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'user_role',
                    'operator' => '==',
                    'value' => 'bearbeiter',
                ),
            ),
            array(
                array(
                    'param' => 'user_role',
                    'operator' => '==',
                    'value' => 'arbeitskraft',
                )
            ),
            array(
                array(
                    'param' => 'user_role',
                    'operator' => '==',
                    'value' => 'inaktiv',
                )
            ),
            array(
                array(
                    'param' => 'user_role',
                    'operator' => '==',
                    'value' => 'administrator',
                )
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
    );
    $posttype_class->register_fieldgroups($data);
}

/*** BENUTZER END ***/

/*** KUNDEN START ***/

function register_kunden_posttype()
{
    $posttype_class = new PV_Posttype();

    $post_slug = 'kunden';
    $post_data['post_type'] = array(
        'label' => __($post_slug),
        'description' => __('Kunden'),
        'labels' => array(
            'name' => __('Kunden'),
            'singular_name' => __('Kunde'),
            'menu_name' => __('Kunden'),
            'parent_item' => __('Eltern Kunde'),
            'parent_item_colon' => __('Eltern Kunde:'),
            'all_items' => __('Alle Kunden'),
            'view_item' => __('Kunde ansehen'),
            'add_new_item' => __('Neuer Kunde'),
            'add_new' => __('Neuer Kunde'),
            'edit_item' => __('Kunde bearbeiten'),
            'update_item' => __('Kunde aktualisieren'),
            'search_items' => __('Kunde suchen'),
            'not_found' => __('Nichts gefunden'),
            'not_found_in_trash' => __('Nichts gefunden im Papierkorb'),
            'archive_title' => __('Kunden')
        ),
        'supports' => array('title', 'custom-fields'),
        'public' => true,
        'hierarchical' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'has_archive' => false,
        'can_export' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => false,
        'capability_type' => 'page',
        'rewrite' => array('slug' => $post_slug)
    );
    $post_data['custom_fields'] = array(
        'key' => 'kunde_informationen',
        'title' => 'Kunde Informationen',
        'fields' => array(
            array(
                'key' => 'pv_id',
                'label' => 'Kundennummer',
                'name' => 'pv_id',
                'type' => 'number',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => true,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_shortname',
                'label' => 'Kürzel',
                'name' => 'pv_shortname',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_prefix',
                'label' => 'Prefix',
                'name' => 'pv_prefix',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_slug,
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
    );
    $posttype_class->register_posttype($post_slug, $post_data);
}

add_filter('manage_edit-kunden_columns', 'pv_kunden_edit_admin_columns');
function pv_kunden_edit_admin_columns($columns)
{
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Kunde'),
        'shortname' => __('Kürzel'),
        'prefix' => __('Prefix'),
    );
    return $columns;
}

add_action('manage_kunden_posts_custom_column', 'pv_kunden_post_custom_columns');
function pv_kunden_post_custom_columns($column)
{
    global $post;
    switch ($column) {
        case 'shortname':
            echo get_field('pv_shortname');
            break;
        case 'prefix':
            echo get_field('pv_prefix');
            break;
    }
}

add_filter('manage_edit-kunden_sortable_columns', 'pv_kunden_sortierbare_columns');
function pv_kunden_sortierbare_columns($columns)
{
    $columns['shortname'] = 'pv_shortname';
    return $columns;
}

/*** KUNDEN END ***/

/*** TRACKINGS START ***/

function register_trackings_posttype()
{
    $posttype_class = new PV_Posttype();

    $post_slug = 'trackings';
    $post_data['post_type'] = array(
        'label' => __($post_slug),
        'description' => __('Trackings'),
        'labels' => array(
            'name' => __('Trackings'),
            'singular_name' => __('Tracking'),
            'menu_name' => __('Trackings'),
            'parent_item' => __('Eltern Tracking'),
            'parent_item_colon' => __('Eltern Tracking:'),
            'all_items' => __('Alle Trackings'),
            'view_item' => __('Tracking ansehen'),
            'add_new_item' => __('Neues Tracking'),
            'add_new' => __('Neues Tracking'),
            'edit_item' => __('Tracking bearbeiten'),
            'update_item' => __('Tracking aktualisieren'),
            'search_items' => __('Tracking suchen'),
            'not_found' => __('Nichts gefunden'),
            'not_found_in_trash' => __('Nichts gefunden im Papierkorb'),
            'archive_title' => __('Trackings')
        ),
        'supports' => array('title', 'author', 'custom-fields'),
        'public' => true,
        'hierarchical' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'has_archive' => false,
        'can_export' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => false,
        'capability_type' => 'page',
        'rewrite' => array('slug' => $post_slug)
    );
    $post_data['custom_fields'] = array(
        'key' => 'tracking_informationen',
        'title' => 'Tracking Informationen',
        'fields' => array(
            array(
                'key' => 'pv_tracking_id',
                'label' => 'Trackingsnummer',
                'name' => 'pv_tracking_id',
                'type' => 'number',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => 'pv_hidden_field',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => true,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_tracking_person',
                'label' => 'Mitarbeiter',
                'name' => 'pv_tracking_person',
                'type' => 'user',
                'prefix' => '',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => 'pv_hidden_field',
                    'id' => '',
                ),
                'default_value' => '',
                'role' => array('bearbeiter', 'arbeitskraft'),
                'allow_null' => 0,
                'multiple' => false,
                'bidirectional' => false,
                'bidirectional_target' => array('pv_user_bearbeitung')
            ),
            array(
                'key' => 'pv_tracking_job',
                'label' => 'Job',
                'name' => 'pv_tracking_job',
                'type' => 'relationship',
                'prefix' => '',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => 'pv_hidden_field',
                    'id' => '',
                ),
                'default_value' => '',
                'post_type' => 'jobs',
                'taxonomy' => '',
                'filters' => array('search'),
                'elements' => array(),
                'min' => 0,
                'max' => 1,
                'return_format' => 'object',
                'bidirectional' => false,
                'bidirectional_target' => array('pv_jobs_bearbeitung')
            ),
            array(
                'key' => 'pv_service_code',
                'label' => 'Leistungsart',
                'name' => 'pv_service_code',
                'type' => 'select',
                'prefix' => '',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'choices' => array(),
                'allow_null' => true,
                'multiple' => 0,
                'ui' => 0,
                'ajax' => 0,
                'placeholder' => '',
            ),
            array(
                'key' => 'pv_tracking_time',
                'label' => 'Zeiteingabe',
                'name' => 'pv_tracking_time',
                'type' => 'number',
                'prefix' => '',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'pv_tracking_beschreibung',
                'label' => 'Beschreibung',
                'name' => 'pv_tracking_beschreibung',
                'type' => 'textarea',
                'prefix' => '',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => '5',
                'new_lines' => '',
                'readonly' => 0,
                'disabled' => 0,
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_slug,
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
    );
    $posttype_class->register_posttype($post_slug, $post_data);
}

/*** TRACKINGS END ***/

/*** FILTER/ACTIONS START ***/

// ACF-Frontend: Immer Revision erzeugen + ACF-Metas in die neue Revision kopieren. Funktioniert auch, wenn zuvor noch keine Revision existierte.
add_action('acf/save_post', function ($post_id) {
    if (!is_numeric($post_id)) return;

    $post_id = (int) $post_id;
    if (get_post_type($post_id) !== 'bearbeitungen') return;
    if (!post_type_supports('bearbeitungen', 'revisions')) return;

    // Immer eine Revision erzwingen (auch nur bei Meta-Änderungen)
    add_filter('wp_save_post_revision_check_for_changes', '__return_false', 10);
    $rev_id = wp_save_post_revision($post_id);
    remove_filter('wp_save_post_revision_check_for_changes', '__return_false', 10);

    if (!$rev_id || is_wp_error($rev_id)) return;

    // ACF-Metas kopieren
    if (function_exists('acf_copy_postmeta')) {
        acf_copy_postmeta($post_id, $rev_id);
    } elseif (!empty($_POST['acf']) && is_array($_POST['acf'])) {
        foreach ($_POST['acf'] as $field_key => $value) {
            if (!function_exists('acf_is_field_key') || !acf_is_field_key($field_key)) continue;
            $field = acf_get_field($field_key);
            if (!empty($field['name'])) {
                update_post_meta($rev_id, $field['name'], $value);
                update_post_meta($rev_id, '_' . $field['name'], $field_key);
            }
        }
    }
}, 20);







add_filter('pre_get_posts', 'pv_oder_by_title_cpt');
function pv_oder_by_title_cpt($query)
{
    if ($query->is_admin) {
        $post_type = $query->get('post_type');
        if ($post_type == 'kunden') {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
        }
    }
    return $query;
}

add_filter('acf/load_value/name=pv_bearbeiter', 'pv_bearbeiter_load_value', 10, 3);
function pv_bearbeiter_load_value($value, $post_id, $field)
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_add_job') {
        $value = $_POST['user_id'];
    }
    return $value;
}

add_filter('acf/load_field/name=pv_service_code', 'pv_service_code_load_value');
function pv_service_code_load_value($field)
{
    $posttype_class = new PV_Posttype();
    $status = $posttype_class->get_service_codes();
    if (!empty($status)) {
        foreach ($status as $key => $value) {
            $field['choices'][$key] = $value;
        }
    }
    return $field;
}

add_filter('acf/load_field/name=pv_bearbeitung_status', 'pv_bearbeitung_status_load_value');
function pv_bearbeitung_status_load_value($field)
{
    $posttype_class = new PV_Posttype();
    $status = $posttype_class->get_projektstatus();
    if (!empty($status)) {
        foreach ($status as $key => $value) {
            $field['choices'][$key] = $value;
        }
    }

    $private_job = (!empty($_POST['job_id']) ? (!empty(get_field('pv_private_visible', $_POST['job_id'])) ? get_field('pv_private_visible', $_POST['job_id']) : array()) : array());
    if (!empty($_POST['status']) && $_POST['status'] == 'false' && !in_array('not_visible', $private_job)) {
        unset($field['choices']['Abschluss']['Geliefert - Fertiggestellt']);
    }
    return $field;
}

add_action('acf/save_post', 'pv_set_job_done');
function pv_set_job_done($post_id)
{
    $posttype = get_post_type($post_id);
    $status = get_field('pv_bearbeitung_status', $post_id);

    if ('bearbeitungen' == $posttype && !empty($status)) {
        if ($status == 'Geliefert - Fertiggestellt') {
            wp_update_post(array(
                'ID' => $post_id,
                'post_status' => 'fertiggestellt'
            ));
        } else if (get_post_status($post_id) == 'fertiggestellt' && $status != 'Geliefert - Fertiggestellt') {
            wp_update_post(array(
                'ID' => $post_id,
                'post_status' => 'publish'
            ));
        }

        if ($status == 'Abgebrochen') {
            wp_update_post(array(
                'ID' => $post_id,
                'post_status' => 'abgebrochen'
            ));
        }
    }
}

add_filter('acf/fields/relationship/result/name=pv_jobs', 'pv_jobs_posts_filter', 10, 4);
function pv_jobs_posts_filter($text, $post, $field, $post_id)
{
    $jobs_bearbeitung = get_field('pv_jobs_bearbeitung', $post->ID);
    if (!empty($jobs_bearbeitung)) {
        $text = '<span class="pv_already_selected" id="jobbearbeitung-' . $jobs_bearbeitung[0]->ID . '">' . $text . '</span>';
    }
    return $text;
}

add_filter('kses_allowed_protocols', function ($protocols) {
    $protocols[] = 'data';

    return $protocols;
});

#add_shortcode('shortcode', 'test_doppelt');
function test_doppelt()
{
    $query = get_posts(array(
        'post_type' => 'jobs',
        'post_status' => 'publish',
        'posts_per_page' => -1
    ));
    foreach ($query as $post) {
        $bearbeitungen = get_field('pv_jobs_bearbeitung', $post->ID);
        if (!empty($bearbeitungen)) {
            if (count($bearbeitungen) > 1) {
                echo $post->post_title . '<br>';
            }
        }
    }
}

#add_filter('acf/fields/relationship/query/name=pv_jobs', 'pv_jobs_status_filter', 10, 3);
#add_filter('acf/fields/relationship/query/name=pv_jobs_bearbeitung', 'pv_jobs_status_filter', 10, 3);
function pv_jobs_status_filter($args, $field, $post_id)
{
    $args['post_status'] = array('publish', 'abgeschlossen');
    return $args;
}

add_filter('acf/fields/relationship/query/name=pv_job_gruppe', 'pv_group_jobs_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=pv_group_jobs', 'pv_group_jobs_filter', 10, 3);
function pv_group_jobs_filter($args, $field, $post_id)
{
    $args['post_status'] = array('publish');
    return $args;
}

#add_filter('acf/fields/relationship/result/name=pv_group_jobs', 'pv_group_jobs_posts_filter', 10, 4);
function pv_group_jobs_posts_filter($text, $post, $field, $post_id)
{
    $jobs_gruppe = get_field('pv_job_gruppe', $post->ID);
    if (!empty($jobs_gruppe)) {
        $text = '<span class="pv_already_selected" data-group="' . $jobs_gruppe[0]->ID . '">' . $text . '</span>';
    }
    return $text;
}

add_action('save_post_bearbeitungen', 'pv_add_beschreibung_bearbeitung', 10, 3);
function pv_add_beschreibung_bearbeitung($post_id, $post, $update)
{
    $data = array();
    $import_class = new PV_Importapi();

    if (!empty($_POST['acf']['pv_jobs'])) {
        $job_id = $_POST['acf']['pv_jobs'][0];
        $job_id_before = !empty(get_field('pv_jobs', $post_id)) ? get_field('pv_jobs', $post_id)[0]->ID : '';
        $job_beschreibung = get_field('pv_description', $job_id);
        $job_internenummer = get_field('pv_id', $job_id);
        $bearbeitung_notiz = $_POST['acf']['pv_notiz'];

        if ((!empty($job_beschreibung) && !$update) || (empty($job_id_before) && !empty($job_id))) {
            if (!empty($bearbeitung_notiz)) {
                $_POST['acf']['pv_notiz'] = strip_tags($job_beschreibung . '\r\n\r\n' . $bearbeitung_notiz);
            } else {
                $_POST['acf']['pv_notiz'] = strip_tags($job_beschreibung);
            }
        }
        if ($update) {
            $additional = '';
            $bearbeitung_updates_arr = $_POST['acf']['pv_group_additional']['pv_job_updates'];
            $bearbeitung_bestandteile_arr = $_POST['acf']['pv_group_additional']['pv_job_bestandteile'];
            $bearbeitung_abwicklungsinfos_arr = $_POST['acf']['pv_group_additional']['pv_job_abwicklungsinfos'];

            if (!empty($bearbeitung_updates_arr)) {
                $additional .= PHP_EOL . '—UPDATES—' . PHP_EOL;
                foreach ($bearbeitung_updates_arr as $bearbeitung_update) {
                    $additional .= date('d.m.Y', strtotime($bearbeitung_update['pv_job_updates_date'])) . ' ' . $bearbeitung_update['pv_job_updates_kuerzel'] . ': ' . $bearbeitung_update['pv_job_updates_text'] . PHP_EOL;
                }
            }
            if (!empty($bearbeitung_bestandteile_arr)) {
                $additional .= PHP_EOL . '—BESTANDTEILE—' . PHP_EOL;
                foreach ($bearbeitung_bestandteile_arr as $bearbeitung_bestandteil) {
                    $additional .= ($bearbeitung_bestandteil['pv_job_bestandteile_done'] == true ? '✓' : 'X') . ' ' . $bearbeitung_bestandteil['pv_job_bestandteile_kuerzel'] . ' ' . $bearbeitung_bestandteil['pv_job_bestandteile_text'] . PHP_EOL;
                }
            }
            if (!empty($bearbeitung_abwicklungsinfos_arr)) {
                $additional .= PHP_EOL . '—ABWICKLUNGSINFOS—' . PHP_EOL;
                foreach ($bearbeitung_abwicklungsinfos_arr as $bearbeitung_abwicklungsinfo) {
                    $bearbeitung_entry = '';
                    if (!empty($bearbeitung_abwicklungsinfo['pv_job_abwicklung_druck'])) {
                        $bearbeitung_entry = $bearbeitung_abwicklungsinfo['pv_job_abwicklung_druck'];
                    } else if (!empty($bearbeitung_abwicklungsinfo['pv_job_abwicklung_bilder'])) {
                        $bearbeitung_entry = $bearbeitung_abwicklungsinfo['pv_job_abwicklung_bilder'];
                    } else if (!empty($bearbeitung_abwicklungsinfo['pv_job_abwicklung_externe'])) {
                        $bearbeitung_entry = $bearbeitung_abwicklungsinfo['pv_job_abwicklung_externe'];
                    } else if (!empty($bearbeitung_abwicklungsinfo['pv_job_abwicklung_sonstiges'])) {
                        $bearbeitung_entry = $bearbeitung_abwicklungsinfo['pv_job_abwicklung_sonstiges'];
                    }
                    $additional .= $bearbeitung_entry . (!empty($bearbeitung_abwicklungsinfo['pv_job_abwicklung_kuerzel']) ? ' (' . $bearbeitung_abwicklungsinfo['pv_job_abwicklung_kuerzel'] . ')' : '') . PHP_EOL;
                }
            }
            $data['id'] = $job_internenummer;
            $data['beschreibung'] = (!empty($bearbeitung_notiz) ? strip_tags($bearbeitung_notiz) : strip_tags($job_beschreibung)) . PHP_EOL . strip_tags($additional);

            $result = $import_class->put_data('projekte', $data);
        }
    }
}

add_filter('acf/validate_value/key=pv_bearbeitung_status', 'validate_pv_bearbeitung_status', 10, 4);
function validate_pv_bearbeitung_status($valid, $value, $field, $input)
{
    $pv_bearbeiter = $_POST['acf']['pv_bearbeiter'];
    if (!empty($pv_bearbeiter) && count($pv_bearbeiter) > 1 && $value == 'Geliefert - Abgeschlossen') {
        return 'Dieser Job kann nicht abgeschlossen werden, wenn mehrere Bearbeiter verknüpft sind. Entferne dich!';
    }
    return $valid;
}

add_action('wp_ajax_pv_job_list_select', 'pv_job_list_select');
add_action('wp_ajax_nopriv_pv_job_list_select', 'pv_job_list_select');
function pv_job_list_select()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_job_list_select') {
        if (!empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['value'])) {
            $_POST['old_value'] = get_field($_POST['name'], $_POST['id']);
            update_field($_POST['name'], $_POST['value'], $_POST['id']);
            do_action('acf/save_post', $_POST['id']);
            wp_send_json_success();
        }
    }
    wp_send_json_error();
}

add_action('wp_ajax_pv_job_small_notiz', 'pv_job_small_notiz');
add_action('wp_ajax_nopriv_pv_job_small_notiz', 'pv_job_small_notiz');
function pv_job_small_notiz()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_job_small_notiz') {
        if (!empty($_POST['id']) && !empty($_POST['name'])) {
            update_post_meta($_POST['id'], $_POST['name'], $_POST['value']);
            wp_send_json_success();
        }
    }
    wp_send_json_error();
}

add_action('wp_ajax_pv_group_small_notiz', 'pv_group_small_notiz');
add_action('wp_ajax_nopriv_pv_group_small_notiz', 'pv_group_small_notiz');
function pv_group_small_notiz()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_group_small_notiz') {
        if (!empty($_POST['id']) && !empty($_POST['name'])) {
            update_post_meta($_POST['id'], $_POST['name'], $_POST['value']);
            wp_send_json_success();
        }
    }
    wp_send_json_error();
}

add_action('wp_ajax_pv_job_color_select', 'pv_job_color_select');
add_action('wp_ajax_nopriv_pv_job_color_select', 'pv_job_color_select');
function pv_job_color_select()
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_job_color_select') {
        if (!empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['value'])) {
            update_field($_POST['name'], $_POST['value'], $_POST['id']);
            do_action('acf/save_post', $_POST['id']);
            wp_send_json_success();
        }
    }
    wp_send_json_error();
}

add_filter('acf/load_value/name=pv_tracking_person', 'pv_tracking_person_load_value', 10, 3);
function pv_tracking_person_load_value($value, $post_id, $field)
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_add_tracking') {
        $user_id = get_current_user_id();
        $value = $user_id;
    }
    return $value;
}

add_filter('acf/load_value/name=pv_tracking_job', 'pv_tracking_job_load_value', 10, 3);
function pv_tracking_job_load_value($value, $post_id, $field)
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_add_tracking') {
        $value = $_POST['job_id'];
    }
    return $value;
}

add_filter('acf/fields/user/query/name=pv_tracking_person', 'pv_tracking_person_load_query', 10, 3);
function pv_tracking_person_load_query($args, $field, $post_id)
{
    $script_filename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
    $ref = wp_unslash($_SERVER['HTTP_REFERER']);
    if (((strpos($ref, admin_url()) === false) && (basename($script_filename) === 'admin-ajax.php'))) {
        $args['include'] = array(get_current_user_id());
    }
    return $args;
}

add_action('acf/save_post', 'generate_tracking_title', 20);
function generate_tracking_title($post_id)
{
    if (get_post_type($post_id) != 'trackings') {
        return;
    }

    $post = get_post($post_id);

    $user = get_field('pv_tracking_person', $post_id)['display_name'];
    $job = get_field('pv_tracking_job', $post_id)[0]->post_title;
    $title = $job . ' ' . $user . ' ' . date('d.m.Y');
    $post->post_title = $title;
    $post->post_name = sanitize_title($title);

    remove_filter('acf/update_post', 20);
    wp_update_post($post);
    add_action('acf/update_post', 20);
}

add_action('acf/save_post', 'pv_tranfer_status_to_proad', 5, 1);
function pv_tranfer_status_to_proad($post_id)
{
    $import_class = new PV_Importapi();
    $post_type = get_post_type($post_id);
    if ($post_type === 'bearbeitungen') {
        if (!empty($_POST['acf'])) {

            $old_val = get_field('pv_bearbeitung_status', $post_id);
            $new_val = $_POST['acf']['pv_bearbeitung_status'];
            if (!empty($_POST['action'])) {
                if ($_POST['action'] == 'pv_job_list_select') {
                    $old_val = $_POST['old_value'];
                    $new_val = $_POST['value'];
                }
            }

            if ($old_val != $new_val && ($new_val == 'Geliefert - Abgeschlossen' || $new_val == 'Geliefert - Fertiggestellt')) {

                $pv_jobs = $_POST['acf']['pv_jobs'];
                if (!empty($_POST['action'])) {
                    if ($_POST['action'] == 'pv_job_list_select') {
                        $pv_jobs = get_field('pv_jobs', $post_id);
                    }
                }

                if (!empty($pv_jobs) && count($pv_jobs) == 1) {
                    $project_wp_id = $pv_jobs[0];
                    $urno = get_field('pv_id', $project_wp_id);

                    $data['id'] = $urno;
                    $data['status'] = 400;

                    $result = $import_class->put_data('projekte', $data);
                }
            }
        }
    }
}

add_action('save_post_bearbeitungen', 'pv_store_notification', 10, 3);
function pv_store_notification($post_id, $post, $update)
{
    $users_class = new PV_Users();

    if (!empty($_POST['acf']['pv_bearbeiter'])) {
        foreach ($_POST['acf']['pv_bearbeiter'] as $bearbeiter) {

            if ($bearbeiter != get_current_user_id()) {
                $users_class->insert_notification_to_users($post_id, 'u', array('edited_by' => get_current_user_id()), $bearbeiter);
            }
        }
    }
}

add_filter('acf/load_value/name=pv_jobs', 'pv_jobs_preload_job', 10, 3);
function pv_jobs_preload_job($value, $post_id, $field)
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_add_job') {
        if (!empty($_POST['projekt_id'])) {
            $value = $_POST['projekt_id'];
        }
    }
    return $value;
}

add_filter('acf/load_value/name=pv_requestid', 'pv_requestid_preload_requestid', 10, 3);
function pv_requestid_preload_requestid($value, $post_id, $field)
{
    if (!empty($_POST['action']) && $_POST['action'] == 'pv_jobrequest_action') {
        if (!empty($_POST['requestid'])) {
            $value = $_POST['requestid'];
        }
    } else {
        if (!empty($_GET['post'])) {
            $value = $_GET['post'];
        }
    }
    return $value;
}

add_filter('acf/validate_value/name=pv_anfrage_projektname', 'pv_validate_projektname_value', 10, 4);
function pv_validate_projektname_value($valid, $value, $field, $input_name)
{
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $value)) {
        return 'Nur Buchstaben (a–Z), Zahlen (0–9) und Unterstriche (_) erlaubt.';
    }
    return $valid;
}
