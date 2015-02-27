<?php

/**
 * @moduleName Group
 * 
 * 
 * */
require_once("GroupObject.php");
require_once("GroupPage.php");

class Group implements Module {

    public function info() {
        return array(
            "name" => "Group",
            "readablename" => "Groups"
        );
    }

    public function install() {
        $this->create("administrators");
        $this->create("users");
        $o = new GroupObject();
        if ($o->load_by_label("administrators")) {
            $o->add_member(1);
        }
        return true;
    }

    public function schema($schema = array()) {
        GroupObject::schema($schema);
        return $schema;
    }

    public static function create($name) {
        $g = new GroupObject();
        try {
            $g->label = $name;
            $g->save();
        } catch (Exception_Database_Exists $e) {
            return false;
        }
        return true;
    }

    public function menu($item = array()) {

        $item['/admin/groups'] = array(
            "access" => "administrer",
            "callback" => array("GroupModulePages", "list_of_groups"),
        );
        $item['/admin/groups/@'] = array(
            "access" => "administrer",
            "callback" => array("GroupModulePages", "list_of_members"),
        );
        $item['/admin/groups/create/'] = array(
            "access" => "administrer",
            "callback" => array("GroupModulePages", "creategroup"),
        );
        $item['/admin/groups/confirm/@']=array(
             "access" => "administrer",
            "callback" => array("GroupModulePages", "confirmDelete"),
        );
        $item['/admin/groups/@/@'] = array(
            "access" => "administrer",
            "callback" => array("GroupModulePages", "action"),
        );
        $item['/admin/groups/@/@/@'] = array(
            "access" => "administrer",
            "callback" => array("GroupModulePages", "action"),
        );
        
        return $item;
    }

    public function permissions() {
        return true;
    }

 
}
