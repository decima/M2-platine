<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CONFIG_SITE_ENCODE; ?>">
        <title><?php echo CONFIG_SITE_TITLE;?></title>
        <!-- CSS -->
        <link href="<?php echo Page::url("/themes/default/styles/font-opensans.css"); ?>" rel="stylesheet" type="text/css">
        <link href="<?php echo Page::url("/themes/default/styles/body_admin.css"); ?>" rel="stylesheet" type="text/css">
        <link href="<?php echo Page::url("/themes/default/styles/font-awesome.min.css"); ?>" rel="stylesheet" type="text/css">
        <!-- Scripts -->
        <script type="text/javascript" src="<?php echo Page::url("/themes/default/scripts/jquery-1.11.2.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo Page::url("/themes/default/scripts/jquery.elastic.source.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo Page::url("/themes/default/scripts/initTheme_default.js"); ?>"></script>
        <!-- Meta/Communs à l'appli -->
        <?php Theme::head(); ?>
    </head>
    <body>
        <div id="lateral_left"></div>
        <div id="page">
            <?php Theme::showMenu(); ?>
            <div id="page_contenu">
                <?php if(Theme::$title != null): ?>
                <div class="titre titre_page"><?php echo Theme::$title; ?></div>
                <div class="page_contenu_sep"></div>
                <?php endif; ?>
                <?php echo Theme::displayNotification(); ?>
                <?php Theme::body(); ?>
            </div>
        </div>
        <div class="clear" id="clear_page"></div>
    </body>
</html>