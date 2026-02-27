<?php
/**
 * Template Name: Home
 * Description: Página institucional con presentación, áreas y valores
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'pll_current_language' ) ) {
    $current_lang = pll_current_language();
    if ( $current_lang === 'en' ) {
        switch_to_locale( 'en_US' );
    } elseif ( $current_lang === 'pt' ) {
        switch_to_locale( 'pt_BR' );
    }
}

get_header();

$pid = get_the_ID();
?>

<div id="smooth-wrapper">
  <div id="smooth-content">
    <main>

      <!-- ================================================================
           HERO
      ================================================================ -->
      <section id="sectionOne" class="hero">
        <div class="container-fluid">
          <div class="row justify-content-between align-items-center">

            <div class="col-xl-4 col-lg-6 order-1 order-lg-2">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/img/globo.webp"
                   class="globo"
                   alt="Maggiore's Hot Air Balloon">
            </div>

            <div class="col-xl-6 col-lg-6 move mb-4 order-2 order-lg-1">
              <div class="hero-content">
                <img class="brand-name d-lg-inline-block d-none"
                     src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-mm.svg"
                     style="width: 180px; margin: 60px auto"
                     alt="Maggiore">
              </div>

              <h1 class="title-reveal">
                <?php _e( 'Pensamos en grande para que tu marca llegue a lo más alto', 'maggiore' ); ?>
              </h1>

              <div class="bajada-reveal">
                <p class="bajada"><?php echo esc_html( mg_home_get( $pid, 'mg_home_hero_bajada' ) ); ?></p>

                <div class="mg-link">
                  <?php $cta_texto = mg_home_get( $pid, 'mg_home_hero_cta_texto' ); ?>
                  <a class="btns-mgr" href="#sectionFour">
                    <div class="btn-brillo"></div>
                    <div class="btn-container">
                      <div class="btn-content">
                        <span class="btn-text"><?php echo esc_html( $cta_texto ); ?></span>
                        <span class="btn-text-2"><?php echo esc_html( $cta_texto ); ?></span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      <!-- ================================================================
           FEATURES
      ================================================================ -->
      <section id="features" class="container-fluid" aria-labelledby="features-title">
        <h2 id="features-title" class="visually-hidden"><?php _e( 'Diferenciales y metodología de trabajo', 'maggiore' ); ?></h2>

        <div class="row justify-content-end justify-content-lg-between align-items-start position-relative">

          <div class="col-lg-6 order-2 order-lg-1">

            <article class="separador" id="robinHood">
              <div class="feature-name">
                <h3><?php echo esc_html( mg_home_get( $pid, 'mg_home_feature1_nombre' ) ); ?></h3>
              </div>
              <h4 class="mb-4 title-feature"><?php echo esc_html( mg_home_get( $pid, 'mg_home_feature1_titulo' ) ); ?></h4>
              <p><?php echo esc_html( mg_home_get( $pid, 'mg_home_feature1_parrafo' ) ); ?></p>
              <?php if ( mg_home_get( $pid, 'mg_home_feature1_btn_mostrar' ) === '1' ) : ?>
              <div class="mg-link">
                <a class="btns-mgr" href="<?php echo esc_url( mg_home_get( $pid, 'mg_home_feature1_btn_link' ) ); ?>">
                  <div class="btn-brillo"></div>
                  <div class="btn-container">
                    <div class="btn-content">
                      <?php $btn1 = esc_html( mg_home_get( $pid, 'mg_home_feature1_btn_texto' ) ); ?>
                      <span class="btn-text"><?php echo $btn1; ?></span>
                      <span class="btn-text-2"><?php echo $btn1; ?></span>
                    </div>
                  </div>
                </a>
              </div>
              <?php endif; ?>
              <hr class="hr-mg">
            </article>

            <article class="separador" id="inteligencia">
              <div class="feature-name">
                <h3><?php echo esc_html( mg_home_get( $pid, 'mg_home_feature2_nombre' ) ); ?></h3>
              </div>
              <h4 class="mb-4 title-feature"><?php echo esc_html( mg_home_get( $pid, 'mg_home_feature2_titulo' ) ); ?></h4>
              <p><?php echo esc_html( mg_home_get( $pid, 'mg_home_feature2_parrafo' ) ); ?></p>
              <?php if ( mg_home_get( $pid, 'mg_home_feature2_btn_mostrar' ) === '1' ) : ?>
              <div class="mg-link">
                <a class="btns-mgr" href="<?php echo esc_url( mg_home_get( $pid, 'mg_home_feature2_btn_link' ) ); ?>">
                  <div class="btn-brillo"></div>
                  <div class="btn-container">
                    <div class="btn-content">
                      <?php $btn2 = esc_html( mg_home_get( $pid, 'mg_home_feature2_btn_texto' ) ); ?>
                      <span class="btn-text"><?php echo $btn2; ?></span>
                      <span class="btn-text-2"><?php echo $btn2; ?></span>
                    </div>
                  </div>
                </a>
              </div>
              <?php endif; ?>
              <hr class="hr-mg">
            </article>

            <article class="separador" id="flexible">
              <div class="feature-name">
                <h3><?php echo esc_html( mg_home_get( $pid, 'mg_home_feature3_nombre' ) ); ?></h3>
              </div>
              <h4 class="mb-4 title-feature"><?php echo esc_html( mg_home_get( $pid, 'mg_home_feature3_titulo' ) ); ?></h4>
              <p><?php echo esc_html( mg_home_get( $pid, 'mg_home_feature3_parrafo' ) ); ?></p>
              <?php if ( mg_home_get( $pid, 'mg_home_feature3_btn_mostrar' ) === '1' ) : ?>
              <div class="mg-link">
                <a class="btns-mgr" href="<?php echo esc_url( mg_home_get( $pid, 'mg_home_feature3_btn_link' ) ); ?>">
                  <div class="btn-brillo"></div>
                  <div class="btn-container">
                    <div class="btn-content">
                      <?php $btn3 = esc_html( mg_home_get( $pid, 'mg_home_feature3_btn_texto' ) ); ?>
                      <span class="btn-text"><?php echo $btn3; ?></span>
                      <span class="btn-text-2"><?php echo $btn3; ?></span>
                    </div>
                  </div>
                </a>
              </div>
              <?php endif; ?>
              <hr class="hr-mg">
            </article>

          </div>

          <div class="col-lg-5 order-1 order-lg-2 d-none d-lg-block position-relative">
            <div class="constelacion" aria-hidden="true">
              <img class="img-constelacion arco"
                   src="<?php echo get_template_directory_uri(); ?>/assets/img/bow.webp"
                   alt="Bow and arrow">
              <img class="img-constelacion ajedrez"
                   src="<?php echo get_template_directory_uri(); ?>/assets/img/pawn.webp"
                   alt="A chess pawn">
              <img class="img-constelacion infinito"
                   src="<?php echo get_template_directory_uri(); ?>/assets/img/infinity.webp"
                   alt="Infinity symbol">
              <svg id="Layer_1" data-name="Layer 1"
                   xmlns="http://www.w3.org/2000/svg"
                   viewBox="-20 0 598.29 697.84">
                <defs>
                  <style>
                    .cls-1 { fill: #fff; }
                    .dot   { filter: url(#glow); }
                  </style>
                  <filter id="glow" x="-200%" y="-200%" width="400%" height="400%">
                    <feGaussianBlur stdDeviation="5.5" result="blur" />
                    <feColorMatrix in="blur" type="matrix"
                      values="1 0 0 0 0  1 0 0 0 0  1 0 0 0 0  0 0 0 1 0"
                      result="coloredBlur" />
                    <feMerge>
                      <feMergeNode in="coloredBlur" />
                      <feMergeNode in="SourceGraphic" />
                    </feMerge>
                  </filter>
                </defs>
                <circle class="cls-1 dot dot-1" cx="245.44" cy="84.87"  r="3" />
                <circle class="cls-1 dot dot-2" cx="340"    cy="169.99" r="2" />
                <circle class="cls-1 dot dot-3" cx="513.42" cy="317.7"  r="6" />
                <circle class="cls-1 dot dot-4" cx="406.94" cy="331.07" r="3" />
                <circle class="cls-1 dot dot-5" cx="75.87"  cy="370.57" r="3" />
                <circle class="cls-1 dot dot-6" cx="373.34" cy="497.11" r="2" />
                <circle class="cls-1 dot dot-7" cx="279.89" cy="602.97" r="3" />
              </svg>
            </div>
          </div>

        </div>
      </section>

      <!-- ================================================================
           FRASE PROMESA  (sin metaboxes por ahora)
      ================================================================ -->
      <section id="sectionTwo" class="sectionTwo separador text-center position-relative">
        <div class="container-fluid containerTwo">
          <h2 class="maggiore-frase text-appear mb-4">
            <b>Maggiore</b><br>
            <?php _e( 'Significa', 'maggiore' ); ?> <b><?php _e( 'Mayor', 'maggiore' ); ?></b>
          </h2>
          <h2 class="mid-title"><?php _e( 'Nuestra promesa es crecimiento para tu empresa', 'maggiore' ); ?></h2>
        </div>
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/globo.webp"
             class="globo-2"
             alt="Maggiore's hot air balloon">
      </section>

      <!-- ================================================================
           CASOS DE ÉXITO
      ================================================================ -->
      <section class="separador testimonials" id="sectionThree">
        <div class="container-fluid">
          <div class="row">

            <div class="col-md-5">
              <div class="testimonial-title">
                <div class="feature-name-2">
                  <h2><?php echo esc_html( mg_home_get( $pid, 'mg_home_casos_label' ) ); ?></h2>
                </div>
                <h2 class="mb-4"><?php echo esc_html( mg_home_get( $pid, 'mg_home_casos_titulo' ) ); ?></h2>
              </div>
            </div>

            <div class="col-md-7">

              <!-- Grid de logos de clientes -->
              <div class="logos-grid mb-4">
                <?php
               $clientes_args = [
                    'post_type'      => 'mg_cliente',
                    'posts_per_page' => -1,
                  'orderby' => 'menu_order',
                  'order'   => 'ASC',
                    'meta_query'     => mg_get_indexable_clients_meta_query(),
                ];


                if ( function_exists( 'pll_current_language' ) ) {
                    $clientes_args['lang'] = pll_current_language();
                }

                $clientes_query = new WP_Query( $clientes_args );

                if ( $clientes_query->have_posts() ) :
                    while ( $clientes_query->have_posts() ) : $clientes_query->the_post();
                        $cliente_id     = get_the_ID();
                        $cliente_nombre = get_the_title();
                        $cliente_logo   = get_the_post_thumbnail_url( $cliente_id, 'medium' );
                        $cliente_url    = get_permalink( $cliente_id );

                        if ( $cliente_logo ) :
                ?>
                    <a href="<?php echo esc_url( $cliente_url ); ?>"
                       class="logo-item"
                       title="<?php echo esc_attr( $cliente_nombre ); ?>">
                        <img src="<?php echo esc_url( $cliente_logo ); ?>"
                             alt="<?php echo esc_attr( 'Logo de ' . $cliente_nombre ); ?>"
                             loading="lazy"
                             decoding="async">
                    </a>
                <?php
                        endif;
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
              </div>

              <?php
                   $args = [
                    'post_type'      => 'mg_caso_exito',
                    'posts_per_page' => 3,
                    'meta_key'       => 'mg_caso_aparece_en_landing',
                    'meta_value'     => '1',
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                ];


              if ( function_exists( 'pll_current_language' ) ) {
                  $args['lang'] = pll_current_language();
              }

              $casos_landing = new WP_Query( $args );

              if ( $casos_landing->have_posts() ) :
                  while ( $casos_landing->have_posts() ) : $casos_landing->the_post(); ?>
                      <div class="mb-2">
                          <?php get_template_part( 'template-parts/card', 'caso-exito' ); ?>
                      </div>
                  <?php endwhile;
                  wp_reset_postdata();
              else : ?>
                  <p><?php _e( 'No hay casos de éxito destacados en este momento.', 'maggiore' ); ?></p>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </section>

      <!-- ================================================================
           FORMULARIO
      ================================================================ -->
      <section class="separador" id="sectionFour">
        <div class="container">
          <h2 class="text-center mb-5">
            <?php echo esc_html( mg_home_get( $pid, 'mg_home_form_titulo_linea1' ) ); ?><br>
            <?php echo esc_html( mg_home_get( $pid, 'mg_home_form_titulo_linea2' ) ); ?>
          </h2>

          <form id="contactForm" data-origen="Formulario Home">

            <!-- Campo Honeypot (anti-spam) -->
            <div style="position: absolute; left: -5000px;" aria-hidden="true">
              <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombre"><?php _e( 'Nombre', 'maggiore' ); ?></label>
                <input type="text" id="nombre" name="nombre" class="form-control"
                       placeholder="<?php _e( 'Tu nombre completo', 'maggiore' ); ?>"
                       required>
              </div>

              <div class="col-md-6 mb-3">
                <label for="cargo"><?php _e( 'Cargo', 'maggiore' ); ?></label>
                <input type="text" id="cargo" name="cargo" class="form-control"
                       placeholder="<?php _e( 'Ej: Gerente de Marketing', 'maggiore' ); ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label for="correo"><?php _e( 'Correo', 'maggiore' ); ?></label>
                <input type="email" id="correo" name="correo" class="form-control"
                       placeholder="nombre@empresa.com"
                       required>
              </div>

              <div class="col-md-6 mb-3">
                <label for="empresa"><?php _e( 'Empresa', 'maggiore' ); ?></label>
                <input type="text" id="empresa" name="empresa" class="form-control"
                       placeholder="<?php _e( 'Nombre de la empresa', 'maggiore' ); ?>">
              </div>

              <div class="col-12 mb-4">
                <label for="telefono"><?php _e( 'Teléfono Celular', 'maggiore' ); ?></label>
                <input type="tel" id="telefono" name="telefono" class="form-control"
                       placeholder="1234 5678">
              </div>
            </div>

            <!-- Acordeón con campos opcionales -->
            <div class="accordion mb-4" id="acordeonOpcional">
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingOpcional">
                  <button class="accordion-button collapsed"
                          type="button"
                          data-bs-toggle="collapse"
                          data-bs-target="#collapseOpcional"
                          aria-expanded="false"
                          aria-controls="collapseOpcional">
                    <?php _e( 'Detalles Opcionales', 'maggiore' ); ?>
                  </button>
                </h2>
                <div id="collapseOpcional"
                     class="accordion-collapse collapse"
                     aria-labelledby="headingOpcional"
                     data-bs-parent="#acordeonOpcional">
                  <div class="accordion-body">
                    <div class="mb-3">
                      <label class="text-white" for="dolorEmpresa"><?php _e( 'Dolor de la empresa', 'maggiore' ); ?></label>
                      <textarea id="dolorEmpresa" name="dolorEmpresa" class="form-control" rows="3"
                                placeholder="<?php _e( '¿Cuál es el principal problema o dolor hoy?', 'maggiore' ); ?>"></textarea>
                    </div>
                    <div class="mb-3">
                      <label for="objetivos" class="text-white"><?php _e( 'Objetivos', 'maggiore' ); ?></label>
                      <textarea id="objetivos" name="objetivos" class="form-control" rows="3"
                                placeholder="<?php _e( '¿Qué objetivos buscan lograr?', 'maggiore' ); ?>"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btns-mgr">
                <div class="btn-brillo"></div>
                <div class="btn-container">
                  <div class="btn-content">
                    <span class="btn-text"><?php _e( 'Enviar', 'maggiore' ); ?></span>
                  </div>
                </div>
              </button>
            </div>

          </form>
        </div>
      </section>

    </main>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    gsap.registerPlugin(ScrollToPlugin);

    const ctaButton = document.querySelector('.btns-mgr[href="#sectionFour"]');

    if (ctaButton) {
        ctaButton.addEventListener('click', function (e) {
            e.preventDefault();

            const smoother = ScrollSmoother.get();

            if (smoother) {
                smoother.scrollTo('#sectionFour', true, 'top 100px');
            } else {
                gsap.to(window, {
                    duration: 1.5,
                    scrollTo: { y: '#sectionFour', offsetY: 100 },
                    ease: 'power2.inOut'
                });
            }
        });
    }
});
</script>

<?php
// Carga el archivo de desgloces según idioma activo
$current_lang = 'es';

if ( function_exists( 'pll_current_language' ) ) {
    $current_lang = pll_current_language();
} elseif ( defined( 'ICL_LANGUAGE_CODE' ) ) {
    $current_lang = ICL_LANGUAGE_CODE;
} else {
    $current_lang = substr( get_locale(), 0, 2 );
}

$desgloces_url = get_template_directory_uri() . '/assets/js/desgloces-' . $current_lang . '.js';
?>

<!-- Desgloces idioma: <?php echo strtoupper( $current_lang ); ?> -->
<script src="<?php echo esc_url( $desgloces_url ); ?>"></script>

<?php get_footer(); ?>
