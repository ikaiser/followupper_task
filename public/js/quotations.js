$(document).ready(function() {

    $('#quotations_table').DataTable( {
        "lengthChange": false,
    });

    $('a[name="quotation_remove"]').click(function () {
        var id = $(this).attr('data-id');
        $('input[name="quotation_id"]').val(id);
    });

    $('#confirm_quotation_remove').click(function () {
        window.location.href = 'quotations/' + $('input[name="quotation_id"]').val() + '/remove';
    });

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
