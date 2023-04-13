<?php
global $wpdb;
// table name
$admin_message = ATL_ADMIN_MESSAGE_TABLE;
$admin_users_message = ATL_ADMIN_USERS_MESSAGE_TABLE;
$users = $wpdb->prefix . "users";
//
$current_user_id = get_current_user_id();
$chack_msg = $wpdb->get_var("SELECT user_id FROM $admin_users_message WHERE user_id = $current_user_id and status = 0 or status =2");
?>

<div class="modal atl-font">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>ایجاد تسک جدید</h2>
        <div class="content-dialog">
            <header class="wf-header-modal">
                <div class="row-head box-title-write">
                    <span class="txt-label">عنوان</span>
                    <div class="row-content box-design input-title-div">
                        <input type="text" class="form-input" id="input-title" maxlength="40">
                    </div>
                </div>

                <div class="row-head box-user-select">
                    <span class="txt-label">کاربر</span>
                    <div class="row-content select-user">
                            <select class="atl-select select2 msg-users" id="msg_users" name="msg_users[]"  multiple="multiple">
                                <?php

                                $args = array(
                                    'role' => 'Administrator'
                                );
                                $users_admin = get_users($args);

                                ?>
                                <option value="0">a</option>
                                <option value="0">s</option>
                                <option value="0">sss</option>
                                <option value="0">b</option>
                                <option value="0">همه ی کاربران</option>

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
                <span class="des-txt">توضیحات بیشتر</span>
                <textarea name="" id="description-area" class=" box-design" cols="30" rows="10"></textarea>
            </div>
            <footer class="wf-footer-modal">
                <button class="btn_status btn_submit" id="saveMsg"
                        aria-hidden="true">
                    ذخیره و
                    ارسال
                </button>
            </footer>
        </div>
    </div>
</div>
<div class="have-modal">
    <?php if ($chack_msg) { ?>
        <a class="pointer topbutton openModal" data-btn="modal-messages">
            <img class="btn_sticky" src="<?php echo plugin_dir_url(__FILE__) . '../assets/img/icons/haveMsg.png'; ?>">
        </a>
    <?php } else { ?>
        <a class="pointer topbutton openModal" data-btn="modal-new">
            <img class="btn_sticky" src="<?php echo plugin_dir_url(__FILE__) . '../assets/img/icons/noMsg.png'; ?>">
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

                    </div>

                </header>


                <div class="more-description">
                    <span class="des-txt">توضیحات بیشتر</span>
                    <textarea name="" id="description-area" class=" box-design" cols="30" rows="10"></textarea>
                </div>
                <footer class="wf-footer-modal">
                    <button class="btn_status btn_submit" id="saveMsg"
                            aria-hidden="true">
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
    <section class="wrp">
        <?php $querystr = "SELECT tb1.* , $users.user_nicename as sender_name , $users.user_email  FROM (SELECT $admin_users_message.* , $admin_message.* , $users.user_nicename as 
    receiver_name FROM $admin_users_message LEFT JOIN $users ON $users.ID = $admin_users_message.user_id
    LEFT JOIN $admin_message ON $admin_users_message.msg_id = $admin_message.id WHERE  $admin_users_message.user_id = $current_user_id and $admin_users_message.status
     = 0 or $admin_users_message.status =2)
     as tb1 LEFT JOIN $users on tb1.creator_id = $users.ID  ";
        $data = $wpdb->get_results($querystr); ?>
        <div class="wf-modal" aria-hidden="true" id="modal-messages">
            <article class="wf-dialog-modal opened">
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
                            foreach ($data as $msg) { ?>
                                <div class="row-msg" id="msg-id-<?php echo $msg->msg_id ?>">
                                    <div class="info-msg">
                                        <div class="avatar-msg">
                                            <img src="https://www.gravatar.com/avatar/<?php
                                            wp_generate_password($msg->creator_id, false); ?>">
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
                                                data-user-id="<?php echo $current_user_id ?>"
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
</section>
<script type="text/javascript">
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>