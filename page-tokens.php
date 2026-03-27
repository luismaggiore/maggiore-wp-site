<?php
/**
 * Template Name: Tokens
 *
 * Sistema de Tokens — Maggiore Marketing
 *
 * @package Maggiore
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════════════════════════════════════════════
   SEO — JSON-LD Schemas
   Se inyectan en <head> vía wp_head antes de que se llame get_header().
   • Service        → describe el Sistema de Tokens como servicio
   • FAQPage        → genera rich results de preguntas frecuentes en Google
   • ItemList       → catálogo de entregables con precio en tokens
   • BreadcrumbList → ruta de navegación para el Knowledge Panel
══════════════════════════════════════════════════════════════════════════════ */
add_action( 'wp_head', function () {

    $page_url    = get_permalink();
    $org_url     = home_url( '/' );
    $org_name    = 'Maggiore Marketing';
    $logo_url    = get_template_directory_uri() . '/assets/img/logo-mm.svg';

    /* ── 1. Service ── */
    $schema_service = [
        '@context'            => 'https://schema.org',
        '@type'               => 'Service',
        '@id'                 => $page_url . '#service',
        'name'                => 'Sistema de Tokens Maggiore Marketing',
        'alternateName'       => 'Token System',
        'description'         => 'Modelo de trabajo flexible basado en tokens que reemplaza las cuotas fijas. Cada token equivale a una unidad estandarizada de esfuerzo en marketing digital. Los tokens son válidos por 12 meses, acumulables y aplicables a cualquier servicio del catálogo.',
        'url'                 => $page_url,
        'serviceType'         => 'Marketing Digital',
        'category'            => 'Marketing Agency Service',
        'provider'            => [
            '@type' => 'Organization',
            '@id'   => $org_url . '#organization',
            'name'  => $org_name,
            'url'   => $org_url,
            'logo'  => [ '@type' => 'ImageObject', 'url' => $logo_url ],
            'address' => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => 'Alcántara 1791',
                'addressLocality' => 'Las Condes',
                'addressRegion'   => 'Región Metropolitana',
                'addressCountry'  => 'CL',
            ],
        ],
        'offers' => [
            '@type'         => 'Offer',
            'priceCurrency' => 'USD',
            'price'         => '50.00',
            'description'   => 'Precio por token desde USD 50. Descuento por volumen acumulado de hasta 16.7%. Sin contratos de permanencia.',
            'eligibleRegion' => [
                [ '@type' => 'Place', 'name' => 'Chile' ],
                [ '@type' => 'Place', 'name' => 'América Latina' ],
            ],
        ],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name'  => 'Catálogo de entregables en tokens',
            'url'   => $page_url . '#tk-catalogo',
        ],
        'termsOfService' => 'Tokens válidos 12 meses desde compra. Sin mínimo de compra. Sin contratos de permanencia. Las revisiones están incluidas.',
    ];

    /* ── 2. FAQPage ── */
    $faqs_data = [
        [ '¿Qué es un token?',                                    'Un token es la unidad estandarizada de esfuerzo de Maggiore Marketing: el trabajo coordinado mínimo necesario para entregar un entregable básico de marketing digital, como una publicación estática en redes sociales.' ],
        [ '¿Hay un mínimo de tokens para empezar?',               'No. No hay una cantidad mínima de tokens que debas comprar para empezar a trabajar con Maggiore Marketing.' ],
        [ '¿Qué pasa si en un mes no se entregan los insumos?',   'Tus tokens se acumulan para el mes siguiente. A diferencia de una cuota fija mensual, aquí solo se gastan tokens cuando hay una entrega concreta. Si surge un imprevisto, no pierdes nada.' ],
        [ '¿Puedo cambiar las entregas de un mes a otro?',         'Sí, tienes flexibilidad total. Cada mes puedes decidir, junto con Maggiore, en qué invertir tus tokens según lo que tu negocio necesite en ese momento.' ],
        [ '¿Los tokens tienen fecha de vencimiento?',              'Sí, los tokens son válidos por 12 meses desde la fecha de compra. Si compras un monto igual o mayor a tu saldo actual, la vigencia se renueva por 12 meses completos.' ],
        [ '¿Las revisiones cuestan tokens adicionales?',           'No. Las revisiones están incluidas en el costo original del entregable. No hay límite de rondas de revisión. Maggiore opera con una garantía de satisfacción total.' ],
        [ '¿El presupuesto de pauta publicitaria está incluido?',  'No. El presupuesto de pauta (Meta Ads, Google Ads, etc.) se maneja por separado de los tokens. En general, se carga directamente a la tarjeta de crédito del cliente.' ],
        [ '¿Puedo mezclar servicios creativos con inteligencia social?', 'Sí. El sistema de tokens permite mezclar libremente servicios de contenido creativo, análisis de inteligencia social y experimentos iterativos.' ],
        [ '¿Hay contratos de permanencia?',                        'No. La fidelidad de los clientes se premia con mejores precios por volumen acumulado, no con amarres contractuales.' ],
        [ '¿Cómo sé en qué se han gastado mis tokens?',            'Desde la plataforma maggiore.app puedes ver tu saldo de tokens, cada entregable solicitado, quién lo solicitó, la fecha y el costo en tokens.' ],
    ];

    $faq_entities = array_map( function ( $faq ) {
        return [
            '@type'          => 'Question',
            'name'           => $faq[0],
            'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $faq[1] ],
        ];
    }, $faqs_data );

    $schema_faq = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        '@id'        => $page_url . '#faqpage',
        'name'       => 'Preguntas Frecuentes — Sistema de Tokens Maggiore Marketing',
        'url'        => $page_url . '#tk-faq',
        'mainEntity' => $faq_entities,
    ];

    /* ── 3. ItemList — catálogo de entregables ── */
    $catalog_items = [
        [ 'Post estático para redes sociales',    'Contenido', 1  ],
        [ 'Carrusel (5 láminas)',                 'Contenido', 3  ],
        [ 'Video 0–60s grabado por cliente',      'Contenido', 3  ],
        [ 'Video 0–60s grabado por Maggiore',     'Contenido', 6  ],
        [ 'Video 0–60s Motion Graphic',           'Contenido', 9  ],
        [ 'Blog 800–1,200 palabras',              'Contenido', 2  ],
        [ 'Email Marketing (redacción + HTML)',   'Contenido', 4  ],
        [ 'Estrategia de contenido mensual',      'Contenido', 5  ],
        [ 'Logo (creación desde cero)',           'Branding',  10 ],
        [ 'Manual de marca',                      'Branding',  8  ],
        [ 'Página web básica (1 página)',         'Web',       10 ],
        [ 'Página web media (2–5 páginas)',       'Web',       20 ],
        [ 'Página web compleja (5–20 páginas)',   'Web',       50 ],
        [ 'Lanzamiento de campaña publicitaria',  'Medios',    2  ],
        [ 'Reporte completo (Orgánico+Meta+Google)', 'Reportes', 4 ],
        [ 'Workshop 2h con capacitación',         'Training',  4  ],
        [ 'Análisis de Inteligencia Social (básico)', 'Inteligencia Social', 100 ],
        [ 'Experimento Stopping Power (básico)',  'Experimentos', 100 ],
    ];

    $list_items = array_map( function ( $item, $pos ) use ( $page_url ) {
        return [
            '@type'    => 'ListItem',
            'position' => $pos + 1,
            'item'     => [
                '@type'       => 'Offer',
                'name'        => $item[0],
                'category'    => $item[1],
                'description' => $item[1] . ' — costo: ' . $item[2] . ' token' . ( $item[2] > 1 ? 's' : '' ),
                'priceCurrency' => 'TOKEN',
                'price'       => (string) $item[2],
                'url'         => $page_url . '#tk-catalogo',
            ],
        ];
    }, $catalog_items, array_keys( $catalog_items ) );

    $schema_catalog = [
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        '@id'             => $page_url . '#catalog',
        'name'            => 'Catálogo de servicios de marketing digital en tokens — Maggiore Marketing',
        'description'     => 'Lista de entregables de marketing digital con su costo expresado en tokens. Incluye contenido, branding, web, medios, reportes, inteligencia social y experimentos.',
        'numberOfItems'   => count( $list_items ),
        'itemListElement' => $list_items,
    ];

    /* ── 4. BreadcrumbList ── */
    $schema_breadcrumb = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [
            [ '@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio',            'item' => $org_url ],
            [ '@type' => 'ListItem', 'position' => 2, 'name' => 'Sistema de Tokens', 'item' => $page_url ],
        ],
    ];

    /* ── Output ── */
    $schemas = [ $schema_service, $schema_faq, $schema_catalog, $schema_breadcrumb ];
    foreach ( $schemas as $schema ) {
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
    }

}, 5 ); // priority 5 — antes del SEO plugin para no colisionar

