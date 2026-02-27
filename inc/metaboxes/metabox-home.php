<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Metaboxes para el template Home (page-home.php)
 * 4 metaboxes separados: Hero · Features · Casos de Éxito · Formulario
 * Solo visibles en páginas que usen el template page-home.php
 */


/* =============================================================================
   REGISTRO
============================================================================= */

add_action( 'add_meta_boxes', 'mg_home_register_metaboxes' );

function mg_home_register_metaboxes() {
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'page' ) return;

    add_meta_box( 'mg_home_hero',       '🚀 Hero',                       'mg_home_hero_render',       'page', 'normal', 'high'    );
    add_meta_box( 'mg_home_features',   '⭐ Features',                    'mg_home_features_render',   'page', 'normal', 'high'    );
    add_meta_box( 'mg_home_casos',      '🏆 Casos de Éxito — Encabezado','mg_home_casos_render',      'page', 'normal', 'default' );
    add_meta_box( 'mg_home_formulario', '📬 Formulario — Encabezado',     'mg_home_formulario_render', 'page', 'normal', 'default' );
}

// Oculta los metaboxes en páginas que NO usen page-home.php
add_action( 'do_meta_boxes', 'mg_home_hide_on_wrong_template' );

function mg_home_hide_on_wrong_template() {
    global $post;
    if ( ! $post ) return;

    $template = get_post_meta( $post->ID, '_wp_page_template', true );

    if ( $template !== 'page-home.php' ) {
        remove_meta_box( 'mg_home_hero',       'page', 'normal' );
        remove_meta_box( 'mg_home_features',   'page', 'normal' );
        remove_meta_box( 'mg_home_casos',      'page', 'normal' );
        remove_meta_box( 'mg_home_formulario', 'page', 'normal' );
    }
}


/* =============================================================================
   DEFAULTS — textos originales del template para que nunca quede vacío
============================================================================= */

function mg_home_defaults() {
    return [
        // Hero
        'mg_home_hero_bajada'          => 'Somos más que una agencia de Marketing Digital, Somos un aliado estratégico para tu negocio',
        'mg_home_hero_cta_texto'       => 'Agenda una Reunión',

        // Feature 1 — Robin Hood
        'mg_home_feature1_nombre'      => 'Metodología Robin Hood',
        'mg_home_feature1_titulo'      => 'Nuestra experiencia ayudando a crecer a gigantes nos permite impulsar a quienes quieren serlo.',
        'mg_home_feature1_parrafo'     => 'Usamos experiencia real, datos y ejecución precisa para darle a tu marca el empuje que necesita y el plan que merece para llegar más alto.',
        'mg_home_feature1_btn_mostrar' => '1',
        'mg_home_feature1_btn_texto'   => 'Conoce nuestros clientes',
        'mg_home_feature1_btn_link'    => '/clientes/',

        // Feature 2 — Inteligente
        'mg_home_feature2_nombre'      => 'Metodología Inteligente',
        'mg_home_feature2_titulo'      => 'Creamos estrategias sólidas, ejecutamos con excelencia y medimos los resultados para optimizar las probabilidades de éxito.',
        'mg_home_feature2_parrafo'     => 'En Maggiore no solo ejecutamos estrategias: las fundamentamos con datos. Gracias a nuestro departamento propio de inteligencia de mercados, respondemos preguntas de negocio de forma rápida, profunda y costo eficiente. Esto nos permite guiar a nuestros clientes con decisiones mejor informadas, anticipar tendencias y construir estrategias alineadas con lo que realmente ocurre en el mercado.',
        'mg_home_feature2_btn_mostrar' => '1',
        'mg_home_feature2_btn_texto'   => 'Ver casos de éxito',
        'mg_home_feature2_btn_link'    => '/casos-de-exito/',

        // Feature 3 — Flexible
        'mg_home_feature3_nombre'      => 'Metodología Flexible',
        'mg_home_feature3_titulo'      => 'Nuestro modelo de trabajo con tokens permite a los clientes gestionar su inversión con la máxima flexibilidad, sin perder control ni eficiencia.',
        'mg_home_feature3_parrafo'     => 'Al pagar anticipadamente, acceden a mejores precios y pueden asignar sus tokens según sus necesidades cambiantes: se acumulan, se intercambian entre tareas y les permiten comenzar sin cotizar cada actividad. Cada entrega tiene un valor claro en tokens, lo que vuelve el proceso ágil, transparente y auditable.',
        'mg_home_feature3_btn_mostrar' => '1',
        'mg_home_feature3_btn_texto'   => 'Conoce el sistema de tokens',
        'mg_home_feature3_btn_link'    => '/tokens/',

        // Casos de éxito
        'mg_home_casos_label'          => 'Casos de Éxito',
        'mg_home_casos_titulo'         => 'Clientes reales, estrategias medibles para un crecimiento sostenido',

        // Formulario
        'mg_home_form_titulo_linea1'   => 'Hablemos de crecimiento:',
        'mg_home_form_titulo_linea2'   => 'Cuéntanos tu meta y te guiamos',
    ];
}

