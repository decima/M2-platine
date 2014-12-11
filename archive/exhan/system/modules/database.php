<?php
class database implements system_module, m_constants {

    public static $_last_id;
    public function main() {
        
    }

    public function priority() {
        return -99;
    }

    public function system_init() {
        
    }

    public function install_schema($module_name) {
        $schema = method_invoke($module_name, "schema");
        foreach($schema as $table_name => $datas) {
            database::create_table($table_name, $datas['fields'], $datas['pk']);
        }
    }

    public function uninstall_schema($module_name) {
        $schema = method_invoke($module_name, "schema");
        print_r($schema);
        foreach($schema as $table_name => $datas) {
            database::drop_table($table_name);
        }
    }

    public static function pdo() {
        $pdo = null;
        $host = constants::get("BDD_HOST");
        $login = constants::get("BDD_LOGIN");
        $pass = constants::get("BDD_PASS");
        $bd = constants::get("BDD_DB");
        $param1PDO = 'mysql:host='.$host.';dbname='.$bd;
        try {
            $pdo = new PDO($param1PDO,$login,$pass);
        }catch(Exception $e) {
            die('Erreur : '.$e->getMessage());
        }
        $pdo->exec("SET NAMES 'UTF8'");
        return $pdo;
    }

    public static function query($sql, $params = array()) {
        $pdo = self::pdo();
        $sql = secure_string($sql, $params);
        //echo $sql."<hr/>".printer($params)."<hr/>";
        $b = $pdo->query($sql);
        self::$_last_id = $pdo->lastInsertId();
        return $b;
    }

    public static function exec($sql) {
        $pdo = self::pdo();
        return $pdo->exec($sql);
    }

    public static function fetch(PDOStatement $statement,
            $method = PDO::FETCH_OBJ) {
        return $statement->fetch($method);
    }

    public static function fetchAll(PDOStatement $statement,
            $method = PDO::FETCH_OBJ) {
        return $statement->fetchAll($method);
    }

    public static function insert($table, $values) {
        $set = "";
        $val = "";
        $params = array();
        foreach($values as $key => $value) {
            $set .=" $key,";
            //$b = new PDO($dsn, $username, $passwd, $options);
            $val .=" ".self::pdo()->quote($value).",";
        }

        $set = substr($set, 0, strlen($set) - 1);
        $val = substr($val, 0, strlen($val) - 1);
        $sql = "insert into $table ( $set ) value( $val )";

        $q = self::query($sql, $params);

        return $q;
    }

    public static function delete($table, $condition = 1, $array = array()) {
        $sql = "delete from `$table`  where $condition";
        return self::query($sql, $array);
    }

    public static function update($table, $fields, $condition = 1,
            $params = array()) {
        $value = "";
        foreach($fields as $k => $v) {
            $value .= " $k=".self::pdo()->quote($v).",";
            $params["%up_$k"] = $v;
        }
        $value = substr($value, 0, strlen($value) - 1);
        $sql = "update `$table`  set $value where $condition";
        return self::query($sql, $params);
    }

    public static function select($table, $champ = array(), $condition = 1,
            $params = array()) {
        $field = " ";
        if(empty($champ)) {
            $field = "*";
        }else {
            foreach($champ as $value) $field .=" $value,";
            $field = substr($field, 0, strlen($field) - 1);
        }
        $sql = "select $field from `$table` where $condition";

        return self::query($sql, $params);
    }

    public static function create_table(
    $table_name, $fields = array(), $primary_keys = array()) {

        $pk = "";
        foreach($primary_keys as $k) {
            $pk .=" $k,";
        }
        $pk = substr($pk, 0, strlen($pk) - 1);

        $sql = "CREATE TABLE $table_name
        (";
        foreach($fields as $fieldname => $fielddata) {
            $sql.="\n$fieldname $fielddata,";
        }
        if($pk != "") {
            $sql .="\nCONSTRAINT pk_$table_name PRIMARY KEY ($pk)";
        }
        $sql .=");";
        //echo "<hr/>".$sql."<hr/>";
        $_SESSION['log'][] = $sql;

        return self::exec($sql);
    }

    public static function drop_table($table_name) {
        $sql = "DROP TABLE $table_name";
        return self::exec($sql);
    }

    public static function setPrimaryKey($table, $key = array()) {
        $pk = "";
        foreach($key as $k) {
            $pk .=" $k,";
        }
        $pk = substr($pk, 0, strlen($pk) - 1);

        $sql = "ALTER TABLE $table ADD PRIMARY KEY ($pk)";
        return self::exec($sql);
    }

    public static function addfield($table_name, $field_name, $field_data) {
        $sql = "ALTER TABLE $table_name 
        ADD $field_name $field_data";
        return self::exec($sql);
    }

    public static function removefield($table_name, $field_name) {
        $sql = "ALTER TABLE $table_name DROP COLUMN $field_name";
        return self::exec($sql);
    }

    public function constants() {

        return array(
            "BDD_HOST" => "localhost",
            "BDD_LOGIN" => "com.exhan",
            "BDD_PASS" => "Wv8zurR4fFrpUmPh",
            "BDD_DB" => "com.exhan",
        );
    }

}

