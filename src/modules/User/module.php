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
        $ret = self::create(CONFIG_ADMIN_LOGIN, CONFIG_ADMIN_PASSWORD, "Admin", "Administrator");
        return $ret;
    }

    public function schema($schema = array()) {
        UserObject::schema($schema);
        return $schema;
    }

    public function widget($item = array()) {
        $item["user_logged"] = array("permissions" => "logged", "callback" => array("User", "widget_user_logged"));
        return $item;
    }

    public function widget_user_logged() {
        return Theme::listing(array(
                    Theme::linking(Page::url("/profile"), "Profil"),
                    Theme::linking(Page::url("/logout"), "deconnexion"),
        ));
    }

    public static function create($email, $password, $firstname = "", $lastname = "") {
        $user = new UserObject();
        try {
            $user->email = $email;
            $user->password = $password;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->save();
        } catch (Exception_Database_Exists $e) {

            return false;
        }
        return true;
    }

    public function menu($item = array()) {
        $item['/profile'] = array(
            "access" => "access-content",
            "callback" => array("User", "page_profile")
        );
        $item['/'] = array(
            "access" => "access-content",
            "callback" => array("User", "page_home")
        );
        $item['/logout'] = array(
            "access" => "access-content",
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

    public static function page_login() {

        $theme = new Theme();
        $theme->set_title(t("connexion"));

        $f = new Form("POST", Page::url("/"));
        $t = (new InputElement("login", t("identifiant"), ""));
        $f->addElement($t);
        $t = (new InputElement("password", t("mot de passe"), "", "password"));
        $f->addElement($t);

        $t = (new InputElement("submit-login", "", t("Envoyer"), "submit"));
        $f->addElement($t);
        $theme->process_form($f);
        $theme->process_theme(Theme::STRUCT_ADMIN);
    }

    public static function page_main() {
        $theme = new Theme();
        $theme->add_to_body("hello world");
        $theme->process_theme(Theme::STRUCT_DEFAULT);
    }

    public static function page_logout() {
        session_destroy();
        header("location:" . Page::url("/"));
    }

    public static function page_profile() {
        $theme = new Theme();
        $theme->process_theme(Theme::STRUCT_DEFAULT);
    }

    public function permissions() {
        return true;
    }

}
