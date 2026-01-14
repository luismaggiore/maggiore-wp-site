<?php
if (!defined('ABSPATH')) exit;

add_action('add_meta_boxes', function () {
    add_meta_box(
        'mg_equipo_detalles',
        __('Detalles del Miembro del Equipo', 'maggiore'),
        'mg_equipo_metabox_callback',
        'mg_equipo',
        'normal',
        'default'
    );
});

function mg_equipo_metabox_callback($post) {
    wp_nonce_field('mg_save_equipo_meta', 'mg_equipo_nonce');
    
    $cargo     = get_post_meta($post->ID, 'mg_equipo_cargo', true);
    $area_id   = get_post_meta($post->ID, 'mg_equipo_area', true);
    $especialidades = get_post_meta($post->ID, 'mg_equipo_especialidades', true) ?: [];
    $bio       = get_post_meta($post->ID, 'mg_equipo_bio', true);
    $linkedin  = get_post_meta($post->ID, 'mg_equipo_linkedin', true);
    
    // NUEVOS CAMPOS
    $jefe_directo = get_post_meta($post->ID, 'mg_equipo_jefe_directo', true);
    $email = get_post_meta($post->ID, 'mg_equipo_email', true);

    $lang = function_exists('pll_get_post_language') ? pll_get_post_language($post->ID) : false;

    // Obtener áreas (para worksFor en schema)
    $args = [
        'post_type'   => 'mg_area',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
    ];

    if ($lang) {
        $args['lang'] = $lang;
    }

    $areas = get_posts($args);
    
    // Obtener todos los miembros del equipo (excepto este mismo)
    $args_equipo = [
        'post_type'   => 'mg_equipo',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
        'exclude'     => [$post->ID]
    ];
    
    if ($lang) {
        $args_equipo['lang'] = $lang;
    }
    
    $todos_equipo = get_posts($args_equipo);
    
    // Detectar si esta persona es director de algún área
    $areas_director = mg_is_director($post->ID);

    // Lista predefinida de especialidades
    $especialidades_disponibles = [
        'SEO Técnico',
        'SEO de Contenidos',
        'SEM / Google Ads',
        'Social Media Marketing',
        'Email Marketing',
        'Marketing de Contenidos',
        'Analítica Web',
        'Analítica de Datos',
        'CRO (Optimización de Conversiones)',
        'UX/UI Design',
        'Diseño Gráfico',
        'Diseño de Marca',
        'Branding',
        'Identidad Visual',
        'Desarrollo Web',
        'Desarrollo Frontend',
        'Desarrollo Backend',
        'WordPress',
        'E-commerce',
        'Shopify',
        'Copywriting',
        'Redacción Publicitaria',
        'Content Strategy',
        'Video Marketing',
        'Motion Graphics',
        'Fotografía',
        'Ilustración',
        'Gestión de Proyectos',
        'Consultoría Estratégica',
        'Transformación Digital',
        'Automatización de Marketing',
        'CRM',
        'Inbound Marketing',
        'Growth Hacking'
    ];

    // Ordenar alfabéticamente
    sort($especialidades_disponibles);
    ?>

    <div class="mg-equipo-metabox">
        
        <?php if ($areas_director): ?>
        <!-- Notificación de Director -->
        <div class="notice notice-info inline" style="margin: 0 0 20px 0; padding: 12px;">
            <p style="margin: 0;">
                <strong>📊 Esta persona es Director de:</strong>
            </p>
            <ul style="margin: 8px 0 0 20px;">
                <?php foreach ($areas_director as $area_dir): ?>
                    <li>
                        <strong><?= esc_html($area_dir->post_title); ?></strong>
                        <a href="<?= get_edit_post_link($area_dir->ID); ?>" target="_blank" style="text-decoration: none;">
                            (Editar área →)
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p style="margin: 8px 0 0 0; font-size: 12px; color: #666;">
                💡 Como director, este rol tendrá validación especial en el Schema.org
            </p>
        </div>
        <?php endif; ?>
        
        <!-- Cargo -->
        <p>
            <label><strong><?php _e('Cargo del miembro', 'maggiore'); ?></strong></label><br>
            <input type="text" 
                   name="mg_equipo_cargo" 
                   value="<?= esc_attr($cargo); ?>" 
                   class="widefat"
                   placeholder="<?php _e('ej: Especialista en SEO', 'maggiore'); ?>">
        </p>
        
        <!-- Email Corporativo (NUEVO) -->
        <p>
            <label><strong><?php _e('Email corporativo', 'maggiore'); ?></strong></label><br>
            <input type="email" 
                   name="mg_equipo_email" 
                   value="<?= esc_attr($email); ?>" 
                   class="widefat"
                   placeholder="nombre@empresa.com">
            <small class="description">
                <?php _e('Email que aparecerá en el Schema.org (mejora la verificación de identidad).', 'maggiore'); ?>
            </small>
        </p>

        <!-- Área (para estructura organizacional) -->
        <p>
            <label><strong><?php _e('Área asignada', 'maggiore'); ?></strong></label><br>
            <select name="mg_equipo_area" class="widefat">
                <option value="">— <?php _e('Sin asignar', 'maggiore'); ?> —</option>
                <?php foreach ($areas as $area): ?>
                    <option value="<?= $area->ID; ?>" <?= selected($area_id, $area->ID, false); ?>>
                        <?= esc_html($area->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="description">
                <?php _e('Área organizacional del equipo (ej: Marketing, Desarrollo, Diseño)', 'maggiore'); ?>
            </small>
        </p>
        
        <!-- Jefe Directo (NUEVO) -->
        <p>
            <label><strong><?php _e('Jefe directo (reporta a)', 'maggiore'); ?></strong></label>
            
            <?php if ($areas_director): ?>
                <br><small style="color: #d63638; display: block; margin-bottom: 8px;">
                    ⚠️ <?php _e('Como director, generalmente no tienes jefe directo (o reportas al CEO/Fundador)', 'maggiore'); ?>
                </small>
            <?php endif; ?>
            
            <select name="mg_equipo_jefe_directo" id="mg_equipo_jefe_directo" class="widefat">
                <option value="">— <?php _e('Sin jefe directo', 'maggiore'); ?> —</option>
                <?php 
                foreach ($todos_equipo as $jefe): 
                    $cargo_jefe = get_post_meta($jefe->ID, 'mg_equipo_cargo', true);
                    $es_director_jefe = mg_is_director($jefe->ID);
                    
                    $etiqueta = $jefe->post_title;
                    if ($cargo_jefe) {
                        $etiqueta .= ' (' . $cargo_jefe . ')';
                    }
                    if ($es_director_jefe) {
                        $etiqueta .= ' 📊 Director';
                    }
                ?>
                    <option value="<?= $jefe->ID; ?>" <?= selected($jefe_directo, $jefe->ID, false); ?>>
                        <?= esc_html($etiqueta); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <small class="description">
                <?php _e('Persona a quien reporta directamente en la organización. Esto valida la jerarquía en Schema.org.', 'maggiore'); ?>
            </small>
        </p>

        <!-- Áreas de Especialización -->
        <p>
            <label><strong><?php _e('Áreas de Especialización', 'maggiore'); ?> *</strong></label><br>
            
            <!-- Input para agregar tags -->
            <div class="mg-tags-input-wrapper" style="border: 1px solid #8c8f94; background: white; padding: 5px; border-radius: 4px; min-height: 40px; cursor: text;">
                <div id="mg-tags-container" style="display: inline-block; width: 100%;">
                    <?php foreach ($especialidades as $esp): ?>
                        <span class="mg-tag" style="display: inline-block; background: #0073aa; color: white; padding: 4px 8px 4px 12px; border-radius: 3px; margin: 3px; font-size: 12px; cursor: default;">
                            <?= esc_html($esp); ?>
                            <button type="button" class="mg-remove-tag" data-tag="<?= esc_attr($esp); ?>" style="background: none; border: none; color: white; cursor: pointer; margin-left: 5px; font-weight: bold; padding: 0 4px;">×</button>
                        </span>
                    <?php endforeach; ?>
                    <input type="text" 
                           id="mg-tag-input" 
                           placeholder="<?php _e('Escribe y presiona Enter...', 'maggiore'); ?>" 
                           style="border: none; outline: none; padding: 5px; font-size: 13px; min-width: 200px; display: inline-block;"
                           autocomplete="off">
                </div>
            </div>
            
            <!-- Hidden inputs para guardar los tags -->
            <div id="mg-hidden-tags">
                <?php foreach ($especialidades as $esp): ?>
                    <input type="hidden" name="mg_equipo_especialidades[]" value="<?= esc_attr($esp); ?>">
                <?php endforeach; ?>
            </div>
            
            <small class="description" style="display: block; margin-top: 8px;">
                <?php _e('Escribe una especialidad y presiona Enter para agregarla. Click en × para eliminar.', 'maggiore'); ?>
            </small>
            
            <!-- Sugerencias comunes -->
            <div style="margin-top: 10px;">
                <small style="color: #666;"><?php _e('Sugerencias:', 'maggiore'); ?></small><br>
                <div id="mg-suggestions" style="margin-top: 5px;">
                    <?php 
                    $sugerencias = array_slice($especialidades_disponibles, 0, 10);
                    foreach ($sugerencias as $sug): 
                        if (!in_array($sug, $especialidades)):
                    ?>
                        <button type="button" 
                                class="mg-add-suggestion button button-small" 
                                data-tag="<?= esc_attr($sug); ?>"
                                style="margin: 2px;">
                            + <?= esc_html($sug); ?>
                        </button>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </p>

        <!-- Biografía -->
        <p>
            <label><strong><?php _e('Biografía breve', 'maggiore'); ?></strong></label><br>
            <textarea name="mg_equipo_bio" 
                      rows="4" 
                      class="widefat"
                      placeholder="<?php _e('Descripción profesional del miembro...', 'maggiore'); ?>"><?= esc_textarea($bio); ?></textarea>
            <small class="description">
                <?php _e('Esta biografía aparecerá en el perfil público y en el schema de la persona.', 'maggiore'); ?>
            </small>
        </p>

        <!-- LinkedIn -->
        <p>
            <label><strong><?php _e('Enlace a LinkedIn', 'maggiore'); ?></strong></label><br>
            <input type="url" 
                   name="mg_equipo_linkedin" 
                   value="<?= esc_url($linkedin); ?>" 
                   class="widefat"
                   placeholder="https://linkedin.com/in/usuario">
            <small class="description">
                <?php _e('LinkedIn es clave para validación de credibilidad en el schema.', 'maggiore'); ?>
            </small>
        </p>

        <!-- Información de ayuda -->
        <div style="background: #f0f9ff; border-left: 4px solid #0284c7; padding: 12px; margin-top: 20px;">
            <p style="margin: 0; font-size: 13px;">
                <strong>💡 <?php _e('Tip de SEO:', 'maggiore'); ?></strong>
                <?php _e('Las especialidades alimentan el campo "knowsAbout" en Schema.org, mejorando tu expertise en Google.', 'maggiore'); ?>
            </p>
            <?php if ($areas_director): ?>
            <p style="margin: 8px 0 0 0; font-size: 13px;">
                <strong>📊 <?php _e('Schema de Director:', 'maggiore'); ?></strong>
                <?php _e('Como director, tu schema incluirá validación organizacional adicional (memberOf, hasOccupation) que refuerza tu autoridad aunque tengas menos portafolio que otros miembros.', 'maggiore'); ?>
            </p>
            <?php endif; ?>
        </div>

    </div>

    <style>
    .mg-equipo-metabox p {
        margin-bottom: 20px;
    }
    .mg-equipo-metabox .description {
        display: block;
        margin-top: 5px;
        font-style: italic;
        color: #666;
    }
    .mg-tags-input-wrapper {
        transition: border-color 0.2s ease;
    }
    .mg-tags-input-wrapper:focus-within {
        border-color: #2271b1 !important;
        box-shadow: 0 0 0 1px #2271b1;
    }
    .mg-tag {
        transition: all 0.2s ease;
    }
    .mg-tag:hover {
        background: #005a87 !important;
    }
    .mg-remove-tag:hover {
        transform: scale(1.2);
    }
    .mg-add-suggestion {
        transition: all 0.2s ease;
    }
    .mg-add-suggestion:hover {
        background: #0073aa !important;
        color: white !important;
        border-color: #0073aa !important;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Función para agregar un tag
        function addTag(tagText) {
            tagText = tagText.trim();
            
            if (!tagText) return;
            
            // Verificar si ya existe
            var exists = false;
            $('#mg-hidden-tags input').each(function() {
                if ($(this).val() === tagText) {
                    exists = true;
                    return false;
                }
            });
            
            if (exists) {
                $('#mg-tag-input').val('');
                return;
            }
            
            // Crear el tag visual
            var tagHtml = '<span class="mg-tag" style="display: inline-block; background: #0073aa; color: white; padding: 4px 8px 4px 12px; border-radius: 3px; margin: 3px; font-size: 12px; cursor: default; animation: slideIn 0.2s ease;">' +
                tagText +
                '<button type="button" class="mg-remove-tag" data-tag="' + tagText + '" style="background: none; border: none; color: white; cursor: pointer; margin-left: 5px; font-weight: bold; padding: 0 4px;">×</button>' +
                '</span>';
            
            // Agregar antes del input
            $('#mg-tag-input').before(tagHtml);
            
            // Agregar hidden input
            $('#mg-hidden-tags').append('<input type="hidden" name="mg_equipo_especialidades[]" value="' + tagText + '">');
            
            // Limpiar input
            $('#mg-tag-input').val('').focus();
            
            // Ocultar sugerencia si existe
            $('.mg-add-suggestion[data-tag="' + tagText + '"]').hide();
        }
        
        // Función para remover un tag
        function removeTag(tagText) {
            // Remover tag visual
            $('.mg-remove-tag[data-tag="' + tagText + '"]').parent().remove();
            
            // Remover hidden input
            $('#mg-hidden-tags input[value="' + tagText + '"]').remove();
            
            // Mostrar sugerencia si existe
            $('.mg-add-suggestion[data-tag="' + tagText + '"]').show();
        }
        
        // Enter para agregar tag
        $('#mg-tag-input').on('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                addTag($(this).val());
            }
            
            // Backspace en input vacío elimina último tag
            if ((e.key === 'Backspace' || e.keyCode === 8) && $(this).val() === '') {
                e.preventDefault();
                var lastTag = $('.mg-tag').last().find('.mg-remove-tag');
                if (lastTag.length) {
                    removeTag(lastTag.data('tag'));
                }
            }
        });
        
        // Click en × para remover
        $(document).on('click', '.mg-remove-tag', function() {
            removeTag($(this).data('tag'));
        });
        
        // Click en sugerencia para agregar
        $(document).on('click', '.mg-add-suggestion', function() {
            addTag($(this).data('tag'));
        });
        
        // Click en el contenedor enfoca el input
        $('.mg-tags-input-wrapper').on('click', function(e) {
            if (e.target === this || $(e.target).closest('#mg-tags-container').length) {
                $('#mg-tag-input').focus();
            }
        });
        
        // Validación de jefe directo (evitar seleccionarse a sí mismo)
        $('#mg_equipo_jefe_directo').on('change', function() {
            var postId = <?= $post->ID ?: 0; ?>;
            var selectedId = parseInt($(this).val());
            
            if (selectedId === postId) {
                alert('<?php _e('No puedes seleccionarte a ti mismo como jefe directo.', 'maggiore'); ?>');
                $(this).val('');
            }
        });
    });
    </script>
    <?php
}

