<?php
class user_access {

    public static function user_has_group_access($permission) {
        global $user;
        $uid = 0;
        if(isset($user->uid)) {
            $uid = $user->uid;
        }
        if($uid == 1) {
            return true;
        }else {
            foreach($user->groups as $gid) {

                if(in_array($gid, user::load_all_groups_permissions($permission))) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function user_is_connected() {
        global $user;
        return isset($user->uid);
    }

    public static function user_is_not_connected() {
        return !self::user_is_connected();
    }

    public static function get_all_roles() {
        return database::fetchAll(database::select("groups"));
    }

    public static function block_login_access() {
        return self::user_is_not_connected() && page::clean_path() != "login";
    }

}
