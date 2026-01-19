<?php
if (!defined('ABSPATH')) exit;
get_header();

// Obtener el término actual si estamos en una taxonomía
$current_term = get_queried_object();
$is_taxonomy = is_tax('mg_equipos');
$lang = function_exists('pll_current_language') ? pll_current_language() : false;

// Obtener todos los equipos para el filtro
$args_equipos = [
    'taxonomy' => 'mg_equipos',
    'hide_empty' => true,
];
if ($lang) {
    $args_equipos['lang'] = $lang;
}
$equipos = get_terms($args_equipos);

// Query personalizada para mostrar todos los miembros
$args_team = [
    'post_type' => 'mg_equipo',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
];

// Si estamos en una taxonomía, filtrar por el término actual
if ($is_taxonomy && isset($current_term->term_id)) {
    $args_team['tax_query'] = [
        [
            'taxonomy' => 'mg_equipos',
            'field' => 'term_id',
            'terms' => $current_term->term_id,
        ]
    ];
}

// Filtrar por idioma si está activo Polylang
if ($lang) {
    $args_team['lang'] = $lang;
}

$team_query = new WP_Query($args_team);
?>

<main class="container py-5">
    
    <!-- Header -->
    <header class="mb-5 p-top" >
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
                <?php _e('Nuestro Equipo', 'maggiore'); ?>
            </h1>
            <p class="lead text-muted">
                <?php _e('Conoce a las personas que hacen posible nuestro trabajo.', 'maggiore'); ?>
            </p>
        <?php endif; ?>
    </header>
<!-- Filtros por Equipo -->
<?php if (!empty($equipos) && !$is_taxonomy): ?>
  <section class="mb-5">
    <div class="d-flex flex-wrap gap-1 align-items-center">

      <?php $is_all_active = !$is_taxonomy; ?>

      <a href="<?= esc_url(get_post_type_archive_link('mg_equipo')); ?>"
         class="btn-filter <?= $is_all_active ? 'active' : ''; ?>">
        <?php _e('Todos', 'maggiore'); ?>
      </a>

      <?php foreach ($equipos as $equipo): ?>
        <?php
          $is_term_active = $is_taxonomy
            && isset($current_term->term_id)
            && ((int)$current_term->term_id === (int)$equipo->term_id);

          $count = (int)$equipo->count;
        ?>
        <a href="<?= esc_url(get_term_link($equipo)); ?>"
           class="btn-filter <?= $is_term_active ? 'active' : ''; ?>">
          <?= esc_html($equipo->name); ?>
          <?php if ($count > 0): ?>
            <span class="count-filter ms-1"><?= $count; ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>

    </div>
  </section>
<?php endif; ?>


    <!-- Grid de miembros del equipo -->
    <?php if ($team_query->have_posts()): ?>
        <section class="team-grid">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2">
                <?php while ($team_query->have_posts()): $team_query->the_post(); ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'equipo'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

    <?php else: ?>
        <!-- Mensaje si no hay miembros -->
        <div class="alert alert-info text-center py-5">
            <h2 class="h4 mb-3"><?php _e('No se encontraron miembros', 'maggiore'); ?></h2>
            <p class="mb-0">
                <?php _e('Actualmente no hay miembros del equipo publicados.', 'maggiore'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</main>

<?php get_footer(); ?>
