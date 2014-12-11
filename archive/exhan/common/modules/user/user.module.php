<?php
/**
 * @name : User Module
 * @desc : User Module.
 * @mach : user
 * @author : d3cima
 * 
 */
class user implements common_module, m_install, m_database, m_page, extend_permissions, m_access {

    public function schema() {
        $schema = array();
        $schema['users'] = array(
            'fields' => array(
                "uid" => "int not null  AUTO_INCREMENT",
                "username" => "varchar(100) not null UNIQUE",
                "password" => "varchar(255) not null",
                "email" => "varchar(255) not null",
            ),
            'pk' => array("uid"),
        );
        $schema['groups'] = array(
            'fields' => array(
                "gid" => "int not null AUTO_INCREMENT",
                "groupname" => "varchar(255) not null UNIQUE",
                "description" => "TEXT",
            ),
            'pk' => array("gid"),
        );
        $schema['user_group_member'] = array(
            'fields' => array(
                "uid" => "int not null",
                "gid" => "int not null"
            ),
            'pk' => array("uid","gid"),
        );

        $schema['permission_role'] = array(
            "fields" => array(
                "permission" => "varchar(255) not null",
                "gid" => "int"
            ),
            "pk" => array("permission","gid")
        );
        return $schema;
    }

    public static function add_user($username, $password, $email) {
        database::insert("users",
                array(
            "username" => $username,
            "password" => $password,
            "email" => $email
        ));
        return database::$_last_id;
    }

    public static function add_group($groupname, $description) {
        database::insert("groups",
                array(
            "groupname" => $groupname,
            "description" => $description
        ));
        return database::$_last_id;
    }

    public static function add_user_to_group($gid, $uid) {
        database::insert("user_group_member",
                array(
            "uid" => $uid,
            "gid" => $gid
        ));
    }

    public function install() {
        $root_uid = self::add_user("root", "toor", "h.larget@gmail.com");
        $test_uid = self::add_user("test", "pass", "test@example.com");

        $gid = self::add_group("visitor", "visitor");
        self::add_user_to_group($gid, $root_uid);
        self::add_user_to_group($gid, $test_uid);
        $gid = self::add_group("member", "member");
        self::add_user_to_group($gid, $root_uid);
        self::add_user_to_group($gid, $test_uid);
        $gid = self::add_group("admin", "Administrators");
        self::add_user_to_group($gid, $root_uid);
    }

    public function menu() {
        $a = array();
        $a["/login"] = array(
            "callback" => "user_ui::login_page",
            "access" => "user_access::user_is_not_connected",
            "permission" => "login"
        );
        $a["/login/process"] = array(
            "callback" => "user_ui::login_process_page",
            "access" => "user_access::user_is_not_connected",
            "permission" => "login"
        );
        $a["/logout"] = array(
            "callback" => "user_ui::logout_page",
            "access" => "user_access::user_is_connected",
            "permission" => "login"
        );

        $a['admin/permissions'] = array(
            "callback" => "user_ui::permissions_page",
            "access" => constants::get("PAGE_ACCESS_FUNCTION"),
            "permission" => "administrer",
        );
        $a['admin/permissions/update'] = array(
            "callback" => "user_ui::permission_save_page",
            "access" => constants::get("PAGE_ACCESS_FUNCTION"),
            "permission" => "administrer",
        );
        $a['user/%'] = array(
            "callback" => "user_ui::profile_page",
            "access" => constants::get("PAGE_ACCESS_FUNCTION"),
            "permission" => "administrer",
        );
        $a['user/%/%'] = array(
            "callback" => "user_ui::profile_action_page",
            "access" => constants::get("PAGE_ACCESS_FUNCTION"),
            "permission" => "administrer",
        );
        $a['user/%/edit'] = array(
            "callback" => "user_ui::profile_edit_page",
            "access" => constants::get("PAGE_ACCESS_FUNCTION"),
            "permission" => "administrer",
        );
        return $a;
    }

    public function check_permission($page, $page_data) {
        global $user;
        if(isset($page_data['permission'])) {
            return user_access::user_has_group_access($page_data['permission']);
        }
        return false;
    }

    public static function user_load($uid) {
        return database::fetch(database::select("users", array(), "uid=%user",
                                array("%user" => $uid)));
    }

    public static function groups_user_load($uid) {
        return database::fetchAll(
                        database::select("user_group_member", array("gid"),
                                "uid=%user", array("%user" => $uid)),
                        pdo::FETCH_COLUMN);
    }

    public static function user_connect($uid) {
        global $user;
        $user = self::user_load($uid);
        $user->groups = self::groups_user_load($uid);
    }

    public static function user_load_by_nickname($nickname) {
        return database::fetch(database::select("users", array(),
                                "username='%nickname'",
                                array("%nickname" => $nickname)));
    }

    public static function user_disconnect() {
        session_destroy();
        global $user;
        $user = new stdClass();
        $user->uid = 0;
        $user->groups = array(1);
    }

    public static function load_all_groups_permissions($permission) {
        return database::fetchAll(database::select("permission_role",
                                array("gid"), "permission='%permission'",
                                array("%permission" => $permission)),
                        pdo::FETCH_COLUMN);
    }

    public function menu_ui() {

        return array(
            array(
                "name" => "login",
                "url" => "/login"
            ),
            array(
                "name" => "logout",
                "url" => "/logout"
            ),
            array("name" => "admin","submenu" =>
                array(
                    array("name" => "permissions","url" => "/admin/permissions")
                )
            ),
        );
    }

    public static function has_permission($perm, $group) {
        return is_object(
                        database::fetch(
                                database::select(
                                        "permission_role", array(),
                                        "permission='%perm' AND gid='%group'",
                                        array("%perm" => $perm,"%group" => $group)
                                )
                        )
        );
    }

    public static function delete_all_permissions() {
        database::delete("permission_role");
    }

    public static function add_permission($perm, $group) {
        database::insert("permission_role",
                array(
            "permission" => $perm,
            "gid" => $group
        ));
    }

    public function perms() {
        return array(
            "login" => "connect to website",
        );
    }

    public function block_info() {
        $array = array();
        $array['user_connector'] = array(
            "callback" => "user::block_login",
            "access" => "user_access::block_login_access",
            "permission" => "access content",
            "position"=>"left-2",
        );
        return $array;
    }

    public static function block_login() {
        return "<h3>Login</h3>".user_ui::login_block();
    }

}

require_once "user_ui.php";
require_once "user_access.php";