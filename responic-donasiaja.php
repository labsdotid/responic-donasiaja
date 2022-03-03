<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.responic.com
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Responic Donasiaja
 * Plugin URI:        https://www.responic.com
 * Description:       Integrate Responic with donasiaja Plugin
 * Version:           1.0.0
 * Author:            Responic Team
 * Author URI:        https://www.salesloo.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       responic-donasiaja
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('RESPONIC_DONASIAJA_VERSION', '1.0.0');
define('RESPONIC_DONASIAJA_URL', plugin_dir_url(__FILE__));
define('RESPONIC_DONASIAJA_PATH', plugin_dir_path(__FILE__));
define('RESPONIC_DONASIAJA_ROOT', __FILE__);

/**
 * Main salesloo Addon class
 */
class Responic_Donasiaja
{
    /**
     * Instance
     */
    private static $_instance = null;

    /**
     * run
     *
     * @return Responic_Donasiaja An instance of class
     */
    public static function run()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @access public
     */
    public function i18n()
    {
        load_plugin_textdomain(
            'responic-donasiaja',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }

    /**
     * On Plugins Loaded
     *
     * Checks if Salesloo has loaded, and performs some compatibility checks.
     *
     * @access public
     */
    public function on_plugins_loaded()
    {

        if ($this->is_compatible()) {
            $this->i18n();

            require_once RESPONIC_DONASIAJA_PATH . 'includes/admin.php';
            require_once RESPONIC_DONASIAJA_PATH . 'includes/setting.php';
            require_once RESPONIC_DONASIAJA_PATH . 'includes/rest-api.php';

            $admin = new \Responic_Donasiaja\Admin();

            $restapi = new \Responic_Donasiaja\Rest_Api();

            add_action('admin_menu', [$admin, 'menu']);
            add_action('admin_init', [$admin, 'on_save']);

            add_action('wp_ajax_djafunction_submit_donasi', [$this, 'test']);
            add_action('wp_ajax_nopriv_djafunction_submit_donasi', [$this, 'test']);
            add_action('rest_api_init', [$restapi, 'register_route']);
        }
    }

    public function test()
    {
        echo 'mantapp';
        exit;
    }

    /**
     * Compatibility Checks
     *
     * @access public
     */
    public function is_compatible()
    {
        // Check if Donasiaja installed and activated
        if (!function_exists('dja_options_install')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return false;
        }

        return true;
    }


    /**
     * Admin notice
     *
     * Warning when the site doesn't have Donasiaja installed or activated.
     *
     * @access public
     */
    public function admin_notice_missing_main_plugin()
    {

        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
            /* translators: 1: Responic Donasiaja 2: Donasiaja */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'responic-donasiaja'),
            '<strong>' . esc_html__('Responic Donasiaja', 'responic-donasiaja') . '</strong>',
            '<strong>' . esc_html__('Donasiaja', 'responic-donasiaja') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}

/**
 * debuging function
 */
if (!function_exists('__debug')) {
    function __debug()
    {
        echo '<pre>';
        print_r(func_get_args());
        echo '</pre>';
    }
}

Responic_Donasiaja::run();
