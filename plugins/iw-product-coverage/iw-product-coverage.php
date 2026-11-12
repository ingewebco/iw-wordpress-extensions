<?php
/**
 * Plugin Name: IW Product Coverage
 * Description: Cobertura regional de productos WooCommerce y contenido condicional para Divi con integración Contact Form 7.
 * Version: 1.0.0
 * Author: IngeWeb
 * License: GPL-2.0-or-later
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: iw-product-coverage
 */
if (!defined('ABSPATH')) { exit; }
final class IW_Product_Coverage {
    const TAX = 'iw_product_region';
    public static function boot() {
        add_action('init', [__CLASS__, 'taxonomy']);
        add_shortcode('iw_cobertura', [__CLASS__, 'shortcode']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'assets']);
        add_filter('wpcf7_validate', [__CLASS__, 'validate'], 20, 2);
        add_filter('wpcf7_posted_data', [__CLASS__, 'posted']);
        add_action('admin_notices', [__CLASS__, 'notice']);
    }
    public static function taxonomy() {
        register_taxonomy(self::TAX, ['product'], [
            'labels' => ['name'=>'Regiones disponibles', 'singular_name'=>'Región disponible', 'menu_name'=>'Regiones disponibles', 'all_items'=>'Todas las regiones', 'edit_item'=>'Editar región', 'add_new_item'=>'Añadir nueva región', 'new_item_name'=>'Nombre de la región', 'search_items'=>'Buscar regiones', 'parent_item'=>'Región superior'],
            'public'=>false, 'show_ui'=>true, 'show_admin_column'=>true, 'show_in_rest'=>true,
            'hierarchical'=>true, 'rewrite'=>false, 'show_in_nav_menus'=>false,
            'capabilities'=>['manage_terms'=>'manage_product_terms','edit_terms'=>'edit_product_terms','delete_terms'=>'delete_product_terms','assign_terms'=>'assign_product_terms']
        ]);
    }
    public static function activate() {
        self::taxonomy();
        if (!get_option('iwpc_seeded')) {
            foreach (['Cesar','Guajira','Magdalena','Bolívar','Santander'] as $name) {
                if (!term_exists($name, self::TAX)) { wp_insert_term($name, self::TAX); }
            }
            update_option('iwpc_seeded', 1, false);
        }
    }
    public static function notice() {
        if (!class_exists('WooCommerce') && current_user_can('activate_plugins')) {
            echo '<div class="notice notice-warning"><p>IW Product Coverage necesita WooCommerce para asignar cobertura a productos.</p></div>';
        }
    }
    public static function assets() {
        // Global, small assets also support shortcodes rendered in Divi templates.
        wp_enqueue_style('iwpc', plugins_url('assets/coverage.css', __FILE__), [], '1.0.0');
        wp_enqueue_script('iwpc', plugins_url('assets/coverage.js', __FILE__), [], '1.0.0', true);
    }
    private static function signature($id) { return hash_hmac('sha256', 'iwpc:'.$id, wp_salt('auth')); }
    public static function shortcode($atts, $content = '') {
        $a = shortcode_atts(['producto_id'=>0,'destino'=>'','etiqueta'=>'Selecciona la región donde necesitas este producto','placeholder'=>'Seleccione una región','sin_cobertura'=>'Este producto no tiene cobertura disponible en ninguna región del país.'], $atts, 'iw_cobertura');
        $id = absint($a['producto_id']);
        if (!$id && is_singular('product')) { $id = get_queried_object_id(); }
        if (!$id || get_post_type($id) !== 'product' || (get_post_status($id) !== 'publish' && !current_user_can('edit_post', $id))) {
            return '<p class="iwpc-message">No se pudo determinar el producto para consultar su cobertura.</p>';
        }
        $terms = wp_get_object_terms($id, self::TAX, ['orderby'=>'name']);
        if (is_wp_error($terms)) { $terms = []; }
        $uid = wp_unique_id('iwpc-');
        $html = '<div class="iwpc" data-product="'.esc_attr($id).'" data-signature="'.esc_attr(self::signature($id)).'" data-target="'.esc_attr($a['destino']).'">';
        if (!$terms) {
            return $html.'<p class="iwpc-message" role="status">'.esc_html($a['sin_cobertura']).'</p></div>';
        }
        $html .= '<label for="'.esc_attr($uid).'">'.esc_html($a['etiqueta']).'</label><select id="'.esc_attr($uid).'" class="iwpc-select" aria-controls="'.esc_attr($uid).'-content"><option value="">'.esc_html($a['placeholder']).'</option>';
        foreach ($terms as $term) { $html .= '<option value="'.esc_attr($term->term_id).'">'.esc_html($term->name).'</option>'; }
        $html .= '</select>';
        if (!$a['destino']) {
            $html .= '<div id="'.esc_attr($uid).'-content" class="iwpc-content" hidden>'.do_shortcode(shortcode_unautop($content)).'</div>';
        }
        return $html.'</div>';
    }
    private static function context() {
        $id = isset($_POST['_iwpc_product']) ? absint($_POST['_iwpc_product']) : 0;
        $sig = isset($_POST['_iwpc_signature']) && is_string($_POST['_iwpc_signature']) ? sanitize_text_field(wp_unslash($_POST['_iwpc_signature'])) : '';
        $region = isset($_POST['_iwpc_region']) ? absint($_POST['_iwpc_region']) : 0;
        if (!$id || !hash_equals(self::signature($id), $sig) || get_post_type($id) !== 'product' || get_post_status($id) !== 'publish' || !$region || !has_term($region, self::TAX, $id)) { return false; }
        $term = get_term($region, self::TAX);
        return $term && !is_wp_error($term) ? $term->name : false;
    }
    public static function posted($data) {
        if (array_key_exists('region-disponible', $data)) {
            $region = self::context();
            $data['region-disponible'] = $region ?: '';
        }
        return $data;
    }
    public static function validate($result, $tags) {
        foreach ($tags as $tag) {
            if ($tag->name === 'region-disponible' && !self::context()) {
                $result->invalidate($tag, 'Seleccione una región con cobertura para este producto.');
            }
        }
        return $result;
    }
}
IW_Product_Coverage::boot();
register_activation_hook(__FILE__, ['IW_Product_Coverage', 'activate']);
