<?php
if (!defined('ABSPATH')) exit;

/**
 * WhatsApp Floating Button — Tracking Completo
 *
 * Funcionalidades:
 *  - Botón flotante configurable (posición, tooltip, mensaje)
 *  - Disparo de evento GA4  (opcional)
 *  - Disparo de evento Meta Pixel (opcional)
 *  - Guardado de click en CPT mg_wa_click (opcional)
 *  - Panel de admin bajo Tracking & Analytics
 *
 * @package Maggiore
 */

// =============================================================================
// 1. CUSTOM POST TYPE — mg_wa_click
// =============================================================================

add_action('init', function () {
    register_post_type('mg_wa_click', [
        'label'               => __('Clicks WhatsApp', 'maggiore'),
        'labels'              => [
            'name'               => __('Clicks WhatsApp', 'maggiore'),
            'singular_name'      => __('Click WhatsApp', 'maggiore'),
            'menu_name'          => __('Clicks WA', 'maggiore'),
            'all_items'          => __('Todos los Clicks', 'maggiore'),
            'view_item'          => __('Ver Click', 'maggiore'),
            'search_items'       => __('Buscar Clicks', 'maggiore'),
            'not_found'          => __('No se encontraron clicks.', 'maggiore'),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => false, // Lo mostramos bajo nuestro submenú
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'supports'            => ['title'],
        'capability_type'     => 'post',
        'capabilities'        => [
            'create_posts'    => 'do_not_allow', // Solo lectura desde el admin
        ],
        'map_meta_cap'        => true,
    ]);
});


// =============================================================================
// 2. SUBMENÚ BAJO "TRACKING & ANALYTICS"
// =============================================================================

add_action('admin_menu', function () {

    // --- Submenú: Configuración del botón flotante ---
    add_submenu_page(
        'mg-tracking-settings',                    // Parent slug
        __('Botón WhatsApp Flotante', 'maggiore'),
        __('WhatsApp Flotante', 'maggiore'),
        'manage_options',
        'mg-whatsapp-float',
        'mg_render_whatsapp_float_settings'
    );

    // --- Submenú: Historial de clicks ---
    add_submenu_page(
        'mg-tracking-settings',
        __('Clicks WhatsApp', 'maggiore'),
        __('Clicks WA', 'maggiore'),
        'manage_options',
        'edit.php?post_type=mg_wa_click'
    );
});


// =============================================================================
// 3. PANEL DE CONFIGURACIÓN
// =============================================================================

function mg_render_whatsapp_float_settings() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Sin permisos.', 'maggiore'));
    }

    // --- Guardar ---
    if (isset($_POST['mg_wa_float_submit']) && check_admin_referer('mg_wa_float_settings', 'mg_wa_float_nonce')) {
        $fields = [
            'mg_wa_float_enabled'         => 'checkbox',
            'mg_wa_float_position'        => 'select',
            'mg_wa_float_tooltip'         => 'text',
            'mg_wa_float_message'         => 'textarea',
            'mg_wa_float_ga4_enabled'     => 'checkbox',
            'mg_wa_float_ga4_event'       => 'text',
            'mg_wa_float_pixel_enabled'   => 'checkbox',
            'mg_wa_float_pixel_event'     => 'text',
            'mg_wa_float_cpt_enabled'     => 'checkbox',
        ];

        foreach ($fields as $key => $type) {
            if ($type === 'checkbox') {
                update_option($key, isset($_POST[$key]) ? '1' : '0');
            } elseif ($type === 'textarea') {
                update_option($key, sanitize_textarea_field($_POST[$key] ?? ''));
            } else {
                update_option($key, sanitize_text_field($_POST[$key] ?? ''));
            }
        }

        echo '<div class="notice notice-success is-dismissible"><p><strong>'
            . __('Configuración guardada.', 'maggiore')
            . '</strong></p></div>';
    }

    // --- Leer opciones ---
    $enabled       = get_option('mg_wa_float_enabled', '1');
    $position      = get_option('mg_wa_float_position', 'bottom-right');
    $tooltip       = get_option('mg_wa_float_tooltip', '¡Escríbenos!');
    $message       = get_option('mg_wa_float_message', 'Hola, me gustaría obtener más información.');
    $ga4_enabled   = get_option('mg_wa_float_ga4_enabled', '1');
    $ga4_event     = get_option('mg_wa_float_ga4_event', 'whatsapp_click');
    $pixel_enabled = get_option('mg_wa_float_pixel_enabled', '1');
    $pixel_event   = get_option('mg_wa_float_pixel_event', 'WhatsAppClick');
    $cpt_enabled   = get_option('mg_wa_float_cpt_enabled', '1');

    // Número de WhatsApp configurado en Settings
    $whatsapp_num  = get_option('maggiore_whatsapp', '');

    // Total de clicks registrados
    $total_clicks  = wp_count_posts('mg_wa_click')->publish ?? 0;

    ?>
    <div class="wrap">
        <h1><?php _e('Botón WhatsApp Flotante', 'maggiore'); ?></h1>
        <p class="description">
            <?php _e('Configura el botón flotante de WhatsApp y el tracking asociado. El número de WhatsApp se toma de', 'maggiore'); ?>
            <a href="<?= admin_url('admin.php?page=mg-settings'); ?>">
                <?php _e('Configuración General', 'maggiore'); ?>
            </a>.
        </p>

        <?php if (empty($whatsapp_num)): ?>
            <div class="notice notice-warning">
                <p>
                    ⚠️ <strong><?php _e('No hay número de WhatsApp configurado.', 'maggiore'); ?></strong>
                    <?php _e('Agrega uno en', 'maggiore'); ?>
                    <a href="<?= admin_url('admin.php?page=mg-settings'); ?>">
                        <?php _e('Configuración General', 'maggiore'); ?>
                    </a>.
                </p>
            </div>
        <?php else: ?>
            <div class="notice notice-info" style="border-left-color:#25D366">
                <p>
                    📱 <?php _e('Número activo:', 'maggiore'); ?>
                    <strong><?= esc_html($whatsapp_num); ?></strong>
                </p>
            </div>
        <?php endif; ?>

        <!-- Tarjeta de estadística rápida -->
        <div style="display:flex; gap:20px; margin: 20px 0;">
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:20px 30px; text-align:center;">
                <div style="font-size:36px; font-weight:700; color:#25D366;"><?= number_format((int)$total_clicks); ?></div>
                <div style="font-size:13px; color:#718096; margin-top:4px;"><?php _e('Clicks registrados en BD', 'maggiore'); ?></div>
                <a href="<?= admin_url('edit.php?post_type=mg_wa_click'); ?>" style="font-size:12px;">
                    <?php _e('Ver historial →', 'maggiore'); ?>
                </a>
            </div>
        </div>

        <form method="post">
            <?php wp_nonce_field('mg_wa_float_settings', 'mg_wa_float_nonce'); ?>

            <!-- ============================================================
                 SECCIÓN: BOTÓN
            ============================================================= -->
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:25px; margin-bottom:25px;">
                <h2 style="margin-top:0; border-bottom:1px solid #f0f0f0; padding-bottom:12px;">
                    💬 <?php _e('Configuración del Botón', 'maggiore'); ?>
                </h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Estado', 'maggiore'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="mg_wa_float_enabled" value="1" <?php checked($enabled, '1'); ?>>
                                <?php _e('Mostrar botón flotante en el sitio', 'maggiore'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mg_wa_float_position"><?php _e('Posición', 'maggiore'); ?></label></th>
                        <td>
                            <select name="mg_wa_float_position" id="mg_wa_float_position">
                                <option value="bottom-right" <?php selected($position, 'bottom-right'); ?>><?php _e('Inferior derecha', 'maggiore'); ?></option>
                                <option value="bottom-left"  <?php selected($position, 'bottom-left');  ?>><?php _e('Inferior izquierda', 'maggiore'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mg_wa_float_tooltip"><?php _e('Tooltip', 'maggiore'); ?></label></th>
                        <td>
                            <input type="text" id="mg_wa_float_tooltip" name="mg_wa_float_tooltip"
                                   value="<?= esc_attr($tooltip); ?>" class="regular-text">
                            <p class="description"><?php _e('Texto que aparece al pasar el cursor. Dejar vacío para ocultar.', 'maggiore'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mg_wa_float_message"><?php _e('Mensaje predeterminado', 'maggiore'); ?></label></th>
                        <td>
                            <textarea id="mg_wa_float_message" name="mg_wa_float_message"
                                      class="large-text" rows="3"><?= esc_textarea($message); ?></textarea>
                            <p class="description">
                                <?php _e('Texto pre-cargado en WhatsApp al hacer click. Puedes usar', 'maggiore'); ?>
                                <code>{page_title}</code> <?php _e('y', 'maggiore'); ?> <code>{page_url}</code>
                                <?php _e('como variables dinámicas.', 'maggiore'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ============================================================
                 SECCIÓN: TRACKING
            ============================================================= -->
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:25px; margin-bottom:25px;">
                <h2 style="margin-top:0; border-bottom:1px solid #f0f0f0; padding-bottom:12px;">
                    📊 <?php _e('Tracking & Conversiones', 'maggiore'); ?>
                </h2>

                <table class="form-table">

                    <!-- GA4 -->
                    <tr>
                        <th scope="row"><?php _e('Google Analytics 4', 'maggiore'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="mg_wa_float_ga4_enabled" value="1" <?php checked($ga4_enabled, '1'); ?>>
                                <?php _e('Disparar evento en GA4 al hacer click', 'maggiore'); ?>
                            </label>
                            <?php
                            $ga4_activo = get_option('mg_ga4_enabled', '0') === '1' && !empty(get_option('mg_ga4_id', ''));
                            if (!$ga4_activo): ?>
                                <p class="description" style="color:#e53e3e;">
                                    ⚠️ <?php _e('GA4 no está activo. Actívalo en', 'maggiore'); ?>
                                    <a href="<?= admin_url('admin.php?page=mg-tracking-settings'); ?>">Tracking & Analytics</a>.
                                </p>
                            <?php else: ?>
                                <p class="description" style="color:#38a169;">
                                    ✅ GA4 activo (<?= esc_html(get_option('mg_ga4_id')); ?>)
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mg_wa_float_ga4_event"><?php _e('Nombre del evento GA4', 'maggiore'); ?></label></th>
                        <td>
                            <input type="text" id="mg_wa_float_ga4_event" name="mg_wa_float_ga4_event"
                                   value="<?= esc_attr($ga4_event); ?>" class="regular-text">
                            <p class="description">
                                <?php _e('Nombre del evento que verás en GA4 → Informes → Eventos. Ej:', 'maggiore'); ?>
                                <code>whatsapp_click</code>, <code>contact_whatsapp</code>
                            </p>
                        </td>
                    </tr>

                    <tr><td colspan="2"><hr></td></tr>

                    <!-- Meta Pixel -->
                    <tr>
                        <th scope="row"><?php _e('Meta Pixel', 'maggiore'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="mg_wa_float_pixel_enabled" value="1" <?php checked($pixel_enabled, '1'); ?>>
                                <?php _e('Disparar evento en Meta Pixel al hacer click', 'maggiore'); ?>
                            </label>
                            <?php
                            $pixel_activo = get_option('mg_meta_pixel_enabled', '0') === '1' && !empty(get_option('mg_meta_pixel_id', ''));
                            if (!$pixel_activo): ?>
                                <p class="description" style="color:#e53e3e;">
                                    ⚠️ <?php _e('Meta Pixel no está activo. Actívalo en', 'maggiore'); ?>
                                    <a href="<?= admin_url('admin.php?page=mg-tracking-settings'); ?>">Tracking & Analytics</a>.
                                </p>
                            <?php else: ?>
                                <p class="description" style="color:#38a169;">
                                    ✅ Meta Pixel activo (<?= esc_html(get_option('mg_meta_pixel_id')); ?>)
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mg_wa_float_pixel_event"><?php _e('Nombre del evento Pixel', 'maggiore'); ?></label></th>
                        <td>
                            <input type="text" id="mg_wa_float_pixel_event" name="mg_wa_float_pixel_event"
                                   value="<?= esc_attr($pixel_event); ?>" class="regular-text">
                            <p class="description">
                                <?php _e('Nombre del evento customizado. Ej:', 'maggiore'); ?>
                                <code>WhatsAppClick</code>, <code>ContactWhatsApp</code>
                            </p>
                        </td>
                    </tr>

                    <tr><td colspan="2"><hr></td></tr>

                    <!-- CPT -->
                    <tr>
                        <th scope="row"><?php _e('Base de Datos Interna', 'maggiore'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="mg_wa_float_cpt_enabled" value="1" <?php checked($cpt_enabled, '1'); ?>>
                                <?php _e('Guardar cada click en la base de datos del sitio', 'maggiore'); ?>
                            </label>
                            <p class="description">
                                <?php _e('Registra: página origen, título, dispositivo, UTMs (source, medium, campaign), referrer y timestamp. Visible en', 'maggiore'); ?>
                                <a href="<?= admin_url('edit.php?post_type=mg_wa_click'); ?>">
                                    <?php _e('Clicks WA', 'maggiore'); ?>
                                </a>.
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="submit">
                <button type="submit" name="mg_wa_float_submit" class="button button-primary button-large">
                    <?php _e('Guardar Configuración', 'maggiore'); ?>
                </button>
            </p>

        </form>

        <!-- Guía rápida -->
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:20px; margin-top:10px;">
            <h3 style="margin-top:0; color:#166534;">📚 <?php _e('¿Cómo se guardan los datos?', 'maggiore'); ?></h3>
            <p style="margin:0; color:#166534; font-size:14px;">
                <?php _e('Cada click dispara una petición AJAX silenciosa al servidor. Los datos guardados incluyen: URL exacta de la página, título de la página, dispositivo (mobile/tablet/desktop), sistema operativo aproximado, UTM source/medium/campaign (si el usuario llegó de una campaña), referrer HTTP, y timestamp con zona horaria.', 'maggiore'); ?>
            </p>
            <p style="margin:10px 0 0; color:#166534; font-size:14px;">
                <?php _e('El usuario es redirigido a WhatsApp inmediatamente. El guardado ocurre en paralelo sin bloquear la experiencia.', 'maggiore'); ?>
            </p>
        </div>
    </div>
    <?php
}


// =============================================================================
// 4. COLUMNAS PERSONALIZADAS EN EL LISTADO DE CLICKS
// =============================================================================

add_filter('manage_mg_wa_click_posts_columns', function ($columns) {
    return [
        'cb'              => $columns['cb'],
        'title'           => __('Página', 'maggiore'),
        'wa_device'       => __('Dispositivo', 'maggiore'),
        'wa_utm_source'   => __('Fuente (UTM)', 'maggiore'),
        'wa_utm_campaign' => __('Campaña (UTM)', 'maggiore'),
        'wa_referrer'     => __('Referrer', 'maggiore'),
        'date'            => __('Fecha', 'maggiore'),
    ];
});

add_action('manage_mg_wa_click_posts_custom_column', function ($column, $post_id) {
    switch ($column) {
        case 'wa_device':
            $device = get_post_meta($post_id, '_wa_device', true);
            $icon   = $device === 'mobile' ? '📱' : ($device === 'tablet' ? '🖥' : '💻');
            echo $icon . ' ' . esc_html(ucfirst($device ?: '—'));
            break;
        case 'wa_utm_source':
            $val = get_post_meta($post_id, '_wa_utm_source', true);
            echo $val ? '<code>' . esc_html($val) . '</code>' : '—';
            break;
        case 'wa_utm_campaign':
            $val = get_post_meta($post_id, '_wa_utm_campaign', true);
            echo $val ? '<code>' . esc_html($val) . '</code>' : '—';
            break;
        case 'wa_referrer':
            $val = get_post_meta($post_id, '_wa_referrer', true);
            if ($val) {
                $host = parse_url($val, PHP_URL_HOST);
                echo '<span title="' . esc_attr($val) . '">' . esc_html($host ?: $val) . '</span>';
            } else {
                echo '—';
            }
            break;
    }
}, 10, 2);

add_filter('manage_edit-mg_wa_click_sortable_columns', function ($columns) {
    $columns['wa_device']     = 'wa_device';
    $columns['wa_utm_source'] = 'wa_utm_source';
    return $columns;
});


// =============================================================================
// 5. METABOX DE DETALLE EN EL CLICK INDIVIDUAL
// =============================================================================

add_action('add_meta_boxes', function () {
    add_meta_box(
        'mg_wa_click_detail',
        __('Detalle del Click', 'maggiore'),
        'mg_render_wa_click_metabox',
        'mg_wa_click',
        'normal',
        'high'
    );
});

function mg_render_wa_click_metabox($post) {
    $fields = [
        '_wa_page_url'      => __('URL de la página', 'maggiore'),
        '_wa_page_title'    => __('Título de la página', 'maggiore'),
        '_wa_device'        => __('Dispositivo', 'maggiore'),
        '_wa_os'            => __('Sistema Operativo', 'maggiore'),
        '_wa_utm_source'    => __('UTM Source', 'maggiore'),
        '_wa_utm_medium'    => __('UTM Medium', 'maggiore'),
        '_wa_utm_campaign'  => __('UTM Campaign', 'maggiore'),
        '_wa_referrer'      => __('Referrer HTTP', 'maggiore'),
        '_wa_timestamp'     => __('Timestamp (servidor)', 'maggiore'),
    ];
    echo '<table class="widefat striped" style="margin-top:10px;">';
    echo '<thead><tr><th style="width:200px;">' . __('Campo', 'maggiore') . '</th><th>' . __('Valor', 'maggiore') . '</th></tr></thead><tbody>';
    foreach ($fields as $key => $label) {
        $val = get_post_meta($post->ID, $key, true);
        echo '<tr>';
        echo '<td><strong>' . esc_html($label) . '</strong></td>';
        echo '<td>' . ($val ? esc_html($val) : '<em style="color:#999">—</em>') . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}


// =============================================================================
// 6. ENDPOINT AJAX — GUARDAR CLICK
// =============================================================================

add_action('wp_ajax_mg_wa_click',        'mg_handle_wa_click_ajax');
add_action('wp_ajax_nopriv_mg_wa_click', 'mg_handle_wa_click_ajax');

function mg_handle_wa_click_ajax() {
    // Verificar nonce
    if (!check_ajax_referer('mg_wa_click_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'Nonce inválido'], 403);
    }

    // Solo guardar si la opción está activa
    if (get_option('mg_wa_float_cpt_enabled', '1') !== '1') {
        wp_send_json_success(['saved' => false]);
    }

    // Sanitizar datos del request
    $page_url      = esc_url_raw($_POST['page_url']      ?? '');
    $page_title    = sanitize_text_field($_POST['page_title']   ?? '');
    $device        = sanitize_text_field($_POST['device']       ?? '');
    $os            = sanitize_text_field($_POST['os']           ?? '');
    $utm_source    = sanitize_text_field($_POST['utm_source']   ?? '');
    $utm_medium    = sanitize_text_field($_POST['utm_medium']   ?? '');
    $utm_campaign  = sanitize_text_field($_POST['utm_campaign'] ?? '');
    $referrer      = esc_url_raw($_POST['referrer']     ?? '');

    // Crear el post
    $post_title = sprintf(
        'WA Click — %s — %s',
        $page_title ?: __('Sin título', 'maggiore'),
        current_time('d/m/Y H:i')
    );

    $post_id = wp_insert_post([
        'post_type'   => 'mg_wa_click',
        'post_title'  => $post_title,
        'post_status' => 'publish',
    ]);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => $post_id->get_error_message()], 500);
    }

    // Guardar metadata
    $meta = [
        '_wa_page_url'     => $page_url,
        '_wa_page_title'   => $page_title,
        '_wa_device'       => $device,
        '_wa_os'           => $os,
        '_wa_utm_source'   => $utm_source,
        '_wa_utm_medium'   => $utm_medium,
        '_wa_utm_campaign' => $utm_campaign,
        '_wa_referrer'     => $referrer,
        '_wa_timestamp'    => current_time('Y-m-d H:i:s'),
    ];

    foreach ($meta as $key => $value) {
        if (!empty($value)) {
            update_post_meta($post_id, $key, $value);
        }
    }

    wp_send_json_success(['saved' => true, 'post_id' => $post_id]);
}


// =============================================================================
// 7. FRONTEND — BOTÓN + JAVASCRIPT
// =============================================================================

add_action('wp_footer', 'mg_render_whatsapp_float_button');

function mg_render_whatsapp_float_button() {

    // Solo si está habilitado y hay número configurado
    if (get_option('mg_wa_float_enabled', '1') !== '1') return;

    $whatsapp = get_option('maggiore_whatsapp', '');
    if (empty($whatsapp)) return;

    // Opciones
    $position      = get_option('mg_wa_float_position', 'bottom-right');
    $tooltip       = get_option('mg_wa_float_tooltip', '¡Escríbenos!');
    $message       = get_option('mg_wa_float_message', 'Hola, me gustaría obtener más información.');
    $ga4_enabled   = get_option('mg_wa_float_ga4_enabled', '1') === '1';
    $ga4_event     = get_option('mg_wa_float_ga4_event', 'whatsapp_click');
    $pixel_enabled = get_option('mg_wa_float_pixel_enabled', '1') === '1';
    $pixel_event   = get_option('mg_wa_float_pixel_event', 'WhatsAppClick');
    $cpt_enabled   = get_option('mg_wa_float_cpt_enabled', '1') === '1';

    // Posición CSS
    $pos_css = $position === 'bottom-left'
        ? 'bottom:28px; left:28px;'
        : 'bottom:28px; right:28px;';

    // Número limpio para wa.me
    $numero = preg_replace('/[^0-9]/', '', $whatsapp);

    // Mensaje dinámico — el reemplazo de {page_title} y {page_url} se hace en JS
    $message_encoded = esc_js($message);

    // Config para JS (solo lo necesario)
    $js_config = json_encode([
        'numero'       => $numero,
        'message'      => $message,
        'ga4'          => $ga4_enabled,
        'ga4Event'     => $ga4_event,
        'pixel'        => $pixel_enabled,
        'pixelEvent'   => $pixel_event,
        'cpt'          => $cpt_enabled,
        'ajaxUrl'      => admin_url('admin-ajax.php'),
        'nonce'        => wp_create_nonce('mg_wa_click_nonce'),
    ]);

    ?>
    <!-- WhatsApp Float Button — Maggiore -->
    <div id="mg-wa-float" style="
        position: fixed;
        <?= $pos_css ?>
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: <?= $position === 'bottom-left' ? 'flex-start' : 'flex-end'; ?>;
        gap: 8px;
    ">
        <?php if (!empty($tooltip)): ?>
        <div id="mg-wa-tooltip" style="
            background: #fff;
            color: #1a202c;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: none;
            white-space: nowrap;
        "><?= esc_html($tooltip); ?></div>
        <?php endif; ?>

        <a id="mg-wa-btn"
           href="#"
           aria-label="<?php _e('Contactar por WhatsApp', 'maggiore'); ?>"
           style="
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: #25D366;
            border-radius: 50%;
            box-shadow: 0 4px 20px rgba(37,211,102,0.45);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
        ">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#fff" viewBox="0 0 16 16">
                <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
            </svg>
        </a>
    </div>

    <script>
    (function () {
        const cfg = <?= $js_config; ?>;

        const btn     = document.getElementById('mg-wa-btn');
        const tooltip = document.getElementById('mg-wa-tooltip');

        if (!btn) return;

        // --- Tooltip hover ---
        if (tooltip) {
            btn.addEventListener('mouseenter', () => {
                tooltip.style.opacity = '1';
                tooltip.style.transform = 'translateY(0)';
            });
            btn.addEventListener('mouseleave', () => {
                tooltip.style.opacity = '0';
                tooltip.style.transform = 'translateY(6px)';
            });
        }

        // --- Hover visual del botón ---
        btn.addEventListener('mouseenter', () => {
            btn.style.transform = 'scale(1.1)';
            btn.style.boxShadow = '0 6px 25px rgba(37,211,102,0.6)';
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'scale(1)';
            btn.style.boxShadow = '0 4px 20px rgba(37,211,102,0.45)';
        });

        // --- Click handler ---
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const pageTitle = document.title || '';
            const pageUrl   = window.location.href;

            // Reemplazar variables en el mensaje
            const finalMsg  = cfg.message
                .replace('{page_title}', pageTitle)
                .replace('{page_url}',   pageUrl);

            const waUrl = 'https://wa.me/' + cfg.numero + '?text=' + encodeURIComponent(finalMsg);

            // --- 1. GA4 ---
            if (cfg.ga4 && typeof gtag === 'function') {
                gtag('event', cfg.ga4Event, {
                    event_category : 'WhatsApp',
                    event_label    : pageTitle,
                    page_url       : pageUrl,
                    device_type    : detectDevice(),
                });
            }

            // --- 2. Meta Pixel ---
            if (cfg.pixel && typeof fbq === 'function') {
                fbq('trackCustom', cfg.pixelEvent, {
                    page_title : pageTitle,
                    page_url   : pageUrl,
                    device     : detectDevice(),
                });
            }

            // --- 3. AJAX → CPT (silencioso, no bloquea) ---
            if (cfg.cpt) {
                const utmParams = getUTMParams();
                const formData  = new FormData();
                formData.append('action',       'mg_wa_click');
                formData.append('nonce',        cfg.nonce);
                formData.append('page_url',     pageUrl);
                formData.append('page_title',   pageTitle);
                formData.append('device',       detectDevice());
                formData.append('os',           detectOS());
                formData.append('utm_source',   utmParams.source);
                formData.append('utm_medium',   utmParams.medium);
                formData.append('utm_campaign', utmParams.campaign);
                formData.append('referrer',     document.referrer || '');

                // fetch sin await — no bloquea la apertura de WhatsApp
                fetch(cfg.ajaxUrl, { method: 'POST', body: formData }).catch(() => {});
            }

            // --- 4. Abrir WhatsApp ---
            window.open(waUrl, '_blank', 'noopener,noreferrer');
        });

        // --- Helpers ---
        function detectDevice() {
            const ua = navigator.userAgent;
            if (/Mobi|Android|iPhone|iPod/.test(ua)) return 'mobile';
            if (/iPad|Tablet/.test(ua))               return 'tablet';
            return 'desktop';
        }

        function detectOS() {
            const ua = navigator.userAgent;
            if (/Windows/.test(ua))       return 'Windows';
            if (/Mac OS X/.test(ua))      return 'macOS';
            if (/Android/.test(ua))       return 'Android';
            if (/iPhone|iPad/.test(ua))   return 'iOS';
            if (/Linux/.test(ua))         return 'Linux';
            return 'Unknown';
        }

        function getUTMParams() {
            const params = new URLSearchParams(window.location.search);
            return {
                source   : params.get('utm_source')   || '',
                medium   : params.get('utm_medium')   || '',
                campaign : params.get('utm_campaign') || '',
            };
        }
    })();
    </script>
    <!-- / WhatsApp Float Button -->
    <?php
}
