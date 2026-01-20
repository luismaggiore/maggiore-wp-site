<?php
if (!defined('ABSPATH')) exit;

/**
 * Página de Administración: Tracking (Meta Pixel & Google Tag Manager)
 */

// Agregar menú en el admin
add_action('admin_menu', function () {
    add_menu_page(
        __('Tracking & Analytics', 'maggiore'),           // Page title
        __('Tracking', 'maggiore'),                       // Menu title
        'manage_options',                                  // Capability
        'mg-tracking-settings',                            // Menu slug
        'mg_render_tracking_settings_page',                // Callback
        'dashicons-chart-line',                            // Icon
        85                                                 // Position (después de Settings)
    );
});

/**
 * Renderizar la página de configuración
 */
function mg_render_tracking_settings_page() {
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes permisos para acceder a esta página.', 'maggiore'));
    }

    // Guardar configuración
    if (isset($_POST['mg_tracking_submit']) && check_admin_referer('mg_tracking_settings', 'mg_tracking_nonce')) {
        
        // Meta Pixel
        $meta_pixel_id = sanitize_text_field($_POST['mg_meta_pixel_id'] ?? '');
        $meta_pixel_enabled = isset($_POST['mg_meta_pixel_enabled']) ? '1' : '0';
        
        update_option('mg_meta_pixel_id', $meta_pixel_id);
        update_option('mg_meta_pixel_enabled', $meta_pixel_enabled);
        
        // Google Tag Manager
        $gtm_id = sanitize_text_field($_POST['mg_gtm_id'] ?? '');
        $gtm_enabled = isset($_POST['mg_gtm_enabled']) ? '1' : '0';
        
        update_option('mg_gtm_id', $gtm_id);
        update_option('mg_gtm_enabled', $gtm_enabled);
        
        // Google Analytics 4 (opcional adicional)
        $ga4_id = sanitize_text_field($_POST['mg_ga4_id'] ?? '');
        $ga4_enabled = isset($_POST['mg_ga4_enabled']) ? '1' : '0';
        
        update_option('mg_ga4_id', $ga4_id);
        update_option('mg_ga4_enabled', $ga4_enabled);
        
        echo '<div class="notice notice-success is-dismissible"><p><strong>' . __('Configuración guardada correctamente.', 'maggiore') . '</strong></p></div>';
    }

    // Obtener valores actuales
    $meta_pixel_id = get_option('mg_meta_pixel_id', '');
    $meta_pixel_enabled = get_option('mg_meta_pixel_enabled', '0');
    
    $gtm_id = get_option('mg_gtm_id', '');
    $gtm_enabled = get_option('mg_gtm_enabled', '0');
    
    $ga4_id = get_option('mg_ga4_id', '');
    $ga4_enabled = get_option('mg_ga4_enabled', '0');
    ?>

    <div class="wrap">
        <h1><?php _e('Tracking & Analytics', 'maggiore'); ?></h1>
        
        <p class="description">
            <?php _e('Configura los códigos de seguimiento y analytics para tu sitio web. Estos códigos se insertarán automáticamente en todas las páginas.', 'maggiore'); ?>
        </p>

        <hr style="margin: 30px 0;">

        <form method="post" action="">
            <?php wp_nonce_field('mg_tracking_settings', 'mg_tracking_nonce'); ?>

            <!-- ============================ -->
            <!-- META PIXEL (FACEBOOK)        -->
            <!-- ============================ -->
            <div class="mg-tracking-section" style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 25px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                        <span style="background: #1877f2; color: white; width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold;">f</span>
                        <?php _e('Meta Pixel (Facebook)', 'maggiore'); ?>
                    </h2>
                    <label class="mg-toggle-switch">
                        <input type="checkbox" name="mg_meta_pixel_enabled" value="1" <?= checked($meta_pixel_enabled, '1', false); ?>>
                        <span class="mg-toggle-slider"></span>
                    </label>
                </div>

                <div class="mg-tracking-fields">
                    <p>
                        <label><strong><?php _e('Meta Pixel ID', 'maggiore'); ?></strong></label>
                        <input type="text" 
                               class="regular-text" 
                               name="mg_meta_pixel_id" 
                               value="<?= esc_attr($meta_pixel_id); ?>" 
                               placeholder="123456789012345">
                        <small class="description" style="display: block; margin-top: 5px;">
                            <?php _e('Ejemplo: 123456789012345 (15 dígitos)', 'maggiore'); ?>
                        </small>
                    </p>

                    <div style="background: #f0f9ff; border-left: 4px solid #0284c7; padding: 15px; margin-top: 15px;">
                        <p style="margin: 0; font-size: 14px;">
                            <strong>📍 Dónde encontrar tu Pixel ID:</strong><br>
                            1. Ingresa a <a href="https://business.facebook.com/events_manager" target="_blank">Facebook Events Manager</a><br>
                            2. Selecciona tu Pixel<br>
                            3. Copia el ID de 15 dígitos
                        </p>
                    </div>

                    <?php if ($meta_pixel_enabled && $meta_pixel_id): ?>
                    <div style="background: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; margin-top: 15px;">
                        <p style="margin: 0; font-size: 14px;">
                            ✅ <strong>Meta Pixel activo</strong><br>
                            El pixel se está cargando en todas las páginas del sitio.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ============================ -->
            <!-- GOOGLE TAG MANAGER           -->
            <!-- ============================ -->
            <div class="mg-tracking-section" style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 25px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                        <span style="background: #4285f4; color: white; width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold;">G</span>
                        <?php _e('Google Tag Manager', 'maggiore'); ?>
                    </h2>
                    <label class="mg-toggle-switch">
                        <input type="checkbox" name="mg_gtm_enabled" value="1" <?= checked($gtm_enabled, '1', false); ?>>
                        <span class="mg-toggle-slider"></span>
                    </label>
                </div>

                <div class="mg-tracking-fields">
                    <p>
                        <label><strong><?php _e('Container ID (GTM)', 'maggiore'); ?></strong></label>
                        <input type="text" 
                               class="regular-text" 
                               name="mg_gtm_id" 
                               value="<?= esc_attr($gtm_id); ?>" 
                               placeholder="GTM-XXXXXXX">
                        <small class="description" style="display: block; margin-top: 5px;">
                            <?php _e('Ejemplo: GTM-XXXXXXX', 'maggiore'); ?>
                        </small>
                    </p>

                    <div style="background: #f0f9ff; border-left: 4px solid #0284c7; padding: 15px; margin-top: 15px;">
                        <p style="margin: 0; font-size: 14px;">
                            <strong>📍 Dónde encontrar tu Container ID:</strong><br>
                            1. Ingresa a <a href="https://tagmanager.google.com" target="_blank">Google Tag Manager</a><br>
                            2. Selecciona tu contenedor<br>
                            3. Copia el ID que comienza con "GTM-"
                        </p>
                    </div>

                    <?php if ($gtm_enabled && $gtm_id): ?>
                    <div style="background: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; margin-top: 15px;">
                        <p style="margin: 0; font-size: 14px;">
                            ✅ <strong>Google Tag Manager activo</strong><br>
                            El contenedor se está cargando en todas las páginas del sitio.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ============================ -->
            <!-- GOOGLE ANALYTICS 4           -->
            <!-- ============================ -->
            <div class="mg-tracking-section" style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 25px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                        <span style="background: #e37400; color: white; width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold;">GA</span>
                        <?php _e('Google Analytics 4', 'maggiore'); ?>
                    </h2>
                    <label class="mg-toggle-switch">
                        <input type="checkbox" name="mg_ga4_enabled" value="1" <?= checked($ga4_enabled, '1', false); ?>>
                        <span class="mg-toggle-slider"></span>
                    </label>
                </div>

                <div class="mg-tracking-fields">
                    <p>
                        <label><strong><?php _e('Measurement ID (GA4)', 'maggiore'); ?></strong></label>
                        <input type="text" 
                               class="regular-text" 
                               name="mg_ga4_id" 
                               value="<?= esc_attr($ga4_id); ?>" 
                               placeholder="G-XXXXXXXXXX">
                        <small class="description" style="display: block; margin-top: 5px;">
                            <?php _e('Ejemplo: G-XXXXXXXXXX', 'maggiore'); ?>
                        </small>
                    </p>

                    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 15px;">
                        <p style="margin: 0; font-size: 14px;">
                            <strong>⚠️ Nota:</strong> Si estás usando Google Tag Manager, te recomendamos configurar GA4 desde allí en lugar de usar este campo directo. Esto evitará duplicar el tracking.
                        </p>
                    </div>

                    <div style="background: #f0f9ff; border-left: 4px solid #0284c7; padding: 15px; margin-top: 15px;">
                        <p style="margin: 0; font-size: 14px;">
                            <strong>📍 Dónde encontrar tu Measurement ID:</strong><br>
                            1. Ingresa a <a href="https://analytics.google.com" target="_blank">Google Analytics</a><br>
                            2. Ve a Admin → Data Streams<br>
                            3. Copia el Measurement ID que comienza con "G-"
                        </p>
                    </div>

                    <?php if ($ga4_enabled && $ga4_id): ?>
                    <div style="background: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; margin-top: 15px;">
                        <p style="margin: 0; font-size: 14px;">
                            ✅ <strong>Google Analytics 4 activo</strong><br>
                            GA4 se está cargando en todas las páginas del sitio.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Botón Guardar -->
            <p class="submit">
                <button type="submit" name="mg_tracking_submit" class="button button-primary button-large">
                    <?php _e('Guardar Configuración', 'maggiore'); ?>
                </button>
            </p>
        </form>

        <!-- Documentación adicional -->
        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 25px; margin-top: 30px;">
            <h3><?php _e('📚 Documentación', 'maggiore'); ?></h3>
            <p><?php _e('Aprende más sobre cada herramienta:', 'maggiore'); ?></p>
            <ul>
                <li><a href="https://www.facebook.com/business/help/952192354843755" target="_blank">Meta Pixel - Guía oficial</a></li>
                <li><a href="https://support.google.com/tagmanager/answer/6103696" target="_blank">Google Tag Manager - Guía oficial</a></li>
                <li><a href="https://support.google.com/analytics/answer/9304153" target="_blank">Google Analytics 4 - Guía oficial</a></li>
            </ul>
        </div>
    </div>

    <style>
    .mg-toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    .mg-toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .mg-toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }
    .mg-toggle-slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    .mg-toggle-switch input:checked + .mg-toggle-slider {
        background-color: #10b981;
    }
    .mg-toggle-switch input:checked + .mg-toggle-slider:before {
        transform: translateX(26px);
    }
    .mg-tracking-section {
        transition: all 0.3s ease;
    }
    .mg-tracking-section:hover {
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
    </style>
<?php
}

