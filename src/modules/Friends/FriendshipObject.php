<?php

class FriendshipObject extends DataObject {

    public static function schema(&$schema) {
        $schema["friendship"] = array(
            "sid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY,
            "rid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY,
            "accepted" => Database::FIELD_TYPE_INT,
            "date" => Database::FIELD_TYPE_DATE
        );
    }

    public function __construct(){
        parent::__construct();
        $this -> accepted = 0;
    }

    public function __set($param, $value) {
        parent::__set($param, $value);
    }

    public function index() {
        return array("sid", "rid");
    }

    public function tableName() {
        return "friendship";
    }


    public function loadAllFriends($id_user) {
        $d = new FriendshipObject();
        $request = "SELECT * FROM " . CONFIG_DB_PREFIX . $d->tableName() ." WHERE (sid = ".$id_user." OR rid = ".$id_user.") AND accepted = 1 ORDER BY date ASC, sid ASC, rid ASC";
        $results = Database::getAll($request);
        $list_of_friends=array();
        if(is_array($results)){
            foreach($results as $r){
                if($r->sid != $id_user)
                    $list_of_friends[] = $r->sid;
                if($r->rid != $id_user)
                    $list_of_friends[] = $r->rid;
            }
        }
        return $list_of_friends;
    }

    public static function loadAllPendingRequests($id_demandeur = null, $id_receveur = null) {
        $d = new FriendshipObject();
        $request = "SELECT * FROM " . CONFIG_DB_PREFIX . $d->tableName();
        $request .= (isset($id_demandeur) AND $id_demandeur != null) ? " WHERE sid = $id_demandeur" : "";
        $request .= (isset($id_receveur) AND $id_receveur != null) ? ((isset($id_demandeur) AND $id_demandeur != null) ? " AND" : " WHERE" )." rid = $id_demandeur" : "";
        $request .= "AND accepted = 0 ORDER BY date ASC, sid ASC, rid ASC";
        $results = Database::getAll($request);
        return $results == null ? array() : $results;
    }

    public static function isFriend($id_demandeur, $id_receveur) {
        $d = new FriendshipObject();
        $request = "SELECT * FROM " . CONFIG_DB_PREFIX . $d->tableName();
        $request .= " WHERE (sid = $id_demandeur OR sid = $id_receveur)";
        $request .= " AND (rid = $id_demandeur OR rid = $id_receveur)";
        $request .= "AND accepted = 1";
        $results = Database::getAll($request);
        return $results == null ? false : true;
    }
}