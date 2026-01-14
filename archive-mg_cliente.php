<?php
if (!defined('ABSPATH')) exit;
get_header();

// Obtener el término actual si estamos en una taxonomía
$current_term = get_queried_object();
$is_taxonomy = is_tax('mg_industria');
$lang = function_exists('pll_current_language') ? pll_current_language() : false;

// Obtener todas las industrias para el filtro
$args_industrias = [
    'taxonomy' => 'mg_industria',
    'hide_empty' => true,
];
if ($lang) {
    $args_industrias['lang'] = $lang;
}
$industrias = get_terms($args_industrias);
?>

<main class="container py-5">
    
    <!-- Header -->
    <header class="mb-5" style="padding-top: 10vh;">
        <?php if ($is_taxonomy): ?>
            <h1 class="display-4 mb-3">
                <?= esc_html(sprintf(__('Clientes en %s', 'maggiore'), $current_term->name)); ?>
            </h1>
            <?php if ($current_term->description): ?>
                <p class="lead text-muted">
                    <?= esc_html($current_term->description); ?>
                </p>
            <?php endif; ?>
        <?php else: ?>
            <h1 class="display-4 mb-3">
                <?php _e('Nuestros Clientes', 'maggiore'); ?>
            </h1>
            <p class="lead text-muted">
                <?php _e('Empresas que confían en nosotros para impulsar su crecimiento.', 'maggiore'); ?>
            </p>
        <?php endif; ?>
    </header>

    <!-- Filtros por Industria -->
    <?php if (!empty($industrias) && !$is_taxonomy): ?>
        <section class="mb-5">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted me-2"><?php _e('Filtrar por industria:', 'maggiore'); ?></span>
                
                <a href="<?= get_post_type_archive_link('mg_cliente'); ?>" 
                   class="btn btn-sm <?= !$is_taxonomy ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <?php _e('Todos', 'maggiore'); ?>
                </a>
                
                <?php foreach ($industrias as $industria): ?>
                    <a href="<?= get_term_link($industria); ?>" 
                       class="btn btn-sm <?= ($is_taxonomy && $current_term->term_id === $industria->term_id) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <?= esc_html($industria->name); ?>
                        <span class="badge bg-light text-dark ms-1"><?= $industria->count; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Grid de clientes -->
    <?php if (have_posts()): ?>
        <section class="clientes-grid">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-2">
                <?php while (have_posts()): the_post(); ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'cliente'); ?>
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
            <nav class="mt-5" aria-label="<?php _e('Paginación de clientes', 'maggiore'); ?>">
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
        <!-- Mensaje si no hay clientes -->
        <div class="alert alert-info text-center py-5">
            <h2 class="h4 mb-3"><?php _e('No se encontraron clientes', 'maggiore'); ?></h2>
            <p class="mb-0">
                <?php _e('Actualmente no hay clientes publicados en esta sección.', 'maggiore'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</main>

<?php get_footer(); ?>
