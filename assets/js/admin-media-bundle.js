/**
 * ============================================================================
 * ADMIN MEDIA BUNDLE - MAGGIORE THEME
 * ============================================================================
 * 
 * Bundle optimizado que fusiona 3 scripts de administración:
 * 1. admin-gallery.js (Galería de imágenes)
 * 2. admin-gallery-videos.js (Galería de videos)
 * 3. admin-videos-externos-simple.js (Videos externos YouTube/Vimeo)
 * 
 * @package Maggiore
 * @version 1.0.0
 * @optimized true
 */

jQuery(document).ready(function ($) {

  // ==========================================================================
  // FUNCIONES HELPER COMPARTIDAS
  // ==========================================================================
  
  /**
   * Escapar HTML para prevenir XSS
   */
  function escapeHtml(text) {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
  }

  /**
   * Extraer ID de video de YouTube o Vimeo
   */
  function extractVideoId(url) {
    // YouTube
    const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
    const youtubeMatch = url.match(youtubeRegex);
    if (youtubeMatch && youtubeMatch[1]) {
      return youtubeMatch[1];
    }
    
    // Vimeo
    const vimeoRegex = /vimeo\.com\/(\d+)/;
    const vimeoMatch = url.match(vimeoRegex);
    if (vimeoMatch && vimeoMatch[1]) {
      return vimeoMatch[1];
    }
    
    return null;
  }

  /**
   * Mostrar feedback visual en botón
   */
  function showButtonFeedback($button, message, duration = 2000) {
    const originalText = $button.html();
    const originalBg = $button.css('background-color');
    
    $button.html(message);
    $button.css('background-color', '#46b450');
    
    setTimeout(function() {
      $button.html(originalText);
      $button.css('background-color', originalBg);
    }, duration);
  }

  // ==========================================================================
  // PARTE 1: GALERÍA DE IMÁGENES
  // ==========================================================================
  
  $("#mg_select_galeria").on("click", function (e) {
    e.preventDefault();

    console.log("Admin-bundle: Abriendo selector de imágenes...");

    const frame = wp.media({
      title: "Agregar imágenes a la galería",
      multiple: true,
      library: { type: "image" },
      button: { text: "Agregar selección" },
    });

    frame.on("select", function () {
      console.log("Admin-bundle: Imágenes seleccionadas");

      const selection = frame.state().get("selection");

      // Obtener IDs actuales
      let currentIds = $("#mg_portafolio_galeria_input").val();
      currentIds = currentIds
        ? currentIds
            .split(",")
            .map((id) => parseInt(id))
            .filter(Boolean)
        : [];

      console.log("IDs actuales de imágenes:", currentIds);

      let addedCount = 0;

      selection.each(function (attachment) {
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

      // Feedback visual
      if (addedCount > 0) {
        showButtonFeedback(
          $("#mg_select_galeria"),
          '<span class="dashicons dashicons-yes" style="margin-top: 3px;"></span> ¡' +
            addedCount +
            " agregada(s)!"
        );
      }
    });

    frame.open();
  });

  // Limpiar galería de imágenes
  $("#mg_clear_galeria").on("click", function (e) {
    e.preventDefault();
    if (
      confirm(
        "¿Estás seguro de que quieres eliminar todas las imágenes de la galería?"
      )
    ) {
      $("#mg_galeria_preview").html("");
      $("#mg_portafolio_galeria_input").val("");
      console.log("Galería de imágenes limpiada");
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
  });

  // ==========================================================================
  // PARTE 2: GALERÍA DE VIDEOS
  // ==========================================================================
  
  $("#mg_select_videos").on("click", function (e) {
    e.preventDefault();

    console.log('Admin-bundle: Abriendo selector de videos...');

    const MAX_VIDEOS = 10; // Límite recomendado
    const MAX_SIZE_MB = 100; // Límite de tamaño por video en MB
    const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;

    const frame = wp.media({
      title: "Agregar videos (máx. " + MAX_SIZE_MB + "MB cada uno)",
      multiple: true,
      library: { 
        type: 'video'
      },
      button: { text: "Agregar videos seleccionados" },
    });

    frame.on("select", function () {
      console.log('Admin-bundle: Videos seleccionados');
      
      const selection = frame.state().get("selection");
      
      // Obtener IDs actuales
      let currentIds = $("#mg_portafolio_videos_input").val();
      currentIds = currentIds ? currentIds.split(',').map(id => parseInt(id)).filter(Boolean) : [];
      
      console.log('IDs de videos actuales:', currentIds);
      
      let addedCount = 0;
      let skippedCount = 0;

      selection.each(function (attachment) {
        // Verificar límite de videos
        if (currentIds.length >= MAX_VIDEOS) {
          console.warn('⚠️ Límite de ' + MAX_VIDEOS + ' videos alcanzado');
          alert('Límite de ' + MAX_VIDEOS + ' videos alcanzado. No se agregarán más videos.');
          return false;
        }

        const attachmentId = attachment.get('id');
        const filesize = attachment.get('filesizeInBytes') || 0;
        const filesizeMB = filesize ? (filesize / (1024 * 1024)).toFixed(2) : 0;
        
        // Validar tamaño
        if (filesizeMB > MAX_SIZE_MB) {
          console.warn('⚠️ Video muy grande:', filesizeMB + 'MB (máx: ' + MAX_SIZE_MB + 'MB)');
          skippedCount++;
          return;
        }
        
        // Solo agregar si no existe
        if (!currentIds.includes(attachmentId)) {
          currentIds.push(attachmentId);
          
          // Obtener datos del video
          const filename = attachment.get('filename') || 'Video';
          
          // Obtener thumbnail (poster) del video si existe
          let posterUrl = '';
          const image = attachment.get('image');
          if (image && image.src) {
            posterUrl = image.src;
          } else {
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
      
      // Feedback visual
      if (addedCount > 0) {
        showButtonFeedback(
          $('#mg_select_videos'),
          '<span class="dashicons dashicons-yes" style="margin-top: 3px;"></span> ¡' + addedCount + ' video(s) agregado(s)!'
        );
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
    $(this).closest('.mg-video-wrapper').remove();
    
    // Actualizar el input hidden
    let currentIds = $("#mg_portafolio_videos_input").val().split(',').filter(Boolean);
    currentIds = currentIds.filter(id => id != idToRemove);
    $("#mg_portafolio_videos_input").val(currentIds.join(','));
    
    console.log('Video removido:', idToRemove);
  });

  // ==========================================================================
  // PARTE 3: VIDEOS EXTERNOS (YouTube/Vimeo)
  // ==========================================================================
  
  /**
   * Actualizar hidden input con URLs
   */
  function actualizarHiddenInput() {
    const urls = [];
    
    $('.mg-video-externo-item').each(function() {
      urls.push($(this).data('url'));
    });
    
    // Guardar como string separado por \n
    $('#mg_portafolio_videos_externos_hidden').val(urls.join('\n'));
    
    console.log('Hidden input actualizado:', urls.length + ' videos externos');
  }

  /**
   * Agregar video externo a la lista
   */
  $('#mg_agregar_video_externo').on('click', function() {
    const url = $('#mg_video_externo_input').val().trim();
    
    // Validar que no esté vacío
    if (url === '') {
      alert('Por favor ingresa una URL');
      return;
    }
    
    // Validar que sea YouTube o Vimeo
    const isYoutube = url.includes('youtube.com') || url.includes('youtu.be');
    const isVimeo = url.includes('vimeo.com');
    
    if (!isYoutube && !isVimeo) {
      alert('⚠️ Por favor ingresa una URL válida de YouTube o Vimeo');
      return;
    }
    
    // Verificar que no esté duplicado
    let existente = false;
    $('.mg-video-externo-item').each(function() {
      if ($(this).data('url') === url) {
        existente = true;
        return false;
      }
    });
    
    if (existente) {
      alert('⚠️ Este video ya está en la lista');
      return;
    }
    
    // Extraer ID del video y generar thumbnail
    const videoId = extractVideoId(url);
    let thumbnail = '';
    let platform = '';
    let color = '';
    
    if (isYoutube) {
      platform = 'YouTube';
      color = '#FF0000';
      if (videoId) {
        thumbnail = 'https://img.youtube.com/vi/' + videoId + '/mqdefault.jpg';
      } else {
        thumbnail = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="320" height="180" viewBox="0 0 320 180"%3E%3Crect fill="%23FF0000" width="320" height="180"/%3E%3Cpath fill="%23fff" d="M140 90 L180 70 L180 110 Z"/%3E%3C/svg%3E';
      }
    } else if (isVimeo) {
      platform = 'Vimeo';
      color = '#1AB7EA';
      thumbnail = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="320" height="180" viewBox="0 0 320 180"%3E%3Crect fill="%231ab7ea" width="320" height="180"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="white" font-size="30" font-family="Arial"%3EVimeo%3C/text%3E%3C/svg%3E';
    }
    
    // Crear HTML del item
    const itemHtml = '<div class="mg-video-externo-item" data-url="' + escapeHtml(url) + '" style="display: flex; gap: 10px; align-items: center; padding: 10px; background: #f9f9f9; border-radius: 4px; margin-bottom: 10px;">' +
      '<!-- Thumbnail -->' +
      '<div style="flex-shrink: 0; width: 120px; height: 68px; border-radius: 3px; overflow: hidden; background: #000;">' +
        '<img src="' + thumbnail + '" style="width: 100%; height: 100%; object-fit: cover;" alt="' + platform + ' thumbnail">' +
      '</div>' +
      '<!-- Info -->' +
      '<div style="flex: 1; min-width: 0;">' +
        '<div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">' +
          '<span class="dashicons dashicons-video-alt3" style="color: ' + color + '; font-size: 16px;"></span>' +
          '<strong style="color: ' + color + ';">' + platform + '</strong>' +
        '</div>' +
        '<div style="font-size: 12px; color: #666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' + escapeHtml(url) + '">' +
          escapeHtml(url) +
        '</div>' +
      '</div>' +
      '<!-- Botón eliminar -->' +
      '<button type="button" class="button mg-eliminar-video-externo" data-url="' + escapeHtml(url) + '" style="flex-shrink: 0;">' +
        '<span class="dashicons dashicons-trash" style="margin-top: 3px;"></span> Eliminar' +
      '</button>' +
    '</div>';
    
    // Agregar a la lista
    const $lista = $('#mg_videos_externos_lista');
    
    // Si existe el mensaje de "no hay videos", quitarlo
    $lista.find('p').remove();
    
    // Agregar el nuevo item
    $lista.append(itemHtml);
    
    // Limpiar input
    $('#mg_video_externo_input').val('');
    
    // Actualizar hidden input
    actualizarHiddenInput();
    
    // Feedback visual
    showButtonFeedback(
      $('#mg_agregar_video_externo'),
      '<span class="dashicons dashicons-yes" style="margin-top: 3px;"></span> ¡Agregado!',
      1500
    );
    
    console.log('Video externo agregado:', url);
  });
  
  /**
   * Eliminar video externo de la lista
   */
  $(document).on('click', '.mg-eliminar-video-externo', function() {
    const url = $(this).data('url');
    
    if (confirm('¿Estás seguro de eliminar este video?')) {
      $(this).closest('.mg-video-externo-item').fadeOut(300, function() {
        $(this).remove();
        
        // Si no quedan videos, mostrar mensaje
        if ($('.mg-video-externo-item').length === 0) {
          $('#mg_videos_externos_lista').html('<p style="color: #666; font-style: italic;">No hay videos externos agregados. Usa el campo de arriba para agregar uno.</p>');
        }
        
        // Actualizar hidden input
        actualizarHiddenInput();
      });
      
      console.log('Video externo eliminado:', url);
    }
  });
  
  /**
   * Permitir agregar con Enter
   */
  $('#mg_video_externo_input').on('keypress', function(e) {
    if (e.which === 13) { // Enter
      e.preventDefault();
      $('#mg_agregar_video_externo').click();
    }
  });

  // ==========================================================================
  // LOG DE INICIALIZACIÓN
  // ==========================================================================
  
  console.log('✅ Admin Media Bundle loaded successfully');
  console.log('📦 Includes: Image Gallery + Video Gallery + External Videos');

});
