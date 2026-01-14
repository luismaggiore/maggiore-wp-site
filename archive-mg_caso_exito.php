<?php
if (!defined('ABSPATH')) exit;
get_header();

// Obtener el término actual si estamos en una taxonomía
$current_term = get_queried_object();
$is_taxonomy = is_tax('mg_industria');
$lang = function_exists('pll_current_language') ? pll_current_language() : false;

// Obtener todas las industrias para el filtro (los casos heredan industria del cliente)
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
    <header class="mb-5" style="padding-top: 15vh;">
        <?php if ($is_taxonomy): ?>
            <h1 class="display-4 mb-3">
                <?= esc_html(sprintf(__('Casos de Éxito en %s', 'maggiore'), $current_term->name)); ?>
            </h1>
            <?php if ($current_term->description): ?>
                <p class="lead text-muted">
                    <?= esc_html($current_term->description); ?>
                </p>
            <?php endif; ?>
        <?php else: ?>
            <h1 class="display-4 mb-3">
                <?php _e('Casos de Éxito', 'maggiore'); ?>
            </h1>
            <p class="lead text-muted">
                <?php _e('Historias reales de transformación y resultados excepcionales.', 'maggiore'); ?>
            </p>
        <?php endif; ?>
    </header>
<!-- Filtros por Industria -->
<?php if (!empty($industrias) && !$is_taxonomy): ?>
  <section class="mb-5">
    <div class="d-flex flex-wrap gap-1 align-items-center">
      <span class="text-muted me-2"><?php _e('Filtrar por industria:', 'maggiore'); ?></span>

      <?php $is_all_active = !$is_taxonomy; ?>

      <a href="<?= esc_url(get_post_type_archive_link('mg_caso_exito')); ?>"
         class="btn-filter <?= $is_all_active ? 'active' : ''; ?>">
        <?php _e('Todos', 'maggiore'); ?>
      </a>

      <?php foreach ($industrias as $industria): ?>
        <?php
          $is_term_active = $is_taxonomy
            && isset($current_term->term_id)
            && ((int)$current_term->term_id === (int)$industria->term_id);
          
          // Añadir parámetro ?tipo=casos al link de taxonomía
          $industria_url = add_query_arg('tipo', 'casos', get_term_link($industria));
        ?>
        <a href="<?= esc_url($industria_url); ?>"
           class="btn-filter <?= $is_term_active ? 'active' : ''; ?>">
          <?= esc_html($industria->name); ?>
        </a>
      <?php endforeach; ?>

    </div>
  </section>
<?php endif; ?>

    <!-- Grid de casos de éxito -->
    <?php if (have_posts()): ?>
        <section class="casos-grid">
            <div class="row g-2">
                <?php while (have_posts()): the_post(); ?>
                    <div class="col-xl-4 col-md-6" style="min-height:100%">
                        <?php get_template_part('template-parts/card', 'caso-exito'); ?>
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
            <nav class="mt-5" aria-label="<?php _e('Paginación de casos de éxito', 'maggiore'); ?>">
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
        <!-- Mensaje si no hay casos -->
        <div class="alert alert-info text-center py-5">
            <h2 class="h4 mb-3"><?php _e('No se encontraron casos de éxito', 'maggiore'); ?></h2>
            <p class="mb-0">
                <?php _e('Actualmente no hay casos de éxito publicados en esta sección.', 'maggiore'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</main>

<?php get_footer(); ?>
