function get_tags(element) {
    var list = $(element).next();
    var query = element.value;
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
}
