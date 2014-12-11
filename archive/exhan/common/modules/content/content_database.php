<?php

class content_database {

    public static function schema() {
        $array = array();
        $array['node_type'] = array(
            "fields" => array(
                "type" => "VARCHAR(128) NOT NULL",
                "name" => "VARCHAR(128) NOT NULL",
                "table_name" => "VARCHAR(128) NOT NULL",
                "is_commentable" => "INT(1) NOT NULL DEFAULT 1",
                "has_title" => "INT(1) NOT NULL DEFAULT 1",
                "has_body" => "INT(1) NOT NULL DEFAULT 1",
            ),
            "pk" => array("type"),
        );

        $array['node_structure'] = array(
            "fields" => array(
                "type" => "VARCHAR(128) NOT NULL",
                "field_name" => "VARCHAR(128) NOT NULL",
                "field_machine_name" => "VARCHAR(128) NOT NULL",
                "field_type" => "VARCHAR(128) NOT NULL",
                "default_value" => "TEXT",
            ),
            "pk" => array("type", "field_machine_name"),
        );

        $array['node'] = array(
            "fields" => array(
                "nid" => "INT NOT NULL AUTO_INCREMENT",
                "type" => "VARCHAR(128) NOT NULL",
                "title" => "VARCHAR(150)",
                "description" => "TEXT",
                "uid" => "INT",
                "author" => "VARCHAR(255)",
                "date" => "INT",
            ),
            "pk" => array("nid")
        );
        $array['node_comment'] = array(
            "fields" => array(
                "cid" => "INT NOT NULL AUTO_INCREMENT",
                "nid" => "INT NOT NULL",
                "content" => "TEXT NOT NULL",
                "author" => "VARCHAR(255)",
                "email" => "VARCHAR(255)",
                "uid" => "INT",
                "date" => "INT",
            ),
            "pk" => array("cid")
        );
        return $array;
    }

    public static function load_node_type($type) {
        $b = new stdClass();
        $b = database::fetch(database::select("node_type", array(), "type='%type'", array("%type" => $type)));
        if (is_object($b)) {
            $b->fields = database::fetchAll(
                            database::select(
                                    "node_structure"
                                    , array()
                                    , "type='%type'"
                                    , array("%type" => $type)
                            ), PDO::FETCH_ASSOC);
            return $b;
        }
        else {
            return false;
        }
    }

    public static function get_all_node_types() {
        return database::fetchAll(
                        database::select(
                                "node_type"));
    }

    public static function get_list_of_node_types() {
        return database::fetchAll(
                        database::select(
                                "node_type", array('type')), PDO::FETCH_COLUMN);
    }

    public static function create_new_content_type($readableName, $machineName) {
        $table = "nodedata_$machineName";
        database::create_table($table, array(
            "nid" => "INT NOT NULL",
                ), array("nid"));
        database::insert("node_type", array(
            "type" => $machineName,
            "name" => $readableName,
            "table_name" => $table,
                )
        );
    }

    public static function node_type_get_table($machinename) {
        return database::fetch(database::select(
                                "node_type", array("table_name"), "type='%machineName'", array("%machineName" => $machinename)
                        ), PDO::FETCH_COLUMN);
    }

    public static function add_field(
    $type
    , $readablename
    , $machinename
    , $field_type = 'TEXT'
    , $default = 'this is some default value'
    ) {
        database::insert("node_structure", array(
            "type" => $type,
            "field_name" => $readablename,
            "field_machine_name" => $machinename,
            "field_type" => $field_type,
            "default_value" => $default
                )
        );
        database::addfield(
                self::node_type_get_table($type)
                , $machinename
                , "$field_type");
    }

    public static function delete_field($type, $machinename) {
        database::delete("node_structure", "type='%type' AND field_machine_name='%machine'", array("%type" => $type, "%machine" => $machinename));
        database::removefield(self::node_type_get_table($type), $machinename);
    }

    public static function insert_node($type, $title, $body, $content = array(), $uid = 0, $author = "unknown") { 
        $nid = self::insert_node_basic($type, $title, $body, $uid, $author);
        $nt = self::load_node_type($type);
        $table_node = $nt->table_name;
        $content = array_merge(array('nid' => $nid), $content);

        database::insert($table_node, $content);
        return $nid;
    }

    public static function insert_node_basic($type, $title, $body, $uid = 0, $author = "unknown") {
        database::insert("node", array(
            "type" => $type,
            "title" => $title,
            "description" => $body,
            "uid" => $uid,
            "author" => $author,
            "date" => time(),
        ));
        return database::$_last_id;
    }

    public static function node_exist($nid) {
        $node = database::fetch(database::select("node", array(), "nid='%nid'", array('%nid' => $nid)));
        return $node ? true : false;
    }

    public static function node_load($nid) {
        if (!self::node_exist($nid))
            return false;
        $node = database::fetch(database::select("node", array(), "nid='%nid'", array('%nid' => $nid)));
        $nt = self::load_node_type($node->type);
        $table_node = $nt->table_name;
        $fields = database::fetch(database::select($table_node, array(), "nid='%nid'", array('%nid' => $nid)), PDO::FETCH_ASSOC);
        if ($fields != false)
            foreach ($fields as $k => $v) {
                $node->{$k} = $v;
            }
        return $node;
    }

    public static function node_load_all() {

        return database::fetchAll(database::select("node", array(), "1 ORDER BY date DESC"));
    }

    public static function delete_content_type($type) {
        $table = self::node_type_get_table($type);
        database::drop_table($table);
        Database::delete("node_type", "type='$type'");
        Database::delete("node","type='$type'");
    }

}
