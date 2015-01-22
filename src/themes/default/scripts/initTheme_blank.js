$(window).resize(function() {
    var height = ($(window).height()-$("#bloc_central").height())/2;
    $("#bloc_central").css("margin-top", height+"px");
});
$(window).load(function(){
    var height = ($(window).height()-$("#bloc_central").height())/2;
    $("#bloc_central").css("margin-top", height+"px");
});
