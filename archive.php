<?php
/**
 * Archive Template - Blog Principal
 * 
 * Este template maneja:
 * - Archive principal del blog (home del blog)
 * - Archives de categorías
 * - Archives de tags
 * - Archives de autor
 * - Archives por fecha
 * 
 * Usa card-articulo.php para mostrar los posts
 */

if (!defined('ABSPATH')) exit;
get_header();

$lang = function_exists('pll_current_language') ? pll_current_language() : false;

// Detectar tipo de archive
$is_category = is_category();
$is_tag = is_tag();
$is_author = is_author();
$is_date = is_date();
$is_blog_home = is_home();

// Obtener categorías para el filtro
$args_categorias = [
    'taxonomy' => 'category',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
];
if ($lang) {
    $args_categorias['lang'] = $lang;
}
$categorias = get_terms($args_categorias);

// Obtener tags populares para el filtro
$args_tags = [
    'taxonomy' => 'post_tag',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 10,
];
if ($lang) {
    $args_tags['lang'] = $lang;
}
$tags_populares = get_terms($args_tags);

// Obtener autores (miembros del equipo que han escrito)
$args_autores = [
    'post_type' => 'mg_equipo',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'meta_query' => [
        [
            'key' => '_mg_posts_autor',
            'compare' => 'EXISTS'
        ]
    ]
];
if ($lang) {
    $args_autores['lang'] = $lang;
}
$autores = new WP_Query($args_autores);
?>

