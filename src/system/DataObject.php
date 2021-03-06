<?php

abstract class DataObject {

    private $_data;

    public function __construct() {
        $this->_data = new stdClass();
    }

    public abstract function tableName();

    public abstract function index();

    public static function loadAll() {
        $cname = get_called_class();
        $o = new $cname();
        return Database::getAll("SELECT * FROM " . CONFIG_DB_PREFIX . $o->tableName() . " WHERE 1");
    }

    public function load($keyload) {
        $conditions = array();
        if (is_array($keyload)) {
            foreach ($keyload as $k => $v) {
                $conditions[] = "$k='$v'";
            }
        } else {
            $t = $this->index();
            $conditions[] = $t[0] . "='$keyload'";
        }
        $result = Database::getRow("SELECT * FROM  " . CONFIG_DB_PREFIX . $this->tableName() . " WHERE " . implode(" AND ", $conditions));
        if ($result !== false) {
            $this->_data = $result;
            return true;
        }
        return false;
    }

    public function getStdClass() {
        return $this->_data;
    }

    public function delete() {
        Database::delete($this->tableName(), $this->_data);
        return true;
    }

    public function save() {
        $keys = $this->index();
        Database::insert($this->tableName(), $this->_data, true);
        $t = Database::lastID();
        $this->{$keys[0]} = $t;
        return true;
    }

    public function __get($param) {
        if (isset($this->_data->{$param})) {
            return _Security::stripSlashesLoop($this->_data->{$param});
        } else {
            return null;
        }
    }

    public function __set($param, $value) {
        $this->_data->{$param} = _Security::addSlahesLoop($value);
    }

    public function __isset($name) {
        return (isset($this->_data->{$name}));
    }

}
