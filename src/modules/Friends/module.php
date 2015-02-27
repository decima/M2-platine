<?php

/**
 * @moduleName Friends
 * 
 * 
 * */
require_once("FriendshipObject.php");

class Friends implements Module {

    public function info() {
        return array(
            "name" => "Friends",
            "readablename" => "Friends"
        );
    }

    public function schema($schema = array()) {
        FriendshipObject::schema($schema);
        return $schema;
    }

    public function menu($item = array()) {
        $item['/friends'] = array(
            "access" => "access-content",
            "callback" => array("Friends", "list_of_friends"),
        );
        $item['/friends/@'] = array(
            "access" => "access-content",
            "callback" => array("Friends", "list_of_friends"),
        );
        
        return $item;
    }


    public static function list_of_friends($id_user=null) {
        $friends = new FriendshipObject();
        if($id_user == null && ($u = User::get_user_logged_id()) != null){
            $id_user = $u;
        }
        $theme = new Theme();
        $theme->set_title(t("Liste des amis"));
        if ($friends->loadAllFriends($id_user)) {


        } else {
            Notification::statusNotify(t("Parce qu'on peut dire que le salami est vôtre seul ami..."), Notification::STATUS_INFO);
        }
        $theme->process_theme(Theme::STRUCT_ADMIN);

        return;
    }
}
