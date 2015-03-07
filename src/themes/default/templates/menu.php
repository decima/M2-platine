<div id="page_lateral_profil">
    <div id="page_lateral_profil_contenu">
        <?php method_invoke("Widget", "runWidgets", array(0, function( $w){Theme::add_to_menu($w);})); ?>
        <?php Themed::menu(); ?>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>