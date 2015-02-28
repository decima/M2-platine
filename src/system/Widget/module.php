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
            "access" => "administrer",
            "callback" => array("Widget", "page_config")
        );
        $item['/admin/view/widget/install/@'] = array(
            "access" => "administrer",
            "callback" => array("Widget", "installWidgetPage")
        );
        $item['/admin/view/widget/enable/@'] = array(
            "access" => "administrer",
            "callback" => array("Widget", "enableWidgetPage")
        );
        $item['/admin/view/widget/disable/@'] = array(
            "access" => "administrer",
            "callback" => array("Widget", "disableWidgetPage")
        );
        $item['/admin/view/widget/uninstall/@'] = array(
            "access" => "administrer",
            "callback" => array("Widget", "uninstallWidgetPage")
        );
        $item['/admin/view/widget/up/@'] = array(
            "access" => "administrer",
            "callback" => array("Widget", "upWidgetPage")
        );
        $item['/admin/view/widget/down/@'] = array(
            "access" => "administrer",
            "callback" => array("Widget", "downWidgetPage")
        );
        return $item;
    }

    public function scanForWidget() {
        $wraw = WidgetObject::loadAll();
        $widgets = array();
        foreach ($wraw as $w) {
            $widgets[$w->widget_name] = $w;
        }
        $a = method_invoke_all("widget", array(), true);
        foreach ($a as $k => $v) {
            $wo = new WidgetObject();
            $wo->load($k);
            $wo->widget_name = $k;
            $wo->module_name = "";
            $wo->callback = implode("::", $v["callback"]);
            $wo->permissions = $v["permissions"];
            $wo->save();
            if(isset($widgets[$k])){
                unset($widgets[$k]);
            }
        }
        foreach ($widgets as $w) {
            $wo = new WidgetObject();
            $wo->load($k);
            $wo->delete();
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
        $widgets = WidgetObject::loadAll(true);
        $unistalled_widgets = WidgetObject::loadAll(false);
        $activates = WidgetObject::loadAllActivate();
        $theme = new Theme();

        $theme->set_title(t("Liste des widgets disponibles"));
        $c = count($widgets);
        $a = count($activates);
        $u = count($unistalled_widgets);

        $notification = "";
        $notification .= ($c+$u) > 1 ? "%cnt widgets disponibles" : (($c+$u) == 1 ? "%cnt widget disponible" : "Aucun widget disponible");
        if($c > 0){
            $notification .= $a > 1 ? " · %ant widgets activés" : ($a == 1 ? " · %ant widget activé" : "");
            $notification .= $c > 1 ? " · %int widgets installés" : ($c == 1 ? " · %int widget installé" : "");
            $notification .= $u > 1 ? " · %unt widgets désinstallés" : ($u == 1 ? " · %unt widget désinstallé" : "");
        }
        Notification::statusNotify(t("$notification", array("%cnt" => $c+$u, "%ant" => $a, "%int" => $c, "%unt" => $u)), Notification::STATUS_INFO);

        $r = array(t("Nom du widget"), t("Etat du widget"), t("Actions"));
        $array = array();
        foreach ($widgets as $k => $w) {
            $uninstall = $theme->linking(Page::url("/admin/view/widget/uninstall/" . $w->widget_name), t("désinstaller"));
            $disable = $theme->linking(Page::url("/admin/view/widget/disable/" . $w->widget_name), t("désactiver"));
            $enable = $theme->linking(Page::url("/admin/view/widget/enable/" . $w->widget_name), t("activer"));

            $up = $theme->linking(Page::url("/admin/view/widget/up/" . $w->widget_name), "<i class='fa fa-arrow-up fa-fw'></i>", false, array("title" => "Monter"));
            $up_disabled = "<span class='link_disabled' title='Monter'><i class='fa fa-arrow-up fa-fw'></i></span>";
            $down = $theme->linking(Page::url("/admin/view/widget/down/" . $w->widget_name), "<i class='fa fa-arrow-down fa-fw'></i>", false, array("title" => "Descendre"));
            $down_disabled = "<span class='link_disabled' title='Descendre'><i class='fa fa-arrow-down fa-fw'></i></span>";


            // Si le widget est activé
            if ($w->activate) {
                $array[] = array($w->widget_name, t("Activé"), $disable, $k == 0 ? $up_disabled : $up, $k == ($a - 1) ? $down_disabled : $down);
            } else {
                $array[] = array($w->widget_name, t("Désactivé"), $enable." - ".$uninstall, $up_disabled, $down_disabled);
            }
        }
        foreach ($unistalled_widgets as $k => $w) {
            $install = $theme->linking(Page::url("/admin/view/widget/install/" . $w->widget_name), t("installer"));
            $up_disabled = "<span class='link_disabled' title='Monter'><i class='fa fa-arrow-up fa-fw'></i></span>";
            $down_disabled = "<span class='link_disabled' title='Descendre'><i class='fa fa-arrow-down fa-fw'></i></span>";

            $array[] = array($w->widget_name, t("Désinstallé"), $install, $up_disabled, $down_disabled);
        }

        $theme->add_to_body($theme->tabling($array, $r));
        $theme->process_theme(Theme::STRUCT_ADMIN);
        return;
    }

    public static function upWidgetPage($widgetName) {
        $widget = new WidgetObject();
        if ($widget->load($widgetName)) {
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
        if ($widget->load($widgetName)) {
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

    public static function enableWidgetPage($widgetName) {
        $widget = new WidgetObject();
        if ($widget->load($widgetName)) {
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

    public static function disableWidgetPage($widgetName) {
        $widget = new WidgetObject();
        if ($widget->load($widgetName)) {
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

    public static function installWidgetPage($widgetName) {
        $widget = new WidgetObject();
        if ($widget->load($widgetName)) {
            if ($widget->install($widget)) {
                header("location: " . Page::url("/admin/view/widget/"));
            } else {
                header("location: " . Page::url("/admin/view/widget/?err=install"));
            }
        } else {
            header("location: " . Page::url("/admin/view/widget/?err=unknown"));
        }
        return;
    }

    public static function uninstallWidgetPage($widgetName) {
        $widget = new WidgetObject();
        if ($widget->load($widgetName)) {
            if ($widget->uninstall($widget)) {
                header("location: " . Page::url("/admin/view/widget/"));
            } else {
                header("location: " . Page::url("/admin/view/widget/?err=install"));
            }
        } else {
            header("location: " . Page::url("/admin/view/widget/?err=unknown"));
        }
        return;
    }

    public function priority() {
        return 100;
    }

    public function system_init() {
        // TODO: Implement system_init() method.
    }

}
