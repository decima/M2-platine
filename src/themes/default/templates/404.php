<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CONFIG_SITE_ENCODE; ?>">
    <title><?php echo CONFIG_SITE_TITLE;?></title>
    <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="<?php echo Page::url("/themes/default/styles/body.css"); ?>">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,600,700,400' rel='stylesheet' type='text/css'>
    <style>
        body {
            background: #1A65B5;
        }
        #smiley {
            font-size: 180px;
            color: #E6E6DD;
        }
        #description,
        #liens {
            margin-top: 30px;
            font-size: 22px;
            line-height: 30px;
            color: #E6E6DD;
        }
        #liens {
            font-size: 14px;
        }
        #liens a {
            text-decoration: none;
            color: #E6E6DD;
        }
    </style>
    <!-- Scripts -->
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="<?php echo Page::url("/themes/default/scripts/jquery.elastic.source.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo Page::url("/themes/default/scripts/initTheme_blank.js"); ?>"></script>
    <!-- Meta/Communs à l'appli -->
    <?php Theme::head(); ?>
</head>

<body>
    <div id="page">
        <div id="smiley">:(</div>
        <div id="description">La page que vous désirez consulter n'existe pas ou plus.<br />Nous collectons simplement des informations relatives aux erreurs, puis nous allons vous rediriger vers l'accueil du site. (0% effectués)</div>
        <div id="liens"><a href="http://blue.simsideo.net/">Accueil</a> · Code erreur : 404_NOT_FOUND</div>
    </div>
</body>
</html>
