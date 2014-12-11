<?php
class file implements system_module, m_database {

    public function priority() {
        
    }

    public function system_init() {
        
    }

    public function schema() {
        $schema = array();
        $schema['file'] = array(
            "fields" => array(
                "fid" => "int not null AUTO_INCREMENT",
                "file_path" => "varchar(1024) NOT NULL UNIQUE",
                "file_size" => "int",
                "file_type" => "varchar(256) NOT NULL DEFAULT 'unknown'",
            ),
            "pk" => array("fid"),
        );
        $schema['file_usage'] = array(
            "fields" => array(
                "fid" => "int not null",
                "time" => "int not null",
                "module" => "varchar(256) not null default 'file'",
            ),
            "pk" => array("fid","time"),
        );
        return $schema;
    }

}