jQuery(document).ready(function () {
    jQuery('#pv_menu_open_button').on('click', function () {
        jQuery('#pv_user_menu').show();
    });
    jQuery('#pv_menu_close_button').on('click', function () {
        jQuery('#pv_user_menu').hide();
    });
});

jQuery(document).ready(function () {
    jQuery(document).on('change', '#pv_choose_filter_users', function () {
        var that = jQuery(this);

        jQuery.ajax({
            type: 'POST',
            url: pv_js_variables.ajax_url,
            data: {
                action: 'pv_choose_filter_users',
                value: jQuery(this).val()
            },
            success: function (data, textStatus, XMLHttpRequest) {
                location.reload();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });

    jQuery(document).on('click', '.pv_filter_user', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();

        jQuery.ajax({
            type: 'POST',
            url: pv_js_variables.ajax_url,
            data: {
                action: 'pv_choose_filter_users',
                value: jQuery(this).val()
            },
            success: function (data, textStatus, XMLHttpRequest) {
                location.reload();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });

    jQuery(document).on('click', '.pv-remove-not-button', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();

        var action_key = jQuery(this).attr('data-action_key');
        var action_data = jQuery(this).attr('data-action_data');

        jQuery.ajax({
            type: 'POST',
            url: pv_js_variables.ajax_url,
            data: {
                action: 'pv_remove_notification',
                action_key: action_key,
                action_data: action_data,
                user_id: jQuery(this).attr('data-userid')
            },
            success: function (data, textStatus, XMLHttpRequest) {
                if (action_key == 'remove') {
                    that.parents('.pv-card-wrapper').find('.pv-not-list-item').remove();
                    that.find('.pv-spinner').hide();
                    that.find('i').css('display', 'inline-block');
                } else {
                    that.parents('.pv-not-list-item').remove();
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });
});

jQuery(document).ready(function () {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            sanitize: false
        }
        )
    });
});

function scrollElements() {
    var scrolledTop = localStorage.getItem('scrolledTop');
    var scrolledLeft = localStorage.getItem('scrolledLeft');
    jQuery('.pv-keep-scroll-position').animate({ scrollTop: scrolledTop }, 5);
    jQuery('.pv-keep-scroll-position').animate({ scrollLeft: scrolledLeft }, 5);
}

jQuery(document).ready(function () {
    scrollElements();

    jQuery('.pv-keep-scroll-position').scroll(function (event) {
        var scrolledTop = jQuery('.pv-keep-scroll-position').scrollTop();
        var scrolledLeft = jQuery('.pv-keep-scroll-position').scrollLeft();
        localStorage.setItem('scrolledTop', scrolledTop);
        localStorage.setItem('scrolledLeft', scrolledLeft);
    });
});

jQuery(document).ready(function () {
    jQuery('.pv_add_group').on('click', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: 'pv_add_group',
            user_id: jQuery(this).attr('data-userid'),
            status: jQuery(this).attr('data-status'),
            url: window.location.href
        }, function () {
            jQuery('#pv_modal .pv_modal_title').text('Gruppe erstellen');
            jQuery('#pv_modal').modal('show');
            acf.do_action('append', jQuery('#pv_modal'));
            that.find('.pv-spinner').hide();
            that.find('i').show();
        });
    });

    jQuery('.pv_edit_group').on('click', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: 'pv_edit_group',
            user_id: jQuery(this).attr('data-userid'),
            group_id: jQuery(this).attr('data-groupid'),
            url: window.location.href
        }, function () {
            jQuery('#pv_modal .pv_modal_title').text('Gruppe aktualisieren');
            jQuery('#pv_modal').modal('show');
            acf.do_action('append', jQuery('#pv_modal'));
            that.find('.pv-spinner').hide();
            that.find('i').show();
        });
    });
});

jQuery(document).ready(function () {
    jQuery('.pv_filter_button').on('click', function () {
        let searchParams = new URLSearchParams(window.location.search)
        var that = jQuery(this);
        var action = that.attr('data-action');

        var value = '';
        if (action == 'pv_search_filter') {
            value = searchParams.get('suche');
        } else if (action == 'pv_older_filter') {
            value = searchParams.get('older');
        }

        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: action,
            user_id: jQuery(this).attr('data-userid'),
            value: value,
            url: window.location.href
        }, function () {
            var text = '';
            if (action == 'pv_search_filter') {
                text = 'Jobs durchsuchen';
            } else if (action == 'pv_older_filter') {
                text = 'Liegende Jobs filtern';
            }
            jQuery('#pv_modal .pv_modal_title').text(text);
            jQuery('#pv_modal').modal('show');
            that.find('.pv-spinner').hide();
            that.find('i').show();
        });
    });
});

