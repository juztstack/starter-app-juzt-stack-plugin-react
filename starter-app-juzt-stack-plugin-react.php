<?php

/**
 * Plugin Name: starter-app-juzt-stack-plugin-react-v1
 * Plugin URI: https://github.com/juztstack/starter-app-juzt-stack-plugin-react
 * Description: Example for Application react based on plugin wordpress
 * Version: 1.0.0
 * Author: Jesus Uzcategui
 * Author URI: https://github.com/juztstack
 * License: MIT License
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: starter-app-juzt-stack-plugin-react
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MY_REACT_IS_DEV_MODE', false);
define('MY_REACT_APP_PATH', plugin_dir_path(__FILE__));
define('MY_REACT_APP_URL', plugin_dir_url(__FILE__));
define('MY_REACT_APP_BASEPATH', 'buy');
define('MY_REACT_APP_QUERY_PARAM', 'react_app');
define('VITE_DEV_SERVER_PORT', '5173');
define('VITE_DEV_SERVER_URL', 'http://localhost:' . VITE_DEV_SERVER_PORT);

class My_React_App_Plugin
{

    public function __construct()
    {
        add_action('init', array($this, 'register_routes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_react_app'));
        add_filter('template_include', array($this, 'load_react_template'));
        if(MY_REACT_IS_DEV_MODE){
            add_filter('script_loader_tag', array($this, 'add_module_type_to_vite_scripts'), 10, 2);
        }
    }

    public function add_module_type_to_vite_scripts($tag, $handle)
    {
        if ($handle === 'vite-client' || $handle === 'react-app-dev') {
            if (strpos($tag, 'type="module"') === false) {
                $tag = str_replace(' src=', ' type="module" src=', $tag);
            }
        }
        return $tag;
    }

    public function register_routes()
    {

        add_rewrite_rule('^' . MY_REACT_APP_BASEPATH . '(/.*)?$', 'index.php?' . MY_REACT_APP_QUERY_PARAM . '=1', 'top');
        add_rewrite_tag('%' . MY_REACT_APP_QUERY_PARAM . '%', '([^&]+)');

        if (get_option('my_react_app_flush_rewrite_rules') === 'yes') {
            flush_rewrite_rules();
            update_option('my_react_app_flush_rewrite_rules', 'no');
        }
    }

    public function enqueue_react_app()
    {

        if (!get_query_var(MY_REACT_APP_QUERY_PARAM)) {
            return;
        }


        $manifest_path = MY_REACT_APP_PATH . 'assets/.vite/manifest.json';

        if (!file_exists($manifest_path)) {
            return;
        }

        if (MY_REACT_IS_DEV_MODE) {
            wp_enqueue_script(
                'vite-client',
                VITE_DEV_SERVER_URL . '/@vite/client',
                array(),
                null,
                true
            );
            wp_enqueue_script(
                'react-app-dev',
                VITE_DEV_SERVER_URL . '/src/main.jsx',
                array(),
                null,
                true
            );

            $handle = 'react-app-dev';

        } else {

            $manifest = json_decode(file_get_contents($manifest_path), true);


            if (isset($manifest['src/main.jsx']['css'])) {
                foreach ($manifest['src/main.jsx']['css'] as $css_file) {
                    wp_enqueue_style(
                        'react-app-style',
                        MY_REACT_APP_URL . 'assets/' . $css_file,
                        array(),
                        null
                    );
                }
            }


            if (isset($manifest['src/main.jsx']['file'])) {
                wp_enqueue_script(
                    'react-app-script',
                    MY_REACT_APP_URL . 'assets/' . $manifest['src/main.jsx']['file'],
                    array(),
                    null,
                    true
                );
            }

            $handle = 'react-app-script';
        }

        wp_localize_script($handle, 'wpData', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('react_app_nonce'),
                'basePath' => '/' . MY_REACT_APP_BASEPATH,
            ));
    }

    public function load_react_template($template)
    {
        if (get_query_var(MY_REACT_APP_QUERY_PARAM)) {
            return MY_REACT_APP_PATH . 'includes/template-react.php';
        }
        return $template;
    }
}


new My_React_App_Plugin();


register_activation_hook(__FILE__, function () {
    update_option('my_react_app_flush_rewrite_rules', 'yes');
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});
