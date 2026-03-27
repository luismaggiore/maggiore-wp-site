<?php
if (!defined('ABSPATH')) exit;

/**
 * SEO para Archives de CPTs — con soporte multilenguaje (Polylang)
 *
 * Clave por idioma:  mg_archive_{post_type}_{lang}_{field}
 * Ejemplo ES:        mg_archive_mg_portafolio_es_seo_title
 * Ejemplo EN:        mg_archive_mg_portafolio_en_seo_title
 *
 * Ubicación: inc/seo-archive-options.php
 */

// ────────────────────────────────────────────────────────────
// CPTs con archive
// ────────────────────────────────────────────────────────────
function mg_get_cpts_with_archive() {
    return [
        'mg_portafolio'  => [ 'label' => 'Portafolio',    'slug_es' => 'portafolio' ],
        'mg_servicio'    => [ 'label' => 'Servicios',      'slug_es' => 'servicios' ],
        'mg_caso_exito'  => [ 'label' => 'Casos de Éxito', 'slug_es' => 'casos-de-exito' ],
        'mg_cliente'     => [ 'label' => 'Clientes',       'slug_es' => 'clientes' ],
        'mg_equipo'      => [ 'label' => 'Equipo',         'slug_es' => 'equipo' ],
        'mg_area'        => [ 'label' => 'Áreas',          'slug_es' => 'area' ],
    ];
}

// ────────────────────────────────────────────────────────────
// Idiomas disponibles (Polylang o solo ES)
// ────────────────────────────────────────────────────────────
function mg_archive_get_languages() {
    if ( function_exists('pll_languages_list') ) {
        $langs = pll_languages_list( [ 'fields' => 'slug' ] );
        if ( ! empty( $langs ) ) return (array) $langs;
    }
    return [ 'es' ];
}

function mg_archive_lang_label( $code ) {
    $map = [
        'es' => '🇨🇱 Español',
        'en' => '🇺🇸 English',
        'pt' => '🇧🇷 Português',
    ];
    return $map[ $code ] ?? strtoupper( $code );
}

// Genera la clave de wp_option con idioma incluido
function mg_archive_option_key( $post_type, $lang, $field ) {
    return 'mg_archive_' . $post_type . '_' . $lang . '_' . $field;
}

// URL del archive para un CPT + idioma
function mg_archive_get_url( $post_type, $lang ) {
    $slug = '';

    if ( function_exists('mg_get_translated_cpt_slugs') ) {
        $slugs = mg_get_translated_cpt_slugs();
        $slug  = $slugs[ $post_type ][ $lang ] ?? ( $slugs[ $post_type ]['es'] ?? '' );
    }

    if ( ! $slug ) {
        $cpts = mg_get_cpts_with_archive();
        $slug = $cpts[ $post_type ]['slug_es'] ?? '';
    }

    $langs = mg_archive_get_languages();
    $base  = home_url('/');

    // Idiomas distintos al principal (es) llevan prefijo
    if ( count( $langs ) > 1 && $lang !== 'es' ) {
        return trailingslashit( $base . $lang . '/' . $slug );
    }
    return trailingslashit( $base . $slug );
}

// ────────────────────────────────────────────────────────────
// Menú admin
// ────────────────────────────────────────────────────────────
add_action('admin_menu', function () {
    add_submenu_page(
        'options-general.php',
        'SEO Archives CPT',
        'SEO Archives',
        'manage_options',
        'mg-seo-archives',
        'mg_render_seo_archives_page'
    );
});

add_action('admin_enqueue_scripts', function ( $hook ) {
    if ( $hook !== 'settings_page_mg-seo-archives' ) return;
    wp_enqueue_media();
    wp_enqueue_script('jquery');
});

