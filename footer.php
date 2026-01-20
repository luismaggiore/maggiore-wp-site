
    <!-- Footer principal -->
  
<!-- BREADCRUMBS -->
<?php if (!is_front_page() && !is_home()): ?>
<nav aria-label="breadcrumb" class="breadcrumb-container">
    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <i class="bi bi-house-door"></i> <?php _e('Inicio', 'maggiore'); ?>
                </a>
            </li>
            <?php
            if (is_singular()) {
                $post_type = get_post_type();
                $post_type_object = get_post_type_object($post_type);
                
                // Enlace al archivo del CPT
                if ($post_type !== 'post' && $post_type !== 'page') {
                    $archive_link = get_post_type_archive_link($post_type);
                    if ($archive_link) {
                        echo '<li class="breadcrumb-item"><a href="' . esc_url($archive_link) . '">' . 
                             esc_html($post_type_object->labels->name) . '</a></li>';
                    }
                }
                
                // Título actual
                echo '<li class="breadcrumb-item active" aria-current="page">' . 
                     get_the_title() . '</li>';
                     
            } elseif (is_archive()) {
                echo '<li class="breadcrumb-item active" aria-current="page">' . 
                     get_the_archive_title() . '</li>';
                     
            } elseif (is_search()) {
                echo '<li class="breadcrumb-item active" aria-current="page">' . 
                     __('Resultados de búsqueda', 'maggiore') . '</li>';
            }
            ?>
        </ol>
    </div>
</nav>
<?php endif; ?>

<!-- FOOTER -->
<footer class="footer-maggiore">
    <div class="container">
        <div class="row footer-main">
            
            <!-- COLUMNA 1: Logo + Contacto + Redes -->
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <!-- Logo -->
                <div class="mb-4">
                    <img class="m-0 p-0 d-lg-inline-block d-none" 
                         src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-mm.svg" 
                         style="width: 180px; margin: 60px auto" 
                         alt="Maggiore">
                </div>
                
                <!-- Información de contacto -->
                <div class="footer-contact mb-4">
                    <div class="contact-item mb-2">
                        <i class="bi bi-geo-alt"></i>
                        <span> <?php echo maggiore_get_direccion(true); ?></span>
                    </div>
                    
                    <div class="contact-item mb-2">
                        <i class="bi bi-envelope"></i>
                        <a href="mailto:<?php echo maggiore_get_email(); ?>"><?php echo maggiore_get_email(); ?></a>
                    </div>
                    
                    <div class="contact-item mb-2">
                        <i class="bi bi-telephone"></i>
                        <a href="tel:<?php echo maggiore_get_telefono(); ?>">                                    <?php echo maggiore_get_telefono(); ?>
</a>
                    </div>
                </div>
                
                <!-- Redes Sociales -->
                <div class="footer-social">
                    <?php maggiore_social_icons(); ?>
                </div>
            </div>
            
            <!-- COLUMNA 2: Menú de WordPress -->
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <h4 class="footer-title"><?php _e('Navegación', 'maggiore'); ?></h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer-menu',
                    'menu_class'     => 'footer-menu',
                    'container'      => 'nav',
                    'fallback_cb'    => false,
                    'depth'          => 1,
                ]);
                ?>
            </div>
            
            <!-- COLUMNA 3: Archives de CPTs -->
            <div class="col-lg-4 col-md-12">
                <h4 class="footer-title"><?php _e('Explora', 'maggiore'); ?></h4>
                <nav class="footer-cpt-links">
                    <?php
                    $cpts = [
                        'mg_portafolio' => __('Portafolio', 'maggiore'),
                        'mg_servicio'   => __('Servicios', 'maggiore'),
                        'mg_equipo'     => __('Equipo', 'maggiore'),
                        'mg_cliente'    => __('Clientes', 'maggiore'),
                        'mg_caso_exito' => __('Casos de Éxito', 'maggiore'),
                        'mg_area'       => __('Áreas', 'maggiore')
                    ];
                    
                    foreach ($cpts as $cpt => $label) {
                        $archive_link = get_post_type_archive_link($cpt);
                        if ($archive_link) {
                            echo '<a href="' . esc_url($archive_link) . '" class="footer-cpt-link">' . 
                                 esc_html($label) . '</a>';
                        }
                    }
                    ?>
                </nav>
            </div>
            
        </div>
        
        <!-- COPYRIGHT -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-12 text-center">
                    <p class="copyright-text mb-0">
                        &copy; <?php echo date('Y'); ?> Maggiore. <?php _e('Todos los derechos reservados', 'maggiore'); ?>.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Schema Markup para SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Maggiore",
        "url": "<?php echo esc_url(home_url('/')); ?>",
        "logo": "<?php echo esc_url(wp_get_attachment_url(get_theme_mod('custom_logo'))); ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Alcántara 1791",
            "addressLocality": "Las Condes",
            "addressRegion": "Santiago",
            "addressCountry": "CL"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+56-9-1234-5678",
            "contactType": "Marketing",
            "email": "marketing@maggiore.cl"
        },
        "sameAs": [
            "https://www.linkedin.com/company/maggiore",
            "https://www.instagram.com/maggiore"
        ]
    }
    </script>
</footer>
       </div> <!-- #smooth-content -->
    </div> <!-- #smooth-wrapper -->


    <?php wp_footer(); ?>
  </body>
</html>
