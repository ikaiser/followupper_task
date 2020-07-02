$(document).ready(function () {
    $(document).on('keyup', '[name="users[]"]', function(){
        var list = $(this).parent().next();
        var query = $(this).val();
        var btn = $(this).next();

        var role = $(this).attr('data-role');
        var project = $(this).attr('data-project');
        if(typeof(project) === 'undefined') {
            project = 0;
        }
        if (query != '' && query.length > 3) {
            $.ajax({
                url: "/users/fetch",
                method: "GET",
                data: {
                    query: query,
                    role: role,
                    project: project,
                },
                success: function (data) {
                    btn.show();
                    list.fadeIn();
                    list.html(data);
                }
            });
        } else if(query.length === 0) {
            list.hide();
            btn.hide();
        }
    });

    $(document).on('click', 'li', function(){
        if( $(this).data('ref') === 'user' ){
            var list = $(this).parent().parent();
            var input = $(list.prev().children()[0]);
            var id= $(this).data('value');

            input.val($(this).text());
            input.attr('data-role', $(this).data('role'));
            input.attr('data-id', id);

            var project = $(this).attr('data-project');

            if(typeof(project) !== 'undefined') {
                input.attr('data-project', project);

                list.parent().append('<div class="input-field my-3"><input autocomplete="off" type="text" name="users[]" class="w-50 my-2" data-role="' + $(this).data('role') + '" data-project="' + project + '"/><button class="btn btn-small waves-effect waves-light red" name="deassign_user" style="display: none;"><i class="material-icons">delete</i></button></div><div class="w-50"></div>');
            } else {
                list.parent().append('<div class="input-field my-3"><input autocomplete="off" type="text" name="users[]" class="w-50 my-2" data-role="' + $(this).data('role') + '"/><button class="btn btn-small waves-effect waves-light red" name="deassign_user" style="display: none;"><i class="material-icons">delete</i></button></div><div class="w-50"></div>');
            }

            if($('#reminder_date').length > 0) {
                if($('#reminded_user').length == 0) {
                    $('#schedule').parent().parent().after('<div class="input-field my-3"> <select id="reminded_user" name="reminded_user" class="form-control"> </select> <label for="reminded_user">Utente principale</label> </div>')
                }

                var opt = new Option($(this).text(), id);
                $(opt).html($(this).text());
                $("#reminded_user").append(opt);

                $('select').formSelect({
                    classes: 'room-select'
                });
            }
            list.fadeOut();
        }
    });

    $(document).on('click', 'button[name="deassign_user"]', function() {
        var form_div = $(this).parent();
        var list_div = form_div.next();

        var user =  $(this).prev().attr('data-id');
        var option = $('option[value="' + user + '"]');

        option.remove();

        $('select').formSelect({
            classes: 'room-select'
        });

        form_div.remove();
        list_div.remove();
    });
});
