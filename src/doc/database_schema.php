<?php

/**
 * This is an example on how to declare a database scheme.
 */
class aRandomModule {

    public function schema() {
        $schema = array();



        $schema['tablename1'] = array(
            "fieldname1" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT, //integer primary key autoincrement
            "fieldname2" => Database::FIELD_TYPE_FLOAT, // float
            "fieldname3" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL, //varchar(255) and not null
            "fieldname4" => Database::FIELD_TYPE_TEXT, //Text
            "fieldname5" => Database::FIELD_TYPE_DATE, //datetime
        );

        $schema['tablename2'] = array(
                //another table with other fields
        );
        return $schema;
    }

}