jQuery(document).ready(function () {
    jQuery('.pv_jobrequest_action').on('click', function () {
        var that = jQuery(this);
        var action = jQuery(this).attr('data-action');
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: 'pv_jobrequest_action',
            aktion: action,
            requestid: jQuery(this).attr('data-requestid'),
            url: window.location.href
        }, function () {
            if (action == 'new') {
                jQuery('#pv_modal .pv_modal_title').text('Neue Job-Anfrage');
            } else if (action == 'accept') {
                jQuery('#pv_modal .pv_modal_title').text('Job-Anfrage bearbeiten');
            } else if (action == 'refuse') {
                jQuery('#pv_modal .pv_modal_title').text('Job-Anfrage ablehnen');
            }
            jQuery('#pv_modal').modal('show');
            acf.do_action('append', jQuery('#pv_modal'));
            that.find('.pv-spinner').hide();
            that.find('i').show();
        });
    });
});

jQuery(document).ready(function () {
    jQuery('.pv_add_job').on('click', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: 'pv_add_job',
            user_id: jQuery(this).attr('data-userid'),
            projekt_id: jQuery(this).attr('data-projektid'),
            status: jQuery(this).attr('data-status'),
            url: window.location.href
        }, function () {
            jQuery('#pv_modal .pv_modal_title').text('Job hinzufügen');
            jQuery('#pv_modal').modal('show');
            acf.do_action('append', jQuery('#pv_modal'));
            that.find('.pv-spinner').hide();
            that.find('i').show();
        });
    });

    jQuery('.pv_edit_job').on('click', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: 'pv_edit_job',
            user_id: jQuery(this).attr('data-userid'),
            job_id: jQuery(this).attr('data-jobid'),
            status: jQuery(this).attr('data-status'),
            url: window.location.href
        }, function () {
            jQuery('#pv_modal .pv_modal_title').text('Job aktualisieren');
            jQuery('#pv_modal').modal('show');
            acf.do_action('append', jQuery('#pv_modal'));
            that.find('.pv-spinner').hide();
            that.find('i').show();
        });
    });

    jQuery(document).on('click', '#acf-pv_jobs .pv_already_selected', function () {
        if (confirm('Möchtest du zu diesem Job wechseln? Achtung die Eingaben in dieser Maske gehen verlohren!') == true) {
            jQuery('#pv_modal .modal-body').html('<div class="pv_inside_modal_spinner"><span class="spinner-border"></span></div>');

            var innerId = jQuery(this).attr('id') || jQuery(this).closest('.pv_already_selected').attr('id');
            var match = innerId.match(/^jobbearbeitung-(\d+)$/);
            var jobId = match ? match[1] : null;

            jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
                action: 'pv_edit_job',
                job_id: jobId,
                url: window.location.href
            }, function () {
                jQuery('#pv_modal .pv_modal_title').text('Job aktualisieren');
                acf.do_action('append', jQuery('#pv_modal'));
            });
        }
    });

    jQuery(document).on('click', '.pv_add_tracking_button', function (event) {
        event.preventDefault();
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        var pv_job = acf.getField('pv_jobs').val()[0];

        jQuery('#pv_tracking_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: 'pv_add_tracking',
            job_id: pv_job,
            url: window.location.href
        }, function () {
            that.find('.pv-spinner').hide();
            jQuery('#pv_modal').modal('hide');
            jQuery('#pv_tracking_modal').modal('show');
            acf.do_action('append', jQuery('#pv_tracking_modal'));
        });
    });

    jQuery(document).ajaxComplete(function (event, xhr, settings) {
        if (settings?.context?.data?.key) {
            if (settings.context.data.key == 'pv_jobs' || settings.context.data.key == 'pv_group_jobs') {
                jQuery('#acf-pv_jobs .pv_already_selected, #acf-pv_group_jobs .pv_already_selected').parent().addClass('disabled');
            }
        }
    });

    jQuery('.pv_job_col_sort .pv_active_job').sortable({
        items: '.pv_job_card, .pv_group_card',
        scroll: true,
        stop: function (event, ui) {
            var ids = [];
            jQuery(this).find('.pv_job_card, .pv_group_card').each(function () {
                if (jQuery(this).attr('data-id')) {
                    ids.push(jQuery(this).attr('data-id'));
                }
            });

            var form_data = [];
            form_data.push({
                name: 'action',
                value: 'pv_sort_user_jobs'
            });
            form_data.push({
                name: 'ids',
                value: ids
            });
            form_data.push({
                name: 'user_id',
                value: jQuery(this).parent('.pv_job_col').attr('data-user_id')
            });

            jQuery.ajax({
                url: pv_js_variables.ajax_url,
                type: 'POST',
                data: form_data,
                success: function (msg) {
                    if (msg.success) {

                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.error(XMLHttpRequest, textStatus, errorThrown);
                }
            });
        }
    });

    jQuery('.pv-job-list-of-user .pv_active_job').sortable({
        items: '.pv-job-list-item',
        scroll: true,
        start: function (event, ui) {
            jQuery(this).find('.pv_popover_link').popover('hide');
        },
        stop: function (event, ui) {
            var ids = [];
            jQuery(this).find('.pv-job-list-item').each(function () {
                if (jQuery(this).attr('data-postid')) {
                    ids.push(jQuery(this).attr('data-postid'));
                }
            });

            var form_data = [];
            form_data.push({
                name: 'action',
                value: 'pv_sort_user_jobs'
            });
            form_data.push({
                name: 'ids',
                value: ids
            });
            form_data.push({
                name: 'user_id',
                value: jQuery(this).parents('.pv-listview-user').attr('data-user_id')
            });

            jQuery.ajax({
                url: pv_js_variables.ajax_url,
                type: 'POST',
                data: form_data,
                success: function (msg) {
                    if (msg.success) {

                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.error(XMLHttpRequest, textStatus, errorThrown);
                }
            });
        }
    });

    // PASSWÖRTER
    jQuery('#pv-passwords-table').DataTable({
        'layout': {
            'topStart': {
                'pageLength': {
                    'menu': [25, 50, 100, 500]
                },
                // 'buttons': [
                //     {
                //         'extend': 'excelHtml5',
                //         'title': 'Passwörter - Sislak Design',
                //         'exportOptions': {
                //             'columns': [0, 1, 2, 3]
                //         }
                //     },
                //     {
                //         'extend': 'pdfHtml5',
                //         'title': 'Passwörter - Sislak Design',
                //         'orientation': 'landscape',
                //         'exportOptions': {
                //             'columns': [0, 1, 2, 3]
                //         }
                //     }
                // ]
            }
        },
        'ajax': {
            'url': pv_js_variables.ajax_url,
            'type': 'POST',
            'data': { 'action': 'pv_load_passwords_table' }
        },
        'processing': true,
        'language': {
            'url': '//cdn.datatables.net/plug-ins/1.11.4/i18n/de_de.json',
            'processing': 'Daten werden geladen...'
        },
        'paging': true,
        'pageLength': 50,
        'autoWidth': false,
        'columnDefs': [
            {
                'targets': 4,
                'data': 'action',
                'searchable': false,
                'sortable': false,
                'render': function (data, type, row, meta) {
                    return '<a href="javascript:void(0);" class="pv_edit_password" data-id="' + row[4] + '"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>';
                }
            }
        ],
        'initComplete': function () {
            jQuery('#pv-passwords-table').show();
        }
    });

    jQuery(document).on('click', '.pv_add_password', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: 'pv_add_password',
            id: jQuery(this).attr('data-id'),
            url: window.location.href
        }, function () {
            jQuery('#pv_modal .modal-title').text('Eintrag hinzufügen');
            jQuery('#pv_modal').modal('show');
            acf.do_action('append', jQuery('#pv_modal'));
            that.find('.pv-spinner').hide();
            that.find('i').show();
        });
    });

    jQuery(document).on('click', '.pv_edit_password', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: 'pv_edit_password',
            id: jQuery(this).attr('data-id'),
            url: window.location.href
        }, function () {
            jQuery('#pv_modal .modal-title').text('Eintrag aktualisieren');
            jQuery('#pv_modal').modal('show');
            acf.do_action('append', jQuery('#pv_modal'));
            that.find('.pv-spinner').hide();
            that.find('i').show();
        });
    });

    //JOB-LISTE
    jQuery(document).on('change', '.pv_job_list_select', function () {
        var that = jQuery(this);
        var postid = jQuery(this).attr('data-postid');
        var name = jQuery(this).attr('data-name');

        jQuery.ajax({
            type: 'POST',
            url: pv_js_variables.ajax_url,
            data: {
                action: 'pv_job_list_select',
                id: postid,
                name: name,
                value: jQuery(this).val()
            },
            success: function (data, textStatus, XMLHttpRequest) {
                jQuery(that).css('background-color', '#c0e6c0');
                setTimeout(function () {
                    jQuery(that).css('background-color', '');
                }, 2000);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                jQuery(that).css('background-color', 'red');
                console.log(errorThrown);
            }
        });
    });

    jQuery(document).on('change', '.pv_job_small_notiz_inline, .pv_group_small_notiz_inline', function () {
        var that = jQuery(this);
        var postid = jQuery(this).attr('data-postid');
        var name = jQuery(this).attr('data-name');

        jQuery.ajax({
            type: 'POST',
            url: pv_js_variables.ajax_url,
            data: {
                action: name,
                id: postid,
                name: name,
                value: jQuery(this).val()
            },
            success: function (data, textStatus, XMLHttpRequest) {
                jQuery(that).css('background-color', '#c0e6c063');
                setTimeout(function () {
                    jQuery(that).css('background-color', '');
                }, 2000);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                jQuery(that).css('background-color', '#ff000042');
                console.log(errorThrown);
            }
        });
    });

    jQuery(document).on('click', '.pv_job_color_select', function () {
        jQuery(this).data('old_color_val', jQuery(this).val());
    });

    jQuery(document).on('change', '.pv_job_color_select', function () {
        var that = jQuery(this);
        var postid = jQuery(this).attr('data-postid');
        var name = jQuery(this).attr('data-name');
        var prev_val = jQuery(this).data('old_color_val');
        var value = jQuery(this).val();

        jQuery.ajax({
            type: 'POST',
            url: pv_js_variables.ajax_url,
            data: {
                action: 'pv_job_color_select',
                id: postid,
                name: name,
                value: value
            },
            success: function (data, textStatus, XMLHttpRequest) {
                jQuery(that).css('background-color', '#c0e6c063');
                jQuery('.list-group-item[data-postid="' + postid + '"]').addClass('pv_job_' + value).removeClass('pv_job_' + prev_val);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                jQuery(that).css('background-color', '#ff000042');
                console.log(errorThrown);
            }
        });
    });

    // KUNDEN
    jQuery('#pv-customers-table').DataTable({
        'ajax': {
            'url': pv_js_variables.ajax_url,
            'type': 'POST',
            'data': { 'action': 'pv_load_customers_table' }
        },
        'processing': true,
        'language': {
            'url': '//cdn.datatables.net/plug-ins/1.11.4/i18n/de_de.json',
            'processing': 'Daten werden geladen...'
        },
        'paging': true,
        'pageLength': 50,
        'autoWidth': false,
        'columnDefs': [
            {
                'targets': 3,
                'data': 'action',
                'searchable': false,
                'sortable': false,
                'render': function (data, type, row, meta) {
                    return '<a href="javascript:void(0);" class="pv_edit_customer" data-id="' + row[3] + '"><i class="bi bi-pen"></i><span class="pv-spinner spinner-border"></span></a>';
                }
            }
        ],
        'initComplete': function () {
            jQuery('#pv-customers-table').show();
        }
    });

    jQuery(document).on('click', '.pv_edit_customer', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: 'pv_edit_customer',
            id: jQuery(this).attr('data-id'),
            url: window.location.href
        }, function () {
            jQuery('#pv_modal .modal-title').text('Eintrag aktualisieren');
            jQuery('#pv_modal').modal('show');
            acf.do_action('append', jQuery('#pv_modal'));
            that.find('.pv-spinner').hide();
            that.find('i').show();
        });
    });
});