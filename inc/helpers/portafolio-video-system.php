<?php
/**
 * ============================================================================
 * MAGGIORE - Portafolio Video System
 * ============================================================================
 * 
 * Sistema completo de galerías con videos para el CPT Portafolio.
 * Incluye: galería de videos, videos externos, layouts, optimizaciones.
 * 
 * @package Maggiore
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

/* -------------------------------------------------------
 * Enqueue Scripts para Galerías (Imágenes + Videos)
 * ----------------------------------------------------- */
function mg_portafolio_admin_scripts($hook) {
    // Solo cargar en páginas de edición
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    
    // Solo para el CPT portafolio
    global $post_type;
    if ($post_type !== 'mg_portafolio') {
        return;
    }
    
    // Media uploader de WordPress
    wp_enqueue_media();
    
    // ===== GALERÍA DE IMÁGENES (ya existe, solo aseguramos que esté cargado) =====
    wp_enqueue_script(
        'mg-admin-gallery',
        get_template_directory_uri() . '/assets/js/admin-gallery.js',
        array('jquery', 'media-upload', 'media-views'),
        '2.0.0',
        true
    );
    wp_enqueue_script(
    'mg-admin-videos-externos',
    get_template_directory_uri() . '/assets/js/admin-videos-externos-simple.js',
    array('jquery'),
    '1.0.0',
    true
);
    // ===== GALERÍA DE VIDEOS (NUEVO) =====
    wp_enqueue_script(
        'mg-admin-gallery-videos',
        get_template_directory_uri() . '/assets/js/admin-gallery-videos.js',
        array('jquery', 'media-upload', 'media-views'),
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'mg_portafolio_admin_scripts');

/* -------------------------------------------------------
 * Aumentar Límite de Upload para Videos
 * ----------------------------------------------------- */
function mg_increase_upload_limit($size) {
    // 100MB en bytes
    return 104857600;
}
add_filter('upload_size_limit', 'mg_increase_upload_limit');

/* -------------------------------------------------------
 * Permitir Tipos de Archivo de Video
 * ----------------------------------------------------- */
function mg_custom_video_mime_types($mimes) {
    // MP4
    $mimes['mp4']  = 'video/mp4';
    $mimes['m4v']  = 'video/mp4';
    
    // WebM
    $mimes['webm'] = 'video/webm';
    
    // OGG
    $mimes['ogv']  = 'video/ogg';
    
    // MOV (QuickTime)
    $mimes['mov']  = 'video/quicktime';
    
    return $mimes;
}
add_filter('upload_mimes', 'mg_custom_video_mime_types');

/* -------------------------------------------------------
 * Agregar Soporte para Thumbnails de Video
 * ----------------------------------------------------- */
function mg_video_thumbnail_support() {
    // Ya debería estar, pero lo aseguramos
    add_theme_support('post-thumbnails');
    
    // Tamaños de imagen para poster frames de videos
    add_image_size('video-poster', 1280, 720, true);
    add_image_size('video-thumb', 640, 360, true);
}
add_action('after_setup_theme', 'mg_video_thumbnail_support', 20);

/* -------------------------------------------------------
 * Limpiar Metadata de Videos al Eliminar Proyecto
 * ----------------------------------------------------- */
function mg_cleanup_portafolio_meta($post_id) {
    $post_type = get_post_type($post_id);
    
    if ($post_type === 'mg_portafolio') {
        delete_post_meta($post_id, 'mg_portafolio_galeria');
        delete_post_meta($post_id, 'mg_portafolio_videos');
        delete_post_meta($post_id, 'mg_portafolio_videos_externos');
        delete_post_meta($post_id, 'mg_portafolio_layout');
    }
}
add_action('before_delete_post', 'mg_cleanup_portafolio_meta');

/* -------------------------------------------------------
 * Agregar Columnas Custom en Listado de Portafolio
 * ----------------------------------------------------- */
function mg_portafolio_custom_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        // Agregar después del título
        if ($key === 'title') {
            $new_columns['layout'] = '📐 Layout';
            $new_columns['media_count'] = '📊 Media';
        }
    }
    
    return $new_columns;
}
add_filter('manage_mg_portafolio_posts_columns', 'mg_portafolio_custom_columns');

