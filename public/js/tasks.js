$(document).ready(function () {
    $('#add_comment').click(function() {
        var task = $('#task').attr('data-id');
        var project = $('#task').attr('data-project');
        var comment = $('#comment').val();
        if(comment !== '') {
            $.ajax({
                url:    'comment/save',
                type:   'POST',
                data:   ({
                    '_token' : $('[name="_token"]').val(),
                    'comment': comment
                }),
                success: function (data) {
                    $('#comment_div').prepend('<div class="card"> <div class="card-content"> <div class="row"> <div class="col s3 l1" style="width: auto"> <div class="circle" style="background-image: url(\'' + data.img + '\'); height: 50px; width: 50px; background-position: center;background-size: cover; background-repeat: no-repeat;"></div> </div> <div class="col s8 l11 left-align" style="padding-left: 0"> <span class="black-text"> ' + data.user + ' </span> <br> <p class="mt-4">' + data.data + '</p> </div> </div> <div class="row valign-wrapper"> <div class="col s12 mt-3 ml-6" style="padding-left: 0"> <button name="reply_comment" data-id="' + data.id + '" class="btn btn-small waves-effect waves-light" title="Rimuovi Commento"> Reply </button> <button name="remove_comment" data-id="' + data.id + '" class="btn btn-small waves-effect waves-light red" title="Rimuovi Commento"> Remove </button> </div> </div> </div> </div>');
                    $('#comment').val('');
                }
            });
        }
    });

    $('#comment_div').on('click', 'button[name="add_reply"]', function () {
        var btn = $(this);
        var id = $(this).attr('data-id');

        var reply = btn.prev().find('textarea').val();
        var task = $('#task').attr('data-id');
        var project = $('#task').attr('data-project');
        if(reply !== '') {
            $.ajax({
                url:     'comment/save',
                type:    'POST',
                data:    ({
                    '_token' : $('[name="_token"]').val(),
                    'comment': reply,
                    'parent' : id
                }),
                success: function (data) {
                    var card = btn.parent().parent();
                    $('[name="reply_comment"][data-id="' + id + '"]').attr('disabled', false);
                    $('[name="remove_comment"][data-id="' + id + '"]').after('<div class="card"> <div class="card-content"> <div class="row"> <div class="col s3 l1"> <div class="circle" style="background-image: url(\'' + data.img + '\'); height: 50px; width: 50px; background-position: center;background-size: cover; background-repeat: no-repeat;"></div> </div> <div class="col s8 l11 left-align" style="padding-left: 0"> <span class="black-text"> ' + data.user + ' </span> <br> <p class="mt-4">' + data.data + '</p> </div> </div> <div class="row valign-wrapper"> <div class="col s12 mt-3 ml-6" style="padding-left: 0"> <button name="reply_comment" data-id="' + data.id + '" class="btn btn-small waves-effect waves-light m-1" title="Rimuovi Commento"> Reply </button> <button name="remove_comment" data-id="' + data.id + '" class="btn btn-small waves-effect waves-light red m-1" title="Rimuovi Commento"> Remove </button> </div> </div> </div> </div>');
                    card.remove();
                }
            });
        }
    });

    $('#comment_div').on('click', 'button[name="remove_comment"]', function() {

        var btn = $(this);
        var comment = btn.attr('data-id');
        var file = $('#file_id').val();

        $.ajax({
            url:    'comment/remove',
            type:   'POST',
            data:   ({
                '_token' : $('[name="_token"]').val(),
                'comment': comment
            }),
            success: function () {
                btn.parent().parent().parent().parent().remove();
            }
        });
    });

    $('#comment_div').on('click', 'button[name="reply_comment"]', function() {
        var id = $(this).attr('data-id');
        $(this).parent().children('.btn').last().after('<div class="card mb-4"><div class="card-content"><div class="input-field"><label>Commento</label><textarea class="materialize-textarea" name="reply" rows="3"></textarea></div><button name="add_reply" data-id="' + id + '" class="bbtn btn-small waves-effect waves-light">Reply</button></div></div>');
        $(this).attr('disabled', true);
    });

    $(document).on('click', 'li', function(){
        if($(this).data('ref') == 'tags'){
            var list = $(this).parent().parent();
            var input = list.prev();
            input.val($(this).text());
            list.fadeOut();
            return;
        }
    });

    $('[name="remove_task"]').click(function () {
        var id = $(this).attr('data-id');
        $('#confirm_remove').attr('data-id', id);
    });

    $('#confirm_remove').click(function () {
        var id = $(this).attr('data-id');
        window.location.href = $('[name="remove_task"][data-id="' + id + '"]').attr('data-route');
    });
});
