<?php
global $wpdb;
// table name
$admin_message = $wpdb->prefix . 'atl_admin_message';
$admin_users_message = $wpdb->prefix . 'atl_admin_users_message';
$users = $wpdb->prefix . "users";

//
$current_user_id = get_current_user_id();
$my_msg = $wpdb->get_results("SELECT id  FROM $admin_message  LEFT JOIN $admin_users_message ON $admin_message.id = $admin_users_message.msg_id WHERE $admin_users_message.user_id = $current_user_id  and  $admin_message.status = 'pending'");
$chack_msg = !empty($my_msg );
$querystr = "SELECT tb1.* , $users.user_nicename as sender_name , $users.user_email  FROM (SELECT $admin_users_message.* , $admin_message.* , $users.user_nicename as
    receiver_name FROM $admin_users_message LEFT JOIN $users ON $users.ID = $admin_users_message.user_id
    LEFT JOIN $admin_message ON $admin_users_message.msg_id = $admin_message.id WHERE  $admin_users_message.user_id = $current_user_id and $admin_message.status != 'done')
     as tb1 LEFT JOIN $users on tb1.creator_id = $users.ID  ";
$data = $wpdb->get_results($querystr);
?>

<div class="have-modal">
    <a class="pointer topbutton openModal <?php echo $chack_msg ? 'have-msg' : '' ?>"
       data-tab="<?php echo $chack_msg ? 'task-list' : 'new' ?>">
        <img class="btn_sticky"
             src="<?php echo $chack_msg ? plugin_dir_url(__FILE__) . '../assets/img/icons/haveMsg.png' : plugin_dir_url(__FILE__) . '../assets/img/icons/noMsg.png'; ?>">
    </a>
</div>
<div class="modal atl-font">
    <div class="modal-content">
        <div class="wrap">
            <span class="close-button">&times;</span>

            <ul class="nav nav-tabs">
                <li class=""><a href="#new" data-toggle="tab"><?php _e('Add New', 'atl'); ?></a></li>
                <li class=""><a href="#task-list" data-toggle="tab"><?php _e('Task List', 'atl'); ?></a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane " id="new">
                    <h5><?php _e('Add New Task','atl'); ?></h5>
                    <div class="content-dialog">
                        <header class="wf-header-modal">
                            <div class="row-head box-title-write">
                                <span class="txt-label"><?php _e('title','atl'); ?></span>
                                <div class="row-content box-design input-title-div">
                                    <input type="text" class="form-input" id="input-title" maxlength="40">
                                </div>
                            </div>

                            <div class="row-head box-user-select">
                                <span class="txt-label"><?php _e('users','atl'); ?></span>
                                <div class="row-content select-user">
                                    <select class="atl-select select2 msg-users" id="msg_users" name="msg_users[]"
                                            multiple="multiple">
                                        <?php

                                        $args = array(
                                            'role' => 'Administrator'
                                        );
                                        $users_admin = get_users($args);

                                        ?>
                                        <?php foreach ($users_admin as $user) : ?>
                                            <option value="<?php echo esc_html($user->ID) ?>">
                                                <?php echo esc_html($user->display_name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <small><?php _e('Note : empty to send to all','atl'); ?></small>
                            </div>

                        </header>


                        <div class="more-description">
                            <span class="des-txt"><?php _e('Description','atl'); ?></span>
                            <textarea  id="description-area" class=" box-design" cols="30" rows="10"></textarea>
                        </div>
                        <footer class="wf-footer-modal">
                            <button class="btn_status btn_submit" id="saveMsg"
                                    aria-hidden="true">
                                <?php _e('Submit','atl'); ?>
                            </button>
                        </footer>
                    </div>
                </div>
                <div class="tab-pane " id="task-list">
                    <div>
                        <?php
                        foreach ($data as $msg) { ?>
                            <div class="row-msg msg-item" id="msg-id-<?php echo $msg->msg_id ?>"
                                 data-msg-detailed="<?php echo htmlspecialchars(json_encode($msg),
                                     ENT_QUOTES, 'UTF-8') ?>">
                                <div class="info-msg">
                                    <div class="avatar-msg">
                                        <img src="https://www.gravatar.com/avatar/<?php wp_generate_password($msg->creator_id, false); ?>">
                                    </div>
                                    <div class="header-msg">
                                        <div class="name-msg">
                                            <?php
                                            echo $msg->sender_name;
                                            ?>
                                        </div>
                                        <div class="title-msg title-all-msg ">
                                            <a id="show-message" class=" pointer"> <?php
                                                echo $msg->title;
                                                ?>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                                <div class="description-msg"><?php echo $msg->description; ?></div>
                                <div class="time_btn_msg">
                                    <?php echo ATL_Helper::time_elapsed_string($msg->time_create); ?>
                                    <button class="btn_status btn_submit changeStatus"
                                            data-user-id="<?php echo $current_user_id ?>"
                                            data-msg-id="<?php echo $msg->msg_id ?>"
                                            data-status="done"
                                            aria-hidden="true">
                                        <?php _e('Done','atl'); ?>
                                    </button>
                                </div>
                            </div>

                        <?php } ?>
                    </div>
                </div>
                <div class="tab-pane " id="tab-hide">
                    <div>
                        <span class="back-tab">
                            <?php _e('Back','atl'); ?></span>
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
                                    <!--                                    <button class="btn_status btn_check changeStatus"-->
                                    <!--                                            id="btn_check"-->
                                    <!--                                    >نیاز به بررسی-->
                                    <!--                                    </button>-->
                                    <button class="btn_status btn_submit changeStatus"
                                            id="btn_submit"
                                    >
                                        <?php _e('Done','atl'); ?>
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
        </div>
    </div>
</div>
