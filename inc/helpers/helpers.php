<?php
/**
 * Helper Functions - Datos de Contacto
 * 
 * Funciones para obtener fácilmente los datos de contacto
 * configurados en el admin de WordPress
 */

if (!defined('ABSPATH')) exit;

// =============================================================================
// INFORMACIÓN GENERAL
// =============================================================================

/**
 * Obtener nombre de la empresa
 * 
 * @return string
 */
function maggiore_get_company_name() {
    return get_option('maggiore_company_name', get_bloginfo('name'));
}

/**
 * Obtener eslogan/tagline
 * 
 * @return string
 */
function maggiore_get_tagline() {
    return get_option('maggiore_tagline', '');
}

// =============================================================================
// CONTACTO
// =============================================================================

/**
 * Obtener email principal
 * 
 * @param bool $link Si es true, devuelve un enlace mailto
 * @return string
 */
function maggiore_get_email($link = false) {
    $email = get_option('maggiore_email_principal', get_option('admin_email'));
    
    if ($link) {
        return '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
    }
    
    return $email;
}

/**
 * Obtener teléfono principal
 * 
 * @param bool $link Si es true, devuelve un enlace tel:
 * @return string
 */
function maggiore_get_telefono($link = false) {
    $telefono = get_option('maggiore_telefono_principal', '');
    
    if (empty($telefono)) {
        return '';
    }
    
    if ($link) {
        $tel_clean = preg_replace('/[^0-9+]/', '', $telefono);
        return '<a href="tel:' . esc_attr($tel_clean) . '">' . esc_html($telefono) . '</a>';
    }
    
    return $telefono;
}

/**
 * Obtener número de WhatsApp
 * 
 * @param bool $link Si es true, devuelve el enlace completo de WhatsApp
 * @param string $message Mensaje predeterminado (opcional)
 * @return string
 */
function maggiore_get_whatsapp($link = false, $message = '') {
    $whatsapp = get_option('maggiore_whatsapp', '');
    
    if (empty($whatsapp)) {
        return '';
    }
    
    if ($link) {
        $numero_limpio = preg_replace('/[^0-9]/', '', $whatsapp);
        $url = 'https://wa.me/' . $numero_limpio;
        
        if (!empty($message)) {
            $url .= '?text=' . urlencode($message);
        }
        
        return $url;
    }
    
    return $whatsapp;
}

/**
 * Renderizar botón de WhatsApp
 * 
 * @param string $text Texto del botón
 * @param string $message Mensaje predeterminado
 * @param string $classes Clases CSS adicionales
 */
function maggiore_whatsapp_button($text = '', $message = '', $classes = 'btn btn-success') {
    $whatsapp = maggiore_get_whatsapp();
    
    if (empty($whatsapp)) {
        return;
    }
    
    $text = $text ?: __('Contactar por WhatsApp', 'maggiore');
    $url = maggiore_get_whatsapp(true, $message);
    
    ?>
    <a href="<?php echo esc_url($url); ?>" 
       class="<?php echo esc_attr($classes); ?>" 
       target="_blank" 
       rel="noopener">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 8px;">
            <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
        </svg>
        <?php echo esc_html($text); ?>
    </a>
    <?php
}

// =============================================================================
// UBICACIÓN
// =============================================================================

/**
 * Obtener dirección completa
 * 
 * @param bool $formatted Si es true, devuelve dirección formateada con ciudad y país
 * @return string
 */
function maggiore_get_direccion($formatted = false) {
    $direccion = get_option('maggiore_direccion', '');
    
    if ($formatted && !empty($direccion)) {
        $ciudad = get_option('maggiore_ciudad', '');
        $pais = get_option('maggiore_pais', '');
        
        $parts = array_filter([$direccion, $ciudad, $pais]);
        return implode(', ', $parts);
    }
    
    return $direccion;
}

/**
 * Obtener ciudad
 * 
 * @return string
 */
function maggiore_get_ciudad() {
    return get_option('maggiore_ciudad', '');
}

