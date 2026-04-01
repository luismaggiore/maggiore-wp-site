/**
 * Maggiore — Admin PDF Uploader
 * Maneja la selección de PDF desde la Media Library de WordPress
 * para el campo mg_portafolio_pdf del metabox de Portafolio.
 */
(function ($) {
    'use strict';

    $(document).ready(function () {

        var mediaFrame;

        // ── Abrir la Media Library filtrada a PDFs ──────────────────────────
        $('#mg_select_pdf').on('click', function (e) {
            e.preventDefault();

            // Reutilizar el frame si ya existe
            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            mediaFrame = wp.media({
                title: 'Seleccionar PDF',
                button: { text: 'Usar este PDF' },
                library: { type: 'application/pdf' },
                multiple: false,
            });

            mediaFrame.on('select', function () {
                var attachment = mediaFrame.state().get('selection').first().toJSON();

                // Guardar ID en el input hidden
                $('#mg_portafolio_pdf_input').val(attachment.id);

                // Mostrar preview
                var filename = attachment.filename || attachment.url.split('/').pop();
                var filesize = attachment.filesizeHumanReadable || '';

                $('#mg_pdf_preview').html(
                    '<div class="mg-pdf-preview-item" style="' +
                        'display:flex;align-items:center;gap:12px;' +
                        'padding:10px 14px;background:#f9f9f9;' +
                        'border:1px solid #ddd;border-radius:4px;margin-top:10px;">' +
                    '<span class="dashicons dashicons-pdf" style="font-size:32px;width:32px;height:32px;color:#c00;flex-shrink:0;"></span>' +
                    '<div style="flex:1;min-width:0;">' +
                        '<div style="font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' + attachment.url + '">' + filename + '</div>' +
                        (filesize ? '<div style="font-size:11px;color:#666;margin-top:2px;">' + filesize + '</div>' : '') +
                    '</div>' +
                    '<button type="button" id="mg_remove_pdf" class="button" style="flex-shrink:0;">' +
                        '<span class="dashicons dashicons-trash" style="margin-top:3px;"></span> Quitar' +
                    '</button>' +
                    '</div>'
                );

                bindRemovePdf();
            });

            mediaFrame.open();
        });

        // ── Quitar PDF seleccionado ─────────────────────────────────────────
        function bindRemovePdf() {
            $('#mg_remove_pdf').on('click', function () {
                $('#mg_portafolio_pdf_input').val('');
                $('#mg_pdf_preview').html(
                    '<p style="color:#999;font-style:italic;margin:8px 0 0;">No hay PDF seleccionado.</p>'
                );
            });
        }

        // Bind si ya hay un PDF al cargar la página (para el botón "Quitar" existente)
        bindRemovePdf();
    });

})(jQuery);
