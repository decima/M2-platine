<?php

class PermissionObject extends DataObject {

    public static function schema(&$schema) {
        $schema["permission"] = array(
            "pid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            "permission_name" => Database::FIELD_TYPE_STRING,
        );
        $schema["permission_group"] = array(
            "pid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY,
            "gid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY,
        );
    }

    public function index() {
        return array("pid");
    }

    public function tableName() {
        return "permission";
    }

    public function load($id) {
        return parent::load(array("pid" => $id));
    }

    public function groupIsAllowed($gid) {
        return Database::getValue("select 1 from " . CONFIG_DB_PREFIX . "permission_group where pid=" . $this->pid . " AND gid=$gid");
    }

    public function loadByName($pm) {
        return parent::load(array("permission_name" => $pm));
    }

    public static function loadAllPermissions($pid = null) {
        if ($pid != null) {
            return Database::getAll("select * from " . CONFIG_DB_PREFIX . "permission_group where pid=$pid");
        } else {

            return Database::getAll("select * from " . CONFIG_DB_PREFIX . "permission_group");
        }
    }

    public static function removeAllPermissions() {
        return Database::execute("DELETE from " . CONFIG_DB_PREFIX . "permission_group");
    }

    public static function addPermission($pid, $gid) {
        return Database::execute("insert into " . CONFIG_DB_PREFIX . "permission_group(pid, gid) values ($pid, $gid);");
    }

}
