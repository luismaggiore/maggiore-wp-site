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
?>

<main class="container py-5">
    
    <!-- Header -->
    <header class="mb-5" style="padding-top: 15vh;">
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
    <?php if (have_posts()): ?>
        <section class="team-grid">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-2">
                <?php while (have_posts()): the_post(); ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'equipo'); ?>
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
            <nav class="mt-5" aria-label="<?php _e('Paginación del equipo', 'maggiore'); ?>">
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
            <h2 class="h4 mb-3"><?php _e('No se encontraron miembros', 'maggiore'); ?></h2>
            <p class="mb-0">
                <?php _e('Actualmente no hay miembros del equipo publicados.', 'maggiore'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</main>

<?php get_footer(); ?>
