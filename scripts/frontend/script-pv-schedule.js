jQuery(document).ready(function () {
    var schedule = jQuery("#pv-time-schedule");
    var userid = schedule.attr('data-userid');

    var isDraggable = true;
    var isResizable = true;
    schedule.timeSchedule({
        startTime: "07:00",     // schedule start time(HH:ii)
        endTime: "20:00",       // schedule end time(HH:ii)
        widthTime: 60 * 10,     // cell timestamp example 10 minutes
        timeLineY: 60,          // height(px)
        verticalScrollbar: 0,   // scrollbar (px)
        timeLineBorder: 2,      // border(top and bottom)
        bundleMoveWidth: 6,     // width to move all schedules to the right of the clicked time line cell
        draggable: isDraggable,
        resizable: isResizable,
        resizableLeft: true,
        rows: {
            0: {
                title: 'Montag'
            },
            1: {
                title: 'Dienstag'
            },
            2: {
                title: 'Mittwoch'
            },
            3: {
                title: 'Donnerstag'
            },
            4: {
                title: 'Freitag'
            },
            5: {
                title: 'Samstag'
            },
            6: {
                title: 'Sonntag'
            },
            7: {
                title: 'Zugeteilte Jobs'
            }
        },
        onChange: function (node, data) {
            // SET SCHEDULES
            jQuery.ajax({
                type: 'POST',
                url: pv_js_variables.ajax_url,
                data: {
                    action: 'pv_set_user_schedules',
                    userid: userid,
                    value: schedule.timeSchedule('timelineData')
                },
                success: function (data, textStatus, XMLHttpRequest) {
                    console.log('Gespeichert');
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        }
    });

    // GET SCHEDULES
    jQuery.ajax({
        type: 'POST',
        url: pv_js_variables.ajax_url,
        data: {
            action: 'pv_get_user_schedules',
            userid: userid
        },
        success: function (data, textStatus, XMLHttpRequest) {
            if (data && data.data) {
                schedule.timeSchedule('setRows', data.data);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });

    jQuery('.pv_reset_timeline').on('click', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery('#pv_modal .modal-body').load(pv_js_variables.ajax_url, {
            action: 'pv_reset_timeline',
            user_id: jQuery(this).attr('data-userid'),
            url: window.location.href
        }, function () {
            jQuery('#pv_modal .pv_modal_title').text('Wirklich zurücksetzen?');
            jQuery('#pv_modal .pv_reset_timeline_confirmation').attr('data-userid', that.attr('data-userid'));
            jQuery('#pv_modal .modal-footer').show();
            jQuery('#pv_modal').modal('show');
            that.find('.pv-spinner').hide();
            that.find('i').show();
        });
    });

    jQuery('#pv_modal').on('hidden.bs.modal', function (e) {
        jQuery(this).find('.modal-footer').hide();
    });

    //RESET SCHEDULES
    jQuery('.pv_reset_timeline_confirmation').on('click', function () {
        var that = jQuery(this);
        that.find('.pv-spinner').css('display', 'inline-block');
        that.find('i').hide();
        jQuery.ajax({
            type: 'POST',
            url: pv_js_variables.ajax_url,
            data: {
                action: 'pv_reset_timeline_confirmation',
                userid: jQuery(this).attr('data-userid'),
                url: window.location.href
            },
            success: function (data, textStatus, XMLHttpRequest) {
                window.location.href = data.data.url;
                that.find('.pv-spinner').hide();
                that.find('i').show();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });
});