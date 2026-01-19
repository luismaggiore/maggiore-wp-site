<?php get_header(); ?>
<main class="container py-5">
<?php while (have_posts()) : the_post(); ?>

    <?php
    $portafolio_id = get_the_ID();
    $cliente_id = mg_get_translated_post_id(post_id: get_post_meta(get_the_ID(), 'mg_portafolio_cliente', true));
    $caso_id = mg_get_translated_post_id(get_post_meta(get_the_ID(), 'mg_portafolio_caso_exito', true));
    $servicios = mg_translate_post_ids((array) get_post_meta(get_the_ID(), 'mg_portafolio_servicio', true));
    $equipo_ids = (array) get_post_meta(get_the_ID(), 'mg_portafolio_equipo', true);
    $cliente_logo = get_the_post_thumbnail_url($cliente_id, 'thumbnail');
    $cliente_nombre = get_the_title($cliente_id);
    $descripcion = get_post_meta($portafolio_id, 'mg_portafolio_descripcion', true);
    $enlace = get_post_meta($portafolio_id, 'mg_portafolio_source', true);
    $columnas = $caso_id ? 'col-xl-8' : 'col-xl-12';
    $fecha = get_post_meta($portafolio_id, 'mg_portafolio_fecha', true);
     

    $fecha_formateada = '';
if ($fecha) {
    $date = DateTime::createFromFormat('Y-m', $fecha);
    if ($date) {
        // Usar date_i18n() de WordPress para respetar el idioma activo
        $timestamp = $date->getTimestamp();
        $fecha_formateada = date_i18n('F Y', $timestamp);
    }
}
?>
<article class="portafolio-single p-top" >
<div class="mb-5">
    <h1 class="display-4 mb-3"><?php the_title(); ?></h1>


<?php if ($fecha_formateada): ?>
                    <p class="mb-2" style="font-size: 0.875rem;">
                          
                        <time datetime="<?= esc_attr($fecha); ?>" itemprop="datePublished">
                            <?= esc_html($fecha_formateada); ?>
                        </time>
                    </p>
                <?php endif; ?>
  <?php if ($descripcion): ?>
        <p class="lead"><?= esc_html($descripcion); ?></p>
      <?php endif; ?>

</div>
    <div class="row g-2">

    
    <?php if (!empty($equipo_ids)): ?>
      <div class="col-md-8" aria-labelledby="equipo-portafolio-title"> <div class=" card-mg">
        <h3 class="label" id="equipo-portafolio-title"><?php _e('Equipo que participó', 'maggiore'); ?></h3>
        
        <ul class="services-tags" style="margin-bottom:0">
          <?php foreach ($equipo_ids as $equipo_id): 
              $nombre = get_the_title($equipo_id);
              $foto = get_the_post_thumbnail_url($equipo_id, 'thumbnail');
              $link = get_permalink($equipo_id);
          ?>
            <li class="person-tag" itemscope itemtype="https://schema.org/Person">
              <a href="<?= esc_url($link); ?>" itemprop="url" aria-label="<?= esc_attr("Ver perfil de $nombre"); ?>">
                <?php if ($foto): ?>
                  <img src="<?= esc_url($foto); ?>"
                       alt="<?= esc_attr("Foto de $nombre"); ?>"
                       width="20"
                       height="20"
                       loading="lazy"
                       decoding="async"
                       itemprop="image">
                <?php endif; ?>
                <span itemprop="name"><?= esc_html($nombre); ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div></div>
    <?php endif; ?>


    <?php if ($cliente_id): ?>
      <div class="col-md-4 "><div class=" card-mg">
    <h3 class="label"><?php _e('Cliente', 'maggiore'); ?></h3>
      <div class="client-tag" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
        <a href="<?= esc_url(get_permalink($cliente_id)) ?>" itemprop="url"
           aria-label="<?= esc_attr(sprintf(__('Ver página de %s', 'maggiore'), $cliente_nombre)) ?>">
          <?php if ($cliente_logo): ?>
            <img src="<?= esc_url($cliente_logo) ?>"
                 alt="<?= esc_attr("Logo de $cliente_nombre") ?>"
                 width="20"
                 height="20"
                 loading="lazy"
                 decoding="async"
                 itemprop="logo">
          <?php endif; ?>
          <span itemprop="name"><?= esc_html($cliente_nombre) ?></span>
        </a>
      </div> </div></div>
    <?php endif; ?>

    
    <?php if ($caso_id) : ?>
      <div class="col-xl-4"><div class=" card-mg">
    <h3 class="label"><?php _e('Caso de éxito', 'maggiore'); ?></h3>
            <a class="service-tag" href="<?php echo get_permalink($caso_id); ?>"><?php echo get_the_title($caso_id); ?></a>
        </p></div></div>
    <?php endif; ?>

    <?php if (!empty($servicios)): ?>
      <div class="<?= esc_html($columnas); ?>"          
      ><div class=" card-mg">
    <h3 class="label"><?php _e('Servicios aplicados', 'maggiore'); ?></h3>
      <ul class="services-tags">
        <?php foreach ($servicios as $sid): ?>
          <li>
            <a class="service-tag" href="<?= get_permalink($sid) ?>">
              <?= esc_html(get_the_title($sid)) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul></div></div>
    <?php endif; ?>

