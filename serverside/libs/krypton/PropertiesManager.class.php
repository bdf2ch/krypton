<?php

    class PropertiesManager {

        public static function init() {
            if (!DBManager::is_table_exists_mysql("settings")) {
                DBManager::create_table_mysql("settings");
                DBManager::add_column_mysql("settings", "module_id", "varchar(200) NOT NULL default ''");
                DBManager::add_column_mysql("settings", "title", "varchar(200) NOT NULL");
                DBManager::add_column_mysql("settings", "type", "varchar(100) NOT NULL");
                DBManager::add_column_mysql("settings", "value", "varchar(500) NOT NULL");
                DBManager::add_column_mysql("settings", "is_system", "int(11) NOT NULL default 1");
            }
            DBManager::insert_row_mysql("settings", ["title", "type", "value", "is_system"], ["'Продолжительность сессии пользователя'", "'integer'", 1800, 1]);
        }

        public static function add ($moduleName, $propName, $propType, $propValue, $propIsSystem) {
            if (DBManager::is_table_exists_mysql() == true) {
                DBManager::insert_row_mysql("settings", ["title", "type", "value", "is_system"], ["'Продолжительность сессии пользователя'", "'integer'", 1800, 1]);
            }
        }

    }

?>