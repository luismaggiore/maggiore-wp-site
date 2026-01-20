<?php
/**
 * Metabox para Contactos
 * 
 * Muestra los datos del contacto en una interfaz clara y organizada
 */

if (!defined('ABSPATH')) exit;

/**
 * Agregar metabox de datos del contacto
 */
add_action('add_meta_boxes', function() {
    add_meta_box(
        'mg_contacto_datos',
        __('📋 Información del Contacto', 'maggiore'),
        'maggiore_contacto_datos_callback',
        'mg_contacto',
        'normal',
        'high'
    );
    
    add_meta_box(
        'mg_contacto_metadatos',
        __('🔍 Información Técnica', 'maggiore'),
        'maggiore_contacto_metadatos_callback',
        'mg_contacto',
        'side',
        'default'
    );
});

/**
 * Renderizar metabox principal con datos del contacto
 */
function maggiore_contacto_datos_callback($post) {
    $nombre = get_post_meta($post->ID, 'correo_nombre', true);
    $cargo = get_post_meta($post->ID, 'correo_cargo', true);
    $correo = get_post_meta($post->ID, 'correo_email', true);
    $empresa = get_post_meta($post->ID, 'correo_empresa', true);
    $telefono = get_post_meta($post->ID, 'correo_telefono', true);
    $dolor = get_post_meta($post->ID, 'correo_dolor', true);
    $objetivos = get_post_meta($post->ID, 'correo_objetivos', true);
    ?>
    
    <style>
        .contacto-field {
            margin-bottom: 25px;
            padding: 20px;
            background: #f9fafb;
            border-left: 4px solid #667eea;
            border-radius: 4px;
        }
        .contacto-field label {
            display: block;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .contacto-field .value {
            color: #2d3748;
            font-size: 15px;
            line-height: 1.6;
        }
        .contacto-field .value a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .contacto-field .value a:hover {
            text-decoration: underline;
        }
        .contacto-field.destacado {
            border-left-color: #fc8181;
            background: #fff5f5;
        }
        .contacto-field.destacado label {
            color: #fc8181;
        }
        .contacto-actions {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #e2e8f0;
        }
        .contacto-actions .button {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .empty-field {
            color: #a0aec0;
            font-style: italic;
        }
    </style>
    
    <div class="contacto-datos-wrapper">
        
        <!-- Nombre -->
        <div class="contacto-field">
            <label><?php _e('Nombre completo', 'maggiore'); ?></label>
            <div class="value">
                <?php echo $nombre ? esc_html($nombre) : '<span class="empty-field">No proporcionado</span>'; ?>
            </div>
        </div>
        
        <!-- Email -->
        <div class="contacto-field">
            <label><?php _e('Correo electrónico', 'maggiore'); ?></label>
            <div class="value">
                <?php if ($correo): ?>
                    <a href="mailto:<?php echo esc_attr($correo); ?>">
                        <?php echo esc_html($correo); ?>
                    </a>
                <?php else: ?>
                    <span class="empty-field">No proporcionado</span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Teléfono -->
        <?php if ($telefono): ?>
        <div class="contacto-field">
            <label><?php _e('Teléfono celular', 'maggiore'); ?></label>
            <div class="value">
                <a href="tel:<?php echo esc_attr($telefono); ?>">
                    <?php echo esc_html($telefono); ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Cargo -->
        <?php if ($cargo): ?>
        <div class="contacto-field">
            <label><?php _e('Cargo', 'maggiore'); ?></label>
            <div class="value"><?php echo esc_html($cargo); ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Empresa -->
        <?php if ($empresa): ?>
        <div class="contacto-field">
            <label><?php _e('Empresa', 'maggiore'); ?></label>
            <div class="value"><?php echo esc_html($empresa); ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Dolor de la empresa (destacado) -->
        <?php if ($dolor): ?>
        <div class="contacto-field destacado">
            <label><?php _e('⚠️ Dolor de la empresa', 'maggiore'); ?></label>
            <div class="value"><?php echo nl2br(esc_html($dolor)); ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Objetivos -->
        <?php if ($objetivos): ?>
        <div class="contacto-field">
            <label><?php _e('🎯 Objetivos', 'maggiore'); ?></label>
            <div class="value"><?php echo nl2br(esc_html($objetivos)); ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Acciones rápidas -->
        <div class="contacto-actions">
            <a href="mailto:<?php echo esc_attr($correo); ?>" class="button button-primary button-large">
                <span class="dashicons dashicons-email" style="vertical-align: middle;"></span>
                <?php _e('Enviar Email', 'maggiore'); ?>
            </a>
            
            <?php if ($telefono): ?>
            <a href="tel:<?php echo esc_attr($telefono); ?>" class="button button-large">
                <span class="dashicons dashicons-phone" style="vertical-align: middle;"></span>
                <?php _e('Llamar', 'maggiore'); ?>
            </a>
            <?php endif; ?>
            
            <button type="button" class="button button-large" onclick="copiarDatosContacto()">
                <span class="dashicons dashicons-clipboard" style="vertical-align: middle;"></span>
                <?php _e('Copiar Datos', 'maggiore'); ?>
            </button>
        </div>
        
    </div>
    
    <script>
    function copiarDatosContacto() {
        const datos = `
Nombre: <?php echo esc_js($nombre); ?>
Email: <?php echo esc_js($correo); ?>
<?php if ($telefono): ?>Teléfono: <?php echo esc_js($telefono); ?><?php endif; ?>
<?php if ($cargo): ?>Cargo: <?php echo esc_js($cargo); ?><?php endif; ?>
<?php if ($empresa): ?>Empresa: <?php echo esc_js($empresa); ?><?php endif; ?>
<?php if ($dolor): ?>
Dolor: <?php echo esc_js($dolor); ?><?php endif; ?>
<?php if ($objetivos): ?>
Objetivos: <?php echo esc_js($objetivos); ?><?php endif; ?>
        `.trim();
        
        navigator.clipboard.writeText(datos).then(() => {
            alert('✓ Datos copiados al portapapeles');
        });
    }
    </script>
    
    <?php
}

/**
 * Renderizar metabox con metadatos técnicos
 */
function maggiore_contacto_metadatos_callback($post) {
    $origen = get_post_meta($post->ID, 'correo_origen', true);
    $ip = get_post_meta($post->ID, 'correo_ip', true);
    $user_agent = get_post_meta($post->ID, 'correo_user_agent', true);
    $idioma = get_post_meta($post->ID, 'correo_idioma', true);
    $fecha_recepcion = get_post_meta($post->ID, 'correo_fecha_recepcion', true);
    
    // UTM
    $utm_source = get_post_meta($post->ID, 'correo_utm_source', true);
    $utm_medium = get_post_meta($post->ID, 'correo_utm_medium', true);
    $utm_campaign = get_post_meta($post->ID, 'correo_utm_campaign', true);
    ?>
    
    <style>
        .metadato-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        .metadato-item:last-child {
            border-bottom: none;
        }
        .metadato-item strong {
            display: block;
            color: #4a5568;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 700;
        }
        .metadato-item span {
            color: #2d3748;
            font-size: 13px;
        }
        .badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
    </style>
    
    <div class="metadatos-wrapper">
        
        <!-- Origen -->
        <?php if ($origen): ?>
        <div class="metadato-item">
            <strong><?php _e('Origen', 'maggiore'); ?></strong>
            <span class="badge"><?php echo esc_html($origen); ?></span>
        </div>
        <?php endif; ?>
        
        <!-- Fecha de recepción -->
        <?php if ($fecha_recepcion): ?>
        <div class="metadato-item">
            <strong><?php _e('Recibido', 'maggiore'); ?></strong>
            <span><?php echo date_i18n('d/m/Y H:i', strtotime($fecha_recepcion)); ?></span>
        </div>
        <?php endif; ?>
        
        <!-- Idioma -->
        <?php if ($idioma): ?>
        <div class="metadato-item">
            <strong><?php _e('Idioma', 'maggiore'); ?></strong>
            <span><?php echo strtoupper($idioma); ?></span>
        </div>
        <?php endif; ?>
        
        <!-- IP -->
        <?php if ($ip): ?>
        <div class="metadato-item">
            <strong><?php _e('IP Address', 'maggiore'); ?></strong>
            <span style="font-family: monospace; font-size: 12px;"><?php echo esc_html($ip); ?></span>
        </div>
        <?php endif; ?>
        
        <!-- UTM Tracking -->
        <?php if ($utm_source || $utm_medium || $utm_campaign): ?>
        <div class="metadato-item">
            <strong><?php _e('🎯 UTM Tracking', 'maggiore'); ?></strong>
            <?php if ($utm_source): ?>
                <div style="margin-top: 5px; font-size: 12px;">
                    <strong>Source:</strong> <?php echo esc_html($utm_source); ?>
                </div>
            <?php endif; ?>
            <?php if ($utm_medium): ?>
                <div style="font-size: 12px;">
                    <strong>Medium:</strong> <?php echo esc_html($utm_medium); ?>
                </div>
            <?php endif; ?>
            <?php if ($utm_campaign): ?>
                <div style="font-size: 12px;">
                    <strong>Campaign:</strong> <?php echo esc_html($utm_campaign); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- User Agent (colapsable) -->
        <?php if ($user_agent): ?>
        <div class="metadato-item">
            <strong><?php _e('User Agent', 'maggiore'); ?></strong>
            <details>
                <summary style="cursor: pointer; font-size: 12px; color: #667eea;">
                    <?php _e('Ver detalles', 'maggiore'); ?>
                </summary>
                <p style="margin-top: 10px; font-size: 11px; font-family: monospace; word-wrap: break-word;">
                    <?php echo esc_html($user_agent); ?>
                </p>
            </details>
        </div>
        <?php endif; ?>
        
    </div>
    
    <?php
}

/**
 * Prevenir edición de metadatos (son solo lectura)
 */
add_action('save_post_mg_contacto', function($post_id) {
    // No hacer nada - los campos son solo lectura
    // Los datos solo se crean desde el formulario
}, 10, 1);
