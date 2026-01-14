<?php
/**
 * Template para mostrar comentarios
 * Compatible con el diseño Maggiore
 */

if (!defined('ABSPATH')) exit;

// Si el post está protegido por contraseña y no se ha ingresado la contraseña
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()): ?>
        <h2 class="comments-title">
            <?php
            $comment_count = get_comments_number();
            if ($comment_count === 1) {
                echo '1 ' . __('Comentario', 'maggiore');
            } else {
                printf(
                    _n(
                        '%1$s Comentario',
                        '%1$s Comentarios',
                        $comment_count,
                        'maggiore'
                    ),
                    number_format_i18n($comment_count)
                );
            }
            ?>
        </h2>

        <?php the_comments_navigation(); ?>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
                'callback'    => 'maggiore_custom_comment',
            ]);
            ?>
        </ol>

        <?php
        the_comments_navigation();

        // Si los comentarios están cerrados y hay comentarios
        if (!comments_open()):
        ?>
            <p class="no-comments">
                <?php _e('Los comentarios están cerrados.', 'maggiore'); ?>
            </p>
        <?php endif; ?>

    <?php endif; // Fin de have_comments() ?>

    <?php
    // Formulario de comentarios
    comment_form([
        'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
        'title_reply_after'  => '</h3>',
        'title_reply'        => __('Deja un comentario', 'maggiore'),
        'title_reply_to'     => __('Responder a %s', 'maggiore'),
        'cancel_reply_link'  => __('Cancelar respuesta', 'maggiore'),
        'label_submit'       => __('Publicar comentario', 'maggiore'),
        'submit_button'      => '<button type="submit" class="submit">%4$s</button>',
        'comment_field'      => '<p class="comment-form-comment">
            <label for="comment">' . __('Comentario', 'maggiore') . ' <span class="required">*</span></label>
            <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required" placeholder="' . esc_attr__('Escribe tu comentario aquí...', 'maggiore') . '"></textarea>
        </p>',
        'fields'             => [
            'author' => '<p class="comment-form-author">
                <label for="author">' . __('Nombre', 'maggiore') . ' <span class="required">*</span></label>
                <input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245" required="required" placeholder="' . esc_attr__('Tu nombre', 'maggiore') . '" />
            </p>',
            'email'  => '<p class="comment-form-email">
                <label for="email">' . __('Email', 'maggiore') . ' <span class="required">*</span></label>
                <input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" aria-describedby="email-notes" required="required" placeholder="' . esc_attr__('tu@email.com', 'maggiore') . '" />
            </p>',
            'url'    => '<p class="comment-form-url">
                <label for="url">' . __('Sitio web', 'maggiore') . '</label>
                <input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200" placeholder="' . esc_attr__('https://tusitio.com (opcional)', 'maggiore') . '" />
            </p>',
            'cookies' => '<p class="comment-form-cookies-consent">
                <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" />
                <label for="wp-comment-cookies-consent">' . __('Guardar mi nombre, correo electrónico y sitio web en este navegador para la próxima vez que comente.', 'maggiore') . '</label>
            </p>',
        ],
        'class_form'         => 'comment-form',
        'class_submit'       => 'submit',
    ]);
    ?>

</div>

<?php
/**
 * Función personalizada para renderizar cada comentario
 */
function maggiore_custom_comment($comment, $args, $depth) {
    $tag = ('div' === $args['style']) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <footer class="comment-meta">
                <div class="comment-author vcard">
                    <?php
                    if (0 != $args['avatar_size']) {
                        echo get_avatar($comment, $args['avatar_size']);
                    }
                    ?>
                    <b class="fn"><?php echo get_comment_author_link($comment); ?></b>
                    <?php if ('0' == $comment->comment_approved): ?>
                        <em class="comment-awaiting-moderation">
                            <?php _e('Tu comentario está esperando moderación.', 'maggiore'); ?>
                        </em>
                    <?php endif; ?>
                </div>

                <div class="comment-metadata">
                    <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                        <time datetime="<?php comment_time('c'); ?>">
                            <?php
                            printf(
                                _x('%1$s a las %2$s', '1: fecha, 2: hora', 'maggiore'),
                                get_comment_date('', $comment),
                                get_comment_time()
                            );
                            ?>
                        </time>
                    </a>
                    <?php edit_comment_link(__('Editar', 'maggiore'), '<span class="edit-link">', '</span>'); ?>
                </div>
            </footer>

            <div class="comment-content">
                <?php comment_text(); ?>
            </div>

            <?php
            comment_reply_link(array_merge($args, [
                'add_below' => 'div-comment',
                'depth'     => $depth,
                'max_depth' => $args['max_depth'],
                'before'    => '<div class="reply">',
                'after'     => '</div>',
            ]));
            ?>
        </article>
    <?php
}
?>
