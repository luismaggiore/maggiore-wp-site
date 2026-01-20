<?php
/**
 * Configuración de Datos de Contacto
 * 
 * Sistema centralizado para gestionar información de contacto
 * desde el admin de WordPress
 */

if (!defined('ABSPATH')) exit;

/**
 * Agregar página de opciones al menú de WordPress
 */
add_action('admin_menu', function() {
    add_menu_page(
        __('Datos de Contacto', 'maggiore'),           // Título de la página
        __('Datos Contacto', 'maggiore'),              // Título del menú
        'manage_options',                              // Capacidad requerida
        'maggiore-contact-data',                       // Slug del menú
        'maggiore_contact_data_page',                  // Función de callback
        'dashicons-phone',                             // Icono
        59                                             // Posición (después de Ajustes)
    );
});

/**
 * Registrar configuraciones
 */
add_action('admin_init', function() {
    
    // Grupo de configuración
    $option_group = 'maggiore_contact_data';
    
    // =========================================================================
    // INFORMACIÓN GENERAL
    // =========================================================================
    
    register_setting($option_group, 'maggiore_company_name', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => get_bloginfo('name')
    ]);
    
    register_setting($option_group, 'maggiore_tagline', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    ]);
    
    // =========================================================================
    // CONTACTO PRINCIPAL
    // =========================================================================
    
    register_setting($option_group, 'maggiore_email_principal', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_email',
        'default' => get_option('admin_email')
    ]);
    
    register_setting($option_group, 'maggiore_telefono_principal', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    ]);
    
    register_setting($option_group, 'maggiore_whatsapp', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    ]);
    
    // =========================================================================
    // UBICACIÓN
    // =========================================================================
    
    register_setting($option_group, 'maggiore_direccion', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    ]);
    
    register_setting($option_group, 'maggiore_ciudad', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => 'Santiago'
    ]);
    
    register_setting($option_group, 'maggiore_pais', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => 'Chile'
    ]);
    
    register_setting($option_group, 'maggiore_google_maps_url', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => ''
    ]);
    
    // =========================================================================
    // HORARIOS
    // =========================================================================
    
    register_setting($option_group, 'maggiore_horario_dias', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => 'Lunes a Viernes'
    ]);
    
    register_setting($option_group, 'maggiore_horario_horas', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => '9:00 - 18:00'
    ]);
    
    register_setting($option_group, 'maggiore_horario_nota', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default' => 'Respondemos emails en menos de 24 horas hábiles'
    ]);
    
    // =========================================================================
    // REDES SOCIALES
    // =========================================================================
    
    register_setting($option_group, 'maggiore_instagram_url', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => ''
    ]);
    
    register_setting($option_group, 'maggiore_linkedin_url', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => ''
    ]);
    
    register_setting($option_group, 'maggiore_facebook_url', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => ''
    ]);
    
    register_setting($option_group, 'maggiore_twitter_url', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => ''
    ]);
    
    register_setting($option_group, 'maggiore_youtube_url', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => ''
    ]);
    
    register_setting($option_group, 'maggiore_tiktok_url', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => ''
    ]);
    
});

/**
 * Renderizar página de configuración
 */
