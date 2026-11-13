<?php
/**
 * Plugin Name: IW MegaMenu Divi
 * Plugin URI: https://ingeweb.co/
 * Description: Mega menús por columnas o enlaces con contenido de Divi Library para la cabecera Divi de IngeWeb.
 * Version: 1.0.0
 * Author: IngeWeb.co
 * Author URI: https://ingeweb.co/
 * Text Domain: iw-megamenu-divi
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */
defined('ABSPATH') || exit;

final class IW_MegaMenu_Divi {
    private static $rendered = array();
    public static function settings($id) {
        $data = get_post_meta($id, '_iw_megamenu', true);
        return wp_parse_args(is_array($data) ? $data : array(), array('enabled' => 0, 'mode' => 'columns', 'layout' => 0, 'width' => 25));
    }
    public static function boot() {
        add_action('wp_nav_menu_item_custom_fields', array(__CLASS__, 'fields'), 10, 5);
        add_action('wp_update_nav_menu_item', array(__CLASS__, 'save'), 10, 2);
        add_filter('wp_nav_menu_args', array(__CLASS__, 'walker'), 30);
        add_filter('nav_menu_css_class', array(__CLASS__, 'classes'), 20, 4);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'assets'), 99);
    }
    public static function fields($id, $item, $depth, $args, $current_object_id) {
        $s = self::settings($id);
        $layouts = get_posts(array('post_type' => 'et_pb_layout', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
        $name = 'iw_megamenu[' . absint($id) . ']';
        echo '<div class="description description-wide" style="padding:12px 0;border-top:1px solid #ddd"><strong>IW MegaMenu Divi</strong><p>Configura solo elementos principales. Los hijos con subelementos forman columnas; los enlaces sueltos se agrupan a la izquierda.</p>';
        echo '<input type="hidden" name="iw_mega_nonce[' . absint($id) . ']" value="' . esc_attr(wp_create_nonce('iw_mega_' . $id)) . '">';
        echo '<p><label><input type="checkbox" name="' . esc_attr($name) . '[enabled]" value="1" ' . checked($s['enabled'], 1, false) . '> Activar mega menú</label></p>';
        echo '<p><label>Diseño<br><select name="' . esc_attr($name) . '[mode]" class="widefat">';
        foreach (array('columns' => 'Columnas de enlaces', 'mixed' => 'Enlaces a la izquierda + Divi Library') as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($s['mode'], $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select></label></p><p><label>Contenido de Divi Library<br><select name="' . esc_attr($name) . '[layout]" class="widefat"><option value="0">— Sin contenido adicional —</option>';
        foreach ($layouts as $layout) {
            if (!self::valid_layout($layout->ID)) continue;
            echo '<option value="' . absint($layout->ID) . '" ' . selected($s['layout'], $layout->ID, false) . '>' . esc_html($layout->post_title) . '</option>';
        }
        echo '</select></label></p><p><label>Ancho de enlaces (diseño mixto)<br><select name="' . esc_attr($name) . '[width]">';
        foreach (array(25, 33, 40, 50) as $width) {
            echo '<option value="' . $width . '" ' . selected($s['width'], $width, false) . '>' . $width . ' %</option>';
        }
        echo '</select></label></p><p>Edita el contenido visual desde Divi → Biblioteca Divi. En móvil se muestran los enlaces del menú.</p></div>';
    }
    public static function save($menu_id, $id) {
        if (!current_user_can('edit_theme_options') || empty($_POST['iw_mega_nonce'][$id]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['iw_mega_nonce'][$id])), 'iw_mega_' . $id)) return;
        $s = isset($_POST['iw_megamenu'][$id]) ? (array) wp_unslash($_POST['iw_megamenu'][$id]) : array();
        $layout = isset($s['layout']) ? absint($s['layout']) : 0;
        if ($layout && !self::valid_layout($layout)) $layout = 0;
        update_post_meta($id, '_iw_megamenu', array(
            'enabled' => empty($s['enabled']) ? 0 : 1,
            'mode' => isset($s['mode']) && $s['mode'] === 'mixed' ? 'mixed' : 'columns',
            'layout' => $layout,
            'width' => isset($s['width']) && in_array((int) $s['width'], array(25, 33, 40, 50), true) ? (int) $s['width'] : 25,
        ));
    }
    public static function classes($classes, $item, $args, $depth) {
        $s = self::settings($item->ID);
        if ($depth === 0 && $s['enabled']) {
            $classes[] = 'iw-mega-menu';
            $classes[] = 'iw-mega-' . $s['mode'];
            if ($s['layout']) $classes[] = 'menu-item-has-children';
            // El plugin controla la distribución; evita reglas mega-menu de Divi.
            $classes = array_diff($classes, array('mega-menu'));
        }
        return array_unique($classes);
    }
    public static function walker($args) {
        // Conserva walkers personalizados de otros plugins y menús ajenos a Divi.
        if (empty($args['walker']) && strpos($args['menu_class'] ?? '', 'et-menu') !== false) {
            $args['walker'] = new IW_MegaMenu_Divi_Walker();
        }
        return $args;
    }
    public static function valid_layout($id, $ancestors = array()) {
        $id = absint($id);
        $post = get_post($id);
        if (!$post || $post->post_type !== 'et_pb_layout' || $post->post_status !== 'publish' || in_array($id, $ancestors, true) || count($ancestors) > 10) return false;
        $ancestors[] = $id;
        preg_match_all('/(?:global_module|divi_library)\\s*=\\s*["\x27](\\d+)["\x27]/', $post->post_content, $matches);
        foreach ($matches[1] as $reference) {
            if (!self::valid_layout((int) $reference, $ancestors)) return false;
        }
        return true;
    }
    public static function layout($id) {
        $id = absint($id);
        if (!$id || !self::valid_layout($id) || !function_exists('et_builder_render_layout')) return '';
        if (!isset(self::$rendered[$id])) {
            self::$rendered[$id] = ''; // Evita recursión si un diseño contiene el mismo menú.
            self::$rendered[$id] = et_builder_render_layout('[et_pb_section global_module="' . $id . '"][/et_pb_section]');
        }
        return self::$rendered[$id];
    }
    public static function assets() {
        $base = plugin_dir_url(__FILE__) . 'assets/';
        wp_enqueue_style('iw-megamenu-divi', $base . 'mega-menu.css', array(), filemtime(__DIR__ . '/assets/mega-menu.css'));
        wp_enqueue_script('iw-megamenu-divi', $base . 'mega-menu.js', array(), filemtime(__DIR__ . '/assets/mega-menu.js'), true);
        // Renderizar antes de wp_head permite a Divi registrar estilos y scripts de módulos.
        $ids = get_posts(array('post_type' => 'nav_menu_item', 'post_status' => 'publish', 'numberposts' => -1, 'meta_key' => '_iw_megamenu', 'fields' => 'ids'));
        foreach ($ids as $id) {
            $s = self::settings($id);
            if ($s['enabled'] && $s['mode'] === 'mixed' && $s['layout']) self::layout($s['layout']);
        }
    }
}

class IW_MegaMenu_Divi_Walker extends Walker_Nav_Menu {
    private $settings;
    private $parent_item;
    private $panel = false;
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if ($depth === 0) {
            $this->parent_item = $item;
            $this->settings = IW_MegaMenu_Divi::settings($item->ID);
            $this->panel = false;
        }
        parent::start_el($output, $item, $depth, $args, $id);
    }
    private function open(&$output) {
        $this->panel = true;
        $output .= '<ul class="sub-menu iw-mega-panel" style="--iw-mega-links-width:' . absint($this->settings['width']) . '%">';
        if ($this->settings['mode'] === 'mixed') {
            $output .= '<li class="iw-mega-links menu-item"><a class="iw-mega-title" href="' . esc_url($this->parent_item->url) . '">' . esc_html(wp_strip_all_tags($this->parent_item->title)) . '</a><ul class="iw-mega-link-list">';
        }
    }
    public function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0 && $this->settings['enabled']) $this->open($output);
        else parent::start_lvl($output, $depth, $args);
    }
    private function finish(&$output) {
        if ($this->settings['mode'] === 'mixed') {
            $output .= '</ul></li><li class="iw-mega-content"><div class="et-l et-l--post"><div class="et_builder_inner_content et_pb_gutters3">' . IW_MegaMenu_Divi::layout($this->settings['layout']) . '</div></div></li>';
        }
        $output .= '</ul>';
    }
    public function end_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0 && $this->settings['enabled']) $this->finish($output);
        else parent::end_lvl($output, $depth, $args);
    }
    public function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth === 0 && $this->settings['enabled'] && !$this->panel && $this->settings['mode'] === 'mixed' && $this->settings['layout']) {
            $this->open($output);
            $this->finish($output);
        }
        parent::end_el($output, $item, $depth, $args);
    }
}
IW_MegaMenu_Divi::boot();
