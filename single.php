<?php
if (!defined('ABSPATH')) exit;
get_header();

the_post();
$post_id = get_the_ID();
$lang = function_exists('pll_get_post_language') ? pll_get_post_language($post_id) : false;

// === Datos del Post ===
$fecha = get_the_date('c'); // formato ISO 8601
$fecha_lectura = get_the_date(get_option('date_format'));
$fecha_modificacion = get_the_modified_date('c');

// === URL para compartir ===
$post_url = urlencode(get_permalink($post_id));
$post_title = urlencode(get_the_title($post_id));

// === Datos del Autor (miembro del equipo) ===
$autor_id = get_post_meta($post_id, 'mg_blog_autor', true);

if ($autor_id) {
    // Ajustar autor según idioma si es necesario
    if ($lang && function_exists('pll_get_post_language') && pll_get_post_language($autor_id) !== $lang) {
        $autor_id = pll_get_post($autor_id, $lang);
    }
    
    $autor_nombre = get_the_title($autor_id);
    $autor_cargo = get_post_meta($autor_id, 'mg_equipo_cargo', true);
    $autor_bio = get_post_meta($autor_id, 'mg_equipo_bio', true);
    $autor_foto = get_the_post_thumbnail_url($autor_id, 'medium');
    $autor_linkedin = get_post_meta($autor_id, 'mg_equipo_linkedin', true);
    $autor_email = get_post_meta($autor_id, 'mg_equipo_email', true);
    $autor_url = get_permalink($autor_id);
    $autor_area_id = get_post_meta($autor_id, 'mg_equipo_area', true);
    $autor_especialidades = get_post_meta($autor_id, 'mg_equipo_especialidades', true) ?: [];
    
    // Ajustar área del autor según idioma
    if ($lang && $autor_area_id && function_exists('pll_get_post_language') && pll_get_post_language($autor_area_id) !== $lang) {
        $autor_area_id = pll_get_post($autor_area_id, $lang);
    }
} else {
    // Fallback si no hay autor asignado
    $autor_nombre = get_bloginfo('name');
    $autor_cargo = '';
    $autor_bio = '';
    $autor_foto = '';
    $autor_linkedin = '';
    $autor_email = '';
    $autor_url = home_url();
    $autor_area_id = null;
    $autor_especialidades = [];
}

// === Tags del Post ===
$tags = get_the_tags($post_id);

// === Categorías del Post ===
$categorias = get_the_category($post_id);

// === Artículos Relacionados ===
$articulos_relacionados = [];
if ($autor_id) {
    $articulos_relacionados = get_posts([
        'post_type'   => 'post',
        'numberposts' => 3,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'post__not_in' => [$post_id],
        'meta_query'  => [[
            'key'     => 'mg_blog_autor',
            'value'   => $autor_id,
            'compare' => '='
        ]]
    ]);
    
    // Filtrar por idioma si es necesario
    if ($lang && !empty($articulos_relacionados)) {
        foreach ($articulos_relacionados as &$art) {
            if (function_exists('pll_get_post_language') && pll_get_post_language($art->ID) !== $lang) {
                $translated = pll_get_post($art->ID, $lang);
                if ($translated) {
                    $art = get_post($translated);
                }
            }
        }
    }
}

?>