function maggiore_contact_data_page() {
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Guardar cambios si se envió el formulario
    if (isset($_GET['settings-updated'])) {
        add_settings_error(
            'maggiore_contact_data_messages',
            'maggiore_contact_data_message',
            __('Datos de contacto guardados correctamente', 'maggiore'),
            'updated'
        );
    }
    
    settings_errors('maggiore_contact_data_messages');
    ?>
    
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <p class="description">
            <?php _e('Configura los datos de contacto de tu empresa. Estos datos se pueden usar en cualquier parte del sitio web.', 'maggiore'); ?>
        </p>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('maggiore_contact_data');
            ?>
            
            <div style="max-width: 800px; margin-top: 30px;">
                
                <!-- INFORMACIÓN GENERAL -->
                <div class="card" style="margin-bottom: 20px;">
                    <h2 class="title" style="padding: 15px 20px; margin: 0; border-bottom: 1px solid #ddd;">
                        <?php _e('Información General', 'maggiore'); ?>
                    </h2>
                    <div style="padding: 20px;">
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_company_name">
                                        <?php _e('Nombre de la Empresa', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="maggiore_company_name" 
                                        name="maggiore_company_name" 
                                        value="<?php echo esc_attr(get_option('maggiore_company_name', get_bloginfo('name'))); ?>"
                                        class="regular-text"
                                    >
                                    <p class="description">
                                        <?php _e('Nombre oficial de tu empresa', 'maggiore'); ?>
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_tagline">
                                        <?php _e('Eslogan', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="maggiore_tagline" 
                                        name="maggiore_tagline" 
                                        value="<?php echo esc_attr(get_option('maggiore_tagline')); ?>"
                                        class="regular-text"
                                        placeholder="<?php _e('Ej: Marketing Digital que genera resultados', 'maggiore'); ?>"
                                    >
                                    <p class="description">
                                        <?php _e('Frase descriptiva de tu empresa (opcional)', 'maggiore'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- CONTACTO PRINCIPAL -->
                <div class="card" style="margin-bottom: 20px;">
                    <h2 class="title" style="padding: 15px 20px; margin: 0; border-bottom: 1px solid #ddd;">
                        <?php _e('Contacto Principal', 'maggiore'); ?>
                    </h2>
                    <div style="padding: 20px;">
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_email_principal">
                                        <?php _e('Email Principal', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="email" 
                                        id="maggiore_email_principal" 
                                        name="maggiore_email_principal" 
                                        value="<?php echo esc_attr(get_option('maggiore_email_principal', get_option('admin_email'))); ?>"
                                        class="regular-text"
                                    >
                                    <p class="description">
                                        <?php _e('Email principal de contacto', 'maggiore'); ?>
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_telefono_principal">
                                        <?php _e('Teléfono Principal', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="maggiore_telefono_principal" 
                                        name="maggiore_telefono_principal" 
                                        value="<?php echo esc_attr(get_option('maggiore_telefono_principal')); ?>"
                                        class="regular-text"
                                        placeholder="+56 9 1234 5678"
                                    >
                                    <p class="description">
                                        <?php _e('Teléfono de contacto (con código de país)', 'maggiore'); ?>
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_whatsapp">
                                        <?php _e('WhatsApp', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="maggiore_whatsapp" 
                                        name="maggiore_whatsapp" 
                                        value="<?php echo esc_attr(get_option('maggiore_whatsapp')); ?>"
                                        class="regular-text"
                                        placeholder="+56912345678"
                                    >
                                    <p class="description">
                                        <?php _e('Número de WhatsApp (solo números, sin espacios ni guiones)', 'maggiore'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- UBICACIÓN -->
                <div class="card" style="margin-bottom: 20px;">
                    <h2 class="title" style="padding: 15px 20px; margin: 0; border-bottom: 1px solid #ddd;">
                        <?php _e('Ubicación', 'maggiore'); ?>
                    </h2>
                    <div style="padding: 20px;">
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_direccion">
                                        <?php _e('Dirección', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="maggiore_direccion" 
                                        name="maggiore_direccion" 
                                        value="<?php echo esc_attr(get_option('maggiore_direccion')); ?>"
                                        class="regular-text"
                                        placeholder="<?php _e('Av. Providencia 123, Of. 456', 'maggiore'); ?>"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_ciudad">
                                        <?php _e('Ciudad', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="maggiore_ciudad" 
                                        name="maggiore_ciudad" 
                                        value="<?php echo esc_attr(get_option('maggiore_ciudad', 'Santiago')); ?>"
                                        class="regular-text"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_pais">
                                        <?php _e('País', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="maggiore_pais" 
                                        name="maggiore_pais" 
                                        value="<?php echo esc_attr(get_option('maggiore_pais', 'Chile')); ?>"
                                        class="regular-text"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_google_maps_url">
                                        <?php _e('URL de Google Maps', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="url" 
                                        id="maggiore_google_maps_url" 
                                        name="maggiore_google_maps_url" 
                                        value="<?php echo esc_url(get_option('maggiore_google_maps_url')); ?>"
                                        class="regular-text"
                                        placeholder="https://maps.google.com/..."
                                    >
                                    <p class="description">
                                        <?php _e('Enlace a tu ubicación en Google Maps', 'maggiore'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- HORARIOS -->
                <div class="card" style="margin-bottom: 20px;">
                    <h2 class="title" style="padding: 15px 20px; margin: 0; border-bottom: 1px solid #ddd;">
                        <?php _e('Horarios de Atención', 'maggiore'); ?>
                    </h2>
                    <div style="padding: 20px;">
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_horario_dias">
                                        <?php _e('Días', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="maggiore_horario_dias" 
                                        name="maggiore_horario_dias" 
                                        value="<?php echo esc_attr(get_option('maggiore_horario_dias', 'Lunes a Viernes')); ?>"
                                        class="regular-text"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_horario_horas">
                                        <?php _e('Horario', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="maggiore_horario_horas" 
                                        name="maggiore_horario_horas" 
                                        value="<?php echo esc_attr(get_option('maggiore_horario_horas', '9:00 - 18:00')); ?>"
                                        class="regular-text"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_horario_nota">
                                        <?php _e('Nota Adicional', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <textarea 
                                        id="maggiore_horario_nota" 
                                        name="maggiore_horario_nota" 
                                        rows="3"
                                        class="large-text"
                                    ><?php echo esc_textarea(get_option('maggiore_horario_nota', 'Respondemos emails en menos de 24 horas hábiles')); ?></textarea>
                                    <p class="description">
                                        <?php _e('Información adicional sobre horarios', 'maggiore'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- REDES SOCIALES -->
                <div class="card" style="margin-bottom: 20px;">
                    <h2 class="title" style="padding: 15px 20px; margin: 0; border-bottom: 1px solid #ddd;">
                        <?php _e('Redes Sociales', 'maggiore'); ?>
                    </h2>
                    <div style="padding: 20px;">
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_instagram_url">
                                        <span class="dashicons dashicons-instagram" style="color: #E4405F;"></span>
                                        <?php _e('Instagram', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="url" 
                                        id="maggiore_instagram_url" 
                                        name="maggiore_instagram_url" 
                                        value="<?php echo esc_url(get_option('maggiore_instagram_url')); ?>"
                                        class="regular-text"
                                        placeholder="https://instagram.com/tu_usuario"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_linkedin_url">
                                        <span class="dashicons dashicons-linkedin" style="color: #0077B5;"></span>
                                        <?php _e('LinkedIn', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="url" 
                                        id="maggiore_linkedin_url" 
                                        name="maggiore_linkedin_url" 
                                        value="<?php echo esc_url(get_option('maggiore_linkedin_url')); ?>"
                                        class="regular-text"
                                        placeholder="https://linkedin.com/company/tu_empresa"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_facebook_url">
                                        <span class="dashicons dashicons-facebook" style="color: #1877F2;"></span>
                                        <?php _e('Facebook', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="url" 
                                        id="maggiore_facebook_url" 
                                        name="maggiore_facebook_url" 
                                        value="<?php echo esc_url(get_option('maggiore_facebook_url')); ?>"
                                        class="regular-text"
                                        placeholder="https://facebook.com/tu_pagina"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_twitter_url">
                                        <span class="dashicons dashicons-twitter" style="color: #1DA1F2;"></span>
                                        <?php _e('Twitter / X', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="url" 
                                        id="maggiore_twitter_url" 
                                        name="maggiore_twitter_url" 
                                        value="<?php echo esc_url(get_option('maggiore_twitter_url')); ?>"
                                        class="regular-text"
                                        placeholder="https://twitter.com/tu_usuario"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_youtube_url">
                                        <span class="dashicons dashicons-video-alt3" style="color: #FF0000;"></span>
                                        <?php _e('YouTube', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="url" 
                                        id="maggiore_youtube_url" 
                                        name="maggiore_youtube_url" 
                                        value="<?php echo esc_url(get_option('maggiore_youtube_url')); ?>"
                                        class="regular-text"
                                        placeholder="https://youtube.com/@tu_canal"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="maggiore_tiktok_url">
                                        <span class="dashicons dashicons-format-video" style="color: #000000;"></span>
                                        <?php _e('TikTok', 'maggiore'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="url" 
                                        id="maggiore_tiktok_url" 
                                        name="maggiore_tiktok_url" 
                                        value="<?php echo esc_url(get_option('maggiore_tiktok_url')); ?>"
                                        class="regular-text"
                                        placeholder="https://tiktok.com/@tu_usuario"
                                    >
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
            </div>
            
            <?php submit_button(__('Guardar Cambios', 'maggiore')); ?>
        </form>
        
        <!-- Información de ayuda -->
        <div class="card" style="max-width: 800px; margin-top: 30px; background: #f0f6fc; border-left: 4px solid #0073aa;">
            <div style="padding: 20px;">
                <h3 style="margin-top: 0;">
                    <span class="dashicons dashicons-info" style="color: #0073aa;"></span>
                    <?php _e('Cómo usar estos datos en tu tema', 'maggiore'); ?>
                </h3>
                <p><?php _e('Para usar estos datos en cualquier template o página, usa las funciones helper:', 'maggiore'); ?></p>
                <code style="display: block; background: white; padding: 15px; border-radius: 4px; margin: 10px 0;">
                    &lt;?php echo maggiore_get_email(); ?&gt;<br>
                    &lt;?php echo maggiore_get_telefono(); ?&gt;<br>
                    &lt;?php echo maggiore_get_whatsapp_link(); ?&gt;<br>
                    &lt;?php maggiore_social_icons(); ?&gt;
                </code>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=maggiore-contact-data-docs'); ?>" class="button">
                        <?php _e('Ver documentación completa', 'maggiore'); ?>
                    </a>
                </p>
            </div>
        </div>
        
    </div>
    
    <?php
}