/**
 * Devuelve el valor guardado, o el default si el campo no existe aún.
 * Para checkboxes: get_post_meta devuelve '' cuando nunca se ha guardado,
 * en ese caso el default ('1') activa el botón por defecto.
 */
function mg_home_get( $post_id, $key ) {
    $value = get_post_meta( $post_id, $key, true );
    if ( $value !== '' && $value !== false ) return $value;
    $defaults = mg_home_defaults();
    return $defaults[ $key ] ?? '';
}


/* =============================================================================
   RENDERS
============================================================================= */

// ── HERO ──────────────────────────────────────────────────────────────────────
function mg_home_hero_render( $post ) {
    wp_nonce_field( 'mg_home_save', 'mg_home_nonce' );
    $d = mg_home_defaults();
    ?>
    <table class="form-table">
        <tr>
            <th style="width:180px;"><label for="mg_home_hero_bajada">Bajada</label></th>
            <td>
                <textarea id="mg_home_hero_bajada" name="mg_home_hero_bajada" rows="3" class="widefat"><?php
                    echo esc_textarea( mg_home_get( $post->ID, 'mg_home_hero_bajada' ) );
                ?></textarea>
                <p class="description">Párrafo bajo el H1. Default: <em><?php echo esc_html( $d['mg_home_hero_bajada'] ); ?></em></p>
            </td>
        </tr>
        <tr>
            <th><label for="mg_home_hero_cta_texto">Texto del botón CTA</label></th>
            <td>
                <input type="text" id="mg_home_hero_cta_texto" name="mg_home_hero_cta_texto"
                       value="<?php echo esc_attr( mg_home_get( $post->ID, 'mg_home_hero_cta_texto' ) ); ?>"
                       class="widefat">
                <p class="description">Default: <em><?php echo esc_html( $d['mg_home_hero_cta_texto'] ); ?></em></p>
            </td>
        </tr>
    </table>
    <?php
}