<main class="container py-5" itemscope itemtype="https://schema.org/Article">

    <div  class="p-top">
           <header class="mb-2"> <!-- Categorías (solo si existen) -->

                        <?php if ($categorias && !is_wp_error($categorias) && !empty($categorias)): ?>
                                <ul class="services-tags" style="margin:auto;max-width:fit-content;margin-bottom:10px">
                                    <?php foreach ($categorias as $categoria): ?>
                                        <li class="service-tag ">
                                        <a class="text-white" href="<?= get_category_link($categoria->term_id); ?>">
                                            <?= esc_html($categoria->name); ?>
                                        </a>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                        <?php endif; ?>


                  <h1 class="display-4 mb-3 text-center" itemprop="headline"><?php the_title(); ?></h1>
 <div class="d-flex align-items-center flex-wrap gap-3 mb-4 blog-meta text-center" style="margin:auto; max-width:fit-content">
                        <!-- Fecha de publicación -->
                        <div class="d-flex align-items-center text-center">
                            <i class="bi bi-calendar3 me-2"></i>
                            <time datetime="<?= esc_attr($fecha); ?>" itemprop="datePublished" >
                                <?= esc_html($fecha_lectura); ?>
                            </time>
                        </div>
                        
                        <!-- Autor -->
                        <?php if ($autor_id): ?>
                            <div class="d-flex align-items-center text-center">
                                <i class="bi bi-person me-2"></i>
                                <span>
                                    <?php _e('Por', 'maggiore'); ?>
                                    <a href="<?= esc_url($autor_url); ?>" 
                                       itemprop="author" 
                                       itemscope 
                                       itemtype="https://schema.org/Person"
                                       style="color: inherit; text-decoration: none;">
                                        <span itemprop="name"><?= esc_html($autor_nombre); ?></span>
                                    </a>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <meta itemprop="dateModified" content="<?= esc_attr($fecha_modificacion); ?>">
                </header>
          

              <?php if (has_post_thumbnail()): ?>
                    <figure class="blog-thumbnail mb-2" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                        <?php the_post_thumbnail('large', [
                            'class' => 'img-fluid blog-img', 
                            'style' => ''
                        ]); ?>
                        <meta itemprop="url" content="<?= get_the_post_thumbnail_url($post_id, 'large'); ?>">
                        <meta itemprop="width" content="1200">
                        <meta itemprop="height" content="630">
                    </figure>
                <?php endif; ?>

        <div class="row g-2">
            <!-- COLUMNA PRINCIPAL DEL BLOG -->
           <div class="col-lg-8 order-lg-2 " >
                <div class=" card-mg container-blog" >
                <!-- HEADER DEL ARTÍCULO -->
             

                <!-- IMAGEN DESTACADA -->
             <div class="blog-mainbar">
                <!-- Contenido del artículo -->
                <article class="blog-content " itemprop="articleBody">
                    <?php the_content(); ?>
                </article>
                      </div>
                <!-- BARRA DE COMPARTIR EN REDES SOCIALES -->
                <div class="social-share-bar">
                    <h4 class="social-share-title">
                        <?php _e('Compartir este artículo', 'maggiore'); ?>
                    </h4>
                    <div class="social-share-buttons">
                        <!-- LinkedIn -->
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $post_url; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="social-share-btn linkedin"
                           aria-label="<?php _e('Compartir en LinkedIn', 'maggiore'); ?>">
                            <i class="bi bi-linkedin"></i>
                            <span>LinkedIn</span>
                        </a>

                        <!-- Twitter/X -->
                        <a href="https://twitter.com/intent/tweet?url=<?= $post_url; ?>&text=<?= $post_title; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="social-share-btn twitter"
                           aria-label="<?php _e('Compartir en Twitter', 'maggiore'); ?>">
                            <i class="bi bi-twitter-x"></i>
                            <span>Twitter</span>
                        </a>

                        <!-- Facebook -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $post_url; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="social-share-btn facebook"
                           aria-label="<?php _e('Compartir en Facebook', 'maggiore'); ?>">
                            <i class="bi bi-facebook"></i>
                            <span>Facebook</span>
                        </a>

                        <!-- WhatsApp -->
                        <a href="https://wa.me/?text=<?= $post_title; ?>%20<?= $post_url; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="social-share-btn whatsapp"
                           aria-label="<?php _e('Compartir en WhatsApp', 'maggiore'); ?>">
                            <i class="bi bi-whatsapp"></i>
                            <span>WhatsApp</span>
                        </a>

                        <!-- Email -->
                        <a href="mailto:?subject=<?= $post_title; ?>&body=<?= $post_url; ?>" 
                           class="social-share-btn email"
                           aria-label="<?php _e('Compartir por Email', 'maggiore'); ?>">
                            <i class="bi bi-envelope"></i>
                            <span>Email</span>
                        </a>

                        <!-- Copiar link -->
                        <button type="button" 
                                class="social-share-btn copy-link" 
                                data-url="<?= get_permalink($post_id); ?>"
                                aria-label="<?php _e('Copiar enlace', 'maggiore'); ?>">
                            <i class="bi bi-link-45deg"></i>
                            <span><?php _e('Copiar', 'maggiore'); ?></span>
                        </button>
                    </div>
                </div>
                  
                <!-- SECCIÓN DE COMENTARIOS -->
                <?php if (comments_open() || get_comments_number()): ?>
                    <div class="comments-section">
                        <?php comments_template(); ?>
                    </div>
                <?php endif; ?>
            </div>
                   </div>
            <!-- SIDEBAR: INFORMACIÓN DEL AUTOR Y TAXONOMÍAS -->
            <aside class="col-lg-4 order-lg-1"><div class="blog-sidebar">
                <?php if ($autor_id): ?>
                    <div class="sticky-top" style="top: 100px;">
                        <!-- Tarjeta del Autor -->
                        <div class="card-mg mb-2">
                            <h3 class="label mb-4">
                                <?php _e('Sobre el autor', 'maggiore'); ?>
                            </h3>

                            <!-- Foto y datos básicos -->
                            <div class="d-flex mb-3">

                         

                                <?php if ($autor_foto): ?>
                                    <a href="<?= esc_url($autor_url); ?>">
                                        <img src="<?= esc_url($autor_foto); ?>" 
                                             alt="<?= esc_attr("Foto de $autor_nombre"); ?>"
                                             class="rounded-circle me-3"
                                             style="width: 80px; height: 80px; object-fit: cover;"
                                             loading="lazy">
                                    </a>
                                <?php endif; ?>
                                
                                <div style="flex: 1;">
                                    <h4 class="h6 mb-0">
                                        <a href="<?= esc_url($autor_url); ?>" 
                                           style="color: white; text-decoration: none;">
                                            <?= esc_html($autor_nombre); ?>
                                        </a>
                                    </h4>
                                    
                                    <?php if ($autor_cargo): ?>
                                        <p class="text-muted small mb-0">
                                            <?= esc_html($autor_cargo); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if ($autor_area_id): ?>
                                        <p class="small mb-0">
                                            <a href="<?= get_permalink($autor_area_id); ?>" 
                                               style="color: var(--text-secondary); text-decoration: none;">
                                             
                                                <?= get_the_title($autor_area_id); ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Biografía breve -->
                            <?php if ($autor_bio): ?>
                                <p class="small mb-3" style="color: var(--text-secondary); line-height: 1.6;">
                                    <?= esc_html(wp_trim_words($autor_bio, 50, '...')); ?>
                                </p>
                              
                            <?php endif; ?>

                            <!-- Especialidades -->
                            <?php if (!empty($autor_especialidades) && is_array($autor_especialidades)): ?>
                                <div class="mb-3">
                                    <h5 class="label"><?php _e('Especialidades', 'maggiore'); ?></h5>
                                        <ul class="list-unstyled row g-1">

                                      

                                        <?php foreach (array_slice($autor_especialidades, 0, 5) as $especialidad): ?>
                                            <li class="col-auto">
                                                <span class="badge-especialidad">
                                                    <?= esc_html($especialidad); ?>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <!-- Redes sociales -->
                            <div class="d-flex gap-2">
                                <?php if ($autor_linkedin): ?>
                                    <a href="<?= esc_url($autor_linkedin); ?>" 
                                       target="_blank" 
                                       rel="noopener noreferrer" 
                                       class="btn   btn-sm btn-linkedin text-start"
                                       style="flex: 1;">
                                        <i class="bi bi-linkedin"></i> LinkedIn
                                    </a>
                                <?php endif; ?>

                                <?php if ($autor_email): ?>
                                    <a href="mailto:<?= esc_attr($autor_email); ?>" 
                                       class="btn btn-sm btn-outline-secondary text-start"
                                       style="flex: 1;">
                                        <i class="bi bi-envelope"></i> Email
                                    </a>
                                <?php endif; ?>
                            </div>

                        
                        </div>

                        <!-- Tags (si existen) -->
                        <?php if ($tags && !is_wp_error($tags)): ?>
                            <div class="card-mg mb-2">
                                <h4 class="label "><?php _e('Etiquetas', 'maggiore'); ?></h4>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach ($tags as $tag): ?>
                                        <a href="<?= get_tag_link($tag->term_id); ?>" 
                                           class="service-tag">
                                            #<?= esc_html($tag->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Artículos relacionados -->
                        <?php if (!empty($articulos_relacionados)): ?>
                            <div class="card-mg">
                                <h4 class="label ">
                                    <?php _e('Más artículos de este autor', 'maggiore'); ?> 
                                </h4>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($articulos_relacionados as $articulo): 
                                        $art_id = $articulo->ID;
                                        $art_titulo = get_the_title($art_id);
                                        $art_url = get_permalink($art_id);
                                        $art_fecha = get_the_date(get_option('date_format'), $art_id);
                                        $art_thumb = get_the_post_thumbnail_url($art_id, 'thumbnail');
                                    ?>
                                        <a href="<?= esc_url($art_url); ?>" class="related-article">
                                            <?php if ($art_thumb): ?>
                                                <img src="<?= esc_url($art_thumb); ?>" 
                                                     alt="<?= esc_attr($art_titulo); ?>"
                                                     class="related-article-thumb"
                                                     loading="lazy">
                                            <?php endif; ?>
                                            <div class="related-article-info">
                                                <h5 class="related-article-title">
                                                    <?= esc_html($art_titulo); ?>
                                                </h5>
                                                <p class="related-article-date">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?= esc_html($art_fecha); ?>
                                                </p>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
           </div>
            </aside>

             

        </div>
    </div>

</main>

<style>
</style>

<script>
// Script para copiar el enlace
document.addEventListener('DOMContentLoaded', function() {
    const copyButton = document.querySelector('.copy-link');
    if (copyButton) {
        copyButton.addEventListener('click', function() {
            const url = this.dataset.url;
            navigator.clipboard.writeText(url).then(function() {
                const originalText = copyButton.querySelector('span').textContent;
                copyButton.querySelector('span').textContent = '<?php _e('¡Copiado!', 'maggiore'); ?>';
                copyButton.style.borderColor = 'var(--secondary-color)';
                
                setTimeout(function() {
                    copyButton.querySelector('span').textContent = originalText;
                    copyButton.style.borderColor = '';
                }, 2000);
            }).catch(function(err) {
                console.error('Error al copiar: ', err);
            });
        });
    }
});
</script>

<?php get_footer(); ?>
