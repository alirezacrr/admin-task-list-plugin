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
            $this->includes();
        }

        public function includes()
        {
            include_once ATL_ABSPATH . 'inc/class-db-install.php';
            include_once ATL_ABSPATH . 'inc/helper.php';

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
            add_action('admin_footer', array($this, 'modals'));
            add_action('wp_ajax_status', array($this, 'status_msg'));
            add_action('wp_ajax_save', array($this, 'save_msg'));


        }

        public function save_msg()
        {
            if (!check_ajax_referer('ajax-nonce', 'security')) {
                wp_send_json_error('Invalid security token sent.');
                wp_die();
            }
            $args = array(
                'role' => 'Administrator'
            );
            $users = get_users($args);
            global $wpdb;
            $table_name = $wpdb->prefix . 'admin_message';
            $table_name_2 = $wpdb->prefix . 'admin_users_message';
            $current_user = wp_get_current_user();
            $wpdb->insert(
                $table_name,
                array(
                    'time_create' => current_time('mysql'),
                    'creator_id' => $current_user->id,
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                )
            );
            $lastid = $wpdb->insert_id;
            if ($_POST['user_id'] === '0' || null) {
                foreach ($users as $user) {
                    $wpdb->insert(
                        $table_name_2,
                        array(
                            'user_id' => $user->ID,
                            'msg_id' => $lastid,
                            'status' => 0,
                        )
                    );
                };
            } else {
                $wpdb->insert(
                    $table_name_2,
                    array(
                        'user_id' => $_POST['user_id'],
                        'msg_id' => $lastid,
                        'status' => 0,
                    )
                );
            }
            wp_die();


        }

        public function status_msg()
        {
            if (!check_ajax_referer('ajax-nonce', 'security')) {
                wp_send_json_error('Invalid security token sent.');
                wp_die();
            }
            global $wpdb;

            $newStatus = $_POST['status'];
            $currentId = $_POST['user_id'];
            $msgId = $_POST['msg_id'];
            $table = $wpdb->prefix . 'admin_users_message';
            $table_msg = $wpdb->prefix . 'admin_message';
            $wpdb->update($table, array('status' => $newStatus), array('user_id' => $currentId, 'msg_id' => $msgId));
            $wpdb->update($table_msg, array('time_edit' => current_time('mysql')), array('id' => $msgId));

            wp_die();
        }

        public function modals()
        {
            return include_once ATL_ABSPATH . 'templates/modal.php';
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
            wp_enqueue_style('atl_font_awesome_css',
                plugins_url('../assets/css/font-awesome.min.css', __FILE__),
                array(),
                ATL_VERSION
            );
            wp_enqueue_style('atl_font_iranyekan_css',
                plugins_url('../assets/fonts/iranyekan/font-iranyekan.css', __FILE__),
                array(),
                ATL_VERSION
            );
            wp_enqueue_style('atl_select2-bootstrap4.css',
                plugins_url('../assets/css/select2-bootstrap4.css', __FILE__),
                array(),
                ATL_VERSION
            );
            wp_enqueue_style('atl_select2_css',
                plugins_url('../assets/css/select2.min.css', __FILE__),
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
            wp_enqueue_script('atl_select2_js',
                plugins_url('../assets/js/select2.min.js', __FILE__),
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