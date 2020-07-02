$(document).ready(function(){


    $(document).on('keyup', '[name="project_list"]', function(){
        var query = $(this).val();
        var list = $(this).next();
        if (query != '' && query.length > 3)
        {
            // var _token = $('input[name="_token"]').val();
            $.ajax({
                url:"/projects/fetch",
                method:"GET",
                data:{query:query},
                success:function(data){
                    list.fadeIn();
                    list.html(data);
                }
            });
        }
    });

    $('#doc_type').keyup(function(){
        var query = $(this).val();
        if(query != '')
        {
            // var _token = $('input[name="_token"]').val();
            $.ajax({
                url:"/file/extension/fetch",
                method:"GET",
                data:{query:query},
                success:function(data){
                    $('#list_doc_type').fadeIn();
                    $('#list_doc_type').html(data);
                }
            });
        }
    });

    $(document).on('keyup', '[name="tags[]"]', function(){
        var list = $(this).next();
        var query = $(this).val();
        if (query != '' && query.length > 3) {
            // var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "/file/tags/fetch",
                method: "GET",
                data: {
                    query: query
                },
                success: function (data) {
                    list.fadeIn();
                    list.html(data);
                }
            });
        }
    });

    $(document).on('click', 'li', function(){

        if( $(this).data('ref') === 'states' ){
            var list = $(this).parent().parent();
            var input = list.prev();
            var ids = $('#projects');

            input.val($(this).text());
            input.attr('data-id', $(this).data('value'));

            ids.val('');
            $('[name="project_list"]').each(function() {
                ids.val(ids.val() + $(this).attr('data-id') + ';');
            })
            list.fadeOut();
            list.after('<input autocomplete="off" type="text" name="project_list" class="form-control" data-id="" placeholder="Progetto"/> <div id="list"></div>');
            return;
        }

        if($(this).data('ref') === 'doc_type'){
            $('#doc_type').val($(this).text());
            $('#list_doc_type').fadeOut();
            return;
        }

        if($(this).data('ref') === 'tags'){
            var list = $(this).parent().parent();
            var input = list.prev();
            input.val($(this).text());
            list.fadeOut();
            list.after('<input type="text" class="form-control mb-2" placeholder="Tag" name="tags[]" autocomplete="off"/><div id="list_tags"></div>')
            return;
        }

    });

    $(document).on('click', 'button[name="add_query"]', function () {
        $(this).text('AND');
        $(this).attr('name', 'change_query');

        $(this).parent().after('<input type="hidden" name="rel[]" value="and">');
        $(this).parent().parent().append('<div class="input-field my-2"> <label for="search_text"> Query </label> <input type="text" class="form-control input-button" name="search_text[]" title="Text"> <button type="button" id="add_query" name="add_query" class="btn btn-floating waves-effect waves-light ml-2"> <i class="material-icons">add</i> </button> <button type="button" class="btn btn-floating waves-effect waves-light red ml-2" name="remove_query"> <i class="material-icons">delete</i> </button> </div>')
    });

    $(document).on('click', 'button[name="change_query"]', function () {
        if($(this).text() === 'AND') {
            $(this).text('OR');
            $(this).parent().next().val('or');
        } else {
            $(this).text('AND');
            $(this).parent().next().val('and');
        }
    });

    $(document).on('click', 'button[name="remove_query"]', function() {
        if($(this).parent().children('input[type="hidden"]').length === 0)
        {
            var span = $(this).parent().prev().prev().find('button[name="change_query"]');
            console.log(span);
            span.attr('name', 'add_query');
            span.html('<i class="material-icons">add</i>')

            $(this).parent().prev().find('input[type="hidden"]').remove();
        }
        $(this).parent().remove();

    });

});
