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
        $item['/friends/request/@'] = array(
            "access" => "access content",
            "callback" => array("Friends", "send_request"),
        );
        $item['/friends/remove/@'] = array(
            "access" => "access content",
            "callback" => array("Friends", "remove"),
        );
        $item['/friends/accept/@'] = array(
            "access" => "access content",
            "callback" => array("Friends", "accept"),
        );
        $item['/friends/decline/@'] = array(
            "access" => "access content",
            "callback" => array("Friends", "decline"),
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


    public static function hook_profile_view($id_user){
        $u = User::get_user_logged_id();
        $button = "";
        $atr = array();
        $atr["class"] = "btn";

        $demande_envoyee = FriendshipObject::loadAllPendingRequests($u, $id_user);
        $demande_attente = FriendshipObject::loadAllPendingRequests($id_user, $u);

        // Si on est sur le profil de quelqu'un
        if($u != $id_user) {
            if(FriendshipObject::isFriend($u, $id_user)){
                $button = Theme::linking(Page::url("/friends/remove/$id_user"), t("<i class=\"fa fa-user-times fa-fw\"></i> Retirer"), false, $atr);
            }
            else if(sizeof($demande_envoyee) > 0) {
                $button = Theme::linking(Page::url(""), t("<i class=\"fa fa-external-link-square fa-fw\"></i> Demande envoyée"), false, $atr);
            }
            else if(sizeof($demande_attente) > 0) {
                $button = Theme::linking(Page::url("/friends/decline/$id_user"), t("<i class=\"fa fa-times fa-fw\"></i> Annuler"), false, $atr);
                $button .= " ".Theme::linking(Page::url("/friends/accept/$id_user"), t("<i class=\"fa fa-check fa-fw\"></i> Accepter"), false, $atr);
            }
            else {
                $button = Theme::linking(Page::url("/friends/request/$id_user"), t("<i class=\"fa fa-user-plus fa-fw\"></i> Ajouter"), false, $atr);
            }
            $button .= Theme::linking(Page::url("/messages/$id_user"), t("<i class=\"fa fa-envelope fa-fw\"></i> Messgaerie"), false, $atr);
        }
        return $button;
    }


    public static function send_request($id_user){
        $u = User::get_user_logged_id();
        $a = new UserObject();

        if($u != null){
            if($a->load($id_user)){
                if(!FriendshipObject::isFriend($u, $id_user) && !FriendshipObject::loadAllPendingRequests($u, $id_user)){
                    $friendship = new FriendshipObject();
                    $friendship->__set("sid", $u);
                    $friendship->__set("rid", $id_user);
                    $friendship->__set("accepted", 0);
                    $friendship->__set("date", date("Y-m-d H:i:s"));
                    $friendship->save();
                }
                header("location: " . Page::url("/profile/$id_user"));
                return;
            }
            header("location: " . Page::url("/profile"));
            return;
        }
        header("location: " . Page::url("/"));
        return;
    }


    public static function remove($id_user){
        $u = User::get_user_logged_id();
        $a = new UserObject();

        if($u != null){
            if($a->load($id_user)){
                if(FriendshipObject::isFriend($u, $id_user)){
                    $friendship = new FriendshipObject();
                    if(!$friendship->load($u, $id_user))
                        $friendship->load($id_user, $u);
                    $friendship->delete();
                }
                header("location: " . Page::url("/profile/$id_user"));
                return;
            }
            header("location: " . Page::url("/profile"));
            return;
        }
        header("location: " . Page::url("/"));
        return;
    }


    public static function accept($id_user){
        $u = User::get_user_logged_id();
        $a = new UserObject();

        if($u != null){
            if($a->load($id_user)){
                if(!FriendshipObject::isFriend($u, $id_user) && sizeof($req = FriendshipObject::loadAllPendingRequests($id_user, $u)) > 0){
                    $friendship = new FriendshipObject();
                    $friendship->load($id_user, $u);
                    $friendship->__set("accepted", 1);
                    $friendship->save();
                }
                header("location: " . Page::url("/profile/$id_user"));
                return;
            }
            header("location: " . Page::url("/profile"));
            return;
        }
        header("location: " . Page::url("/"));
        return;
    }

    public static function decline($id_user){
        $u = User::get_user_logged_id();
        $a = new UserObject();

        if($u != null){
            if($a->load($id_user)){
                if(!FriendshipObject::isFriend($u, $id_user) && sizeof($req = FriendshipObject::loadAllPendingRequests($id_user, $u)) > 0){
                    $friendship = new FriendshipObject();
                    $friendship->load($id_user, $u);
                    $friendship->delete();
                }
                header("location: " . Page::url("/profile/$id_user"));
                return;
            }
            header("location: " . Page::url("/profile"));
            return;
        }
        header("location: " . Page::url("/"));
        return;
    }
}
