String.prototype.endsWith = function (suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};
$(document).ready(function () {
    setInterval(function () {
        $.get(window.location.href + "/ajax/", function (data) {
            $(".messagerie").remove();
            for (var index = data.length - 1; index >= 0; index--) {
                var icon = '<i class="fa fa-4x fa-user fa-inverse"></i>';
                if (window.location.href.endsWith(data[index].rid)) {
                    icon = '<i class="fa fa-4x fa-user"></i>';
                }

                var mt = data[index];
                console.log(mt);
                var m = '<div class="messagerie"><div class="messagerie_avatar_area">\n\
<div class="messagerie_avatar">\n\
    ' + icon + '\n\
</div><div class="messagerie_nom"><a>' + mt.sender_firstname + '<br>' + mt.sender_lastname + '</a></div> \n\
</div>\n\
<div class="messagerie_bloc_message messagerie_bloc_message_decalageG">\n\
<div class="messagerie_bloc_message_date">\n\
</div> <div class="messagerie_bloc_texte_large">' + mt.message + '</div></div><div class="clear"></div></div>';
                $("#messaging").before(m);
            }
        });


    }, 2000);


    $("#messaging textarea").keypress(function (e) {
        if (e.which == 13) {
            send_message($("#messaging textarea").val());
            $('html, body').scrollTop($(document).height() - $(window).height());
            $("#messaging textarea").val("");
        }
    });

    $(".actualite_btn").click(function () {

        send_message($("#messaging textarea").val());
        $('html, body').scrollTop($(document).height() - $(window).height());
        $("#messaging textarea").val("");

    });
});

function send_message(e) {
    $.post(window.location.href + "/ajax/send", {message: e}, function (data) {
    });
}