// ────────────────────────────────────────────────────────────
// Guardar opciones
// ────────────────────────────────────────────────────────────
add_action('admin_post_mg_save_archive_seo', function () {
    if ( ! current_user_can('manage_options') ) wp_die('Sin permiso');
    check_admin_referer('mg_save_archive_seo_nonce');

    $cpt  = sanitize_key( $_POST['_cpt_current']  ?? '' );
    $lang = sanitize_key( $_POST['_lang_current'] ?? 'es' );

    if ( ! array_key_exists( $cpt, mg_get_cpts_with_archive() ) ) wp_die('CPT inválido');

    foreach ( [ 'seo_title', 'seo_description', 'og_image', 'schema_json', 'custom_head' ] as $field ) {
        $key = mg_archive_option_key( $cpt, $lang, $field );
        update_option( $key, wp_kses_post( stripslashes( $_POST[ $key ] ?? '' ) ) );
    }

    // Keywords (array)
    $kw_key = mg_archive_option_key( $cpt, $lang, 'seo_keywords' );
    $raw    = $_POST[ $kw_key ] ?? [];
    if ( is_array( $raw ) ) {
        update_option( $kw_key, array_values( array_filter( array_map( 'sanitize_text_field', stripslashes_deep( $raw ) ) ) ) );
    } else {
        update_option( $kw_key, [] );
    }

    // Noindex
    $ni_key = mg_archive_option_key( $cpt, $lang, 'noindex' );
    update_option( $ni_key, isset( $_POST[ $ni_key ] ) ? '1' : '' );

    wp_redirect( add_query_arg( [
        'page'    => 'mg-seo-archives',
        'cpt'     => $cpt,
        'lang'    => $lang,
        'updated' => '1',
    ], admin_url('options-general.php') ) );
    exit;
});