</div>
    <?php
    // ===================== RECUPERAR DATOS =====================
    
    // Galería de IMÁGENES
    $galeria_raw = get_post_meta(get_the_ID(), 'mg_portafolio_galeria', true);
    $galeria_ids = [];
    if (!empty($galeria_raw)) {
        if (is_array($galeria_raw)) {
            $galeria_ids = array_filter(array_map('intval', $galeria_raw));
        } else {
            $galeria_ids = array_filter(array_map('intval', explode(',', $galeria_raw)));
        }
    }

    // Galería de VIDEOS
    $videos_raw = get_post_meta(get_the_ID(), 'mg_portafolio_videos', true);
    $videos_ids = [];
    if (!empty($videos_raw)) {
        if (is_array($videos_raw)) {
            $videos_ids = array_filter(array_map('intval', $videos_raw));
        } else {
            $videos_ids = array_filter(array_map('intval', explode(',', $videos_raw)));
        }
    }

    // Videos EXTERNOS
    $videos_externos = get_post_meta(get_the_ID(), 'mg_portafolio_videos_externos', true);
    $videos_externos_array = [];
    if (!empty($videos_externos)) {
        $videos_externos_array = array_filter(array_map('trim', explode("\n", $videos_externos)));
    }

    // Layout seleccionado para IMÁGENES
    $layout = get_post_meta(get_the_ID(), 'mg_portafolio_layout', true) ?: 'grid';

    // Layout seleccionado para VIDEOS
    $layout_videos = get_post_meta(get_the_ID(), 'mg_portafolio_layout_videos', true) ?: 'grid';
    ?>

    <?php // ========== GALERÍA DE IMÁGENES ========== ?>
    <?php if (!empty($galeria_ids)): ?>
      <section class="portafolio-images my-5">
                     <div class="feature-name-2 mb-2" >
        <h2><?php _e('Galería de Imágenes', 'maggiore'); ?></h2></div>
        
        <?php 
        $layout_class = 'layout-' . $layout;
        $container_class = '';
        
        switch ($layout) {
            case 'grid':
                $container_class = 'row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2 ';
                break;
            case 'masonry':
                $container_class = 'masonry-grid';
                break;
            case 'vertical':
                $container_class = 'vertical-gallery';
                break;
            case 'slider':
                $container_class = 'swiper-wrapper';
                break;
        }
        ?>

        <div class="images-gallery <?= esc_attr($layout_class); ?> <?= esc_attr($container_class); ?>">
          
          <?php foreach ($galeria_ids as $image_id): 
              if (!wp_attachment_is_image($image_id)) continue;
              
              $image_url = wp_get_attachment_image_url($image_id, 'large');
              $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: get_the_title($image_id);
              $image_full = wp_get_attachment_image_url($image_id, 'full');
          ?>
            <div class="media-item <?= $layout === 'grid' ? 'col' : ''; ?>">
              <figure class="figure mb-0">
                <a href="<?= esc_url($image_full); ?>" data-lightbox="gallery" data-title="<?= esc_attr($image_alt); ?>">
                  <img src="<?= esc_url($image_url); ?>"
                       alt="<?= esc_attr($image_alt); ?>"
                       class="img-fluid rounded"
                       loading="lazy"
                       decoding="async">
                </a>
              </figure>
            </div>
          <?php endforeach; ?>

        </div>
      </section>
    <?php endif; ?>

    <?php // ========== GALERÍA DE VIDEOS ========== ?>
    <?php if (!empty($videos_ids) || !empty($videos_externos_array)): ?>
      <section class="portafolio-videos my-5">
               <div class="feature-name-2 mb-2" >
        <h2><?php _e('Galería de Videos', 'maggiore'); ?></h2></div>
        
      <?php 
        // Determinar clases según el layout de videos seleccionado
        $videos_container_class = '';
        $videos_layout_class = 'layout-videos-' . $layout_videos;
        
        switch ($layout_videos) {
            case 'grid':
                $videos_container_class = 'videos-masonry';
                break;
            case 'vertical':
                $videos_container_class = 'videos-vertical';
                break;
        }
        ?>
        
        <div class="<?= esc_attr($videos_container_class); ?> <?= esc_attr($videos_layout_class); ?>">
          
          <?php // ========== VIDEOS DEL SERVIDOR ========== ?>
          <?php foreach ($videos_ids as $video_id): 
              $video_url = wp_get_attachment_url($video_id);
              if (!$video_url) continue;
              
              $video_mime = get_post_mime_type($video_id);
              $video_title = get_the_title($video_id);
              $video_metadata = wp_get_attachment_metadata($video_id);
              
              // Detectar ratio del video
              $ratio_class = 'ratio-16x9';
              if (isset($video_metadata['width']) && isset($video_metadata['height'])) {
                  $width = $video_metadata['width'];
                  $height = $video_metadata['height'];
                  $ratio = $width / $height;
                  
                  if ($ratio > 1.5) {
                      $ratio_class = 'ratio-16x9';
                  } elseif ($ratio < 0.7) {
                      $ratio_class = 'ratio-9x16';
                  } else {
                      $ratio_class = 'ratio-1x1';
                  }
              }
              
              // Thumbnail del video - CORREGIDO
              $poster = '';
              
              // 1. Intentar obtener imagen destacada del video
              $thumb_id = get_post_thumbnail_id($video_id);
              if ($thumb_id) {
                  $poster = wp_get_attachment_image_url($thumb_id, 'large');
              }
              
              // 2. Si no hay, intentar obtener de metadata del video
              if (!$poster && isset($video_metadata['thumb'])) {
                  $upload_dir = wp_upload_dir();
                  $video_file_path = $video_metadata['file'];
                  $video_dir = dirname($video_file_path);
                  $poster = $upload_dir['baseurl'] . '/' . $video_dir . '/' . $video_metadata['thumb'];
              }
              
              // 3. Si aún no hay thumbnail, crear uno placeholder o usar canvas
              if (!$poster) {
                  // Placeholder SVG mientras se genera el thumbnail con JavaScript
                  $poster = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="1280" height="720" viewBox="0 0 1280 720"%3E%3Crect fill="%23333" width="1280" height="720"/%3E%3Cpath fill="%23fff" d="M590 310 L690 360 L590 410 Z"/%3E%3C/svg%3E';
              }
          ?>
            <div class="video-item <?= esc_attr($ratio_class); ?>">
              <div class="custom-video-player" 
                   data-src="<?= esc_url($video_url); ?>"
                   data-mime="<?= esc_attr($video_mime); ?>"
                   data-poster="<?= esc_url($poster); ?>"
                   data-title="<?= esc_attr($video_title); ?>"
                   data-needs-thumb="<?= strpos($poster, 'data:image/svg') !== false ? 'true' : 'false'; ?>">
                
                <!-- Video element (oculto inicialmente, usado para generar thumbnail si es necesario) -->
                <video class="video-element" playsinline preload="metadata" <?php if (strpos($poster, 'data:image/svg') === false): ?>poster="<?= esc_url($poster); ?>"<?php endif; ?>></video>
                
                <!-- Canvas oculto para capturar frame del video -->
                <canvas class="thumbnail-canvas" style="display: none;"></canvas>
                
                <!-- Thumbnail/Poster con imagen visible -->
                <div class="video-poster">
                  <img src="<?= esc_url($poster); ?>" alt="<?= esc_attr($video_title); ?>" class="poster-image">
                  <div class="poster-overlay"></div>
                  <button class="play-btn-center" aria-label="Reproducir video">
                    <svg width="80" height="80" viewBox="0 0 80 80">
                      <circle cx="40" cy="40" r="38" fill="rgba(0,0,0,0.7)" stroke="white" stroke-width="2"/>
                      <path d="M 30 20 L 30 60 L 60 40 Z" fill="white"/>
                    </svg>
                  </button>
                </div>
                
                <!-- Controles custom -->
                <div class="video-controls">
                  <div class="progress-bar">
                    <div class="progress-filled"></div>
                  </div>
                  <div class="controls-bottom">
                    <button class="control-btn play-pause" aria-label="Play/Pause">
                      <svg class="play-icon" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" fill="white"/>
                      </svg>
                      <svg class="pause-icon" width="24" height="24" viewBox="0 0 24 24" style="display:none;">
                        <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z" fill="white"/>
                      </svg>
                    </button>
                    <div class="time-display">
                      <span class="current-time">0:00</span>
                      <span>/</span>
                      <span class="duration">0:00</span>
                    </div>
                    <div class="spacer"></div>
                    <button class="control-btn volume-btn" aria-label="Volumen">
                      <svg width="24" height="24" viewBox="0 0 24 24">
                        <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z" fill="white"/>
                      </svg>
                    </button>
                    <input type="range" class="volume-slider" min="0" max="100" value="100">
                    <button class="control-btn fullscreen-btn" aria-label="Pantalla completa">
                      <svg width="24" height="24" viewBox="0 0 24 24">
                        <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z" fill="white"/>
                      </svg>
                    </button>
                  </div>
                </div>
                
                <!-- Título del video -->
                <?php if ($video_title): ?>
                  <div class="video-title"><?= esc_html($video_title); ?></div>
                <?php endif; ?>
                
              </div>
            </div>
          <?php endforeach; ?>

          <?php // ========== VIDEOS EXTERNOS ========== ?>
          <?php foreach ($videos_externos_array as $video_url): 
              $embed_url = mg_get_video_embed_url($video_url);
              if (!$embed_url) continue;
              
              $is_youtube = strpos($video_url, 'youtube') !== false || strpos($video_url, 'youtu.be') !== false;
              $is_vimeo = strpos($video_url, 'vimeo') !== false;
              
              $thumbnail = '';
              if ($is_youtube) {
                  preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video_url, $matches);
                  if (isset($matches[1])) {
                      $video_id = $matches[1];
                      $thumbnail = "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg";
                  }
              } elseif ($is_vimeo) {
                  $thumbnail = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="1280" height="720" viewBox="0 0 1280 720"%3E%3Crect fill="%231ab7ea" width="1280" height="720"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="white" font-size="80" font-family="Arial"%3EVimeo%3C/text%3E%3C/svg%3E';
              }
          ?>
            <div class="video-item ratio-16x9">
              <div class="external-video" data-embed-url="<?= esc_attr($embed_url); ?>">
                <div class="video-poster">
                  <?php if ($thumbnail): ?>
                    <img src="<?= esc_url($thumbnail); ?>" alt="Video thumbnail" class="poster-image">
                  <?php endif; ?>
                  <div class="poster-overlay"></div>
                  <button class="play-btn-center" aria-label="Reproducir video">
                    <svg width="80" height="80" viewBox="0 0 80 80">
                      <circle cx="40" cy="40" r="38" fill="rgba(255,0,0,0.9)" stroke="white" stroke-width="2"/>
                      <path d="M 30 20 L 30 60 L 60 40 Z" fill="white"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
      </section>
    <?php endif; ?>
          
                <?php if ($enlace): ?>
                    <section>
                      <h2 class="p-source display-6">
                      <?php _e('  Vea este proyecto en acción ', 'maggiore'); ?>  
                      <a href="<?= esc_html($enlace); ?>" target="_blank">
                      <?php _e('  aquí ', 'maggiore'); ?>  
                      </a></section> </h2>
      
      <?php endif; ?>

                  

</article>

<?php
set_query_var('prev_label', __('Proyecto anterior', 'maggiore'));
set_query_var('next_label', __('Siguiente proyecto', 'maggiore'));
set_query_var('show_thumbnail', false); // Si quieres mostrar logos
get_template_part('template-parts/navigation', 'single');
?>
<!-- ========== JAVASCRIPT ========== -->
<script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/portafolio.js'); ?>"></script>

</main>
<?php endwhile; ?>
<?php get_footer(); ?>
