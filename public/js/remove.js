$(document).ready(function () {

    /* element remove */
    $(document).on( 'click', 'a[name="element_remove"]', function () {
        var id   = $(this).attr('data-id');
        var type = $(this).attr('data-type');
        console.log(type);
        $('input[name="'+type+'_id"]').val(id);
    });

    $(document).on( 'click', '#confirm_remove', function () {
        var type  = $(this).attr('data-type');
        var model = $(this).attr('data-model');
        window.location.href = '/' + model + '/delete/' + $('input[name="'+ type +'_id"]').val();
    });
    /* element remove end */

});
