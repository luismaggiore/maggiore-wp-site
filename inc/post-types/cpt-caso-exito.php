    <?php
    if (!defined('ABSPATH')) exit;

    function mg_register_cpt_caso_exito() {
        $labels = [
            'name' => __('Casos de Éxito', 'maggiore'),
            'singular_name' => __('Caso de Éxito', 'maggiore'),
            'add_new_item' => __('Agregar caso de éxito', 'maggiore'),
            'edit_item' => __('Editar caso de éxito', 'maggiore'),
            'view_item' => __('Ver caso de éxito', 'maggiore'),
            'menu_name' => __('Casos de Éxito', 'maggiore'),
        ];

        $args = [
            'label' => __('Casos de Éxito', 'maggiore'),
            'labels' => $labels,
            'public' => true,
            'menu_icon' => 'dashicons-star-filled',
            'has_archive' => true,
            'rewrite' => ['slug' => 'casos-de-exito'],
            'supports' => ['title','thumbnail'],
            'show_in_rest' => true,
        ];

        register_post_type('mg_caso_exito', $args);
    }
    add_action('init', 'mg_register_cpt_caso_exito');
