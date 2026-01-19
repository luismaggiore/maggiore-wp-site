<?php
/**
 * Archive de Taxonomía: Equipos
 * 
 * Muestra todos los miembros de un equipo específico (ej: Diseño, Desarrollo, Marketing)
 * Con opción de mostrar su contenido relacionado
 */

if (!defined('ABSPATH')) exit;
get_header();

$current_term = get_queried_object();
$term_id = $current_term->term_id;
$lang = function_exists('pll_current_language') ? pll_current_language() : false;

// Query principal
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$args = [
    'post_type' => 'mg_equipo',
    'posts_per_page' => 12,
    'paged' => $paged,
    'tax_query' => [
        [
            'taxonomy' => 'mg_equipos',
            'field' => 'term_id',
            'terms' => $term_id,
        ]
    ],
    'orderby' => 'title',
    'order' => 'ASC'
];

if ($lang) {
    $args['lang'] = $lang;
}

$query = new WP_Query($args);

// Obtener el color del equipo
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

   
    </header>
<?php
// Obtener todos los equipos para navegación
$all_teams = get_terms([
  'taxonomy'   => 'mg_equipos',
  'hide_empty' => true,
  'lang'       => $lang ?: '',
]);
?>

<!-- Filtros por Equipo -->
<?php if (!empty($all_teams) && count($all_teams) > 1): ?>
  <section class="mb-5">
    <div class="d-flex flex-wrap gap-1 align-items-center">
    
      <?php
        // En este template estás en taxonomía, así que "Todos" normalmente NO es active
        $is_all_active = false;
      ?>
      <a href="<?= esc_url(get_post_type_archive_link('mg_equipo')); ?>"
         class="btn-filter <?= $is_all_active ? 'active' : ''; ?>">
        <?php _e('Todos', 'maggiore'); ?>
      </a>

      <?php foreach ($all_teams as $team): ?>
        <?php
          $is_team_active = ((int)$team->term_id === (int)$term_id);
          $count = (int)$team->count;
        ?>
        <a href="<?= esc_url(get_term_link($team)); ?>"
           class="btn-filter <?= $is_team_active ? 'active' : ''; ?>">
          <?= esc_html($team->name); ?>
          <?php if ($count > 0): ?>
            <span class="count-filter ms-1"><?= $count; ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>

    </div>
  </section>
<?php endif; ?>

    <!-- Grid de miembros -->
    <?php if ($query->have_posts()): ?>
        <section class="team-grid mb-5">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-2">
                <?php while ($query->have_posts()): $query->the_post(); ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'equipo'); ?>
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
        <!-- Mensaje si no hay miembros -->
        <div class="alert alert-info text-center py-5">
            <h2 class="h4 mb-3">
                <?php _e('No se encontraron miembros', 'maggiore'); ?>
            </h2>
            <p class="mb-0">
                <?php _e('Actualmente no hay miembros asignados a este equipo.', 'maggiore'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</main>

<?php get_footer(); ?>
