<?php
/*
if (file_exists("../config/settings.php")) {
    // header("Location: ../");
}
*/

    if(isset($_POST['data']) AND !empty($_POST['data'])){
        $myfile = "";

        foreach($_POST['data'] as $k => $v){
            $k1 = strtoupper($k);
            foreach($v as $k2 => $v2){
                $k2 = strtoupper($k2);
                $myfile .= "define(\"CONFIG_".$k1."_".$k2."\", \"".$v2."\");\n";
            }
        }

        $writingOK = true;
        if ((isset($_POST['config_show_only']) && $_POST['config_show_only'] == "show")) {
            //show only if folder is not writeable
            $myfile = "<?php\n".$myfile."?>";
            $writingOK = false;
        }
        else {
            //write config file if folder is writeable
            if(!file_put_contents("../config/settings.php", $myfile)){
                $myfile = "<?php\n".$myfile."?>";
                $writingOK = false;
            }
        }
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Jinn : Installation</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link href="styles/design.css" rel="stylesheet" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,600,700,400' rel='stylesheet' type='text/css'>
        <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
        <script type="text/javascript" src="scripts/jquery.elastic.source.js"></script>
        <script type="text/javascript" src="scripts/initstyle.js"></script>
    </head>
    <body>
        <div id="lateral_left"></div>
        <div id="page">
            <div id="page_lateral_profil">
                <!-- Menu latéral -->
                <div class="clear"></div>
            </div>
            <div id="page_contenu">
                <div class="titre titre_page">Installation</div>
                <div class="page_contenu_sep"></div>
                <?php if (isset($writingOK) && !$writingOK): ?>
                    <div class="actualite">
                        <?php if ((isset($_POST['config_show_only']) && $_POST['config_show_only'] == "show")): ?>
                            <div class="bandeau_info">
                                <div class="bandeau_info_inside">
                                    <div class="bandeau_info_ico"><i class="fa  fa-exclamation-triangle fa-fw"></i></div>
                                    <div class="bandeau_info_txt">Le répertoire <b>/config</b> n'est pas accessible en écriture.<br />Copiez puis collez ceci dans un fichier <i>settings.php</i> dans le répertoire <b>config/</b> pour continuer.</div>
                                    <div class="bandeau_info_close"></div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bandeau_info">
                                <div class="bandeau_info_inside">
                                    <div class="bandeau_info_ico"><i class="fa  fa-exclamation-triangle fa-fw"></i></div>
                                    <div class="bandeau_info_txt">Une erreur s'est produite dans la création et/ou l'écriture du fichier.<br />Copiez puis collez ceci dans un fichier <i>settings.php</i> dans le répertoire <b>config/</b> pour continuer.</div>
                                    <div class="bandeau_info_close"></div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- Affichage du contenu du fichier -->
                        <textarea class="codebloc"><?=$myfile ?></textarea>
                    </div>
                <?php elseif(isset($writingOK) && $writingOK): ?>
                    <div class="actualite">
                        <div class="bandeau_info">
                            <div class="bandeau_info_inside">
                                <div class="bandeau_info_ico"><i class="fa  fa-info-circle fa-fw"></i></div>
                                <div class="bandeau_info_txt">Création et remplissage du fichier <i>settings.php</i> terminé.</div>
                                <div class="bandeau_info_close"></div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                <form method="POST" action="./">
                    <?php if (!is_writeable("../config/")): ?>
                    <div class="bandeau_info">
                        <div class="bandeau_info_inside">
                            <div class="bandeau_info_ico"><i class="fa  fa-exclamation-triangle fa-fw"></i></div>
                            <div class="bandeau_info_txt">Le répertoire <b>/config</b> n'est pas accessible en écriture.<br />Veuillez modifier les permissions du répertoire avant de poursuivre.<input type='hidden' name='config_show_only' value='show'/></div>
                            <div class="bandeau_info_close"><a href="" class="bandeau_info_close_lnk" title="Fermer"><i class="fa fa-times"></i></a></div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="formulaire">
                        <div class="titre sous_titre_page"><i class="fa  fa-database fa-fw"></i> Base de données</div>
                        <table>
                            <tr>
                                <td class="formulaire_item_name">Serveur : </td>
                                <td><input type="text" name="data[db][server]" value="localhost"/></td>
                            </tr>
                            <tr>
                                <td class="formulaire_item_name">Utilisateur : </td>
                                <td><input type="text" name="data[db][login]" value="root"/></td>
                            </tr>
                            <tr>
                                <td class="formulaire_item_name">Mot de passe : </td>
                                <td><input type="password" name="data[db][password]" value=""/></td>
                            </tr>
                            <tr>
                                <td class="formulaire_item_name">Base de données : </td>
                                <td><input type="text" name="data[db][database]" value="jinn"/></td>
                            </tr>
                            <tr>
                                <td class="formulaire_item_name">Préfixe des tables : </td>
                                <td><input type="text" name="data[db][prefix]" value="jn_"/></td>
                            </tr>
                        </table>
                        <div class="titre sous_titre_page sous_titre_page_margeTop"><i class="fa  fa-wrench fa-fw"></i> Configuration</div>
                        <table>
                            <tr>
                                <td class="formulaire_item_name">Clé de sécurité : </td>
                                <td><input type="text" name="data[site][cookie]" value="<?= rtrim(base64_encode(md5(microtime())), "="); ?>"/></td>
                            </tr>
                            <tr>
                                <td class="formulaire_item_name">Email administrateur : </td>
                                <td><input type="email" name="data[admin][login]"/></td>
                            </tr>
                            <tr>
                                <td class="formulaire_item_name">Mot de passe administrateur : </td>
                                <td><input type="password" name="data[admin][password]"/></td>
                            </tr>
                        </table>
                        <div class="page_contenu_sep"></div>
                        <input type='submit' class="btn actualite_btn"/>
                    </div>
                </form>
                <?php endif; ?>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
    </body>
</html>