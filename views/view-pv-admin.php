<div class="wrap">
    <h2>Allgemeine Einstellungen</h2>
    <p>Stellen Sie Funktionen der Plan Verwaltung ein.</p>
    <form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="pv_importapi_admin_form">
        <h3>Import aus REST-API</h3>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="pv_import_type">Welche Daten?</label>
                    </th>
                    <td>
                        <select id="pv_import_type" name="pv_import_type">
                            <option value="projekte">Projekte</option>
                            <option value="benutzer">Benutzer</option>
                            <option value="status">Status</option>
                            <option value="kunden">Kunden</option>
                            <option value="trackings">Trackings</option>
                            <option value="service_codes">Leistungsarten</option>
                        </select>
                        <br>
                        <span class="description">Wählen Sie aus, welche Daten importiert werden sollen.</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
        wp_nonce_field('pv_importapi_admin_form', 'admin_form_nonce');
        submit_button('Daten importieren');
        ?>
    </form>
    <form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="pv_delete_all_records">
        <h3>Daten löschen</h3>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="pv_delete_type">Welche Daten?</label>
                    </th>
                    <td>
                        <select id="pv_delete_type" name="pv_delete_type">
                            <option value="projekte">Projekte</option>
                            <option value="kunden">Kunden</option>
                        </select>
                        <br>
                        <span class="description">Wählen Sie aus, welche Art von Daten gelöscht werden sollen.</span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="pv_delete_source">Welche Beiträge?</label>
                    </th>
                    <td>
                        <select id="pv_delete_source" name="pv_delete_source">
                            <option value="imported">Alle Importierten</option>
                            <option value="all">Alle Beiträge</option>
                        </select>
                        <br>
                        <span class="description">Wählen Sie aus, welche Beiträge gelöscht werden sollen.</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
        wp_nonce_field('pv_delete_all_records', 'admin_form_nonce');
        submit_button('Beiträge löschen');
        ?>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(document).on('click', '#pv_gallery_import', function() {
            var button = jQuery(this);
            var form_data = [];
            form_data.push({
                name: 'action',
                value: button.attr('name')
            });

            jQuery('#pv_gallery_msg').text('Prozess läuft...');

            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: form_data,
                success: function(msg) {
                    if (msg.success) {
                        jQuery('#pv_gallery_msg').text('Import erfolgreich!');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        jQuery('#pv_gallery_msg').text('Import fehlgeschlagen!');
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.error(XMLHttpRequest, textStatus, errorThrown);
                    jQuery('#pv_gallery_msg').text('Import fehlgeschlagen!');
                }
            });
        });
    });
</script>