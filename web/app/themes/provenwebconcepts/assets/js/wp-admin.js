jQuery(document).ready(function ($) {

    var field = $('div[data-name="groupID"]');

    field.each(function () {
        if ($(this).find('input').val()) {
            $(this).find('input').prop('disabled', true);
            $(this).find('button').prop('disabled', true);
        }
    });

});