<?php

/**
 * @moduleName MenuWidget
 *
 *
 * */
class MenuWidget implements Module {

    public function info() {
        return array(
            "name" => "MenuWidget",
            "readablename" => "Menu"
        );
    }

    public function widget($item = array()) {
        $item["admin_menu_user"] = array("permissions" => "administrer", "callback" => array("MenuWidget", "widget_menu_utilisateurs_groupes"));

        $item["admin_menu_view"] = array("permissions" => "administrer", "callback" => array("MenuWidget", "widget_menu_view"));
        $item["admin_menu_system"] = array("permissions" => "administrer", "callback" => array("MenuWidget", "widget_menu_system"));

        return $item;
    }

    public function widget_menu_view() {
        $theme = new Theme();
        $list = array();
        $list[] = Theme::linking(Page::url("/admin/view/widget"), t("widgets"));

        return t("Vue") . $theme->listing($list);
    }

    public function widget_menu_utilisateurs_groupes() {
        $theme = new Theme();
        $list = array();

        $list[] = Theme::linking(Page::url("/admin/users/"), t("Gestionnaire des utilisateurs"));
        $list[] = Theme::linking(Page::url("/admin/groups/"), t("Gestionnaire des groupes"));
        $list[] = Theme::linking(Page::url("/admin/permissions/"), t("Permissions"));

        return t("utilisateurs et permissions") . $theme->listing($list);
    }
    
     public function widget_menu_system() {
        $theme = new Theme();
        $list = array();

        $list[] = Theme::linking(Page::url("/admin/modules/"), t("Modules"));
        $list[] = Theme::linking(Page::url("/admin/database/"), t("Base de données"));

        return t("Système") . $theme->listing($list);
    }

}
