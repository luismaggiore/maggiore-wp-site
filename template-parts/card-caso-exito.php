<?php
if (!defined('ABSPATH')) exit;
global $post;

$post_id = $post->ID;

$cliente_id           = (int) get_post_meta($post_id, 'mg_caso_cliente', true);
$servicios_ids        = get_post_meta($post_id, 'mg_caso_servicios', true) ?: [];

$contratador_nombre   = get_post_meta($post_id, 'mg_caso_contratador_nombre', true);
$contratador_cargo    = get_post_meta($post_id, 'mg_caso_contratador_cargo', true);
$contratador_img_id   = get_post_meta($post_id, 'mg_caso_contratador_img', true);
$contratador_img      = $contratador_img_id ? wp_get_attachment_image_url($contratador_img_id, 'thumbnail') : '';
$contratador_linkedin = get_post_meta($post_id, 'mg_caso_contratador_linkedin', true);

$encabezado           = get_post_meta($post_id, 'mg_caso_landing_titulo', true);
$bajada               = get_post_meta($post_id, 'mg_caso_landing_bajada', true);

$cliente_logo         = $cliente_id ? get_the_post_thumbnail_url($cliente_id, 'thumbnail') : '';
$cliente_nombre       = $cliente_id ? get_the_title($cliente_id) : '';

$case_id_attr         = 'case-' . $post->post_name;
$permalink            = get_permalink($post_id);

$show_author          = (!is_archive() && !empty($contratador_nombre));
$class_archive        = is_archive() ? 'archive' : '';

// =============================================================
// SCHEMA: imagen para itemprop="image" (requerido por Article)
// Prioridad: thumbnail del caso → logo del cliente → OG por defecto
// =============================================================
$article_image = get_the_post_thumbnail_url($post_id, 'large');
if (!$article_image) {
    $article_image = $cliente_logo;
}
if (!$article_image) {
    $default_og    = get_option('mg_default_og_image', '');
    $article_image = $default_og ?: get_template_directory_uri() . '/assets/img/default-og.webp';
}

// =============================================================
// SCHEMA: autor fallback como Person (requerido por Article)
// Solo se necesita cuando no hay contratador visible ($show_author = false).
// Busca el primer miembro del equipo asociado al caso, por menu_order.
// Prioridad: mg_caso_equipo (manual) → _mg_equipo_auto (automático).
// =============================================================
$fallback_autor = null;

if (!$show_author) {
    $equipo_ids = (array) get_post_meta($post_id, 'mg_caso_equipo', true);
    $equipo_ids = array_filter(array_map('intval', $equipo_ids));

    // Si no hay equipo manual, usar el automático
    if (empty($equipo_ids)) {
        $equipo_ids = (array) get_post_meta($post_id, '_mg_equipo_auto', true);
        $equipo_ids = array_filter(array_map('intval', $equipo_ids));
    }

    if (!empty($equipo_ids)) {
        $primer_miembro = get_posts([
            'post_type'      => 'mg_equipo',
            'post__in'       => $equipo_ids,
            'posts_per_page' => 1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'post_status'    => 'publish',
        ]);

        if (!empty($primer_miembro)) {
            $m = $primer_miembro[0];
            $fallback_autor = [
                'name'     => $m->post_title,
                'cargo'    => get_post_meta($m->ID, 'mg_equipo_cargo', true),
                'linkedin' => get_post_meta($m->ID, 'mg_equipo_linkedin', true),
                'url'      => get_permalink($m->ID),
                'image'    => get_the_post_thumbnail_url($m->ID, 'thumbnail'),
            ];
        }
    }
}
?>

<article
  class="testimonial-mg card-mg position-relative mb-0 <?= esc_html($class_archive); ?>"
  aria-labelledby="<?= esc_attr($case_id_attr); ?>-title"
  itemscope
  itemtype="https://schema.org/Article"
