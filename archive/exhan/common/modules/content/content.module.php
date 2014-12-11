<?php

/**
 * @name : Content Module
 * @desc : Content Module.
 * @mach : content
 * @author : d3cima
 * 
 */
require 'content_database.php';
require 'content_page.php';

class content implements common_module, m_install, m_database, m_page {

    public function install() {
        content_database::create_new_content_type("post", "post");
    }

    public function uninstall() {
        $types = content_database::get_list_of_node_types();
        foreach ($types as $type) {            
            content_database::delete_content_type($type);
        }
    }

    public function schema() {
        return content_database::schema();
    }

    public function perms() {
        $b = array();
        foreach (content_database::get_all_node_types() as $element) {
            $b[$element->type . " read"] = "Read $element->name contents";
            $b[$element->type . " create"] = "create new $element->name contents";
            $b[$element->type . " edit"] = "edit/delete own $element->name contents";
            $b[$element->type . " admin"] = "administrate $element->name contents";
        }
        return $b;
    }

    public function menu() {
        $a = array();
        $a["node/add"] = array(
            "access" => "user_access::user_is_connected",
            "permission" => "access content"
        );
        $a["node/%"] = array(
            "callback" => "content_page::view_node",
            "access" => "access_granted",
            "permission" => "access content"
        );
        $a["node/add/%"] = array(
            "callback" => "content_page::form_content",
            "access" => "user_access::user_is_connected",
            "permission" => "access content"
        );
        $a["node/adding/%"] = array(
            "callback" => "content_page::save_content",
            "access" => "user_access::user_is_connected",
            "permission" => "access content"
        );
        return $a;
    }

    public static function node_access_read($nid){
        $node = content_database::node_load($nid);
         return (user_access::user_has_group_access($node->type . " read"));
    }
    
    public function menu_ui() {
        $b = content_database::get_all_node_types();
        $k = array();
        foreach ($b as $content) {
            $k[] = array("name" => $content->name, "url" => "node/add/" . $content->type);
        }
        return array(
            array(
                "name" => "add new content",
                "url" => "/node/add",
                "submenu" => $k
            ),
        );
    }

}
