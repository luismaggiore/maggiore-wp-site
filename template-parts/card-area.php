<?php
if (!defined('ABSPATH')) exit;
global $post;

$area_id     = get_the_ID();
$titulo      = get_the_title();
$enlace      = get_permalink();
$descripcion = get_post_meta($area_id, 'mg_area_descripcion', true);
$director_id = get_post_meta($area_id, 'mg_area_director', true);

/**
 * Director
 */
$director_nombre = '';
$director_foto   = '';
$director_cargo  = '';

if ($director_id) {
  $director_nombre = get_the_title($director_id);
  $director_foto   = get_the_post_thumbnail_url($director_id, 'thumbnail');
  $director_cargo  = get_post_meta($director_id, 'mg_equipo_cargo', true);
}

/**
 * Idioma (Polylang)
 */
$lang = function_exists('pll_get_post_language') ? pll_get_post_language($area_id) : false;

/**
 * Contar servicios del área
 */
$args_servicios = [
  'post_type'      => 'mg_servicio',
  'posts_per_page' => -1,
  'meta_query'     => [
    [
      'key'     => 'mg_servicio_area',
      'value'   => $area_id,
      'compare' => '='
    ]
  ]
];

if ($lang) {
  $args_servicios['lang'] = $lang;
}

$servicios       = new WP_Query($args_servicios);
$servicios_count = $servicios->found_posts;

/**
 * Contar miembros del área
 */
$miembros_ids   = get_post_meta($area_id, 'mg_area_miembros', true) ?: [];
$miembros_count = is_array($miembros_ids) ? count($miembros_ids) : 0;

/**
 * Paleta de colores para áreas
 */
$area_color = get_post_meta($area_id, 'mg_area_color', true);

// Fallback si no tiene color asignado
if (!$area_color) {
  $area_color = '#000000';
}
?>

<article
  class="card-mg"
  itemscope
  itemtype="https://schema.org/Organization"
  style="
    height:100%;
    border-left: 4px solid <?= esc_attr($area_color); ?>;
    display:flex;
    flex-direction:column;
  "
>

  <!-- Header -->
  <div class="area-header">
    <h3 class="area-nombre" itemprop="name">
      <?= esc_html($titulo); ?>
    </h3>
  </div>

  <!-- Descripción -->
  <?php if ($descripcion): ?>
    <p class="area-descripcion" itemprop="description">
      <?= esc_html(wp_trim_words($descripcion, 20, '...')); ?>
    </p>
  <?php endif; ?>

  <!-- Métricas -->
  <div class="area-metrics d-flex gap-2 mb-4">
    <?php if ($servicios_count > 0): ?>
      <div class="metric card-mg" style="flex: 1;">
        <?= (int) $servicios_count; ?>
        <?= $servicios_count === 1 ? __('servicio', 'maggiore') : __('servicios', 'maggiore'); ?>
      </div>
    <?php endif; ?>

    <?php if ($miembros_count > 0): ?>
      <div class="metric card-mg" style="flex: 1;">
        <?= (int) $miembros_count; ?>
        <?= $miembros_count === 1 ? __('miembro', 'maggiore') : __('miembros', 'maggiore'); ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Director (alineado abajo) -->
  <?php if ($director_id): ?>
    <div
      class="area-director"
      style="margin-top:auto; "
      itemprop="member"
      itemscope
      itemtype="https://schema.org/Person"
    >
      <span class="label"><?php _e('Director', 'maggiore'); ?></span>

      <a href="<?= esc_url(get_permalink($director_id)); ?>"
         class="person-card z-2 position-relative mt-2"
         aria-label="<?= esc_attr(sprintf(__('Ver perfil de %s', 'maggiore'), $director_nombre)); ?>"
         rel="noopener noreferrer"
         itemprop="url">

        <?php if ($director_foto): ?>
          <img src="<?= esc_url($director_foto); ?>"
               alt="<?= esc_attr("Foto de $director_nombre"); ?>"
               width="35"
               height="35"
               loading="lazy"
               decoding="async"
               itemprop="image"
               class="rounded-circle me-1">
        <?php endif; ?>

        <span class="px-2"> 
          <span itemprop="name" style="display:block; line-height:18px;">
            <?= esc_html($director_nombre); ?>
          </span>

          <?php if ($director_cargo): ?>
            <small itemprop="jobTitle" >
              <?= esc_html($director_cargo); ?>
            </small>
          <?php endif; ?>
        </span>

      </a>
    </div>
  <?php endif; ?>

  <!-- Link clickeable completo -->
  <a href="<?= esc_url($enlace); ?>"
     class="stretched-link"
     aria-label="<?= esc_url($enlace); ?>">
  </a>

</article>