>

  <?php /*
    Meta tags requeridos por schema.org/Article sin representación visual.
  */ ?>
  <meta itemprop="image"         content="<?= esc_url($article_image); ?>">
  <meta itemprop="datePublished" content="<?= esc_attr(get_the_date('c', $post_id)); ?>">
  <meta itemprop="url"           content="<?= esc_url($permalink); ?>">

  <?php if ($cliente_id): ?>
    <div class="client-tag z-2 position-relative"
         itemprop="publisher"
         itemscope
         itemtype="https://schema.org/Organization">
      <a class="z-2 position-relative"
         href="<?= esc_url(get_permalink($cliente_id)); ?>"
         aria-label="<?= esc_attr(sprintf(__('Ver página de %s', 'maggiore'), $cliente_nombre)); ?>"
         itemprop="url">

        <?php if ($cliente_logo): ?>
          <img src="<?= esc_url($cliente_logo); ?>"
               alt="<?= esc_attr("Logo de $cliente_nombre"); ?>"
               width="20" height="20"
               loading="lazy" decoding="async"
               itemprop="logo">
        <?php endif; ?>

        <span itemprop="name"><?= esc_html($cliente_nombre); ?></span>
      </a>
    </div>
  <?php endif; ?>

  <header>
    <h3 class="title-feature" itemprop="headline" id="<?= esc_attr($case_id_attr); ?>-title">
      <a class="case-link z-2 position-relative" href="<?= esc_url($permalink); ?>">
        <?= esc_html($encabezado ?: get_the_title($post_id)); ?>
      </a>
    </h3>

    <?php if ($bajada): ?>
      <blockquote class="case-quote">
        <p itemprop="description"><?= esc_html($bajada); ?></p>
      </blockquote>
    <?php endif; ?>
  </header>

  <?php if ($show_author): ?>
    <div class="row mt-5 gx-3 gy-0 justify-content-between">
      <div class="col-lg-8">
  <?php endif; ?>

        <section class="case-services" aria-labelledby="<?= esc_attr($case_id_attr); ?>-services">
          <h4 class="label" id="<?= esc_attr($case_id_attr); ?>-services">
            <?= esc_html__('Servicios involucrados', 'maggiore'); ?>
          </h4>

          <?php if (!empty($servicios_ids)): ?>
            <ul class="services-tags">
              <?php foreach ($servicios_ids as $sid): $sid = (int) $sid; ?>
                <li>
                  <a class="service-tag z-2 position-relative" href="<?= esc_url(get_permalink($sid)); ?>">
                    <?= esc_html(get_the_title($sid)); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>

  <?php if ($show_author): ?>
      </div>

      <div class="col-lg-4"
           itemprop="author"
           itemscope
           itemtype="https://schema.org/Person">

        <dl>
          <dt class="label mb-2"><?= esc_html__('Contratador', 'maggiore'); ?></dt>
          <dd>
            <a href="<?= esc_url($contratador_linkedin ?: '#'); ?>"
               class="person-card z-2 position-relative"
               aria-label="<?= esc_attr(sprintf(__('Ver perfil de %s en LinkedIn', 'maggiore'), $contratador_nombre)); ?>"
               rel="noopener noreferrer"
               target="_blank"
               itemprop="sameAs">

              <?php if ($contratador_img): ?>
                <img src="<?= esc_url($contratador_img); ?>"
                     alt="<?= esc_attr("Foto de $contratador_nombre"); ?>"
                     width="35" height="35"
                     loading="lazy" decoding="async"
                     itemprop="image"
                     class="rounded-circle me-1">
              <?php endif; ?>

              <span>
                <span itemprop="name" style="display:block; line-height:18px;">
                  <?= esc_html($contratador_nombre); ?>
                </span>

                <?php if ($contratador_cargo): ?>
                  <small style="display:block; line-height:18px;">
                    <?= esc_html($contratador_cargo); ?>
                  </small>
                <?php endif; ?>
              </span>
            </a>
          </dd>
        </dl>

      </div>
    </div>

  <?php elseif ($fallback_autor): ?>

    <?php /*
      Autor fallback: primer miembro del equipo asociado al caso (por menu_order).
      Invisible en la UI pero satisface el requisito Person de schema.org/Article.
    */ ?>
    <div itemprop="author" itemscope itemtype="https://schema.org/Person" hidden>
      <meta itemprop="name"    content="<?= esc_attr($fallback_autor['name']); ?>">
      <meta itemprop="url"     content="<?= esc_url($fallback_autor['url']); ?>">
      <?php if ($fallback_autor['cargo']): ?>
        <meta itemprop="jobTitle" content="<?= esc_attr($fallback_autor['cargo']); ?>">
      <?php endif; ?>
      <?php if ($fallback_autor['linkedin']): ?>
        <meta itemprop="sameAs"   content="<?= esc_url($fallback_autor['linkedin']); ?>">
      <?php endif; ?>
      <?php if ($fallback_autor['image']): ?>
        <meta itemprop="image"    content="<?= esc_url($fallback_autor['image']); ?>">
      <?php endif; ?>
    </div>

  <?php endif; ?>

  <a href="<?= esc_url($permalink); ?>"
     class="stretched-link"
     aria-label="<?= esc_attr(get_the_title($post_id)); ?>"></a>

</article>
