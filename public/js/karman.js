$(function () {
    new ClipboardJS('.copy', {
        text: function(trigger) {
            return trigger.getAttribute('data-text');
        }
    });

    $('.ignore-complaint').on('click', function () {
        clickPutRequest($(this), '/karman/complaints/ignore-complaint?id=' + getParam('id'), {});
    });

    $('.ignore-all-complaints').on('click', function () {
        let ids = $(this).data('ids');
        clickPutRequest($(this), '/karman/complaints/ignore-complaints', {
            'ids': ids
        });
    });

    $('.remove-ad').on('click', function () {
        clickDeleteRequest($(this), '/karman/complaints/remove-ad?id=' + getParam('id'), {});
    });

    $('.remove-all-ads').on('click', function () {
        let ids = $(this).data('ids');
        clickDeleteRequest($(this), '/karman/complaints/remove-ads', {
            'ids': ids
        });
    });

    $('.disable-site').on('click', function () {
        $.ajax({
            url: '/karman/status/disable-site',
            dataType: "json",
            type: 'PUT',
            data: {},
            success: function () {
                document.location.reload();
            }
        });
    });

    $('.enable-site').on('click', function () {
        $.ajax({
            url: '/karman/status/enable-site',
            dataType: "json",
            type: 'PUT',
            data: {},
            success: function () {
                document.location.reload();
            }
        });
    });

    $('.disable-cache').on('click', function () {
        $.ajax({
            url: '/karman/status/disable-cache',
            dataType: "json",
            type: 'PUT',
            data: {},
            success: function () {
                document.location.reload();
            }
        });
    });

    $('.enable-cache').on('click', function () {
        $.ajax({
            url: '/karman/status/enable-cache',
            dataType: "json",
            type: 'PUT',
            data: {},
            success: function () {
                document.location.reload();
            }
        });
    });

    function getParam(name) {
        if (!name) {
            return '';
        }

        name = name.replace(/[\[\]]/g, "\\$&");
        let regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
        let url = window.location.href;
        let results = regex.exec(url);
        if (!results) {
            return null;
        }

        if (!results[2]) {
            return '';
        }

        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    function clickPutRequest($button, url, data) {
        return clickRequest($button, url, data, 'PUT')
    }

    function clickDeleteRequest($button, url, data) {
        return clickRequest($button, url, data, 'DELETE')
    }

    function clickRequest($button, url, data, requestType) {
        let urlParts = document.location.pathname.split('/');
        urlParts.pop();
        let redirect  = urlParts.join('/');
        if (!$button.attr('disabled')) {
            $button.attr('disabled', 'disabled');
            $.ajax({
                url: url,
                dataType: "json",
                type: requestType,
                data: data,
                success: function (resp) {
                    $button.removeAttr('disabled');
                    if (resp.success) {
                        document.location = redirect;
                    } else {
                        $('.alert').addClass('show').html('<strong>Ошибка: </strong>' + resp.error);
                    }
                }
            });
        }

    }

    $('.category').on('submit', function () {
        var emoji = $('#emoji-button').html();
        $.ajax({
            url: '/karman/categories/update?id=' + getParam('id'),
            dataType: "json",
            type: 'PUT',
            data: {
                title: $(this).find('[name=title]').val(),
                url: $(this).find('[name=url]').val(),
                emoji: emoji === 'Emoji' ? '' : emoji
            },
            success: function () {
                document.location = '/karman/categories';
            }
        });

        return false;
    });

    const button = document.querySelector('#emoji-button');
    const picker = new EmojiButton();
    picker.on('emoji', emoji => {
        $('#emoji-button').html(emoji);
    });

    button.addEventListener('click', () => {
        picker.togglePicker(button);
    });
});