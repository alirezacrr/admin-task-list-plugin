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
            add_action('wp_ajax_table', array($this,'get_task_data'));

        }

        public function get_task_data()
        {
            if ( ! check_ajax_referer( 'ajax-nonce', 'security' ) ) {
                wp_send_json_error( 'Invalid security token sent.' );
                wp_die();
            }

            global $wpdb;
            $admin_message = $wpdb->prefix . 'atl_admin_message';
            $admin_users_message = $wpdb->prefix . 'atl_admin_users_message';
            $users=$wpdb->prefix . "users";
            //
            $userID = $_POST['id'];
            $table = "wp_admin_" . $_POST['table'];
            $limit = 10;
            $page = $_POST['page'];
            $offset = ($page - 1) * $limit;
            $_where = "$admin_users_message.status
     = 0 or $admin_users_message.status =2 ";
            if ($_POST['filter'] != '') {
                if ($_POST['filter'] === "all") {
                    $_where = $_where . "or $admin_users_message.status =1 ";
                } else {
                    $rule = $table . "." . $_POST['filter'] . "=" . $userID;
                    $_where = $rule . " " . "and" . " " . $_where;
                }

            }

            $querystr = "SELECT tb1.* , $users.user_nicename as sender_name , $users.user_email  FROM (SELECT $admin_users_message.* ,
    $admin_message.* , $users.user_nicename as
    receiver_name FROM $admin_users_message LEFT JOIN $users ON $users.ID = $admin_users_message.user_id
    LEFT JOIN $admin_message ON $admin_users_message.msg_id = $admin_message.id WHERE " . $_where . ")
     as tb1 LEFT JOIN $users on tb1.creator_id = $users.ID LIMIT $offset , $limit";
            $data = $wpdb->get_results($querystr);

            $total = $wpdb->get_var("select count(*) as total from $admin_users_message LEFT JOIN $admin_message ON
    $admin_users_message.msg_id = $admin_message.id  WHERE $_where");

            $num_of_pages = ceil($total / $limit);
            wp_send_json(array($data, $num_of_pages));
        }
        public function save_msg()
        {
//            if (!check_ajax_referer('ajax-nonce', 'security')) {
//                wp_send_json_error('Invalid security token sent.');
//                wp_die();
//            }
            $users = $_POST['users_id'];
            if (empty($users)){
                $args = array(
                    'role' => 'Administrator',
                    'fields' => 'ID'
                );
                $users = get_users($args);
            }
            global $wpdb;
            $msg_table =$wpdb->prefix . 'atl_admin_message';
            $users_msg_table =$wpdb->prefix . 'atl_admin_users_message';
            $wpdb->insert(
                $msg_table,
                array(
                    'time_create' => current_time('mysql'),
                    'creator_id' => get_current_user_id(),
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                )
            );
            $lastid = $wpdb->insert_id;
            if (!empty($users)) {
                foreach ($users as $user_id) {
                    $wpdb->insert(
                        $users_msg_table,
                        array(
                            'user_id' => $user_id,
                            'msg_id' => $lastid
                        )
                    );
                };
            }
            wp_die();
        }

        public function status_msg()
        {
//            if (!check_ajax_referer('ajax-nonce', 'security')) {
//                wp_send_json_error('Invalid security token sent.');
//                wp_die();
//            }
            global $wpdb;
            $status= $_POST['status'];
            $msgId = $_POST['msg_id'];
            $admin_message =  $wpdb->prefix . 'atl_admin_message';
            $res = $wpdb->update($admin_message, array('time_edit' => current_time('mysql'),'status'=>$status,'done_by'=>get_current_user_id()), array('id' => $msgId));
            if ($res){
                wp_send_json(true);
            }else{
                wp_send_json_error(false);
            }
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
            if (is_rtl()){
                wp_enqueue_style('atl_admin_css_rtl',
                    plugins_url('../assets/css/rtl.css', __FILE__),
                    array(),
                    ATL_VERSION
                );
            }

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
                'nonce' => wp_create_nonce('ajax-nonce'),
                'l10n' => array(
                    'y' => __('year','atl'),
                    'm' => __('month','atl'),
                    'w' => __('week','atl'),
                    'd' => __('day','atl'),
                    'h' => __('hours','atl'),
                    'i' => __('min','atl'),
                    's' => __('sec','atl'),
                    'ago' => __('ago','atl'),
                )
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