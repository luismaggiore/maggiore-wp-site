<?php
/**
 * Archive de Taxonomía: Industria
 * 
 * Muestra clientes Y casos de éxito de una industria específica
 * Con filtros para separar por tipo de contenido
 */

if (!defined('ABSPATH')) exit;
get_header();

$current_term = get_queried_object();
$term_id = $current_term->term_id;
$lang = function_exists('pll_current_language') ? pll_current_language() : false;

// Obtener el filtro actual (soporta URL limpia /clientes/ o /casos/ via query var, o ?tipo= legacy)
$filter_qv = get_query_var('mg_tipo', '');
if ($filter_qv) {
    $filter = sanitize_text_field($filter_qv);
} elseif (isset($_GET['tipo'])) {
    $filter = sanitize_text_field($_GET['tipo']);
} else {
    $filter = 'todos';
}
// Normalizar valores válidos
if (!in_array($filter, ['todos', 'clientes', 'casos'])) {
    $filter = 'todos';
}

// Construir query según el filtro
$post_types = [];
if ($filter === 'todos') {
    $post_types = ['mg_cliente', 'mg_caso_exito'];
} elseif ($filter === 'clientes') {
    $post_types = ['mg_cliente'];
} elseif ($filter === 'casos') {
    $post_types = ['mg_caso_exito'];
}

