$(document).ready(function() {

    $('#add_contact').click(function() {
        $(this).before('<input type="text" class="mb-2" name="contact[]" autocomplete="off">');
    });

    $('a[name="company_remove"]').click(function () {
        var id = $(this).attr('data-id');
        $('input[name="company_id"]').val(id);
    });

    $('#confirm_company_remove').click(function () {
        window.location.href = 'company/' + $('input[name="company_id"]').val() + '/remove';
    });

});
