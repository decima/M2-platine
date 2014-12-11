<?php

class user_ui {

    public static function login_page() {
        page::title("Login");

        return theme::t_form(self::login_form_ui(array()));
    }

    public static function login_block(){
                return theme::t_form(self::login_form_ui(array()));
    }
    protected static function login_form_ui($form = array()) {
        $form["action"] = page::url("login/process");
        if (!isset($form["fields"]))
            $form["fields"] = array();
        $form["fields"]["login"] = array(
            "type" => "input",
            "label" => "Login",
        );
        $form["fields"]["password"] = array(
            "type" => "password",
            "label" => "Password",
        );

        $form['fields']["save"] = array(
            "type" => "submit",
            "value" => "connect",
        );
        return $form;
    }

    public static function login_process_page() {
        $login = "";
        $pass = "";
        if (!empty($_POST['login'])) {
            $login = $_POST['login'];
        }
        if (!empty($_POST['password'])) {
            $pass = $_POST['password'];
        }
        $u = user::user_load_by_nickname($login);
        if (is_object($u)) {
            if ($u->password == $pass) {
                user::user_connect($u->uid);
                page::redirect("");
            }
        }
        else {
            page::redirect("login");
        }
    }

    public static function logout_page() {
        system_session_logout();
        page::redirect("");
    }

    public static function permissions_page() {
        page::title("Permissions");

        $out = page::link("admin/permissions/scan", "scan for more permissions");
        $out .= "<form method='post' action='" . page::url("admin/permissions/update") . "'>";
        $array = array();
        $header = array("permissions");
        $groups = user_access::get_all_roles();
        foreach ($groups as $g) {
            $header[] = $g->groupname;
        }
        $perms = permission::get_all_permissions();
        foreach ($perms as $p) {
            $t_array = array();
            $t_array[] = "<b>$p->permission</b> <i>$p->description</i>";
            foreach ($groups as $g) {
                $o = "<input type='checkbox' name='permissions[" .
                        $p->permission . "][" . $g->gid . "]' ";
                if (user::has_permission($p->permission, $g->gid)) {
                    $o .="checked";
                }
                $o.="/>";
                $t_array[] = $o;
            }
            $array[] = $t_array;
        }
        $out .= theme::t_table($array, $header);

        $out .="<input type='submit' value='update'/>";
        $out .="</form>";
        return $out;
    }

    public static function permission_save_page() {
        if (isset($_POST['permissions'])) {
            user::delete_all_permissions();
            foreach ($_POST['permissions'] as $permission => $boxes) {
                foreach ($boxes as $group => $status) {
                    user::add_permission($permission, $group);
                }
            }
        }
        page::redirect("/admin/permissions");
    }

    public static function profile_page($user) {

        if (!is_numeric($user)) {
            $user = user::user_load_by_nickname($user);
        }
        else {
            $user = user::user_load($user);
        }
        page::title("profile");
        $out = t("nickname : %user", array("%user" => $user->username)
        );
        return $out;
    }

    public static function profile_edit_page($user) {
        return "PROFILE EDIT PAGE";
    }

    public static function profile_action_page($user, $action) {
        return "PROFILE ACTION";
    }

}
