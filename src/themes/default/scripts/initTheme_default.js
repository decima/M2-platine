$(window).resize(function() {
    var height = 0;
    var w = $(window);
    var p = $('#page');
    var cp = $('#page_contenu');
    var ll = $('#lateral_left');
    var hasVerticalScrollbar = false;

    if(w.height() > cp.outerHeight(true)){
        height = w.outerHeight(true);
        hasVerticalScrollbar = false;
    }
    else {
        height = cp.outerHeight(true);
        hasVerticalScrollbar = true;
    }

    ll.height(height);
    $("#page_lateral_profil").height(height);

    var window_width = w.width();
    var page_width = p.width();
    var window_innerwidth = window.innerWidth;

    var width_lateral_left = (window_width-page_width)/2;
    ll.width(width_lateral_left);

    // On appelle cette fonction car JS fait de la merde sinon avec les calculs de valeurs
    ll.width();

    // On est dans la config' de merde
    // $(window).width() prend en compte la scrollbar alors qu'elle n'est pas là :/
    if(window_innerwidth > window_width && !hasVerticalScrollbar){
        var diff = window_innerwidth-window_width;
        diff = diff/2;
        ll.width(width_lateral_left+diff);
    }
});
$(window).load(function(){
    var height = 0;
    var w = $(window);
    var p = $('#page');
    var cp = $('#page_contenu');
    var ll = $('#lateral_left');

    var width_lateral_left = (w.width()-p.width())/2;
    ll.width(width_lateral_left);

    if(w.height() > cp.height())
        height = w.height();
    else
        height = cp.height();

    ll.height(height);
    $("#page_lateral_profil").height(height);

    $('textarea').elastic();
});
