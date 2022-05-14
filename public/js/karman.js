$(function () {
    new ClipboardJS('.copy', {
        text: function(trigger) {
            return trigger.getAttribute('data-text');
        }
    });

    $('.remove-emoji').on('click', function () {
        let id = $(this).data('id');
        $.ajax({
            url: '/karman/remove-emoji/' + id,
            dataType: "json",
            type: 'DELETE',
            data: {},
            success: function () {
                document.location.reload();
            }
        });
    });

    $('.ignore-complaint').on('click', function () {
        let id = $(this).data('id');
        clickPutRequest($(this), '/karman/ignore-complaint/' + id, {});
    });

    $('.ignore-all-complaints').on('click', function () {
        let ids = $(this).data('ids');
        clickPutRequest($(this), '/karman/ignore-complaints', {
            'ids': ids
        });
    });

    $('.remove-ad').on('click', function () {
        let id = $(this).data('id');
        clickDeleteRequest($(this), '/karman/remove-ad/' + id, {});
    });

    $('.remove-all-ads').on('click', function () {
        let ids = $(this).data('ids');
        clickDeleteRequest($(this), '/karman/remove-ads', {
            'ids': ids
        });
    });

    $('.disable-site').on('click', function () {
        $.ajax({
            url: '/karman/disable-site',
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
            url: '/karman/enable-site',
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
            url: '/karman/disable-cache',
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
            url: '/karman/enable-cache',
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
        let emoji = $('#emoji-button').html();
        let id = $(this).find('[type=submit]').data('id');
        $.ajax({
            url: '/karman/update-category/' + id,
            dataType: "json",
            type: 'PUT',
            data: {
                title: $(this).find('[name=title]').val(),
                url: $(this).find('[name=url]').val(),
                emoji: emoji === 'Emoji' ? '' : emoji,
                synonyms: $(this).find('[name=synonyms]').val(),
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

    if (button) {
        button.addEventListener('click', () => {
            picker.togglePicker(button);
        });
    }

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    if (tooltipTriggerList) {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    }

    let modal = document.getElementById('moveUndefinedModal');
    let moveUndefinedModal = modal ? new bootstrap.Modal(modal) : null;
    $('.move-ad').on('click', function () {
        $('#adId').val($(this).data('adId'));
        $('#adCategoryId').val($(this).data('categoryId'));
        $('#adCategoryParentId').val($(this).data('categoryParentId'));

        $('#categoryLevel1').parents('div:first').removeClass('d-none');
        $('#newCategoryLevel1').parents('div:first').addClass('d-none');
        $('#categoryLevel2').parents('div:first').removeClass('d-none');
        $('#newCategoryLevel2').parents('div:first').addClass('d-none');

        updateCategoriesLevel1();
        updateCategoriesLevel2($(this).data('categoryParentId'));
        $('#moveUndefinedModal').find('.alert').addClass('d-none');

        moveUndefinedModal.show();
    });

    $('.find-and-move-ad').on('click', function () {
        let $report = $('.find-and-move-ad-report');
        let $loading = $report.find('.loading');
        let $text = $report.find('.text');
        $loading.removeClass('d-none');
        $report.removeClass('d-none');
        $text.addClass('d-none');
        let adId = $(this).data('adId');
        $.ajax({
            url: '/karman/find-ad-category/' + adId,
            dataType: "json",
            type: 'GET',
            data: "",
            success: function (response) {
                $loading.addClass('d-none');
                $text.html(response['report']);
                $text.removeClass('d-none')
            }
        });
    });


    $('#categoryLevel1').on('change', function () {
        let categoryLevel1 = $(this).val();
        if (categoryLevel1) {
            updateCategoriesLevel2(categoryLevel1);
            $('#categoryLevel2').parents('div:first').removeClass('d-none');
            $('#newCategoryLevel1').parents('div:first').addClass('d-none');
        } else {
            $('#categoryLevel2').parents('div:first').addClass('d-none');
            $('#newCategoryLevel1').parents('div:first').removeClass('d-none');
        }
    });

    let removeCategoryModalElement = document.getElementById('removeCategoryModal');
    let removeCategoryModal = removeCategoryModalElement ? new bootstrap.Modal(removeCategoryModalElement) : null;
    let $removeCategoryModal = $('#removeCategoryModal');
    $('.remove-category').on('click', function () {
        let adsCount = parseInt($(this).data('adsCount'))
        let categoriesCount = parseInt($(this).data('categoriesCount'));
        let message = '';
        if (adsCount > 0 && categoriesCount > 0) {
            message = 'У категории есть '
                + categoriesCount
                + ' '
                + pluralForm(categoriesCount, 'подкатегория', 'подкатегории', 'подкатегорий')
                + ' и '
                + adsCount
                + ' '
                + pluralForm(adsCount, 'объявление', 'объявления', 'объявлений')
                + '. Удалить всё?';
        } else if (categoriesCount > 0) {
            message = 'У категории есть ' + categoriesCount + ' подкатегорий. Удалить всё?';
        } else if (adsCount > 0) {
            message = 'У категории есть ' + adsCount + ' объявлений. Удалить всё?';
        }

        if (message) {
            $removeCategoryModal.find('.alert').html(message);
            $removeCategoryModal.find('#categoryId').val($(this).data('id'));
            removeCategoryModal.show();
        } else {
            removeCategory($(this).data('id'));
        }
    });

    $removeCategoryModal.find('.remove').on('click', function () {
        removeCategory($removeCategoryModal.find('#categoryId').val())
    });

    function removeCategory(id) {
        $.ajax({
            url: '/karman/remove-category/' + id,
            dataType: "json",
            type: 'DELETE',
            data: "",
            success: function (response) {
                removeCategoryModal.hide();
                document.location = '/karman/categories?cache=0';
            }
        });
    }


    $('#moveUndefinedModal .save').on('click', function () {
        let $moveUndefinedModal = $('#moveUndefinedModal');
        $moveUndefinedModal.find('.alert').addClass('d-none');
        $.ajax({
            url: '/karman/move-ad/?cache=0',
            dataType: "json",
            type: 'PUT',
            data: $('#moveUndefinedModal form').serialize(),
            success: function (response) {
                if (response.error) {
                    $moveUndefinedModal.find('.alert div').text(response.error).removeClass('d-none');
                    $moveUndefinedModal.find('.alert').removeClass('d-none');
                } else {
                    moveUndefinedModal.hide();
                    document.location.reload();
                }
            }
        });
    });

    $('.add-category-level-1').on('click', function () {
        $('#categoryLevel1').parents('div:first').addClass('d-none');
        $('#categoryLevel1').val(0);
        $('#newCategoryLevel1').parents('div:first').removeClass('d-none');
    });
    $('.add-category-level-2').on('click', function () {
        $('#categoryLevel2').parents('div:first').addClass('d-none');
        $('#categoryLevel2').val(0);
        $('#newCategoryLevel2').parents('div:first').removeClass('d-none');
    });

    $('#categoryLevel2').on('change', function () {
        let categoryLevel2 = $(this).val();
        if (categoryLevel2) {
            $('#newCategoryLevel2').parents('div:first').addClass('d-none');
        } else {
            $('#newCategoryLevel2').parents('div:first').removeClass('d-none');
        }
    });

    function updateCategoriesLevel1() {
        $.ajax({
            url: '/karman/get-categories/?cache=0',
            dataType: "json",
            type: 'GET',
            data: {},
            success: function (response) {
                let $categoryLevel1 = $('#categoryLevel1');
                $categoryLevel1.find('option[value!="0"]').remove();
                $.each(response, function (key, categoryLevel1) {
                    let isSelected = $('#adCategoryId').val() == categoryLevel1.id
                        || $('#adCategoryParentId').val() == categoryLevel1.id;
                    $categoryLevel1.append('<option value="'
                        + categoryLevel1.id
                        + '"'
                        + (isSelected ? ' selected' : '')
                        + '>'
                        + categoryLevel1.title
                        + '</option>'
                    );
                });
            }
        });
    }

    function updateCategoriesLevel2(categoryLevel1) {
        $.ajax({
            url: '/karman/get-categories/' + categoryLevel1 + '/?cache=0',
            dataType: "json",
            type: 'GET',
            data: {},
            success: function (response) {
                let $categoryLevel2 = $('#categoryLevel2');
                $categoryLevel2.find('option').remove();
                let $newCategoryLevel2 = $('#newCategoryLevel2').parents('div:first');
                if (response.length) {
                    $categoryLevel2.parents('div:first').addClass('d-none');
                    $newCategoryLevel2.addClass('d-none');
                    $categoryLevel2.parents('div:first').removeClass('d-none');
                    $.each(response, function (key, categoryLevel2) {
                        let isSelected = $('#adCategoryId').val() == categoryLevel2.id;
                        $categoryLevel2.append('<option value="'
                            + categoryLevel2.id
                            + '"'
                            + (isSelected ? ' selected' : '')
                            + '>'
                            + categoryLevel2.title
                            + '</option>'
                        );
                    });
                } else {
                    $categoryLevel2.parents('div:first').addClass('d-none');
                    $newCategoryLevel2.removeClass('d-none');
                }
            }
        });
    }

    if ($('.logs').length) {
        let t;
        function loadLogs (directory, type) {
            $.ajax({
                url: '/karman/get-logs/' + directory + '/' + type,
                dataType: "json",
                type: 'GET',
                data: {},
                success: function (response) {
                    $('.logs').html('');
                    $.each(response.logs, function (i, log) {
                        let className = '';
                        if (log.includes('.INFO')) {
                            className = 'text-primary';
                        } else if (log.includes('.ERROR')) {
                            className = 'text-danger';
                        } else if (log.includes('.WARNING')) {
                            className = 'text-warning';
                        }  else if (log.includes('.DEBUG')) {
                            className = 'text-secondary';
                        }

                        $('.logs').append('<li class="' + className + '">' + log + '</li>');
                    });
                    clearInterval(t);
                    t = setInterval(function () {
                        loadLogs(directory, type, t);
                    }, 1000);
                }
            });
        }
        loadLogs($('.logs').data('directory'), $('.logs').data('type'));
    }

    function pluralForm(count, formFor1, formFor2, formFor5) {
        let intCount = parseInt(count);
        let lastNumber = intCount.toString().substring(-1);
        let form;
        if (lastNumber % 10 == 1 && intCount % 100 != 11) {
            form = formFor1;
        } else if ($.inArray(lastNumber % 10, [2, 3, 4]) !== -1 && $.inArray(count % 100, [12, 13, 14]) == -1) {
            form = formFor2;
        } else {
            form = formFor5;
        }

        return form;
    }
});