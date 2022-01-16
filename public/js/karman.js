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
});