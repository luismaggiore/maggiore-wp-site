jQuery(document).ready(function($) {
  
  // ===== INICIALIZAR SELECT2 CON TAGS PARA VIDEOS EXTERNOS =====
  if ($('#mg_portafolio_videos_externos').length) {
    $('#mg_portafolio_videos_externos').select2({
      tags: true,
      tokenSeparators: ['\n', ','],
      width: '100%',
      placeholder: 'Escribe la URL del video y presiona Enter...',
      allowClear: true,
      createTag: function (params) {
        var term = $.trim(params.term);
        
        if (term === '') {
          return null;
        }
        
        // Validar que sea una URL válida
        var urlPattern = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|vimeo\.com)\/.+$/i;
        
        if (!urlPattern.test(term)) {
          // Mostrar error temporal
          $('#mg_videos_externos_error').remove();
          $('#mg_portafolio_videos_externos').parent().append(
            '<div id="mg_videos_externos_error" style="color: #d63638; font-size: 12px; margin-top: 5px;">⚠️ Por favor ingresa una URL válida de YouTube o Vimeo</div>'
          );
          setTimeout(function() {
            $('#mg_videos_externos_error').fadeOut(function() {
              $(this).remove();
            });
          }, 3000);
          return null;
        }
        
        // Limpiar error si existe
        $('#mg_videos_externos_error').remove();
        
        return {
          id: term,
          text: term,
          newTag: true
        };
      },
      templateResult: function(data) {
        if (data.newTag) {
          return $('<span><strong>+ Agregar:</strong> ' + data.text + '</span>');
        }
        return data.text;
      },
      templateSelection: function(data) {
        // Extraer plataforma de la URL
        var platform = '';
        var icon = '';
        
        if (data.text.includes('youtube.com') || data.text.includes('youtu.be')) {
          platform = 'YouTube';
          icon = '<span class="dashicons dashicons-video-alt3" style="color: #FF0000; font-size: 14px; vertical-align: middle; margin-right: 3px;"></span>';
        } else if (data.text.includes('vimeo.com')) {
          platform = 'Vimeo';
          icon = '<span class="dashicons dashicons-video-alt3" style="color: #1AB7EA; font-size: 14px; vertical-align: middle; margin-right: 3px;"></span>';
        }
        
        // Acortar URL para display
        var displayText = data.text;
        if (displayText.length > 50) {
          displayText = displayText.substring(0, 47) + '...';
        }
        
        return $('<span>' + icon + displayText + '</span>');
      }
    });
    
    // Actualizar preview cuando cambien las URLs
    $('#mg_portafolio_videos_externos').on('change', function() {
      updateVideosExternosPreview();
    });
    
    // Generar preview inicial
    updateVideosExternosPreview();
  }
  
  // ===== FUNCIÓN PARA ACTUALIZAR PREVIEW DE VIDEOS EXTERNOS =====
  function updateVideosExternosPreview() {
    var urls = $('#mg_portafolio_videos_externos').val();
    var $preview = $('#mg_videos_externos_preview');
    
    $preview.empty();
    
    if (!urls || urls.length === 0) {
      return;
    }
    
    urls.forEach(function(url) {
      var videoId = extractVideoId(url);
      var platform = '';
      var thumbnailUrl = '';
      
      if (url.includes('youtube.com') || url.includes('youtu.be')) {
        platform = 'YouTube';
        if (videoId) {
          thumbnailUrl = 'https://img.youtube.com/vi/' + videoId + '/mqdefault.jpg';
        }
      } else if (url.includes('vimeo.com')) {
        platform = 'Vimeo';
        // Vimeo requiere API para thumbnail, usamos placeholder
        thumbnailUrl = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="320" height="180" viewBox="0 0 320 180"%3E%3Crect fill="%231ab7ea" width="320" height="180"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="white" font-size="30" font-family="Arial"%3EVimeo%3C/text%3E%3C/svg%3E';
      }
      
      if (thumbnailUrl) {
        var $item = $('<div class="video-preview-item" style="position: relative; width: 160px;">' +
          '<div style="position: relative; width: 160px; height: 90px; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">' +
            '<img src="' + thumbnailUrl + '" style="width: 100%; height: 100%; object-fit: cover;" alt="' + platform + ' video">' +
            '<div style="position: absolute; bottom: 5px; right: 5px; background: rgba(0,0,0,0.8); color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">' + platform + '</div>' +
          '</div>' +
          '<div style="font-size: 10px; margin-top: 3px; color: #666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' + url + '">' + url + '</div>' +
        '</div>');
        
        $preview.append($item);
      }
    });
  }
  
  // ===== FUNCIÓN PARA EXTRAER VIDEO ID =====
  function extractVideoId(url) {
    var videoId = null;
    
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
  
  // ===== COPIAR URL AL PORTAPAPELES (BONUS) =====
  $(document).on('click', '.video-preview-item', function() {
    var url = $(this).find('div[title]').attr('title');
    
    // Copiar al portapapeles
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(function() {
        // Feedback visual
        var $item = $(this).parent();
        var originalBg = $item.css('background-color');
        $item.css('background-color', '#46b450');
        setTimeout(function() {
          $item.css('background-color', originalBg);
        }, 300);
      }.bind(this));
    }
  });
  
});
