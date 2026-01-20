<?php
/**
 * Analytics Dashboard
 * 
 * Dashboard de estadísticas y métricas de contactos
 */

if (!defined('ABSPATH')) exit;

/**
 * Agregar página de analytics al menú
 */
add_action('admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=mg_contacto',
        __('Estadísticas', 'maggiore'),
        __('📊 Estadísticas', 'maggiore'),
        'manage_options',
        'mg-contactos-analytics',
        'maggiore_contactos_analytics_page'
    );
});

/**
 * Renderizar página de analytics
 */
function maggiore_contactos_analytics_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('📊 Estadísticas de Contactos', 'maggiore'); ?></h1>
        <p class="description">
            <?php _e('Analiza el rendimiento de tus formularios de contacto y conoce mejor a tu audiencia.', 'maggiore'); ?>
        </p>
        
        <div class="maggiore-analytics-dashboard">
            
            <!-- Cards de métricas principales -->
            <div class="analytics-cards">
                <?php maggiore_render_analytics_cards(); ?>
            </div>
            
            <!-- Gráfico de contactos por período -->
            <div class="analytics-chart">
                <h2><?php _e('Tendencia de contactos', 'maggiore'); ?></h2>
                <?php maggiore_render_contacts_chart(); ?>
            </div>
            
            <!-- Top empresas -->
            <div class="analytics-section">
                <h2><?php _e('Top Empresas', 'maggiore'); ?></h2>
                <?php maggiore_render_top_empresas(); ?>
            </div>
            
            <!-- Distribución por origen -->
            <div class="analytics-section">
                <h2><?php _e('Distribución por origen', 'maggiore'); ?></h2>
                <?php maggiore_render_origen_distribution(); ?>
            </div>
            
            <!-- Horarios de mayor actividad -->
            <div class="analytics-section">
                <h2><?php _e('Horarios de mayor actividad', 'maggiore'); ?></h2>
                <?php maggiore_render_activity_hours(); ?>
            </div>
            
        </div>
    </div>
    
    <style>
        .maggiore-analytics-dashboard {
            margin-top: 30px;
        }
        .analytics-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .analytics-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .analytics-card h3 {
            margin: 0 0 10px;
            font-size: 13px;
            color: #718096;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .analytics-card .metric {
            font-size: 36px;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }
        .analytics-card .change {
            font-size: 13px;
            margin-top: 10px;
        }
        .analytics-card .change.positive {
            color: #48bb78;
        }
        .analytics-card .change.negative {
            color: #f56565;
        }
        .analytics-chart,
        .analytics-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-bottom: 30px;
        }
        .analytics-section h2 {
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 18px;
            color: #2d3748;
        }
        .stat-bar {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .stat-bar .label {
            width: 150px;
            font-weight: 600;
            color: #4a5568;
        }
        .stat-bar .bar-container {
            flex: 1;
            height: 30px;
            background: #edf2f7;
            border-radius: 4px;
            overflow: hidden;
            margin: 0 15px;
        }
        .stat-bar .bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }
        .stat-bar .count {
            min-width: 40px;
            text-align: right;
            font-weight: 700;
            color: #2d3748;
        }
        .hour-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
        }
        .hour-cell {
            padding: 15px;
            background: #f7fafc;
            border-radius: 6px;
            text-align: center;
            border: 2px solid transparent;
        }
        .hour-cell.active {
            background: #ebf4ff;
            border-color: #667eea;
        }
        .hour-cell .hour-label {
            font-size: 13px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 5px;
        }
        .hour-cell .hour-count {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
        }
    </style>
    <?php
}

/**
 * Renderizar cards de métricas principales
 */