get_header();
?>

<style>
/* ══════════════════════════════════════════════════════════════════════════════
   PAGE TOKENS
   · Secciones transparentes — la aurora se ve a través de todo
   · Cards usan .card-mg (sus animaciones GSAP ya vienen de main.js)
   · Botones siguen exactamente la estructura de page-home.php
══════════════════════════════════════════════════════════════════════════════ */

/* ── Layout secciones ─────────────────────────────────────────────────────── */
.tk-section {
    padding: 90px 0;
    background: transparent;
    position: relative;
    z-index: 1;
}

/* ── Hero ─────────────────────────────────────────────────────────────────── */
.tk-hero {
    min-height: 90vh;
    display: flex;
    align-items: center;
    padding-top: clamp(120px, 18vh, 18vh);
    padding-bottom: 80px;
    background: transparent;
    position: relative;
    z-index: 1;
}
.tk-hero h1 {
    font-size: var(--font-size-hero);
    line-height: var(--line-height-hero);
    font-weight: 400;
    font-family: var(--font-display);
    letter-spacing: 0.03em;
    color: #fff;
    margin-bottom: 24px;
}
.tk-hero h1 em {  color: var(--secondary-color); }

/* ── Labels de sección ────────────────────────────────────────────────────── */
.tk-label {
    font-variant: all-small-caps;
    letter-spacing: 0.28em;
    font-size: 18px;
    color: var(--secondary-color);
    margin-bottom: 14px;
    font-weight: 500;
}
.tk-title {
    font-size: var(--font-size-big);
    font-family: var(--font-display);
    font-weight: 400;
    letter-spacing: 0.04em;
    color: #fff;
    margin-bottom: 18px;
    line-height: 1.1;
}
.tk-sub {
    font-size: 17px;
    color: var(--text-secondary);
    font-weight: 300;
    max-width: 600px;
    line-height: 1.75;
}

/* ── Hero card lateral ────────────────────────────────────────────────────── */
.tk-hero-card {
    border: var(--border-container);
    border-radius: var(--border-radius);
    background-color: var(--background-container);
    overflow: hidden;
    position: relative;
}
.tk-hero-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
}
.tk-hero-card-header {
    padding: 26px 28px 20px;
}
.tk-hero-card-def {
    font-size: 18px;
    color: var(--text-secondary);
    line-height: 1.25;
    font-weight: 300;
    margin: 0;
}
.tk-hero-card table { width: 100%; border-collapse: collapse; font-size: 14px; }
.tk-hero-card table th {
    font-variant: all-small-caps; letter-spacing: 0.15em;
    font-size: 11px; color: var(--text-secondary);
    padding: 12px 28px 8px; border-bottom: var(--border-container);
    text-align: left; opacity: 0.6; font-weight: 500;
}
.tk-hero-card table td {
    padding: 10px 28px; border-bottom: var(--border-container);
    color: var(--text-secondary); font-weight: 300;
}
.tk-hero-card table tr:last-child td { border-bottom: none; }
.tk-hero-card table td:last-child {
    text-align: right; font-weight: 700;
    color: var(--secondary-color);
}
.tk-hero-card-footer {
    padding: 14px 28px; font-size: 12px;
    color: var(--text-secondary); opacity: 0.45;
    text-align: center; border-top: var(--border-container);
    letter-spacing: 0.05em; font-weight: 300;
}

/* ── Blockquote ───────────────────────────────────────────────────────────── */
.tk-quote-wrap {
    padding: 60px 0;
    background: transparent;
    position: relative; z-index: 1;
}
.tk-blockquote {
    font-size: clamp(20px, 2.5vw, 28px);
    font-family: var(--font-display);
 font-weight: 300;
    color: #fff; line-height: 1.5;
    max-width: 760px; margin: 0 auto;
    text-align: center; letter-spacing: 0.02em;
}
.tk-blockquote em { color: var(--secondary-color); }

/* ── Step cards ───────────────────────────────────────────────────────────── */
.tk-step-num {
    font-size: clamp(48px, 5vw, 68px);
    font-family: var(--font-display);
    font-weight: 400;
    color: var(--secondary-color);
    opacity: 0.6; line-height: 1;
    margin-bottom: 20px;
}
.tk-step-title {
    font-size: 18px; font-weight: 500;
    color: #fff; margin-bottom: 10px; letter-spacing: 0.02em;
}
.tk-step-body {
    font-size: 14px; color: var(--text-secondary);
    line-height: 1.7; font-weight: 300; margin: 0;
}

