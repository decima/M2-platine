<?php

/**
 * @moduleName Widget
 *
 *
 * */
require_once("WidgetObject.php");

class Widget implements Module {

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
                    Notification::statusNotify(t("Echec de l'installation du module"), Notification::STATUS_ERROR);
                    break;
                case 'enable':
                    Notification::statusNotify(t("Echec de l'activation du module"), Notification::STATUS_ERROR);
                    break;
                case 'disable':
                    Notification::statusNotify(t("Echec de la desactivation du module"), Notification::STATUS_ERROR);
                    break;
                case 'uninstall':
                    Notification::statusNotify(t("Echec de la désinstation du module"), Notification::STATUS_ERROR);
                    break;
                default:
                    Notification::statusNotify(t("Une erreur inconnue est survenue"), Notification::STATUS_ERROR);

                    break;
            }
        }
        $widget = new Widget();
        $widget->scanForWidget();
        $widgets = WidgetObject::loadAll();
        $theme = new Theme();

        $theme->set_title(t("Liste des widgets disponibles"));
        Notification::statusNotify(t("%cnt widgets disponibles",array("%cnt"=>count($widgets))), Notification::STATUS_INFO);
        $r = array(t("Nom du widget"), t("Etat du widget"), t("Actions"));
        $array = array();
        foreach ($widgets as $w) {
            /*
            $install = $theme->linking(Page::url("/admin/modules/install/" . $m['name']), t("installer"));
            $uninstall = $theme->linking(Page::url("/admin/modules/uninstall/" . $m['name']), t("désinstaller"));
            $disable = $theme->linking(Page::url("/admin/modules/disable/" . $m['name']), t("désactiver"));
            $enable = $theme->linking(Page::url("/admin/modules/enable/" . $m['name']), t("activer"));

            $statement = t("activé");
            $link_1 = $disable;
            $link_2 = null;
            if (!self::is_enabled($m['name'])) {
                $statement = t("installé");
                $link_1 = $enable;
                $link_2 = $uninstall;
            }
            if (!self::is_installed($m['name'])) {
                $link_1=$install;$link_2 = null;
                $statement = t("désinstallé");
            }


            if ($m["system_module"] == 1) {
                $rtm = t("système");
                $statement = t("");

            } else {
                $rtm = ($link_1).($link_2==null?"":" - ").$link_2;
            }
            */
            $array[] = array($w -> widget_name, $w -> activate ? t("Activé") : t("Désactivé"), t("Pomme"));
        }

        $theme->add_to_body($theme->tabling($array, $r));
        $theme->process_theme(Theme::STRUCT_ADMIN);
        return;
    }

}