function maggiore_render_analytics_cards() {
    global $wpdb;
    
    // Total de contactos
    $total_contactos = wp_count_posts('mg_contacto')->publish;
    
    // Contactos hoy
    $hoy_start = date('Y-m-d 00:00:00');
    $hoy_end = date('Y-m-d 23:59:59');
    $contactos_hoy = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM {$wpdb->posts}
        WHERE post_type = 'mg_contacto'
        AND post_status = 'publish'
        AND post_date BETWEEN %s AND %s
    ", $hoy_start, $hoy_end));
    
    // Contactos esta semana
    $semana_start = date('Y-m-d 00:00:00', strtotime('monday this week'));
    $contactos_semana = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM {$wpdb->posts}
        WHERE post_type = 'mg_contacto'
        AND post_status = 'publish'
        AND post_date >= %s
    ", $semana_start));
    
    // Contactos este mes
    $mes_start = date('Y-m-01 00:00:00');
    $contactos_mes = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM {$wpdb->posts}
        WHERE post_type = 'mg_contacto'
        AND post_status = 'publish'
        AND post_date >= %s
    ", $mes_start));
    
    // Calcular cambio respecto al mes anterior
    $mes_anterior_start = date('Y-m-01 00:00:00', strtotime('first day of last month'));
    $mes_anterior_end = date('Y-m-t 23:59:59', strtotime('last day of last month'));
    $contactos_mes_anterior = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM {$wpdb->posts}
        WHERE post_type = 'mg_contacto'
        AND post_status = 'publish'
        AND post_date BETWEEN %s AND %s
    ", $mes_anterior_start, $mes_anterior_end));
    
    $cambio_porcentaje = 0;
    $cambio_clase = 'neutral';
    if ($contactos_mes_anterior > 0) {
        $cambio_porcentaje = (($contactos_mes - $contactos_mes_anterior) / $contactos_mes_anterior) * 100;
        $cambio_clase = $cambio_porcentaje >= 0 ? 'positive' : 'negative';
    }
    
    ?>
    
    <!-- Card: Total -->
    <div class="analytics-card">
        <h3><?php _e('Total Contactos', 'maggiore'); ?></h3>
        <p class="metric"><?php echo number_format_i18n($total_contactos); ?></p>
        <div class="change neutral">
            <?php _e('Desde el inicio', 'maggiore'); ?>
        </div>
    </div>
    
    <!-- Card: Hoy -->
    <div class="analytics-card">
        <h3><?php _e('Hoy', 'maggiore'); ?></h3>
        <p class="metric"><?php echo number_format_i18n($contactos_hoy); ?></p>
        <div class="change neutral">
            <?php echo date_i18n('l, j F'); ?>
        </div>
    </div>
    
    <!-- Card: Esta semana -->
    <div class="analytics-card">
        <h3><?php _e('Esta Semana', 'maggiore'); ?></h3>
        <p class="metric"><?php echo number_format_i18n($contactos_semana); ?></p>
        <div class="change neutral">
            <?php _e('Lunes a hoy', 'maggiore'); ?>
        </div>
    </div>
    
    <!-- Card: Este mes -->
    <div class="analytics-card">
        <h3><?php _e('Este Mes', 'maggiore'); ?></h3>
        <p class="metric"><?php echo number_format_i18n($contactos_mes); ?></p>
        <div class="change <?php echo $cambio_clase; ?>">
            <?php if ($cambio_porcentaje > 0): ?>
                ↑ +<?php echo number_format($cambio_porcentaje, 1); ?>%
            <?php elseif ($cambio_porcentaje < 0): ?>
                ↓ <?php echo number_format($cambio_porcentaje, 1); ?>%
            <?php else: ?>
                <?php _e('Sin cambios', 'maggiore'); ?>
            <?php endif; ?>
            <?php _e('vs mes anterior', 'maggiore'); ?>
        </div>
    </div>
    
    <?php
}

/**
 * Renderizar gráfico simple de contactos por mes (últimos 6 meses)
 */
