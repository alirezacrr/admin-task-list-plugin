<?php


if (!class_exists('ATL_DB')) {
    class ATL_DB
    {
        protected static $instance;

        /**
         * Items table name
         *
         * @var string
         * @access private
         * @since 1.0.0
         */
        private $_table_prefix;
        private $_table_admin_message;
        private $_table_admin_users_message;

        private $_num_tables;

        /**
         * Returns single instance of the class
         *
         * @return ATL_DB
         * @since 1.0.0
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Constructor.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            global $wpdb;


            $this->_num_tables = 2;

            $this->_table_prefix = $wpdb->prefix . 'atl_';
            $this->_table_admin_message = $wpdb->prefix . 'atl_admin_message';
            $this->_table_admin_users_message = $wpdb->prefix . 'atl_admin_users_message';

            $wpdb->atl_table_admin_message = $this->_table_admin_message;
            $wpdb->atl_table_admin_users_message = $this->_table_admin_users_message;


            define('ATL_ADMIN_MESSAGE_TABLE', $this->_table_admin_message);
            define('ATL_ADMIN_USERS_MESSAGE_TABLE', $this->_table_admin_users_message);


        }

        /**
         * Init db structure of the plugin
         */
        public function init()
        {
            $this->_add_tables();
            $this->register_current_version();
        }

        /**
         * Update db structure of the plugin
         *
         * @param string $current_version Version from which we're updating.
         */

        public function update($current_version)
        {
            if (version_compare($current_version, '1.0.0', '<')) {
                $this->_update_100();
            }

            $this->register_current_version();
        }

        /**
         * Register current version of plugin and database sctructure
         *
         */
        public function register_current_version()
        {
            ATL_Helper::update_or_create_option('atl_db_version', ATL_DB_VERSION);
        }

        /**
         * Check if the table of the plugin already exists.
         *
         * @return bool
         * @since 1.0.0
         */
        public function is_installed()
        {
            global $wpdb;
            $number_of_tables = $wpdb->query($wpdb->prepare('SHOW TABLES LIKE %s', "{$this->_table_prefix}%"));
            return (bool)($this->_num_tables == $number_of_tables);
        }

        /**
         * Update from 0.x to 1.0
         */
        private function _update_100()
        {
            flush_rewrite_rules();
            $this->_add_tables();

        }


        /**
         * Add tables for a fresh installation
         *
         * @return void
         * @access private
         * @since 1.0.0
         */
        private function _add_tables()
        {
            $this->_add_message();
            $this->_add_users_message();
        }


        private function _add_message()
        {
            global $wpdb;
            if (!$this->is_installed() || version_compare(get_option('atl_db_version'), '1.0.0', '<')) {
                $sql = "CREATE TABLE {$this->_table_admin_message} (
         id int   NOT NULL  AUTO_INCREMENT,
         creator_id mediumint(9) NOT NULL ,
         time_create datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         time_edit datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         title  varchar(40) DEFAULT '' NOT NULL,
         description  text DEFAULT '' NOT NULL,
         status varchar(20) DEFAULT 'pending' NOT NULL,
         done_by  mediumint(9) ,
         		  UNIQUE KEY id (id)
				)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }

        private function _add_users_message()
        {
            global $wpdb;
            if (!$this->is_installed() || version_compare(get_option('atl_db_version'), '1.0.0', '<')) {
                $sql = "CREATE TABLE {$this->_table_admin_users_message} (
		   user_id mediumint(9) NOT NULL ,
        msg_id mediumint(9) NOT NULL 
						)DEFAULT CHARSET=utf8; ";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }


    }
}

/**
 *
 * @return ATL_DB
 * @since 1.0.0
 */
function ATL_DB()
{
    return ATL_DB::get_instance();
}