jQuery(document).ready(function ($) {

// show message sob menu
    $("#tableBox").on('click', '.msgTable',function () {
            var msgData = $(this).data('table-msg');
            var email = msgData.user_email;
            var gravatar = $('<img>').attr({src: 'https://www.gravatar.com/avatar/' + md5(email)});
            $('#avatar-msgTable').html(gravatar);
            $('#name-msgTable').text(msgData.sender_name);
            $('#title-msgTable').text(msgData.title);
            $('#description-msgTable').text(msgData.description);
            $('#time-msgTable').text(ago(new Date(msgData.time_create)));
    })
// save message ajax

    $("#saveMsg").on('click', function () {
        var user_id = $('#msg_users').val();
        var title = $('#input-title').val();
        var description = $('#description-area').val();
        console.log(user_id,title)
        if (title === '' || user_id === '') {
            alert('پر کردن عنوان و انتخاب کاربر ضروری است!')
        } else {
            //  Save message
            jQuery.ajax({
                type: 'POST',
                data: {
                    action: 'save',
                    security: ajax_var.nonce,
                    user_id: user_id,
                    title: title,
                    description: description
                },
                url: ajaxurl,
                success: function () {
                    alert('پیام ثبت شد!');
                    $('#input-title').val('');
                    $('#description-area').val('')
                }
            });
        }

    })

// change status message ajax
    $(".changeStatus").on('click', function () {
        // Change status
        jQuery.ajax({
            type: 'POST',
            data: {
                action: 'status',
                security: ajax_var.nonce,
                user_id: $(this).data('user-id'),
                msg_id: $(this).data('msg-id'),
                status: $(this).data('for-status')
            },
            url: ajaxurl,
            success: function () {
                console.log('success change msg status!');
            }

        });
        if ($(this).data('for-status') === 1) {
            var id = $(this).data('msg-id');
            document.getElementById("msg-id-" + id).style.display = 'none';
        }

    })

// dropdown
    $("#dropdown_admin").on('click', function () {
        document.getElementById("myDropdown").classList.toggle("show");
    })

    $(".dropdown-content").on('click', 'li', function () {
        $("#dropbtn").html($(this).text());
        $('#value-hide').val($(this).attr('id'));
    });
    $('#filter').change(function () {
        if ($(this).val() === '' || $(this).val() === 'all') {
            $('#searchByuser').css('display', 'none');
        } else {
            $('#searchByuser').css('display', 'block');
        }
    });



// add data in show message fields
    $("#show-message").on('click', function () {
        var msgData = $(this).data('msg-detailed');
        var email = msgData.user_email;
        var gravatar = $('<img>').attr({src: 'https://www.gravatar.com/avatar/' + md5(email)});
        $('#avatar-msg').html(gravatar);
        $('#name-msg').text(msgData.sender_name);
        $('#title-msg').text(msgData.title);
        $('#description-msg').text(msgData.description);
        $('#time-msg').text(ago(new Date(msgData.time_create)));
        $('#btn_submit').attr({
            'data-user-id': msgData.user_id,
            'data-msg-id': msgData.msg_id,
            'data-for-status': 1
        });
        $('#btn_check').attr({
            'data-user-id': msgData.user_id,
            'data-msg-id': msgData.msg_id,
            'data-for-status': 2
        });
    })
    $(document).ready(function() {
        var modal = document.querySelector(".modal");
        var triggers = document.querySelectorAll(".openModal");
        var closeButton = document.querySelector(".close-button");

        function toggleModal() {
            modal.classList.toggle("show-modal");
        }

        function windowOnClick(event) {
            if (event.target === modal) {
                toggleModal();
            }
        }

        for (var i = 0, len = triggers.length; i < len; i++) {
            triggers[i].addEventListener("click", toggleModal);
        }
        closeButton.addEventListener("click", toggleModal);
        window.addEventListener("click", windowOnClick);

        $('.atl-select').each(function () {
            $(this).select2({
                theme: 'bootstrap4',
                width: 'style',
                placeholder: $(this).attr('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
            });
        });
    });
    function ago(val) {
        val = 0 | (Date.now() - val) / 1000;
        var unit, length = {
            ثانیه: 60, دقیقه: 60, ساعت: 24, روز: 7, هفته: 4.35,
            ماه: 12, سال: 10000
        }, result;

        for (unit in length) {
            result = val % length[unit];
            if (!(val = 0 | val / length[unit]))
                return result + ' ' + (result - 1 ? unit : unit) + ' ' + 'پیش';
        }
    }
})