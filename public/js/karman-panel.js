$(function () {
    $('.site-checkbox').on('change', function () {
        $.ajax({
            url: $(this).is(':checked') ? '/karman/enable-site' : '/karman/disable-site',
            dataType: "json",
            type: 'PUT',
            data: {},
            success: function () {}
        });
    });
    $('.cache-checkbox').on('change', function () {
        $.ajax({
            url: $(this).is(':checked') ? '/karman/enable-cache' : '/karman/disable-cache',
            dataType: "json",
            type: 'PUT',
            data: {},
            success: function () {}
        });
    });
});