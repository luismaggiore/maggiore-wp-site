<?php
/**
 * Template: Single Caso de Éxito
 * 
 * Versión optimizada usando loops reutilizables
 * Cambios principales:
 * - Uso de loop-portafolio.php para proyectos relacionados
 * - Código más limpio y mantenible
 */

if (!defined('ABSPATH')) exit;
get_header();

the_post();
$caso_id = get_the_ID();
$lang = function_exists('pll_get_post_language') ? pll_get_post_language($caso_id) : false;

// Datos del caso de éxito
$cliente_id = get_post_meta($caso_id, 'mg_caso_cliente', true);
$servicios_ids = get_post_meta($caso_id, 'mg_caso_servicios', true) ?: [];
$equipo_ids = get_post_meta($caso_id, 'mg_caso_equipo', true) ?: [];
$contexto = get_post_meta($caso_id, 'mg_caso_contexto', true);
$acciones = get_post_meta($caso_id, 'mg_caso_acciones', true);
$resultados = get_post_meta($caso_id, 'mg_caso_resultados', true);
$fecha = get_post_meta($caso_id, 'mg_caso_fecha', true);

// Datos del contratador
$contratador_nombre = get_post_meta($caso_id, 'mg_caso_contratador_nombre', true);
$contratador_cargo = get_post_meta($caso_id, 'mg_caso_contratador_cargo', true);
$contratador_linkedin = get_post_meta($caso_id, 'mg_caso_contratador_linkedin', true);
$contratador_cita = get_post_meta($caso_id, 'mg_caso_contratador_cita', true);
$contratador_img = wp_get_attachment_image_url(
    get_post_meta($caso_id, 'mg_caso_contratador_img', true),
    'thumbnail'
);

// Datos del cliente
$cliente_nombre = $cliente_id ? get_the_title($cliente_id) : '';
$cliente_logo = $cliente_id ? get_the_post_thumbnail_url($cliente_id, 'thumbnail') : '';
$industrias = $cliente_id ? get_the_terms($cliente_id, 'mg_industria') : [];
$industria = (!empty($industrias) && !is_wp_error($industrias)) ? $industrias[0] : null;

// Traducir IDs si es necesario
if ($lang && function_exists('pll_get_post')) {
    if ($cliente_id && pll_get_post_language($cliente_id) !== $lang) {
        $cliente_id = pll_get_post($cliente_id, $lang);
    }
    foreach ($servicios_ids as &$sid) {
        if (pll_get_post_language($sid) !== $lang) {
            $sid = pll_get_post($sid, $lang);
        }
    }
    unset($sid);
    foreach ($equipo_ids as &$eid) {
        if (pll_get_post_language($eid) !== $lang) {
            $eid = pll_get_post($eid, $lang);
        }
    }
    unset($eid);
}

// Buscar portafolios relacionados
$portafolios_query = new WP_Query([
    'post_type'      => 'mg_portafolio',
    'posts_per_page' => -1,
    'meta_key'       => 'mg_portafolio_caso_exito',
    'meta_value'     => $caso_id,
    'post_status'    => 'publish'
]);

// Formatear fecha para mostrar en el idioma actual
$fecha_formateada = '';
if ($fecha) {
    $date = DateTime::createFromFormat('Y-m', $fecha);
    if ($date) {
        $timestamp = $date->getTimestamp();
        $fecha_formateada = date_i18n('F Y', $timestamp);
    }
}
?>

