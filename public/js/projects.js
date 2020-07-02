$(document).ready(function () {

    $('[name="remove_project"]').click(function () {
        var id = $(this).attr('data-id');
        $('#confirm_remove').attr('data-id', id);
    });

    $('#confirm_remove').click(function () {
        var id = $(this).attr('data-id');
        window.location.href = $('[name="remove_project"][data-id="' + id + '"]').attr('data-route');
    });

});
