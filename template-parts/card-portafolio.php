<?php
if (!defined('ABSPATH')) exit;
global $post;

$post_id = get_the_ID();

$cliente_id     = get_post_meta($post_id, 'mg_portafolio_cliente', true);
$cliente_logo   = $cliente_id ? get_the_post_thumbnail_url($cliente_id, 'thumbnail') : '';
$cliente_nombre = $cliente_id ? get_the_title($cliente_id) : '';

$servicios_ids  = get_post_meta($post_id, 'mg_portafolio_servicio', true);
$servicios_ids  = is_array($servicios_ids) ? $servicios_ids : [];

$bg_image       = get_the_post_thumbnail_url($post_id, 'large');
$permalink      = get_permalink($post_id);
$titulo         = get_the_title($post_id);

$show_cliente_tag = (!is_singular('mg_cliente') && !is_singular('mg_caso_exito'));
?>

<article class="portafolio-mg" itemscope itemtype="https://schema.org/CreativeWork">
  <?php if ($bg_image): ?>
    <div class="portafolio-bg " style="background-image: url('<?php echo esc_url($bg_image); ?>');"></div>
  <?php else: ?>
    <div class="portafolio-bg is-empty"></div>
  <?php endif; ?>

  <!-- Stretched link (cubre todo el article) -->
  <a
    href="<?php echo esc_url($permalink); ?>"
    class="stretched-link"
    aria-label="<?php echo esc_attr($titulo); ?>"
  >
    <span class="overlay-p" aria-hidden="true"></span>
  </a>

  <?php if ($show_cliente_tag && $cliente_id): ?>
    <div class="client-tag z-2" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
      <a
        href="<?php echo esc_url(get_permalink($cliente_id)); ?>"
        itemprop="url"
        aria-label="<?php echo esc_attr(sprintf(__('Ver página de %s', 'maggiore'), $cliente_nombre)); ?>"
      >
        <?php if ($cliente_logo): ?>
          <img
            src="<?php echo esc_url($cliente_logo); ?>"
            alt="<?php echo esc_attr("Logo de $cliente_nombre"); ?>"
            width="20"
            height="20"
            loading="lazy"
            decoding="async"
            itemprop="logo"
          >
        <?php endif; ?>
        <span itemprop="name"><?php echo esc_html($cliente_nombre); ?></span>
      </a>
    </div>
  <?php endif; ?>

  <h3 class="case-title " itemprop="headline">
    <?php echo esc_html($titulo); ?>
  </h3>

  <?php if (!empty($servicios_ids)): ?>
    <ul class="services-tags ">
      <?php foreach ($servicios_ids as $sid): ?>
        <?php
          $sid = (int) $sid;
          if (!$sid) continue;
        ?>
        <li>
          <a class="service-tag z-2" href="<?php echo esc_url(get_permalink($sid)); ?>">
            <?php echo esc_html(get_the_title($sid)); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</article>
