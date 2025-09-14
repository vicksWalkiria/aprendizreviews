<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews {
    
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->plugin_name = 'aprendiz-reviews';
        $this->version = APRENDIZ_REVIEWS_VERSION;
        
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        // Cargar loader
        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'includes/class-loader.php';
        
        // Cargar activator y deactivator
        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'includes/class-activator.php';
        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'includes/class-deactivator.php';
        
        // Cargar admin
        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'admin/class-admin.php';
        
        // Cargar public
        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'public/class-public.php';
        
        // Cargar models
        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'models/class-product.php';
        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'models/class-review.php';
        
        // Cargar controllers
        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'controllers/class-product-controller.php';
        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'controllers/class-review-controller.php';
        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'controllers/class-ajax-controller.php';

        require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'controllers/class-import-controller.php';
        
        $this->loader = new Aprendiz_Reviews_Loader();
    }

    private function define_admin_hooks() {
        $plugin_admin = new Aprendiz_Reviews_Admin($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_init', $plugin_admin, 'migrate_data');
    }

    private function define_public_hooks() {
        $plugin_public = new Aprendiz_Reviews_Public($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('init', $plugin_public, 'register_shortcodes');
        
        // AJAX hooks
        $ajax_controller = new Aprendiz_Reviews_Ajax_Controller();
        $this->loader->add_action('wp_ajax_submit_review_frontend', $ajax_controller, 'handle_frontend_review');
        $this->loader->add_action('wp_ajax_nopriv_submit_review_frontend', $ajax_controller, 'handle_frontend_review');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }
}