/* -------------------------------------------------------
 * Mostrar Contenido de Columnas Custom
 * ----------------------------------------------------- */
function mg_portafolio_column_content($column, $post_id) {
    switch ($column) {
        case 'layout':
            $layout = get_post_meta($post_id, 'mg_portafolio_layout', true) ?: 'grid';
            $icons = array(
                'grid'     => '🔲 Grid',
                'masonry'  => '🧱 Masonry',
                'vertical' => '⬇️ Vertical',
                'slider'   => '➡️ Slider'
            );
            echo $icons[$layout] ?? $layout;
            break;
            
        case 'media_count':
            $images = get_post_meta($post_id, 'mg_portafolio_galeria', true);
            $videos = get_post_meta($post_id, 'mg_portafolio_videos', true);
            $externos = get_post_meta($post_id, 'mg_portafolio_videos_externos', true);
            
            $img_count = is_array($images) ? count($images) : 0;
            $vid_count = is_array($videos) ? count($videos) : 0;
            $ext_count = !empty($externos) ? count(array_filter(explode("\n", $externos))) : 0;
            
            $parts = array();
            if ($img_count > 0) $parts[] = "📸 {$img_count}";
            if ($vid_count > 0) $parts[] = "🎬 {$vid_count}";
            if ($ext_count > 0) $parts[] = "🔗 {$ext_count}";
            
            echo !empty($parts) ? implode(' | ', $parts) : '—';
            break;
    }
}
add_action('manage_mg_portafolio_posts_custom_column', 'mg_portafolio_column_content', 10, 2);

/* -------------------------------------------------------
 * Hacer Columnas Ordenables
 * ----------------------------------------------------- */
function mg_portafolio_sortable_columns($columns) {
    $columns['layout'] = 'layout';
    return $columns;
}
add_filter('manage_edit-mg_portafolio_sortable_columns', 'mg_portafolio_sortable_columns');

/* -------------------------------------------------------
 * Frontend: Scripts y Estilos para Portafolio Single
 * ----------------------------------------------------- */
function mg_portafolio_frontend_scripts() {
    if (is_singular('mg_portafolio')) {
        // GLightbox para lightbox/modal (opcional pero recomendado)
        wp_enqueue_script(
            'glightbox',
            'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js',
            array(),
            '3.2.0',
            true
        );
        
        wp_enqueue_style(
            'glightbox',
            'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css',
            array(),
            '3.2.0'
        );
        
        // Script de inicialización del lightbox
        wp_add_inline_script('glightbox', '
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof GLightbox !== "undefined") {
                    const lightbox = GLightbox({
                        selector: "[data-lightbox]",
                        touchNavigation: true,
                        loop: true,
                        autoplayVideos: true
                    });
                }
            });
        ');
    }
}
add_action('wp_enqueue_scripts', 'mg_portafolio_frontend_scripts');

/* -------------------------------------------------------
 * Helper: Obtener Embed Code de Videos Externos
 * ----------------------------------------------------- */
function mg_get_video_embed($url) {
    $url = trim($url);
    
    // YouTube
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
        return '<iframe src="https://www.youtube.com/embed/' . $matches[1] . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    }
    
    // Vimeo
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
        return '<iframe src="https://player.vimeo.com/video/' . $matches[1] . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
    }
    
    return false;
}

/* -------------------------------------------------------
 * Helper: Obtener URL de Embed de Videos Externos
 * ----------------------------------------------------- */
function mg_get_video_embed_url($url) {
    $url = trim($url);
    
    // YouTube
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    
    // Vimeo
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
        return 'https://player.vimeo.com/video/' . $matches[1];
    }
    
    return false;
}

/* -------------------------------------------------------
 * Mostrar Aviso si Límite de Upload es Bajo
 * ----------------------------------------------------- */
function mg_upload_limit_admin_notice() {
    $screen = get_current_screen();
    
    if ($screen && $screen->post_type === 'mg_portafolio') {
        $upload_max = wp_max_upload_size();
        $upload_max_mb = $upload_max / (1024 * 1024);
        
        if ($upload_max_mb < 50) {
            ?>
            <div class="notice notice-warning">
                <p>
                    <strong>⚠️ Límite de subida bajo:</strong> 
                    El límite actual es <?= round($upload_max_mb, 1); ?>MB. 
                    Para subir videos, se recomienda al menos 100MB.
                    <a href="https://wordpress.org/support/article/common-wordpress-errors/#exceeds-the-maximum-upload-size" target="_blank">¿Cómo aumentarlo?</a>
                </p>
            </div>
            <?php
        }
    }
}
add_action('admin_notices', 'mg_upload_limit_admin_notice');

