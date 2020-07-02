function sort_rooms() {
    var options = $('select option');
    var arr = options.map(function(_, o) { return { t: $(o).text(), v: o.value }; }).get();
    arr.sort(function(o1, o2) {
        var t1 = o1.t.toLowerCase(), t2 = o2.t.toLowerCase();
        if(t1 === ' choose a room ') { return -1; }
        if(t2 === ' choose a room ') { return 1; }
        return t1 > t2 ? 1 : t1 < t2 ? -1 : 0;
    });
    options.each(function(i, o) {
        o.value = arr[i].v;
        $(o).text(arr[i].t);
    });
    $('select').formSelect({
        classes: 'room-select'
    });
}

$(document).ready(function () {

    $(document).on('change', '.room-select', function () {
        var level = $(this).data('level');
        var rooms = $(this).val();
        var rooms_array = {};
        var rooms_level = $(this).data('level');

        if(typeof rooms === 'string') {
            return false;
        }

        $.each(rooms, function(index, value) {
            var text = $('select[data-level="' + rooms_level + '"] option[value="' + value + '"]').text();
            rooms_array[value] = text;
        });
        console.log(rooms_array);

        $.ajax({
            url:     '/file/get-rooms',
            type:    'POST',
            data:    ({
                '_token'        : $('[name="_token"]').val(),
                'rooms'         : rooms,
                'rooms_array'   : rooms_array
            }),
            success: function (data) {
                if(data.result === 'empty') {
                    var selects = $("select").filter(function() { return  $(this).data("level") > level; });
                    selects.each(function () {
                        $(this).parent().parent().remove();
                    });
                } else {
                    var new_level = level+1;
                    var select = $('select[data-level="' + new_level + '"]');
                    if(select.length === 0)
                    {
                        $('#tags').before('<div class="input-field my-3 valign-wrapper" style="display: flex"> <select name="rooms[]" class="room-select" data-level="' + new_level + '" multiple> <option value="" disabled>' + data.lang + '</option> </select> <label>' + data.lang + '</label> <p class="mt-1 ml-2"> <label> <input type="checkbox" data-level="' + new_level + '" checked /> <span> </span> </label> </p> </div>');
                        $.each(data.data, function (index, value) {
                            console.log(value.parent);
                            var opt = new Option(value.name, value.id);
                            $(opt).html(value.name);
                            $('select[data-level="' + new_level + '"]').append(opt);
                        });
                        $('select').formSelect({
                            classes: 'room-select'
                        });
                    }
                    else
                    {
                        select.find('option').remove();
                        var opt = new Option(data.lang, '');
                        $(opt).html(data.lang);
                        select.append(opt);
                        $('select[data-level="' + new_level + '"] option[value=""]').attr('disabled','disabled')
                        $.each(data.data, function (index, value) {
                            var opt = new Option(value.name, value.id);
                            $(opt).html(value.name);
                            select.append(opt);
                        });
                        select.formSelect({
                            classes: 'room-select'
                        });
                        var selects = $("select").filter(function() { return  $(this).data("level") > new_level; });
                        selects.each(function () {
                            $(this).parent().parent().remove();
                        });
                    }
                }
            }
        });
    });

    $(document).on('change', 'input[type="checkbox"]', function () {
        var level = $(this).data('level');
        var select = $('select[data-level="' + level + '"]');
        if($(this).prop('checked')) {
            select.attr('name', 'rooms[]');
        } else {
            select.attr('name', 'no_rooms[]');
        }
    });

    $('#add_tag').click(function () {
        $('#add_tag').before('<input type="text" class="form-control mb-2" onkeyup="get_tags(this)" placeholder="Tag" name="tags[]" autocomplete="off"/><div id="list_tags"></div>');
    });

    $('#confirm_remove').click(function () {
        window.location.href = $('#remove_file').attr('data-id') + '/remove';
    });

    $('#add_comment').click(function() {
        var comment = $('#comment').val();
        if(comment !== '') {
            var file = $('#file_id').val();
            $.ajax({
                url:    file + '/comment/save',
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
        var id = btn.attr('data-id');

        var reply = btn.prev().find('textarea').val();
        var file = $('#file_id').val();
        if(reply !== '') {
            $.ajax({
               url:     file + '/comment/save',
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
            url:    file + '/comment/remove',
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

    $('#comment_div').on('click', 'button[name="edit_comment"]', function () {
        var id = $(this).attr('data-id');

        var text_box = $($($(this).parent().parent().parent().find('div')[0]).children()[1]).find('p');
        var text = text_box.text();

        text_box.html('<div class="input-field my-3"><textarea name="reply" class="materialize-textarea">' + text + '</textarea> </div><button name="save_edit" data-id="' + id + '" class="btn btn-primary btn-sm">Save</button>');
        $(this).attr('disabled', true);
    });

    $('#comment_div').on('click', 'button[name="save_edit"]', function () {
        var btn = $(this);
        var id = btn.attr('data-id');
        var edit = btn.prev().children().val();
        var file = $('#file_id').val();
        if(edit !== '') {
            $.ajax({
                url:     file + '/comment/edit',
                type:    'POST',
                data:    ({
                    '_token' : $('[name="_token"]').val(),
                    'comment': edit,
                    'id' : id
                }),
                success: function (data) {
                    btn.parent().text(edit);
                    $('[name="edit_comment"][data-id="' + id + '"]').attr('disabled', false);
                }
            });
        }
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

});
