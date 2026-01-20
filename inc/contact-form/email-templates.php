<?php
/**
 * Email Templates
 * 
 * Templates HTML para emails del formulario de contacto
 */

if (!defined('ABSPATH')) exit;

/**
 * Template de confirmación para el CLIENTE
 * 
 * @param array $data Datos del contacto
 * @return string HTML del email
 */
function maggiore_get_email_cliente_template($data) {
    $nombre = $data['nombre'];
    $logo_url = get_template_directory_uri() . '/assets/img/logo-white.svg';
    $site_name = get_bloginfo('name');
    $portafolio_url = home_url('/portafolio');
    $instagram_url = 'https://instagram.com/maggiore'; // Configurar
    $linkedin_url = 'https://linkedin.com/company/maggiore'; // Configurar
    
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($site_name); ?></title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
                background: #f4f4f7; 
                padding: 20px;
                line-height: 1.6;
            }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                background: white; 
                border-radius: 12px; 
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            }
            .header { 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 50px 30px; 
                text-align: center; 
            }
            .header img { 
                max-width: 200px;
                height: auto;
            }
            .content { 
                padding: 50px 40px; 
            }
            .content h1 { 
                color: #1a1a1a; 
                font-size: 28px; 
                margin-bottom: 25px;
                font-weight: 700;
            }
            .content p { 
                color: #4a5568; 
                font-size: 16px;
                line-height: 1.8; 
                margin-bottom: 20px;
            }
            .content strong {
                color: #2d3748;
                font-weight: 600;
            }
            .cta-button {
                display: inline-block;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white !important;
                padding: 16px 40px;
                text-decoration: none;
                border-radius: 8px;
                margin: 30px 0;
                font-weight: 600;
                font-size: 16px;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
                transition: transform 0.2s;
            }
            .cta-button:hover {
                transform: translateY(-2px);
            }
            .divider {
                height: 1px;
                background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
                margin: 40px 0;
            }
            .footer { 
                background: #f7fafc; 
                padding: 40px 30px; 
                text-align: center; 
            }
            .footer p {
                color: #718096;
                font-size: 14px;
                margin-bottom: 15px;
            }
            .social-links {
                margin-top: 25px;
            }
            .social-links a {
                color: #667eea;
                text-decoration: none;
                margin: 0 15px;
                font-size: 14px;
                font-weight: 500;
            }
            .social-links a:hover {
                color: #764ba2;
            }
            @media only screen and (max-width: 600px) {
                .content { padding: 30px 25px; }
                .content h1 { font-size: 24px; }
                .header { padding: 40px 20px; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <!-- Header -->
            <div class="header">
                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>">
            </div>
            
            <!-- Contenido -->
            <div class="content">
                <h1>¡Hola <?php echo esc_html($nombre); ?>! 👋</h1>
                
                <p>
                    Gracias por contactarnos. <strong>Hemos recibido tu mensaje correctamente</strong> 
                    y queremos que sepas que ya está en manos de nuestro equipo.
                </p>
                
                <p>
                    Revisaremos tu consulta con atención y te responderemos en las 
                    <strong>próximas 24 horas hábiles</strong>. Mientras tanto, queremos que conozcas 
                    más sobre nuestro trabajo.
                </p>
                
                <center>
                    <a href="<?php echo esc_url($portafolio_url); ?>" class="cta-button">
                        Ver nuestro portafolio
                    </a>
                </center>
                
                <div class="divider"></div>
                
                <p style="font-size: 15px; color: #718096;">
                    <strong>¿Tienes alguna duda urgente?</strong><br>
                    Puedes escribirnos directamente a 
                    <a href="mailto:hola@maggiore.cl" style="color: #667eea; text-decoration: none;">
                        hola@maggiore.cl
                    </a>
                </p>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <p style="font-weight: 600; color: #4a5568; margin-bottom: 5px;">
                    <?php echo esc_html($site_name); ?>
                </p>
                <p style="margin-bottom: 20px;">
                    Marketing Digital que genera resultados
                </p>
                
                <div class="social-links">
                    <a href="<?php echo esc_url($instagram_url); ?>">Instagram</a>
                    <span style="color: #cbd5e0;">•</span>
                    <a href="<?php echo esc_url($linkedin_url); ?>">LinkedIn</a>
                </div>
                
                <p style="margin-top: 30px; font-size: 12px; color: #a0aec0;">
                    © <?php echo date('Y'); ?> <?php echo esc_html($site_name); ?>. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

/**
 * Template de notificación para el ADMIN
 * 
 * @param array $data Datos del contacto
 * @return string HTML del email
 */
function maggiore_get_email_admin_template($data) {
    $nombre = $data['nombre'];
    $cargo = $data['cargo'];
    $correo = $data['correo'];
    $empresa = $data['empresa'];
    $telefono = $data['telefono'];
    $dolor = $data['dolor'];
    $objetivos = $data['objetivos'];
    $post_id = $data['post_id'];
    
    $edit_url = admin_url('post.php?post=' . $post_id . '&action=edit');
    $site_name = get_bloginfo('name');
    
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuevo Contacto - <?php echo esc_html($site_name); ?></title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: #f7fafc; 
                padding: 20px;
                line-height: 1.6;
            }
            .container { 
                max-width: 700px; 
                margin: 0 auto; 
                background: white; 
                border-radius: 10px; 
                padding: 40px;
                box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            }
            .header {
                border-bottom: 3px solid #667eea;
                padding-bottom: 20px;
                margin-bottom: 30px;
            }
            .header h1 {
                margin: 0 0 10px;
                color: #2d3748;
                font-size: 26px;
                font-weight: 700;
            }
            .header .meta {
                color: #718096;
                font-size: 14px;
            }
            .field {
                margin-bottom: 25px;
                padding: 20px;
                background: #f7fafc;
                border-left: 4px solid #667eea;
                border-radius: 4px;
            }
            .field label {
                display: block;
                font-weight: 700;
                color: #667eea;
                margin-bottom: 8px;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .field .value {
                color: #2d3748;
                font-size: 16px;
                word-wrap: break-word;
            }
            .field .value a {
                color: #667eea;
                text-decoration: none;
                font-weight: 500;
            }
            .field .value a:hover {
                text-decoration: underline;
            }
            .priority {
                display: inline-block;
                background: #fc8181;
                color: white;
                padding: 6px 16px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                margin-top: 12px;
            }
            .actions {
                margin-top: 40px;
                padding-top: 30px;
                border-top: 2px solid #e2e8f0;
                text-align: center;
            }
            .btn {
                display: inline-block;
                background: #667eea;
                color: white !important;
                padding: 14px 32px;
                text-decoration: none;
                border-radius: 6px;
                font-weight: 600;
                font-size: 15px;
                margin: 0 8px;
            }
            .btn:hover {
                background: #5568d3;
            }
            .btn-secondary {
                background: #48bb78;
            }
            .btn-secondary:hover {
                background: #38a169;
            }
            .footer-note {
                margin-top: 30px;
                padding: 20px;
                background: #edf2f7;
                border-radius: 6px;
                text-align: center;
                color: #4a5568;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <!-- Header -->
            <div class="header">
                <h1>🔔 Nuevo contacto desde la web</h1>
                <div class="meta">
                    Recibido el <?php echo date('d/m/Y'); ?> a las <?php echo date('H:i'); ?> hrs
                </div>
            </div>
            
            <!-- Datos del contacto -->
            <div class="field">
                <label>Nombre completo</label>
                <div class="value"><?php echo esc_html($nombre); ?></div>
            </div>
            
            <div class="field">
                <label>Correo electrónico</label>
                <div class="value">
                    <a href="mailto:<?php echo esc_attr($correo); ?>">
                        <?php echo esc_html($correo); ?>
                    </a>
                </div>
            </div>
            
            <?php if ($telefono): ?>
            <div class="field">
                <label>Teléfono celular</label>
                <div class="value">
                    <a href="tel:<?php echo esc_attr($telefono); ?>">
                        <?php echo esc_html($telefono); ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($cargo): ?>
            <div class="field">
                <label>Cargo</label>
                <div class="value"><?php echo esc_html($cargo); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if ($empresa): ?>
            <div class="field">
                <label>Empresa</label>
                <div class="value"><?php echo esc_html($empresa); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if ($dolor): ?>
            <div class="field">
                <label>Dolor de la empresa</label>
                <div class="value"><?php echo nl2br(esc_html($dolor)); ?></div>
                <span class="priority">⚠️ Requiere atención</span>
            </div>
            <?php endif; ?>
            
            <?php if ($objetivos): ?>
            <div class="field">
                <label>Objetivos</label>
                <div class="value"><?php echo nl2br(esc_html($objetivos)); ?></div>
            </div>
            <?php endif; ?>
            
            <!-- Acciones -->
            <div class="actions">
                <a href="<?php echo esc_url($edit_url); ?>" class="btn">
                    📝 Ver en WordPress
                </a>
                <a href="mailto:<?php echo esc_attr($correo); ?>" class="btn btn-secondary">
                    ✉️ Responder ahora
                </a>
            </div>
            
            <!-- Nota al pie -->
            <div class="footer-note">
                <strong>💡 Tip:</strong> Responde directamente a este email para contactar al cliente.
                <br>Tu respuesta se enviará automáticamente a <?php echo esc_html($correo); ?>
            </div>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}
