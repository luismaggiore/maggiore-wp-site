<?php
if (!defined('ABSPATH')) exit;
get_header();

the_post();
$servicio_id = get_the_ID();
$lang = function_exists('pll_get_post_language') ? pll_get_post_language($servicio_id) : false;

// ===============================
// DATOS DEL SERVICIO
// ===============================
$area_id          = get_post_meta($servicio_id, 'mg_servicio_area', true);
$director_id      = get_post_meta($servicio_id, 'mg_servicio_director', true);
$bajada           = get_post_meta($servicio_id, 'mg_servicio_bajada', true);
$consiste         = get_post_meta($servicio_id, 'mg_servicio_consiste', true);
$proceso          = get_post_meta($servicio_id, 'mg_servicio_proceso', true);
$entregables      = get_post_meta($servicio_id, 'mg_servicio_entregables', true);
$para_quien       = get_post_meta($servicio_id, 'mg_servicio_para_quien', true);
$beneficios       = get_post_meta($servicio_id, 'mg_servicio_beneficios', true);
$ventajas         = get_post_meta($servicio_id, 'mg_servicio_ventajas', true);
$precio           = get_post_meta($servicio_id, 'mg_servicio_precio', true);

// ===============================
// DATOS DEL ÁREA Y DIRECTOR
// ===============================
$area_nombre = $area_id ? get_the_title($area_id) : '';
$area_logo = $area_id ? get_the_post_thumbnail_url($area_id, 'thumbnail') : '';

// Si no hay director explícito en el servicio, obtener del área
if (!$director_id && $area_id) {
    $director_id = get_post_meta($area_id, 'mg_area_director', true);
}

$director_nombre = $director_id ? get_the_title($director_id) : '';
$director_foto = $director_id ? get_the_post_thumbnail_url($director_id, 'thumbnail') : '';
$director_cargo = $director_id ? get_post_meta($director_id, 'mg_equipo_cargo', true) : '';

// Traducir IDs si es necesario
if ($lang && function_exists('pll_get_post')) {
    if ($area_id && pll_get_post_language($area_id) !== $lang) {
        $area_id = pll_get_post($area_id, $lang);
        $area_nombre = get_the_title($area_id);
        $area_logo = get_the_post_thumbnail_url($area_id, 'thumbnail');
    }
    if ($director_id && pll_get_post_language($director_id) !== $lang) {
        $director_id = pll_get_post($director_id, $lang);
        $director_nombre = get_the_title($director_id);
        $director_foto = get_the_post_thumbnail_url($director_id, 'thumbnail');
        $director_cargo = get_post_meta($director_id, 'mg_equipo_cargo', true);
    }
}

// ===============================
// CASOS DE ÉXITO RELACIONADOS
// ===============================
$casos_exito = [];
$casos_args = [
    'post_type'   => 'mg_caso_exito',
    'numberposts' => -1,
    'post_status' => 'publish'
];

if ($lang) {
    $casos_args['lang'] = $lang;
}

$todos_casos = get_posts($casos_args);

// Filtrar manualmente los que incluyen este servicio
foreach ($todos_casos as $caso) {
    $servicios_ids = (array) get_post_meta($caso->ID, 'mg_caso_servicios', true);
    if (in_array($servicio_id, $servicios_ids)) {
        $casos_exito[] = $caso;
    }
}

// ===============================
// PORTAFOLIOS RELACIONADOS
// ===============================
$portafolios = [];
$portafolios_args = [
    'post_type'   => 'mg_portafolio',
    'numberposts' => -1,
    'post_status' => 'publish'
];

if ($lang) {
    $portafolios_args['lang'] = $lang;
}

$todos_portafolios = get_posts($portafolios_args);

// Filtrar manualmente los que incluyen este servicio
foreach ($todos_portafolios as $portafolio) {
    $servicios_ids = (array) get_post_meta($portafolio->ID, 'mg_portafolio_servicio', true);
    if (in_array($servicio_id, $servicios_ids)) {
        $portafolios[] = $portafolio;
    }
}



?>

<main class="container py-5" itemscope itemtype="https://schema.org/Service">

    <!-- HEADER DEL SERVICIO -->
    <header class="mb-5 p-top" style="position: relative;">
        
        <!-- Título principal -->
        <h1 class="display-4 mb-3" itemprop="name"><?php the_title(); ?></h1>
        
        <!-- Bajada breve -->
        <?php if ($bajada): ?>
            <p class="lead" itemprop="description">
                <?= esc_html($bajada); ?>
            </p>
        <?php endif; ?>

        <!-- Meta datos ocultos para SEO -->
        <meta itemprop="dateModified" content="<?= esc_attr(get_the_modified_date('c')); ?>">
        <meta itemprop="author" content="<?= esc_attr(get_bloginfo('name')); ?>">
    </header>

    <!-- IMAGEN DESTACADA -->
    <?php if (has_post_thumbnail()): ?>
        <section class="mb-5">
            <figure itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                <?php the_post_thumbnail('large', ['class' => 'img-fluid w-100', 'style' => 'border-radius: 8px;']); ?>
                <meta itemprop="url" content="<?= get_the_post_thumbnail_url($servicio_id, 'large'); ?>">
                <meta itemprop="width" content="1200">
                <meta itemprop="height" content="630">
            </figure>
        </section>
    <?php endif; ?>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="row g-2">
        <div class="col-lg-8 ">
            <div class="card-mg detalle-servicio mb-2" >
            <!-- ¿EN QUÉ CONSISTE? -->
            <?php if ($consiste): ?>
                <section class="mb-4" itemprop="description">
                        <h2 class="label"><?php _e('¿En qué consiste el servicio?', 'maggiore'); ?></h2>
                    <div class="content-section">
                        <?= wp_kses_post($consiste); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- PROCESO -->
            <?php if ($proceso): ?>
                <section class="mb-4">
                        <h2 class="label"><?php _e('Proceso', 'maggiore'); ?></h2>
                    <div class="content-section">
                        <?= wp_kses_post($proceso); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- ENTREGABLES -->
            <?php if ($entregables): ?>
                <section >
               
                        <h2 class="label"><?php _e('Entregables', 'maggiore'); ?></h2>
                    <div class="content-section">
                        <?= wp_kses_post($entregables); ?>
                    </div>
                </section>
            <?php endif; ?>
