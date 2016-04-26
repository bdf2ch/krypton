<?php

    class Settings extends Module {
        private static $settings = array();

        public static function install () {
            if (!DBManager::is_table_exists_mysql("settings")) {
                if (DBManager::create_table_mysql("settings")) {
                    if (DBManager::add_column_mysql("settings", "module_id", "varchar(200) NOT NULL default ''") &&
                        DBManager::add_column_mysql("settings", "code", "varchar(200) NOT NULL default ''") &&
                        DBManager::add_column_mysql("settings", "title", "varchar(200) NOT NULL") &&
                        DBManager::add_column_mysql("settings", "description", "varchar(200) default ''") &&
                        DBManager::add_column_mysql("settings", "type", "varchar(100) NOT NULL") &&
                        DBManager::add_column_mysql("settings", "value", "varchar(500)") &&
                        DBManager::add_column_mysql("settings", "is_system", "int(11) NOT NULL default 1")
                    ) {
                        Settings::setInstalled(true);
                        if (Settings::add("'engine'", "'app_title'", "'Наименование приложения'", "''", "'string'", "''", 1) &&
                            Settings::add("'engine'", "'app_description'", "'описание приложения'", "''", "'string'", "''", 1) &&
                            Settings::add("'engine'", "'app_debug_mode'", "'Режим отладки'", "'Приложение находится в режиме отладки'", "'boolean'", 0, 1) &&
                            Settings::add("'engine'", "'app_construction_mode'", "'Сервисный режим'", "'Приложение находится в сервисном режиме'", "'boolean'", 0, 1)
                        )
                            echo("Установка модуля Settings выполнена успешно</br>");
                        else
                            echo("Не удалось выполнить установку модуля Settings</br>");
                    } else
                        echo("Не удалось выполнить установку модуля Settings</br>");
                } else
                    echo("Не удалось выполнить установку модуля Settings</br>");
            }
        }


        public function init () {
            echo("settings module is here</br>");
            Settings::setLoaded(true);

            $settings = DBManager::select_mysql("settings", ["*"], "''");
            Settings::$settings = $settings != false ? $settings : array();
            var_dump(Settings::$settings);

            DBManager::update_row_mysql("settings", ["title", "description"], ["'test_title'", "'test_description'"], "code = 'app_title'");
        }


        public static function add ($moduleTitle, $settingCode, $settingTitle, $settingDescription, $settingDataType, $settingValue, $settingIsSystem) {
            if (Settings::isInstalled()) {
                if (DBManager::insert_row_mysql("settings", ["module_id", "code", "title", "description", "type", "value", "is_system"], [$moduleTitle, $settingCode, $settingTitle, $settingDescription, $settingDataType, $settingValue, $settingIsSystem]))
                    return true;
            } else {
                ErrorManager::add(
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_SETTINGS_TABLE_DOES_NOT_EXISTS,
                    "Не удалось добавить настройку - отсутствует таблица с настройками"
                ) -> send();
                return false;
            }



            /*
            if (DBManager::is_table_exists_mysql("settings") == true) {
                if (DBManager::insert_row_mysql("settings", ["module_id", "code", "title", "description", "type", "value", "is_system"], [$moduleName, $propCode, $propName, $propType, $propValue, $propIsSystem]))

                    return true;
            } else {
                ErrorManager::add(
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_SETTINGS_TABLE_DOES_NOT_EXISTS,
                    "Не удалось добавить настройку - отсутствует таблица с настройками"
                ) -> send();
                return false;
            }
            */

        }


        public static function getByCode ($settingCode) {
            if ($settingCode != null) {
                if (gettype($settingCode) == "string") {
                    foreach (self::$settings as $key => $setting) {
                        if ($setting["code"] == $settingCode)
                            return self::$settings[$key]["value"];
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


        public static function setByCode ($settingCode, $settingValue) {
            if ($settingCode != null) {
                if (gettype($settingCode) == "string") {
                    if ($settingValue != null) {

                    }
                }
            }
        }


    };

?>