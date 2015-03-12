<?php

class FileObject extends DataObject {


    public static function schema(&$schema) {
        $schema["file"] = array(
            "id_file" => Database::FIELD_TYPE_INT + Database::AUTOINCREMENT + Database::PRIMARY_KEY,
            "permission" => Database::FIELD_TYPE_STRING,
            "module" => Database::FIELD_TYPE_STRING,
            "path" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "content_type" => Database::FIELD_TYPE_STRING,
            "date_upload" => Database::FIELD_TYPE_INT,
            "nb_dl" => Database::FIELD_TYPE_INT
        );
    }

    public function __construct(){
        parent::__construct();
        $this -> date_upload = time();
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

    public function uploadFile($file){
        $target_dir = "data/";
        $filename = md5(time());
        $i = 1;
        $cpt = md5($i);
        $ext = strtolower(substr(strrchr($file['name'], '.'), 1));
        $target_file = $target_dir . $filename."_".$cpt.".".$ext;

        // Si le fichier existe
        while(file_exists(Page::path($target_file))){
            $i++;
            $cpt = md5($i);
            $target_file = $target_dir . $filename."_".$cpt.".".$ext;
        }

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $f = new FileObject();
            $f -> permission = "access content";
            $f -> module = "";
            $f -> path = "/".$target_file;
            $f -> content_type = $file['type'];
            $f -> save();

            return $f -> id_file;
        } else {
            return false;
        }

    }
}