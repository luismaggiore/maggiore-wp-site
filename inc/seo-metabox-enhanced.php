<?php
if (!defined('ABSPATH')) exit;

/**
 * Mejora del Metabox SEO con Preview de Schema Automático
 */
add_action('add_meta_boxes', function () {
    $screens = ['post', 'page', 'mg_cliente', 'mg_caso_exito', 'mg_portafolio', 'mg_equipo', 'mg_servicio', 'mg_area'];

    foreach ($screens as $screen) {
        add_meta_box(
            'mg_seo_metabox',
            __('SEO & Schema', 'maggiore'),
            'mg_render_seo_metabox_enhanced',
            $screen,
            'normal',
            'low'
        );
    }
});

function mg_render_seo_metabox_enhanced($post) {
    wp_nonce_field('mg_save_seo_meta', 'mg_seo_nonce');

    $seo_title       = get_post_meta($post->ID, 'mg_seo_title', true);
    $seo_description = get_post_meta($post->ID, 'mg_seo_description', true);
    $seo_keywords    = get_post_meta($post->ID, 'mg_seo_keywords', true);
    $og_image        = get_post_meta($post->ID, 'mg_og_image', true);
    $schema_json     = get_post_meta($post->ID, 'mg_schema_json', true);
    $noindex         = get_post_meta($post->ID, 'mg_seo_noindex', true);
    
    // Generar schema automático para preview
    $auto_schema = mg_generate_auto_schema($post->ID);
    ?>

    <div class="mg-seo-metabox">
        
        <!-- Meta Title -->
        <p>
            <label><strong><?php _e('Meta Title', 'maggiore'); ?></strong></label>
            <input type="text" class="widefat" name="mg_seo_title" value="<?= esc_attr($seo_title); ?>" 
                   placeholder="<?= esc_attr(get_the_title($post->ID)); ?>">
            <small class="description">
                Caracteres: <span class="mg-title-count">0</span>/60 | 
                <?php _e('Óptimo: 50-60 caracteres', 'maggiore'); ?>
            </small>
        </p>

        <!-- Meta Description -->
        <p>
            <label><strong><?php _e('Meta Description', 'maggiore'); ?></strong></label>
            <textarea class="widefat" rows="3" name="mg_seo_description" 
                      placeholder="<?= esc_attr(wp_trim_words(get_the_excerpt($post->ID), 20)); ?>"><?= esc_textarea($seo_description); ?></textarea>
            <small class="description">
                Caracteres: <span class="mg-desc-count">0</span>/160 | 
                <?php _e('Óptimo: 150-160 caracteres', 'maggiore'); ?>
            </small>
        </p>

        <!-- Meta Keywords -->
        <p>
            <label><strong><?php _e('Meta Keywords', 'maggiore'); ?></strong></label>
            <input type="text" class="widefat" name="mg_seo_keywords" value="<?= esc_attr($seo_keywords); ?>" 
                   placeholder="<?php _e('ejemplo: marketing digital, SEO, estrategia', 'maggiore'); ?>">
            <small class="description">
                <?php _e('Separa tus palabras clave con comas. Ejemplo: marketing digital, SEO, estrategia', 'maggiore'); ?>
            </small>
        </p>

        <!-- Open Graph Image -->
        <p>
            <label><strong><?php _e('Open Graph Image', 'maggiore'); ?></strong></label><br>
            <input type="hidden" name="mg_og_image" id="mg_og_image" value="<?= esc_attr($og_image); ?>">
            <button type="button" class="button mg-upload-og">
                <?php _e('Seleccionar imagen', 'maggiore'); ?>
            </button>
            <button type="button" class="button mg-remove-og" style="<?= $og_image ? '' : 'display:none;' ?>">
                <?php _e('Quitar', 'maggiore'); ?>
            </button>
            <div class="mg-og-preview" style="margin-top:10px">
                <?php if ($og_image): ?>
                    <img src="<?= esc_url($og_image); ?>" style="max-width:300px; border: 1px solid #ddd; border-radius: 4px;">
                <?php elseif (has_post_thumbnail($post->ID)): ?>
                    <?php the_post_thumbnail('medium', ['style' => 'max-width:300px; border: 1px solid #ddd; border-radius: 4px; opacity: 0.6;']); ?>
                    <small class="description"><?php _e('Se usará la imagen destacada si no seleccionas una OG image', 'maggiore'); ?></small>
                <?php endif; ?>
            </div>
        </p>

        <hr>

        <!-- Schema JSON-LD -->
        <div class="mg-schema-section">
            <p>
                <label><strong><?php _e('Schema JSON-LD', 'maggiore'); ?></strong></label>
            </p>

            <!-- Tabs -->
            <div class="mg-schema-tabs" style="border-bottom: 1px solid #ccc; margin-bottom: 15px;">
                <button type="button" class="mg-schema-tab active" data-tab="auto">
                    <?php _e('🤖 Automático (Recomendado)', 'maggiore'); ?>
                </button>
                <button type="button" class="mg-schema-tab" data-tab="manual">
                    <?php _e('✍️ Manual', 'maggiore'); ?>
                </button>
            </div>

            <!-- Tab: Automático -->
            <div class="mg-schema-content" data-content="auto" style="display: block;">
                <div style="background: #f0f9ff; border: 1px solid #0284c7; border-radius: 4px; padding: 15px; margin-bottom: 15px;">
                    <p style="margin: 0 0 10px 0;">
                        <strong>✅ Schema Automático Generado</strong>
                    </p>
                    <p style="margin: 0; font-size: 13px; color: #666;">
                        Este schema se genera automáticamente usando las relaciones de tu contenido:
                    </p>
                    <?php 
                    $post_type = get_post_type($post->ID);
                    $schema_info = [
                        'post' => '✓ Autor verificable con casos de éxito | ✓ Trabajo documentado del autor',
                        'mg_equipo' => '✓ Casos de éxito participados | ✓ Artículos escritos | ✓ LinkedIn',
                        'mg_caso_exito' => '✓ Cliente real | ✓ Equipo verificable | ✓ Review del contratador',
                        'mg_cliente' => '✓ Proyectos documentados | ✓ Industria',
                        'mg_servicio' => '✓ Casos de éxito donde se aplicó',
                        'mg_portafolio' => '✓ Cliente | ✓ Equipo participante'
                    ];
                    ?>
                    <p style="margin: 10px 0 0 0; font-size: 12px; color: #0284c7;">
                        <?= isset($schema_info[$post_type]) ? $schema_info[$post_type] : '✓ Datos básicos del contenido'; ?>
                    </p>
                </div>

                <!-- Preview del Schema -->
                <details style="margin-bottom: 15px;">
                    <summary style="cursor: pointer; font-weight: 600; padding: 10px; background: #f5f5f5; border-radius: 4px;">
                        👁️ <?php _e('Ver Schema Generado', 'maggiore'); ?>
                    </summary>
                    <pre style="background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px; max-height: 400px; margin-top: 10px;"><?= json_encode($auto_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?></pre>
                </details>

                <!-- Validación de datos faltantes -->
                <?php
                $warnings = [];
                if ($post_type === 'post') {
                    if (!get_post_meta($post->ID, 'mg_blog_autor', true)) {
                        $warnings[] = '⚠️ No hay autor asignado. Asigna un miembro del equipo como autor.';
                    }
                }
                if ($post_type === 'mg_equipo') {
                    if (!get_post_meta($post->ID, 'mg_equipo_linkedin', true)) {
                        $warnings[] = '💡 Agrega LinkedIn para mayor credibilidad.';
                    }
                    if (!has_post_thumbnail($post->ID)) {
                        $warnings[] = '📸 Agrega una foto del miembro.';
                    }
                }
                if ($post_type === 'mg_caso_exito') {
                    if (!get_post_meta($post->ID, 'mg_caso_contratador_nombre', true)) {
                        $warnings[] = '👤 Agrega datos del contratador para validación.';
                    }
                }
                
                if (!empty($warnings)): ?>
                    <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 12px; margin-bottom: 15px;">
                        <strong>Recomendaciones para mejorar SEO:</strong>
                        <ul style="margin: 10px 0 0 20px; font-size: 13px;">
                            <?php foreach ($warnings as $warning): ?>
                                <li><?= $warning; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <p style="font-size: 13px; color: #666; margin: 0;">
                    <?php _e('No necesitas hacer nada. El schema se generará automáticamente al publicar.', 'maggiore'); ?>
                </p>
            </div>

            <!-- Tab: Manual -->
            <div class="mg-schema-content" data-content="manual" style="display: none;">
                <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 4px; padding: 12px; margin-bottom: 15px;">
                    <p style="margin: 0; font-size: 13px;">
                        ⚠️ <strong>Avanzado:</strong> Solo usa esto si necesitas sobrescribir el schema automático.
                    </p>
                </div>
                
                <textarea class="widefat code" rows="12" name="mg_schema_json" 
                          placeholder='{ "@context": "https://schema.org", "@type": "Article" }'><?= esc_textarea($schema_json); ?></textarea>
                
                <p style="margin-top: 10px;">
                    <button type="button" class="button" onclick="this.nextElementSibling.value = document.querySelector('[data-content=auto] pre').textContent; return false;">
                        📋 Copiar schema automático como base
                    </button>
                    <textarea style="display:none;"></textarea>
                </p>
                
                <small class="description">
                    <?php _e('Si está vacío, se usará schema automático. Si hay contenido aquí, se usará este en lugar del automático.', 'maggiore'); ?>
                </small>
            </div>
        </div>

        <hr>

        <!-- NoIndex -->
        <p>
            <label>
                <input type="checkbox" name="mg_seo_noindex" value="1" <?= checked($noindex, '1', false); ?>>
                <?php _e('No indexar esta página (excluir de Google)', 'maggiore'); ?>
            </label>
        </p>
    </div>

    <style>
    .mg-schema-tabs {
        display: flex;
        gap: 5px;
        padding-bottom: 0;
    }
    .mg-schema-tab {
        background: #f5f5f5;
        border: 1px solid #ccc;
        border-bottom: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 4px 4px 0 0;
        font-weight: 500;
    }
    .mg-schema-tab.active {
        background: white;
        border-bottom: 1px solid white;
        margin-bottom: -1px;
        color: #0073aa;
    }
    .mg-schema-content {
        display: none;
        padding: 15px 0;
    }
    .mg-schema-content[data-content="auto"] {
        display: block;
    }
    </style>

    <script>
    jQuery(document).ready(function ($) {
        // Character counters
        function updateCounter(textarea, counter) {
            const count = $(textarea).val().length;
            $(counter).text(count);
            
            const maxChars = counter.includes('title') ? 60 : 160;
            if (count > maxChars) {
                $(counter).css('color', 'red');
            } else if (count > maxChars * 0.8) {
                $(counter).css('color', 'orange');
            } else {
                $(counter).css('color', 'green');
            }
        }
        
        $('[name="mg_seo_title"]').on('input', function() {
            updateCounter(this, '.mg-title-count');
        }).trigger('input');
        
        $('[name="mg_seo_description"]').on('input', function() {
            updateCounter(this, '.mg-desc-count');
        }).trigger('input');

        // Schema tabs
        $('.mg-schema-tab').on('click', function() {
            const tab = $(this).data('tab');
            
            $('.mg-schema-tab').removeClass('active');
            $(this).addClass('active');
            
            $('.mg-schema-content').hide();
            $('[data-content="' + tab + '"]').show();
        });

        // OG Image uploader
        $('.mg-upload-og').on('click', function (e) {
            e.preventDefault();
            const frame = wp.media({
                title: 'Seleccionar imagen OG',
                button: { text: 'Usar imagen' },
                multiple: false
            });

            frame.on('select', function () {
                const attachment = frame.state().get('selection').first().toJSON();
                $('#mg_og_image').val(attachment.url);
                $('.mg-og-preview').html('<img src="'+attachment.url+'" style="max-width:300px; border: 1px solid #ddd; border-radius: 4px;">');
                $('.mg-remove-og').show();
            });

            frame.open();
        });

        // Remove OG image
        $('.mg-remove-og').on('click', function(e) {
            e.preventDefault();
            $('#mg_og_image').val('');
            $('.mg-og-preview').html('');
            $(this).hide();
        });
    });
    </script>
<?php
}

/**
 * Guardar Metabox (sin cambios)
 */
add_action('save_post', function ($post_id) {
    if (!isset($_POST['mg_seo_nonce']) || !wp_verify_nonce($_POST['mg_seo_nonce'], 'mg_save_seo_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = [
        'mg_seo_title',
        'mg_seo_description',
        'mg_seo_keywords',
        'mg_og_image',
        'mg_schema_json',
        'mg_seo_noindex'
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, $_POST[$field]);
        } else {
            delete_post_meta($post_id, $field);
        }
    }
});
