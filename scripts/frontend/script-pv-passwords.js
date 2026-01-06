jQuery(document).ready(function () {
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

// SDPV: Master-Unlock + Reveal (jQuery) – immer im vorhandenen #pv_modal arbeiten
(function ($) {

    /* ------------ AJAX helper ------------ */
    function sdpvAjax(data, done) {
        $.ajax({
            url: (window.pv_js_variables && pv_js_variables.ajax_url) || (window.ajaxurl || '/wp-admin/admin-ajax.php'),
            type: 'POST',
            dataType: 'json',
            data: data
        }).done(function (json) { done(null, json); })
            .fail(function (xhr) {
                var json = null; try { json = JSON.parse(xhr.responseText); } catch (e) { }
                done(xhr, json);
            });
    }

    /* ------------ Modal Content Swapper (immer #pv_modal) ------------ */
    var SDPVModal = {
        get: function () {
            var $m = $('#pv_modal');
            if (!$m.length) { alert('Modal #pv_modal wurde nicht gefunden.'); return null; }
            return $m;
        },
        stash: function ($modal) {
            if (!$modal || !$modal.length) return;
            if ($modal.data('sdpv_stashed')) return; // schon gespeichert
            var $title = $modal.find('.modal-title');
            var $body = $modal.find('.modal-body');
            $modal.data('sdpv_stashed', {
                title: $title.length ? $title.html() : '',
                body: $body.length ? $body.html() : ''
            });
        },
        restore: function ($modal) {
            if (!$modal || !$modal.length) return;
            var st = $modal.data('sdpv_stashed');
            if (!st) return;
            var $title = $modal.find('.modal-title');
            var $body = $modal.find('.modal-body');
            if ($title.length) $title.html(st.title);
            if ($body.length) $body.html(st.body);
            $modal.removeData('sdpv_stashed');
        },
        showUnlock: function (messageHtml) {
            var $modal = SDPVModal.get();
            if (!$modal) return null;
            var $title = $modal.find('.modal-title');
            var $body = $modal.find('.modal-body');
            if (!$title.length || !$body.length) { alert('Modal-Struktur (#pv_modal .modal-title/.modal-body) fehlt.'); return null; }

            SDPVModal.stash($modal);

            var body = '' +
                '<form id="sdpv-unlock-form">' +
                '  <div class="mb-3">' +
                '    <label class="form-label">Master-Passwort</label>' +
                '    <input type="password" class="form-control" name="master" autocomplete="current-password" required />' +
                '    <div class="form-text">' + (messageHtml || 'Bitte Master-Passwort eingeben, um die Passwörter 15 Minuten freizuschalten.') + '</div>' +
                '  </div>' +
                '  <div class="d-flex gap-2 align-items-center">' +
                '    <button type="submit" class="btn btn-primary">Entsperren</button>' +
                '    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>' +
                '    <span class="pv-spinner spinner-border" style="display:none;"></span>' +
                '  </div>' +
                '</form>';

            $title.text('Tresor entsperren');
            $body.html(body);

            if (!$modal.hasClass('show')) { $modal.modal('show'); }
            // BS5: Layout aktualisieren (falls notwendig)
            if (typeof $modal.modal === 'function' && $modal.data('bs.modal')) {
                try { $modal.data('bs.modal').handleUpdate(); } catch (e) { }
            }
            return $modal;
        }
    };

    // Beim Schließen des Modals ggf. ursprünglichen Inhalt wiederherstellen
    $(document).on('hidden.bs.modal', '#pv_modal', function () {
        var $modal = $(this);
        if ($modal.data('sdpv_stashed')) {
            SDPVModal.restore($modal);
        }
    });

    function runRetry() {
        if (typeof window._sdpv_retry === 'function') {
            var fn = window._sdpv_retry; window._sdpv_retry = null;
            setTimeout(fn, 150);
        }
    }

    /* ------------ Tabelle: Reveal (Auge⇄Spinner) ------------ */
    function tryRevealTable($btn, done) {
        var post = $btn.data('post'),
            field = $btn.data('field'),
            nonce = $btn.data('nonce'),
            $out = $btn.next('.sdpv-mini-out');

        sdpvAjax({ action: 'sdpv_reveal', post_id: post, field: field, _sdpv_nonce: nonce }, function (err, json) {
            if (json && json.success) {
                if ($out.length) $out.text(json.data.value || '—');
                if (done) done();
                return;
            }
            if (json && json.data && json.data.message === 'LOCKED') {
                window._sdpv_retry = function () { tryRevealTable($btn, done); };
                SDPVModal.showUnlock(null);
            } else {
                if ($out.length) $out.text('—');
                if (done) done();
            }
        });
    }

    $(document).on('click', '.sdpv-mini-btn', function (e) {
        e.preventDefault();
        var $btn = $(this),
            $icon = $btn.find('i'),
            $spin = $btn.find('.pv-spinner');
        $icon.hide(); $spin.show();
        tryRevealTable($btn, function () { $spin.hide(); $icon.show(); });
    });

    /* ------------ Modal: Entsperren (im selben #pv_modal) ------------ */
    $(document).on('submit', '#sdpv-unlock-form', function (e) {
        e.preventDefault();
        var $form = $(this),
            $modal = $('#pv_modal'),
            $spin = $form.find('.pv-spinner'),
            master = $.trim($form.find('input[name="master"]').val());
        if (!master) return;
        $spin.show();
        sdpvAjax({ action: 'sdpv_master_unlock', master: master }, function (err, json) {
            $spin.hide();
            if (json && json.success) {
                SDPVModal.restore($modal); // ACF-Form/Content zurück
                runRetry();                // gewünschte Aktion nochmal ausführen
            } else {
                var msg = (json && json.data && json.data.message) ? json.data.message : 'Unlock fehlgeschlagen';
                var $body = $modal.find('.modal-body');
                if ($body.length) {
                    var body = '' +
                        '<form id="sdpv-unlock-form">' +
                        '  <div class="mb-3">' +
                        '    <label class="form-label">Master-Passwort</label>' +
                        '    <input type="password" class="form-control" name="master" autocomplete="current-password" required />' +
                        '    <div class="form-text"><span class="text-danger">' + msg + '</span></div>' +
                        '  </div>' +
                        '  <div class="d-flex gap-2 align-items-center">' +
                        '    <button type="submit" class="btn btn-primary">Entsperren</button>' +
                        '    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>' +
                        '    <span class="pv-spinner spinner-border" style="display:none;"></span>' +
                        '  </div>' +
                        '</form>';
                    $body.html(body);
                } else {
                    alert(msg);
                }
            }
        });
    });

    /* ------------ ACF im Modal: Auge⇄Entschlüsseln ------------ */
    $(document).on('click', '.sdpv-acf-eye', function (e) {
        e.preventDefault();
        var $eye = $(this),
            $wrap = $eye.closest('.sdpv-acf-controls'),
            post = $wrap.data('post'),
            field = $wrap.data('field'),
            nonce = $wrap.data('nonce'),
            ajaxu = $wrap.data('ajax'),
            $hide = $wrap.find('.sdpv-acf-hide'),
            $spin = $wrap.find('.pv-spinner'),
            $input = $wrap.closest('.acf-field').find('input[type="password"], input[type="text"]').first();

        if (!$input.length) return;
        $eye.find('i').hide(); $spin.show();

        $.ajax({
            url: ajaxu, type: 'POST', dataType: 'json',
            data: { action: 'sdpv_reveal', post_id: post, field: field, _sdpv_nonce: nonce }
        }).done(function (json) {
            if (json && json.success) {
                $input.attr('type', 'text').val(json.data.value || '');
                $hide.show(); $eye.hide();
            } else if (json && json.data && json.data.message === 'LOCKED') {
                // Unlock-Flow im GLEICHEN #pv_modal
                window._sdpv_retry = function () { $eye.trigger('click'); };
                SDPVModal.showUnlock(null);
            }
        }).always(function () {
            $spin.hide(); $eye.find('i').show();
        });
    });

    $(document).on('click', '.sdpv-acf-hide', function (e) {
        e.preventDefault();
        var $wrap = $(this).closest('.sdpv-acf-controls'),
            sentinel = $wrap.data('sentinel'),
            $eye = $wrap.find('.sdpv-acf-eye'),
            $hide = $(this),
            $input = $wrap.closest('.acf-field').find('input[type="password"], input[type="text"]').first();
        if (!$input.length) return;
        $input.attr('type', 'password').val(sentinel);
        $hide.hide(); $eye.show();
    });

})(jQuery);
