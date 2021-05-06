$(function() {
    let $youtube = $('.youtube');
    let url = $youtube.data('url');
    if (url.length) {
        $.ajax({
            type: "GET",
            url: url,
            data: {},
            dataType: "json",
            success: function (resp) {
                $youtube.html('');
                $('<iframe width="560" height="315" src="https://www.youtube.com/embed/'
                    + resp.video_id
                    + '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
                ).appendTo('.youtube');
            }
        });
    }

    $('.show-phone').on('click', function () {
        let phone = $(this).data('phone');
        $(this).html(phone);
    });
});