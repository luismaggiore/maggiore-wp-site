<?php
/**
 * Template Name: Quiénes Somos
 */

get_header();
?>

<main>
  <!-- Hero Section -->
  <section class="hero-quienes-somos">
    <div class="container">
      <h1><?php _e('Quiénes Somos', 'maggiore'); ?></h1>
      <p class="lead">Misión y visión</p>
    </div>
  </section>

  <!-- Historia -->
  <section class="historia py-5">
    <div class="container">
      <!-- Contenido editable desde el editor -->
      <?php the_content(); ?>
    </div>
  </section>

  <!-- Valores -->
  <section class="valores py-5">
    <!-- ACF o metaboxes para valores -->
  </section>

  <!-- Equipo por Áreas -->
  <section class="equipo-por-areas py-5">
    <div class="container">
      <?php
      // Query de áreas
      $areas = new WP_Query([
        'post_type' => 'mg_area',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC'
      ]);

      if ($areas->have_posts()):
        while ($areas->have_posts()): $areas->the_post();
          // Mostrar área
          ?>
          <div class="area-section mb-5">
            <h2><?php the_title(); ?></h2>
            
            <?php
            // Query de equipo en esta área
            $miembros = get_post_meta(get_the_ID(), '_mg_miembros_area', true);
            
            if ($miembros):
              echo '<div class="row">';
              foreach ($miembros as $miembro_id):
                // Usar card-equipo.php
                set_query_var('miembro_id', $miembro_id);
                get_template_part('template-parts/cards/card', 'equipo');
              endforeach;
              echo '</div>';
            endif;
            ?>
          </div>
          <?php
        endwhile;
        wp_reset_postdata();
      endif;
      ?>
    </div>
  </section>

  <!-- Certificaciones/Premios -->
  <section class="certificaciones py-5">
    <!-- Galería de logos -->
  </section>

  <!-- CTA Final -->
  <section class="cta-contacto py-5 bg-dark text-white">
    <div class="container text-center">
      <h2><?php _e('¿Listo para trabajar con nosotros?', 'maggiore'); ?></h2>
      <a href="<?php echo get_permalink(get_page_by_path('contacto')); ?>" class="btn btn-primary">
        <?php _e('Contáctanos', 'maggiore'); ?>
      </a>
    </div>
  </section>
</main>

<?php get_footer(); ?>