/**
 * Insertar Meta Pixel en el head
 */
add_action('wp_head', function () {
    $enabled = get_option('mg_meta_pixel_enabled', '0');
    $pixel_id = get_option('mg_meta_pixel_id', '');
    
    if ($enabled === '1' && !empty($pixel_id)) {
        ?>
        <!-- Meta Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?= esc_js($pixel_id); ?>');
        fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=<?= esc_attr($pixel_id); ?>&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Meta Pixel Code -->
        <?php
    }
}, 10);

/**
 * Insertar Google Tag Manager en el head
 */
add_action('wp_head', function () {
    $enabled = get_option('mg_gtm_enabled', '0');
    $gtm_id = get_option('mg_gtm_id', '');
    
    if ($enabled === '1' && !empty($gtm_id)) {
        ?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?= esc_js($gtm_id); ?>');</script>
        <!-- End Google Tag Manager -->
        <?php
    }
}, 10);

/**
 * Insertar Google Tag Manager noscript en el body
 */
add_action('wp_body_open', function () {
    $enabled = get_option('mg_gtm_enabled', '0');
    $gtm_id = get_option('mg_gtm_id', '');
    
    if ($enabled === '1' && !empty($gtm_id)) {
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= esc_attr($gtm_id); ?>"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }
}, 1);

/**
 * Insertar Google Analytics 4 en el head
 */
add_action('wp_head', function () {
    $enabled = get_option('mg_ga4_enabled', '0');
    $ga4_id = get_option('mg_ga4_id', '');
    
    if ($enabled === '1' && !empty($ga4_id)) {
        ?>
        <!-- Google Analytics 4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc_attr($ga4_id); ?>"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= esc_js($ga4_id); ?>');
        </script>
        <!-- End Google Analytics 4 -->
        <?php
    }
}, 10);
