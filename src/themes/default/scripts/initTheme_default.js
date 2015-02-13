$(window).resize(function() {
    var height = 0;
    var w = $(window);
    var p = $('#page');
    var cp = $('#page_contenu');
    var plpc = $('#page_lateral_profil_contenu');
    var ll = $('#lateral_left');
    var hasVerticalScrollbar = false;

    var w_height = w.height();
    var cp_height = cp.height();
    var plpc_height = plpc.height();

    if(w_height >= cp_height && w_height >= plpc_height){
        height = w_height;
        hasVerticalScrollbar = false;
    }
    else if(cp_height > plpc_height){
        height = cp_height;
        hasVerticalScrollbar = true;
    }
    else{
        height = plpc_height;
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
    var plpc = $('#page_lateral_profil_contenu');
    var ll = $('#lateral_left');
    var hasVerticalScrollbar = false;

    var w_height = w.height();
    var cp_height = cp.height();
    var plpc_height = plpc.height();

    if(w_height >= cp_height && w_height >= plpc_height){
        height = w_height;
        hasVerticalScrollbar = false;
    }
    else if(cp_height > plpc_height){
        height = cp_height;
        hasVerticalScrollbar = true;
    }
    else{
        height = plpc_height;
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
    /*
    var height = 0;
    var w = $(window);
    var p = $('#page');
    var cp = $('#page_contenu');
    var ll = $('#lateral_left');
    var plpc = $('#page_lateral_profil_contenu');

    var width_lateral_left = (w.width()-p.width())/2;
    ll.width(width_lateral_left);

    var w_height = w.height();
    var cp_height = cp.height();
    var plpc_height = plpc.height();

    if(w_height >= cp_height && w_height >= plpc_height) {
        height = w_height;
    }
    else if(cp_height > plpc_height) {
        height = cp_height;
    }
    else {
        height = plpc_height;
    }


    ll.height(height);
    $("#page_lateral_profil").height(height);
    */
    $('textarea').elastic();
});
