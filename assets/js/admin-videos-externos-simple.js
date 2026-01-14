jQuery(document).ready(function($) {
  
  // ===== AGREGAR VIDEO EXTERNO A LA LISTA =====
  $('#mg_agregar_video_externo').on('click', function() {
    var url = $('#mg_video_externo_input').val().trim();
    
    // Validar que no esté vacío
    if (url === '') {
      alert('Por favor ingresa una URL');
      return;
    }
    
    // Validar que sea YouTube o Vimeo
    var isYoutube = url.includes('youtube.com') || url.includes('youtu.be');
    var isVimeo = url.includes('vimeo.com');
    
    if (!isYoutube && !isVimeo) {
      alert('⚠️ Por favor ingresa una URL válida de YouTube o Vimeo');
      return;
    }
    
    // Verificar que no esté duplicado
    var existente = false;
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
    var videoId = extractVideoId(url);
    var thumbnail = '';
    var platform = '';
    var color = '';
    
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
    var itemHtml = '<div class="mg-video-externo-item" data-url="' + escapeHtml(url) + '" style="display: flex; gap: 10px; align-items: center; padding: 10px; background: #f9f9f9; border-radius: 4px; margin-bottom: 10px;">' +
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
    var $lista = $('#mg_videos_externos_lista');
    
    // Si existe el mensaje de "no hay videos", quitarlo
    $lista.find('p').remove();
    
    // Agregar el nuevo item
    $lista.append(itemHtml);
    
    // Limpiar input
    $('#mg_video_externo_input').val('');
    
    // Actualizar hidden input
    actualizarHiddenInput();
    
    // Feedback visual
    var $button = $('#mg_agregar_video_externo');
    var originalText = $button.html();
    $button.html('<span class="dashicons dashicons-yes" style="margin-top: 3px;"></span> ¡Agregado!');
    $button.css('background-color', '#46b450');
    
    setTimeout(function() {
      $button.html(originalText);
      $button.css('background-color', '');
    }, 1500);
    
    console.log('Video agregado:', url);
  });
  
  // ===== ELIMINAR VIDEO DE LA LISTA =====
  $(document).on('click', '.mg-eliminar-video-externo', function() {
    var url = $(this).data('url');
    
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
      
      console.log('Video eliminado:', url);
    }
  });
  
  // ===== PERMITIR AGREGAR CON ENTER =====
  $('#mg_video_externo_input').on('keypress', function(e) {
    if (e.which === 13) { // Enter
      e.preventDefault();
      $('#mg_agregar_video_externo').click();
    }
  });
  
  // ===== FUNCIÓN: ACTUALIZAR HIDDEN INPUT =====
  function actualizarHiddenInput() {
    var urls = [];
    
    $('.mg-video-externo-item').each(function() {
      urls.push($(this).data('url'));
    });
    
    // Guardar como string separado por \n
    $('#mg_portafolio_videos_externos_hidden').val(urls.join('\n'));
    
    console.log('Hidden input actualizado:', urls.length + ' videos');
  }
  
  // ===== FUNCIÓN: EXTRAER VIDEO ID =====
  function extractVideoId(url) {
    // YouTube
    var youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
    var youtubeMatch = url.match(youtubeRegex);
    if (youtubeMatch && youtubeMatch[1]) {
      return youtubeMatch[1];
    }
    
    // Vimeo
    var vimeoRegex = /vimeo\.com\/(\d+)/;
    var vimeoMatch = url.match(vimeoRegex);
    if (vimeoMatch && vimeoMatch[1]) {
      return vimeoMatch[1];
    }
    
    return null;
  }
  
  // ===== FUNCIÓN: ESCAPE HTML =====
  function escapeHtml(text) {
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }
  
});
