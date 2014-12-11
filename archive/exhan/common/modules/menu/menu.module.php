<?php
/**
 * @name : Menu Module
 * @desc : Menu Module.
 * @mach : menu
 * @author : d3cima
 * 
 */
class menu implements common_module, m_install, m_database {

    public function schema() {
        $schema = array();
        $schema['menu'] = array(
            "fields" => array(
                "url" => "VARCHAR(255) NOT NULL",
                "name" => "VARCHAR(255) NOT NULL",
            ),
            "pk" => array("url")
        );
        return $schema;
    }

    public function block_info() {
        $array = array();
        $array['external_menu'] = array(
            "position" => "left-1",
            "callback" => "menu::list_of_menus",
            "access" => "access_granted",
            "permission" => "access content",
        );
        $array['footer'] = array(
            "position" => "footer",
            "callback" => "menu::footer",
            "access" => "access_granted",
            "permission" => "access content",
        );
        return $array;
    }

    public static function get_all_menus() {
        return database::fetchAll(database::select("menu"));
    }

    public static function list_of_menus() {
        $array = array();
        foreach(self::get_all_menus() as $list) {
            $array[] = page::link($list->url, $list->name);
        }
        return "<h3>Links</h3>".theme::t_list($array);
    }

    public static function footer() {
     return "<b>copyrigth d3cima</b>";   
    }

}

