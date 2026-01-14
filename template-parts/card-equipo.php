<?php
if (!defined('ABSPATH')) exit;

$miembro_id = get_the_ID();

// Datos del miembro
$nombre   = get_the_title($miembro_id);
$permalink = get_permalink($miembro_id);

$cargo     = get_post_meta($miembro_id, 'mg_equipo_cargo', true);
$area_id   = get_post_meta($miembro_id, 'mg_equipo_area', true);
$sub_area  = get_post_meta($miembro_id, 'mg_equipo_subarea', true);
$linkedin  = get_post_meta($miembro_id, 'mg_equipo_linkedin', true);
$foto      = get_the_post_thumbnail_url($miembro_id, 'medium');

// Equipo (taxonomía)
$equipos = get_the_terms($miembro_id, 'mg_equipos');
$equipo  = (!empty($equipos) && !is_wp_error($equipos)) ? $equipos[0] : null;
// Variables para las clases CSS
$slug = $equipo ? $equipo->slug : '';
$badge_class = $equipo ? 'btn-' . $slug : '';

$is_area_single = is_singular('mg_area');
?>

<article
  class="team-member-card <?php echo $is_area_single ? 'is-in-area' : ''; ?>"
  itemscope
  style="gap:0"
  itemtype="https://schema.org/Person"
>

    <div class="member-team mx-1">

 

  <?php if (!$is_area_single && $equipo): ?>
      <a  href="<?php echo esc_url(get_term_link($equipo)); ?>" class="team-tag service-tag mx-0 <?= $badge_class ?>">
        <?php echo esc_html($equipo->name); ?>
      </a>

  <?php endif; ?>

       <?php if ($linkedin): ?>
      <a
        href="<?php echo esc_url($linkedin); ?>"
       class="service-tag mt-1 mx-0 px-1  btn-linkedin "              
        target="_blank"
        rel="noopener noreferrer"
        itemprop="sameAs"
        aria-label="<?php echo esc_attr(sprintf(__('Ver perfil de %s en LinkedIn', 'maggiore'), $nombre)); ?>"
      >
   
                           <i>
<svg class="m-1" id="Modo_de_aislamiento" width="11" height="14"  fill="white" data-name="Modo de aislamiento" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.18 13.71">
  <path d="M2.54,10.71V3.48H.14v7.22h2.4ZM1.34,2.5c.84,0,1.36-.55,1.36-1.25-.01-.71-.52-1.25-1.34-1.25S0,.54,0,1.25s.52,1.25,1.33,1.25h.02ZM6.25,10.71v-4.03c0-.22.02-.43.08-.59.17-.43.57-.88,1.23-.88.87,0,1.22.66,1.22,1.63v3.86h2.4v-4.14c0-2.22-1.18-3.25-2.76-3.25-1.27,0-1.85.7-2.16,1.19v.03h-.02l.02-.03v-1.02h-2.4c.03.68,0,7.22,0,7.22h2.4Z"/>
</svg></i>
      </a>
  <?php endif; ?>

      
    </div>



  <a
    href="<?php echo esc_url($permalink); ?>"
    class="member-photo-link"
    aria-label="<?php echo esc_attr($nombre); ?>"
    itemprop="url"
  >
    <div class="member-photo-wrap">
      <?php if ($foto): ?>
        <img
          src="<?php echo esc_url($foto); ?>"
          alt="<?php echo esc_attr('Foto de ' . $nombre); ?>"
          class="member-photo"
          loading="lazy"
          decoding="async"
          itemprop="image"
        >
      <?php else: ?>
        <div class="member-photo-placeholder" aria-hidden="true">
          <svg width="80" height="80" fill="#6c757d" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
          </svg>
        </div>
      <?php endif; ?>
    </div>

    <div class="member-info">
      <h3 class="member-name" itemprop="name">
        <?php echo esc_html($nombre); ?>
      </h3>

      <?php if ($cargo): ?>
        <p class="member-position" itemprop="jobTitle">
          <?php echo esc_html($cargo); ?>
        </p>
      <?php endif; ?>
    </div>
  </a>
</article>
