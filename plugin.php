<?php
/**
 * The plugin bootstrap file.
 *
 * @link
 * @since
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Admin Task
 * Plugin URI:
 * Description:       Task manager for administrator
 * Version:           1.0.0
 * Author:            Alireza Jafari
 * Author URI:        https://alirezacrr.ir
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       atl
 * Domain Path:       /languages
 */

defined('ABSPATH') || exit;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!defined('ATL_FILE')) {
    define('ATL_FILE', __FILE__);
}
$plugin_version = '1.0.0';
$db_version = '1.0.0';
/**
 * ATL Version Define
 */
define('ATL_VERSION', $plugin_version);
define('ATL_DB_VERSION', $plugin_version);
define('ATL_ABSPATH', dirname(ATL_FILE) . '/');

if (!class_exists('ATL', false)) {
    include_once dirname(ATL_FILE) . '/inc/admin-task-list.php';
    ATL::instance();
}

