<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    // SEO custom (meta, og, twitter, schema, canonical, hreflang)
    if (function_exists('mg_output_seo_meta')) {
        mg_output_seo_meta();
    }
    ?>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php 
if (function_exists('wp_body_open')) {
    wp_body_open();
} 
?>
  <!-- Aurora canvas + overlays si aplica en todas las páginas -->
  <div class="aurora" style="position: fixed">
    <canvas  id="aurora"></canvas>
  </div>
  <div class="overlay" style="position: fixed"></div>
  <div class="overlay2" style="position: fixed"></div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top ">
  
      
      <!-- Logo móvil -->
      <a class="navbar-brand d-lg-none d-block " href="<?php echo esc_url(home_url('/')); ?>">
        <img
          src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-mm.svg"
          alt="<?php esc_attr_e('Maggiore Marketing Logo', 'maggiore'); ?>"
          style="width: 140px"
        />
      </a>

      <!-- Botón hamburguesa -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
              aria-controls="mainNav" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'maggiore'); ?>">
        <span class="navbar-toggler-icon"></span>
      </button>

   <?php if (function_exists('pll_the_languages')): ?>
          <div class=" language-switcher  " style="margin-left:-10px;margin-right:10px">
            <?php pll_the_languages([
              'show_flags' => 1,
              'show_names' => 0,
              'hide_current' => 0,
              'raw' => 0,
            ]); ?>
          </div>
        <?php endif; ?>


      <!-- Menú principal -->
      <div class="collapse navbar-collapse" id="mainNav">
        <?php
          wp_nav_menu([
            'theme_location'  => 'primary',
            'depth'           => 2,
            'container'       => false,
            'menu_class'      => 'navbar-nav ms-auto ',
            'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
            'walker'          => new WP_Bootstrap_Navwalker(),
          ]);
        ?>
        


      </div>
        
  </nav>




  <!-- Wrapper para animaciones con ScrollSmoother -->
  <div id="smooth-wrapper">
    <div id="smooth-content">
  <?php if (!is_page_template('page-home.php')): ?>
<div class="logo-fixed d-none d-lg-block" style="position: absolute; top: 60px; left: 40px; z-index: 1000;">
    <a href="<?php echo esc_url(home_url('/')); ?>">
        <img
            src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-mm.svg"
            alt="<?php esc_attr_e('Maggiore Marketing Logo', 'maggiore'); ?>"
            style="width: 140px;"
        />
    </a>
</div>
<?php endif; ?>