<?php

class FileObject extends DataObject {


    public static function schema(&$schema) {
        $schema["file"] = array(
            "id_file" => Database::FIELD_TYPE_INT + Database::AUTOINCREMENT + Database::PRIMARY_KEY,
            "permission" => Database::FIELD_TYPE_STRING,
            "module" => Database::FIELD_TYPE_STRING,
            "path" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "content_type" => Database::FIELD_TYPE_STRING,
            "nb_dl" => Database::FIELD_TYPE_INT
        );
    }

    public function __construct(){
        parent::__construct();
        $this -> nb_dl = 0;
    }

    public function __set($param, $value) {
        parent::__set($param, $value);
    }

    public function index() {
        return array("id_file");
    }

    public function tableName() {
        return "file";
    }

    public function load($id) {
        return parent::load(array("id_file" => $id));
    }

    public function getExtension(){
        return strtolower(substr(strrchr($this->path, '.'), 1));
    }
}