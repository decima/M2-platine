$(window).resize(function() {
    var height = ($(window).height()-$("#page").height())/2;
    $("#page").css("margin-top", height+"px");

    if($(window).width() < window.innerWidth)
        console.log("true");
    else
        console.log("false");
});
$(window).load(function(){
    var height = ($(window).height()-$("#page").height())/2;
    $("#page").css("margin-top", height+"px");
});
