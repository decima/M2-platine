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

    public function install()  {
        try {
            return self::create(CONFIG_ADMIN_LOGIN, CONFIG_ADMIN_PASSWORD, "Admin", "Administrator");
        }
        catch (Exception $e){
            return false;
        }
    }
    public function schema($schema = array()) {
        UserObject::schema($schema);
        return $schema;
    }

    public static function create($email, $password, $firstname = "", $lastname = "") {
        $user = new UserObject();
        $user->email = $email;
        $user->password = $password;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        return $user->save();
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
        $item['/signin'] = array(
            "access" => "access-content",
            "callback" => array("User", "page_signin")
        );
        $item['/logout'] = array(
            "access" => "access-content",
            "callback" => array("User", "page-logout")
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
        $theme->set_title(t("Connexion"));

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

    public static function page_signin(){
        if (isset($_POST['submit-signin'])) {
            if(!empty($_POST['login']) &&
                !empty($_POST['password']) &&
                !empty($_POST['first_name']) &&
                !empty($_POST['last_name'])){
                if($_POST['password'] == $_POST['password_confirm']){
                    try{
                        $ret = self::create($_POST['login'], $_POST['password'], $_POST['first_name'], $_POST['last_name']);
                        self::page_logout();
                    }
                    catch (Exception_Database_Exists $e){
                        Notification::statusNotify(t("L'identifiant est déjà utilisé."), Notification::STATUS_ERROR);
                    }
                    catch (Exception_Database_Format $e){
                        Notification::statusNotify(t("L'indentifiant est incorrect."), Notification::STATUS_ERROR);
                    }
                }
                else {
                    Notification::statusNotify(t("Les mots de passe sont différents."), Notification::STATUS_ERROR);
                }
            }
            else {
                Notification::statusNotify(t("Des informations d'inscription sont manquantes."), Notification::STATUS_ERROR);
            }

        }

        $theme = new Theme();
        $theme->set_title(t("Inscription"));

        $f = new Form("POST");
        $t = (new InputElement("login", t("identifiant"), ""));
        $f->addElement($t);
        $t = (new InputElement("first_name", t("prénom"), ""));
        $f->addElement($t);
        $t = (new InputElement("last_name", t("nom"), ""));
        $f->addElement($t);
        $t = (new InputElement("password", t("mot de passe"), "", "password"));
        $f->addElement($t);
        $t = (new InputElement("password_confirm", t("confirmation du mot de passe"), "", "password"));
        $f->addElement($t);

        $t = (new InputElement("submit-signin", "", t("Envoyer"), "submit"));
        $f->addElement($t);
        $theme->process_form($f);
        $theme->add_to_body(print_r($_POST, true));
        $theme->process_theme(Theme::STRUCT_BLANK);
    }

    public static function page_main() {
        $theme = new Theme();
        $theme->add_to_body("hello world");
        $theme->process_theme(Theme::STRUCT_DEFAULT);
    }

    public static function page_logout(){
        session_destroy();
        header("location:".Page::url("/"));
    }

    public static function page_profile() {
        $theme = new Theme();
        $theme->process_theme(Theme::STRUCT_DEFAULT);
    }

}
