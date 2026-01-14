<?php
if (!defined('ABSPATH')) exit;
get_header();

$lang = function_exists('pll_current_language') ? pll_current_language() : false;

// Obtener todas las categorías de portafolio para el filtro
$args_categorias = [
    'taxonomy' => 'mg_categoria_portafolio',
    'hide_empty' => true,
];
if ($lang) {
    $args_categorias['lang'] = $lang;
}
$categorias = get_terms($args_categorias);
?>

<main class="container py-5">
    
    <!-- Header -->
    <header class="mb-5" style="padding-top: 15vh;">
        <h1 class="display-4 mb-3">
            <?php _e('Portafolio', 'maggiore'); ?>
        </h1>
        <p class="lead text-muted">
            <?php _e('Nuestros trabajos más destacados y proyectos realizados.', 'maggiore'); ?>
        </p>
    </header>

    <!-- Filtros por Categoría -->
    <?php if (!empty($categorias)): ?>
        <section class="mb-5">
            <div class="d-flex flex-wrap gap-1 align-items-center">
                <span class="text-muted me-2"><?php _e('Filtrar por categoría:', 'maggiore'); ?></span>

                <!-- Botón "Todos" siempre activo en el archive principal -->
                <a href="<?= esc_url(get_post_type_archive_link('mg_portafolio')); ?>"
                   class="btn-filter active">
                    <?php _e('Todos', 'maggiore'); ?>
                </a>

                <?php foreach ($categorias as $categoria): ?>
                    <?php $count = (int)$categoria->count; ?>
                    <a href="<?= esc_url(get_term_link($categoria)); ?>"
                       class="btn-filter">
                        <?= esc_html($categoria->name); ?>
                        <?php if ($count > 0): ?>
                            <span class="count-filter ms-1"><?= $count; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>

            </div>
        </section>
    <?php endif; ?>

    <!-- Grid de portafolio -->
    <?php if (have_posts()): ?>
        <section class="mt-3">
            <div class="row g-2">
                <?php
                get_template_part('template-parts/loops/loop', 'portafolio');
                ?>
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
            <nav class="mt-5" aria-label="<?php _e('Paginación de portafolio', 'maggiore'); ?>">
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
        <!-- Mensaje si no hay proyectos -->
        <div class="alert alert-info text-center py-5">
            <h2 class="h4 mb-3"><?php _e('No se encontraron proyectos', 'maggiore'); ?></h2>
            <p class="mb-0">
                <?php _e('Actualmente no hay proyectos publicados.', 'maggiore'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</main>

<?php get_footer(); ?>
