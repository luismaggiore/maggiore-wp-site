<?php
if (!defined('ABSPATH')) exit;

function mg_metabox_portafolio() {
    add_meta_box(
        'mg_portafolio_relaciones',
        __('Portafolio — Información y Relaciones', 'maggiore'),
        'mg_metabox_portafolio_render',
        'mg_portafolio',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'mg_metabox_portafolio');

function mg_metabox_portafolio_render($post) {
    wp_nonce_field('mg_portafolio_nonce_action', 'mg_portafolio_nonce');

    $descripcion = get_post_meta($post->ID, 'mg_portafolio_descripcion', true);
    $cliente     = get_post_meta($post->ID, 'mg_portafolio_cliente', true);
    $servicios   = (array) get_post_meta($post->ID, 'mg_portafolio_servicio', true);
    $equipo      = (array) get_post_meta($post->ID, 'mg_portafolio_equipo', true);
    $caso        = get_post_meta($post->ID, 'mg_portafolio_caso_exito', true);
    $source      = get_post_meta($post->ID, 'mg_portafolio_source', true);
    $fecha = get_post_meta($post->ID, 'mg_portafolio_fecha', true);
     
    // Recuperar la galería de IMÁGENES
    $galeria_raw = get_post_meta($post->ID, 'mg_portafolio_galeria', true);
    $galeria_ids = [];
    if (!empty($galeria_raw)) {
        if (is_array($galeria_raw)) {
            $galeria_ids = array_filter(array_map('intval', $galeria_raw));
        } else {
            $galeria_ids = array_filter(array_map('intval', explode(',', $galeria_raw)));
        }
    }
    
    // NUEVO: Recuperar la galería de VIDEOS
    $videos_raw = get_post_meta($post->ID, 'mg_portafolio_videos', true);
    $videos_ids = [];
    if (!empty($videos_raw)) {
        if (is_array($videos_raw)) {
            $videos_ids = array_filter(array_map('intval', $videos_raw));
        } else {
            $videos_ids = array_filter(array_map('intval', explode(',', $videos_raw)));
        }
    }
    
    // NUEVO: URLs de videos externos
    $videos_externos = get_post_meta($post->ID, 'mg_portafolio_videos_externos', true);
    
    // NUEVO: Layout seleccionado
    $layout = get_post_meta($post->ID, 'mg_portafolio_layout', true) ?: 'grid';
    $layout_videos = get_post_meta($post->ID, 'mg_portafolio_layout_videos', true) ?: 'grid';

    // PDF Flipbook
    $pdf_id  = (int) get_post_meta($post->ID, 'mg_portafolio_pdf', true);
    $pdf_url = $pdf_id ? wp_get_attachment_url($pdf_id) : '';

    $clientes_options  = mg_get_clientes_options();
    $servicios_options = mg_get_servicios_options();
    $equipo_options    = mg_get_equipo_options();
    $casos_options     = mg_get_casos_exito_options();
    ?>

    <!-- DESCRIPCIÓN -->
    <p>
        <label for="mg_portafolio_descripcion">
            <strong><?php _e('Descripción del proyecto', 'maggiore'); ?></strong>
        </label>
    </p>
    <textarea name="mg_portafolio_descripcion" id="mg_portafolio_descripcion" class="widefat" rows="4"><?= esc_textarea($descripcion); ?></textarea>

    <hr>

    <!-- CLIENTE -->
    <p>


    
        <label for="mg_portafolio_cliente">
            <strong><?php _e('Cliente', 'maggiore'); ?></strong>
        </label>
    </p>
    <select name="mg_portafolio_cliente" id="mg_portafolio_cliente" class="widefat">
        <option value=""><?php _e('Seleccionar cliente', 'maggiore'); ?></option>
        <?php foreach ($clientes_options as $id => $label): ?>
            <option value="<?= esc_attr($id); ?>" <?= selected($cliente, $id, false); ?>>
                <?= esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
<!-- FECHA -->

  <label for="mg_portafolio_fecha"><?php _e('Fecha del proyecto', 'maggiore') ?></strong>
        </label>
   <input type="month" name="mg_portafolio_fecha" class="widefat" value="<?=  esc_attr($fecha)?>">
    



    <!-- SERVICIOS -->
    <p class="mt-3">
        <label><strong><?php _e('Servicios aplicados', 'maggiore'); ?></strong></label>
    </p>
    <select name="mg_portafolio_servicio[]" class="widefat select2" multiple>
        <?php foreach ($servicios_options as $id => $label): ?>
            <option value="<?= esc_attr($id); ?>" <?= in_array($id, $servicios) ? 'selected' : ''; ?>>
                <?= esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- EQUIPO -->
    <p class="mt-3">
        <label><strong><?php _e('Miembros del equipo', 'maggiore'); ?></strong></label>
    </p>
    <select name="mg_portafolio_equipo[]" class="widefat select2" multiple>
        <?php foreach ($equipo_options as $id => $label): ?>
            <option value="<?= esc_attr($id); ?>" <?= in_array($id, $equipo) ? 'selected' : ''; ?>>
                <?= esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- CASO DE ÉXITO -->
    <p class="mt-3">
        <label for="mg_portafolio_caso_exito">
            <strong><?php _e('Caso de Éxito relacionado (opcional)', 'maggiore'); ?></strong>
        </label>
    </p>
    <select name="mg_portafolio_caso_exito" id="mg_portafolio_caso_exito" class="widefat">
        <option value=""><?php _e('Ninguno', 'maggiore'); ?></option>
        <?php foreach ($casos_options as $id => $label): ?>
            <option value="<?= esc_attr($id); ?>" <?= selected($caso, $id, false); ?>>
                <?= esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- LINK DEL PRODUCTO EN DISPLAY -->
    <p class="mt-3">
        <label for="mg_portafolio_source">
            <strong><?php _e('Link del proyecto en display', 'maggiore'); ?></strong>
        </label>
    </p>
    <textarea name="mg_portafolio_source" id="mg_portafolio_source" class="widefat" rows="1"><?= esc_textarea($source); ?></textarea>

    <hr style="margin: 30px 0;">

    <!-- ===================== NUEVO: SELECTOR DE LAYOUT ===================== -->
    <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #2271b1; margin-bottom: 20px;">
        <p style="margin-top: 0;">
            <label for="mg_portafolio_layout">
                <strong><?php _e('📐 Diseño de la Galería', 'maggiore'); ?></strong>
            </label>
            <br>
            <small style="color: #666;"><?php _e('Selecciona cómo se mostrarán las imágenes y videos en el proyecto', 'maggiore'); ?></small>
        </p>
        <select name="mg_portafolio_layout" id="mg_portafolio_layout" class="widefat">
            <option value="grid" <?= selected($layout, 'grid', false); ?>>
                🔲 Grid Cuadrado - Mosaico de cuadrados uniformes
            </option>
            <option value="masonry" <?= selected($layout, 'masonry', false); ?>>
                🧱 Masonry - Diseño de Pinterest (altura variable)
            </option>
            <option value="vertical" <?= selected($layout, 'vertical', false); ?>>
                ⬇️ Galería Vertical - Una columna hacia abajo
            </option>
            <option value="slider" <?= selected($layout, 'slider', false); ?>>
                ➡️ Slider/Carrusel - Deslizable con flechas
            </option>
        </select>
    </div>

    <hr style="margin: 30px 0;">

    <!-- ===================== GALERÍA DE IMÁGENES (EXISTENTE) ===================== -->
    <p><strong>📸 <?php _e('Galería de imágenes del proyecto', 'maggiore'); ?></strong></p>
    
    <div style="background: #f0f0f0; padding: 10px; margin-bottom: 10px; border-radius: 4px; font-size: 11px;">
        <strong>Debug:</strong> 
        <?php if (!empty($galeria_ids)): ?>
            <?= count($galeria_ids); ?> imágenes guardadas (IDs: <?= implode(', ', $galeria_ids); ?>)
        <?php else: ?>
            No hay imágenes guardadas
        <?php endif; ?>
    </div>
    
    <div id="mg_galeria_preview" style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom: 10px;">
        <?php if (!empty($galeria_ids)): ?>
            <?php foreach ($galeria_ids as $img_id): ?>
                <div class="mg-thumb-wrapper" data-id="<?= esc_attr($img_id); ?>" style="position: relative;">
                    <?= wp_get_attachment_image($img_id, 'thumbnail'); ?>
                    <button type="button" class="mg-remove-image" data-id="<?= esc_attr($img_id); ?>" 
                            style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px; line-height: 1;">×</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <input type="hidden" name="mg_portafolio_galeria" id="mg_portafolio_galeria_input" value="<?= esc_attr(implode(',', $galeria_ids)); ?>">
    <br>
    <button type="button" class="button button-primary" id="mg_select_galeria">
        <span class="dashicons dashicons-images-alt2" style="margin-top: 3px;"></span>
        <?php _e('Agregar imágenes', 'maggiore'); ?>
    </button>
    <button type="button" class="button" id="mg_clear_galeria" style="margin-left: 5px;">
        <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
        <?php _e('Limpiar galería', 'maggiore'); ?>
    </button>

    <hr style="margin: 30px 0;">
    <!-- ===================== SELECTOR DE LAYOUT PARA VIDEOS ===================== -->
    <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ff9800; margin-bottom: 20px;">
        <p style="margin-top: 0;">
            <label for="mg_portafolio_layout_videos">
                <strong>🎬 <?php _e('Diseño de la Galería de Videos', 'maggiore'); ?></strong>
            </label>
            <br>
            <small style="color: #666;"><?php _e('Selecciona cómo se mostrarán los videos en el proyecto', 'maggiore'); ?></small>
        </p>
        <select name="mg_portafolio_layout_videos" id="mg_portafolio_layout_videos" class="widefat">
            <option value="grid" <?= selected($layout_videos, 'grid', false); ?>>
                🔲 Grid Masonry - Columnas fluidas (ideal para múltiples videos)
            </option>
            <option value="vertical" <?= selected($layout_videos, 'vertical', false); ?>>
                ⬇️ Vertical Completo - Ancho total hacia abajo (ideal para 1-2 videos largos)
            </option>
        </select>
        <p style="color: #666; font-size: 11px; margin-bottom: 0; margin-top: 10px;">
            💡 <strong>Tip:</strong> Usa "Grid Masonry" para reels/clips cortos. Usa "Vertical Completo" para videos largos horizontales.
        </p>
    </div>
    <!-- ===================== NUEVO: GALERÍA DE VIDEOS ===================== -->
    <p><strong>🎬 <?php _e('Galería de videos del proyecto', 'maggiore'); ?></strong></p>
    <p style="color: #666; font-size: 12px; margin-top: -8px;">
        ⚠️ <strong>Recomendaciones:</strong> Videos cortos (reels, clips). Máximo 100MB por video. 
        Para videos largos, usa el campo de "Videos Externos" más abajo.
    </p>
    
    <div style="background: #f0f0f0; padding: 10px; margin-bottom: 10px; border-radius: 4px; font-size: 11px;">
        <strong>Debug:</strong> 
        <?php if (!empty($videos_ids)): ?>
            <?= count($videos_ids); ?> videos guardados (IDs: <?= implode(', ', $videos_ids); ?>)
        <?php else: ?>
            No hay videos guardados
        <?php endif; ?>
    </div>
    
    <div id="mg_videos_preview" style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom: 10px;">
        <?php if (!empty($videos_ids)): ?>
            <?php foreach ($videos_ids as $video_id): 
                $video_url = wp_get_attachment_url($video_id);
                $video_metadata = wp_get_attachment_metadata($video_id);
                $filesize = size_format(filesize(get_attached_file($video_id)), 2);
                $filename = basename(get_attached_file($video_id));
                
                // Intentar obtener thumbnail
                $poster = wp_get_attachment_image_url($video_id, 'thumbnail');
                if (!$poster) {
                    $poster = includes_url('images/media/video.png');
                }
            ?>
                <div class="mg-video-wrapper" data-id="<?= esc_attr($video_id); ?>" style="position: relative; width: 120px;">
                    <div style="position: relative; width: 120px; height: 120px; border: 1px solid #ccc; border-radius: 3px; overflow: hidden; background: #000;">
                        <img src="<?= esc_url($poster); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <span class="dashicons dashicons-controls-play" style="color: white; font-size: 24px;"></span>
                        </div>
                    </div>
                    <div style="font-size: 10px; margin-top: 3px; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= esc_attr($filename); ?>">
                        <?= esc_html($filename); ?>
                    </div>
                    <div style="font-size: 9px; text-align: center; color: #666;">
                        <?= $filesize; ?>
                    </div>
                    <button type="button" class="mg-remove-video" data-id="<?= esc_attr($video_id); ?>" 
                            style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px; line-height: 1;">×</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <input type="hidden" name="mg_portafolio_videos" id="mg_portafolio_videos_input" value="<?= esc_attr(implode(',', $videos_ids)); ?>">
    <br>
    <button type="button" class="button button-primary" id="mg_select_videos">
        <span class="dashicons dashicons-video-alt3" style="margin-top: 3px;"></span>
        <?php _e('Agregar videos', 'maggiore'); ?>
    </button>
    <button type="button" class="button" id="mg_clear_videos" style="margin-left: 5px;">
        <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
        <?php _e('Limpiar videos', 'maggiore'); ?>
    </button>

    <hr style="margin: 30px 0;">

  <!-- ===================== VIDEOS EXTERNOS - SISTEMA SIMPLE ===================== -->
    <p><strong>🔗 <?php _e('Videos Externos (YouTube/Vimeo)', 'maggiore'); ?></strong></p>
    <p style="color: #666; font-size: 12px; margin-top: -8px;">
        Para videos largos o que ya tengas en YouTube/Vimeo. Agrega un link a la vez.
    </p>
    
    <?php
    // Convertir el string de URLs a array
    $videos_externos_array = [];
    if (!empty($videos_externos)) {
        $videos_externos_array = array_filter(array_map('trim', explode("\n", $videos_externos)));
    }
    ?>
    
    <!-- Campo para agregar nuevo video -->
    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
        <input type="text" 
               id="mg_video_externo_input" 
               class="widefat" 
               placeholder="Pega aquí la URL del video (YouTube o Vimeo)"
               style="flex: 1;">
        <button type="button" 
                class="button button-primary" 
                id="mg_agregar_video_externo"
                style="white-space: nowrap;">
            <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
            Agregar video
        </button>
    </div>
    
    <!-- Lista de videos agregados -->
    <div id="mg_videos_externos_lista" style="margin-bottom: 15px;">
        <?php if (!empty($videos_externos_array)): ?>
            <?php foreach ($videos_externos_array as $index => $video_url): 
                // Detectar plataforma
                $is_youtube = (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false);
                $is_vimeo = strpos($video_url, 'vimeo.com') !== false;
                
                // Extraer ID del video para thumbnail
                $thumbnail = '';
                if ($is_youtube) {
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video_url, $matches);
                    if (isset($matches[1])) {
                        $thumbnail = 'https://img.youtube.com/vi/' . $matches[1] . '/mqdefault.jpg';
                    }
                    $platform = 'YouTube';
                    $color = '#FF0000';
                } elseif ($is_vimeo) {
                    $thumbnail = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="320" height="180" viewBox="0 0 320 180"%3E%3Crect fill="%231ab7ea" width="320" height="180"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="white" font-size="30" font-family="Arial"%3EVimeo%3C/text%3E%3C/svg%3E';
                    $platform = 'Vimeo';
                    $color = '#1AB7EA';
                } else {
                    continue; // Saltar si no es YouTube o Vimeo
                }
            ?>
                <div class="mg-video-externo-item" data-url="<?= esc_attr($video_url); ?>" style="display: flex; gap: 10px; align-items: center; padding: 10px; background: #f9f9f9; border-radius: 4px; margin-bottom: 10px;">
                    <!-- Thumbnail -->
                    <div style="flex-shrink: 0; width: 120px; height: 68px; border-radius: 3px; overflow: hidden; background: #000;">
                        <img src="<?= esc_url($thumbnail); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?= esc_attr($platform); ?> thumbnail">
                    </div>
                    
                    <!-- Info -->
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                            <span class="dashicons dashicons-video-alt3" style="color: <?= $color; ?>; font-size: 16px;"></span>
                            <strong style="color: <?= $color; ?>;"><?= $platform; ?></strong>
                        </div>
                        <div style="font-size: 12px; color: #666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= esc_attr($video_url); ?>">
                            <?= esc_html($video_url); ?>
                        </div>
                    </div>
                    
                    <!-- Botón eliminar -->
                    <button type="button" 
                            class="button mg-eliminar-video-externo" 
                            data-url="<?= esc_attr($video_url); ?>"
                            style="flex-shrink: 0;">
                        <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
                        Eliminar
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #666; font-style: italic;">No hay videos externos agregados. Usa el campo de arriba para agregar uno.</p>
        <?php endif; ?>
    </div>
    
    <!-- Input hidden para guardar todos los links -->
    <input type="hidden" name="mg_portafolio_videos_externos" id="mg_portafolio_videos_externos_hidden" value="<?= esc_attr($videos_externos); ?>">
    
    <p style="color: #666; font-size: 11px;">
        💡 <strong>Tip:</strong> Los videos externos no consumen espacio en tu servidor y cargan más rápido.
    </p>

    <hr style="margin: 30px 0;">

    <!-- ===================== PDF FLIPBOOK ===================== -->
    <div style="background: #eef6ff; padding: 15px; border-left: 4px solid #0073aa; margin-bottom: 20px;">
        <p style="margin-top: 0;">
            <strong>📄 <?php _e('PDF Flipbook (Manual de Marca / Documento)', 'maggiore'); ?></strong><br>
            <small style="color: #666;"><?php _e('Sube un PDF para mostrarlo como un libro interactivo en el portafolio. Ideal para manuales de marca, brochures, catálogos.', 'maggiore'); ?></small>
        </p>

        <div id="mg_pdf_preview">
            <?php if ($pdf_id && $pdf_url): ?>
                <?php
                $pdf_file  = get_attached_file($pdf_id);
                $pdf_name  = basename($pdf_file);
                $pdf_size  = $pdf_file && file_exists($pdf_file) ? size_format(filesize($pdf_file), 2) : '';
                ?>
                <div class="mg-pdf-preview-item" style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:#f0f7ff;border:1px solid #b3d4f5;border-radius:4px;margin-bottom:10px;">
                    <span class="dashicons dashicons-pdf" style="font-size:32px;width:32px;height:32px;color:#c00;flex-shrink:0;"></span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= esc_url($pdf_url); ?>">
                            <?= esc_html($pdf_name); ?>
                        </div>
                        <?php if ($pdf_size): ?>
                            <div style="font-size:11px;color:#666;margin-top:2px;"><?= esc_html($pdf_size); ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="mg_remove_pdf" class="button" style="flex-shrink:0;">
                        <span class="dashicons dashicons-trash" style="margin-top:3px;"></span> Quitar
                    </button>
                </div>
            <?php else: ?>
                <p style="color:#999;font-style:italic;margin:0 0 10px;">No hay PDF seleccionado.</p>
            <?php endif; ?>
        </div>

        <input type="hidden"
               name="mg_portafolio_pdf"
               id="mg_portafolio_pdf_input"
               value="<?= esc_attr($pdf_id ?: ''); ?>">

        <button type="button" class="button button-primary" id="mg_select_pdf">
            <span class="dashicons dashicons-upload" style="margin-top:3px;"></span>
            <?php _e('Seleccionar / cambiar PDF', 'maggiore'); ?>
        </button>

        <?php if ($pdf_url): ?>
            <a href="<?= esc_url($pdf_url); ?>" target="_blank" class="button" style="margin-left:8px;">
                <span class="dashicons dashicons-visibility" style="margin-top:3px;"></span>
                Ver PDF
            </a>
        <?php endif; ?>

        <p style="color:#666;font-size:11px;margin-top:10px;margin-bottom:0;">
            💡 <strong>Tip:</strong> Solo archivos PDF. El flipbook se renderizará automáticamente en el frontend al existir un PDF asignado.
        </p>
    </div>

    <style>
        .mg-thumb-wrapper { position: relative; }
        .mg-thumb-wrapper img { width: 80px; height: auto; display: block; border:1px solid #ccc; padding:3px; border-radius:3px; }
        .mg-remove-image:hover { background: darkred; }
        .mg-video-wrapper { position: relative; }
        .mg-remove-video:hover { background: darkred; }
    </style>
<?php
}

function mg_save_metabox_portafolio($post_id) {
    if (!isset($_POST['mg_portafolio_nonce']) || !wp_verify_nonce($_POST['mg_portafolio_nonce'], 'mg_portafolio_nonce_action')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, 'mg_portafolio_descripcion', sanitize_textarea_field($_POST['mg_portafolio_descripcion'] ?? ''));
    update_post_meta($post_id, 'mg_portafolio_source', sanitize_textarea_field($_POST['mg_portafolio_source'] ?? ''));

    update_post_meta($post_id, 'mg_portafolio_cliente', intval($_POST['mg_portafolio_cliente'] ?? 0));

    $servicios = isset($_POST['mg_portafolio_servicio']) ? array_map('intval', $_POST['mg_portafolio_servicio']) : [];
    update_post_meta($post_id, 'mg_portafolio_servicio', $servicios);

    $equipo = isset($_POST['mg_portafolio_equipo']) ? array_map('intval', $_POST['mg_portafolio_equipo']) : [];
    update_post_meta($post_id, 'mg_portafolio_equipo', $equipo);

    update_post_meta($post_id, 'mg_portafolio_caso_exito', intval($_POST['mg_portafolio_caso_exito'] ?? 0));
    update_post_meta($post_id, 'mg_portafolio_fecha', sanitize_text_field($_POST['mg_portafolio_fecha'] ?? ''));
        
    // Guardar la galería de IMÁGENES
    if (isset($_POST['mg_portafolio_galeria'])) {
        $galeria_string = sanitize_text_field($_POST['mg_portafolio_galeria']);
        if (!empty($galeria_string)) {
            $galeria = array_filter(array_map('intval', explode(',', $galeria_string)));
            update_post_meta($post_id, 'mg_portafolio_galeria', $galeria);
        } else {
            delete_post_meta($post_id, 'mg_portafolio_galeria');
        }
    }

    // NUEVO: Guardar la galería de VIDEOS
    if (isset($_POST['mg_portafolio_videos'])) {
        $videos_string = sanitize_text_field($_POST['mg_portafolio_videos']);
        if (!empty($videos_string)) {
            $videos = array_filter(array_map('intval', explode(',', $videos_string)));
            update_post_meta($post_id, 'mg_portafolio_videos', $videos);
            error_log('Videos guardados para post ' . $post_id . ': ' . print_r($videos, true));
        } else {
            delete_post_meta($post_id, 'mg_portafolio_videos');
        }
    }

    // NUEVO: Guardar URLs de videos externos
    if (isset($_POST['mg_portafolio_videos_externos'])) {
        $videos_externos = sanitize_textarea_field($_POST['mg_portafolio_videos_externos']);
        update_post_meta($post_id, 'mg_portafolio_videos_externos', $videos_externos);
    }

    // NUEVO: Guardar layout seleccionado
    if (isset($_POST['mg_portafolio_layout'])) {
        $layout = sanitize_text_field($_POST['mg_portafolio_layout']);
        // Validar que sea una opción válida
        if (in_array($layout, ['grid', 'masonry', 'vertical', 'slider'])) {
            update_post_meta($post_id, 'mg_portafolio_layout', $layout);
        }
    }
        // NUEVO: Guardar layout seleccionado para VIDEOS
    if (isset($_POST['mg_portafolio_layout_videos'])) {
        $layout_videos = sanitize_text_field($_POST['mg_portafolio_layout_videos']);
        // Validar que sea una opción válida
        if (in_array($layout_videos, ['grid', 'vertical'])) {
            update_post_meta($post_id, 'mg_portafolio_layout_videos', $layout_videos);
        }
    }

    // PDF Flipbook
    if (isset($_POST['mg_portafolio_pdf'])) {
        $pdf_id = intval($_POST['mg_portafolio_pdf']);
        if ($pdf_id > 0) {
            // Verificar que sea un PDF real
            $mime = get_post_mime_type($pdf_id);
            if ($mime === 'application/pdf') {
                update_post_meta($post_id, 'mg_portafolio_pdf', $pdf_id);
            }
        } else {
            delete_post_meta($post_id, 'mg_portafolio_pdf');
        }
    }

}
add_action('save_post_mg_portafolio', 'mg_save_metabox_portafolio');

/* -------------------------------------------------------
 * Enqueue admin-pdf.js para el uploader de PDF
 * ----------------------------------------------------- */
function mg_portafolio_admin_pdf_scripts($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') return;
    global $post_type;
    if ($post_type !== 'mg_portafolio') return;

    wp_enqueue_media();

    wp_enqueue_script(
        'mg-admin-pdf',
        get_template_directory_uri() . '/assets/js/admin-pdf.js',
        ['jquery', 'media-upload', 'media-views'],
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'mg_portafolio_admin_pdf_scripts');
