jQuery(document).ready(function ($) {

    var messageContainer = $('.js-cookie-message');
    var cookieButton = $('.js-cookie-accept');

    var cookieName = 'cookie-acceptance';
    var cookieValue = 'cookie-accepted';
    var cookieLifetime = 7;

    if (getCookie(cookieName)) {
        messageContainer.addClass('is-hidden');
    } else {
        messageContainer.removeClass('is-hidden');
    }

    cookieButton.click(function () {
        messageContainer.addClass('is-hidden');
        setCookie(cookieName, cookieValue, cookieLifetime);
    })
});

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length, c.length);
        }
    }
    return null;
}

function eraseCookie(name) {
    document.cookie = name + '=; Max-Age=-99999999;';
}