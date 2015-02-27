<?php

class WidgetObject extends DataObject {

    const WIDGET_NO_POSITION = -1;
    const WIDGET_LATERAL_LEFT = 0;

    const WIDGET_CHANGE_PRIORITY_UP = 1;
    const WIDGET_CHANGE_PRIORITY_DOWN = 0;

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

    public static function loadAll($isInstalled = null) {
        $d = new WidgetObject();
        $request = "SELECT * FROM " . CONFIG_DB_PREFIX . $d->tableName();
        $request .= (isset($isInstalled) AND $isInstalled != null) ? ($isInstalled ? " WHERE position > -1" : " WHERE position = -1") : "";
        $request .= " ORDER BY position ASC, activate DESC, priority ASC";
        $results = Database::getAll($request);
        return $results == null ? array() : $results;
    }

    public static function loadAllActivate($position = null, $havePriority = null) {
        $d = new WidgetObject();
        $request = "SELECT * FROM " . CONFIG_DB_PREFIX . $d->tableName() . " WHERE activate = 1";
        $request .= (isset($position) AND $position != null) ? " AND position = ".$position : "";
        $request .= (isset($havePriority) AND $havePriority != null) ? ($havePriority ? " AND priority <> 0" : " AND priority = 0") : "";
        $request .= " ORDER BY position ASC, activate DESC, priority ASC";
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

    public static function loadByPositionAndPriority($position, $priority) {
        $d = new WidgetObject();
        $request = "SELECT widget_name FROM " . CONFIG_DB_PREFIX . $d->tableName() . " WHERE position = " . $position . " AND priority = " . $priority . " AND activate = 1 ORDER BY priority ASC";
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

    public static function changePriority($widget, $direction){
        $widgets = WidgetObject::loadAllActivate();
        $c = count($widgets);

        switch($direction){
            case self::WIDGET_CHANGE_PRIORITY_UP:
                if($widget->priority > 1)
                    $voisin_priority = $widget->priority-1;
                else
                    return false;
                break;
            case self::WIDGET_CHANGE_PRIORITY_DOWN:
                if($widget->priority < $c)
                    $voisin_priority = $widget->priority+1;
                else
                    return false;
                break;
            default:
                return false;
                break;
        }
        $voisin = self::loadByPositionAndPriority($widget->position, $voisin_priority);

        if(sizeof($voisin) == 1){
            $voisin[0]->priority = $widget->priority;
            $widget->priority = $voisin_priority;

            $voisin[0]->save();
            $widget->save();
            return true;
        }
        return false;
    }

    public static function changeActivate($widget){
        switch($widget->activate){
            case 0:
                $widget->activate = 1;
                $widget->save();
                self::resetAllPriorityByPosition($widget->position);
                return true;
            case 1:
                $widget->activate = 0;
                $widget->priority = 0;
                $widget->save();
                self::resetAllPriorityByPosition($widget->position);
                return true;
            default:
                return false;
        }
    }

    public static function install($widget){
        if($widget->position == -1){
            $widget->position = 0;
            $widget->save();
            self::resetAllPriorityByPosition($widget->position);
            return true;
        }
        return false;
    }

    public static function uninstall($widget){
        if($widget->position > -1 AND $widget->activate == 0){
            $widget->position = -1;
            $widget->save();
            self::resetAllPriorityByPosition($widget->position);
            return true;
        }
        return false;
    }

    public static function resetAllPriorityByPosition($position){
        $widgets = WidgetObject::loadByPosition($position);
        $activates = self::loadAllActivate($position, true);
        $max = count($activates)+1;

        foreach($widgets as $k => $w){
            if($w->activate == 1){
                if($w->priority == 0){
                    $w->priority = $max;
                    $max++;
                    $w->save();
                }
                else if($w->priority > ($k+1)){
                    $w->priority = $k+1;
                    $w->save();
                }
            }
            else if ($w->activate == 0 AND $w->priority != 0){
                $w->priority = 0;
                $w->save();
            }
        }
    }
}