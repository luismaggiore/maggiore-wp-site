<?php
if (!defined('ABSPATH')) exit;
get_header();

$lang = function_exists('pll_current_language') ? pll_current_language() : false;
?>

<main class="container py-5">
    
    <!-- Header -->
    <header class="mb-5" style="padding-top: 10vh;">
        <h1 class="display-4 mb-3">
            <?php _e('Nuestras Áreas', 'maggiore'); ?>
        </h1>
        <p class="lead text-muted">
            <?php _e('Conoce los equipos especializados que componen nuestra estructura organizacional.', 'maggiore'); ?>
        </p>
    </header>

    <!-- Grid de áreas -->
    <?php if (have_posts()): ?>
        <section class="areas-grid">
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php while (have_posts()): the_post(); ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'area'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Paginación -->
        <?php
        $pagination = paginate_links([
            'prev_text' => '&laquo; ' . __('Anterior', 'maggiore'),
            'next_text' => __('Siguiente', 'maggiore') . ' &raquo;',
            'type' => 'array',
        ]);
        ?>
        
        <?php if ($pagination): ?>
            <nav class="mt-5" aria-label="<?php _e('Paginación de áreas', 'maggiore'); ?>">
                <ul class="pagination justify-content-center">
                    <?php foreach ($pagination as $page): ?>
                        <li class="page-item <?= strpos($page, 'current') !== false ? 'active' : ''; ?>">
                            <?= str_replace('page-numbers', 'page-link', $page); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>

    <?php else: ?>
        <!-- Mensaje si no hay áreas -->
        <div class="alert alert-info text-center py-5">
            <h2 class="h4 mb-3"><?php _e('No se encontraron áreas', 'maggiore'); ?></h2>
            <p class="mb-0">
                <?php _e('Actualmente no hay áreas publicadas.', 'maggiore'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

    <!-- Información adicional -->
    <section class="mt-5 pt-5 border-top">
        <div class="row">
            <div class="col-md-6">
                <h2 class="h4 mb-3"><?php _e('¿Qué son las áreas?', 'maggiore'); ?></h2>
                <p class="text-muted">
                    <?php _e('Las áreas representan las divisiones estratégicas de nuestra agencia, cada una especializada en diferentes aspectos del marketing digital y servicios relacionados.', 'maggiore'); ?>
                </p>
            </div>
            <div class="col-md-6">
                <h2 class="h4 mb-3"><?php _e('¿Cómo trabajamos?', 'maggiore'); ?></h2>
                <p class="text-muted">
                    <?php _e('Cada área cuenta con un equipo de especialistas que colaboran para ofrecer soluciones integrales. Nuestros servicios se organizan por área para garantizar expertise y excelencia en cada proyecto.', 'maggiore'); ?>
                </p>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
