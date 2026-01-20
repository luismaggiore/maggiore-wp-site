<?php
/**
 * Export Functionality
 * 
 * Permite exportar contactos a formato CSV
 */

if (!defined('ABSPATH')) exit;

/**
 * Registrar AJAX handler para exportación
 */
add_action('admin_init', 'maggiore_handle_contacts_export');

function maggiore_handle_contacts_export() {
    if (!isset($_GET['action']) || $_GET['action'] !== 'export_contacts_csv') {
        return;
    }
    
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes permisos para exportar contactos.', 'maggiore'));
    }
    
    // Obtener todos los contactos
    $contactos = get_posts([
        'post_type' => 'mg_contacto',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    
    if (empty($contactos)) {
        wp_die(__('No hay contactos para exportar.', 'maggiore'));
    }
    
    // Preparar archivo CSV
    $filename = 'contactos-' . date('Y-m-d-His') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8 (para que Excel lo reconozca)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Headers del CSV
    $headers = [
        'ID',
        'Fecha',
        'Nombre',
        'Correo',
        'Teléfono',
        'Cargo',
        'Empresa',
        'Dolor',
        'Objetivos',
        'Origen',
        'IP',
        'Idioma',
        'UTM Source',
        'UTM Medium',
        'UTM Campaign'
    ];
    
    fputcsv($output, $headers);
    
    // Datos
    foreach ($contactos as $contacto) {
        $row = [
            $contacto->ID,
            get_the_date('Y-m-d H:i', $contacto->ID),
            get_post_meta($contacto->ID, 'correo_nombre', true),
            get_post_meta($contacto->ID, 'correo_email', true),
            get_post_meta($contacto->ID, 'correo_telefono', true),
            get_post_meta($contacto->ID, 'correo_cargo', true),
            get_post_meta($contacto->ID, 'correo_empresa', true),
            get_post_meta($contacto->ID, 'correo_dolor', true),
            get_post_meta($contacto->ID, 'correo_objetivos', true),
            get_post_meta($contacto->ID, 'correo_origen', true),
            get_post_meta($contacto->ID, 'correo_ip', true),
            get_post_meta($contacto->ID, 'correo_idioma', true),
            get_post_meta($contacto->ID, 'correo_utm_source', true),
            get_post_meta($contacto->ID, 'correo_utm_medium', true),
            get_post_meta($contacto->ID, 'correo_utm_campaign', true),
        ];
        
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

/**
 * Agregar botón de exportación en la página de listado
 */
add_action('restrict_manage_posts', function() {
    global $typenow;
    
    if ($typenow === 'mg_contacto') {
        $export_url = admin_url('admin.php?action=export_contacts_csv');
        ?>
        <a href="<?php echo esc_url($export_url); ?>" 
           class="button button-primary" 
           style="margin-left: 10px;">
            <span class="dashicons dashicons-download" style="vertical-align: middle; margin-top: 3px;"></span>
            <?php _e('Exportar a CSV', 'maggiore'); ?>
        </a>
        <?php
    }
});

/**
 * Agregar botón de exportación en el dashboard de analytics
 */
add_action('admin_footer', function() {
    $screen = get_current_screen();
    
    if ($screen && $screen->id === 'mg_contacto_page_mg-contactos-analytics') {
        $export_url = admin_url('admin.php?action=export_contacts_csv');
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.wrap h1').after(
                '<a href="<?php echo esc_js($export_url); ?>" class="page-title-action">' +
                '<span class="dashicons dashicons-download" style="vertical-align: middle;"></span> ' +
                '<?php _e('Exportar Datos', 'maggiore'); ?>' +
                '</a>'
            );
        });
        </script>
        <?php
    }
});

/**
 * Función auxiliar para exportación programática
 * (útil para integraciones futuras)
 */
function maggiore_export_contacts_to_array($filters = []) {
    $args = [
        'post_type' => 'mg_contacto',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ];
    
    // Aplicar filtros opcionales
    if (!empty($filters['date_from'])) {
        $args['date_query'][] = [
            'after' => $filters['date_from']
        ];
    }
    
    if (!empty($filters['date_to'])) {
        $args['date_query'][] = [
            'before' => $filters['date_to']
        ];
    }
    
    if (!empty($filters['origen'])) {
        $args['meta_query'][] = [
            'key' => 'correo_origen',
            'value' => $filters['origen']
        ];
    }
    
    $contactos = get_posts($args);
    
    $data = [];
    foreach ($contactos as $contacto) {
        $data[] = [
            'id' => $contacto->ID,
            'fecha' => get_the_date('Y-m-d H:i', $contacto->ID),
            'nombre' => get_post_meta($contacto->ID, 'correo_nombre', true),
            'correo' => get_post_meta($contacto->ID, 'correo_email', true),
            'telefono' => get_post_meta($contacto->ID, 'correo_telefono', true),
            'cargo' => get_post_meta($contacto->ID, 'correo_cargo', true),
            'empresa' => get_post_meta($contacto->ID, 'correo_empresa', true),
            'dolor' => get_post_meta($contacto->ID, 'correo_dolor', true),
            'objetivos' => get_post_meta($contacto->ID, 'correo_objetivos', true),
            'origen' => get_post_meta($contacto->ID, 'correo_origen', true),
            'ip' => get_post_meta($contacto->ID, 'correo_ip', true),
            'idioma' => get_post_meta($contacto->ID, 'correo_idioma', true),
            'utm_source' => get_post_meta($contacto->ID, 'correo_utm_source', true),
            'utm_medium' => get_post_meta($contacto->ID, 'correo_utm_medium', true),
            'utm_campaign' => get_post_meta($contacto->ID, 'correo_utm_campaign', true),
        ];
    }
    
    return $data;
}

/**
 * Widget para el dashboard principal de WordPress
 */
add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'maggiore_contacts_widget',
        __('📬 Contactos Recientes', 'maggiore'),
        'maggiore_dashboard_widget_contacts'
    );
});

function maggiore_dashboard_widget_contacts() {
    $recent_contacts = get_posts([
        'post_type' => 'mg_contacto',
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    
    if (empty($recent_contacts)) {
        echo '<p>' . __('No hay contactos recientes.', 'maggiore') . '</p>';
        return;
    }
    
    echo '<ul style="margin: 0;">';
    foreach ($recent_contacts as $contact) {
        $nombre = get_post_meta($contact->ID, 'correo_nombre', true);
        $empresa = get_post_meta($contact->ID, 'correo_empresa', true);
        $correo = get_post_meta($contact->ID, 'correo_email', true);
        $fecha = human_time_diff(strtotime($contact->post_date), current_time('timestamp'));
        
        echo '<li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f1;">';
        echo '<strong>' . esc_html($nombre) . '</strong>';
        if ($empresa) {
            echo ' <span style="color: #666;">(' . esc_html($empresa) . ')</span>';
        }
        echo '<br>';
        echo '<small style="color: #666;">';
        echo '<a href="mailto:' . esc_attr($correo) . '">' . esc_html($correo) . '</a>';
        echo ' • Hace ' . $fecha;
        echo '</small>';
        echo '</li>';
    }
    echo '</ul>';
    
    echo '<p style="margin-top: 15px;">';
    echo '<a href="' . admin_url('edit.php?post_type=mg_contacto') . '" class="button button-primary">';
    echo __('Ver todos', 'maggiore');
    echo '</a>';
    echo '</p>';
}
