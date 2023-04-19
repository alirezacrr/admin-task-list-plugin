<?php
global $wpdb;
// table name
$admin_message = $wpdb->prefix . 'atl_admin_message';
$users = $wpdb->prefix . "users";

//
$current_user_id = get_current_user_id();
$querystr = "SELECT * From $admin_message Where user_id = $current_user_id and status != 'done' ";
$data = $wpdb->get_results($querystr);
$chack_msg = !empty($data);
$toggle_class = '';
$toggle_tab = 'task-list';
if (current_user_can('administrator')) {
    $toggle_class = $chack_msg ? 'have-msg' : '';
    $toggle_tab = $chack_msg ? 'task-list' : 'new';
}

?>

<div class="have-modal">

    <a class="pointer topbutton openModal <?php echo $toggle_class ?>"
       data-tab="<?php echo $toggle_tab ?>">
        <img class="btn_sticky"
             src="<?php echo $chack_msg ? plugin_dir_url(__FILE__) . '../assets/img/icons/haveMsg.png' : plugin_dir_url(__FILE__) . '../assets/img/icons/noMsg.png'; ?>"
             alt="modal">
    </a>

</div>
<div class="modal atl-font">
    <div class="modal-content">
        <div class="wrap">
            <span class="close-button">&times;</span>

            <ul class="nav nav-tabs">
                <?php if (current_user_can('administrator')): ?>
                <li class=""><a href="#new" data-toggle="new"><?php _e('Add New', 'atl'); ?></a></li>
                <?php endif; ?>
                <li class=""><a href="#task-list" data-toggle="task-list"><?php _e('Your Tasks', 'atl'); ?></a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane " id="new">
                    <h5><?php _e('Add New Task', 'atl'); ?></h5>
                    <div class="content-dialog">
                        <?php if (current_user_can('administrator')) : ?>
                            <header class="wf-header-modal">
                                <div class="row-head box-title-write">
                                    <span class="txt-label"><?php _e('title', 'atl'); ?></span>
                                    <div class="row-content box-design input-title-div">
                                        <input type="text" class="form-input" id="input-title" maxlength="40">
                                    </div>
                                </div>

                                <div class="row-head box-user-select">
                                    <span class="txt-label"><?php _e('users', 'atl'); ?></span>
                                    <div class="row-content select-user">
                                        <select class="atl-select select2 msg-users" id="msg_users" name="msg_users">
                                            <?php
                                            $args = array(
                                                'role__in' => array('author', 'administrator', 'editor')
                                            );
                                            $users_admin = get_users($args);

                                            ?>
                                            <!--                                        <option value="0">-->
                                            <!--                                            --><?php //_e('All Users','atl') ?>
                                            <!--                                        </option>-->
                                            <option value="">
                                                <?php _e('SELECT', 'atl') ?>
                                            </option>
                                            <?php foreach ($users_admin as $user) : ?>
                                                <option value="<?php echo esc_html($user->ID) ?>">
                                                    <?php echo esc_html($user->display_name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                            </header>


                            <div class="more-description">
                                <span class="des-txt"><?php _e('Description', 'atl'); ?></span>
                                <textarea id="description-area" class=" box-design" cols="30" rows="10"></textarea>
                            </div>
                            <footer class="wf-footer-modal">
                                <button class="btn_status btn_submit" id="saveMsg"
                                        aria-hidden="true">
                                    <?php _e('Submit', 'atl'); ?>
                                </button>
                            </footer>

                        <?php else: ?>
                            <div class="access-denied"><?php _e('Only admins can create new tasks!', 'atl'); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="tab-pane " id="task-list">
                    <h5><?php _e('Your Task List', 'atl'); ?></h5>
                    <div class="task-items <?php echo empty($data) ? 'empty' : '' ?>"
                         data-empty="<?php _e('You have no task to perform', 'atl'); ?>">
                        <?php
                        foreach ($data as $msg) { ?>
                            <div class="row-msg msg-item" id="msg-id-<?php echo $msg->id ?>"
                                 data-msg-detailed="<?php echo htmlspecialchars(json_encode($msg),
                                     ENT_QUOTES, 'UTF-8') ?>">
                                <div class="info-msg">
                                    <div class="avatar-msg">
                                        <img src="https://www.gravatar.com/avatar/<?php wp_generate_password($msg->creator_id, false); ?>">
                                    </div>
                                    <div class="header-msg">
                                        <div class="name-msg">
                                            <?php
                                            $sender = get_userdata($msg->creator_id);
                                            echo $sender->user_login;
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
                                    <?php if ((int)$msg->user_id === $current_user_id): ?>
                                        <button class="btn_status btn_submit changeStatus"
                                                data-user-id="<?php echo $msg->user_id ?>"
                                                data-msg-id="<?php echo $msg->id ?>"
                                                data-status="done"
                                                aria-hidden="true">
                                            <?php _e('Done', 'atl'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="tab-pane " id="tab-hide">
                    <div>
                        <span class="back-tab">
                            <?php _e('Back', 'atl'); ?></span>
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
                                    <button class="btn_status btn_submit changeStatus" id="btn_submit"
                                            data-status="done"
                                    >
                                        <?php _e('Done', 'atl'); ?>
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
<input type="hidden" id="atl-get-uid" value="<?php echo $current_user_id ?>">