/* ── Compare ──────────────────────────────────────────────────────────────── */
.tk-compare-card h3 {
    font-size: 22px; font-family: var(--font-display);
    font-weight: 400; letter-spacing: 0.03em;
    color: #fff; margin-bottom: 6px;
}
.tk-compare-sub {
    font-size: 13px; color: var(--text-secondary);
    opacity: 0.5; margin-bottom: 24px; font-weight: 300;
}
.tk-compare-item {
    display: flex; gap: 14px; align-items: flex-start;
    padding: 13px 0; border-bottom: var(--border-container);
    font-size: 14px; line-height: 1.65;
    color: var(--text-secondary); font-weight: 300;
}
.tk-compare-item:last-child { border-bottom: none; }
.tk-compare-icon {
    width: 26px; height: 26px; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; flex-shrink: 0; margin-top: 1px;
}
.tk-icon-neg { background: rgba(234,24,9,.1); color: #ea1809; }
.tk-icon-pos { background: rgba(0,144,155,.12); color: var(--secondary-color); }
.tk-compare-badge {
    display: inline-block;
    font-variant: all-small-caps; letter-spacing: 0.18em;
    font-size: 11px; font-weight: 600;
    padding: 4px 14px; border-radius: 999px; margin-bottom: 18px;
}
.tk-badge-neg { background: rgba(234,24,9,.08); color: #ea1809; }
.tk-badge-pos { background: rgba(0,144,155,.08); color: var(--secondary-color); }
/* Card del modelo Maggiore con borde accent */
.tk-card-highlight { border-color: var(--secondary-color) !important; }

/* ── Calculadora ──────────────────────────────────────────────────────────── */
.tk-calc-label {
    font-size: 12px; font-variant: all-small-caps;
    letter-spacing: 0.2em; color: var(--text-secondary);
    font-weight: 500; display: block; margin-bottom: 8px; opacity: 0.75;
}
.tk-calc-input {
    width: 100%; padding: 13px 16px;
    background-color: var(--inner-background) !important;
    border: var(--border-container) !important;
    border-radius: var(--border-radius) !important;
    color: #fff !important;
    font-size: 16px; font-family: var(--font-family);
    transition: border-color .2s;
}
.tk-calc-input:focus {
    outline: none !important;
    border-color: var(--secondary-color) !important;
    box-shadow: 0 0 0 3px rgba(0,144,155,.15) !important;
}
/* Quita las flechas del input number */
.tk-calc-input::-webkit-outer-spin-button,
.tk-calc-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.tk-calc-input[type=number] { -moz-appearance: textfield; }

.tk-result-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 11px 0; border-bottom: var(--border-container);
}
.tk-result-row:last-child { border-bottom: none; }
.tk-result-label { font-size: 13px; color: var(--text-secondary); font-weight: 300; }
.tk-result-val   { font-size: 15px; font-weight: 600; color: #fff; }
.tk-result-val.accent { color: var(--secondary-color); font-size: 19px; }
.tk-xnote {
    font-size: 12px; color: var(--text-secondary);
    display: flex; align-items: center; gap: 8px;
    margin-top: 14px; opacity: 0.6;
}
.tk-xdot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; background: #e8aa00; }

/* ── Token badge ──────────────────────────────────────────────────────────── */
.tk-badge {
    display: inline-flex; align-items: center; justify-content: center;
    border: 1px solid var(--secondary-color);
    color: var(--secondary-color); font-weight: 700;
    font-size: 12px; min-width: 34px;
    padding: 3px 10px; border-radius: 999px;
}

/* ── Category tag ─────────────────────────────────────────────────────────── */
.tk-cat-pill {
    font-variant: all-small-caps; letter-spacing: 0.1em;
    font-size: 11px; font-weight: 600;
    padding: 3px 10px; border-radius: 999px;
    border: var(--border-container);
    background-color: var(--inner-background);
    color: var(--text-secondary); white-space: nowrap;
}

/* ── Filter buttons ───────────────────────────────────────────────────────── */
.tk-filters {
    display: flex; gap: 8px; flex-wrap: wrap; margin: 36px 0 20px;
}
.tk-filter-btn {
    padding: 7px 18px; border-radius: 999px;
    border: var(--border-container); background: var(--inner-background);
    font-size: 13px; font-weight: 400; cursor: pointer; transition: all .2s;
    color: var(--text-secondary); font-family: var(--font-family);
    font-variant: all-small-caps; letter-spacing: 0.12em;
}
.tk-filter-btn:hover  { border-color: var(--secondary-color); color: var(--secondary-color); }
.tk-filter-btn.active { background: var(--secondary-color); border-color: var(--secondary-color); color: #fff; font-weight: 600; }

/* ── Catalog table ────────────────────────────────────────────────────────── */
.tk-table-wrap {
    border: var(--border-container);
    border-radius: var(--border-radius);
    overflow: hidden;
    background: var(--background-container);
}
.tk-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.tk-table thead th {
    background: var(--inner-background); color: var(--secondary-color);
    padding: 13px 20px; text-align: left; font-weight: 500;
    font-size: 12px; letter-spacing: 0.15em; font-variant: all-small-caps;
    border-bottom: var(--border-container);
}
.tk-table tbody td {
    padding: 12px 20px; border-bottom: var(--border-container);
    color: var(--text-secondary); font-weight: 300;
}
.tk-table tbody tr:last-child td { border-bottom: none; }
.tk-table tbody tr:hover { background: var(--background-hover); transition: background .15s; }

/* ── FAQ ──────────────────────────────────────────────────────────────────── */
.tk-faq-item { border-bottom: var(--border-container); }
.tk-faq-btn {
    width: 100%; display: flex; justify-content: space-between; align-items: center;
    padding: 22px 0; background: none; border: none; cursor: pointer;
    text-align: left; font-family: var(--font-family);
    font-size: 16px; font-weight: 400; color: #fff;
    transition: color .2s; gap: 16px; letter-spacing: 0.03em;
}
.tk-faq-btn:hover { color: var(--secondary-color); }
.tk-faq-icon {
    width: 28px; height: 28px; border-radius: 6px;
    background: var(--inner-background); border: var(--border-container);
    color: var(--secondary-color);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0; transition: all .3s; line-height: 1;
}
.tk-faq-item.open .tk-faq-icon {
    background: var(--secondary-color); border-color: var(--secondary-color);
    color: #fff; transform: rotate(45deg);
}
.tk-faq-body { max-height: 0; overflow: hidden; transition: max-height .35s ease; }
.tk-faq-item.open .tk-faq-body { max-height: 400px; }
.tk-faq-inner {
    padding: 0 0 22px; font-size: 14px;
    color: var(--text-secondary); line-height: 1.8; font-weight: 300;
}

/* ── CTA ──────────────────────────────────────────────────────────────────── */
.tk-cta {
    padding: 100px 0; text-align: center;
    background: transparent; position: relative; z-index: 1;
}
.tk-cta h2 {
    font-size: clamp(28px, 4vw, 52px); font-family: var(--font-display);
    font-weight: 400; letter-spacing: 0.04em; color: #fff; margin-bottom: 16px;
}
.tk-cta p {
    font-size: 17px; color: var(--text-secondary);
    font-weight: 300; max-width: 500px;
    margin: 0 auto 40px; line-height: 1.7;
}

/* ── Responsive ───────────────────────────────────────────────────────────── */
@media (max-width: 991px) {
    .tk-section { padding: 60px 0; }
    .tk-hero    { min-height: auto; }
}
@media (max-width: 768px) {
    .tk-filters { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 6px; -webkit-overflow-scrolling: touch; }
    .tk-filter-btn { flex-shrink: 0; }
    .tk-table thead th, .tk-table tbody td { padding: 10px 12px; font-size: 12px; }
}

/* ── Print ────────────────────────────────────────────────────────────────── */
.tk-print-header {
    display: none; justify-content: space-between; align-items: center;
    padding: 0 0 16px; margin-bottom: 16px;
    border-bottom: 2px solid var(--secondary-color);
}
@media print {
    .tk-print-header { display: flex !important; }
    .tk-hero, .tk-quote-wrap, #tk-comparacion, #tk-faq, .tk-cta, .tk-filters { display: none !important; }
}
</style>

<div class="tk-print-header">
    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-mm.svg' ); ?>"
         alt="Maggiore Marketing" style="height:32px">
    <div style="text-align:right;font-size:11px;color:#666">
        MAGGIORE MARKETING, S.P.A. · Alcántara 1791, Las Condes · Santiago
    </div>
</div>

<main style="position:relative;z-index:1">

    <?php /* ═══ HERO ════════════════════════════════════════════════════════ */ ?>
    <section class="tk-hero" aria-label="<?php esc_attr_e( 'Sistema de Tokens Maggiore Marketing', 'maggiore' ); ?>">
        <div class="container-fluid">
            <div class="row align-items-center g-5">

                <div class="col-lg-6">
                    <div class="feature-name mb-4" aria-hidden="true">
                        <h3><?php _e( 'Modelo exclusivo de Maggiore', 'maggiore' ); ?></h3>
                    </div>

                    <h1 class="bajada-reveal">
                        <?php _e( 'Llevamos la <em>flexibilidad</em> al máximo', 'maggiore' ); ?>
                    </h1>

                    <div class="bajada-reveal">
                        <p class="bajada">
                            <?php _e( 'Nuestro Sistema de Tokens reemplaza las cuotas fijas y los contratos rígidos. Solo pagas por lo que realmente usas, con total flexibilidad para decidir en qué invertir cada mes.', 'maggiore' ); ?>
                        </p>

                        <div class="d-flex gap-4 flex-wrap align-items-center">
                            <?php /* Botón principal — estructura exacta de page-home.php */ ?>
                            <div class="mg-link">
                                <a class="btns-mgr tk-scroll-btn" data-target="#tk-como-funciona">
                                    <div class="btn-brillo"></div>
                                    <div class="btn-container">
                                        <div class="btn-content">
                                            <span class="btn-text"><?php _e( 'Cómo funciona', 'maggiore' ); ?></span>
                                            <span class="btn-text-2"><?php _e( 'Cómo funciona', 'maggiore' ); ?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <?php /* Enlace secundario — scroll suave también */ ?>
                            <a class="tk-scroll-btn case-link" data-target="#tk-catalogo"
                               style="font-size:15px;letter-spacing:0.05em;color:var(--secondary-color)">
                                <?php _e( 'Ver catálogo →', 'maggiore' ); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 offset-lg-1">
                    <div class="tk-hero-card">
                        <div class="tk-hero-card-header">
                            <div class="tk-label mb-2"><?php _e( '1 Token = 1 unidad de esfuerzo', 'maggiore' ); ?></div>
                            <p class="tk-hero-card-def"><?php _e( 'El trabajo coordinado mínimo para entregar un entregable básico de marketing digital.', 'maggiore' ); ?></p>
                        </div>
                        <table aria-label="<?php esc_attr_e( 'Ejemplos de costo en tokens por entregable', 'maggiore' ); ?>">
                            <caption class="visually-hidden"><?php _e( 'Ejemplos de entregables y su costo en tokens', 'maggiore' ); ?></caption>
                            <thead><tr>
                                <th><?php _e( 'Entregable', 'maggiore' ); ?></th>
                                <th style="text-align:right"><?php _e( 'Tokens', 'maggiore' ); ?></th>
                            </tr></thead>
                            <tbody>
                                <tr><td><?php _e( 'Post estático para redes', 'maggiore' ); ?></td><td>1</td></tr>
                                <tr><td><?php _e( 'Carrusel de 5 láminas', 'maggiore' ); ?></td><td>3</td></tr>
                                <tr><td><?php _e( 'Video 0–60s (stock)', 'maggiore' ); ?></td><td>3</td></tr>
                                <tr><td><?php _e( 'Página web básica', 'maggiore' ); ?></td><td>10</td></tr>
                                <tr><td><?php _e( 'Estudio de inteligencia social', 'maggiore' ); ?></td><td>100</td></tr>
                            </tbody>
                        </table>
                        <div class="tk-hero-card-footer">
                            <?php _e( 'Sin contratos · Válidos por 12 meses · Intercambiables entre servicios', 'maggiore' ); ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php /* ═══ BLOCKQUOTE ══════════════════════════════════════════════════ */ ?>
    <div class="tk-quote-wrap">
        <div class="container-fluid">
            <blockquote class="tk-blockquote">
                <?php _e( 'El marketing digital cambia constantemente. Estar amarrado a un fee fijo con los mismos entregables cada mes <em>no es estratégico</em>.', 'maggiore' ); ?>
            </blockquote>
        </div>
    </div>

    <?php /* ═══ CÓMO FUNCIONA ═══════════════════════════════════════════════ */ ?>
    <section class="tk-section  " id="tk-como-funciona" aria-labelledby="tk-h2-como-funciona">
        <div class="container-fluid">
            <div class="tk-label" aria-hidden="true"><?php _e( '¿Cómo funciona?', 'maggiore' ); ?></div>
            <div class="row align-items-end mb-5 g-4">
                <div class="col-lg-5">
                    <h2 class="tk-title mb-0" id="tk-h2-como-funciona"><?php _e( 'Marketing en cuatro pasos simples', 'maggiore' ); ?></h2>
                </div>
                <div class="col-lg-5 offset-lg-2">
                    <p class="tk-sub mb-0"><?php _e( 'El token es nuestra unidad estandarizada de esfuerzo: el trabajo coordinado mínimo para entregar un entregable básico de marketing digital.', 'maggiore' ); ?></p>
                </div>
            </div>
            <div class="row g-3" role="list" aria-label="<?php esc_attr_e( 'Pasos del sistema de tokens', 'maggiore' ); ?>">
                <?php
                $steps = [
                    ['1', __( 'Adquiere tokens',        'maggiore' ), __( 'Compra la cantidad de tokens que necesites. No hay mínimo de compra.', 'maggiore' )],
                    ['2', __( 'Elige tus entregables',  'maggiore' ), __( 'Cada servicio tiene un costo claro. Desde un post estático (1 token) hasta un proyecto de inteligencia social (100+ tokens).', 'maggiore' )],
                    ['3', __( 'Ajusta sobre la marcha', 'maggiore' ), __( 'Si algo funciona mejor, mueve tu inversión hacia allá. Si en un mes surge un imprevisto, tus tokens se acumulan.', 'maggiore' )],
                    ['4', __( 'Mide y optimiza',        'maggiore' ), __( 'Cada entrega tiene un valor claro, lo que facilita calcular el ROI por actividad e invertir más en lo que genera resultados.', 'maggiore' )],
                ];
                foreach ( $steps as $step ) :
                ?>
                <div class="col-md-6 col-xl-3" role="listitem">
                    <div class="card-mg h-100">
                        <div class="tk-step-num" aria-hidden="true"><?php echo esc_html( $step[0] ); ?></div>
                        <h3 class="tk-step-title"><?php echo esc_html( $step[1] ); ?></h3>
                        <p class="tk-step-body"><?php echo esc_html( $step[2] ); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ═══ COMPARACIÓN ═════════════════════════════════════════════════ */ ?>
    <section class="tk-section  " id="tk-comparacion" aria-labelledby="tk-h2-comparacion">
        <div class="container-fluid">
            <div class="tk-label" aria-hidden="true"><?php _e( '¿Por qué tokens?', 'maggiore' ); ?></div>
            <h2 class="tk-title mb-5" id="tk-h2-comparacion"><?php _e( 'Uno de nuestros grandes diferenciales', 'maggiore' ); ?></h2>
            <div class="row g-3">

                <div class="col-lg-6">
                    <div class="card-mg h-100">
                        <div class="tk-compare-badge tk-badge-neg"><?php _e( 'Modelo Tradicional', 'maggiore' ); ?></div>
                        <h3><?php _e( 'Cuota fija mensual', 'maggiore' ); ?></h3>
                        <p class="tk-compare-sub"><?php _e( 'Lo que la mayoría de las agencias ofrece hoy', 'maggiore' ); ?></p>
                        <?php
                        $cons = [
                            __( 'Pagas una cuota mensual fija, la uses o no. Si en un mes ocurre un imprevisto y no se entregan los insumos… sigues pagando el total.', 'maggiore' ),
                            __( 'Las entregas son las mismas cada mes, aunque tu negocio necesite algo diferente.', 'maggiore' ),
                            __( 'Difícil saber qué actividad genera resultados porque todo está empaquetado en un mismo monto.', 'maggiore' ),
                            __( 'Contratos de permanencia que te amarran incluso si no estás conforme.', 'maggiore' ),
                        ];
                        foreach ( $cons as $c ) : ?>
                        <div class="tk-compare-item">
                            <div class="tk-compare-icon tk-icon-neg">✕</div>
                            <div><?php echo esc_html( $c ); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-mg tk-card-highlight h-100">
                        <div class="tk-compare-badge tk-badge-pos"><?php _e( 'Maggiore Marketing', 'maggiore' ); ?></div>
                        <h3><?php _e( 'Sistema de Tokens', 'maggiore' ); ?></h3>
                        <p class="tk-compare-sub"><?php _e( 'Un modelo que diseñamos y que no encontrarás en otra agencia', 'maggiore' ); ?></p>
                        <?php
                        $pros = [
                            __( 'Tus tokens solo se gastan cuando hay una entrega concreta. Si surge un imprevisto, se acumulan para el siguiente mes.', 'maggiore' ),
                            __( 'Flexibilidad total para cambiar las entregas cada mes según lo que tu negocio necesite en ese momento.', 'maggiore' ),
                            __( 'Cada entregable tiene un costo claro, lo que permite auditar el retorno de inversión por actividad.', 'maggiore' ),
                            __( 'Sin contratos de permanencia. Tu fidelidad se premia con mejores precios, no con amarres.', 'maggiore' ),
                        ];
                        foreach ( $pros as $p ) : ?>
                        <div class="tk-compare-item">
                            <div class="tk-compare-icon tk-icon-pos">✓</div>
                            <div><?php echo esc_html( $p ); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php /* ═══ CALCULADORA ══════════════════════════════════════════════════ */ ?>
    <section class="tk-section  " id="tk-calculadora" aria-labelledby="tk-h2-calculadora">
        <div class="container-fluid">
            <div class="tk-label" aria-hidden="true"><?php _e( 'Simulador', 'maggiore' ); ?></div>
            <div class="row align-items-end mb-5 g-4">
                <div class="col-lg-5">
                    <h2 class="tk-title mb-0" id="tk-h2-calculadora"><?php _e( 'Calcula el precio de tus tokens', 'maggiore' ); ?></h2>
                </div>
                <div class="col-lg-5 offset-lg-2">
                    <p class="tk-sub mb-0"><?php _e( 'Simula cuánto pagarías según tu volumen acumulado. Tipo de cambio en tiempo real del Banco Central de Chile.', 'maggiore' ); ?></p>
                </div>
            </div>

            <div class="card-mg">
                <div class="row g-5 align-items-start">

                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="tk-calc-label"><?php _e( 'Tokens comprados históricamente', 'maggiore' ); ?></label>
                            <input type="number" class="tk-calc-input" id="tkCalcHistoric" value="0" min="0" oninput="tkCalc()">
                        </div>
                        <div>
                            <label class="tk-calc-label"><?php _e( 'Tokens que quieres comprar ahora', 'maggiore' ); ?></label>
                            <input type="number" class="tk-calc-input" id="tkCalcNew" value="10" min="1" oninput="tkCalc()">
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card-mg" style="background:var(--inner-background)">
                            <div class="tk-result-row">
                                <span class="tk-result-label"><?php _e( 'Base de cálculo (T)', 'maggiore' ); ?></span>
                                <span class="tk-result-val" id="tkResBase">10</span>
                            </div>
                            <div class="tk-result-row">
                                <span class="tk-result-label"><?php _e( 'Ajuste por volumen', 'maggiore' ); ?></span>
                                <span class="tk-result-val" id="tkResDiscount">0.03%</span>
                            </div>
                            <div class="tk-result-row">
                                <span class="tk-result-label"><?php _e( 'Precio por token (USD)', 'maggiore' ); ?></span>
                                <span class="tk-result-val accent" id="tkResPriceUSD">USD 59.98</span>
                            </div>
                            <div class="tk-result-row">
                                <span class="tk-result-label"><?php _e( 'Precio por token (CLP)', 'maggiore' ); ?></span>
                                <span class="tk-result-val accent" id="tkResPriceCLP"><?php _e( 'Cargando…', 'maggiore' ); ?></span>
                            </div>
                            <div class="tk-result-row">
                                <span class="tk-result-label"><?php _e( 'Neto (USD)', 'maggiore' ); ?></span>
                                <span class="tk-result-val" id="tkResTotalUSD">USD 599.82</span>
                            </div>
                            <div class="tk-result-row">
                                <span class="tk-result-label"><?php _e( 'Neto (CLP)', 'maggiore' ); ?></span>
                                <span class="tk-result-val" id="tkResTotalCLP"><?php _e( 'Cargando…', 'maggiore' ); ?></span>
                            </div>
                            <div class="tk-result-row">
                                <span class="tk-result-label"><?php _e( 'IVA 19% (CLP)', 'maggiore' ); ?></span>
                                <span class="tk-result-val" id="tkResIVACLP"><?php _e( 'Cargando…', 'maggiore' ); ?></span>
                            </div>
                            <div class="tk-result-row" style="border-top:1px solid var(--secondary-color);margin-top:4px;padding-top:14px">
                                <span class="tk-result-label" style="color:#fff;font-weight:500"><?php _e( 'Total con IVA (CLP)', 'maggiore' ); ?></span>
                                <span class="tk-result-val accent" id="tkResTotalIVACLP"><?php _e( 'Cargando…', 'maggiore' ); ?></span>
                            </div>
                        </div>
                        <div class="tk-xnote">
                            <span class="tk-xdot" id="tkXDot"></span>
                            <span id="tkXNote"><?php _e( 'Obteniendo tipo de cambio del Banco Central de Chile…', 'maggiore' ); ?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <?php /* ═══ CATÁLOGO ════════════════════════════════════════════════════ */ ?>
    <section class="tk-section  " id="tk-catalogo" aria-labelledby="tk-h2-catalogo">
        <div class="container-fluid">
            <div class="tk-label" aria-hidden="true"><?php _e( 'Catálogo', 'maggiore' ); ?></div>
            <div class="row align-items-end g-4">
                <div class="col-lg-5">
                    <h2 class="tk-title mb-0" id="tk-h2-catalogo"><?php _e( 'Cada entregable, un valor claro', 'maggiore' ); ?></h2>
                </div>
                <div class="col-lg-5 offset-lg-2">
                    <p class="tk-sub mb-0"><?php _e( 'Mezcla libremente entre contenido, branding, web, medios, reportes, inteligencia social y experimentos.', 'maggiore' ); ?></p>
                </div>
            </div>

            <div class="tk-filters">
                <button class="tk-filter-btn active" onclick="tkFilter('all',this)"><?php _e( 'Todo', 'maggiore' ); ?></button>
                <button class="tk-filter-btn" onclick="tkFilter('contenido',this)"><?php _e( 'Contenido', 'maggiore' ); ?></button>
                <button class="tk-filter-btn" onclick="tkFilter('branding',this)"><?php _e( 'Branding', 'maggiore' ); ?></button>
                <button class="tk-filter-btn" onclick="tkFilter('web',this)"><?php _e( 'Web', 'maggiore' ); ?></button>
                <button class="tk-filter-btn" onclick="tkFilter('medios',this)"><?php _e( 'Medios', 'maggiore' ); ?></button>
                <button class="tk-filter-btn" onclick="tkFilter('reportes',this)"><?php _e( 'Reportes', 'maggiore' ); ?></button>
                <button class="tk-filter-btn" onclick="tkFilter('training',this)"><?php _e( 'Training', 'maggiore' ); ?></button>
                <button class="tk-filter-btn" onclick="tkFilter('sia',this)"><?php _e( 'Inteligencia Social', 'maggiore' ); ?></button>
                <button class="tk-filter-btn" onclick="tkFilter('iep',this)"><?php _e( 'Experimentos', 'maggiore' ); ?></button>
            </div>

            <div class="tk-table-wrap">
                <table class="tk-table" id="tkTable" aria-label="<?php esc_attr_e( 'Catálogo de servicios de marketing digital con costo en tokens', 'maggiore' ); ?>">
                    <caption class="visually-hidden"><?php _e( 'Catálogo de entregables de Maggiore Marketing con su costo expresado en tokens. Incluye contenido, branding, web, medios, reportes, inteligencia social y experimentos.', 'maggiore' ); ?></caption>
                    <thead>
                        <tr>
                            <th><?php _e( 'Categoría', 'maggiore' ); ?></th>
                            <th><?php _e( 'Entregable', 'maggiore' ); ?></th>
                            <th><?php _e( 'Descripción', 'maggiore' ); ?></th>
                            <th style="text-align:center"><?php _e( 'Tokens', 'maggiore' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $catalog = [
                            ['contenido','Contenido','Post estático','Pieza simple para IG / LN / FB',1],
                            ['contenido','Contenido','Carrusel (5 láminas)','Secuencia de 5 láminas',3],
                            ['contenido','Contenido','Video 0–60s','Grabado por cliente o stock',3],
                            ['contenido','Contenido','Video 0–60s','Grabado por Maggiore',6],
                            ['contenido','Contenido','Video 0–60s','Motion Graphic',9],
                            ['contenido','Contenido','Video 0–60s','3D',12],
                            ['contenido','Contenido','Video 60–120s','Grabado por cliente o stock',6],
                            ['contenido','Contenido','Video 60–120s','Grabado por Maggiore',12],
                            ['contenido','Contenido','Video 60–120s','Motion Graphic',18],
                            ['contenido','Contenido','Video 60–120s','3D',24],
                            ['contenido','Contenido','Video 121–180s','Grabado por cliente o stock',9],
                            ['contenido','Contenido','Video 121–180s','Grabado por Maggiore',18],
                            ['contenido','Contenido','Video 121–180s','Motion Graphic',27],
                            ['contenido','Contenido','Video 121–180s','3D',36],
                            ['contenido','Contenido','Guion 0–180s','Script para video o anuncios',1],
                            ['contenido','Contenido','Blog 800–1,200 palabras','Artículo basado en palabras clave',2],
                            ['contenido','Contenido','Email Marketing','Redacción, diseño y HTML',4],
                            ['contenido','Contenido','Estrategia de contenido','Grilla de contenido por 1 mes',5],
                            ['contenido','Contenido','Diseño PPT (1–20 láminas)','Presentación profesional',10],
                            ['contenido','Contenido','Diseño PPT (21–40 láminas)','Presentación profesional',20],
                            ['contenido','Contenido','Diseño PPT (41–60 láminas)','Presentación profesional',30],
                            ['contenido','Contenido','Diseño PPT (61–80 láminas)','Presentación profesional',40],
                            ['contenido','Contenido','Diseño PPT (81–100 láminas)','Presentación profesional',50],
                            ['contenido','Contenido','Material físico','Pendones, flyer, POP (por unidad)',2],
                            ['contenido','Contenido','Creación de cuentas','Habilitación por cuenta (IG, FB, YT, LN, X, GA)',1],
                            ['contenido','Contenido','Gestión de comunidad','Mensual, por cuenta (1h diaria, lun–vie)',7],
                            ['branding','Branding','Logo','Creación desde cero',10],
                            ['branding','Branding','Manual de marca','Diseño excluyendo logo',8],
                            ['web','Web','Página web básica','1 página',10],
                            ['web','Web','Página web media','2–5 páginas',20],
                            ['web','Web','Página web compleja','5–20 páginas',50],
                            ['web','Web','Nueva sección','En página existente',2],
                            ['web','Web','Mantenimiento','Actualización simple (c/u)',1],
                            ['medios','Medios','Lanzamiento de campaña','Configuración y monitoreo',2],
                            ['reportes','Reportes','Reporte orgánico','Indicadores orgánicos',2],
                            ['reportes','Reportes','Reporte orgánico + Meta','Indicadores orgánicos y Meta Ads',3],
                            ['reportes','Reportes','Reporte orgánico + Google','Indicadores orgánicos y Google Ads',3],
                            ['reportes','Reportes','Reporte completo','Orgánico + Google + Meta',4],
                            ['training','Training','Workshop 2h','Capacitación con diapositivas',4],
                            ['sia','Inteligencia','Análisis de Inteligencia Social','Complejidad promedio (1–40 slides)',100],
                            ['sia','Inteligencia','Análisis de Inteligencia Social','Complejo +01 (41–80 slides)',200],
                            ['sia','Inteligencia','Análisis de Inteligencia Social','Complejo +02 (81–120 slides)',300],
                            ['sia','Inteligencia','Análisis de Inteligencia Social','Complejo +03 (121–160 slides)',400],
                            ['sia','Inteligencia','Análisis de Inteligencia Social','Complejo +04 (161–200 slides)',500],
                            ['iep','Experimentos','Experimento (Stopping Power)','Complejidad promedio (1–40 celdas)',100],
                            ['iep','Experimentos','Experimento (Stopping Power)','Complejo +01 (41–80 celdas)',200],
                            ['iep','Experimentos','Experimento (Stopping Power)','Complejo +02 (81–120 celdas)',300],
                            ['iep','Experimentos','Experimento (Stopping Power)','Complejo +03 (121–160 celdas)',400],
                            ['iep','Experimentos','Experimento (Stopping Power)','Complejo +04 (161–200 celdas)',500],
                            ['iep','Experimentos','Experimento (Stopping + Closing)','Complejidad promedio (1–12 celdas)',100],
                            ['iep','Experimentos','Experimento (Stopping + Closing)','Complejo +01 (13–24 celdas)',200],
                            ['iep','Experimentos','Experimento (Stopping + Closing)','Complejo +02 (25–36 celdas)',300],
                            ['iep','Experimentos','Experimento (Stopping + Closing)','Complejo +03 (37–48 celdas)',400],
                            ['iep','Experimentos','Experimento (Stopping + Closing)','Complejo +04 (49–60 celdas)',500],
                        ];
                        foreach ( $catalog as $row ) :
                            [$cat, $label, $entregable, $desc, $tokens] = $row;
                        ?>
                        <tr data-cat="<?php echo esc_attr( $cat ); ?>">
                            <td><span class="tk-cat-pill"><?php echo esc_html( $label ); ?></span></td>
                            <td><?php echo esc_html( $entregable ); ?></td>
                            <td><?php echo esc_html( $desc ); ?></td>
                            <td style="text-align:center"><span class="tk-badge"><?php echo (int) $tokens; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="mt-4" style="font-size:13px;color:var(--text-secondary);opacity:.6;font-weight:300">
                <?php _e( '¿Necesitas un entregable que no aparece aquí? Lo cotizamos e incluimos en el catálogo. El presupuesto de pauta (Meta Ads, Google Ads) se maneja por separado.', 'maggiore' ); ?>
            </p>
        </div>
    </section>

    <?php /* ═══ FAQ ════════════════════════════════════════════════════════ */ ?>
    <section class="tk-section  " id="tk-faq" aria-labelledby="tk-h2-faq">
        <div class="container-fluid">
            <div class="row g-5">
                <div class="col-lg-3">
                    <div class="tk-label" aria-hidden="true"><?php _e( 'Preguntas frecuentes', 'maggiore' ); ?></div>
                    <h2 class="tk-title" id="tk-h2-faq"><?php _e( 'Todo lo que necesitas saber', 'maggiore' ); ?></h2>
                    <p class="tk-sub" style="font-size:15px">
                        <?php _e( 'Si no encuentras la respuesta aquí, escríbenos directamente.', 'maggiore' ); ?>
                    </p>
                </div>
                <div class="col-lg-8 offset-lg-1">
                    <?php
                    $faqs = [
                        [__('¿Qué es un token?','maggiore'),                                   __('Un token es la unidad estandarizada de esfuerzo de Maggiore Marketing: el trabajo coordinado mínimo necesario para entregar un entregable básico de marketing digital, como una publicación estática en redes sociales.','maggiore')],
                        [__('¿Hay un mínimo de tokens para empezar?','maggiore'),              __('No. No hay una cantidad mínima de tokens que debas comprar para empezar a trabajar con nosotros.','maggiore')],
                        [__('¿Qué pasa si en un mes no se entregan los insumos?','maggiore'),  __('Tus tokens se acumulan para el mes siguiente. A diferencia de una cuota fija mensual, aquí solo se gastan tokens cuando hay una entrega concreta. Si surge un imprevisto, no pierdes nada.','maggiore')],
                        [__('¿Puedo cambiar las entregas de un mes a otro?','maggiore'),        __('Sí, tienes flexibilidad total. Cada mes puedes decidir, junto con Maggiore, en qué invertir tus tokens según lo que tu negocio necesite en ese momento.','maggiore')],
                        [__('¿Los tokens tienen fecha de vencimiento?','maggiore'),             __('Sí, los tokens son válidos por 12 meses desde la fecha de compra. Si compras un monto igual o mayor a tu saldo actual, la vigencia se renueva por 12 meses completos.','maggiore')],
                        [__('¿Las revisiones cuestan tokens adicionales?','maggiore'),          __('No. Las revisiones están incluidas en el costo original del entregable. No hay límite de rondas de revisión. Operamos con una garantía de satisfacción total.','maggiore')],
                        [__('¿La pauta publicitaria está incluida en los tokens?','maggiore'),  __('No. El presupuesto de pauta (Meta Ads, Google Ads, etc.) se maneja por separado de los tokens. En general, se carga directamente a la tarjeta del cliente.','maggiore')],
                        [__('¿Puedo mezclar servicios creativos con inteligencia social?','maggiore'), __('Sí. El sistema de tokens está pensado para mezclar libremente servicios de las tres líneas: contenido creativo, análisis de inteligencia social y experimentos iterativos.','maggiore')],
                        [__('¿Hay contratos de permanencia?','maggiore'),                       __('No. Tu fidelidad se premia con mejores precios por volumen acumulado, no con amarres contractuales.','maggiore')],
                        [__('¿Cómo sé en qué se han gastado mis tokens?','maggiore'),          __('Desde la plataforma puedes ver tu saldo de tokens, cada entregable solicitado, quién lo solicitó, la fecha y el costo en tokens. El mandante del contrato puede obtener un reporte detallado completo.','maggiore')],
                    ];
                    foreach ( $faqs as $i => $faq ) :
                        $answer_id  = 'tk-faq-answer-' . $i;
                        $question_id = 'tk-faq-q-' . $i;
                    ?>
                    <?php /* itemscope/itemprop para microdata FAQ adicional a JSON-LD */ ?>
                    <div class="tk-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <button class="tk-faq-btn"
                                id="<?php echo esc_attr( $question_id ); ?>"
                                aria-expanded="false"
                                aria-controls="<?php echo esc_attr( $answer_id ); ?>"
                                onclick="tkFaq(this)">
                            <span itemprop="name"><?php echo esc_html( $faq[0] ); ?></span>
                            <div class="tk-faq-icon" aria-hidden="true">+</div>
                        </button>
                        <div class="tk-faq-body"
                             id="<?php echo esc_attr( $answer_id ); ?>"
                             role="region"
                             aria-labelledby="<?php echo esc_attr( $question_id ); ?>">
                            <div class="tk-faq-inner" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <p itemprop="text"><?php echo esc_html( $faq[1] ); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php /* ═══ CTA ════════════════════════════════════════════════════════ */ ?>
    <section class="tk-cta" id="tk-contacto">
        <div class="container-fluid">
            <h2><?php _e( '¿Listo para un marketing sin desperdicio?', 'maggiore' ); ?></h2>
            <p><?php _e( 'Flexibilidad total, precios transparentes y un aliado estratégico que se adapta a tu ritmo.', 'maggiore' ); ?></p>

            <div class="mg-link d-inline-block">
                <a class="btns-mgr" href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">
                    <div class="btn-brillo"></div>
                    <div class="btn-container">
                        <div class="btn-content">
                            <span class="btn-text"><?php _e( 'Habla con nosotros', 'maggiore' ); ?></span>
                            <span class="btn-text-2"><?php _e( 'Habla con nosotros', 'maggiore' ); ?></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ─────────────────────────────────────────────────────────────────────────
       SCROLL SUAVE CON SCROLLSMOOTHER + SCROLLTO
       Igual que page-home.php: usa smoother.scrollTo si existe,
       sino cae a gsap.to + ScrollToPlugin.
    ───────────────────────────────────────────────────────────────────────── */
    if (typeof gsap !== 'undefined') {
        gsap.registerPlugin(ScrollToPlugin);
    }

    document.querySelectorAll('.tk-scroll-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var target = this.dataset.target;
            if (!target) return;

            var smoother = (typeof ScrollSmoother !== 'undefined') ? ScrollSmoother.get() : null;

            if (smoother) {
                smoother.scrollTo(target, true, 'top 100px');
            } else if (typeof gsap !== 'undefined') {
                gsap.to(window, {
                    duration: 1.2,
                    scrollTo: { y: target, offsetY: 100 },
                    ease: 'power2.inOut'
                });
            } else {
                var el = document.querySelector(target);
                if (el) el.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    /* ─────────────────────────────────────────────────────────────────────────
       FAQ ACCORDION — sincroniza aria-expanded para accesibilidad y SEO
    ───────────────────────────────────────────────────────────────────────── */
    window.tkFaq = function (btn) {
        var item = btn.closest('.tk-faq-item');
        var was  = item.classList.contains('open');
        document.querySelectorAll('.tk-faq-item').forEach(function (i) {
            i.classList.remove('open');
            i.querySelector('.tk-faq-btn').setAttribute('aria-expanded', 'false');
        });
        if (!was) {
            item.classList.add('open');
            btn.setAttribute('aria-expanded', 'true');
        }
    };

    /* ─────────────────────────────────────────────────────────────────────────
       FILTRO CATÁLOGO
    ───────────────────────────────────────────────────────────────────────── */
    window.tkFilter = function (cat, btn) {
        document.querySelectorAll('.tk-filter-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        document.querySelectorAll('#tkTable tbody tr').forEach(function (r) {
            r.style.display = (cat === 'all' || r.dataset.cat === cat) ? '' : 'none';
        });
    };

    /* ─────────────────────────────────────────────────────────────────────────
       CALCULADORA DE PRECIOS
    ───────────────────────────────────────────────────────────────────────── */
    var xr = null;

    window.tkCalc = function () {
        var historic = Math.max(0, parseInt(document.getElementById('tkCalcHistoric').value) || 0);
        var newT     = Math.max(1, parseInt(document.getElementById('tkCalcNew').value)      || 1);
        var T        = historic + newT;
        var disc     = Math.min((T - 1) / 30000, 1 / 6);
        var price    = Math.max(60 * (1 - disc), 50);
        var total    = T * price;
        var hCost    = 0;

        if (historic > 0) {
            var hd = Math.min((historic - 1) / 30000, 1 / 6);
            hCost  = historic * Math.max(60 * (1 - hd), 50);
        }

        var toPay = total - hCost;
        var eff   = toPay / newT;
        var fmt   = function (n) { return n.toLocaleString('es-CL'); };

        document.getElementById('tkResBase').textContent     = fmt(T);
        document.getElementById('tkResDiscount').textContent = (disc * 100).toFixed(2) + '%';
        document.getElementById('tkResPriceUSD').textContent = 'USD ' + eff.toFixed(2);
        document.getElementById('tkResTotalUSD').textContent = 'USD ' + toPay.toFixed(2);

        if (xr) {
            var netoCLP    = Math.round(toPay * xr);
            var ivaCLP     = Math.round(netoCLP * 0.19);
            var totalCLP   = netoCLP + ivaCLP;
            document.getElementById('tkResPriceCLP').textContent  = '$ ' + fmt(Math.round(eff * xr));
            document.getElementById('tkResTotalCLP').textContent  = '$ ' + fmt(netoCLP);
            document.getElementById('tkResIVACLP').textContent    = '$ ' + fmt(ivaCLP);
            document.getElementById('tkResTotalIVACLP').textContent = '$ ' + fmt(totalCLP);
        } else {
            document.getElementById('tkResPriceCLP').textContent   = 'Sin datos';
            document.getElementById('tkResTotalCLP').textContent   = 'Sin datos';
            document.getElementById('tkResIVACLP').textContent     = 'Sin datos';
            document.getElementById('tkResTotalIVACLP').textContent = 'Sin datos';
        }
    };

    async function fetchXR () {
        var dot  = document.getElementById('tkXDot');
        var note = document.getElementById('tkXNote');

        var sources = [
            {
                url:   'https://mindicador.cl/api/dolar',
                parse: function (d) { return (d && d.serie && d.serie[0]) ? { rate: d.serie[0].valor, src: 'Banco Central de Chile' } : null; }
            },
            {
                url:   'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json',
                parse: function (d) { return (d && d.usd && d.usd.clp) ? { rate: d.usd.clp, src: 'Currency API' } : null; }
            },
            {
                url:   'https://open.er-api.com/v6/latest/USD',
                parse: function (d) { return (d && d.rates && d.rates.CLP) ? { rate: d.rates.CLP, src: 'Open ExchangeRate API' } : null; }
            }
        ];

        for (var i = 0; i < sources.length; i++) {
            try {
                var ctrl = new AbortController();
                var t    = setTimeout(function () { ctrl.abort(); }, 5000);
                var res  = await fetch(sources[i].url, { signal: ctrl.signal });
                clearTimeout(t);
                if (!res.ok) continue;
                var data = await res.json();
                var r    = sources[i].parse(data);
                if (r && r.rate > 0) {
                    xr = r.rate;
                    dot.style.background  = 'var(--secondary-color)';
                    note.textContent = 'Tipo de cambio: $' + xr.toFixed(2) + ' CLP/USD · Fuente: ' + r.src;
                    window.tkCalc();
                    return;
                }
            } catch (e) { continue; }
        }

        /* Fallback manual */
        dot.style.background = '#e8aa00';
        var sp  = document.createElement('span');
        sp.textContent = 'Tipo de cambio no disponible. Ingresar manualmente: ';
        var inp = document.createElement('input');
        inp.type = 'number'; inp.placeholder = 'ej: 930';
        inp.style.cssText = 'width:80px;padding:4px 8px;border-radius:4px;border:var(--border-container);background:var(--inner-background);color:#fff;font-size:13px;margin-left:6px;font-family:inherit';
        inp.oninput = function () {
            var v = parseFloat(this.value);
            xr = v > 0 ? v : null;
            if (xr) dot.style.background = 'var(--secondary-color)';
            window.tkCalc();
        };
        note.textContent = '';
        note.appendChild(sp);
        note.appendChild(inp);
        window.tkCalc();
    }

    fetchXR();
    window.tkCalc(); /* cálculo inicial */
});
</script>

<?php get_footer(); ?>
