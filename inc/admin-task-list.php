<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ATL')) {
    class ATL
    {
        /**
         * @var ATL
         */
        private static $_instance;

        /**
         *  Constructor.
         */
        public function __construct()
        {
            $this->init_hooks();
        }

        /**
         * Main Appino Instance.
         *
         * Ensures only one instance of Appino is loaded or can be loaded.
         *
         * @return ATL - Main instance.
         * @static
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function init_hooks()
        {
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));
            add_action('init', array($this, 'install'));

        }
        public function install()
        {
            if (wp_doing_ajax()) {
                return;
            }
            $stored_db_version = get_option('atl_db_version');
            if (!$stored_db_version || !ATL_DB()->is_installed()) {
                // fresh installation.
                ATL_DB()->init();
            } elseif (version_compare($stored_db_version, ATL_DB_VERSION, '<')) {
                // update database.
                ATL_DB()->update($stored_db_version);
            }
        }

        public function admin_enqueue()
        {
            wp_enqueue_style('atl_bootstrap_css',
                plugins_url('../assets/css/bootstrap.min.css', __FILE__),
                array(),
                ATL_VERSION
            );
            wp_enqueue_style('atl_font_awesome_css',
                plugins_url('../assets/css/font-awesome.min.css', __FILE__),
                array(),
                ATL_VERSION
            );
            wp_enqueue_style('atl_admin_css',
                plugins_url('../assets/css/style.css', __FILE__),
                array(),
                ATL_VERSION
            );

            wp_enqueue_script('atl_md5_js',
                plugins_url('../assets/js/md5.min.js', __FILE__),
                array('jquery'),
                ATL_VERSION,
                true
            );
            wp_enqueue_script('atl_functions_js',
                plugins_url('../assets/js/functions.js', __FILE__),
                array('jquery'),
                ATL_VERSION,
                true
            );
            wp_localize_script('atl_functions_js', 'ajax_var', array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ajax-nonce')
            ));
        }
        /**
         * Define constant if not already set.
         *
         * @param string $name Constant name.
         * @param string|bool $value Constant value.
         */
        private function define($name, $value)
        {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }
}