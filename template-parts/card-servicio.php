<?php
/**
 * Card de Servicio - Actualizado con sistema de colores automático
 * 
 * Usa clases CSS generadas automáticamente desde taxonomías
 * Fallback al sistema anterior de colores si no hay color personalizado
 */

if (!defined('ABSPATH')) exit;

global $post;

$servicio_id = get_the_ID();
$titulo      = get_the_title();
$enlace      = get_permalink();
$bajada      = get_post_meta($servicio_id, 'mg_servicio_bajada', true);

// Obtener categoría del servicio (taxonomía mg_categoria)
$categorias = get_the_terms($servicio_id, 'mg_categoria');
$categoria  = (!empty($categorias) && !is_wp_error($categorias)) ? $categorias[0] : null;

// Variables para las clases CSS
$slug = $categoria ? $categoria->slug : '';
$badge_class = $categoria ? 'btn-' . $slug : '';

?>

<article class="card-mg pt-5" style="height:100%" itemscope itemtype="https://schema.org/Service">

  <?php if ($categoria): ?>
    <div class="category-tag <?= $badge_class ?>">
        <a href="<?= esc_url(get_term_link($categoria)) ?>">
            <?= esc_html($categoria->name) ?>
        </a>
    </div>
  <?php endif; ?>

    <h3 class="mt-2" itemprop="name"><?= esc_html($titulo); ?></h3>

    <?php if ($bajada): ?>
      <p style="line-height:21px" itemprop="description"><?= esc_html($bajada); ?></p>
    <?php endif; ?>
    
  <a href="<?= esc_url($enlace); ?>"
     class="stretched-link"
     aria-label="<?= esc_attr($titulo); ?>"></a>
</article>
