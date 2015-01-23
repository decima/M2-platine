$(window).resize(function() {
    var height = ($(window).height()-$("#page").height())/2;
    $("#page").css("margin-top", height+"px");
});
$(window).load(function(){
    var height = ($(window).height()-$("#page").height())/2;
    $("#page").css("margin-top", height+"px");
});