/* -------------------------------------------------------
 * OPCIONAL: Comprimir Videos al Subirlos
 * (Requiere FFmpeg instalado en el servidor)
 * Descomentar solo si tienes FFmpeg
 * ----------------------------------------------------- */
/*
function mg_compress_video_on_upload($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    $filetype = wp_check_filetype($file);
    
    // Solo procesar videos
    if (strpos($filetype['type'], 'video/') !== 0) {
        return $metadata;
    }
    
    // Verificar que FFmpeg esté disponible
    $ffmpeg_path = exec('which ffmpeg');
    if (empty($ffmpeg_path)) {
        return $metadata;
    }
    
    // Comando FFmpeg para comprimir
    $output = str_replace('.mp4', '-compressed.mp4', $file);
    $command = "ffmpeg -i {$file} -vcodec h264 -acodec aac -strict -2 -crf 23 {$output}";
    
    exec($command, $output_array, $return_var);
    
    if ($return_var === 0 && file_exists($output)) {
        // Reemplazar archivo original con el comprimido
        unlink($file);
        rename($output, $file);
        
        // Regenerar metadata
        $metadata = wp_generate_attachment_metadata($attachment_id, $file);
    }
    
    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'mg_compress_video_on_upload', 10, 2);
*/

/* -------------------------------------------------------
 * OPCIONAL: Generar Thumbnail de Video Automáticamente
 * (Requiere FFmpeg instalado en el servidor)
 * Descomentar solo si tienes FFmpeg
 * ----------------------------------------------------- */
/*
function mg_generate_video_thumbnail($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    $filetype = wp_check_filetype($file);
    
    // Solo procesar videos
    if (strpos($filetype['type'], 'video/') !== 0) {
        return $metadata;
    }
    
    // Verificar que FFmpeg esté disponible
    $ffmpeg_path = exec('which ffmpeg');
    if (empty($ffmpeg_path)) {
        return $metadata;
    }
    
    $upload_dir = wp_upload_dir();
    $thumb_path = $upload_dir['path'] . '/' . basename($file, '.mp4') . '-thumb.jpg';
    
    // Extraer frame del segundo 1
    $command = "ffmpeg -i {$file} -ss 00:00:01 -vframes 1 {$thumb_path}";
    
    exec($command, $output_array, $return_var);
    
    if ($return_var === 0 && file_exists($thumb_path)) {
        // Crear attachment para el thumbnail
        $thumb_id = wp_insert_attachment(array(
            'post_mime_type' => 'image/jpeg',
            'post_title' => basename($thumb_path),
            'post_content' => '',
            'post_status' => 'inherit'
        ), $thumb_path);
        
        // Generar metadata del thumbnail
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $thumb_data = wp_generate_attachment_metadata($thumb_id, $thumb_path);
        wp_update_attachment_metadata($thumb_id, $thumb_data);
        
        // Asociar thumbnail con el video
        set_post_thumbnail($attachment_id, $thumb_id);
    }
    
    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'mg_generate_video_thumbnail', 10, 2);
*/

/* -------------------------------------------------------
 * Debug: Log de Videos Guardados
 * ----------------------------------------------------- */
function mg_log_video_save($post_id) {
    if (get_post_type($post_id) !== 'mg_portafolio') {
        return;
    }
    
    // Solo si WP_DEBUG está activo
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $videos = get_post_meta($post_id, 'mg_portafolio_videos', true);
        $externos = get_post_meta($post_id, 'mg_portafolio_videos_externos', true);
        
        error_log('Portafolio ' . $post_id . ' - Videos: ' . print_r($videos, true));
        error_log('Portafolio ' . $post_id . ' - Externos: ' . $externos);
    }
}
add_action('save_post_mg_portafolio', 'mg_log_video_save', 20);

/* -------------------------------------------------------
 * Fin del archivo
 * ----------------------------------------------------- */