// ── FEATURES ──────────────────────────────────────────────────────────────────
function mg_home_features_render( $post ) {
    $d = mg_home_defaults();

    $features = [
        1 => [ 'label' => 'Feature 1 — Robin Hood',  'bg' => '#f5f7ff' ],
        2 => [ 'label' => 'Feature 2 — Inteligente', 'bg' => '#f5fff8' ],
        3 => [ 'label' => 'Feature 3 — Flexible',    'bg' => '#fffaf5' ],
    ];

    foreach ( $features as $n => $meta ) :
        $btn_mostrar = mg_home_get( $post->ID, "mg_home_feature{$n}_btn_mostrar" );
    ?>
    <div style="background:<?php echo $meta['bg']; ?>; border-radius:6px; padding:16px 18px; margin-bottom:18px;">
        <strong style="display:block; margin-bottom:12px; font-size:12px; text-transform:uppercase; letter-spacing:.06em; color:#555;">
            <?php echo esc_html( $meta['label'] ); ?>
        </strong>
        <table class="form-table" style="margin:0;">
            <tr>
                <th style="width:180px;"><label for="mg_home_feature<?php echo $n; ?>_nombre">Etiqueta (H3)</label></th>
                <td>
                    <input type="text"
                           id="mg_home_feature<?php echo $n; ?>_nombre"
                           name="mg_home_feature<?php echo $n; ?>_nombre"
                           value="<?php echo esc_attr( mg_home_get( $post->ID, "mg_home_feature{$n}_nombre" ) ); ?>"
                           class="widefat">
                    <p class="description">Default: <em><?php echo esc_html( $d["mg_home_feature{$n}_nombre"] ); ?></em></p>
                </td>
            </tr>
            <tr>
                <th><label for="mg_home_feature<?php echo $n; ?>_titulo">Título (H4)</label></th>
                <td>
                    <textarea id="mg_home_feature<?php echo $n; ?>_titulo"
                              name="mg_home_feature<?php echo $n; ?>_titulo"
                              rows="2" class="widefat"><?php
                        echo esc_textarea( mg_home_get( $post->ID, "mg_home_feature{$n}_titulo" ) );
                    ?></textarea>
                    <p class="description">Default: <em><?php echo esc_html( $d["mg_home_feature{$n}_titulo"] ); ?></em></p>
                </td>
            </tr>
            <tr>
                <th><label for="mg_home_feature<?php echo $n; ?>_parrafo">Párrafo</label></th>
                <td>
                    <textarea id="mg_home_feature<?php echo $n; ?>_parrafo"
                              name="mg_home_feature<?php echo $n; ?>_parrafo"
                              rows="4" class="widefat"><?php
                        echo esc_textarea( mg_home_get( $post->ID, "mg_home_feature{$n}_parrafo" ) );
                    ?></textarea>
                    <p class="description">Default: <em><?php echo esc_html( $d["mg_home_feature{$n}_parrafo"] ); ?></em></p>
                </td>
            </tr>

            <!-- Separador visual antes del bloque de botón -->
            <tr>
                <td colspan="2"><hr style="border:none; border-top:1px solid #ddd; margin:8px 0 4px;"></td>
            </tr>

            <tr>
                <th><label for="mg_home_feature<?php echo $n; ?>_btn_mostrar">Botón CTA</label></th>
                <td>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox"
                               id="mg_home_feature<?php echo $n; ?>_btn_mostrar"
                               name="mg_home_feature<?php echo $n; ?>_btn_mostrar"
                               value="1"
                               <?php checked( '1', $btn_mostrar ); ?>>
                        Mostrar botón en esta feature
                    </label>
                </td>
            </tr>
            <tr>
                <th><label for="mg_home_feature<?php echo $n; ?>_btn_texto">Texto del botón</label></th>
                <td>
                    <input type="text"
                           id="mg_home_feature<?php echo $n; ?>_btn_texto"
                           name="mg_home_feature<?php echo $n; ?>_btn_texto"
                           value="<?php echo esc_attr( mg_home_get( $post->ID, "mg_home_feature{$n}_btn_texto" ) ); ?>"
                           class="widefat">
                    <p class="description">Default: <em><?php echo esc_html( $d["mg_home_feature{$n}_btn_texto"] ); ?></em></p>
                </td>
            </tr>
            <tr>
                <th><label for="mg_home_feature<?php echo $n; ?>_btn_link">Link del botón</label></th>
                <td>
                    <input type="text"
                           id="mg_home_feature<?php echo $n; ?>_btn_link"
                           name="mg_home_feature<?php echo $n; ?>_btn_link"
                           value="<?php echo esc_attr( mg_home_get( $post->ID, "mg_home_feature{$n}_btn_link" ) ); ?>"
                           class="widefat">
                    <p class="description">URL relativa o absoluta. Default: <em><?php echo esc_html( $d["mg_home_feature{$n}_btn_link"] ); ?></em></p>
                </td>
            </tr>
        </table>
    </div>
    <?php endforeach;
}