<main class="container py-5" itemscope itemtype="https://schema.org/Article">

    <!-- Header del caso de éxito -->
    <header style="padding-top: 15vh; position: relative;">

        <h1 class="display-4 mb-3" itemprop="headline"><?php the_title(); ?></h1>
        
        <div class="row align-items-center mb-3">
            <div class="col">
                <?php if ($fecha_formateada): ?>
                    <p class="mb-2" style="font-size: 0.875rem;">
                        <?= esc_html($cliente_nombre); ?>,
                        <time datetime="<?= esc_attr($fecha); ?>" itemprop="datePublished">
                            <?= esc_html($fecha_formateada); ?>
                        </time>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (has_excerpt()): ?>
            <p class="lead text-muted" itemprop="description">
                <?= get_the_excerpt(); ?>
            </p>
        <?php endif; ?>

        <!-- Meta datos ocultos para SEO -->
        <meta itemprop="dateModified" content="<?= esc_attr(get_the_modified_date('c')); ?>">
        <meta itemprop="author" content="<?= esc_attr(get_bloginfo('name')); ?>">
    </header>

    <!-- Imagen destacada -->
    <?php if (has_post_thumbnail()): ?>
        <section class="mb-2">
            <figure itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                <?php the_post_thumbnail('large', ['class' => 'img-fluid w-100 blog-img']); ?>
                <meta itemprop="url" content="<?= get_the_post_thumbnail_url($caso_id, 'large'); ?>">
                <meta itemprop="width" content="1200">
                <meta itemprop="height" content="630">
            </figure>
        </section>
    <?php endif; ?>

    <!-- Contenido principal -->
    <div class="row g-2">
        <div class="col-lg-8">
            
            <div class="card-mg mb-2">
                <!-- Contexto -->
                <?php if ($contexto): ?>
                    <section class="mb-4" itemprop="articleBody">
                        <h2 class="label"><?php _e('Contexto', 'maggiore'); ?></h2>
                        <div class="content-section">
                            <?= wpautop(esc_html($contexto)); ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Acciones -->
                <?php if ($acciones): ?>
                    <section class="mb-4">
                        <h2 class="label"><?php _e('Acciones Implementadas', 'maggiore'); ?></h2>
                        <div class="content-section">
                            <?= wpautop(esc_html($acciones)); ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Resultados -->
                <?php if ($resultados): ?>
                    <section class="mb-2">
                        <h2 class="label"><?php _e('Resultados Obtenidos', 'maggiore'); ?></h2>
                        <div class="content-section">
                            <?= wpautop(esc_html($resultados)); ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
            
            <!-- Cita del contratador -->
            <?php if ($contratador_nombre): ?>
                <section class="mb-4">
                    <div class="card-mg">

                        <?php if ($contratador_cita): ?>
                            <blockquote class="mb-4" style="font-size: 1.25rem; font-style: italic;letter-spacing:0.03em">
                                <?= esc_html($contratador_cita); ?>
                            </blockquote>
                        <?php else: ?>
                            <h2 class="label">
                                <?php _e('Contratador del servicio', 'maggiore'); ?>
                            </h2>
                        <?php endif; ?>

                        <div class="d-flex align-items-center">
                            <?php if ($contratador_img): ?>
                                <img src="<?= esc_url($contratador_img); ?>" 
                                     alt="<?= esc_attr("Foto de $contratador_nombre"); ?>"
                                     class="rounded-circle me-3"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            <?php endif; ?>

                            <div>
                                <p class="my-0 py-0" style="font-weight: 500; color: white; line-height:18px">
                                    <?= esc_html($contratador_nombre); ?>
                                </p>

                                <?php if ($contratador_cargo): ?>
                                    <p class="my-0 py-0" style="font-size: 0.875rem; line-height:18px">
                                        <?= esc_html($contratador_cargo); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ($contratador_linkedin): ?>
                                    <a href="<?= esc_url($contratador_linkedin); ?>" 
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="btn btn-sm btn-linkedin mt-1"
                                       style="font-size: 0.875rem;">
                                        <i class="bi bi-linkedin"></i> LinkedIn
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </section>
            <?php endif; ?>

        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            
            <!-- Cliente -->
            <?php if ($cliente_id): ?>
                <section class="mb-2">
                    <div class="card-mg">
                        <h3 class="label">
                            <?php _e('Cliente', 'maggiore'); ?>
                        </h3>
                        <div class="client-tag">
                            <a href="<?= get_permalink($cliente_id); ?>">
                                <?php if ($cliente_logo): ?>
                                    <img src="<?= esc_url($cliente_logo); ?>"
                                         alt="<?= esc_attr("Logo de $cliente_nombre"); ?>"
                                         width="20" height="20"
                                         loading="lazy">
                                <?php endif; ?>
                                <span><?= esc_html($cliente_nombre); ?></span>
                            </a>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Fecha del proyecto -->
            <?php if ($fecha_formateada): ?>
                <section class="mb-2">
                    <div class="card-mg">
                        <h3 class="label">
                            <?php _e('Fecha del Proyecto', 'maggiore'); ?>
                        </h3>
                        <p style="color: white; font-weight: 500; margin: 0;">
                            <time datetime="<?= esc_attr($fecha); ?>">
                                <?= esc_html($fecha_formateada); ?>
                            </time>
                        </p>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Industria/Categoría -->
            <?php if ($industria): ?>
                <section class="mb-2">
                    <div class="card-mg">
                        <h3 class="label">
                            <?php _e('Industria', 'maggiore'); ?>
                        </h3>
                        <a class="service-tag" href="<?= esc_url(get_term_link($industria)); ?>">
                            <?= esc_html($industria->name); ?>
                        </a>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Servicios involucrados -->
            <?php if (!empty($servicios_ids)): ?>
                <section class="mb-2">
                    <div class="card-mg">
                        <h3 class="label">
                            <?php _e('Servicios Involucrados', 'maggiore'); ?>
                        </h3>
                        <ul class="services-tags">
                            <?php foreach ($servicios_ids as $sid): ?>
                                <li>
                                    <a class="service-tag" href="<?= get_permalink($sid); ?>">
                                        <?= esc_html(get_the_title($sid)); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Equipo participante -->
            <?php if (!empty($equipo_ids)): ?>
                <section class="mb-2">
                    <div class="card-mg">
                        <h3 class="label">
                            <?php _e('Equipo Participante', 'maggiore'); ?>
                        </h3>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($equipo_ids as $eid): 
                                $miembro_foto = get_the_post_thumbnail_url($eid, 'thumbnail');
                                $miembro_nombre = get_the_title($eid);
                                $miembro_cargo = get_post_meta($eid, 'mg_equipo_cargo', true);
                            ?>
                                <a href="<?= get_permalink($eid); ?>" class="person-card">
                                    <?php if ($miembro_foto): ?>
                                        <img src="<?= esc_url($miembro_foto); ?>"
                                             alt="<?= esc_attr("Foto de $miembro_nombre"); ?>"
                                             class="person-card__photo">
                                    <?php endif; ?>
                                    <div class="person-card__info">
                                        <div class="person-card__name">
                                            <?= esc_html($miembro_nombre); ?>
                                        </div>
                                        <?php if ($miembro_cargo): ?>
                                            <div class="person-card__role">
                                                <?= esc_html($miembro_cargo); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    </div>

    <?php if ($portafolios_query->have_posts()): ?>
        <section class="mt-3">
            <div class="feature-name-2 mb-3">
                <h2><?php _e('Portafolio Relacionado', 'maggiore'); ?></h2>
            </div>
            <div class="row g-2">
                <?php
                // Usar el loop reutilizable
                set_query_var('custom_query', $portafolios_query);
                get_template_part('template-parts/loops/loop', 'portafolio');
                ?>
            </div>
        </section>
    <?php endif; ?>

    <nav class="mt-5 pt-5">
        <div class="row">
            <div class="col-md-6">
                <?php
                $prev_post = get_previous_post();
                if ($prev_post): ?>
                    <a href="<?= get_permalink($prev_post); ?>">
                        <small class="tag-flecha"> <i class="bi bi-chevron-left"></i><?php _e(' Caso anterior', 'maggiore'); ?></small>
                        <p style="color: white;"><?= esc_html($prev_post->post_title); ?></p>
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-md-6 text-md-end">
                <?php
                $next_post = get_next_post();
                if ($next_post): ?>
                    <a href="<?= get_permalink($next_post); ?>">
                        <small class="tag-flecha next-flecha"><?php _e('Siguiente caso ', 'maggiore'); ?><i class="bi bi-chevron-right"></i> </small>
                        <p style="color: white;"><?= esc_html($next_post->post_title); ?></p>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

</main>

<?php get_footer(); ?>
