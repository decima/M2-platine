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

    public static function page_config() {
        $w = new Widget();
        $w -> scanForWidget();
        return "welcome";
    }

    public function scanForWidget(){
        $a = method_invoke_all("widget", array(), true);
        foreach($a as $k => $v){
            $wo = new WidgetObject();
            $wo -> load($k);
            $wo -> widget_name = $k;
            $wo -> module_name = "";
            $wo -> callback = implode("::", $v["callback"]);
            $wo -> permissions = $v["permissions"];
            $wo -> save();
        }
    }

    public function runWidgets($position = WidgetObject::WIDGET_LATERAL_LEFT, callable $callable = null){
        $w = WidgetObject::loadByPosition($position);
        if($callable != null){
            foreach($w as $k => $v){
                $callable(call_user_func($v -> callback));
            }
        }
    }
}
