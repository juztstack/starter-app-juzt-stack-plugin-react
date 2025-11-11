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

define('MY_REACT_APP_PATH', plugin_dir_path(__FILE__));
define('MY_REACT_APP_URL', plugin_dir_url(__FILE__));
define('MY_REACT_APP_BASEPATH', 'buy');
define('MY_REACT_APP_QUERY_PARAM', 'react_app');

class My_React_App_Plugin {
    
    public function __construct() {
        add_action('init', array($this, 'register_routes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_react_app'));
        add_filter('template_include', array($this, 'load_react_template'));
    }
    
    public function register_routes() {
        
        add_rewrite_rule('^' . MY_REACT_APP_BASEPATH . '(/.*)?$', 'index.php?' . MY_REACT_APP_QUERY_PARAM . '=1', 'top');
        add_rewrite_tag('%' . MY_REACT_APP_QUERY_PARAM . '%', '([^&]+)');
        
        if (get_option('my_react_app_flush_rewrite_rules') === 'yes') {
            flush_rewrite_rules();
            update_option('my_react_app_flush_rewrite_rules', 'no');
        }
    }
    
    public function enqueue_react_app() {
        
        if (!get_query_var('react_app')) {
            return;
        }
        
        
        $manifest_path = MY_REACT_APP_PATH . 'assets/.vite/manifest.json';
        
        if (!file_exists($manifest_path)) {
            return;
        }
        
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
                MY_REACT_APP_URL . 'assets/' .$manifest['src/main.jsx']['file'],
                array(),
                null,
                true
            );
        }
        
        
        wp_localize_script('react-app-script', 'wpData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('react_app_nonce'),
            'basePath' => '/' . MY_REACT_APP_BASEPATH,
        ));
    }
    
    public function load_react_template($template) {
        if (get_query_var('react_app')) {
            return MY_REACT_APP_PATH . 'includes/template-react.php';
        }
        return $template;
    }
}


new My_React_App_Plugin();


register_activation_hook(__FILE__, function() {
    update_option('my_react_app_flush_rewrite_rules', 'yes');
});

register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});