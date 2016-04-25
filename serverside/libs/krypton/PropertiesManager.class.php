<?php

    class PropertiesManager {
        private static $properties = array();

        public static function init() {
            $props = DBManager::select_mysql("settings", ["*"], "''");
            if ($props != false)
                self::$properties = $props;

            var_dump(self::$properties);


        }

        public static function getByCode ($propCode) {
            if ($propCode != null) {
                if (gettype($propCode) == "string") {
                    foreach (self::$properties as $key => $prop) {
                        if ($prop["code"] == $propCode)
                            return self::$properties[$key]["value"];
                         //return false;
                    }
                } else {
                    ErrorManager::add (
                        ERROR_TYPE_ENGINE,
                        ERROR_ENGINE_GET_PROPERTY_WRONG_TITLE_TYPE,
                        "Задан неверный тип параметра при получении значения настройки - код настройки"
                    ) -> send();
                    return false;
                }
            } else {
                ErrorManager::add (
                    ERROR_TYPE_ENGINE,
                    ERROR_ENGINE_GET_PROPERTY_NO_TITLE,
                   "Не указан параметр при получении значения настройки - код настройки"
                ) -> send();
                return false;
            }
        }

        public static function add ($moduleName, $propCode, $propName, $propType, $propValue, $propIsSystem) {
            if (DBManager::is_table_exists_mysql("settings") == true) {
                if (DBManager::insert_row_mysql("settings", ["module_id", "code", "title", "type", "value", "is_system"], [$moduleName, $propCode, $propName, $propType, $propValue, $propIsSystem]))
                    return true;
            } else {
                ErrorManager::add(
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_SETTINGS_TABLE_DOES_NOT_EXISTS,
                    "Не удалось добавить настройку - отсутствует таблица с настройками"
                ) -> send();
                return false;
            }
        }

        public static function install () {
            if (!DBManager::is_table_exists_mysql("settings")) {
                if (DBManager::create_table_mysql("settings")) {
                    if (DBManager::add_column_mysql("settings", "module_id", "varchar(200) NOT NULL default ''") &&
                        DBManager::add_column_mysql("settings", "code", "varchar(200) NOT NULL default ''") &&
                        DBManager::add_column_mysql("settings", "title", "varchar(200) NOT NULL") &&
                        DBManager::add_column_mysql("settings", "description", "varchar(200) default ''") &&
                        DBManager::add_column_mysql("settings", "type", "varchar(100) NOT NULL") &&
                        DBManager::add_column_mysql("settings", "value", "varchar(500)") &&
                        DBManager::add_column_mysql("settings", "is_system", "int(11) NOT NULL default 1")) {
                        if (DBManager::insert_row_mysql("settings", ["code", "title", "type", "value", "is_system"], ["'app_title'", "'Наименование приложения'", "'string'", "''", 1]) &&
                            DBManager::insert_row_mysql("settings", ["code", "title", "type", "value", "is_system"], ["'app_description'", "'Описание приложения'", "'string'", "''", 1]) &&
                            DBManager::insert_row_mysql("settings", ["code", "title", "type", "value", "is_system"], ["'app_debug_mode'", "'Приложение в режиме отладки'", "'boolean'", 0, 1]) &&
                            DBManager::insert_row_mysql("settings", ["code", "title", "type", "value", "is_system"], ["'app_under_construction'", "'Приложение в сервисном режиме'", "'boolean'", 0, 1])) {
                            echo("Установка PropertiesManager выполнена успешно</br>");
                            } else
                                echo("Не удалось выполнить установку PropertiesManager");
                    } else
                        echo("Не удалось выполнить установку PropertiesManager");
                } else
                    echo("Не удалось выполнить установку PropertiesManager");
            }
        }

    };

?>