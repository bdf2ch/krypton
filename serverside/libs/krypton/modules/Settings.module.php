<?php

    class Settings extends Module {

        private static $id = "kr_settings";
        private static $settings = array();


        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            if (!DBManager::is_table_exists("kr_settings")) {
                if (DBManager::create_table_mysql("kr_settings")) {
                    if (DBManager::add_column_mysql(self::$id, "module_id", "varchar(200) NOT NULL default ''") &&
                        DBManager::add_column_mysql(self::$id, "code", "varchar(200) NOT NULL default ''") &&
                        DBManager::add_column_mysql(self::$id, "title", "varchar(200) NOT NULL") &&
                        DBManager::add_column_mysql(self::$id, "description", "varchar(200) default ''") &&
                        DBManager::add_column_mysql(self::$id, "type", "varchar(100) NOT NULL") &&
                        DBManager::add_column_mysql(self::$id, "value", "varchar(500)") &&
                        DBManager::add_column_mysql(self::$id, "is_system", "int(11) NOT NULL default 1")
                    ) {
                        Settings::setInstalled(true);
                        if (Settings::add("'".self::$id."'", "'app_title'", "'Наименование приложения'", "''", "'string'", "''", 1) &&
                            Settings::add("'".self::$id."'", "'app_description'", "'описание приложения'", "''", "'string'", "''", 1) &&
                            Settings::add("'".self::$id."'", "'app_debug_mode'", "'Режим отладки'", "'Приложение находится в режиме отладки'", "'boolean'", 0, 1) &&
                            Settings::add("'".self::$id."'", "'app_construction_mode'", "'Сервисный режим'", "'Приложение находится в сервисном режиме'", "'boolean'", 0, 1)
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


        /**
        * Проверяет, установлен ли модуль в системе
        **/
        public static function isInstalled () {
            if (DBManager::is_table_exists(self::$id))
                return true;
            else
                return false;
        }


        /**
        * Производит инициализацию модуля
        **/
        public function init () {
            /*
            Errors::add(new Error (
                Errors::ERROR_TYPE_DEFAULT,
                7001,
                "Settings: Модуль не установлен</br>"
            ));
            Errors::add(new Error (
                Errors::ERROR_TYPE_DEFAULT,
                7005,
                "Settings -> getByCode: Не задан параметр - код настройки</br>"
            ));
            Errors::add(new Error (
                Errors::ERROR_TYPE_DEFAULT,
                7006,
                "Settings -> getByCode: Неверно задан тип параметра - код настройки</br>"
            ));
            Errors::add(new Error (
                Errors::ERROR_TYPE_DEFAULT,
                7007,
                "Settings -> setByCode: Не задан параметр - код настройки</br>"
            ));
            Errors::add(new Error (
                Errors::ERROR_TYPE_DEFAULT,
                7008,
                "Settings -> setByCode: Неверно задан тип параметра - код настройки</br>"
            ));
            Errors::add(new Error (
                Errors::ERROR_TYPE_DEFAULT,
                7009,
                "Settings -> setByCode: Не задан параметр - Значение настройки</br>"
            ));
            Errors::add(new Error (
                Errors::ERROR_TYPE_DEFAULT,
                7010,
                "Settings -> setByCode: Неверно задан тип параметра - значение настройки</br>"
            ));
            */

            //Settings::setLoaded(true);

            $settings = DBManager::select(self::$id, ["*"], "''");
            Settings::$settings = $settings != false ? $settings : array();

            //self::setByCode("app_title", "'another app title'");
            //self::setByCode("test_setting", "'poo poo'");


            //echo("</br>");
            //var_dump(Settings::$settings);
            //echo("</br>");

            //DBManager::update_row_mysql("settings", ["title", "description"], ["'test_title'", "'test_description'"], "code = 'app_title'");
            //self::add("'test'", "'test_setting'", "'test setting'", "'test setting title'", "'string'", "'test setting value'", 1);
        }


        /**
        * Добавляет настройку в систему
        * @moduleTitle - Наименование модуля
        * @settingCode - Код настройки
        * @settingTitle - Наименование настройки
        * @settingDescription - Описание настройки
        * @settingDataType - Тип данных настройки
        * @settingValue - Значение настройки
        * @settingIsSystem - Является ли настройка системной
        **/
        public static function add ($moduleTitle, $settingCode, $settingTitle, $settingDescription, $settingDataType, $settingValue, $settingIsSystem) {
            if (self::isInstalled()) {
                if (DBManager::insert_row(
                         self::$id,
                        ["module_id", "code", "title", "description", "type", "value", "is_system"],
                        [$moduleTitle, $settingCode, $settingTitle, $settingDescription, $settingDataType, $settingValue, $settingIsSystem]
                    )
                )
                return true;
            } else {
                Errors::push(7001);
                return false;
            }
        }


        /**
        * Возвращает значение настройки по коду настройки
        * @settingCode - Код настройки
        **/
        public static function getByCode ($settingCode) {
            if ($settingCode == null) {
                Errors::push(7005);
                return false;
            } else {
                if (gettype($settingCode) != "string") {
                    Errors::push(7006);
                    return false;
                } else {
                    foreach (self::$settings as $key => $setting) {
                        if ($setting["code"] == $settingCode)
                            return self::$settings[$key]["value"];
                    }
                }
            }
        }


        /**
        * Устанавливает значение настройки по коду настройки
        * @settingCode - Код настроки
        * @settingValue - Значение настройки
        **/
        public static function setByCode ($settingCode, $settingValue) {
            if ($settingCode == null) {
                Errors::push(7007);
                return false;
            } else {
                if (gettype($settingCode) != "string") {
                    Errors::push(7008);
                    return false;
                } else {
                    if ($settingValue == null) {
                        Errors::push(7009);
                        return false;
                    } else {
                        if (DBManager::update_row("settings", ["value"], [$settingValue], "code = '$settingCode'"))
                            return true;
                    }
                }
            }
        }


    };

?>