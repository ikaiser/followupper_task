$(document).ready(function() {

    $('#add_contact').click(function() {
        $(this).before('<input type="text" class="mb-2" name="contact[]" autocomplete="off">');
    });

});