add_action('save_post_mg_equipo', function ($post_id) {
    // Verificar nonce
    if (!isset($_POST['mg_equipo_nonce']) || !wp_verify_nonce($_POST['mg_equipo_nonce'], 'mg_save_equipo_meta')) {
        return;
    }
    
    // Verificar autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Guardar campos existentes
    update_post_meta($post_id, 'mg_equipo_cargo', sanitize_text_field($_POST['mg_equipo_cargo'] ?? ''));
    update_post_meta($post_id, 'mg_equipo_area', intval($_POST['mg_equipo_area'] ?? 0));
    
    // Guardar especialidades como array
    $especialidades = isset($_POST['mg_equipo_especialidades']) && is_array($_POST['mg_equipo_especialidades'])
        ? array_map('sanitize_text_field', $_POST['mg_equipo_especialidades'])
        : [];
    
    // Eliminar vacíos y duplicados
    $especialidades = array_filter($especialidades);
    $especialidades = array_unique($especialidades);
    
    update_post_meta($post_id, 'mg_equipo_especialidades', $especialidades);
    
    update_post_meta($post_id, 'mg_equipo_bio', sanitize_textarea_field($_POST['mg_equipo_bio'] ?? ''));
    update_post_meta($post_id, 'mg_equipo_linkedin', esc_url_raw($_POST['mg_equipo_linkedin'] ?? ''));
    
    // NUEVOS CAMPOS
    $jefe_directo = intval($_POST['mg_equipo_jefe_directo'] ?? 0);
    $email = sanitize_email($_POST['mg_equipo_email'] ?? '');
    
    // Validación: No puede ser su propio jefe
    if ($jefe_directo === $post_id) {
        $jefe_directo = 0;
    }
    
    // Validación: Detectar ciclos jerárquicos
    if ($jefe_directo && mg_tiene_ciclo_jerarquico($post_id, $jefe_directo)) {
        // Si hay ciclo, no guardar el jefe
        $jefe_directo = 0;
        set_transient('mg_equipo_error_' . $post_id, 
            __('No se pudo guardar el jefe directo: esto crearía un ciclo jerárquico (A reporta a B, B reporta a A).', 'maggiore'), 
            30
        );
    }
    
    update_post_meta($post_id, 'mg_equipo_jefe_directo', $jefe_directo);
    update_post_meta($post_id, 'mg_equipo_email', $email);
});

// Mostrar errores de validación
add_action('admin_notices', function() {
    global $post;
    
    if (!$post || $post->post_type !== 'mg_equipo') {
        return;
    }
    
    $error = get_transient('mg_equipo_error_' . $post->ID);
    if ($error) {
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($error) . '</p></div>';
        delete_transient('mg_equipo_error_' . $post->ID);
    }
});
