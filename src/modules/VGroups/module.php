<?php

/**
 * @moduleName VGroups
 * 
 * 
 * */
require_once("VGroupObject.php");

class VGroups implements Module {

    public function info() {
        return array(
            "name" => "VGroups",
            "readablename" => "Virtual Groups",
            "dependencies" => array("User", "Group"),
        );
    }

    public function schema($items = array()) {
        VGroupObject::schema($items);
        return $items;
    }

    public function menu($item = array()) {

        $item['/group/new'] = array(
            "access" => "create virtual group",
            "callback" => array("VGroups", "create"),
        );
        $item['/group/@'] = array(
            "access" => "see virtual group",
            "callback" => array("VGroups", "see"),
        );
        $item['/group/@/@'] = array(
            "access" => "join virtual group",
            "callback" => array("VGroups", "action"),
        );

        return $item;
    }

    public function permissions() {
        return true;
    }

}
