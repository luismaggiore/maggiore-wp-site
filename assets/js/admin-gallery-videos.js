jQuery(document).ready(function ($) {
  // ===== GALERÍA DE VIDEOS =====
  $("#mg_select_videos").on("click", function (e) {
    e.preventDefault();

    console.log('Admin-gallery-videos.js: Abriendo selector de videos...');

    const frame = wp.media({
      title: "Agregar videos a la galería",
      multiple: true,
      library: { 
        type: 'video'  // Solo videos
      },
      button: { text: "Agregar videos seleccionados" },
    });

    frame.on("select", function () {
      console.log('Admin-gallery-videos.js: Evento select disparado');
      
      const selection = frame.state().get("selection");
      
      // Obtener IDs actuales
      let currentIds = $("#mg_portafolio_videos_input").val();
      currentIds = currentIds ? currentIds.split(',').map(id => parseInt(id)).filter(Boolean) : [];
      
      console.log('IDs de videos actuales:', currentIds);
      
      let addedCount = 0;
      let skippedCount = 0;
      const MAX_VIDEOS = 10; // Límite recomendado
      const MAX_SIZE_MB = 100; // Límite de tamaño por video en MB

      selection.each(function (attachment) {
        // Verificar límite de videos
        if (currentIds.length >= MAX_VIDEOS) {
          console.warn('⚠️ Límite de ' + MAX_VIDEOS + ' videos alcanzado');
          alert('Límite de ' + MAX_VIDEOS + ' videos alcanzado. No se agregarán más videos.');
          return false; // Detener el loop
        }

        const attachmentId = attachment.get('id');
        const filesize = attachment.get('filesizeInBytes');
        const filesizeMB = filesize ? (filesize / (1024 * 1024)).toFixed(2) : 0;
        
        // Validar tamaño
        if (filesizeMB > MAX_SIZE_MB) {
          console.warn('⚠️ Video muy grande:', filesizeMB + 'MB (máx: ' + MAX_SIZE_MB + 'MB)');
          skippedCount++;
          return; // Siguiente video
        }
        
        // Solo agregar si no existe
        if (!currentIds.includes(attachmentId)) {
          currentIds.push(attachmentId);
          
          // Obtener datos del video
          const filename = attachment.get('filename') || 'Video';
          const url = attachment.get('url');
          const mime = attachment.get('mime') || 'video/mp4';
          
          // Obtener thumbnail (poster) del video si existe
          let posterUrl = '';
          const image = attachment.get('image');
          if (image && image.src) {
            posterUrl = image.src;
          } else {
            // Icono de video por defecto
            posterUrl = attachment.get('icon') || '';
          }
          
          // Si no hay poster, crear uno con ícono
          if (!posterUrl) {
            posterUrl = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24"%3E%3Crect fill="%23333" width="24" height="24"/%3E%3Cpath fill="%23fff" d="M8 5v14l11-7z"/%3E%3C/svg%3E';
          }
          
          const preview = `<div class="mg-video-wrapper" data-id="${attachmentId}" style="position: relative; width: 120px;">
            <div style="position: relative; width: 120px; height: 120px; border: 1px solid #ccc; border-radius: 3px; overflow: hidden; background: #000;">
              <img src="${posterUrl}" style="width: 100%; height: 100%; object-fit: cover;" />
              <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <span class="dashicons dashicons-controls-play" style="color: white; font-size: 24px;"></span>
              </div>
            </div>
            <div style="font-size: 10px; margin-top: 3px; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${filename}">
              ${filename}
            </div>
            <div style="font-size: 9px; text-align: center; color: #666;">
              ${filesizeMB} MB
            </div>
            <button type="button" class="mg-remove-video" data-id="${attachmentId}" 
                    style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px; line-height: 1;">×</button>
          </div>`;
          
          $("#mg_videos_preview").append(preview);
          addedCount++;
        }
      });

      // Actualizar input
      $("#mg_portafolio_videos_input").val(currentIds.join(","));
      
      console.log('✅ Videos agregados:', addedCount);
      if (skippedCount > 0) {
        console.log('⚠️ Videos saltados (muy grandes):', skippedCount);
        alert(skippedCount + ' video(s) fueron omitidos por exceder ' + MAX_SIZE_MB + 'MB');
      }
      console.log('✅ Total de videos:', currentIds.length);
      console.log('✅ IDs finales:', currentIds);
      
      // Feedback visual
      if (addedCount > 0) {
        const $button = $('#mg_select_videos');
        const originalText = $button.html();
        const originalBg = $button.css('background-color');
        
        $button.html('<span class="dashicons dashicons-yes" style="margin-top: 3px;"></span> ¡' + addedCount + ' video(s) agregado(s)!');
        $button.css('background-color', '#46b450');
        
        setTimeout(function() {
          $button.html(originalText);
          $button.css('background-color', originalBg);
        }, 2000);
      }
    });

    frame.open();
  });
  
  // Limpiar galería de videos
  $("#mg_clear_videos").on("click", function(e) {
    e.preventDefault();
    if (confirm('¿Estás seguro de que quieres eliminar todos los videos de la galería?')) {
      $("#mg_videos_preview").html('');
      $("#mg_portafolio_videos_input").val('');
      console.log('Galería de videos limpiada');
    }
  });
  
  // Remover video individual
  $(document).on("click", ".mg-remove-video", function(e) {
    e.preventDefault();
    const idToRemove = $(this).data('id');
    $(this).parent('.mg-video-wrapper').remove();
    
    // Actualizar el input hidden
    let currentIds = $("#mg_portafolio_videos_input").val().split(',').filter(Boolean);
    currentIds = currentIds.filter(id => id != idToRemove);
    $("#mg_portafolio_videos_input").val(currentIds.join(','));
    
    console.log('Video removido:', idToRemove);
    console.log('IDs de videos restantes:', currentIds);
  });
});
