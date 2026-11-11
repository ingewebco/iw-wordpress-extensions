<?php
/**
 * Plugin Name: IW WhatsApp Button
 * Description: Botón flotante de WhatsApp para todo WordPress, sin depender de WooCommerce.
 * Version: 1.1.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: IngeWeb
 */

if (!defined('ABSPATH')) {
    exit;
}

// Se conserva la clase, ruta y opción originales para actualizar sin perder el teléfono.
class WC_WhatsApp_Button_Pro {
    private $option_name = 'wc_whatsapp_phone';

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_footer', [$this, 'render_button']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function sanitize_phone($value) {
        return is_scalar($value) ? preg_replace('/[^0-9]/', '', (string) $value) : '';
    }

    public function enqueue_styles() {
        if (!$this->sanitize_phone(get_option($this->option_name, ''))) {
            return;
        }
        wp_enqueue_style('iw-whatsapp-button', plugin_dir_url(__FILE__) . 'assets/button.css', [], '1.1.0');
    }

    public function render_button() {
        $phone = $this->sanitize_phone(get_option($this->option_name, ''));
        if (!$phone) {
            return;
        }
        $message = get_option('iw_whatsapp_message', 'Hola, me gustaría recibir más información.');
        $url = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
        ?>
        <a class="iw-whatsapp-button" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Contactar por WhatsApp (abre en una nueva pestaña)" title="Contactar por WhatsApp">
            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'assets/whatsapp.svg'); ?>" width="32" height="32" alt="" aria-hidden="true">
        </a>
        <?php
    }

    public function add_admin_menu() {
        add_options_page('WhatsApp Button', 'WhatsApp Button', 'manage_options', 'wc-whatsapp-button', [$this, 'settings_page']);
    }

    public function register_settings() {
        register_setting('wc_whatsapp_settings_group', $this->option_name, [
            'type' => 'string',
            'sanitize_callback' => [$this, 'sanitize_phone'],
            'default' => '',
        ]);
        register_setting('wc_whatsapp_settings_group', 'iw_whatsapp_message', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'Hola, me gustaría recibir más información.',
        ]);
    }

    public function settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>IW WhatsApp Button</h1>
            <p>Botón flotante en la esquina inferior derecha de todo el sitio. No requiere WooCommerce.</p>
            <form method="post" action="options.php">
                <?php settings_fields('wc_whatsapp_settings_group'); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="iw-whatsapp-phone">Número de WhatsApp</label></th>
                        <td>
                            <input id="iw-whatsapp-phone" type="text" inputmode="tel" class="regular-text" name="<?php echo esc_attr($this->option_name); ?>" value="<?php echo esc_attr(get_option($this->option_name, '')); ?>" placeholder="573001234567">
                            <p class="description">Incluye el código de país. Se guardan únicamente los dígitos. Deja vacío para ocultar el botón.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="iw-whatsapp-message">Mensaje inicial</label></th>
                        <td><textarea id="iw-whatsapp-message" name="iw_whatsapp_message" rows="4" class="large-text"><?php echo esc_textarea(get_option('iw_whatsapp_message', 'Hola, me gustaría recibir más información.')); ?></textarea></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

new WC_WhatsApp_Button_Pro();
