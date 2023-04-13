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
 * Text Domain:       admin-tl
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



// add sub menu in dashboard
add_action('admin_menu', 'my_plugin_menu');

function my_plugin_menu()
{
    add_dashboard_page('Admin message ', 'Admin message', 'manage_options', 'my-unique-identifier', 'my_plugin_function');
}

function my_plugin_function()
{
    ?>
    <div class="wrap">
        <h2>جدول نمایش پیام ها</h2>
    </div>
    <div id="table_messages">
        <table>
            <tr>
                <td>
                    <select id='filter'>
                        <option value=''> پیغام های همه کاربران</option>
                        <option data-table="users_message" value='user_id'> پیغام های ارسالی به کاربر</option>
                        <option data-table="message" value='creator_id'> پیغام های ارسال شده توسط کاربر
                        </option>
                        <option value='all'> همه ی پیغام ها با همه ی وضعیت ها</option>
                    </select>
                </td>
                <?php

                $args = array(
                    'role' => 'Administrator'
                );
                $users = get_users($args);

                ?>
                <td>
                    <select id='searchByuser' style="display: none">
                        <?php foreach ($users as $user) : ?>
                            <option value="<?php echo esc_html($user->ID) ?>">
                                <?php echo esc_html($user->display_name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <button class="btn pointer" id="setFilter_btn">صافی</button>
                </td>

            </tr>
        </table>

        <!-- Table -->
        <table id='customers' class='display dataTable'>
            <thead>
            <tr>
                <th>فرستنده</th>
                <th>گیرنده</th>
                <th>عنوان</th>
                <th>زمان</th>
            </tr>
            </thead>
            <tbody id="tableBox" class="have-modal">
            </tbody>

        </table>
        <div id="pagination">
            <button id="next_btn" class="pagination_btn"  data-btn="next">&#8249;</button>
            <button id="previous_btn" class="pagination_btn"  data-btn="previous">&#8250;</button>
        </div>


    </div>
    <!--  modal show  message for this table -->
    <section class="wrp">
        <div class="wf-modal" aria-hidden="true" id="modal-for-table">
            <article class="wf-dialog-modal">
                <div class="content-dialog content-msg">
                    <span class="close"><a class="pointer exit closeModal"
                                           aria-hidden="true">بستن</a></span>
                    <div class="warp-msg">
                        <header class="header-show-msg">
                            <div class="row-msg">
                                <div class="info-msg">
                                    <div class="avatar-msg" id="avatar-msgTable">
                                    </div>
                                    <div class="header-msg">
                                        <div class="name-msg" id="name-msgTable">

                                        </div>
                                        <div class="title-msg">
                                            <a id="title-msgTable">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="time_btn_msg tbm">
                                    <span id="time-msgTable">
                                    </span>
                                </div>
                            </div>

                        </header>
                        <div class="content-show-msg">
                            <div class="description-msg description-show-msg">
                                <p id="description-msgTable"></p>
                            </div>
                        </div>
                    </div>

                </div>
            </article>
        </div>
    </section>
    <div id="my_whatever"></div>

    <script>
        jQuery(document).ready(function ($) {
            var user_sb, filter_sb, nameTable_sb, numPage, allPage;
            // set filter sub menu
            setFilter()
            $("#setFilter").on('click', function () {
                setFilter();
                getMsgTable()
            });
            function setFilter () {
                user_sb = $('#searchByuser').find(":selected").val();
                filter_sb = $('#filter').find(":selected").val();
                nameTable_sb = $('#filter').find(":selected").data('table');
                numPage = 1;
                allPage = 1;
                getMsgTable()
            }
// get message sub menu ajax
            function getMsgTable(pageBtn) {
                if (numPage < allPage || numPage === allPage) {
                    if (pageBtn === 'previous') {
                        numPage = numPage - 1;
                        $('#next_btn').css('display', 'inline-block');
                        if (numPage === 1) {
                            $('#previous_btn').css('display', 'none');
                        }
                    }
                    if (pageBtn === 'next') {
                        numPage = numPage + 1;
                        $('#previous_btn').css('display', 'inline-block');
                    }
                    if (numPage === allPage) {
                        $('#next_btn').css('display', 'none');
                    }
                    if (numPage === 1) {
                        $('#previous_btn').css('display', 'none');
                    }
                    $.ajax({
                        type: 'POST',
                        data: {
                            action: 'table',
                            security: ajax_var.nonce,
                            filter: filter_sb,
                            id: user_sb,
                            table: nameTable_sb,
                            page: numPage
                        },
                        url: ajaxurl,
                        success: function (data) {
                            allPage = data[1];
                            var dataMsg = data[0];
                            if (numPage < allPage) {
                                $('#next_btn').css('display', 'inline-block');
                            }
                            var table = document.getElementById("tableBox");
                            $("#tableBox").empty();
                            dataMsg.forEach(function (msg) {
                                var row = table.insertRow(0);
                                row.setAttribute("data-table-msg", JSON.stringify(msg));
                                row.setAttribute("class", "pointer openModal msgTable");
                                row.setAttribute("data-btn", "modal-for-table");

                                var cell1 = row.insertCell(0);
                                var cell2 = row.insertCell(1);
                                var cell3 = row.insertCell(2);
                                var cell4 = row.insertCell(3);
                                cell1.innerHTML = msg.sender_name;
                                cell2.innerHTML = msg.receiver_name;
                                cell3.innerHTML = msg.title;
                                cell4.innerHTML = ago(new Date(msg.time_create));
                            });
                        }
                    });
                }

            }
        });
    </script>
    <?php

}


// Get messages for sub menu
//
//function getMsgTable()
//{
//    if ( ! check_ajax_referer( 'ajax-nonce', 'security' ) ) {
//        wp_send_json_error( 'Invalid security token sent.' );
//        wp_die();
//    }
//
//    global $wpdb;
//    // table name
//    $admin_users_message= $wpdb->prefix. "admin_users_message";
//    $admin_message=$wpdb->prefix . "admin_message";
//    $users=$wpdb->prefix . "users";
//    //
//    $userID = $_POST['id'];
//    $table = "wp_admin_" . $_POST['table'];
//    $limit = 10;
//    $page = $_POST['page'];
//    $offset = ($page - 1) * $limit;
//    $_where = "$admin_users_message.status
//     = 0 or $admin_users_message.status =2 ";
//    if ($_POST['filter'] != '') {
//        if ($_POST['filter'] === "all") {
//            $_where = $_where . "or $admin_users_message.status =1 ";
//        } else {
//            $rule = $table . "." . $_POST['filter'] . "=" . $userID;
//            $_where = $rule . " " . "and" . " " . $_where;
//        }
//
//    }
//
//    $querystr = "SELECT tb1.* , $users.user_nicename as sender_name , $users.user_email  FROM (SELECT $admin_users_message.* ,
//    $admin_message.* , $users.user_nicename as
//    receiver_name FROM $admin_users_message LEFT JOIN $users ON $users.ID = $admin_users_message.user_id
//    LEFT JOIN $admin_message ON $admin_users_message.msg_id = $admin_message.id WHERE " . $_where . ")
//     as tb1 LEFT JOIN $users on tb1.creator_id = $users.ID LIMIT $offset , $limit";
//    $data = $wpdb->get_results($querystr);
//
//    $total = $wpdb->get_var("select count(*) as total from $admin_users_message LEFT JOIN $admin_message ON
//    $admin_users_message.msg_id = $admin_message.id  WHERE $_where");
//
//    $num_of_pages = ceil($total / $limit);
//    wp_send_json(array($data, $num_of_pages));
//}
//
//add_action('wp_ajax_table', 'getMsgTable');
//
//// Create modals for messages
//
//// get time ago
//
//function time_elapsed_string($datetime, $full = false)
//{
//    $now = new DateTime;
//    $ago = new DateTime($datetime);
//    $diff = $now->diff($ago);
//
//    $diff->w = floor($diff->d / 7);
//    $diff->d -= $diff->w * 7;
//
//    $string = array(
//        'y' => 'سال',
//        'm' => 'ماه',
//        'w' => 'هفته',
//        'd' => 'روز',
//        'h' => 'ساعت',
//        'i' => 'دقیقه',
//        's' => 'ثانیه',
//    );
//    foreach ($string as $k => &$v) {
//        if ($diff->$k) {
//            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
//        } else {
//            unset($string[$k]);
//        }
//    }
//
//    if (!$full) $string = array_slice($string, 0, 1);
//    return $string ? implode(', ', $string) . ' قبل' : 'just now';
//}
