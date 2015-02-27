<?php

/**
 * @moduleName Widget
 *
 *
 * */
require_once("WidgetObject.php");

class Widget implements SystemModule {

    public function info() {
        return array(
            "name" => "Widget",
            "readablename" => "Widget"
        );
    }

    public function schema($schema = array()) {
        WidgetObject::schema($schema);
        return $schema;
    }

    public function menu($item = array()) {
        $item['/admin/view/widget'] = array(
            "callback" => array("Widget", "page_config")
        );
        $item['/admin/view/widget/install/@'] = array(
            "access" => "administration",
            "callback" => array("Widget", "installWidgetPage")
        );
        $item['/admin/view/widget/enable/@'] = array(
            "access" => "administration",
            "callback" => array("Widget", "enableWidgetPage")
        );
        $item['/admin/view/widget/disable/@'] = array(
            "access" => "administration",
            "callback" => array("Widget", "disableWidgetPage")
        );
        $item['/admin/view/widget/uninstall/@'] = array(
            "access" => "administration",
            "callback" => array("Widget", "uninstallWidgetPage")
        );
        $item['/admin/view/widget/up/@'] = array(
            "access" => "administration",
            "callback" => array("Widget", "upWidgetPage")
        );
        $item['/admin/view/widget/down/@'] = array(
            "access" => "administration",
            "callback" => array("Widget", "downWidgetPage")
        );
        return $item;
    }

    public function scanForWidget() {
        $a = method_invoke_all("widget", array(), true);
        foreach ($a as $k => $v) {
            $wo = new WidgetObject();
            $wo->load($k);
            $wo->widget_name = $k;
            $wo->module_name = "";
            $wo->callback = implode("::", $v["callback"]);
            $wo->permissions = $v["permissions"];
            $wo->save();
        }
    }

    public function runWidgets($position = WidgetObject::WIDGET_LATERAL_LEFT, callable $callable = null) {
        $w = WidgetObject::loadByPosition($position);
        if ($callable != null) {
            foreach ($w as $k => $v) {
                $callable(call_user_func($v->callback));
            }
        }
    }

    public static function page_config() {
        if (isset($_GET['err'])) {
            switch ($_GET['err']) {
                case 'install':
                    Notification::statusNotify(t("Echec de l'installation du widget"), Notification::STATUS_ERROR);
                    break;
                case 'enable':
                    Notification::statusNotify(t("Echec de l'activation du widget"), Notification::STATUS_ERROR);
                    break;
                case 'disable':
                    Notification::statusNotify(t("Echec de la desactivation du widget"), Notification::STATUS_ERROR);
                    break;
                case 'uninstall':
                    Notification::statusNotify(t("Echec de la désinstation du widget"), Notification::STATUS_ERROR);
                    break;
                case 'up':
                case 'down':
                    Notification::statusNotify(t("Echec du changement de priorité du widget"), Notification::STATUS_ERROR);
                    break;
                default:
                    Notification::statusNotify(t("Une erreur inconnue est survenue"), Notification::STATUS_ERROR);
                    break;
            }
        }
        $widget = new Widget();
        $widget->scanForWidget();
        $widgets = WidgetObject::loadAll();
        $activates = WidgetObject::loadAllActivate();
        $theme = new Theme();

        $theme->set_title(t("Liste des widgets disponibles"));
        $c = count($widgets);
        $a = count($activates);

        $c > 1 ? Notification::statusNotify(t("%cnt widgets disponibles", array("%cnt"=>$c)), Notification::STATUS_INFO) : ($c == 1 ? Notification::statusNotify(t("%cnt widget disponible", array("%cnt"=>$c)), Notification::STATUS_INFO) : Notification::statusNotify(t("Aucun widget disponible"), Notification::STATUS_INFO));
        $r = array(t("Nom du widget"), t("Etat du widget"), t("Actions"));
        $array = array();
        foreach ($widgets as $k => $w) {
            $install = $theme->linking(Page::url("/admin/view/widget/install/" . $w -> widget_name), t("installer"));
            $uninstall = $theme->linking(Page::url("/admin/view/widget/uninstall/" . $w -> widget_name), t("désinstaller"));
            $disable = $theme->linking(Page::url("/admin/view/widget/disable/" . $w -> widget_name), t("désactiver"));
            $enable = $theme->linking(Page::url("/admin/view/widget/enable/" . $w -> widget_name), t("activer"));

            $up = $theme->linking(Page::url("/admin/view/widget/up/" . $w -> widget_name), "<i class='fa fa-arrow-up fa-fw'></i>", false, array("title"=>"Monter"));
            $up_disabled = "<span class='link_disabled' title='Monter'><i class='fa fa-arrow-up fa-fw'></i></span>";
            $down = $theme->linking(Page::url("/admin/view/widget/down/" . $w -> widget_name), "<i class='fa fa-arrow-down fa-fw'></i>", false, array("title"=>"Descendre"));
            $down_disabled = "<span class='link_disabled' title='Descendre'><i class='fa fa-arrow-down fa-fw'></i></span>";


            // Si le widget est activé
            if($w -> activate){
                $array[] = array($w -> widget_name, t("Activé"), $disable, $k == 0 ? $up_disabled : $up, $k == ($a-1) ? $down_disabled : $down);
            }
            else {
                $array[] = array($w -> widget_name, t("Désactivé"), $enable, $up_disabled, $down_disabled);
            }
        }

        $theme->add_to_body($theme->tabling($array, $r));
        $theme->process_theme(Theme::STRUCT_ADMIN);
        return;
    }

    public static function upWidgetPage($widgetName) {
        $widget = new WidgetObject();
        if ($widget -> load($widgetName)) {
            if ($widget->changePriority($widget, $widget::WIDGET_CHANGE_PRIORITY_UP)) {
                header("location: " . Page::url("/admin/view/widget/"));
            } else {
                header("location: " . Page::url("/admin/view/widget/?err=up"));
            }
        } else {
            header("location: " . Page::url("/admin/view/widget/?err=unknown"));
        }
        return;
    }

    public static function downWidgetPage($widgetName) {
        $widget = new WidgetObject();
        if ($widget -> load($widgetName)) {
            if ($widget->changePriority($widget, $widget::WIDGET_CHANGE_PRIORITY_DOWN)) {
                header("location: " . Page::url("/admin/view/widget/"));
            } else {
                header("location: " . Page::url("/admin/view/widget/?err=down"));
            }
        } else {
            header("location: " . Page::url("/admin/view/widget/?err=unknown"));
        }
        return;
    }

    public static function enableWidgetPage($widgetName){
        $widget = new WidgetObject();
        if ($widget -> load($widgetName)) {
            if ($widget->changeActivate($widget)) {
                header("location: " . Page::url("/admin/view/widget/"));
            } else {
                header("location: " . Page::url("/admin/view/widget/?err=enable"));
            }
        } else {
            header("location: " . Page::url("/admin/view/widget/?err=unknown"));
        }
        return;
    }

    public static function disableWidgetPage($widgetName){
        $widget = new WidgetObject();
        if ($widget -> load($widgetName)) {
            if ($widget->changeActivate($widget)) {
                header("location: " . Page::url("/admin/view/widget/"));
            } else {
                header("location: " . Page::url("/admin/view/widget/?err=disable"));
            }
        } else {
            header("location: " . Page::url("/admin/view/widget/?err=unknown"));
        }
        return;
    }

    public function priority()
    {
        return 100;
    }

    public function system_init()
    {
        // TODO: Implement system_init() method.
    }
}
