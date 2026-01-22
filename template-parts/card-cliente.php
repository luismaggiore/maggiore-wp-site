<?php
if (!defined('ABSPATH')) exit;
global $post;

$cliente_id = get_the_ID();
$titulo = get_the_title();
$enlace = get_permalink();
$descripcion = get_post_meta($cliente_id, 'mg_cliente_descripcion', true);
$logo = get_the_post_thumbnail_url($cliente_id, 'medium');
$num_empleados = get_post_meta($cliente_id, 'mg_cliente_num_empleados', true);
$linkedin = get_post_meta($cliente_id, 'mg_cliente_linkedin', true);

// Obtener industrias
$industrias = get_the_terms($cliente_id, 'mg_industria');

// Contar casos y portafolios
$casos_count = 0;
$portafolios_count = 0;

$lang = function_exists('pll_get_post_language') ? pll_get_post_language($cliente_id) : false;

// Contar casos de éxito
$args_casos = [
    'post_type' => 'mg_caso_exito',
    'posts_per_page' => -1,
    'meta_query' => [
        [
            'key' => 'mg_caso_cliente',
            'value' => $cliente_id,
            'compare' => '='
        ]
    ]
];
if ($lang) $args_casos['lang'] = $lang;
$casos = new WP_Query($args_casos);
$casos_count = $casos->found_posts;

// Contar portafolios
$args_portafolio = [
    'post_type' => 'mg_portafolio',
    'posts_per_page' => -1,
    'meta_query' => [
        [
            'key' => 'mg_portafolio_cliente',
            'value' => $cliente_id,
            'compare' => '='
        ]
    ]
];
if ($lang) $args_portafolio['lang'] = $lang;
$portafolios = new WP_Query($args_portafolio);
$portafolios_count = $portafolios->found_posts;
?>

<article class="cliente-mg card-mg position-relative" itemscope itemtype="https://schema.org/Organization">

  <!-- Card entero clickeable -->
  <a href="<?= esc_url($enlace); ?>" class="stretched-link"></a>
  <div class="cliente-logo-container text-center position-relative">
    <img src="<?= esc_url($logo); ?>"
         alt="<?= esc_attr("Logo de $titulo"); ?>"
         class="cliente-logo"
         loading="lazy"
         decoding="async"
         itemprop="logo">
  </div>

  <div class="cliente-info text-center position-relative">
    <h3 class="cliente-nombre" itemprop="name"><?= esc_html($titulo); ?></h3>

    <?php if (!empty($industrias) && !is_wp_error($industrias)): ?>
      <div class="cliente-industrias mb-2 position-relative z-3">
        <?php foreach ($industrias as $term):
          $term_link = get_term_link($term);
          if (is_wp_error($term_link)) continue;
        ?>
          <a class="service-tag me-1 position-relative z-3"
             href="<?= esc_url($term_link); ?>">
            <?= esc_html($term->name); ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($linkedin): ?>
    <div class="cliente-social text-center position-absolute top-0 end-0 m-2 z-3">

      <a
        href="<?php echo esc_url($linkedin); ?>"
       class="service-tag mt-1  px-1  btn-linkedin "              
        target="_blank"
        rel="noopener noreferrer"
        itemprop="sameAs"
        aria-label="<?php echo esc_attr(sprintf(__('Ver perfil de %s en LinkedIn', 'maggiore'), $titulo)); ?>"
      >
   
                           <i>
<svg class="m-1" id="Modo_de_aislamiento" width="11" height="14"  fill="white" data-name="Modo de aislamiento" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.18 13.71">
  <path d="M2.54,10.71V3.48H.14v7.22h2.4ZM1.34,2.5c.84,0,1.36-.55,1.36-1.25-.01-.71-.52-1.25-1.34-1.25S0,.54,0,1.25s.52,1.25,1.33,1.25h.02ZM6.25,10.71v-4.03c0-.22.02-.43.08-.59.17-.43.57-.88,1.23-.88.87,0,1.22.66,1.22,1.63v3.86h2.4v-4.14c0-2.22-1.18-3.25-2.76-3.25-1.27,0-1.85.7-2.16,1.19v.03h-.02l.02-.03v-1.02h-2.4c.03.68,0,7.22,0,7.22h2.4Z"/>
</svg></i>
      </a>

     
    </div>
  <?php endif; ?>

</article>
