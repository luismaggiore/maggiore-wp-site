<?php
/**
 * Template: Single Cliente
 * Diseño BENTO (Visual/Data-heavy) - Similar a Equipo/Portafolio
 * 
 * Estructura:
 * 1. Header (logo + nombre + descripción)
 * 2. Bento Grid (cards con info estructurada)
 * 3. Contenido dinámico (servicios, casos, portafolio)
 */

if (!defined('ABSPATH')) exit;

require_once get_template_directory() . '/inc/helpers/clientes-helpers.php';

get_header();

the_post();
$cliente_id = get_the_ID();
$lang = function_exists('pll_get_post_language') ? pll_get_post_language($cliente_id) : false;

// ===============================
// DATOS DEL CLIENTE
// ===============================
$descripcion = get_post_meta($cliente_id, 'mg_cliente_descripcion', true);
$logo        = get_the_post_thumbnail_url($cliente_id, 'medium');
$industrias  = get_the_terms($cliente_id, 'mg_industria');
$industria   = (!empty($industrias) && !is_wp_error($industrias)) ? $industrias[0] : null;

// Datos contractuales
$inicio_contrato  = get_post_meta($cliente_id, 'mg_cliente_inicio_contrato', true);
$termino_contrato = get_post_meta($cliente_id, 'mg_cliente_termino_contrato', true);

// Presencia digital
$linkedin  = get_post_meta($cliente_id, 'mg_cliente_linkedin', true);
$website   = get_post_meta($cliente_id, 'mg_cliente_website', true);
$instagram = get_post_meta($cliente_id, 'mg_cliente_instagram', true);
$facebook  = get_post_meta($cliente_id, 'mg_cliente_facebook', true);

// Tamaño (desde helper)
$tamano   = mg_get_tamano_cliente($cliente_id);
$empleados = get_post_meta($cliente_id, 'mg_cliente_num_empleados', true);

// Formatear fechas
$inicio_formateado = $inicio_contrato ? date_i18n('F Y', strtotime($inicio_contrato)) : '';
$termino_formateado = $termino_contrato ? date_i18n('F Y', strtotime($termino_contrato)) : __('Actualidad', 'maggiore');

// Calcular duración
$duracion_texto = '';
if ($inicio_contrato) {
    $inicio_ts = strtotime($inicio_contrato);
    $fin_ts    = $termino_contrato ? strtotime($termino_contrato) : time();
    $diff      = $fin_ts - $inicio_ts;
    $years     = floor($diff / (365 * 60 * 60 * 24));
    $months    = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

    if ($years > 0) {
        $duracion_texto = sprintf(_n('%d año', '%d años', $years, 'maggiore'), $years);
        if ($months > 0) {
            $duracion_texto .= ' ' . sprintf(_n('y %d mes', 'y %d meses', $months, 'maggiore'), $months);
        }
    } else {
        $duracion_texto = sprintf(_n('%d mes', '%d meses', $months, 'maggiore'), $months);
    }
}

// ===============================
// SERVICIOS
// ===============================
$servicios_manual = (array) get_post_meta($cliente_id, 'mg_cliente_servicios', true);
$servicios_auto   = (array) get_post_meta($cliente_id, '_mg_servicios_auto', true);
$servicios_total  = array_unique(array_merge($servicios_manual, $servicios_auto));

$servicios = [];
if (!empty($servicios_total)) {
    $servicios = get_posts([
        'post_type'      => 'mg_servicio',
        'post__in'       => $servicios_total,
        'posts_per_page' => -1,
        'orderby'        => 'post__in',
    ]);
}

// ===============================
// CASOS DE ÉXITO Y PORTAFOLIOS
// ===============================
$casos_exito = get_posts([
    'post_type'   => 'mg_caso_exito',
    'numberposts' => -1,
    'meta_query'  => [[
        'key'   => 'mg_caso_cliente',
        'value' => $cliente_id,
    ]]
]);

$portafolios = get_posts([
    'post_type'   => 'mg_portafolio',
    'numberposts' => -1,
    'meta_query'  => [[
        'key'   => 'mg_portafolio_cliente',
        'value' => $cliente_id
    ]]
]);
?>

