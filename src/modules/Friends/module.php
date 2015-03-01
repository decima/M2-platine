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
            "access" => "access content",
            "callback" => array("Friends", "list_of_friends"),
        );
        $item['/friends/@'] = array(
            "access" => "access content",
            "callback" => array("Friends", "list_of_friends"),
        );
        
        return $item;
    }


    public static function list_of_friends($id_user=null) {
        $friends = new FriendshipObject();
        $output = "";
        if($id_user == null && ($u = User::get_user_logged_id()) != null){
            $id_user = $u;
        }
        $theme = new Theme();
        $theme->set_title(t("Liste des amis"));
        if ($tab = $friends->loadAllFriends($id_user)) {
            foreach($tab as $k => $f){
                $u = new UserObject();
                $u -> load($f);
                $output .= "<div class=\"friend_line\">";
                    $output .= "<div class=\"friend_line_avatar_area\">";
                        $output .= "<div class=\"friend_line_avatar\">";
                            $output .= $theme->linking(Page::url("/profile/".$f), "<img src=\"\" alt=\"\"/>");
                        $output .= "</div>";
                    $output .= "</div>";
                $output .= "<div class=\"friend_line_name_area\">";
                    $output .= "<div class=\"friend_line_name\">";
                        $output .= $theme->linking(Page::url("/profile/".$f), $u->firstname." ".$u->lastname);
                        $output .= "<div class=\"friend_line_name_icon\">";
                            $output .= "<i class=\"fa fa-user fa-fw\"></i>";
                        $output .= "</div>";
                    $output .= "</div>";
                $output .= "</div>";
            }
            $theme->add_to_body($output);
        } else {
            Notification::statusNotify(t("Parce qu'on peut dire que le salami est vôtre seul ami..."), Notification::STATUS_INFO);
        }
        $theme->process_theme(Theme::STRUCT_ADMIN);

        return;
    }
}
