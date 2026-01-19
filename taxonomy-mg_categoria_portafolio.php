<?php
/**
 * Archive de Taxonomía: Categorías de Portafolio
 * 
 * Muestra todos los portafolios de una categoría específica (ej: Diseño, Marketing, Inteligencia)
 * Con navegación entre categorías
 */

if (!defined('ABSPATH')) exit;
get_header();

$current_term = get_queried_object();
$term_id = $current_term->term_id;
$lang = function_exists('pll_current_language') ? pll_current_language() : false;

// Query principal
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$args = [
    'post_type' => 'mg_portafolio',
    'posts_per_page' => 12,
    'paged' => $paged,
    'tax_query' => [
        [
            'taxonomy' => 'mg_categoria_portafolio',
            'field' => 'term_id',
            'terms' => $term_id,
        ]
    ],
    'orderby' => 'date',
    'order' => 'DESC'
];

if ($lang) {
    $args['lang'] = $lang;
}

$query = new WP_Query($args);

// Obtener todas las categorías para navegación
$all_categories = get_terms([
    'taxonomy' => 'mg_categoria_portafolio',
    'hide_empty' => true,
    'lang' => $lang ?: '',
]);

// Obtener el color de la categoría (si usas term_meta para colores)
$term_color = get_term_meta($term_id, 'term_color', true);
$term_slug = $current_term->slug;
?>

<main class="container py-5">
    
    <!-- Header del Archive -->
    <header class="mb-5 p-top" >
        
        <h1 class="display-4 mb-3">
            <?= esc_html($current_term->name); ?>
        </h1>
        
        <?php if ($current_term->description): ?>
            <p class="lead text-muted">
                <?= esc_html($current_term->description); ?>
            </p>
        <?php endif; ?>

        <!-- Contador de portafolios -->
        <p class="text-muted mt-3">
            <strong><?= $query->found_posts; ?></strong> 
            <?php echo _n('proyecto', 'proyectos', $query->found_posts, 'maggiore'); ?>
        </p>
    </header>

    <!-- Filtros por Categoría -->
    <?php if (!empty($all_categories)): ?>
        <section class="mb-5">
            <div class="d-flex flex-wrap gap-1 align-items-center">
                
                <!-- Botón "Todos" (link al archive principal) -->
                <a href="<?= esc_url(get_post_type_archive_link('mg_portafolio')); ?>"
                   class="btn-filter">
                    <?php _e('Todos', 'maggiore'); ?>
                </a>

                <?php foreach ($all_categories as $categoria): ?>
                    <?php
                    $is_category_active = ((int)$categoria->term_id === (int)$term_id);
                    $count = (int)$categoria->count;
                    ?>
                    <a href="<?= esc_url(get_term_link($categoria)); ?>"
                       class="btn-filter <?= $is_category_active ? 'active' : ''; ?>">
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
    <?php if ($query->have_posts()): ?>
        <section class="mt-3">
            <div class="row g-2">
                <?php while ($query->have_posts()): $query->the_post(); ?>
                    <?php get_template_part('template-parts/loops/loop', 'portafolio'); ?>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Paginación -->
        <?php
        $pagination = paginate_links([
            'total' => $query->max_num_pages,
            'current' => $paged,
            'prev_text' => '&laquo; ' . __('Anterior', 'maggiore'),
            'next_text' => __('Siguiente', 'maggiore') . ' &raquo;',
            'type' => 'array'
        ]);
        ?>
        
        <?php if ($pagination): ?>
            <nav class="mt-5" aria-label="<?php _e('Paginación', 'maggiore'); ?>">
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
        <!-- Mensaje si no hay portafolios -->
        <div class="alert alert-info text-center py-5">
            <h2 class="h4 mb-3">
                <?php _e('No se encontraron proyectos', 'maggiore'); ?>
            </h2>
            <p class="mb-0">
                <?php 
                printf(
                    __('Actualmente no hay proyectos en la categoría %s.', 'maggiore'), 
                    '<strong>' . esc_html($current_term->name) . '</strong>'
                );
                ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</main>

<?php get_footer(); ?>