<main class="container py-5" itemscope itemtype="https://schema.org/Organization">

    <!-- ===============================
         HEADER COMPACTO
         =============================== -->
    <header class="mb-4 p-top" style="position: relative;">

        <!-- Logo y Título -->
        <div class="row align-items-center mb-3">
            <?php if ($logo): ?>
                <div class="col-auto">
                    <img src="<?= esc_url($logo); ?>"
                         alt="<?= esc_attr(sprintf(__('Logo de %s', 'maggiore'), get_the_title())); ?>"
                         class="img-fluid"
                         style="max-height: 100px; max-width: 200px;"
                         itemprop="logo">
                </div>
            <?php endif; ?>

            <div class="col">
                <h1 class="display-4 mb-2" itemprop="name"><?php the_title(); ?></h1>

                <?php if ($descripcion): ?>
                    <p class="lead mb-0" itemprop="description">
                        <?= esc_html($descripcion); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meta datos ocultos para SEO -->
        <meta itemprop="dateModified" content="<?= esc_attr(get_the_modified_date('c')); ?>">
        <?php if ($linkedin): ?>
            <meta itemprop="sameAs" content="<?= esc_url($linkedin); ?>">
        <?php endif; ?>
        <?php if ($website): ?>
            <meta itemprop="url" content="<?= esc_url($website); ?>">
        <?php endif; ?>
    </header>

    <!-- ===============================
         BENTO GRID - INFORMACIÓN CLAVE
         =============================== -->
    <div class="row g-2 mb-5">

        <!-- CARD GRANDE: Relación Comercial -->
        <?php if ($inicio_contrato || $termino_contrato): ?>
            <div class="col-lg-6">
                <div class="card-mg" style="height: 100%;">
                    <h3 class="label">
                        <?php _e('Relación Comercial', 'maggiore'); ?>
                    </h3>

                    <div class="row g-2">
                        <?php if ($inicio_contrato): ?>
                            <div class="col-md-6">
                                <div class="card-mg">
                                    <small class="d-block text-muted mb-1">
                                        <?php _e('Inicio', 'maggiore'); ?>
                                    </small>
                                    <p class="mb-0" style="font-weight: 400; color: white; font-size: 1.1rem; line-height: 1.73rem">
                                        <?= esc_html($inicio_formateado); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-6">
                            <div class="card-mg mb-2">
                                <small class="d-block text-muted mb-1">
                                    <?php _e('Estado Actual', 'maggiore'); ?>
                                </small>
                                <?php if (!$termino_contrato): ?>
                                    <span class="badge bg-success" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                                        <i class="bi bi-check-circle"></i>
                                        <?php _e('Cliente Activo', 'maggiore'); ?>
                                    </span>
                                <?php else: ?>
                                    <p class="mb-0" style="font-weight: 400; color: white; font-size: 1.1rem; line-height: 1.73rem">
                                        <?php printf(esc_html__('Finalizado en %s', 'maggiore'), $termino_formateado); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($duracion_texto): ?>
                            <div class="col-12 m-0">
                                <div class="card-mg">
                                    <small class="d-block text-muted mb-1">
                                        <?php echo !$termino_contrato ? __('Tiempo de relación', 'maggiore') : __('Duración total', 'maggiore'); ?>
                                    </small>
                                    <p class="mb-0" style="font-weight: 400; color: white; font-size: 1.1rem; line-height: 1.73rem">
                                        <?= esc_html($duracion_texto); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- CARD: Tamaño de Empresa -->
        <?php if ($tamano || $empleados): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card-mg text-center" style="height: 100%;">
                    <h3 class="label">
                        <?php _e('Tamaño', 'maggiore'); ?>
                    </h3>
      <?php if ($tamano): ?>
                            <h4 class="  mb-3" style="font-size: 1rem;">
                              <?php _e('Empresa', 'maggiore'); ?>       <?= esc_html($tamano['nombre']); ?>     
      </h4>
                        <?php endif; ?>
                    <div >

                        <?php if ($tamano && $tamano['rango']): ?>
                            <div class=" card-mg">
                                <h5 class="m-0 p-0">
                                <?= esc_html($tamano['rango']); ?>
                           </h5>
                            <small class="text-muted d-block" style="font-size: 0.85rem;">
                                <?php _e('en ventas al año', 'maggiore'); ?>
                            </small> </div>
                        <?php endif; ?>

                        <?php if ($empleados): ?>   <div class=" card-mg mt-2">
                          <h5 class="m-0 p-0">
                                <?= number_format($empleados, 0, ',', '.'); ?>
                            </h5>   
                            <small class="text-muted d-block" style="font-size: 0.85rem;">
                                <?php _e('empleados', 'maggiore'); ?>
                            </small></div>
                        <?php endif; ?>

                  

                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- CARD: Industria + Redes -->
        <div class="col-lg-3 col-md-6">
            <div class="card-mg" style="height: 100%;">

                   <!-- Industria -->
                <?php if ($industria): ?>
                    <div class="mb-3">
                        <h3 class="label mb-2">
                            <?php _e('Industria', 'maggiore'); ?>
                        </h3>
                        <a class="service-tag" href="<?= esc_url(get_term_link($industria)); ?>">
                            <?= esc_html($industria->name); ?>
                        </a>
                    </div>
                <?php endif; ?>


                <!-- Redes sociales y web -->
                <?php
                $tiene_redes = $website || $linkedin || $instagram || $facebook;
                if ($tiene_redes):
                ?>
                    <div class="<?= $industria ? 'mt-auto pt-3 border-top' : ''; ?>"
                         style="<?= $industria ? 'border-color: rgba(255,255,255,0.1) !important;' : ''; ?>">
                        <h3 class="label mb-2">
                            <?php _e('Redes', 'maggiore'); ?>
                        </h3>
                        <div class="d-flex flex-wrap gap-2">

                            <?php if ($website): ?>
                                <a href="<?= esc_url($website); ?>"
                                   class="btn btn-sm btn-outline-light"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   itemprop="url">
                                    <i class="bi bi-globe"></i>
                                    Web
                                </a>
                            <?php endif; ?>

                            <?php if ($linkedin): ?>
                                <a href="<?= esc_url($linkedin); ?>"
                                   class="btn btn-sm btn-linkedin"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   itemprop="sameAs">
                                    <i class="bi bi-linkedin"></i>
                                    LinkedIn
                                </a>
                            <?php endif; ?>

                            <?php if ($instagram): ?>
                                <a href="<?= esc_url($instagram); ?>"
                                   class="btn btn-sm btn-outline-warning text-white"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   itemprop="sameAs">
                                    <i class="bi bi-instagram"></i>
                                    Instagram
                                </a>
                            <?php endif; ?>

                            <?php if ($facebook): ?>
                                <a href="<?= esc_url($facebook); ?>"
                                   class="btn btn-sm btn-outline-primary text-white"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   itemprop="sameAs">
                                    <i class="bi bi-facebook"></i>
                                    Facebook
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endif; ?>

                <!-- Fallback si no hay nada -->
                <?php if (!$industria && !$tiene_redes): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-building" style="font-size: 3rem; opacity: 0.2;"></i>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- CARD: Estadísticas Visuales
        <div class="col-12">
            <div class="card-mg">
                <h3 class="label mb-3 text">
                    <?php _e('Resumen de Colaboración', 'maggiore'); ?>
                </h3>

                <div class="row text-center g-2">
                    <div class="col-md-4">
                        <div class="card-mg d-inline-flex gap-2 align-items-center w-100">
                            <div class="number-data">
                                <?= count($servicios); ?>
                            </div>
                            <p class="text-muted pt-3">
                                <?php _e('Servicios Contratados', 'maggiore'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-mg d-inline-flex gap-2 align-items-center w-100">
                            <div class="number-data">
                                <?= count($casos_exito); ?>
                            </div>
                            <p class="text-muted pt-3">
                                <?php _e('Casos de Éxito', 'maggiore'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-mg d-inline-flex gap-2 align-items-center w-100">
                            <div class="number-data">
                                <?= count($portafolios); ?>
                            </div>
                            <p class="text-muted pt-3">
                                <?php _e('Proyectos en Portafolio', 'maggiore'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 -->
    </div>

    <!-- ===============================
         CONTENIDO DINÁMICO: SERVICIOS
         =============================== -->
    <?php if (!empty($servicios)): ?>
        <section class="mb-5">
            <div class="feature-name-2 mb-2">
                <h2><?php _e('Servicios Contratados', 'maggiore'); ?></h2>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
                <?php
                global $post;
                foreach ($servicios as $post):
                    setup_postdata($post);
                ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'servicio'); ?>
                    </div>
                <?php
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- ===============================
         CONTENIDO DINÁMICO: CASOS DE ÉXITO
         =============================== -->
    <?php if (!empty($casos_exito)): ?>
        <section class="mb-5">
            <div class="feature-name-2 mb-2">
                <h2><?php _e('Casos de Éxito', 'maggiore'); ?></h2>
            </div>
            <div class="row gy-0">
                <?php
                global $post;
                foreach ($casos_exito as $post):
                    setup_postdata($post);
                ?>
                    <div class="col-12 mb-2">
                        <?php get_template_part('template-parts/card', 'caso-exito'); ?>
                    </div>
                <?php
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- ===============================
         CONTENIDO DINÁMICO: PORTAFOLIO
         =============================== -->
    <?php if (!empty($portafolios)): ?>
        <section class="mb-5">
            <div class="feature-name-2 mb-2">
                <h2><?php _e('Portafolio de Proyectos', 'maggiore'); ?></h2>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
                <?php
                global $post;
                foreach ($portafolios as $post):
                    setup_postdata($post);
                ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'portafolio'); ?>
                    </div>
                <?php
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- ===============================
         NAVEGACIÓN PREV/NEXT
         =============================== -->
    <?php
    set_query_var('prev_label', __('Cliente anterior', 'maggiore'));
    set_query_var('next_label', __('Siguiente cliente', 'maggiore'));
    set_query_var('show_thumbnail', false);
    get_template_part('template-parts/navigation', 'single');
    ?>

</main>

<?php get_footer(); ?>