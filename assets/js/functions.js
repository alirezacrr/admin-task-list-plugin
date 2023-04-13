jQuery(document).ready(function ($) {
//     var user_sb, filter_sb, nameTable_sb, numPage, allPage;
//     // set filter sub menu
//     $("#setFilter").on('click', function () {
//         setFilter();
//         getMsgTable()
//     });
//     function setFilter () {
//         user_sb = $('#searchByuser').find(":selected").val();
//         filter_sb = $('#filter').find(":selected").val();
//         nameTable_sb = $('#filter').find(":selected").data('table');
//         numPage = 1;
//         allPage = 1;
//         getMsgTable()
//     }
// // get message sub menu ajax
//     function getMsgTable(pageBtn) {
//         if (numPage < allPage || numPage === allPage) {
//             if (pageBtn === 'previous') {
//                 numPage = numPage - 1;
//                 $('#next_btn').css('display', 'inline-block');
//                 if (numPage === 1) {
//                     $('#previous_btn').css('display', 'none');
//                 }
//             }
//             if (pageBtn === 'next') {
//                 numPage = numPage + 1;
//                 $('#previous_btn').css('display', 'inline-block');
//             }
//             if (numPage === allPage) {
//                 $('#next_btn').css('display', 'none');
//             }
//             if (numPage === 1) {
//                 $('#previous_btn').css('display', 'none');
//             }
//             jQuery.ajax({
//                 type: 'POST',
//                 data: {
//                     action: 'table',
//                     security: ajax_var.nonce,
//                     filter: filter_sb,
//                     id: user_sb,
//                     table: nameTable_sb,
//                     page: numPage
//                 },
//                 url: ajaxurl,
//                 success: function (data) {
//                     allPage = data[1];
//                     var dataMsg = data[0];
//                     if (numPage < allPage) {
//                         $('#next_btn').css('display', 'inline-block');
//                     }
//                     var table = document.getElementById("tableBox");
//                     $("#tableBox").empty();
//                     dataMsg.forEach(function (msg) {
//                         var row = table.insertRow(0);
//                         row.setAttribute("data-table-msg", JSON.stringify(msg));
//                         row.setAttribute("class", "pointer openModal msgTable");
//                         row.setAttribute("data-btn", "modal-for-table");
//
//                         var cell1 = row.insertCell(0);
//                         var cell2 = row.insertCell(1);
//                         var cell3 = row.insertCell(2);
//                         var cell4 = row.insertCell(3);
//                         cell1.innerHTML = msg.sender_name;
//                         cell2.innerHTML = msg.receiver_name;
//                         cell3.innerHTML = msg.title;
//                         cell4.innerHTML = ago(new Date(msg.time_create));
//                     });
//                 }
//             });
//         }
//
//     }

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
        var user_id = $('#value-hide').val();
        var title = $('#input-title').val().trim();
        var description = $('#description-area').val();

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

//open modals
    $(".have-modal").on('click', '.openModal',function () {
        $(".wf-modal").removeClass("opened");
        $("#" + $(this).data('btn')).addClass('opened');
    });

// close modals
    $(".closeModal").on('click', function () {
        $(".wf-modal").removeClass("opened");
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