</div>
<div class="card-mg">
            <!-- PARA QUIÉN -->
            <?php if ($para_quien): ?>
                <section class="mb-4">
                  
                        <h2 class="label"><?php _e('¿Para quién es el servicio?', 'maggiore'); ?></h2>
                    <div class="content-section">
                        <?= wp_kses_post($para_quien); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- BENEFICIOS -->
            <?php if ($beneficios): ?>
                <section class="mb-4">
                        <h2 class="label"><?php _e('Beneficios al obtenerlo', 'maggiore'); ?></h2>
                    <div class="content-section">
                        <?= wp_kses_post($beneficios); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- VENTAJAS -->
            <?php if ($ventajas): ?>
                <section >
                        <h2 class="label"><?php _e('Ventajas frente a la competencia', 'maggiore'); ?></h2>
                    <div class="content-section">
                        <?= wp_kses_post($ventajas); ?>
                    </div>
                </section>
            <?php endif; ?>
</div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
            
            <!-- DIRECTOR A CARGO -->
            <?php if ($director_id && $director_nombre): ?>
                <section class="mb-2">
                    <div class="card-mg">
                        <h3 class="label">
                                <?php _e('Director a Cargo', 'maggiore'); ?>
                        </h3>
                        <a href="<?= esc_url(get_permalink($director_id)); ?>" 
                           class="person-card"
                         >
                            <?php if ($director_foto): ?>
                                <img src="<?= esc_url($director_foto); ?>"
                                     alt="<?= esc_attr("Foto de $director_nombre"); ?>"
                                     class="rounded-circle me-3"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            <?php endif; ?>
                            <div style="line-height: 1.3;">
                                <div style="color: white; font-weight: 500;">
                                    <?= esc_html($director_nombre); ?>
                                </div>
                                <?php if ($director_cargo): ?>
                                    <div style="color: var(--text-secondary); font-size: 0.875rem;">
                                        <?= esc_html($director_cargo); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                </section>
            <?php endif; ?>

            <!-- ÁREA RESPONSABLE -->
            <?php if ($area_id && $area_nombre): ?>
                <section class="mb-2">
                    <div class="card-mg">
                         <h3 class="label">
                            <?php _e('Área Responsable', 'maggiore'); ?>
                        </h3>
                        <a href="<?= esc_url(get_permalink($area_id)); ?>" 
                           class="service-tag"
                      >
                            <?= esc_html($area_nombre); ?>
                        </a>
                    </div>
                </section>
            <?php endif; ?>

            <!-- PRECIO -->
            <?php if ($precio): ?>
                <section class="mb-2">
                    <div class="card-mg">
                         <h3 class="label">
                            <?php _e('Precio', 'maggiore'); ?>
                        </h3>
                        <div class="content-section" style="font-size: 0.95rem;">
                            <?= wp_kses_post(wpautop($precio)); ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    </div>

    <!-- CASOS DE ÉXITO RELACIONADOS -->
    <?php if (!empty($casos_exito)): ?>
        <section class="mt-5">
            <div class="feature-name-2 mb-2">
                <h2><?php _e('Casos de Éxito', 'maggiore'); ?></h2>
            </div>
                <?php foreach ($casos_exito as $post): 
                    setup_postdata($post); 
                ?>
                        <?php get_template_part('template-parts/card', 'caso-exito'); ?>
                <?php endforeach; 
                wp_reset_postdata(); 
                ?>
        </section>
    <?php endif; ?>

    <!-- PORTAFOLIOS RELACIONADOS -->
    <?php if (!empty($portafolios)): ?>
        <section class="mt-5">
            <div class="feature-name-2 mb-3">
                <h2><?php _e('Portafolio', 'maggiore'); ?></h2>
            </div>
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3 g-2">
                <?php foreach ($portafolios as $post): 
                    setup_postdata($post); 
                ?>
                    <div class="col mt-0">
                        <?php get_template_part('template-parts/card', 'portafolio'); ?>
                    </div>
                <?php endforeach; 
                wp_reset_postdata(); 
                ?>
            </div>
        </section>
    <?php endif; ?>

    
 


    <!-- NAVEGACIÓN A OTROS SERVICIOS -->
        <nav class="mt-5 pt-5">
            <div class="row">
                <div class="col-md-6">
                    <?php
                    $prev_post = get_previous_post();
                    if ($prev_post): ?>
                        <a href="<?= get_permalink($prev_post); ?>">
                            <small class="tag-flecha"> <i class="bi bi-chevron-left"></i>                            
                            <?php _e(' Servicio anterior', 'maggiore'); ?>
                    </small>
                            <p style="color: white;"><?= esc_html($prev_post->post_title); ?></p>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-md-end">
                    <?php
                    $next_post = get_next_post();
                    if ($next_post): ?>
                        <a href="<?= get_permalink($next_post); ?>">
                            <small class="tag-flecha next-flecha">
                            <?php _e('Siguiente servicio ', 'maggiore'); ?>
                                <i class="bi bi-chevron-right"></i> </small>
                            <i></i>
                            <p style="color: white;"><?= esc_html($next_post->post_title); ?></p>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

</main>

<?php get_footer(); ?>
