<?php
/**
 * @name : Admin Module
 * @desc : Administration Module.
 * @mach : admin
 * @author : d3cima
 * 
 */
class admin implements common_module, m_page, m_access {

    //put your code here
    public function main() {
        
    }

    public function menu() {
        return array(
            "/admin/" => array(
                "callback" => "admin::page_control_panel",
                "access" => constants::get("PAGE_ACCESS_FUNCTION"),
                "permission" => "administrer",
            ),
        );
    }

    public static function page_control_panel() {
        $a = array();
        $a[] = page::link("admin/modules", "Modules");
        $a[] = page::link("admin/themes", "Themes");
        $a[] = page::link("admin/permissions", "Permissions");

        return theme::t_list($a);
    }

    public function perms() {
        return array(
            "administrer" => "admin part",
        );
    }

    public function menu_ui() {

        return array(
            array(
                "name" => "admin",
                "url" => "admin",
                "submenu" => array(
                    array(
                        "name" => "module",
                        "url" => "admin/modules",
                    ),
                    array(
                        "name" => "theme",
                        "url" => "admin/themes",
                    )
                ),
            )
        );
    }

}

