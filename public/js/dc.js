$(document).ready(function () {

    $('#add_tag').click(function () {
        $('#add_tag').before('<input type="text" class="mb-2" onkeyup="get_tags(this)" name="tags[]" autocomplete="off"/><div id="list_tags"></div>');
    });

    $('#remove_dc').click(function () {
        $('#modal-p').html($('#modal-p').attr('data-text') + ' "' + $(this).attr('data-title') + '" ? ');
    });

    $('#confirm_remove').click(function () {
        window.location.href = 'remove/' + $('#remove_dc').attr('data-id');
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

    $('#dc_sort').change(function () {
        var val = $(this).val();
        var dc = $('#dc').val();
        var project = $('#project').val();
        if(val !== '') {
            $.ajax({
                url:    '/dc/sort',
                type:   'POST',
                data:   ({
                    '_token' : $('[name="_token"]').val(),
                    'sort' : val,
                    'project' : project,
                    'room' : dc,
                }),
                success: function (data) {
                    $('#content_div').empty();
                    $('#content_div').append(data);
                }
            });
        }
    });
});