<main class="container py-5">
    
    <!-- Header del Archive -->
    <header class="blog-archive-header mb-5 p-top" >
        <?php if ($is_category): ?>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="category-badge">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                    </svg>
                </div>
                <h1 class="display-4 mb-0">
                    <?= single_cat_title('', false); ?>
                </h1>
            </div>
            <?php if (category_description()): ?>
                <p class="lead text-muted">
                    <?= category_description(); ?>
                </p>
            <?php endif; ?>

        <?php elseif ($is_tag): ?>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="tag-badge">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2zm3.5 4a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                    </svg>
                </div>
                <h1 class="display-4 mb-0">
                    #<?= single_tag_title('', false); ?>
                </h1>
            </div>
            <?php if (tag_description()): ?>
                <p class="lead text-muted">
                    <?= tag_description(); ?>
                </p>
            <?php endif; ?>

        <?php elseif ($is_author): ?>
            <?php
            $author = get_queried_object();
            $author_id = $author->ID;
            
            // Intentar encontrar el miembro del equipo asociado
            $args_miembro = [
                'post_type' => 'mg_equipo',
                'meta_query' => [
                    [
                        'key' => 'mg_equipo_usuario_wp',
                        'value' => $author_id,
                        'compare' => '='
                    ]
                ]
            ];
            $miembro = new WP_Query($args_miembro);
            
            if ($miembro->have_posts()):
                $miembro->the_post();
                $foto = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                $cargo = get_post_meta(get_the_ID(), 'mg_equipo_cargo', true);
                wp_reset_postdata();
            ?>
                <div class="author-header d-flex align-items-center gap-4 mb-4">
                    <?php if ($foto): ?>
                        <img src="<?= esc_url($foto); ?>" 
                             alt="<?= esc_attr($author->display_name); ?>"
                             class="rounded-circle"
                             width="100"
                             height="100"
                             style="object-fit: cover;">
                    <?php endif; ?>
                    <div>
                        <h1 class="display-4 mb-2">
                            <?= esc_html($author->display_name); ?>
                        </h1>
                        <?php if ($cargo): ?>
                            <p class="lead mb-0"><?= esc_html($cargo); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <h1 class="display-4 mb-3">
                    <?= esc_html($author->display_name); ?>
                </h1>
            <?php endif; ?>
            <p class="text-muted">
                <?php printf(__('Artículos escritos por %s', 'maggiore'), $author->display_name); ?>
            </p>

        <?php elseif ($is_date): ?>
            <h1 class="display-4 mb-3">
                <?php
                if (is_year()) {
                    printf(__('Artículos de %s', 'maggiore'), get_the_date('Y'));
                } elseif (is_month()) {
                    printf(__('Artículos de %s', 'maggiore'), get_the_date('F Y'));
                } elseif (is_day()) {
                    printf(__('Artículos del %s', 'maggiore'), get_the_date());
                }
                ?>
            </h1>

        <?php else: ?>
            <h1 class="display-4 mb-3">
                <?php _e('Blog', 'maggiore'); ?>
            </h1>
            <p class="lead text-muted">
                <?php _e('Insights, tendencias y conocimientos del mundo del marketing digital.', 'maggiore'); ?>
            </p>
        <?php endif; ?>

        <!-- Contador de artículos -->
        <div class="mt-4">
            <p class="text-muted mb-0">
                <?php
                global $wp_query;
                $total = $wp_query->found_posts;
                printf(
                    _n('%s artículo encontrado', '%s artículos encontrados', $total, 'maggiore'),
                    '<strong>' . number_format_i18n($total) . '</strong>'
                );
                ?>
            </p>
        </div>
    </header>

    <!-- Filtros -->
    <section class="blog-filters mb-5">
        <div class="row g-3">
            
            <!-- Filtro por Categorías -->
            <?php if (!empty($categorias) && !$is_category): ?>
                <div class="col-lg-6">
                    <details class="filter-section">
                        <summary class="fw-bold" style="cursor: pointer;">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                            </svg>
                            <?php _e('Filtrar por categoría', 'maggiore'); ?>
                        </summary>
                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <a href="<?= get_permalink(get_option('page_for_posts')); ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <?php _e('Todas', 'maggiore'); ?>
                            </a>
                            <?php foreach ($categorias as $categoria): ?>
                                <a href="<?= get_category_link($categoria->term_id); ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <?= esc_html($categoria->name); ?>
                                    <span class="badge bg-light text-dark ms-1"><?= $categoria->count; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </details>
                </div>
            <?php endif; ?>

            <!-- Filtro por Tags -->
            <?php if (!empty($tags_populares) && !$is_tag): ?>
                <div class="col-lg-6">
                    <details class="filter-section">
                        <summary class="fw-bold" style="cursor: pointer;">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                <path d="M2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2zm3.5 4a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                            </svg>
                            <?php _e('Tags populares', 'maggiore'); ?>
                        </summary>
                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <?php foreach ($tags_populares as $tag): ?>
                                <a href="<?= get_tag_link($tag->term_id); ?>" 
                                   class="btn btn-sm btn-outline-secondary">
                                    #<?= esc_html($tag->name); ?>
                                    <span class="badge bg-light text-dark ms-1"><?= $tag->count; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </details>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- Grid de Artículos -->
    <?php if (have_posts()): ?>
        <section class="blog-grid">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
                <?php while (have_posts()): the_post(); ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'articulo'); ?>
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
            <nav class="mt-5" aria-label="<?php _e('Paginación del blog', 'maggiore'); ?>">
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
        <!-- Mensaje cuando no hay artículos -->
        <div class="alert alert-info text-center py-5">
            <svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16" class="mb-3">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            <h2 class="h4 mb-3"><?php _e('No se encontraron artículos', 'maggiore'); ?></h2>
            <p class="mb-3">
                <?php _e('No hay artículos publicados que coincidan con tu búsqueda.', 'maggiore'); ?>
            </p>
            <a href="<?= get_permalink(get_option('page_for_posts')); ?>" class="btn btn-primary">
                <?php _e('Ver todos los artículos', 'maggiore'); ?>
            </a>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

    <!-- Sección adicional: Newsletter o CTA -->
    <?php if ($is_blog_home && !is_paged()): ?>
        <section class="blog-cta mt-5 pt-5 border-top">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="h3 mb-3"><?php _e('Mantente actualizado', 'maggiore'); ?></h2>
                    <p class="text-muted mb-0">
                        <?php _e('Descubre las últimas tendencias, estrategias y casos de éxito en marketing digital.', 'maggiore'); ?>
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="<?= home_url('/contacto'); ?>" class="btn btn-primary">
                        <?php _e('Contáctanos', 'maggiore'); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
