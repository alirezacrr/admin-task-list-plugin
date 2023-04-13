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

function getMsgTable()
{
    if ( ! check_ajax_referer( 'ajax-nonce', 'security' ) ) {
        wp_send_json_error( 'Invalid security token sent.' );
        wp_die();
    }

    global $wpdb;
    // table name
    $admin_users_message= $wpdb->prefix. "admin_users_message";
    $admin_message=$wpdb->prefix . "admin_message";
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

add_action('wp_ajax_table', 'getMsgTable');

// Create modals for messages

function modals()
{
    global $wpdb;
    // table name
    $admin_users_message= $wpdb->prefix. "admin_users_message";
    $admin_message=$wpdb->prefix . "admin_message";
    $users=$wpdb->prefix . "users";
    //
    $current_user = wp_get_current_user();
    $chack_msg = $wpdb->get_var("SELECT user_id FROM $admin_users_message WHERE user_id = $current_user->ID
    and status = 0 or status =2");
    ?>
    <div class="have-modal">
    <?php if ($chack_msg) { ?>
    <a class="pointer topbutton openModal" aria-role="button"  data-btn="modal-messages">
        <img class="btn_sticky" src="<?php echo plugin_dir_url(__FILE__) . 'icons/haveMsg.png'; ?>">
    </a>
<?php } else { ?>
    <a class="pointer topbutton openModal" aria-role="button"  data-btn="modal-new">
        <img class="btn_sticky" src="<?php echo plugin_dir_url(__FILE__) . 'icons/noMsg.png'; ?>">
    </a>
<?php } ?>
    </div>
    <!--  modal new message  -->

    <section class="wrp">
        <div class="wf-modal" aria-hidden="true" id="modal-new">
            <article class="wf-dialog-modal">
                <div class="content-dialog">
                    <span class="close"><a class="pointer exit closeModal"
                                           aria-hidden="true">بستن</a></span>
                    <span class="close"><a class="pointer openModal" data-btn="modal-new"
                                           aria-hidden="true">جدید</a></span>
                    <header class="wf-header-modal">
                        <div class="row-head box-title-write">
                            <span class="txt-label">عنوان</span>
                            <div class="box-design input-title-div">
                                <input type="text" id="input-title" maxlength="40">
                            </div>
                        </div>

                        <div class="row-head box-user-select">

                            <span class="txt-label">کاربر</span>

                            <div class="box-design dropdown" id="dropdown_admin">
                                <img class="icon-arrow-down"
                                     src="<?php echo plugin_dir_url(__FILE__) . 'icons/arrow-down-sign-to-navigate.svg'; ?>"
                                     alt="">
                                <button class="dropbtn" id="dropbtn"></button>
                                <input type="hidden" name="id" value="" id="value-hide">
                                <div id="myDropdown" class="dropdown-content">
                                    <?php

                                    $args = array(
                                        'role' => 'Administrator'
                                    );
                                    $users_admin = get_users($args);

                                    ?>
                                    <ul>
                                        <li id="0">همه ی کاربران</li>
                                        <?php foreach ($users_admin as $user) : ?>
                                            <li id="<?php echo esc_html($user->ID) ?>">
                                                <?php echo esc_html($user->display_name) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </header>
                    <div class="more-description">
                        <span class="des-txt">توضیحات بیشتر</span>
                        <textarea name="" id="description-area" class=" box-design" cols="30" rows="10"></textarea>
                    </div>
                    <footer class="wf-footer-modal">
                        <button class="btn_status btn_submit" id="saveMsg"
                                aria-hidden="true" >
                            ذخیره و
                            ارسال
                        </button>
                    </footer>
                </div>
            </article>
        </div>
    </section>

    <!--  modal show all messages  -->
<div class="have-modal">
    <section class="wrp"  >
        <?php $querystr = "SELECT tb1.* , $users.user_nicename as sender_name , $users.user_email  FROM (SELECT $admin_users_message.* , $admin_message.* , $users.user_nicename as 
    receiver_name FROM $admin_users_message LEFT JOIN $users ON $users.ID = $admin_users_message.user_id
    LEFT JOIN $admin_message ON $admin_users_message.msg_id = $admin_message.id WHERE  $admin_users_message.user_id = $current_user->ID and $admin_users_message.status
     = 0 or $admin_users_message.status =2)
     as tb1 LEFT JOIN $users on tb1.creator_id = $users.ID  ";
        $data = $wpdb->get_results($querystr); ?>
        <div class="wf-modal" aria-hidden="true" id="modal-messages">
            <article class="wf-dialog-modal">
                <div class="content-dialog content-msg">
                    <?php $count = 0;
                    foreach ($data as $value) {
                        if ($value->status === '0') {
                            $count += 1;
                        }
                    } ?>
                    <div class="warp-msg">
                        <span class="close">
                            <a class="pointer exit closeModal"
                               aria-hidden="true">بستن</a></span>
                        <span class="close">
                            <a class="pointer openModal" data-btn="modal-new"
                               aria-hidden="true">جدید</a>
                        </span>
                        <div>
                            <span id="count-msg"><?php echo $count ?></span>
                        </div>
                        <div>
                            <?php
                            foreach ($data as $msg) {?>
                                <div class="row-msg" id="msg-id-<?php echo $msg->msg_id ?>">
                                    <div class="info-msg">
                                        <div class="avatar-msg">
                                            <img src="https://www.gravatar.com/avatar/<?php
                                            wp_generate_password($msg->creator_id, false ); ?>">
                                        </div>
                                        <div class="header-msg">
                                            <div class="name-msg">
                                                <?php
                                                echo $msg->sender_name;
                                                ?>

                                            </div>
                                            <div class="title-msg title-all-msg ">
                                                <a id="show-message" class="openModal pointer"
                                                   data-btn="modal-show-message"
                                                   data-msg-detailed="<?php echo htmlspecialchars(json_encode($msg),
                                                       ENT_QUOTES, 'UTF-8') ?>"> <?php
                                                    echo $msg->title;
                                                    ?>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="description-msg"><?php echo $msg->description; ?></div>
                                    <div class="time_btn_msg">
                                        <?php echo time_elapsed_string($msg->time_create); ?>
                                        <button class="btn_status btn_submit changeStatus"
                                                data-user-id="<?php echo $current_user->id ?>"
                                                data-msg-id="<?php echo $msg->msg_id ?>" data-for-status="1"
                                                aria-hidden="true" href="#">انجام شد
                                        </button>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
</div>
    <!--  modal show  message  -->

    <section class="wrp">
        <div class="wf-modal" aria-hidden="true" id="modal-show-message">
            <article class="wf-dialog-modal">
                <div class="content-dialog content-msg">

                    <div class="warp-msg">
                        <span class="close"><a class="pointer exit closeModal"
                                               aria-hidden="true">بستن</a></span>
                        <span class="close"><a class="pointer openModal" data-btn="modal-new"
                                               aria-hidden="true">جدید</a></span>

                        <header class="header-show-msg">
                            <div class="row-msg">
                                <div class="info-msg">
                                    <input type="hidden" name="user_id" id="user_id" value=""/>
                                    <div class="avatar-msg" id="avatar-msg">

                                    </div>
                                    <div class="header-msg">
                                        <div class="name-msg" id="name-msg">

                                        </div>
                                        <div class="title-msg">
                                            <a
                                                    id="title-msg">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="time_btn_msg tbm">
                                    <span id="time-msg">
                                    </span>
                                    <button class="btn_status btn_check changeStatus"
                                            id="btn_check"
                                    >نیاز به بررسی
                                    </button>
                                    <button class="btn_status btn_submit changeStatus"
                                            id="btn_submit"
                                            >انجام شد
                                    </button>
                                </div>
                            </div>

                        </header>
                        <div class="content-show-msg">
                            <div class="description-msg description-show-msg">
                                <p id="description-msg"></p>
                            </div>
                        </div>
                    </div>

                </div>
        </div>
        </article>
        </div>
    </section>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
    <?php
}

add_action('admin_footer', 'modals');

// save message in database.

function save_msg()
{
    if ( ! check_ajax_referer( 'ajax-nonce', 'security' ) ) {
        wp_send_json_error( 'Invalid security token sent.' );
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

add_action('wp_ajax_save', 'save_msg');

// change status message in database.

function status_msg()
{
    if ( ! check_ajax_referer( 'ajax-nonce', 'security' ) ) {
        wp_send_json_error( 'Invalid security token sent.' );
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

add_action('wp_ajax_status', 'status_msg');

// Create table in database sql

register_activation_hook(__FILE__, 'my_plugin_create_db');
function my_plugin_create_db()
{

    global $wpdb;
    $version = get_option('my_plugin_version', '1.0');
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'admin_message';
    $table_name_2 = $wpdb->prefix . 'admin_users_message';


    $sql = "CREATE TABLE $table_name (
         id int   NOT NULL  AUTO_INCREMENT,
         creator_id mediumint(9) NOT NULL ,
         time_create datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         time_edit datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         title  varchar(40) DEFAULT '' NOT NULL,
         description  text DEFAULT '' NOT NULL,
          PRIMARY KEY (id)
      ) $charset_collate;";
    $sql2 = "CREATE TABLE $table_name_2 (
        user_id mediumint(9) NOT NULL ,
        msg_id mediumint(9) NOT NULL ,
        status mediumint(9) NOT NULL
) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    dbDelta($sql2);

    if (version_compare($version, '2.0') < 0) {

        $sql = "CREATE TABLE $table_name (
         id int   NOT NULL  AUTO_INCREMENT,
         creator_id mediumint(9) NOT NULL ,
         time_create datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         time_edit datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         title  varchar(40) DEFAULT '' NOT NULL,
         description  text DEFAULT '' NOT NULL,
         PRIMARY KEY (id)
          ) $charset_collate;";
        $sql2 = "CREATE TABLE $table_name_2 (
        user_id mediumint(9) NOT NULL ,
        msg_id mediumint(9) NOT NULL ,
        status mediumint(9) NOT NULL
        ) $charset_collate;";
        dbDelta($sql2);
        dbDelta($sql);

        update_option('my_plugin_version', '2.0');

    }


}

// get time ago

function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'سال',
        'm' => 'ماه',
        'w' => 'هفته',
        'd' => 'روز',
        'h' => 'ساعت',
        'i' => 'دقیقه',
        's' => 'ثانیه',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' قبل' : 'just now';
}
