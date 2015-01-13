<?php
if (file_exists("../config/settings.php")) {
    // header("Location: ../");
}
?>
<!doctype HTML>
<html>
    <head>
        <title>Installation de Jinn</title>
        <meta charset="utf-8"/>
        <style>
            input{width: 100%; display:block;}
        </style>
    </head>
    <body>
        <form method="POST" action="config.php">

            <?php if (!is_writeable("../config/")): ?>
                <span style="color:red;">
                    Le dossier "config" n'est pas accessible, changez les permissions du dossier /config.
                </span>
                <input type='text' name='config_show_only' value='show'/>
            <?php endif; ?>
            <fieldset>
                <legend>Base de données</legend>
                Serveur : <input name="data[db][server]" placeholder="db server" value="localhost"/><br/>Utilisateur : 
                <input name="data[db][login]" placeholder="db login" value="root"/><br/>Mot de passe :
                <input name="data[db][password]" placeholder="db password" value=""/><br/>Base de données :
                <input name="data[db][database]" placeholder="db database" value="jinn"/><br/>préfixe des tables :
                <input name="data[db][prefix]" placeholder="prefixe" value="jn_"/>



            </fieldset>

            <fieldset>
                <legend>configuration</legend>
                Clée de sécurité : <input name="data[site][cookie]" value="<?= rtrim(base64_encode(md5(microtime())), "="); ?>"/><br/>

                Email administrateur : <input name="data[site][admin][login]" placeholder="email"/>
                <br/>Mot de passe administrateur : <input name="data[site][admin][password]" placeholder="admin password"/>



            </fieldset>
            <fieldset>
                <input type='submit'/>

            </fieldset>

        </form>


    </body>
</html>