function maggiore_render_contacts_chart() {
    global $wpdb;
    
    $meses_data = [];
    for ($i = 5; $i >= 0; $i--) {
        $mes_timestamp = strtotime("-$i months");
        $mes_inicio = date('Y-m-01 00:00:00', $mes_timestamp);
        $mes_fin = date('Y-m-t 23:59:59', $mes_timestamp);
        
        $count = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM {$wpdb->posts}
            WHERE post_type = 'mg_contacto'
            AND post_status = 'publish'
            AND post_date BETWEEN %s AND %s
        ", $mes_inicio, $mes_fin));
        
        $meses_data[] = [
            'mes' => date_i18n('M Y', $mes_timestamp),
            'count' => (int)$count
        ];
    }
    
    $max_count = max(array_column($meses_data, 'count'));
    $max_count = $max_count > 0 ? $max_count : 1;
    
    ?>
    <div class="chart-container">
        <?php foreach ($meses_data as $data): ?>
            <?php
            $percentage = ($data['count'] / $max_count) * 100;
            ?>
            <div class="stat-bar">
                <div class="label"><?php echo esc_html($data['mes']); ?></div>
                <div class="bar-container">
                    <div class="bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <div class="count"><?php echo $data['count']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Renderizar top empresas que más contactan
 */
function maggiore_render_top_empresas() {
    global $wpdb;
    
    $top_empresas = $wpdb->get_results("
        SELECT meta_value as empresa, COUNT(*) as total
        FROM {$wpdb->postmeta}
        WHERE meta_key = 'correo_empresa'
        AND meta_value != ''
        GROUP BY meta_value
        ORDER BY total DESC
        LIMIT 10
    ");
    
    if (empty($top_empresas)) {
        echo '<p class="description">' . __('No hay datos disponibles aún.', 'maggiore') . '</p>';
        return;
    }
    
    $max_total = $top_empresas[0]->total;
    
    ?>
    <div class="empresas-list">
        <?php foreach ($top_empresas as $empresa): ?>
            <?php
            $percentage = ($empresa->total / $max_total) * 100;
            ?>
            <div class="stat-bar">
                <div class="label"><?php echo esc_html($empresa->empresa); ?></div>
                <div class="bar-container">
                    <div class="bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <div class="count"><?php echo $empresa->total; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Renderizar distribución por origen
 */
function maggiore_render_origen_distribution() {
    global $wpdb;
    
    $origenes = $wpdb->get_results("
        SELECT meta_value as origen, COUNT(*) as total
        FROM {$wpdb->postmeta}
        WHERE meta_key = 'correo_origen'
        GROUP BY meta_value
        ORDER BY total DESC
    ");
    
    if (empty($origenes)) {
        echo '<p class="description">' . __('No hay datos disponibles aún.', 'maggiore') . '</p>';
        return;
    }
    
    $total_contactos = array_sum(array_column($origenes, 'total'));
    
    ?>
    <div class="origen-list">
        <?php foreach ($origenes as $origen): ?>
            <?php
            $percentage = ($origen->total / $total_contactos) * 100;
            ?>
            <div class="stat-bar">
                <div class="label"><?php echo esc_html($origen->origen ?: 'Sin origen'); ?></div>
                <div class="bar-container">
                    <div class="bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <div class="count">
                    <?php echo $origen->total; ?>
                    <span style="font-size: 12px; color: #718096; margin-left: 5px;">
                        (<?php echo number_format($percentage, 1); ?>%)
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Renderizar análisis de horarios de actividad
 */
function maggiore_render_activity_hours() {
    global $wpdb;
    
    $hours_data = [];
    for ($hour = 0; $hour < 24; $hour++) {
        $count = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM {$wpdb->posts}
            WHERE post_type = 'mg_contacto'
            AND post_status = 'publish'
            AND HOUR(post_date) = %d
        ", $hour));
        
        $hours_data[$hour] = (int)$count;
    }
    
    $max_hour_count = max($hours_data);
    
    ?>
    <div class="hour-grid">
        <?php foreach ($hours_data as $hour => $count): ?>
            <?php
            $is_active = $count > ($max_hour_count * 0.5);
            $hour_formatted = sprintf('%02d:00', $hour);
            ?>
            <div class="hour-cell <?php echo $is_active ? 'active' : ''; ?>">
                <div class="hour-label"><?php echo $hour_formatted; ?></div>
                <div class="hour-count"><?php echo $count; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="description" style="margin-top: 20px;">
        <?php _e('Las horas con mayor actividad están destacadas en azul.', 'maggiore'); ?>
    </p>
    <?php
}
