<?php
/**
 * Custom Post Type: Contactos
 * 
 * Registra CPT para almacenar contactos del formulario
 * No permite creación manual, solo desde el formulario
 */

if (!defined('ABSPATH')) exit;

add_action('init', function() {
    register_post_type('mg_contacto', [
        'label' => __('Contactos', 'maggiore'),
        'labels' => [
            'name' => __('Contactos', 'maggiore'),
            'singular_name' => __('Contacto', 'maggiore'),
            'menu_name' => __('Contactos', 'maggiore'),
            'all_items' => __('Todos los contactos', 'maggiore'),
            'view_item' => __('Ver contacto', 'maggiore'),
            'search_items' => __('Buscar contacto', 'maggiore'),
            'not_found' => __('No se encontraron contactos', 'maggiore'),
        ],
        'description' => __('Gestión de contactos recibidos desde formularios', 'maggiore'),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 26,
        'menu_icon' => 'dashicons-email-alt',
        'supports' => ['title'],
        'capability_type' => 'post',
        'capabilities' => [
            'create_posts' => 'do_not_allow', // Deshabilitar "Añadir nuevo"
        ],
        'map_meta_cap' => true,
        'has_archive' => false,
        'rewrite' => false,
        'show_in_rest' => false,
    ]);
});

/**
 * Personalizar columnas en el listado de contactos
 */
add_filter('manage_mg_contacto_posts_columns', function($columns) {
    $new_columns = [
        'cb' => $columns['cb'],
        'title' => __('Nombre', 'maggiore'),
        'empresa' => __('Empresa', 'maggiore'),
        'correo' => __('Correo', 'maggiore'),
        'telefono' => __('Teléfono', 'maggiore'),
        'origen' => __('Origen', 'maggiore'),
        'date' => __('Fecha', 'maggiore'),
    ];
    
    return $new_columns;
});

/**
 * Rellenar las columnas personalizadas
 */
add_action('manage_mg_contacto_posts_custom_column', function($column, $post_id) {
    switch ($column) {
        case 'empresa':
            $empresa = get_post_meta($post_id, 'correo_empresa', true);
            echo $empresa ? esc_html($empresa) : '—';
            break;
            
        case 'correo':
            $correo = get_post_meta($post_id, 'correo_email', true);
            if ($correo) {
                echo '<a href="mailto:' . esc_attr($correo) . '">' . esc_html($correo) . '</a>';
            } else {
                echo '—';
            }
            break;
            
        case 'telefono':
            $telefono = get_post_meta($post_id, 'correo_telefono', true);
            if ($telefono) {
                echo '<a href="tel:' . esc_attr($telefono) . '">' . esc_html($telefono) . '</a>';
            } else {
                echo '—';
            }
            break;
            
        case 'origen':
            $origen = get_post_meta($post_id, 'correo_origen', true);
            echo '<span class="badge" style="background: #667eea; color: white; padding: 3px 8px; border-radius: 3px; font-size: 11px;">';
            echo $origen ? esc_html($origen) : 'Web';
            echo '</span>';
            break;
    }
}, 10, 2);

/**
 * Hacer columnas ordenables
 */
add_filter('manage_edit-mg_contacto_sortable_columns', function($columns) {
    $columns['empresa'] = 'empresa';
    $columns['correo'] = 'correo';
    return $columns;
});

/**
 * Mensaje personalizado cuando no hay contactos manuales permitidos
 */
add_action('admin_notices', function() {
    $screen = get_current_screen();
    
    if ($screen && $screen->post_type === 'mg_contacto' && $screen->base === 'post') {
        ?>
        <div class="notice notice-info">
            <p>
                <strong><?php _e('Nota:', 'maggiore'); ?></strong>
                <?php _e('Los contactos se crean automáticamente desde los formularios del sitio. No se pueden crear manualmente.', 'maggiore'); ?>
            </p>
        </div>
        <?php
    }
});

/**
 * Agregar filtros rápidos en el listado
 */
add_action('restrict_manage_posts', function() {
    global $typenow;
    
    if ($typenow === 'mg_contacto') {
        // Filtro por origen
        $origen_actual = isset($_GET['origen_filter']) ? $_GET['origen_filter'] : '';
        
        $origenes = get_posts([
            'post_type' => 'mg_contacto',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);
        
        $origenes_unicos = [];
        foreach ($origenes as $id) {
            $origen = get_post_meta($id, 'correo_origen', true);
            if ($origen && !in_array($origen, $origenes_unicos)) {
                $origenes_unicos[] = $origen;
            }
        }
        
        if (!empty($origenes_unicos)) {
            echo '<select name="origen_filter">';
            echo '<option value="">' . __('Todos los orígenes', 'maggiore') . '</option>';
            foreach ($origenes_unicos as $origen) {
                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($origen),
                    selected($origen_actual, $origen, false),
                    esc_html($origen)
                );
            }
            echo '</select>';
        }
    }
});

/**
 * Aplicar filtros en la query
 */
add_filter('parse_query', function($query) {
    global $pagenow, $typenow;
    
    if ($pagenow === 'edit.php' && $typenow === 'mg_contacto' && isset($_GET['origen_filter']) && $_GET['origen_filter'] !== '') {
        $query->query_vars['meta_key'] = 'correo_origen';
        $query->query_vars['meta_value'] = $_GET['origen_filter'];
    }
});
