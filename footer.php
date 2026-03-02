
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
                         style="width: 180px; margin: 60px auto"  width="180" height="50"
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
    <?php $email = maggiore_get_email(); ?>
    <a href="mailto:<?php echo antispambot($email); ?>"><?php echo antispambot($email); ?></a>
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
                <p class="footer-title"><?php _e('Navegación', 'maggiore'); ?></p>
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
                <p class="footer-title"><?php _e('Explora', 'maggiore'); ?></p>
                <nav class="footer-cpt-links">
                    <?php
                    $cpts = [
                        'mg_servicio'   => __('Servicios', 'maggiore'),
                        'mg_cliente'    => __('Clientes', 'maggiore'),
                        'mg_caso_exito' => __('Casos de Éxito', 'maggiore'),
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
    

</footer>
       </div> <!-- #smooth-content -->
    </div> <!-- #smooth-wrapper -->


    <?php wp_footer(); ?>
  </body>
</html>