/**
 * Obtener país
 * 
 * @return string
 */
function maggiore_get_pais() {
    return get_option('maggiore_pais', '');
}

/**
 * Obtener URL de Google Maps
 * 
 * @param bool $link Si es true, devuelve un enlace <a>
 * @param string $text Texto del enlace
 * @return string
 */
function maggiore_get_google_maps($link = false, $text = '') {
    $maps_url = get_option('maggiore_google_maps_url', '');
    
    if (empty($maps_url)) {
        return '';
    }
    
    if ($link) {
        $text = $text ?: __('Ver en Google Maps', 'maggiore');
        return '<a href="' . esc_url($maps_url) . '" target="_blank" rel="noopener">' . esc_html($text) . '</a>';
    }
    
    return $maps_url;
}

// =============================================================================
// HORARIOS
// =============================================================================

/**
 * Obtener horario de días
 * 
 * @return string
 */
function maggiore_get_horario_dias() {
    return get_option('maggiore_horario_dias', 'Lunes a Viernes');
}

/**
 * Obtener horario de horas
 * 
 * @return string
 */
function maggiore_get_horario_horas() {
    return get_option('maggiore_horario_horas', '9:00 - 18:00');
}

/**
 * Obtener nota de horarios
 * 
 * @return string
 */
function maggiore_get_horario_nota() {
    return get_option('maggiore_horario_nota', '');
}

/**
 * Renderizar horarios completos
 */
