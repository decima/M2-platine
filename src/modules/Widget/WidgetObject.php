<?php

class WidgetObject extends DataObject {

    const WIDGET_NO_POSITION = -1;
    const WIDGET_LATERAL_LEFT = 0;

    public static function schema(&$schema) {
        $schema["widget"] = array(
            "widget_name" => Database::FIELD_TYPE_STRING + Database::PRIMARY_KEY,
            "module_name" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "callback" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "permissions" => Database::FIELD_TYPE_STRING,
            "priority" => Database::FIELD_TYPE_INT,
            "position" => Database::FIELD_TYPE_INT,
            "activate" => Database::FIELD_TYPE_INT
        );
    }

    public function __construct(){
        parent::__construct();
        $this -> priority = 0;
        $this -> position = self::WIDGET_NO_POSITION;
        $this -> activate = 0;
    }

    public function __set($param, $value) {
        parent::__set($param, $value);
    }

    public function index() {
        return array("widget_name");
    }

    public function tableName() {
        return "widget";
    }

    public function load($bloc_name) {
        return parent::load(array("widget_name" => $bloc_name));
    }

    public static function loadAll() {
        $d = new WidgetObject();
        $request = "SELECT * FROM " . CONFIG_DB_PREFIX . $d->tableName() . " ORDER BY position ASC, activate DESC, priority ASC";
        $results = Database::getAll($request);
        return $results == null ? array() : $results;
    }

    public static function loadByPosition($position) {
        $d = new WidgetObject();
        $request = "SELECT widget_name FROM " . CONFIG_DB_PREFIX . $d->tableName() . " WHERE position = " . $position . " AND activate = 1 ORDER BY priority ASC";
        $results = Database::getAll($request);
        $return = array();
        if(is_array($results)) {
            foreach ($results as $v) {
                $n = new WidgetObject();
                $n->load($v->widget_name);
                $return[] = $n;
            }
        }
        return $return;
    }
}