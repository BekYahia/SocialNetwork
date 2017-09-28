$(function () {
    "use strict";

    $("input[type=submit]").click(function(e) {
        e.preventDefault();
    });
    $('div.posts blockquote:last-of-type').next('hr').hide();
});