function maggiore_display_horarios() {
    $dias = maggiore_get_horario_dias();
    $horas = maggiore_get_horario_horas();
    $nota = maggiore_get_horario_nota();
    
    ?>
    <div class="horarios-info">
        <p class="mb-2">
            <strong><?php echo esc_html($dias); ?>:</strong><br>
            <?php echo esc_html($horas); ?>
        </p>
        <?php if ($nota): ?>
            <p class="text-muted small mb-0">
                <?php echo esc_html($nota); ?>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

// =============================================================================
// REDES SOCIALES
// =============================================================================

/**
 * Obtener URL de Instagram
 * 
 * @return string
 */
function maggiore_get_instagram_url() {
    return get_option('maggiore_instagram_url', '');
}

/**
 * Obtener URL de LinkedIn
 * 
 * @return string
 */
function maggiore_get_linkedin_url() {
    return get_option('maggiore_linkedin_url', '');
}

/**
 * Obtener URL de Facebook
 * 
 * @return string
 */
function maggiore_get_facebook_url() {
    return get_option('maggiore_facebook_url', '');
}

/**
 * Obtener URL de Twitter/X
 * 
 * @return string
 */
function maggiore_get_twitter_url() {
    return get_option('maggiore_twitter_url', '');
}

/**
 * Obtener URL de YouTube
 * 
 * @return string
 */
function maggiore_get_youtube_url() {
    return get_option('maggiore_youtube_url', '');
}

/**
 * Obtener URL de TikTok
 * 
 * @return string
 */
function maggiore_get_tiktok_url() {
    return get_option('maggiore_tiktok_url', '');
}

/**
 * Obtener array con todas las redes sociales configuradas
 * 
 * @return array
 */
function maggiore_get_all_social_networks() {
    $networks = [
        'instagram' => [
            'url' => maggiore_get_instagram_url(),
            'label' => 'Instagram',
            'icon' => 'instagram',
            'color' => '#ffffff'
        ],
        'linkedin' => [
            'url' => maggiore_get_linkedin_url(),
            'label' => 'LinkedIn',
            'icon' => 'linkedin',
            'color' => '#ffffff'
        ],
        'facebook' => [
            'url' => maggiore_get_facebook_url(),
            'label' => 'Facebook',
            'icon' => 'facebook',
            'color' => '#ffffff'
        ],
        'twitter' => [
            'url' => maggiore_get_twitter_url(),
            'label' => 'Twitter',
            'icon' => 'twitter',
            'color' => '#ffffff'
        ],
        'youtube' => [
            'url' => maggiore_get_youtube_url(),
            'label' => 'YouTube',
            'icon' => 'youtube',
            'color' => '#ffffff'
        ],
        'tiktok' => [
            'url' => maggiore_get_tiktok_url(),
            'label' => 'TikTok',
            'icon' => 'tiktok',
            'color' => '#ffffff'
        ],
    ];
    
    // Filtrar solo las que tienen URL configurada
    return array_filter($networks, function($network) {
        return !empty($network['url']);
    });
}

/**
 * Renderizar iconos de redes sociales
 * 
 * @param string $size Tamaño de los iconos (small, medium, large)
 * @param bool $show_label Mostrar etiqueta del nombre de la red
 * @param string $classes Clases CSS adicionales
 */
function maggiore_social_icons($size = 'medium', $show_label = false, $classes = '') {
    $networks = maggiore_get_all_social_networks();
    
    if (empty($networks)) {
        return;
    }
    
    $sizes = [
        'small' => 20,
        'medium' => 24,
        'large' => 32
    ];
    
    $icon_size = $sizes[$size] ?? 24;
    
    // SVG Icons
    $svg_icons = [
        'instagram' => '<path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/>',
        'linkedin' => '<path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>',
        'facebook' => '<path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>',
        'twitter' => '<path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>',
        'youtube' => '<path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.007 2.007 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.007 2.007 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31.4 31.4 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.007 2.007 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A99.788 99.788 0 0 1 7.858 2h.193zM6.4 5.209v4.818l4.157-2.408L6.4 5.209z"/>',
        'tiktok' => '<path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3V0Z"/>'
    ];
    
    ?>
    <div class="social-icons <?php echo esc_attr($classes); ?>">
        <?php foreach ($networks as $key => $network): ?>
            <a href="<?php echo esc_url($network['url']); ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               title="<?php echo esc_attr($network['label']); ?>"
               class="social-icon social-icon-<?php echo esc_attr($key); ?>"
               style="color: <?php echo esc_attr($network['color']); ?>; margin-right: 15px;">
                <svg width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" fill="currentColor" viewBox="0 0 16 16">
                    <?php echo $svg_icons[$network['icon']]; ?>
                </svg>
                <?php if ($show_label): ?>
                    <span class="social-label"><?php echo esc_html($network['label']); ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
}

// =============================================================================
// COMPONENTES COMPLETOS
// =============================================================================

/**
 * Renderizar bloque de información de contacto completo
 * 
 * Perfecto para sidebars, footers, o páginas de contacto
 */
function maggiore_contact_info_block() {
    ?>
    <div class="contact-info-block">
        
        <?php if (maggiore_get_email()): ?>
        <div class="contact-item mb-3">
            <strong class="d-block mb-1">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 5px;">
                    <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
                </svg>
                <?php _e('Email', 'maggiore'); ?>
            </strong>
            <?php echo maggiore_get_email(true); ?>
        </div>
        <?php endif; ?>
        
        <?php if (maggiore_get_telefono()): ?>
        <div class="contact-item mb-3">
            <strong class="d-block mb-1">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 5px;">
                    <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                </svg>
                <?php _e('Teléfono', 'maggiore'); ?>
            </strong>
            <?php echo maggiore_get_telefono(true); ?>
        </div>
        <?php endif; ?>
        
        <?php if (maggiore_get_direccion()): ?>
        <div class="contact-item mb-3">
            <strong class="d-block mb-1">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 5px;">
                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                </svg>
                <?php _e('Ubicación', 'maggiore'); ?>
            </strong>
            <?php echo esc_html(maggiore_get_direccion(true)); ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty(maggiore_get_all_social_networks())): ?>
        <div class="contact-item mt-4">
            <strong class="d-block mb-2"><?php _e('Síguenos', 'maggiore'); ?></strong>
            <?php maggiore_social_icons('medium'); ?>
        </div>
        <?php endif; ?>
        
    </div>
    <?php
}