// Query principal
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$args = [
    'post_type' => $post_types,
    'posts_per_page' => 12,
    'paged' => $paged,
    'tax_query' => [
        [
            'taxonomy' => 'mg_industria',
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

// Contar totales por tipo
$count_clientes = 0;
$count_casos = 0;

if ($filter === 'todos') {
    $count_clientes_query = new WP_Query([
        'post_type' => 'mg_cliente',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'tax_query' => [
            [
                'taxonomy' => 'mg_industria',
                'field' => 'term_id',
                'terms' => $term_id,
            ]
        ],
        'lang' => $lang ?: ''
    ]);
    $count_clientes = $count_clientes_query->found_posts;

    $count_casos_query = new WP_Query([
        'post_type' => 'mg_caso_exito',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'tax_query' => [
            [
                'taxonomy' => 'mg_industria',
                'field' => 'term_id',
                'terms' => $term_id,
            ]
        ],
        'lang' => $lang ?: ''
    ]);
    $count_casos = $count_casos_query->found_posts;
}

// Obtener el color de la industria
$term_color = get_term_meta($term_id, 'term_color', true);
$term_slug = $current_term->slug;
?>

<main class="container py-5">
    
    <!-- Header del Archive -->
    <header class="mb-1 p-top"  >
        <div class="d-flex align-items-center gap-3 mb-3">
            <!-- Badge con color de la industria -->
            <?php if ($term_color): ?>
                <div class="badge-<?= esc_attr($term_slug); ?>" style="padding: 8px 16px; border-radius: 8px; font-size: 14px;">
                    <?= esc_html($current_term->name); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <h1 class="display-4 mb-3">
            <?= esc_html(sprintf(__('Industria: %s', 'maggiore'), $current_term->name)); ?>
        </h1>
        
        <?php if ($current_term->description): ?>
            <p class="lead text-muted">
                <?= esc_html($current_term->description); ?>
            </p>
        <?php endif; ?>

        <!-- Contador de contenido -->
        <?php if ($filter === 'todos'): ?>
            <p class="text-muted mt-3">
                <strong><?= $count_clientes + $count_casos; ?></strong> 
                <?php _e('elementos en total', 'maggiore'); ?>
                (<?= $count_clientes; ?> <?php _e('clientes', 'maggiore'); ?>, 
                <?= $count_casos; ?> <?php _e('casos de éxito', 'maggiore'); ?>)
            </p>
        <?php else: ?>
            <p class="text-muted mt-3">
                <strong><?= $query->found_posts; ?></strong> 
                <?php echo $filter === 'clientes' ? __('clientes', 'maggiore') : __('casos de éxito', 'maggiore'); ?>
            </p>
        <?php endif; ?>
    </header>
    <?php
    // Obtener todas las industrias para navegación
    $all_industries = get_terms([
        'taxonomy' => 'mg_industria',
        'hide_empty' => true,
        'lang' => $lang ?: ''
    ]);
    
    // Solo mostrar si hay más de una industria
    if (!empty($all_industries) && count($all_industries) > 1):
    ?>

    <section class="mb-4">
      <!-- Separador visual -->
     <div class="mt-2 d-flex flex-wrap gap-1 align-items-center"> 
      <span class="text-muted me-1 "><?php _e('Otras industrias:', 'maggiore'); ?></span>
      
      <?php
      foreach ($all_industries as $industry):
          // Saltar la industria actual
          if ($industry->term_id === $term_id) continue;
          
          // Mantener el filtro actual en el enlace a otra industria (URL limpia)
          $other_industry_url = trailingslashit(get_term_link($industry));
          if ($filter === 'clientes') {
              $other_industry_url .= 'clientes/';
          } elseif ($filter === 'casos') {
              $other_industry_url .= 'casos/';
          }
      ?>
        <a href="<?= esc_url($other_industry_url); ?>" 
           class="btn-filter">
          <?= esc_html($industry->name); ?>
          <span class="count-filter ms-1"><?= $industry->count; ?></span>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
    </div></section>
<!-- Filtros por tipo de contenido -->
<section class="mb-2">
    
  <div class="d-flex flex-wrap gap-1 align-items-center">

    <?php
      $base_url = trailingslashit(get_term_link($current_term));

      $is_all_active      = ($filter === 'todos');
      $is_clientes_active = ($filter === 'clientes');
      $is_casos_active    = ($filter === 'casos');

      $total = (int)($count_clientes + $count_casos);

      // URLs limpias para los filtros
      $url_clientes = $base_url . 'clientes/';
      $url_casos    = $base_url . 'casos/';
    ?>

    <a href="<?= esc_url($base_url); ?>"
       class="btn-filter <?= $is_all_active ? 'active' : ''; ?>">
      <?php _e('Todo', 'maggiore'); ?>
      <?php if ($total > 0): ?>
        <span class="count-filter ms-1"><?= $total; ?></span>
      <?php endif; ?>
    </a>

    <a href="<?= esc_url($url_clientes); ?>"
       class="btn-filter <?= $is_clientes_active ? 'active' : ''; ?>">
      <?php _e('Clientes', 'maggiore'); ?>
      <?php if ((int)$count_clientes > 0): ?>
        <span class="count-filter ms-1"><?= (int)$count_clientes; ?></span>
      <?php endif; ?>
    </a>

    <a href="<?= esc_url($url_casos); ?>"
       class="btn-filter <?= $is_casos_active ? 'active' : ''; ?>">
      <?php _e('Casos de Éxito', 'maggiore'); ?>
      <?php if ((int)$count_casos > 0): ?>
        <span class="count-filter ms-1"><?= (int)$count_casos; ?></span>
      <?php endif; ?>
    </a>

  </div>
</section>
<section class="mb-4">
  <div class="d-flex flex-wrap gap-1 align-items-center">
    
    <!-- Enlaces de regreso según el filtro activo -->
    <?php if ($filter === 'clientes' || $filter === 'todos'): ?>
      <a href="<?= esc_url(get_post_type_archive_link('mg_cliente')); ?>" 
         class="btn-filter">
       
        <?php _e('Todos los clientes', 'maggiore'); ?>
      </a>
    <?php endif; ?>
    
    <?php if ($filter === 'casos' || $filter === 'todos'): ?>
      <a href="<?= esc_url(get_post_type_archive_link('mg_caso_exito')); ?>" 
         class="btn-filter">
     
        <?php _e('Todos los casos de éxito', 'maggiore'); ?>
      </a>
    <?php endif; ?>


  </div>
    
</section>

    <!-- Grid Masonry -->
    <?php if ($query->have_posts()): ?>
        <section class="mb-5">
            <div class="masonry-grid" id="masonry-grid">
                <!-- Sizer: define el ancho de columna para Masonry (sin renderizar) -->
                <div class="masonry-sizer"></div>
                <?php while ($query->have_posts()): $query->the_post(); ?>
                    <div class="masonry-item">
                        <?php
                        $post_type = get_post_type();
                        if ($post_type === 'mg_cliente') {
                            get_template_part('template-parts/card', 'cliente');
                        } elseif ($post_type === 'mg_caso_exito') {
                            get_template_part('template-parts/card', 'caso-exito');
                        }
                        ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Paginación -->
        <?php
        // Base de paginación según filtro activo
        $paginate_base = trailingslashit(get_term_link($current_term));
        if ($filter === 'clientes') $paginate_base .= 'clientes/';
        elseif ($filter === 'casos')   $paginate_base .= 'casos/';

        $pagination = paginate_links([
            'base'      => $paginate_base . '%_%',
            'format'    => 'page/%#%/',
            'total'     => $query->max_num_pages,
            'current'   => $paged,
            'prev_text' => '&laquo; ' . __('Anterior', 'maggiore'),
            'next_text' => __('Siguiente', 'maggiore') . ' &raquo;',
            'type'      => 'array',
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
        <!-- Mensaje si no hay contenido -->
        <div class="alert alert-info text-center py-5">
            <h2 class="h4 mb-3">
                <?php _e('No se encontró contenido', 'maggiore'); ?>
            </h2>
            <p class="mb-0">
                <?php 
                if ($filter === 'clientes') {
                    _e('Actualmente no hay clientes en esta industria.', 'maggiore');
                } elseif ($filter === 'casos') {
                    _e('Actualmente no hay casos de éxito en esta industria.', 'maggiore');
                } else {
                    _e('Actualmente no hay contenido disponible para esta industria.', 'maggiore');
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</main>

<!-- Masonry.js para el layout -->
<style>
/* ── Masonry grid ── */
.masonry-grid { position: relative; }

/* El sizer define el ancho de columna; Masonry lo usa como referencia */
.masonry-sizer,
.masonry-item  { width: calc(33.333% - 11px); }   /* 3 cols en desktop */

@media (max-width: 991.98px) {
    .masonry-sizer,
    .masonry-item { width: calc(50% - 8px); }      /* 2 cols en tablet */
}
@media (max-width: 575.98px) {
    .masonry-sizer,
    .masonry-item { width: 100%; }                  /* 1 col en móvil */
}

.masonry-item { margin-bottom: 16px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"></script>
<script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var grid = document.getElementById('masonry-grid');
    if (!grid) return;

    // Esperar a que carguen las imágenes antes de inicializar
    imagesLoaded(grid, function() {
        new Masonry(grid, {
            itemSelector:   '.masonry-item',
            columnWidth:    '.masonry-sizer',   // sizer como referencia de columna
            percentPosition: true,
            gutter:         16
        });
    });
});
</script>

<?php get_footer(); ?>
