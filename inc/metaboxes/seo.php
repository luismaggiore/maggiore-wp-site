<?php
if (!defined('ABSPATH')) exit;

/**
 * Registrar Metabox SEO
 */
add_action('add_meta_boxes', function () {
    $screens = ['post', 'page', 'mg_cliente', 'mg_caso_exito', 'mg_portafolio', 'mg_equipo', 'mg_servicio', 'mg_area'];

    foreach ($screens as $screen) {
        add_meta_box(
            'mg_seo_metabox',
            __('SEO & Schema', 'maggiore'),
            'mg_render_seo_metabox',
            $screen,
            'normal',
            'low'
        );
    }
});

/**
 * Render Metabox
 */
function mg_render_seo_metabox($post) {
    wp_nonce_field('mg_save_seo_meta', 'mg_seo_nonce');

    $seo_title       = get_post_meta($post->ID, 'mg_seo_title', true);
    $seo_description = get_post_meta($post->ID, 'mg_seo_description', true);
    $og_image        = get_post_meta($post->ID, 'mg_og_image', true);
    $schema_json     = get_post_meta($post->ID, 'mg_schema_json', true);
    $noindex         = get_post_meta($post->ID, 'mg_seo_noindex', true);
    ?>

    <p>
        <label><strong><?php _e('Meta Title', 'maggiore'); ?></strong></label>
        <input type="text" class="widefat" name="mg_seo_title" value="<?= esc_attr($seo_title); ?>">
    </p>

    <p>
        <label><strong><?php _e('Meta Description', 'maggiore'); ?></strong></label>
        <textarea class="widefat" rows="3" name="mg_seo_description"><?= esc_textarea($seo_description); ?></textarea>
    </p>

    <p>
        <label><strong><?php _e('Open Graph Image', 'maggiore'); ?></strong></label><br>
        <input type="hidden" name="mg_og_image" id="mg_og_image" value="<?= esc_attr($og_image); ?>">
        <button type="button" class="button mg-upload-og">
            <?php _e('Seleccionar imagen', 'maggiore'); ?>
        </button>
        <div style="margin-top:10px">
            <?php if ($og_image): ?>
                <img src="<?= esc_url($og_image); ?>" style="max-width:200px;">
            <?php endif; ?>
        </div>
    </p>

    <p>
        <label><strong><?php _e('Schema JSON-LD (opcional)', 'maggiore'); ?></strong></label>
        <textarea class="widefat" rows="10" name="mg_schema_json" placeholder='{ "@context": "https://schema.org", "@type": "Article" }'><?= esc_textarea($schema_json); ?></textarea>
        <small><?php _e('Pega aquí el JSON-LD completo. Si está vacío, se usará schema automático.', 'maggiore'); ?></small>
    </p>

    <p>
        <label>
            <input type="checkbox" name="mg_seo_noindex" value="1" <?= checked($noindex, '1', false); ?>>
            <?php _e('No indexar esta página', 'maggiore'); ?>
        </label>
    </p>

    <script>
    jQuery(document).ready(function ($) {
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
                $('.mg-upload-og').next().html('<img src="'+attachment.url+'" style="max-width:200px;">');
            });

            frame.open();
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

    $fields = [
        'mg_seo_title',
        'mg_seo_description',
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


