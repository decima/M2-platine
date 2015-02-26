<?php

class Database implements SystemModule {

    const FIELD_TYPE_INT = 128;
    const FIELD_TYPE_FLOAT = 64;
    const FIELD_TYPE_STRING = 32;
    const FIELD_TYPE_TEXT = 16;
    const FIELD_TYPE_DATE = 8;
    const PRIMARY_KEY = 4;
    const AUTOINCREMENT = 2;
    const NOTNULLVAL = 1;

    public static $connector = null;

    public static function schema_installer($schema) {
        $keywords = array("INT", "FLOAT", "VARCHAR(255)", "TEXT", "DATETIME", "PRIMARY KEY", "AUTO_INCREMENT", "NOT NULL");
        foreach ($schema as $table => $attributes) {
            if (self::table_exists($table)) {
                throw new Exception_Database("Table exists");
            }
        }
        foreach ($schema as $table => $attributes) {
            $sql = "CREATE TABLE IF NOT EXISTS " . CONFIG_DB_PREFIX . "$table(";
            $i = 0;
            $pks = array();

            foreach ($attributes as $key => $infos) {
                $i++;
                $sql .="\n`$key` ";
                $row = str_pad(decbin($infos), 8, "0", STR_PAD_LEFT);
                $splited_row = str_split($row);
                foreach ($splited_row as $k => $r) {
                    if ($r == 1) {
                        if ($k == 5) {
                            $pks[] = $key;
                        } else {
                            $sql .=$keywords[$k] . " ";
                        }
                    }
                }
                $sql.=",";
            }
            $sql .= "CONSTRAINT pk_$table PRIMARY KEY (" . implode(",", $pks) . ")";
            $sql.=");";
            self::execute($sql);
        }
        foreach ($schema as $table => $attributes) {
            if (!self::table_exists($table)) {
                self::schema_uninstaller($schema);
                throw new Exception_Database("Table does not exist");
            }
        }
    }

    public static function schema_uninstaller($schema) {

        foreach ($schema as $table => $k) {
            self::execute("DROP TABLE IF EXISTS " . CONFIG_DB_PREFIX . "$table CASCADE");
        }
        foreach ($schema as $table => $k) {
            if (self::table_exists($table)) {
                throw new Exception_Database("Table exists");
            }
        }
    }

    public static function table_exists($tablename) {
        $database = "jinn";
        $sql = "SELECT * FROM information_schema.tables WHERE table_schema = '$database' AND table_name = '" . CONFIG_DB_PREFIX . "$tablename' LIMIT 1;";
        return self::getAll($sql) != false;
    }

    public function info() {
        _Security::version(0, 1);
        return array(
            "name" => "Database",
            "readablename" => "Database Module"
        );
    }

    public function priority() {
        return -100;
    }

    public function system_init() {
        if (self::$connector == null) {
            $servername = CONFIG_DB_SERVER;
            $dbname = CONFIG_DB_DATABASE;
            $username = CONFIG_DB_LOGIN;
            $password = CONFIG_DB_PASSWORD;
            self::$connector = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        }
    }

    public static function execute($sql) {
        return self::$connector->query($sql);
    }

    public static function lastID() {
        return self::$connector->lastInsertId();
    }

    public static function insert($table, $fields, $duplicate = false) {
        $imploding = implode(",", array_keys(objectToArray($fields)));
        $values = "'" . implode("','", array_values(objectToArray($fields))) . "'";
        $vals = array();
        foreach (objectToArray($fields) as $k => $v) {
            $vals[] = "$k='$v'";
        }
        $sql = "INSERT INTO " . CONFIG_DB_PREFIX . $table . " ($imploding) VALUES($values)";
        if ($duplicate) {
            $im = implode(" , ", $vals);
            $sql .=" ON DUPLICATE KEY UPDATE " . $im;
        }

        self::execute($sql);
    }

    public static function update($table, $fields, $references = array()) {
        $vals = array();
        $conditions = array("1");
        if (count($references) > 0) {
            $conditions = array();
        }
        foreach ($fields as $k => $v) {
            $vals[] = "$k='$v'";
        }
        foreach ($references as $k => $v) {
            $conditions[] = "$k='$v'";
        }
        $imploding = implode(" , ", $vals);
        $imploding2 = implode(" AND ", $conditions);

        $sql = "UPDATE " . CONFIG_DB_PREFIX . $table . " set $imploding where $imploding2;";
        self::execute($sql);
    }

    public static function delete($table, $references) {
        $vals = array();
        if (count($references) > 0) {
            $conditions = array();
            foreach ($references as $k => $v) {
                $conditions[] = "$k='$v'";
            }
            $imploding2 = implode(" AND ", $conditions);
            $sql = "DELETE FROM " . CONFIG_DB_PREFIX . $table . " where $imploding2;";
            self::execute($sql);
        }
    }

    public static function getAll($sql) {
        $exec = self::$connector->query($sql);
        if ($exec != false)
            return $exec->fetchAll(PDO::FETCH_OBJ);
        return false;
    }

    public static function getRow($sql) {
        $exec = self::$connector->query($sql);
        if ($exec != false)
            return $exec->fetch(PDO::FETCH_OBJ);
        return false;
    }

    public static function getValue($sql) {
        $exec = self::$connector->query($sql);
        if ($exec != false)
            return $exec->fetch(PDO::FETCH_COLUMN);
        return false;
    }

}

class Exception_Database extends Exception {
    
}

class Exception_Database_Exists extends Exception {
    
}

class Exception_Database_Format extends Exception {
    
}
