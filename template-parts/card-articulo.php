<?php
if (!defined('ABSPATH')) exit;
global $post;

/**
 * Autor (miembro del equipo)
 */
$autor_id     = get_post_meta($post->ID, 'mg_blog_autor', true);
$autor_nombre = $autor_id ? get_the_title($autor_id) : '';
$autor_cargo  = $autor_id ? get_post_meta($autor_id, 'mg_equipo_cargo', true) : '';
$autor_foto   = $autor_id ? get_the_post_thumbnail_url($autor_id, 'thumbnail') : '';
$autor_url    = $autor_id ? get_permalink($autor_id) : '';

/**
 * Extracto
 */
$extracto         = get_the_excerpt();
$palabras_extracto = 15; // Ajusta la longitud del extracto

/**
 * Tags del post
 */
$tags     = get_the_tags($post->ID);
$max_tags = 3; // Cantidad máxima de tags a mostrar

/**
 * Fecha de publicación
 */
$fecha         = get_the_date('c'); // ISO 8601
$fecha_lectura = get_the_date(get_option('date_format'));
?>

<article class="blog-article" itemscope itemtype="https://schema.org/Article">

  <!-- Imagen destacada + Contenido -->
  <div style="align-self:start;">

    <?php if (has_post_thumbnail()): ?>
      <?= get_the_post_thumbnail($post->ID, 'medium', [
        'itemprop' => 'image',
        'class'    => 'img-fluid img-blog'
      ]) ?>
    <?php endif; ?>

    <div class="blog-info">

      <!-- Tags -->
      <?php if ($tags && !is_wp_error($tags)): ?>
        <ul class="services-tags">
          <?php foreach (array_slice($tags, 0, $max_tags) as $tag): ?>
            <li>
              <a class="service-tag position-relative z-2" href="<?= get_tag_link($tag->term_id); ?>">
                <?= esc_html($tag->name); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <!-- Título -->
      <header>
        <h3 itemprop="headline" class="position-relative z-2">
          <a href="<?= get_permalink(); ?>" rel="bookmark">
            <?= esc_html(get_the_title()); ?>
          </a>
        </h3>

        <meta itemprop="datePublished" content="<?= esc_attr($fecha); ?>">
        <meta itemprop="dateModified" content="<?= esc_attr(get_the_modified_date('c')); ?>">
      </header>

      <!-- Extracto -->
      <div class="blog-extracto">
        <?php if ($extracto): ?>
          <p itemprop="description">
            <?= esc_html(wp_trim_words($extracto, $palabras_extracto, '...')); ?>
          </p>
        <?php endif; ?>
      </div>

    </div><!-- /.blog-info -->

  </div><!-- /wrapper --> 

  <!-- Link total clickable -->
  <a href="<?= get_permalink(); ?>"
     class="stretched-link"
     aria-label="<?= esc_url(get_permalink()); ?>">
  </a>

  <!-- Autor -->
  <?php if ($autor_id): ?>
    <div class="blog-author position-relative">
      <div class="client-tag  z-2" itemprop="author" itemscope itemtype="https://schema.org/Person">
        <a href="<?= esc_url($autor_url); ?>"
           aria-label="<?= esc_attr(sprintf(__('Ver perfil de %s', 'maggiore'), $autor_nombre)); ?>"
           itemprop="url">

          <?php if ($autor_foto): ?>
            <img src="<?= esc_url($autor_foto); ?>"
                 alt="<?= esc_attr("Foto de $autor_nombre"); ?>"
                 width="20" height="20"
                 loading="lazy"
                 decoding="async"
                 itemprop="image">
          <?php endif; ?>

          <span itemprop="name"><?= esc_html($autor_nombre); ?></span>
        </a>
      </div>
    </div>
  <?php endif; ?>

</article>
