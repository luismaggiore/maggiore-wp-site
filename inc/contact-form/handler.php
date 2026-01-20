<?php
/**
 * Contact Form Handler
 * 
 * Procesa envíos del formulario de contacto:
 * - Valida datos
 * - Guarda en BD como CPT
 * - Envía emails (confirmación + notificación)
 */

if (!defined('ABSPATH')) exit;

/**
 * Registrar AJAX handlers
 */
add_action('wp_ajax_maggiore_contact_form', 'maggiore_handle_contact_form');
add_action('wp_ajax_nopriv_maggiore_contact_form', 'maggiore_handle_contact_form');

/**
 * Procesar formulario de contacto
 */
function maggiore_handle_contact_form() {
    
    // Verificar nonce de seguridad
    check_ajax_referer('maggiore_nonce', 'nonce');
    
    // =========================================================================
    // 1. SANITIZAR Y VALIDAR DATOS
    // =========================================================================
    
    $nombre = sanitize_text_field($_POST['nombre'] ?? '');
    $cargo = sanitize_text_field($_POST['cargo'] ?? '');
    $correo = sanitize_email($_POST['correo'] ?? '');
    $empresa = sanitize_text_field($_POST['empresa'] ?? '');
    $telefono = sanitize_text_field($_POST['telefono'] ?? '');
    $dolor = sanitize_textarea_field($_POST['dolorEmpresa'] ?? '');
    $objetivos = sanitize_textarea_field($_POST['objetivos'] ?? '');
    
    // Capturar origen del formulario (enviado desde JavaScript)
    $form_origen = sanitize_text_field($_POST['form_origen'] ?? 'Formulario Web');
    
    // Validar campos obligatorios
    $errores = [];
    
    if (empty($nombre)) {
        $errores[] = __('El nombre es obligatorio', 'maggiore');
    }
    
    if (empty($correo) || !is_email($correo)) {
        $errores[] = __('Debes proporcionar un correo válido', 'maggiore');
    }
    
    if (!empty($errores)) {
        wp_send_json_error([
            'message' => implode('. ', $errores),
            'errores' => $errores
        ]);
        return;
    }
    
    // =========================================================================
    // 2. GUARDAR EN BASE DE DATOS (CPT)
    // =========================================================================
    
    $titulo_post = $nombre;
    if ($empresa) {
        $titulo_post .= ' - ' . $empresa;
    }
    
    $post_data = [
        'post_type' => 'mg_contacto',
        'post_title' => $titulo_post,
        'post_status' => 'publish',
        'post_author' => 1, // Usuario admin por defecto
        'meta_input' => [
            'correo_nombre' => $nombre,
            'correo_cargo' => $cargo,
            'correo_email' => $correo,
            'correo_empresa' => $empresa,
            'correo_telefono' => $telefono,
            'correo_dolor' => $dolor,
            'correo_objetivos' => $objetivos,
            'correo_origen' => $form_origen, // Origen detectado automáticamente
            'correo_ip' => maggiore_get_user_ip(),
            'correo_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'correo_idioma' => function_exists('pll_current_language') ? pll_current_language() : 'es',
            'correo_fecha_recepcion' => current_time('mysql'),
            // UTM tracking (si están disponibles)
            'correo_utm_source' => sanitize_text_field($_POST['utm_source'] ?? ''),
            'correo_utm_medium' => sanitize_text_field($_POST['utm_medium'] ?? ''),
            'correo_utm_campaign' => sanitize_text_field($_POST['utm_campaign'] ?? ''),
        ]
    ];
    
    $post_id = wp_insert_post($post_data);
    
    if (is_wp_error($post_id)) {
        wp_send_json_error([
            'message' => __('Error al guardar el contacto. Por favor, intenta de nuevo.', 'maggiore')
        ]);
        return;
    }
    
    // =========================================================================
    // 3. ENVIAR EMAILS
    // =========================================================================
    
    $emails_enviados = maggiore_send_contact_emails([
        'nombre' => $nombre,
        'cargo' => $cargo,
        'correo' => $correo,
        'empresa' => $empresa,
        'telefono' => $telefono,
        'dolor' => $dolor,
        'objetivos' => $objetivos,
        'post_id' => $post_id
    ]);
    
    // =========================================================================
    // 4. RESPUESTA EXITOSA
    // =========================================================================
    
    wp_send_json_success([
        'message' => __('¡Gracias por tu mensaje! Nos pondremos en contacto pronto.', 'maggiore'),
        'post_id' => $post_id,
        'emails_enviados' => $emails_enviados
    ]);
}

/**
 * Enviar emails de confirmación y notificación
 * 
 * @param array $data Datos del contacto
 * @return array Estado de envío de emails
 */
function maggiore_send_contact_emails($data) {
    
    $resultado = [
        'cliente' => false,
        'admin' => false
    ];
    
    // =========================================================================
    // EMAIL 1: CONFIRMACIÓN AL CLIENTE
    // =========================================================================
    
    $cliente_subject = sprintf(
        __('✓ Hemos recibido tu mensaje - %s', 'maggiore'),
        get_bloginfo('name')
    );
    
    $cliente_message = maggiore_get_email_cliente_template($data);
    
    $cliente_headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <noreply@' . parse_url(home_url(), PHP_URL_HOST) . '>'
    ];
    
    $resultado['cliente'] = wp_mail(
        $data['correo'],
        $cliente_subject,
        $cliente_message,
        $cliente_headers
    );
    
    // =========================================================================
    // EMAIL 2: NOTIFICACIÓN AL ADMIN
    // =========================================================================
    
    $admin_email = get_option('admin_email'); // O configurar otro email
    
    $admin_subject = sprintf(
        __('🔔 Nuevo contacto: %s', 'maggiore'),
        $data['nombre']
    );
    
    $admin_message = maggiore_get_email_admin_template($data);
    
    $admin_headers = [
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $data['nombre'] . ' <' . $data['correo'] . '>'
    ];
    
    $resultado['admin'] = wp_mail(
        $admin_email,
        $admin_subject,
        $admin_message,
        $admin_headers
    );
    
    return $resultado;
}

/**
 * Obtener IP del usuario de forma segura
 */
function maggiore_get_user_ip() {
    $ip_keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER)) {
            $ip = explode(',', $_SERVER[$key]);
            $ip = trim(end($ip));
            
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    
    return 'Unknown';
}

/**
 * Validar honeypot (anti-spam)
 * Agregar campo oculto en el formulario HTML
 */
function maggiore_validate_honeypot() {
    // Si el campo "website" (honeypot) está lleno, es un bot
    if (!empty($_POST['website'])) {
        wp_send_json_error([
            'message' => __('Spam detectado', 'maggiore')
        ]);
        return false;
    }
    return true;
}

/**
 * Rate limiting básico (prevenir abuso)
 */
function maggiore_check_rate_limit() {
    $ip = maggiore_get_user_ip();
    $transient_key = 'contact_form_' . md5($ip);
    
    if (get_transient($transient_key)) {
        wp_send_json_error([
            'message' => __('Por favor, espera unos minutos antes de enviar otro mensaje.', 'maggiore')
        ]);
        return false;
    }
    
    // Bloquear durante 5 minutos
    set_transient($transient_key, true, 5 * MINUTE_IN_SECONDS);
    return true;
}
