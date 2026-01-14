jQuery(document).ready(function ($) {
  $("#mg_select_galeria").on("click", function (e) {
    e.preventDefault();

    console.log("Admin-gallery.js: Abriendo selector...");

    const frame = wp.media({
      title: "Agregar imágenes a la galería",
      multiple: true,
      library: { type: "image" },
      button: { text: "Agregar selección" },
    });

    frame.on("select", function () {
      console.log("Admin-gallery.js: Evento select disparado");

      const selection = frame.state().get("selection");

      // Obtener IDs actuales
      let currentIds = $("#mg_portafolio_galeria_input").val();
      currentIds = currentIds
        ? currentIds
            .split(",")
            .map((id) => parseInt(id))
            .filter(Boolean)
        : [];

      console.log("IDs actuales:", currentIds);

      let addedCount = 0;

      selection.each(function (attachment) {
        // NO usar toJSON() inmediatamente - usar .get() es más seguro
        const attachmentId = attachment.get("id");

        // Solo agregar si no existe
        if (!currentIds.includes(attachmentId)) {
          currentIds.push(attachmentId);

          // Obtener URL de forma segura con fallbacks
          let thumbUrl = "";

          const sizes = attachment.get("sizes");
          if (sizes && sizes.thumbnail && sizes.thumbnail.url) {
            thumbUrl = sizes.thumbnail.url;
          } else if (attachment.get("url")) {
            thumbUrl = attachment.get("url");
          } else if (attachment.get("icon")) {
            thumbUrl = attachment.get("icon");
          } else {
            // Placeholder SVG si no hay nada disponible
            thumbUrl =
              'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="80" height="80"%3E%3Crect fill="%23ddd" width="80" height="80"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999"%3ELoading...%3C/text%3E%3C/svg%3E';

            // Intentar fetch async
            console.log("Haciendo fetch de attachment:", attachmentId);
            attachment.fetch().done(function () {
              const newSizes = attachment.get("sizes");
              const newUrl =
                newSizes && newSizes.thumbnail && newSizes.thumbnail.url
                  ? newSizes.thumbnail.url
                  : attachment.get("url");

              if (newUrl) {
                $('.mg-thumb-wrapper[data-id="' + attachmentId + '"] img').attr(
                  "src",
                  newUrl
                );
              }
            });
          }

          const preview = `<div class="mg-thumb-wrapper" data-id="${attachmentId}" style="position: relative;">
            <img src="${thumbUrl}" style="width: 80px; height: auto; display: block; border:1px solid #ccc; padding:3px; border-radius:3px;" />
            <button type="button" class="mg-remove-image" data-id="${attachmentId}" 
                    style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px; line-height: 1;">×</button>
          </div>`;

          $("#mg_galeria_preview").append(preview);
          addedCount++;
        }
      });

      // Actualizar input
      $("#mg_portafolio_galeria_input").val(currentIds.join(","));

      console.log("✅ Imágenes agregadas:", addedCount);
      console.log("✅ Total en galería:", currentIds.length);
      console.log("✅ IDs finales:", currentIds);

      // Feedback visual
      if (addedCount > 0) {
        const $button = $("#mg_select_galeria");
        const originalText = $button.html();
        const originalBg = $button.css("background-color");

        $button.html(
          '<span class="dashicons dashicons-yes" style="margin-top: 3px;"></span> ¡' +
            addedCount +
            " agregada(s)!"
        );
        $button.css("background-color", "#46b450");

        setTimeout(function () {
          $button.html(originalText);
          $button.css("background-color", originalBg);
        }, 2000);
      }
    });

    frame.open();
  });

  // Limpiar galería
  $("#mg_clear_galeria").on("click", function (e) {
    e.preventDefault();
    if (
      confirm(
        "¿Estás seguro de que quieres eliminar todas las imágenes de la galería?"
      )
    ) {
      $("#mg_galeria_preview").html("");
      $("#mg_portafolio_galeria_input").val("");
      console.log("Galería limpiada");
    }
  });

  // Remover imagen individual
  $(document).on("click", ".mg-remove-image", function (e) {
    e.preventDefault();
    const idToRemove = $(this).data("id");
    $(this).parent(".mg-thumb-wrapper").remove();

    // Actualizar el input hidden
    let currentIds = $("#mg_portafolio_galeria_input")
      .val()
      .split(",")
      .filter(Boolean);
    currentIds = currentIds.filter((id) => id != idToRemove);
    $("#mg_portafolio_galeria_input").val(currentIds.join(","));

    console.log("Imagen removida:", idToRemove);
    console.log("IDs restantes:", currentIds);
  });
});
