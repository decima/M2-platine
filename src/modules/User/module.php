<?php

/**
 * @moduleName User
 *
 *
 * */
require_once("UserObject.php");

class User implements Module {

    public function info() {
        return array(
            "name" => "User",
            "readablename" => "User Manager"
        );
    }

    public function install() {
        try {
            return self::create(CONFIG_ADMIN_LOGIN, CONFIG_ADMIN_PASSWORD, "Admin", "Administrator");
        } catch (Exception $e) {
            return false;
        }
    }

    public function schema($schema = array()) {
        UserObject::schema($schema);
        return $schema;
    }

    public function widget($item = array()) {
        $item["user_logged"] = array("permissions" => "access content", "callback" => array("User", "widget_user_logged"));
        return $item;
    }

    public function widget_user_logged() {
        $output = "";
        $user = self::get_user_logged();

        if ($user != null) {
            $output .= "<div id=\"page_lateral_profil_avatar\">";
            $output .= "<img alt=\"\" src=\"\"/>";
            $output .= "</div>";
            $output .= "<div id=\"page_lateral_profil_nom\">";
            $output .= Theme::linking(Page::url("/profile"), "<i class=\"fa fa-gear fa-fw\"></i> $user->firstname  $user->lastname");
            $output .= "</div>";
            $output .= "<div class=\"page_lateral_profil_sep\"><div class=\"page_lateral_profil_sep_barre\"></div></div>";
            $output .= "<div id=\"page_lateral_liens\">";
            $output .= Theme::linking(Page::url("/"), "<i class=\"fa fa-tachometer fa-fw\"></i>");
            $output .= Theme::linking(Page::url("/logout"), "<i class=\"fa fa-power-off fa-fw\"></i>");
            $output .= "</div>";
        }
        return $output;
        /*
          return Theme::listing(array(
          Theme::linking(Page::url("/profile"), "Profil"),
          Theme::linking(Page::url("/logout"), "deconnexion"),
          ));
         */
    }

    public static function create($email, $password, $firstname = "", $lastname = "") {
        $user = new UserObject();
        $user->email = $email;
        $user->password = $password;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $d = $user->save();
        $u2 = new UserObject();
        if ($u2->load_by_email($email)) {
            method_invoke_all("hook_user_create", array($u2->uid));
        }
        return $d;
    }

    public function menu($item = array()) {
        $item['/profile'] = array(
            "access" => "access content",
            "callback" => array("User", "page_profile")
        );
        $item['/profile/@'] = array(
            "access" => "access content",
            "callback" => array("User", "page_profile")
        );
        $item['/'] = array(
            "access" => "full access",
            "callback" => array("User", "page_home")
        );
        $item['/signin'] = array(
            "access" => "create user",
            "callback" => array("User", "page_signin")
        );
        $item['/logout'] = array(
            "access" => "full access",
            "callback" => array("User", "page_logout")
        );
        return $item;
    }

    public static function page_home() {
        if (isset($_POST['submit-login'])) {

            $user = new UserObject();
            if (!$user->load_by_email_and_password($_POST['login'], $_POST['password'])) {
                Notification::statusNotify(t("les identifants de connexion sont invalides"), Notification::STATUS_ERROR);
            } else {
                $_SESSION['logged'] = $user->{$user->index()[0]};
            }
        }
        if (isset($_SESSION['logged']) && $_SESSION['logged'] > 0) {
            self::page_main();
        } else {
            self::page_login();
        }
    }

    public static function get_user_logged() {
        $user = new UserObject();
        if (isset($_SESSION['logged']) AND $user->load($_SESSION['logged'])) {
            return $user;
        }
        return null;
    }

    public static function get_user_logged_id() {
        if (isset($_SESSION['logged'])) {
            return $_SESSION['logged'];
        }
        return null;
    }