// ────────────────────────────────────────────────────────────
// Render
// ────────────────────────────────────────────────────────────
function mg_render_seo_archives_page() {
    $cpts      = mg_get_cpts_with_archive();
    $languages = mg_archive_get_languages();
    $multilang = count( $languages ) > 1;

    $current_cpt  = sanitize_key( $_GET['cpt']  ?? array_key_first( $cpts ) );
    $current_lang = sanitize_key( $_GET['lang'] ?? ( $languages[0] ?? 'es' ) );
    if ( ! array_key_exists( $current_cpt, $cpts ) )    $current_cpt  = array_key_first( $cpts );
    if ( ! in_array( $current_lang, $languages, true ) ) $current_lang = $languages[0] ?? 'es';

    $get = fn( $f ) => get_option( mg_archive_option_key( $current_cpt, $current_lang, $f ), '' );

    $seo_title       = $get('seo_title');
    $seo_description = $get('seo_description');
    $seo_keywords    = get_option( mg_archive_option_key( $current_cpt, $current_lang, 'seo_keywords' ), [] );
    $og_image        = $get('og_image');
    $schema_json     = $get('schema_json');
    $custom_head     = $get('custom_head');
    $noindex         = $get('noindex');

    if ( ! is_array( $seo_keywords ) ) $seo_keywords = array_filter( explode(',', $seo_keywords) );

    $archive_url = mg_archive_get_url( $current_cpt, $current_lang );
    $tl          = strlen( $seo_title );
    $dl          = strlen( $seo_description );
    $p           = mg_archive_option_key( $current_cpt, $current_lang, '' ); // sufijo vacío → prefijo completo
    ?>
    <div class="wrap">
        <h1>SEO para Archives de CPTs</h1>

        <?php if ( isset( $_GET['updated'] ) ): ?>
            <div class="notice notice-success is-dismissible"><p>✅ Cambios guardados.</p></div>
        <?php endif; ?>

        <div style="display:flex;gap:22px;margin-top:18px;align-items:flex-start;">

            <!-- Sidebar -->
            <div style="min-width:185px;background:#fff;border:1px solid #ddd;border-radius:4px;padding:8px 0;flex-shrink:0;">
                <div style="padding:8px 14px;font-size:11px;font-weight:600;text-transform:uppercase;color:#666;border-bottom:1px solid #eee;letter-spacing:.5px;">
                    Archivos CPT
                </div>
                <?php foreach ( $cpts as $pt => $info ):
                    $act = ( $pt === $current_cpt );
                    $lnk = add_query_arg( ['page'=>'mg-seo-archives','cpt'=>$pt,'lang'=>$current_lang], admin_url('options-general.php') );
                    $cfg = (bool) get_option( mg_archive_option_key( $pt, $current_lang, 'seo_title' ) );
                ?>
                    <a href="<?= esc_url($lnk); ?>"
                       style="display:flex;align-items:center;justify-content:space-between;padding:9px 14px;text-decoration:none;font-size:13px;
                              color:<?= $act?'#2271b1':'#333'; ?>;background:<?= $act?'#f0f6fc':'transparent'; ?>;
                              border-left:3px solid <?= $act?'#2271b1':'transparent'; ?>;">
                        <span><?= esc_html($info['label']); ?></span>
                        <span style="color:<?= $cfg?'#46b450':'#ddd'; ?>;font-size:14px;" title="<?= $cfg?'SEO configurado':'Sin configurar'; ?>">●</span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Panel principal -->
            <div style="flex:1;min-width:0;">

                <!-- Tabs de idioma -->
                <?php if ( $multilang ): ?>
                    <div style="display:flex;gap:0;margin-bottom:-1px;position:relative;z-index:1;">
                        <?php foreach ( $languages as $lang ):
                            $al  = ( $lang === $current_lang );
                            $ll  = add_query_arg(['page'=>'mg-seo-archives','cpt'=>$current_cpt,'lang'=>$lang], admin_url('options-general.php'));
                            $lh  = (bool) get_option( mg_archive_option_key( $current_cpt, $lang, 'seo_title' ) );
                        ?>
                            <a href="<?= esc_url($ll); ?>"
                               style="padding:8px 18px;text-decoration:none;font-size:13px;font-weight:<?= $al?'600':'400'; ?>;
                                      border:1px solid <?= $al?'#ddd':'#e0e0e0'; ?>;border-bottom:1px solid <?= $al?'#fff':'#ddd'; ?>;
                                      background:<?= $al?'#fff':'#f6f7f7'; ?>;color:<?= $al?'#2271b1':'#666'; ?>;
                                      border-radius:4px 4px 0 0;margin-right:2px;">
                                <?= esc_html( mg_archive_lang_label($lang) ); ?>
                                <?php if (!$lh): ?><span title="Sin SEO" style="color:#ffb900;font-size:10px;margin-left:3px;">⚠</span><?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div style="background:#fff;border:1px solid #ddd;border-radius:<?= $multilang?'0 4px 4px 4px':'4px'; ?>;padding:22px;">

                    <!-- Cabecera -->
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid #eee;gap:10px;flex-wrap:wrap;">
                        <div>
                            <h2 style="margin:0 0 4px;font-size:17px;">
                                <?= esc_html($cpts[$current_cpt]['label']); ?>
                                <?php if ($multilang): ?>
                                    <span style="font-size:13px;font-weight:400;color:#666;margin-left:6px;">
                                        — <?= esc_html(mg_archive_lang_label($current_lang)); ?>
                                    </span>
                                <?php endif; ?>
                            </h2>
                            <a href="<?= esc_url($archive_url); ?>" target="_blank" style="font-size:12px;color:#888;">
                                <?= esc_url($archive_url); ?> ↗
                            </a>
                        </div>
                        <?php if ($noindex): ?>
                            <span style="background:#fcf0f1;color:#d63638;border:1px solid #f8bbb9;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">NOINDEX</span>
                        <?php endif; ?>
                    </div>

                    <form method="post" action="<?= admin_url('admin-post.php'); ?>">
                        <?php wp_nonce_field('mg_save_archive_seo_nonce'); ?>
                        <input type="hidden" name="action"        value="mg_save_archive_seo">
                        <input type="hidden" name="_cpt_current"  value="<?= esc_attr($current_cpt); ?>">
                        <input type="hidden" name="_lang_current" value="<?= esc_attr($current_lang); ?>">

                        <table class="form-table" style="margin-top:0;">

                            <tr>
                                <th style="width:170px;padding-top:14px;"><label><strong>Meta Title</strong></label></th>
                                <td style="padding-top:14px;">
                                    <input type="text" class="large-text mg-title-input"
                                           name="<?= $p; ?>seo_title" value="<?= esc_attr($seo_title); ?>"
                                           placeholder="<?= esc_attr($cpts[$current_cpt]['label'].' | '.get_bloginfo('name')); ?>">
                                    <p class="description">
                                        Caracteres: <strong><span class="mg-title-count"><?= $tl; ?></span>/60</strong> &nbsp;|&nbsp; Óptimo: 50–60
                                        <span style="display:inline-block;width:100px;height:7px;background:#eee;border-radius:4px;margin-left:6px;vertical-align:middle;">
                                            <span class="mg-title-fill" style="display:block;height:100%;border-radius:4px;width:<?= min(round($tl/60*100),100); ?>%;background:<?= $tl>60?'#d63638':($tl>=50?'#46b450':'#ffb900'); ?>;"></span>
                                        </span>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th style="padding-top:14px;"><label><strong>Meta Description</strong></label></th>
                                <td style="padding-top:14px;">
                                    <textarea class="large-text mg-desc-input" rows="3"
                                              name="<?= $p; ?>seo_description"><?= esc_textarea($seo_description); ?></textarea>
                                    <p class="description">
                                        Caracteres: <strong><span class="mg-desc-count"><?= $dl; ?></span>/160</strong> &nbsp;|&nbsp; Óptimo: 120–160
                                        <span style="display:inline-block;width:100px;height:7px;background:#eee;border-radius:4px;margin-left:6px;vertical-align:middle;">
                                            <span class="mg-desc-fill" style="display:block;height:100%;border-radius:4px;width:<?= min(round($dl/160*100),100); ?>%;background:<?= $dl>160?'#d63638':($dl>=120?'#46b450':'#ffb900'); ?>;"></span>
                                        </span>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th style="padding-top:14px;"><label><strong>Keywords</strong></label></th>
                                <td style="padding-top:14px;">
                                    <div class="mg-kw-wrap" style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;min-height:36px;padding:7px 10px;border:1px solid #8c8f94;border-radius:4px;background:#fff;cursor:text;">
                                        <?php foreach ($seo_keywords as $kw): ?>
                                            <span class="mg-kw-tag" style="display:inline-flex;align-items:center;gap:4px;background:#2271b1;color:#fff;border-radius:20px;padding:3px 10px;font-size:12px;">
                                                <span><?= esc_html($kw); ?></span>
                                                <input type="hidden" name="<?= $p; ?>seo_keywords[]" value="<?= esc_attr($kw); ?>">
                                                <button type="button" class="mg-kw-remove" style="background:none;border:none;color:#fff;cursor:pointer;font-size:15px;line-height:1;padding:0 0 1px 2px;" title="Eliminar">×</button>
                                            </span>
                                        <?php endforeach; ?>
                                        <input type="text" class="mg-kw-input" placeholder="Escribe y presiona Enter o coma…" style="border:none;outline:none;flex:1;min-width:160px;font-size:13px;">
                                    </div>
                                    <p class="description">Presiona <kbd>Enter</kbd> o <kbd>,</kbd> para agregar. Clic en × para eliminar.</p>
                                </td>
                            </tr>

                            <tr>
                                <th style="padding-top:14px;"><label><strong>Open Graph Image</strong></label></th>
                                <td style="padding-top:14px;">
                                    <input type="hidden" name="<?= $p; ?>og_image" id="mg_archive_og_image" value="<?= esc_attr($og_image); ?>">
                                    <button type="button" class="button mg-upload-og-btn"><?= $og_image?'🔄 Cambiar imagen':'📷 Seleccionar imagen'; ?></button>
                                    <?php if ($og_image): ?>
                                        <button type="button" class="button mg-remove-og-btn" style="margin-left:8px;">✕ Eliminar</button>
                                    <?php endif; ?>
                                    <p class="description">Recomendado: 1200×630px.</p>
                                    <div class="mg-og-preview" style="margin-top:8px;">
                                        <?php if ($og_image): ?><img src="<?= esc_url($og_image); ?>" style="max-width:240px;border:1px solid #ddd;border-radius:4px;"><?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <th style="padding-top:14px;"><label><strong>Indexación</strong></label></th>
                                <td style="padding-top:14px;">
                                    <label>
                                        <input type="checkbox" name="<?= $p; ?>noindex" value="1" <?= checked($noindex,'1',false); ?>>
                                        <strong>No indexar este archive</strong> <small style="color:#666;">(noindex, nofollow)</small>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th style="padding-top:14px;"><label><strong>Schema JSON-LD</strong><br><small style="font-weight:400;">(opcional)</small></label></th>
                                <td style="padding-top:14px;">
                                    <textarea class="large-text code" rows="9" name="<?= $p; ?>schema_json"
                                              placeholder='{ "@context": "https://schema.org", "@type": "CollectionPage" }'><?= esc_textarea($schema_json); ?></textarea>
                                    <p class="description">Vacío = schema <code>CollectionPage</code> automático.</p>
                                </td>
                            </tr>

                            <tr>
                                <th style="padding-top:14px;"><label><strong>Código Custom Head</strong><br><small style="color:#d63638;font-weight:400;">⚠️ Avanzado</small></label></th>
                                <td style="padding-top:14px;">
                                    <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:4px;padding:10px 12px;font-size:12px;margin-bottom:8px;">
                                        Se insertará solo en el <code>&lt;head&gt;</code> de este archive.
                                        Para códigos globales usa el <a href="<?= admin_url('admin.php?page=mg-tracking-settings'); ?>">panel de Tracking</a>.
                                    </div>
                                    <textarea class="large-text code" rows="4" name="<?= $p; ?>custom_head"
                                              placeholder="<!-- Scripts o meta tags adicionales -->"><?= esc_textarea($custom_head); ?></textarea>
                                </td>
                            </tr>

                        </table>

                        <div style="margin-top:18px;padding-top:14px;border-top:1px solid #eee;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            <?php submit_button('Guardar cambios','primary large','submit',false); ?>
                            <a href="<?= esc_url($archive_url); ?>" target="_blank" class="button button-large">👁 Ver en el sitio</a>
                            <?php if ($multilang):
                                foreach ( array_filter($languages,fn($l)=>$l!==$current_lang) as $ol ):
                                    $ot = get_option(mg_archive_option_key($current_cpt,$ol,'seo_title'),'');
                                    $ol_lnk = add_query_arg(['page'=>'mg-seo-archives','cpt'=>$current_cpt,'lang'=>$ol],admin_url('options-general.php'));
                            ?>
                                <a href="<?= esc_url($ol_lnk); ?>" class="button" style="color:<?= $ot?'#333':'#d63638'; ?>;">
                                    <?= esc_html(mg_archive_lang_label($ol)); ?> <?= $ot?'✅':'⚠️ sin SEO'; ?>
                                </a>
                            <?php   endforeach;
                            endif; ?>
                        </div>

                    </form>
                </div>

                <!-- Vista previa Google -->
                <?php if ($seo_title || $seo_description): ?>
                    <div style="background:#fff;border:1px solid #ddd;border-radius:4px;padding:18px;margin-top:14px;">
                        <p style="margin:0 0 10px;font-size:11px;text-transform:uppercase;color:#888;letter-spacing:.5px;font-weight:600;">Vista previa en Google</p>
                        <div style="font-family:arial,sans-serif;max-width:580px;">
                            <div style="font-size:18px;color:#1a0dab;margin-bottom:2px;"><?= esc_html($seo_title ?: $cpts[$current_cpt]['label'].' | '.get_bloginfo('name')); ?></div>
                            <div style="font-size:13px;color:#006621;margin-bottom:4px;"><?= esc_url($archive_url); ?></div>
                            <div style="font-size:14px;color:#545454;line-height:1.5;"><?= esc_html($seo_description ?: 'Sin descripción configurada.'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script>
    (function($){
        function updateBar(inp,cSel,fSel,max){
            var l=$(inp).val().length,p=Math.min(Math.round(l/max*100),100),c=l>max?'#d63638':(l>=max*.8?'#46b450':'#ffb900');
            $(cSel).text(l);$(fSel).css({width:p+'%',background:c});
        }
        $('.mg-title-input').on('input',function(){updateBar(this,'.mg-title-count','.mg-title-fill',60);});
        $('.mg-desc-input').on('input', function(){updateBar(this,'.mg-desc-count', '.mg-desc-fill',160);});

        var kwName='<?= esc_js($p); ?>seo_keywords[]';
        function addKw(v){
            v=v.trim().replace(/,+$/,'').trim();if(!v)return;
            var ex=false;$('.mg-kw-tag input[type=hidden]').each(function(){if($(this).val().toLowerCase()===v.toLowerCase())ex=true;});if(ex)return;
            var t=$('<span class="mg-kw-tag"></span>').css({display:'inline-flex',alignItems:'center',gap:'4px',background:'#2271b1',color:'#fff',borderRadius:'20px',padding:'3px 10px',fontSize:'12px'});
            t.append($('<span></span>').text(v));
            t.append($('<input type="hidden">').attr('name',kwName).val(v));
            t.append($('<button type="button" class="mg-kw-remove" title="Eliminar">×</button>').css({background:'none',border:'none',color:'#fff',cursor:'pointer',fontSize:'15px',lineHeight:'1',padding:'0 0 1px 2px'}));
            $('.mg-kw-input').before(t);
        }
        $('.mg-kw-wrap').on('click',function(){$(this).find('.mg-kw-input').focus();});
        $(document).on('keydown','.mg-kw-input',function(e){
            if(e.key==='Enter'||e.key===','){e.preventDefault();addKw($(this).val());$(this).val('');}
            else if(e.key==='Backspace'&&!$(this).val())$(this).prev('.mg-kw-tag').remove();
        });
        $(document).on('input','.mg-kw-input',function(){
            if($(this).val().includes(',')){var p=$(this).val().split(',');$(this).val(p[p.length-1].trim());for(var i=0;i<p.length-1;i++)addKw(p[i]);}
        });
        $(document).on('click','.mg-kw-remove',function(e){e.preventDefault();$(this).closest('.mg-kw-tag').remove();});

        var ogF;
        $('.mg-upload-og-btn').on('click',function(e){
            e.preventDefault();
            if(ogF){ogF.open();return;}
            ogF=wp.media({title:'Seleccionar imagen OG',button:{text:'Usar imagen'},multiple:false});
            ogF.on('select',function(){var a=ogF.state().get('selection').first().toJSON();
                $('#mg_archive_og_image').val(a.url);
                $('.mg-og-preview').html('<img src="'+a.url+'" style="max-width:240px;border:1px solid #ddd;border-radius:4px;">');
                $('.mg-upload-og-btn').text('🔄 Cambiar imagen');
                if(!$('.mg-remove-og-btn').length)$('.mg-upload-og-btn').after('<button type="button" class="button mg-remove-og-btn" style="margin-left:8px;">✕ Eliminar</button>');
            });ogF.open();
        });
        $(document).on('click','.mg-remove-og-btn',function(e){
            e.preventDefault();$('#mg_archive_og_image').val('');$('.mg-og-preview').html('');$('.mg-upload-og-btn').text('📷 Seleccionar imagen');$(this).remove();
        });
    })(jQuery);
    </script>
    <?php
}
