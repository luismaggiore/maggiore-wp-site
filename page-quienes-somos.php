<?php
/**
 * Template Name: Quienes Somos
 * Description: Página institucional con presentación, áreas y valores
 */

if (!defined('ABSPATH')) exit;
get_header();

$lang = function_exists('pll_current_language') ? pll_current_language() : false;
?>

<main>

    <!-- HERO SECTION -->
    <section class=" quienes-somos-hero p-top" >
        <div class="container-fluid">
            <div class="row justify-content-center ">
                <div class="col-lg-10 col-xl-10 mt-5">
                    <h1 class="title-reveal2">
                        <?php _e('Especialistas en investigación de mercados y estrategias, diseño + ejecución de campañas de marketing digital para marcas de presencia internacional.', 'maggiore'); ?>
                    </h1>
                    <div class="bajada-reveal">
                        <p class="bajada">
                            <?php _e('Desde Chile, trabajamos en inglés, portugués y español para importantes clientes de diversas industrias en todo el continente americano', 'maggiore'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DIRECCIÓN -->
    <?php
    // Obtener todos los directores (quienes tienen áreas asignadas como director)
    $args_all_equipo = [
        'post_type'   => 'mg_equipo',
        'numberposts' => -1,
        'orderby'     => 'menu_order',
        'order'       => 'ASC'
    ];
    if ($lang) $args_all_equipo['lang'] = $lang;
    
    $todos_equipo = get_posts($args_all_equipo);
    
    // Filtrar solo los que son directores
    $directores = [];
    foreach ($todos_equipo as $miembro) {
        if (mg_is_director($miembro->ID)) {
            $directores[] = $miembro;
        }
    }
    
    if ($directores):
    ?>
    <section class=" separador container-fluid mt-5 pt-5">
            <div class="feature-name-2 mb-3 mx-auto text-center">
                <h2><?php _e('Fundadores', 'maggiore'); ?></h2>
            </div>
            
            <?php 
            $index = 0;
            foreach ($directores as $post):
                setup_postdata($post);
                
                $director_id = $post->ID;
                $director_img = get_the_post_thumbnail_url();
                $nombre = get_the_title();
                $bio = get_post_meta($director_id, 'mg_equipo_bio', true);
                $linkedin = get_post_meta($director_id, 'mg_equipo_linkedin', true); 
                $primer_nombre = explode(' ', $nombre)[0];
                                $cargo = get_post_meta($director_id, 'mg_equipo_cargo', true);

                // Determinar si es par o impar para alternar orden
                $is_even = ($index % 2 === 0);
                $index++;
            ?>

            





                    <div class="row g-2 mb-2 align-items-stretch <?= $is_even ? 'justify-content-start' : 'justify-content-end'; ?>">
                        
                        <!-- Card de Equipo -->
                        <div class="col-12 col-xxl-2 col-xl-3 col-lg-3 text-center position-relative <?= $is_even ? 'order-1 order-lg-1' : 'order-1 order-lg-2'; ?>" >
                   
                        
                        <img src="<?= esc_url( $director_img) ?>" class='img-fluid border-mg'
                        itemprop='image'
                        style='height:100%;width:100%; object-fit: cover;object-position:50% 30%;'
                          alt="<?php echo esc_attr('Foto de ' . $nombre); ?>">
                
                
                  
                 <a href="<?= esc_url($director_url); ?>"  class="stretched-link" 
                   aria-label="<?php echo esc_attr(sprintf(__('Ver perfil completo de %s', 'maggiore'), $nombre)); ?>">

                </a>

                    </div>
                        
                        <!-- Biografía -->
                        <div class="col-12 col-xxl-9 col-xl-9 col-lg-9  <?= $is_even ? 'order-2 order-lg-2' : 'order-2 order-lg-1'; ?>" style="max-width:1300px">
                            <div class="card-mg h-100 position-relative <?= $is_even ? 'text-start' : 'text-lg-end'; ?>  d-flex flex-column justify-content-center" >
                                    <?php if ($linkedin): ?>
      <a
        href="<?php echo esc_url($linkedin); ?>"
       class="service-tag mt-1 mx-0 px-1  btn-linkedin member-linkedin position-absolute "              
        target="_blank"
        rel="noopener noreferrer"
        itemprop="sameAs"
        style="top:10px;right:10px;"
        aria-label="<?php echo esc_attr(sprintf(__('Ver perfil de %s en LinkedIn', 'maggiore'), $nombre)); ?>"
      >
   
                           <i>
<svg class="m-1" id="Modo_de_aislamiento" width="11" height="14"  fill="white" data-name="Modo de aislamiento" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.18 13.71">
  <path d="M2.54,10.71V3.48H.14v7.22h2.4ZM1.34,2.5c.84,0,1.36-.55,1.36-1.25-.01-.71-.52-1.25-1.34-1.25S0,.54,0,1.25s.52,1.25,1.33,1.25h.02ZM6.25,10.71v-4.03c0-.22.02-.43.08-.59.17-.43.57-.88,1.23-.88.87,0,1.22.66,1.22,1.63v3.86h2.4v-4.14c0-2.22-1.18-3.25-2.76-3.25-1.27,0-1.85.7-2.16,1.19v.03h-.02l.02-.03v-1.02h-2.4c.03.68,0,7.22,0,7.22h2.4Z"/>
</svg></i>
      </a>
  <?php endif; ?>
                            
                            <h3 class="display-6 mb-2 mt-2">
                                      <?= esc_html($nombre); ?>
                                </h3>
                                 <p  class="text-muted">
                                        <?= esc_html($cargo); ?>
                                    </p>
                                <?php if ($bio): ?><div class="w-100 d-flex <?= $is_even ? 'justify-content-start' : 'justify-content-lg-end'; ?>">     
                                        <p  style="line-height: 1.6; margin: 0 0 1.5rem 0;max-width:1200px">
                                        <?= esc_html($bio); ?>
                                    </p></div>

                                <?php endif; ?>

                                <?php    
                                $areas_director = mg_is_director($director_id);
                                
                                if ($areas_director && !empty($areas_director)): 
                                ?>
                                    <div >
                                        <p class="label">
                                            <?php _e('Director de', 'maggiore'); ?>
                                        </p>
                                        <div class="d-flex flex-wrap gap-2  <?= $is_even ? 'justify-content-start' : 'justify-content-lg-end'; ?> ">
                                            <?php foreach ($areas_director as $area): ?>
                                                <a href="<?= esc_url(get_permalink($area->ID)); ?>" 
                                                   class="service-tag position-relative"
                                                   style="z-index: 10;"
                                                   onclick="event.stopPropagation();">
                                                    <?= esc_html($area->post_title); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                       
                   
                </div>
            <?php 
            endforeach;
            wp_reset_postdata();
            ?>
            
        </section>


    <?php endif; ?>

    <!-- ÁREAS Y EQUIPOS -->
    <?php
    $args_areas = [
        'post_type'   => 'mg_area',
        'numberposts' => -1,
        'orderby'     => 'menu_order',
        'order'       => 'ASC'
    ];
    if ($lang) $args_areas['lang'] = $lang;
    
    $areas = get_posts($args_areas);
    
    if ($areas):
    ?>
        <section class="mt-5 pt-5" >
            <div class=" container-fluid " >
            <div class="feature-name-2 mb-2 text-center mx-auto">
                <h2><?php _e('Nuestros Equipos', 'maggiore'); ?></h2>
            </div>
            </div>
            
            
            <?php 
            foreach ($areas as $area_post):
                setup_postdata($area_post);
                $area_id = $area_post->ID;
                $area_titulo = get_the_title($area_id);
                $area_descripcion = get_post_meta($area_id, 'mg_area_descripcion', true);
                
                // Obtener miembros del área desde el meta field
                $miembros_ids = get_post_meta($area_id, 'mg_area_miembros', true);
                
                if (!is_array($miembros_ids) || empty($miembros_ids)) continue;
                
                // Obtener los posts de los miembros
                $args_miembros = [
                    'post_type'   => 'mg_equipo',
                    'post__in'    => $miembros_ids,
                    'numberposts' => -1,
                    'orderby'     => 'title',
                    'order'       => 'ASC'
                ];
                if ($lang) $args_miembros['lang'] = $lang;
                
                $miembros = get_posts($args_miembros);
                
                if (!$miembros) continue;
            ?>
              <div class="container-fluid  py-2  " 
     itemprop="itemListElement" 
     itemscope 
     itemtype="https://schema.org/ListItem">
    <meta itemprop="position" content="<?= $area_index; ?>">
    <div class="row card-mg  p-2 pb-2 pt-0 g-2 gx-5 align-items-start justify-content-between " 
         itemprop="item" 
         itemscope 
         itemtype="https://schema.org/Organization">
        
        <!-- Columna Info del Área -->
        <div class="col-12 col-xxl-4 col-xl-6   ">
        <div class="p-3 pt-5" >
                <h3 class="mb-4 display-6" itemprop="name"><?= esc_html($area_titulo); ?></h3>
            
            <?php if ($area_descripcion): ?>
                <p class="mb-4" itemprop="description">
                    <?= esc_html($area_descripcion); ?>
                </p>
            <?php endif; ?>
        </div></div>
        <!-- Columna Grid de Equipo -->
        <div class="col-12 col-xxl-8  col-xl-6 p-0" 
             itemprop="member" 
             itemscope 
             itemtype="https://schema.org/ItemList">
            <div class="row  g-2 justify-content-xl-end">
                <?php 
                $member_index = 0;
                foreach ($miembros as $post):
                    setup_postdata($post);
                    $member_index++;
                ?>
                    <div class="col-xxl col-xl-4 col-md-3 col-6 col-xxs-12  alternative-grid" 
                         style="" 
                         itemprop="itemListElement" 
                         itemscope 
                         itemtype="https://schema.org/ListItem">
                        <meta itemprop="position" content="<?= $member_index; ?>">
                        <?php get_template_part('template-parts/card', 'equipo'); ?>
                    </div>
                <?php 
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        </div>
         </div>
    </div>
              
            <?php 
            endforeach;
            wp_reset_postdata();
            ?>
        </section>
    <?php endif; ?>

    <!-- MISIÓN Y VISIÓN -->
    <section class=" separador container-fluid mt-5 pt-5">
        <div class="row g-2 mb-2">
            <div class="col-lg-7">
                <article class="mb-5" style="height: 100%;">
                    <div class="feature-name">
                    <h3 class=" mb-2  "><?php _e('Nuestra Misión', 'maggiore'); ?></h3></div>
                    <p class="mision-vision h3 display-5" style="line-height: 1;">
                        <?php _e('Ser un aliado estratégico en la toma de decisiones de negocio y el fortalecimiento de las marcas en el mundo digital.', 'maggiore'); ?>
                    </p>
                    <hr class="hr-mg">
                </article>
            </div>
             </div>
              <div class="row g-2 justify-content-end">
            <div class="col-lg-7">
                <article class="mt-5 " style="height: 100%;">
                       <div class="feature-name">
                    <h3 class="  "><?php _e('Nuestra Visión', 'maggiore'); ?></h3></div>
                    <p class="mision-vision h3 display-5 " style="line-height: 1;">
                        <?php _e('Convertirnos en una agencia con operación global, desde las Américas para el mundo.', 'maggiore'); ?>
                    </p>
                        <hr class="hr-mg">
                </article>
            </div>
        </div>
    </section>

    <!-- VALORES DE MARCA -->
    <section class=" reveal-group separador container-fluid mt-5 pt-5 mb-5 mg_values_scope">
        <div class="feature-name-2 mb-3 mx-auto text-center">
            <h2 ><?php _e('Valores de Marca', 'maggiore'); ?></h2>
        </div>
            <div>
                            <h3 class="display-2 text-center mb-5 mt-5 reveal-up "><?php _e('En Maggiore  <span class="mg_values_letter">C</span><span class="mg_values_letter">A</span><span class="mg_values_letter">D</span><span class="mg_values_letter">A</span> paso importa', 'maggiore'); ?></h3>

            </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-2">
            <!-- Consistencia -->
            <div class="col">
                <article class="card-mg mg_values_card reveal-up" style="height: 100%">
                    <div class="d-flex align-items-start mb-3">
                   
                        <div>
                            <h3 class="h4 mb-2"><?php _e('Consistencia', 'maggiore'); ?></h3>
                            <p style="color: #999; line-height: 1.6;">
                                <?php _e('En la calidad de nuestras entregas.', 'maggiore'); ?>
                            </p>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Adaptabilidad -->
            <div class="col">
                <article class="card-mg mg_values_card reveal-up" style="height: 100%">
                    <div class="d-flex align-items-start mb-3 ">
                  
                        <div>
                            <h3 class="h4 mb-2"><?php _e('Adaptabilidad', 'maggiore'); ?></h3>
                            <p style="color: #999; line-height: 1.6;">
                                <?php _e('Para apoyar a cada cliente en su necesidad específica.', 'maggiore'); ?>
                            </p>
                        </div>
                    </div>
                </article>
            </div>
            
            <!-- Determinación -->
            <div class="col">
                <article class="card-mg mg_values_card reveal-up" style="height: 100%">
                    <div class="d-flex align-items-start mb-3">
               
                        <div>
                            <h3 class="h4 mb-2"><?php _e('Determinación', 'maggiore'); ?></h3>
                            <p style="color: #999; line-height: 1.6;">
                                <?php _e('En sorprender a nuestros clientes con los mejores insights y entregables.', 'maggiore'); ?>
                            </p>
                        </div>
                    </div>
                </article>
            </div>
            
            <!-- Atención -->
            <div class="col">
                <article class="card-mg mg_values_card reveal-up" style="height: 100%">
                    <div class="d-flex align-items-start mb-3">
            
                        <div>
                            <h3 class="h4 mb-2"><?php _e('Atención', 'maggiore'); ?></h3>
                            <p style="color: #999; line-height: 1.6;">
                                <?php _e('A los detalles para evitar ineficiencias o atrasos.', 'maggiore'); ?>
                            </p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
