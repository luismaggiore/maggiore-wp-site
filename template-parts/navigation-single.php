<?php
/**
 * Navegación Prev/Next para Singles
 * 
 * Variables disponibles:
 * - $prev_label: Texto para enlace anterior (default: 'Anterior')
 * - $next_label: Texto para enlace siguiente (default: 'Siguiente')
 * - $show_thumbnail: Mostrar thumbnail si existe (default: false)
 */

if (!defined('ABSPATH')) exit;

// Defaults
$prev_label = $prev_label ?? __('Anterior', 'maggiore');
$next_label = $next_label ?? __('Siguiente', 'maggiore');
$show_thumbnail = $show_thumbnail ?? false;

$prev_post = get_previous_post();
$next_post = get_next_post();
?>

<?php if ($prev_post || $next_post): ?>
<nav class="mt-5  single-navigation " style="border-top:var(--border-container)">
    <div class="row">
        <!-- Anterior -->
        <div class="col-6">
            <?php if ($prev_post): 
                $prev_thumb = $show_thumbnail ? get_the_post_thumbnail_url($prev_post->ID, 'thumbnail') : false;
            ?>
                <a href="<?= esc_url(get_permalink($prev_post)); ?>" 
                   class="text-decoration-none d-block mb-3  <?= $prev_thumb ? 'd-flex align-items-center' : ''; ?>">
                    
                    <?php if ($prev_thumb): ?>
                        <img src="<?= esc_url($prev_thumb); ?>" 
                             alt="<?= esc_attr($prev_post->post_title); ?>"
                             class="me-3" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    <?php endif; ?>
                    
                    <div>
                        <small >
                            <?= esc_html($prev_label); ?>
                        </small>
                        <p style="color: white;font-size:0.9rem"><?= esc_html($prev_post->post_title); ?></p>
                    </div>
                </a>
            <?php endif; ?>
        </div>

        <!-- Siguiente -->
        <div class="col-6 text-end">
            <?php if ($next_post): 
                $next_thumb = $show_thumbnail ? get_the_post_thumbnail_url($next_post->ID, 'thumbnail') : false;
            ?>
                <a href="<?= esc_url(get_permalink($next_post)); ?>" 
                   class="text-decoration-none d-block mb-3   <?= $next_thumb ? 'd-flex align-items-center justify-content-md-end' : ''; ?>">
                    
                    <?php if ($next_thumb): ?>
                        <div class="order-md-2 ms-md-3">
                            <img src="<?= esc_url($next_thumb); ?>" 
                                 alt="<?= esc_attr($next_post->post_title); ?>"
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                    
                    <div class="<?= $next_thumb ? 'order-md-1' : ''; ?>">
                        <small class="next-flecha">
                            <?= esc_html($next_label); ?>
         
                        </small>
                        <p style="color: white;font-size:0.9rem"><?= esc_html($next_post->post_title); ?></p>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php endif; ?>