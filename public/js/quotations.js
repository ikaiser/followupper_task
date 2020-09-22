$(document).ready(function() {

    /* Quotations */
    $('#quotations_table').DataTable( {
        "lengthChange": true,
        "order": [ [ 2, "desc" ] ],
        "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
        "pageLength": 500
    });

    $('#quotation_status_table').DataTable( {
        "lengthChange": true,
        "order": [ [ 1, "asc" ] ],
        "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
        "pageLength": 500
    });

    /* element remove */
    $('a[name="element_remove"]').click(function () {
        var id   = $(this).attr('data-id');
        var type = $(this).attr('data-type');
        $('input[name="'+type+'_id"]').val(id);
    });

    $('#confirm_remove').click(function () {
        var type  = $(this).attr('data-type');
        var model = $(this).attr('data-model');
        window.location.href = model + '/' + $('input[name="'+ type +'_id"]').val() + '/remove';
    });
    /* element remove end */

    $('#manual_sequential').change(function () {
        var manual = $('#manual_sequential');
        var sequential = $('#sequential');
        if(manual.prop('checked') === true) {
            sequential.prop('disabled', false);
        } else {
            sequential.val('');
            M.updateTextFields();
            sequential.prop('disabled', true);
        }
    });

    $('#project_closed').change(function () {
        var closed = $('#project_closed');
        var invoice_amount = $('#invoice_amount');
        if(closed.prop('checked') === true) {
            invoice_amount.prop('disabled', false);
        } else {
            invoice_amount.val('');
            M.updateTextFields();
            invoice_amount.prop('disabled', true);
        }
    });

    $('#company').keyup(function(){
        var query = $(this).val();
        $('#list_company').fadeOut();

        if(query !== '' && query.length > 3)
        {
            $.ajax({
                url:"/company/fetch",
                method:"GET",
                data:{
                    _token : $('input[name="_token"]').val(),
                    query:query
                },
                success:function(data){
                    $('#list_company').fadeIn();
                    $('#list_company').html(data);
                }
            });
        }
    });

    $(document).on('click', 'li', function() {
        if ($(this).data('ref') === 'company') {
            $('#company').val($(this).text());
            $('#list_company').fadeOut();

            var company_id = $(this).data('value');

            /* Get contacts */
            $.ajax({
                url:"/company/get_contacts",
                method:"GET",
                data:{
                    _token : $('input[name="_token"]').val(),
                    company :company_id
                },
                success:function(data){

                    var contact_select = $('#company_contact');
                    var option = '';
                    var select_text = $('#company_contact option[value=""]').text();

                    contact_select.empty();
                    contact_select.append('<option value="" disabled hidden selected>' + select_text + '</option>');

                    $.each(data, function (index, value) {
                        option = new Option(value.name, value.id);
                        $(option).html(value.name);
                        contact_select.append(option);
                    });
                    contact_select.formSelect({});
                }
            });

            /* Get and set code */
            $.ajax({
                url:"/company/get_code",
                method:"GET",
                data:{
                    _token : $('input[name="_token"]').val(),
                    company :company_id
                },
                success:function(code){
                  $("#code").val(code);
                }
            });
        }
    });

    $(document).on('keyup', '[name="researchers[]"]', function() {
        var list = $(this).parent().next();
        var query = $(this).val();
        var btn = $(this).next();

        if (query !== '' && query.length > 3) {
            $.ajax({
                url: "/users/fetch_researchers",
                method: "GET",
                data: {
                    _token : $('input[name="_token"]').val(),
                    query: query,
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
        if( $(this).data('ref') === 'researcher' ) {
            var list = $(this).parent().parent();
            var id = $(this).data('value');
            var label = $('#researcher_label').text();
            var input = $(list.prev().children()[0]);
            if(input.prop('nodeName') === 'LABEL') {
                input = $(list.prev().children()[1]);
            }


            input.val($(this).text());
            input.attr('data-role', $(this).data('role'));
            input.attr('data-id', id);

            list.parent().append('<div class="input-field my-3"><label for="researchers">' + label + '</label><input autocomplete="off" type="text" name="researchers[]" class="my-2" style="width: 90%"/><button type="button" class="btn btn-small waves-effect waves-light red" name="deassign_researcher" style="display: none; padding: 0 1rem; margin-left: 0.2rem"><i class="material-icons">delete</i></button></div><div></div>');
            list.fadeOut();
        }
    });

    $(document).on('click', 'button[name="deassign_researcher"]', function() {
        var form_div = $(this).parent();
        var list_div = form_div.next();

        form_div.remove();
        list_div.remove();
    });
});

function open_filters(){
  $("#filters").fadeIn(400);
  $(".filters-btn").attr("onclick", "close_filters()");
}

function close_filters(){
  $("#filters").fadeOut(200);
  $(".filters-btn").attr("onclick", "open_filters()");
}
