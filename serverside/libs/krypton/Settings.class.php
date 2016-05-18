<?php

    class Settings {

        private static $id = "kr_settings";
        private static $settings = array();


        /**
        * Производит установку подсистемы
        **/
        public static function install () {
            if (!DBManager::is_table_exists("kr_settings")) {
                if (DBManager::create_table("kr_settings")) {
                    if (DBManager::add_column("kr_settings", "module_id", "varchar(200) NOT NULL default ''") &&
                        DBManager::add_column("kr_settings", "code", "varchar(200) NOT NULL default ''") &&
                        DBManager::add_column("kr_settings", "title", "varchar(200) NOT NULL") &&
                        DBManager::add_column("kr_settings", "description", "varchar(200) default ''") &&
                        DBManager::add_column("kr_settings", "type", "varchar(100) NOT NULL") &&
                        DBManager::add_column("kr_settings", "value", "varchar(500)") &&
                        DBManager::add_column("kr_settings", "is_system", "int(11) NOT NULL default 1")
                    ) {
                        //Settings::setInstalled(true);
                        if (Settings::add("'krypton'", "'app_title'", "'Наименование приложения'", "''", "'string'", "''", 1) &&
                            Settings::add("'krypton'", "'app_description'", "'описание приложения'", "''", "'string'", "''", 1) &&
                            Settings::add("'krypton'", "'app_debug_mode'", "'Режим отладки'", "'Приложение находится в режиме отладки'", "'boolean'", 0, 1) &&
                            Settings::add("'krypton'", "'app_construction_mode'", "'Сервисный режим'", "'Приложение находится в сервисном режиме'", "'boolean'", 0, 1)
                        )
                            return true;
                        else {
                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить базовые настройки в систему");
                            return false;
                        }
                    } else {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось создать структуру таблицы настроек");
                        return false;
                    }
                } else {
                    Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось создать таблицу с настройками");
                    return false;
                }
            } else
                return false;
        }



        /**
        * Проверяет, установлен ли модуль в системе
        **/

        /*public static function isInstalled () {
            if (DBManager::is_table_exists(self::$id))
                return true;
            else
                return false;
        }*/


        /**
        * Производит инициализацию модуля
        **/
        public function init () {
            //Settings::setLoaded(true);

            $settings = DBManager::select(self::$id, ["*"], "''");
            if ($settings != false) {
                foreach ($settings as $key => $item) {
                    $setting = new Setting($item["module_id"], $item["code"], $item["title"], $item["type"], $item["value"], $item["description"], boolval($item["is_system"]));
                    array_push($setting, self::$settings);
                }
            }
            //self::$settings = $settings != false ? $settings : array();

            //self::setByCode("app_title", "'another app title'");
            //self::setByCode("test_setting", "'poo poo'");


            //echo("</br>");
            //var_dump(Settings::$settings);
            //echo("</br>");

            //DBManager::update_row_mysql("settings", ["title", "description"], ["'test_title'", "'test_description'"], "code = 'app_title'");
            //self::add("'test'", "'test_setting'", "'test setting'", "'test setting title'", "'string'", "'test setting value'", 1);
        }


        public static function getAll () {

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
            //if (self::isInstalled()) {
                if (DBManager::insert_row(
                         self::$id,
                        ["module_id", "code", "title", "description", "type", "value", "is_system"],
                        [$moduleTitle, $settingCode, $settingTitle, $settingDescription, $settingDataType, $settingValue, $settingIsSystem]
                    )
                ) {
                    $setting = new Setting();
                    return true;
                }
            //} else {
            //    Errors::push(7001);
            //    return false;
            //}
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