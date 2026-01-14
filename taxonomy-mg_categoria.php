<?php
/**
 * Archive de Taxonomía: Categoría de Servicio
 * 
 * Muestra todos los servicios de una categoría específica
 * (ej: SEO, Social Media, Paid Media)
 */

if (!defined('ABSPATH')) exit;
get_header();

$current_term = get_queried_object();
$term_id = $current_term->term_id;
$lang = function_exists('pll_current_language') ? pll_current_language() : false;

// Query principal
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$args = [
    'post_type' => 'mg_servicio',
    'posts_per_page' => 12,
    'paged' => $paged,
    'tax_query' => [
        [
            'taxonomy' => 'mg_categoria',
            'field' => 'term_id',
            'terms' => $term_id,
        ]
    ],
    'orderby' => 'menu_order',
    'order' => 'ASC'
];

if ($lang) {
    $args['lang'] = $lang;
}

$query = new WP_Query($args);

// Obtener el color de la categoría
$term_color = get_term_meta($term_id, 'term_color', true);
$term_slug = $current_term->slug;

// Obtener todas las categorías para navegación
$all_categories = get_terms([
    'taxonomy' => 'mg_categoria',
    'hide_empty' => true,
    'lang' => $lang ?: ''
]);
?>

<main class="container py-5">
    
    <!-- Header del Archive -->
    <header class="mb-5" style="padding-top: 15vh;">
     
        <h1 class="display-4 mb-3">
            <?= esc_html($current_term->name); ?>
        </h1>
        
        <?php if ($current_term->description): ?>
            <p class="lead text-muted">
                <?= esc_html($current_term->description); ?>
            </p>
        <?php endif; ?>

        <!-- Contador -->
        <p class="text-muted mt-3">
            <strong><?= $query->found_posts; ?></strong> 
            <?php echo _n('servicio', 'servicios', $query->found_posts, 'maggiore'); ?>
        </p>
    </header>

<!-- Navegación entre categorías -->
<?php if (!empty($all_categories) && count($all_categories) > 1): ?>
  <section class="mb-5">
    <div class="d-flex flex-wrap gap-1 align-items-center">
    

      <?php
        // En este template estás en taxonomía, así que "Todos" normalmente NO es active
        $is_all_active = false;
      ?>
      <a href="<?= esc_url(get_post_type_archive_link('mg_servicio')); ?>"
         class="btn-filter <?= $is_all_active ? 'active' : ''; ?>">
        <?php _e('Todos los servicios', 'maggiore'); ?>
      </a>

      <?php foreach ($all_categories as $categoria): ?>
        <?php
          $is_cat_active = ((int)$categoria->term_id === (int)$term_id);
        ?>
        <a href="<?= esc_url(get_term_link($categoria)); ?>"
           class="btn-filter <?= $is_cat_active ? 'active' : 'btn-filter'; ?>">
          <?= esc_html($categoria->name); ?>
          <span class="count-filter ms-1"><?= (int)$categoria->count; ?></span>
        </a>
      <?php endforeach; ?>

    </div>
  </section>
<?php endif; ?>

    <!-- Grid de servicios -->
    <?php if ($query->have_posts()): ?>
        <section class="services-grid mb-5">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
                <?php while ($query->have_posts()): $query->the_post(); ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'servicio'); ?>
                    </div>
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
        <!-- Mensaje si no hay servicios -->
        <div class="alert alert-info text-center py-5">
            <h2 class="h4 mb-3">
                <?php _e('No se encontraron servicios', 'maggiore'); ?>
            </h2>
            <p class="mb-0">
                <?php _e('Actualmente no hay servicios en esta categoría.', 'maggiore'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</main>

<?php get_footer(); ?>
