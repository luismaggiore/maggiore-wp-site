<?php
if (!defined('ABSPATH')) exit;

/**
 * Metabox SEO Ultra - Con keywords tipo tags y código custom head
 */
add_action('add_meta_boxes', function () {
    $screens = ['post', 'page', 'mg_cliente', 'mg_caso_exito', 'mg_portafolio', 'mg_equipo', 'mg_servicio', 'mg_area'];

    foreach ($screens as $screen) {
        add_meta_box(
            'mg_seo_metabox',
            __('SEO & Schema', 'maggiore'),
            'mg_render_seo_metabox_ultra',
            $screen,
            'normal',
            'low'
        );
    }
});

function mg_render_seo_metabox_ultra($post) {
    wp_nonce_field('mg_save_seo_meta', 'mg_seo_nonce');

    $seo_title       = get_post_meta($post->ID, 'mg_seo_title', true);
    $seo_description = get_post_meta($post->ID, 'mg_seo_description', true);
    $seo_keywords    = get_post_meta($post->ID, 'mg_seo_keywords', true) ?: [];
    $custom_head     = get_post_meta($post->ID, 'mg_custom_head_code', true);
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
            <input type="text" class="widefat mg-seo-title-input" name="mg_seo_title" value="<?= esc_attr($seo_title); ?>" 
                   placeholder="<?= esc_attr(get_the_title($post->ID)); ?>">
            <small class="description">
                Caracteres: <span class="mg-title-count">0</span>/60 | 
                <?php _e('Óptimo: 50-60 caracteres', 'maggiore'); ?>
            </small>
        </p>

        <!-- Meta Description -->
        <p>
            <label><strong><?php _e('Meta Description', 'maggiore'); ?></strong></label>
            <textarea class="widefat mg-seo-desc-input" rows="3" name="mg_seo_description" 
                      placeholder="<?= esc_attr(wp_trim_words(get_the_excerpt($post->ID), 20)); ?>"><?= esc_textarea($seo_description); ?></textarea>
            <small class="description">
                Caracteres: <span class="mg-desc-count">0</span>/160 | 
                <?php _e('Óptimo: 150-160 caracteres', 'maggiore'); ?>
            </small>
        </p>

        <!-- Meta Keywords - Sistema de Tags -->
        <p>
            <label><strong><?php _e('Meta Keywords', 'maggiore'); ?></strong></label><br>
            
            <!-- Input para agregar keywords -->
            <div class="mg-keywords-input-wrapper" style="border: 1px solid #8c8f94; background: white; padding: 5px; border-radius: 4px; min-height: 40px; cursor: text;">
                <div id="mg-keywords-container" style="display: inline-block; width: 100%;">
                    <?php if (is_array($seo_keywords)): ?>
                        <?php foreach ($seo_keywords as $keyword): ?>
                            <span class="mg-keyword-tag" style="display: inline-block; background: #0073aa; color: white; padding: 4px 8px 4px 12px; border-radius: 3px; margin: 3px; font-size: 12px; cursor: default;">
                                <?= esc_html($keyword); ?>
                                <button type="button" class="mg-remove-keyword" data-keyword="<?= esc_attr($keyword); ?>" style="background: none; border: none; color: white; cursor: pointer; margin-left: 5px; font-weight: bold; padding: 0 4px;">×</button>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <input type="text" 
                           id="mg-keyword-input" 
                           placeholder="<?php _e('Escribe una keyword y presiona Enter...', 'maggiore'); ?>" 
                           style="border: none; outline: none; padding: 5px; font-size: 13px; min-width: 200px; display: inline-block;"
                           autocomplete="off">
                </div>
            </div>
            
            <!-- Hidden inputs para guardar los keywords -->
            <div id="mg-hidden-keywords">
                <?php if (is_array($seo_keywords)): ?>
                    <?php foreach ($seo_keywords as $keyword): ?>
                        <input type="hidden" name="mg_seo_keywords[]" value="<?= esc_attr($keyword); ?>">
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <small class="description" style="display: block; margin-top: 8px;">
                <?php _e('Escribe una keyword y presiona Enter para agregarla. Click en × para eliminar.', 'maggiore'); ?>
            </small>
        </p>

        <hr>

        <!-- Código Personalizado del Head -->
        <p>
            <label><strong><?php _e('Código Personalizado del Head', 'maggiore'); ?> 
                <span style="color: #d63638; font-size: 11px;">(⚠️ Avanzado)</span>
            </strong></label>
            
            <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 12px; margin-bottom: 10px;">
                <p style="margin: 0; font-size: 13px;">
                    ⚠️ <strong>Atención:</strong> Este código se insertará en el <code>&lt;head&gt;</code> de esta página específicamente.
                    Para códigos globales (Meta Pixel, GTM), usa el <a href="<?= admin_url('admin.php?page=mg-tracking-settings'); ?>">panel de Tracking</a>.
                </p>
            </div>
            
            <textarea class="widefat code" rows="6" name="mg_custom_head_code" 
                      placeholder="<?php _e('<!-- Script personalizado, meta tags adicionales, etc. -->', 'maggiore'); ?>"><?= esc_textarea($custom_head); ?></textarea>
            
            <small class="description" style="display: block; margin-top: 8px;">
                <?php _e('Ejemplo: scripts de remarketing específicos, meta tags especiales, etc. No requiere etiquetas &lt;script&gt; si ya vienen incluidas.', 'maggiore'); ?>
            </small>
        </p>

        <hr>

        <!-- Open Graph Image -->
        <p>
            <label><strong><?php _e('Open Graph Image', 'maggiore'); ?></strong></label><br>
            <input type="hidden" name="mg_og_image" id="mg_og_image" value="<?= esc_attr($og_image); ?>">
            <button type="button" class="button mg-upload-og">
                <?php _e('Seleccionar imagen', 'maggiore'); ?>
            </button>
            
            <?php if ($og_image): ?>
                <button type="button" class="button mg-remove-og" style="margin-left: 10px;">
                    <?php _e('Eliminar imagen', 'maggiore'); ?>
                </button>
            <?php endif; ?>
            
            <div class="mg-og-preview" style="margin-top:10px">
                <?php if ($og_image): ?>
                    <img src="<?= esc_url($og_image); ?>" style="max-width:300px; border: 1px solid #ddd; border-radius: 4px;">
                <?php endif; ?>
            </div>
            
            <small class="description" style="display: block; margin-top: 8px;">
                <?php _e('Imagen que aparecerá cuando compartan esta página en redes sociales. Recomendado: 1200x630px', 'maggiore'); ?>
            </small>
        </p>

        <hr>

        <!-- Schema JSON-LD -->
        <div style="background: #f0f9ff; border: 1px solid #0284c7; border-radius: 4px; padding: 12px; margin-bottom: 15px;">
            <p style="margin: 0; font-size: 13px;">
                💡 <strong>Schema automático:</strong> Este sistema genera schema JSON-LD automáticamente según el tipo de contenido. 
                Solo necesitas usar el campo manual si requieres algo muy específico.
            </p>
        </div>

        <div class="mg-schema-tabs" style="border-bottom: 1px solid #ccc;">
            <button type="button" class="mg-schema-tab active" data-tab="auto">
                📊 Vista Previa (Automático)
            </button>
            <button type="button" class="mg-schema-tab" data-tab="manual">
                ✏️ Schema Manual (Avanzado)
            </button>
        </div>

        <div class="mg-schema-content-wrapper" style="border: 1px solid #ccc; border-top: none; padding: 15px; background: white;">
            <!-- Tab: Automático -->
            <div class="mg-schema-content" data-content="auto">
                <pre style="background: #f5f5f5; padding: 15px; border-radius: 4px; font-size: 12px; overflow-x: auto; max-height: 400px;"><?= esc_html(json_encode($auto_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    ℹ️ <?php _e('El schema se generará automáticamente al publicar.', 'maggiore'); ?>
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
                
                <small class="description" style="display: block; margin-top: 10px;">
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
    .mg-keywords-input-wrapper {
        transition: border-color 0.2s ease;
    }
    .mg-keywords-input-wrapper:focus-within {
        border-color: #2271b1 !important;
        box-shadow: 0 0 0 1px #2271b1;
    }
    .mg-keyword-tag {
        transition: all 0.2s ease;
    }
    .mg-keyword-tag:hover {
        background: #005a87 !important;
    }
    .mg-remove-keyword:hover {
        transform: scale(1.2);
    }
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
    }
    .mg-schema-content[data-content="auto"] {
        display: block;
    }
    </style>

    <script>
    jQuery(document).ready(function ($) {
        
        // ==========================================
        // KEYWORDS - Sistema de Tags
        // ==========================================
        
        // Función para agregar un keyword
        function addKeyword(keywordText) {
            keywordText = keywordText.trim();
            
            if (!keywordText) return;
            
            // Verificar si ya existe
            var exists = false;
            $('#mg-hidden-keywords input').each(function() {
                if ($(this).val() === keywordText) {
                    exists = true;
                    return false;
                }
            });
            
            if (exists) {
                alert('Esta keyword ya fue agregada');
                return;
            }
            
            // Crear el tag visual
            var tag = $('<span class="mg-keyword-tag" style="display: inline-block; background: #0073aa; color: white; padding: 4px 8px 4px 12px; border-radius: 3px; margin: 3px; font-size: 12px; cursor: default;">')
                .text(keywordText)
                .append(
                    $('<button type="button" class="mg-remove-keyword" style="background: none; border: none; color: white; cursor: pointer; margin-left: 5px; font-weight: bold; padding: 0 4px;">×</button>')
                    .data('keyword', keywordText)
                );
            
            // Agregar antes del input
            $('#mg-keyword-input').before(tag);
            
            // Agregar hidden input
            $('<input type="hidden" name="mg_seo_keywords[]">')
                .val(keywordText)
                .appendTo('#mg-hidden-keywords');
            
            // Limpiar input
            $('#mg-keyword-input').val('');
        }
        
        // Event listener para el input
        $('#mg-keyword-input').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addKeyword($(this).val());
            }
        });
        
        // Click en el wrapper para enfocar el input
        $('.mg-keywords-input-wrapper').on('click', function() {
            $('#mg-keyword-input').focus();
        });
        
        // Eliminar keyword
        $(document).on('click', '.mg-remove-keyword', function() {
            var keyword = $(this).data('keyword');
            
            // Eliminar tag visual
            $(this).parent().remove();
            
            // Eliminar hidden input
            $('#mg-hidden-keywords input').each(function() {
                if ($(this).val() === keyword) {
                    $(this).remove();
                    return false;
                }
            });
        });

        // ==========================================
        // CHARACTER COUNTERS
        // ==========================================
        
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
        
        $('.mg-seo-title-input').on('input', function() {
            updateCounter(this, '.mg-title-count');
        }).trigger('input');
        
        $('.mg-seo-desc-input').on('input', function() {
            updateCounter(this, '.mg-desc-count');
        }).trigger('input');

        // ==========================================
        // SCHEMA TABS
        // ==========================================
        
        $('.mg-schema-tab').on('click', function() {
            const tab = $(this).data('tab');
            
            $('.mg-schema-tab').removeClass('active');
            $(this).addClass('active');
            
            $('.mg-schema-content').hide();
            $('[data-content="' + tab + '"]').show();
        });

        // ==========================================
        // OG IMAGE UPLOADER
        // ==========================================
        
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
 * Guardar Metabox
 */
add_action('save_post', function ($post_id) {
    if (!isset($_POST['mg_seo_nonce']) || !wp_verify_nonce($_POST['mg_seo_nonce'], 'mg_save_seo_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Campos de texto normales
    $text_fields = [
        'mg_seo_title',
        'mg_seo_description',
        'mg_custom_head_code',
        'mg_og_image',
        'mg_schema_json',
        'mg_seo_noindex'
    ];

    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, $_POST[$field]);
        } else {
            delete_post_meta($post_id, $field);
        }
    }

    // Keywords (array)
    if (isset($_POST['mg_seo_keywords']) && is_array($_POST['mg_seo_keywords'])) {
        $keywords = array_map('sanitize_text_field', $_POST['mg_seo_keywords']);
        $keywords = array_filter($keywords); // Eliminar vacíos
        update_post_meta($post_id, 'mg_seo_keywords', $keywords);
    } else {
        delete_post_meta($post_id, 'mg_seo_keywords');
    }
});

/**
 * Output del código personalizado del head
 */
add_action('wp_head', function () {
    if (!is_singular()) return;
    
    global $post;
    
    $custom_head = get_post_meta($post->ID, 'mg_custom_head_code', true);
    
    if ($custom_head) {
        echo "\n<!-- Código Personalizado del Head -->\n";
        echo $custom_head;
        echo "\n<!-- /Código Personalizado del Head -->\n";
    }
}, 99); // Prioridad alta para que se ejecute después de otros scripts
