<?php
class permission implements system_module, m_database, m_page {

    public function priority() {
        
    }

    public function system_init() {
        
    }

    public static function list_of_existing_permissions() {
        $perms = array();
        $a = method_invoke_all("perms");
        foreach($a as $b) {
            $perms = array_merge($perms, $b);
        }
        return $perms;
    }
    

    public static function list_of_declared_permissions() {
        return database::fetchAll(database::select("permission",
                                array("permission","description")),
                        PDO::FETCH_KEY_PAIR);
    }

    public static function update_list() {
        database::delete("permission");
        foreach(self::list_of_existing_permissions() as $perm => $descr) {
            database::insert("permission",
                    array(
                "permission" => $perm,
                "description" => $descr
            ));
        }
    }

    public function schema() {
        $schema = array();
        $schema['permission'] = array(
            "fields" => array(
                "permission" => "varchar(255) not null",
                "description" => "TEXT"
            ),
            "pk" => array("permission")
        );
        return $schema;
    }

    public function menu() {
        $a = array();

        $a['admin/permissions/scan'] = array(
            "callback" => "permission::update_page",
            "access" => "permission::access_update",
            "permission" => "administrer",
        );
        return $a;
    }

    public static function get_all_permissions() {
        return database::fetchAll(database::select("permission"));
    }

    public static function access_update() {
        return true;
    }

    public static function update_page() {
        self::update_list();
        page::redirect("admin/permissions");
    }

}