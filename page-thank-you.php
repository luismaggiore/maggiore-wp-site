<?php
/**
 * Template Name: Thank You
 *
 * Se muestra tras un envío exitoso del formulario de contacto.
 * - Mensaje de confirmación genérico
 * - Próximos pasos
 * - Casos de éxito destacados
 * noindex: sí (página transaccional)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

// noindex para esta página transaccional
add_action( 'wp_head', function() {
    echo '<meta name="robots" content="noindex, nofollow">';
}, 1 );
?>

<div class="page-thank-you">
    <main class="container py-5">

        <!-- ================================================================
             HERO DE CONFIRMACIÓN
        ================================================================ -->
        <section class="thankyou-hero text-center py-5 mb-5">

            <!-- Ícono animado -->
            <div class="thankyou-icon mb-4">
                <svg class="check-circle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52" width="80" height="80" aria-hidden="true">
                    <circle class="check-circle__ring" cx="26" cy="26" r="25" fill="none" stroke-width="2"/>
                    <path   class="check-circle__check" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M14 27 l8 8 l16-16"/>
                </svg>
            </div>

            <h1 class="display-5 mb-3">
                <?php _e( '¡Mensaje recibido!', 'maggiore' ); ?>
            </h1>

            <p class="lead text-muted mb-0" style="max-width: 520px; margin: 0 auto;">
                <?php _e( 'Gracias por contactarnos. Revisaremos tu mensaje y te responderemos en menos de 24 horas hábiles.', 'maggiore' ); ?>
            </p>

        </section>

        <!-- ================================================================
             PRÓXIMOS PASOS
        ================================================================ -->
        <section class="thankyou-steps mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-mg p-4 p-md-5">

                        <h2 class="h5 text-center mb-4">
                            <?php _e( '¿Qué pasa ahora?', 'maggiore' ); ?>
                        </h2>

                        <div class="row text-center g-4">

                            <!-- Paso 1 -->
                            <div class="col-md-4">
                                <div class="step-icon mb-3">
                                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--secondary-color);" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <p class="small mb-0"><?php _e( 'Revisamos tu mensaje y evaluamos cómo podemos ayudarte.', 'maggiore' ); ?></p>
                            </div>

                            <!-- Paso 2 -->
                            <div class="col-md-4">
                                <div class="step-icon mb-3">
                                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--secondary-color);" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                </div>
                                <p class="small mb-0"><?php _e( 'Te contactamos en menos de 24 horas hábiles.', 'maggiore' ); ?></p>
                            </div>

                            <!-- Paso 3 -->
                            <div class="col-md-4">
                                <div class="step-icon mb-3">
                                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--secondary-color);" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                </div>
                                <p class="small mb-0"><?php _e( 'Definimos juntos cómo impulsar tu marca.', 'maggiore' ); ?></p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================
             CASOS DE ÉXITO — MIENTRAS ESPERAS
        ================================================================ -->
        <?php
        $casos_query = new WP_Query( [
            'post_type'      => 'mg_caso_exito',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'DESC',
            'lang'           => function_exists( 'pll_current_language' ) ? pll_current_language() : '',
        ] );

        if ( $casos_query->have_posts() ) : ?>

        <section class="thankyou-casos mb-5">

            <h2 class="h4 text-center mb-2">
                <?php _e( 'Mientras tanto, conoce nuestro trabajo', 'maggiore' ); ?>
            </h2>
            <p class="text-muted text-center mb-4">
                <?php _e( 'Resultados reales para marcas reales.', 'maggiore' ); ?>
            </p>

            <div class="row g-4">
                <?php
                set_query_var( 'custom_query', $casos_query );
                get_template_part( 'template-parts/loops/loop', 'caso-exito' );
                ?>
            </div>

            <div class="text-center mt-4">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'mg_caso_exito' ) ); ?>" class="btn btn-outline-secondary">
                    <?php _e( 'Ver todos los casos de éxito', 'maggiore' ); ?> &rarr;
                </a>
            </div>

        </section>

        <?php endif; wp_reset_postdata(); ?>

        <!-- ================================================================
             CTA FINAL
        ================================================================ -->
        <section class="thankyou-cta text-center py-4 mb-5">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-filter text-white">
                <?php _e( 'Volver al inicio', 'maggiore' ); ?>
            </a>
        </section>

    </main>
</div>

<style>
/* ── Animación del check ─────────────────────────── */
.check-circle { display: block; margin: 0 auto; }

.check-circle__ring {
    stroke: var(--secondary-color, #667eea);
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    animation: stroke-ring 0.6s cubic-bezier(0.65,0,0.45,1) forwards;
}
.check-circle__check {
    stroke: var(--secondary-color, #667eea);
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke-check 0.4s cubic-bezier(0.65,0,0.45,1) 0.5s forwards;
}
@keyframes stroke-ring  { to { stroke-dashoffset: 0; } }
@keyframes stroke-check { to { stroke-dashoffset: 0; } }

/* ── Íconos de pasos ─────────────────────────────── */
.step-icon { display: flex; justify-content: center; }
</style>

<?php get_footer(); ?>