// ── CASOS DE ÉXITO ────────────────────────────────────────────────────────────
function mg_home_casos_render( $post ) {
    $d = mg_home_defaults();
    ?>
    <table class="form-table">
        <tr>
            <th style="width:180px;"><label for="mg_home_casos_label">Label pequeño</label></th>
            <td>
                <input type="text" id="mg_home_casos_label" name="mg_home_casos_label"
                       value="<?php echo esc_attr( mg_home_get( $post->ID, 'mg_home_casos_label' ) ); ?>"
                       class="widefat">
                <p class="description">Etiqueta sobre el título. Default: <em><?php echo esc_html( $d['mg_home_casos_label'] ); ?></em></p>
            </td>
        </tr>
        <tr>
            <th><label for="mg_home_casos_titulo">Título (H2)</label></th>
            <td>
                <textarea id="mg_home_casos_titulo" name="mg_home_casos_titulo" rows="2" class="widefat"><?php
                    echo esc_textarea( mg_home_get( $post->ID, 'mg_home_casos_titulo' ) );
                ?></textarea>
                <p class="description">Default: <em><?php echo esc_html( $d['mg_home_casos_titulo'] ); ?></em></p>
            </td>
        </tr>
    </table>
    <?php
}

// ── FORMULARIO ────────────────────────────────────────────────────────────────
function mg_home_formulario_render( $post ) {
    $d = mg_home_defaults();
    ?>
    <table class="form-table">
        <tr>
            <th style="width:180px;"><label for="mg_home_form_titulo_linea1">Título — Línea 1</label></th>
            <td>
                <input type="text" id="mg_home_form_titulo_linea1" name="mg_home_form_titulo_linea1"
                       value="<?php echo esc_attr( mg_home_get( $post->ID, 'mg_home_form_titulo_linea1' ) ); ?>"
                       class="widefat">
                <p class="description">Default: <em><?php echo esc_html( $d['mg_home_form_titulo_linea1'] ); ?></em></p>
            </td>
        </tr>
        <tr>
            <th><label for="mg_home_form_titulo_linea2">Título — Línea 2</label></th>
            <td>
                <input type="text" id="mg_home_form_titulo_linea2" name="mg_home_form_titulo_linea2"
                       value="<?php echo esc_attr( mg_home_get( $post->ID, 'mg_home_form_titulo_linea2' ) ); ?>"
                       class="widefat">
                <p class="description">Default: <em><?php echo esc_html( $d['mg_home_form_titulo_linea2'] ); ?></em></p>
            </td>
        </tr>
    </table>
    <?php
}


/* =============================================================================
   GUARDADO
============================================================================= */

add_action( 'save_post_page', 'mg_home_save_metaboxes' );

function mg_home_save_metaboxes( $post_id ) {
    if ( ! isset( $_POST['mg_home_nonce'] ) )                           return;
    if ( ! wp_verify_nonce( $_POST['mg_home_nonce'], 'mg_home_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )                return;
    if ( ! current_user_can( 'edit_page', $post_id ) )                  return;

    $template = get_post_meta( $post_id, '_wp_page_template', true );
    if ( $template !== 'page-home.php' )                                return;

    // Campos de texto
    $text_fields = [
        'mg_home_hero_bajada',
        'mg_home_hero_cta_texto',
        'mg_home_feature1_nombre',
        'mg_home_feature1_titulo',
        'mg_home_feature1_parrafo',
        'mg_home_feature1_btn_texto',
        'mg_home_feature1_btn_link',
        'mg_home_feature2_nombre',
        'mg_home_feature2_titulo',
        'mg_home_feature2_parrafo',
        'mg_home_feature2_btn_texto',
        'mg_home_feature2_btn_link',
        'mg_home_feature3_nombre',
        'mg_home_feature3_titulo',
        'mg_home_feature3_parrafo',
        'mg_home_feature3_btn_texto',
        'mg_home_feature3_btn_link',
        'mg_home_casos_label',
        'mg_home_casos_titulo',
        'mg_home_form_titulo_linea1',
        'mg_home_form_titulo_linea2',
    ];

    foreach ( $text_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta(
                $post_id,
                $field,
                sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) )
            );
        }
    }

    // Checkboxes — guardar explícitamente '0' cuando no vienen en el POST
    $checkbox_fields = [
        'mg_home_feature1_btn_mostrar',
        'mg_home_feature2_btn_mostrar',
        'mg_home_feature3_btn_mostrar',
    ];

    foreach ( $checkbox_fields as $field ) {
        update_post_meta( $post_id, $field, isset( $_POST[ $field ] ) ? '1' : '0' );
    }
}
