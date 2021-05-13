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

    let $map = $('#map');
    if ($map && $map.length) {
        let latitude = $map.data('latitude');
        let longitude = $map.data('longitude');
        let accuracy = $map.data('accuracy');
        let map = L.map('map').setView([latitude, longitude], accuracy);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        L.marker([latitude, longitude]).addTo(map);
    }
});