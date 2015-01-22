$(window).resize(function() {
    var width_lateral_left = ($(window).width()-$("#page").width())/2;
    $("#lateral_left").width(width_lateral_left);

    if($(window).height() > $("#page_contenu").height())
        var height = $(window).height();
    else
        var height = $("#page_contenu").height();

    $("#lateral_left").height(height);
    $("#page_lateral_profil").height(height);
});
$(window).load(function(){
    var width_lateral_left = ($(window).width()-$("#page").width())/2;
    $("#lateral_left").width(width_lateral_left);

    if($(window).height() > $("#page_contenu").height())
        var height = $(window).height();
    else
        var height = $("#page_contenu").height();

    $("#lateral_left").height(height);
    $("#page_lateral_profil").height(height);

    $('textarea').elastic();
});
