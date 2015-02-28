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
        $this->create("guest", 0);
        $this->create("administrators");
        $this->create("users");
        $o = new GroupObject();
        if ($o->load_by_label("administrators")) {
            $o->add_member(1);
        }

        $o = new GroupObject();
        if ($o->load_by_label("users")) {
            $o->add_member(1);
        }
        return true;
    }

    public function schema($schema = array()) {
        GroupObject::schema($schema);
        return $schema;
    }

    /* à la création d'un utilisateur, l'ajoute au groupe user */

    public function hook_user_create($uid) {
        $g = new GroupObject();
        $g->load_by_label("users");
        $g->add_member($uid);
    }

    public static function create($name, $id = null) {
        $g = new GroupObject();
        try {
            if ($id != null) {
                $g->gid = $id;
            }
            $g->label = $name;
            $g->save();
        } catch (Exception_Database_Exists $e) {
            return false;
        }
        $g->load_by_label($name);
        method_invoke_all("hook_group_create", array($g->gid));
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
        $item['/admin/groups/confirm/@'] = array(
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
