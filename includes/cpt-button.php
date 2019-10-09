<?php
if (!defined('ABSPATH')) {
    exit;
}

function redsys_custom_post_type_button_redsys() {

    $labels = [
        'name' => __('Botones pago', 'redsys'),
        'singular_name' => __('Botón', 'redsys'),
        'add_new' => __('Crear nuevo', 'redsys'),
        'add_new_item' => __('Crear nuevo botón', 'redsys'),
        'edit_item' => __('Editar botón', 'redsys'),
        'new_item' => __('Nuevo botón', 'redsys'),
        'view_item' => __('Ver botón', 'redsys'),
        'search_items' => __('Buscar botón', 'redsys'),
        'not_found' => __('No hemos encontrado botones', 'redsys'),
        'not_found_in_trash' => __('No hemos encontrado botones en la papelera', 'redsys'),
        'parent_item_colon' => ''
    ];

    $args = [
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'botones', 'with_front' => true),
        'hierarchical' => false,
        'menu_position' => null,
        'show_admin_column' => true,
        'supports' => array('title'),
        'has_archive' => false,
    ];

    register_post_type('button_redsys', $args);

}
add_action('after_setup_theme', 'redsys_custom_post_type_button_redsys');

function redsys_custom_metabox_price() {
    add_meta_box(
        '_button_price', 
        __('Precio', 'redsys'), 
        'redsys_custom_metabox_button_price_callback', 
        'button_redsys', 
        'advanced', 
        'default', 
        []
    );
    add_meta_box(
        '_button_shortcode', 
        __('ShortCode', 'redsys'), 
        'redsys_custom_metabox_button_shortcode_callback', 
        'button_redsys', 
        'advanced', 
        'default', 
        []
    );
}
add_action('add_meta_boxes_button_redsys', 'redsys_custom_metabox_price');

function redsys_custom_metabox_button_price_callback($post, $data) {
    $_button_price = get_post_meta($post->ID, '_button_price', true);
    echo '<input type="text" name="_button_price" value="'.$_button_price.'">';
}

function redsys_custom_metabox_button_shortcode_callback($post, $data) {
    echo '<input readonly type="text" name="_button_shortcode" value="[redsysbutton id='.$post->ID.']">';
}

function redsys_custom_metabox_button_price_save($post_id, $post) {
    if (isset($_POST['_button_price'])){
        update_post_meta($post_id, '_button_price', $_POST['_button_price']);
    }
}
add_action('save_post_button_redsys', 'redsys_custom_metabox_button_price_save', 10, 2);