<?php
if (!defined('ABSPATH')) exit;
get_header();

// Obtener el término actual si estamos en una taxonomía
$current_term = get_queried_object();
$is_taxonomy = is_tax('mg_categoria');
$lang = function_exists('pll_current_language') ? pll_current_language() : false;

// Obtener todas las categorías de servicio para el filtro
$args_categorias = [
    'taxonomy' => 'mg_categoria',
    'hide_empty' => true,
];
if ($lang) {
    $args_categorias['lang'] = $lang;
}
$categorias = get_terms($args_categorias);

// Obtener todas las áreas para un filtro adicional
$args_areas = [
    'post_type' => 'mg_area',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
];
if ($lang) {
    $args_areas['lang'] = $lang;
}
$areas_query = new WP_Query($args_areas);
?>

<main class="container py-5">
    
    <!-- Header -->
    <header class="mb-5" style="padding-top: 10vh;">
        <?php if ($is_taxonomy): ?>
            <h1 class="display-4 mb-3">
                <?= esc_html($current_term->name); ?>
            </h1>
            <?php if ($current_term->description): ?>
                <p class="lead text-muted">
                    <?= esc_html($current_term->description); ?>
                </p>
            <?php endif; ?>
        <?php else: ?>
            <h1 class="display-4 mb-3">
                <?php _e('Nuestros Servicios', 'maggiore'); ?>
            </h1>
            <p class="lead text-muted">
                <?php _e('Soluciones integrales para impulsar tu negocio al siguiente nivel.', 'maggiore'); ?>
            </p>
        <?php endif; ?>
    </header>

    <!-- Filtros por Categoría de Servicio -->
    <?php if (!empty($categorias) && !$is_taxonomy): ?>
        <section class="mb-5">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted me-2"><?php _e('Filtrar por categoría:', 'maggiore'); ?></span>
                
                <a href="<?= get_post_type_archive_link('mg_servicio'); ?>" 
                   class="btn btn-sm <?= !$is_taxonomy ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <?php _e('Todos', 'maggiore'); ?>
                </a>
                
                <?php foreach ($categorias as $categoria): ?>
                    <a href="<?= get_term_link($categoria); ?>" 
                       class="btn btn-sm <?= ($is_taxonomy && $current_term->term_id === $categoria->term_id) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <?= esc_html($categoria->name); ?>
                        <span class="badge bg-light text-dark ms-1"><?= $categoria->count; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Filtros por Área (opcional, solo en vista principal) -->
    <?php if ($areas_query->have_posts() && !$is_taxonomy && !is_paged()): ?>
        <section class="mb-5">
            <details>
                <summary class="text-muted" style="cursor: pointer;">
                    <?php _e('Ver servicios por área', 'maggiore'); ?>
                </summary>
                <div class="mt-3 ps-3">
                    <?php while ($areas_query->have_posts()): $areas_query->the_post(); ?>
                        <a href="<?= get_permalink(); ?>" class="d-block mb-2">
                            → <?= get_the_title(); ?>
                        </a>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </details>
        </section>
    <?php endif; ?>

    <!-- Grid de servicios -->
    <?php if (have_posts()): ?>
        <section class="servicios-grid">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-2">
                <?php while (have_posts()): the_post(); ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'servicio'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Paginación -->
        <?php
        $pagination = paginate_links([
            'prev_text' => '&laquo; ' . __('Anterior', 'maggiore'),
            'next_text' => __('Siguiente', 'maggiore') . ' &raquo;',
            'type' => 'array',
        ]);
        ?>
        
        <?php if ($pagination): ?>
            <nav class="mt-5" aria-label="<?php _e('Paginación de servicios', 'maggiore'); ?>">
                <ul class="pagination justify-content-center">
                    <?php foreach ($pagination as $page): ?>
                        <li class="page-item <?= strpos($page, 'current') !== false ? 'active' : ''; ?>">
                            <?= str_replace('page-numbers', 'page-link', $page); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>

    <?php else: ?>
        <!-- Mensaje si no hay servicios -->
        <div class="alert alert-info text-center py-5">
            <h2 class="h4 mb-3"><?php _e('No se encontraron servicios', 'maggiore'); ?></h2>
            <p class="mb-0">
                <?php _e('Actualmente no hay servicios publicados en esta sección.', 'maggiore'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</main>

<?php get_footer(); ?>
