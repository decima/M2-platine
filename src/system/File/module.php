<?php

/**
 * @moduleName File
 *
 *
 * */
require_once("FileObject.php");

class File implements SystemModule {

    public function info() {
        return array(
            "name" => "File",
            "readablename" => "File"
        );
    }

    public function schema($schema = array()) {
        FileObject::schema($schema);
        return $schema;
    }

    public function menu($item = array()) {
        $item['/file/install'] = array(
            "access" => "access content",
            "callback" => array("File", "inst")
        );
        $item['/file/@'] = array(
            "access" => "access content",
            "callback" => array("File", "page_display_content")
        );
        $item['/file/@/download'] = array(
            "access" => "access content",
            "callback" => array("File", "page_download_content")
        );
        return $item;
    }

    /*
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
            if (isset($widgets[$k])) {
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
                $run = true;
                $r = method_invoke_all("permissions", array($v->permissions));
                foreach ($r as $tt)
                    if ($tt == false)
                        $run = false;
                if ($run)
                    $callable(call_user_func($v->callback));
            }
        }
    }
    */

    public static function page_display_content($id_file) {
        $theme = new Theme();
        $f = new FileObject();
        $f->load($id_file);
        if($f->id_file != null) {

        }
        else {
            $theme->process_404();
        }
        return;
    }

    public static function inst(){
        ModuleManager::install_module("File", "./system/File/module.php");
    }


    public function priority() {
        return 100;
    }

    public function system_init() {
        // TODO: Implement system_init() method.
    }

}
