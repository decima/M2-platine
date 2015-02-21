function theme_init(){
    var height = 0;
    var w = $(window);
    var p = $('#page');
    var cp = $('#page_contenu');
    var plp = $('#page_lateral_profil');
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
    plp.height(height);

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

    var plp_width = Math.ceil(plp.width()+1);
    var cp_width = Math.ceil(cp.width()+1);
    var p_width = plp_width+cp_width;
    p.width(p_width);
}

$(window).resize(function() {
    theme_init();
});
$(window).load(function(){
    theme_init();
    $('textarea').elastic();
});
