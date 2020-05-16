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

    public static function widget_menu_view() {
        $theme = new Theme();
        $list = array();
        $list[] = Theme::linking(Page::url("/admin/view/widget"), t("<i class=\"fa fa-cogs fa-fw\"></i> Widgets"));

        return t("<div class=\"widget_titre\"><i class=\"fa fa-laptop fa-fw\"></i> Vue</div>") . $theme->listing($list);
    }

    public static function widget_menu_utilisateurs_groupes() {
        $theme = new Theme();
        $list = array();

        $list[] = Theme::linking(Page::url("/admin/users/"), t("<i class=\"fa fa-user fa-fw\"></i> Gérer les utilisateurs"));
        $list[] = Theme::linking(Page::url("/admin/groups/"), t("<i class=\"fa fa-users fa-fw\"></i> Gérer les groupes"));
        $list[] = Theme::linking(Page::url("/admin/permissions/"), t("<i class=\"fa fa-shield fa-fw\"></i> Gérer les permissions"));

        return t("<div class=\"widget_titre\"><i class=\"fa fa-users fa-fw\"></i> Utilisateurs</div>") . $theme->listing($list);
    }

    public static function widget_menu_system() {
        $theme = new Theme();
        $list = array();

        $list[] = Theme::linking(Page::url("/admin/modules/"), t("<i class=\"fa fa-puzzle-piece fa-fw\"></i> Modules"));
        $list[] = Theme::linking(Page::url("/admin/database/"), t("<i class=\"fa fa-database fa-fw\"></i> Base de données"));

        return t("<div class=\"widget_titre\"><i class=\"fa fa-wrench fa-fw\"></i> Système</div>") . $theme->listing($list);
    }

}
