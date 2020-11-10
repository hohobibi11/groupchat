$(document).ready(function () {
    // scroll down to the end of the list of messages
    $('.messages').scrollTop($('.messages')[0].scrollHeight);

    //pjax:success handler
    $(document).on('pjax:success', function () {
        $('.messages').scrollTop($('.messages')[0].scrollHeight);
        $('.delete').click(function () {
            alert('Are you sure you want to perform this action?');
            $.ajax({
                url:'site/delete?id='+$(this).attr('data'),
                success: function ($data) {
                    $.pjax.defaults.timeout = false;
                    $.pjax.reload({container:"#messages-pj"});
                }
            });
        });
    });

    //delete/undo button click handler
    $('.delete').click(function () {
        alert('Are you sure you want to perform this action?');
        $.ajax({
            url:'site/delete?id='+$(this).attr('data'),
            success: function ($data) {
                $.pjax.defaults.timeout = false;
                $.pjax.reload({container:"#messages-pj"});
            }
        });
    });

    //real time function for polling
    function Poll() {
        $.ajax({
            url : 'site/new-message?count='+$('#count-messages').attr('data'),
            success : function (data) {
                //if new messages was found reload the pjax container
                if (data){
                    $.pjax.defaults.timeout = false;
                    $.pjax.reload({container:"#messages-pj"});
                }
                setTimeout(Poll, 1000);
            }
        });
    }
    Poll();
});