    public static function page_login() {
        $theme = new Theme();
        $title = t("Bienvenue !");
        $contenu = t("Connectez-vous et découvrez votre tout nouveau réseau social.");
        $theme->add_to_body($contenu, $title);

        $f = new Form("POST", Page::url("/"));
        $t = (new InputElement("login", t("Identifiant : "), ""));
        $f->addElement($t);
        $t = (new InputElement("password", t("Mot de passe : "), "", "password"));
        $f->addElement($t);

        $t = (new InputElement("submit-login", "", t("Connexion"), "submit"));
        $f->addElement($t);
        $formulaire = $theme->forming($f);
        $theme->add_to_body($formulaire, t("Connexion"));
        $theme->process_theme(Theme::STRUCT_BLANK);
    }

    public static function page_signin() {
        if (isset($_POST['submit-signin'])) {
            if (!empty($_POST['login']) &&
                    !empty($_POST['password']) &&
                    !empty($_POST['first_name']) &&
                    !empty($_POST['last_name'])) {
                if ($_POST['password'] == $_POST['password_confirm']) {
                    try {
                        $ret = self::create($_POST['login'], $_POST['password'], $_POST['first_name'], $_POST['last_name']);
                        header("location:" . Page::url("/"));
                    } catch (Exception_Database_Exists $e) {
                        Notification::statusNotify(t("L'identifiant est déjà utilisé."), Notification::STATUS_ERROR);
                    } catch (Exception_Database_Format $e) {
                        Notification::statusNotify(t("L'indentifiant est incorrect."), Notification::STATUS_ERROR);
                    }
                } else {
                    Notification::statusNotify(t("Les mots de passe sont différents."), Notification::STATUS_ERROR);
                }
            } else {
                Notification::statusNotify(t("Des informations d'inscription sont manquantes."), Notification::STATUS_ERROR);
            }
        }

        $theme = new Theme();
        $title = t("Découvrez Jinn");
        $contenu = t("Inscrivez-vous puis connectez-vous pour profiter de toutes les fonctionnalités offertes par Jinn !");
        $theme->add_to_body($contenu, $title);

        $f = new Form("POST");
        $t = (new InputElement("login", t("Identifiant : "), ""));
        $f->addElement($t);
        $t = (new InputElement("first_name", t("Prénom : "), ""));
        $f->addElement($t);
        $t = (new InputElement("last_name", t("Nom : "), ""));
        $f->addElement($t);
        $t = (new InputElement("password", t("Mot de passe :"), "", "password"));
        $f->addElement($t);
        $t = (new InputElement("password_confirm", t("Confirmation : "), "", "password"));
        $f->addElement($t);

        $t = (new InputElement("submit-signin", "", t("S'inscrire"), "submit"));
        $f->addElement($t);
        $formulaire = $theme->forming($f);
        $theme->add_to_body($formulaire, t("Inscription"));
        $theme->process_theme(Theme::STRUCT_BLANK);
    }

    public static function page_main() {
        $theme = new Theme();
        $res = method_invoke_all($homepage, array(), true);
        foreach ($res as $r)
            $theme->add_to_body($r);
        $theme->process_theme(Theme::STRUCT_DEFAULT);
    }

    public static function page_logout() {
        session_destroy();
        header("location:" . Page::url("/"));
    }

    public static function page_profile($id_user = null) {
        $theme = new Theme();
        $isMyProfil = false;
        if ($id_user == null) {
            $id_user = self::get_user_logged_id();
            $isMyProfil = true;
        }

        $u = new UserObject();
        $u->load($id_user);

        $output = "";
        $output .= "<div id=\"profil_top\">";
        $output .= "<div id=\"profil_top_avatar\">";
        $output .= Theme::linking("", "<img src=\"\" alt=\"\"/>");
        $output .= "</div>";
        $output .= "<div id=\"profil_top_avatar_nom\">";
        if ($isMyProfil)
            $output .= "<i class=\"fa fa-user fa-fw\" title=\"Mon profil\"></i>";
        $output .= $u->firstname . " " . $u->lastname;
        $output .= "</div>";
        $output .= "</div>";
        $output .= "<div class=\"page_contenu_sep\"></div>";

        $output .= "<div id=\"profil_buttons\">";
        $result = method_invoke_all("hook_profile_view", array($id_user));
        foreach ($result as $r)
            $output .= $r;
        $output .= "</div>";

        $theme->add_to_body($output);
        $theme->process_theme(Theme::STRUCT_DEFAULT);
    }

    public function permissions() {
        return true;
    }

}
