jQuery(document).ready(function ($) {

    var loadingIcon = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:transparent;display:block;"width="10px" height="10px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"> <circle style="fill:transparent !important;"cx="50" cy="50" r="43" stroke="#ffffff" stroke-width="15" stroke-linecap="round" fill="none"> <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;180 50 50;720 50 50" keyTimes="0;0.5;1"></animateTransform> <animate attributeName="stroke-dasharray" repeatCount="indefinite" dur="1s" values="17.59291886010284 158.33626974092556;87.96459430051421 87.96459430051421;17.59291886010284 158.33626974092556" keyTimes="0;0.5;1"></animate> </circle> </svg>';

    $(document).on('click', 'aside .js-edit', function () {
        var parent = $(this).closest('.information'),
            id = parent.attr('data-id'),
            submitButton = $(this);

        $.ajax({
            url: "/wp-json/traces/edit",
            method: 'POST',
            dataType: 'json',
            data: {
                id: id
            },
            beforeSend: function () {
                submitButton.html(loadingIcon);
            },
            success: function (response) {
                switch (response.status) {
                    case 'success':
                        parent.replaceWith(response.html);
                        break;
                }
            }
        });
    });

    $(document).on('click', 'aside .js-save', function () {
        var parent = $(this).closest('.information'),
            id = parent.attr('data-id'),
            submitButton = $(this);

        $.ajax({
            url: "/wp-json/traces/save",
            method: 'POST',
            dataType: 'json',
            data: {
                id: id,
                fields: parent.serializeArray()
            },
            beforeSend: function () {
                submitButton.html(loadingIcon);
            },
            success: function (response) {
                switch (response.status) {
                    case 'success':
                        parent.replaceWith(response.html);
                        break;
                }
            }
        });

        return false;
    });

    $(document).on('click', 'aside .js-revert', function () {
        var parent = $(this).closest('.information'),
            id = parent.attr('data-id'),
            submitButton = $(this);

        $.ajax({
            url: "/wp-json/traces/revert",
            method: 'POST',
            dataType: 'json',
            data: {
                id: id
            },
            beforeSend: function () {
                submitButton.html(loadingIcon);
            },
            success: function (response) {
                switch (response.status) {
                    case 'success':
                        parent.replaceWith(response.html);
                        break;
                }
            }
        });

        return false;
    });

    $(document).on('click', 'aside .js-cancel', function () {
        var parent = $(this).closest('.information'),
            id = parent.attr('data-id'),
            submitButton = $(this);

        $.ajax({
            url: "/wp-json/traces/cancel",
            method: 'POST',
            dataType: 'json',
            data: {
                id: id
            },
            beforeSend: function () {
                submitButton.html(loadingIcon);
            },
            success: function (response) {
                switch (response.status) {
                    case 'success':
                        parent.replaceWith(response.html);
                        break;
                }
            }
        });

        return false;
    });

    $(document).on('click', 'aside .js-filter', function () {
        $(this).closest('.traces-list').find('.traces-list__filters').toggleClass('is-active');

    });
});