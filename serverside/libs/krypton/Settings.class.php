<?php

    class Settings {

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
        * Производит инициализацию подсистемы
        **/
        public function init () {
            $settings = DBManager::select("kr_settings", ["*"], "''");
            if ($settings != false) {
                foreach ($settings as $key => $item) {
                    $setting = new Setting($item["module_id"], $item["code"], $item["title"], $item["type"], $item["value"], $item["description"], $item["is_system"]);
                    array_push(self::$settings, $setting);
                }
            }
        }



        /**
        * Возвращает все настройки системы
        **/
        public static function getAll () {
            return self::$settings;
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
            if (!DBManager::insert_row (
                    self::$id,
                    ["module_id", "code", "title", "description", "type", "value", "is_system"],
                    [$moduleTitle, $settingCode, $settingTitle, $settingDescription, $settingDataType, $settingValue, $settingIsSystem]
            )) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> add: Не удалось добавить настройку в систему");
                return false;
            } else {
                $setting = new Setting($moduleTitle, $settingCode, $settingTitle, $settingDataType, $settingValue, $settingDescription, $settingIsSystem);
                return true;
            }
        }



        /**
        * Возвращает значение настройки по коду настройки
        * @settingCode - Код настройки
        **/
        public static function getByCode ($settingCode) {
            if ($settingCode == null) {
                Errors::push(Errors::ERROR_TYE_DEFAULT, "Settings -> getByCode: Не задан параметр - код настройки");
                return false;
            } else {
                if (gettype($settingCode) != "string") {
                    Errors::push(Errors::ERROR_TYE_DEFAULT, "Settings -> getByCode: Неверно задан тип параметра - код настройки");
                    return false;
                } else {
                    foreach (self::$settings as $key => $setting) {
                        if ($setting -> code == $settingCode)
                            return self::$settings[$key] -> value;
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
                Errors::push(Errors::ERROR_TYE_DEFAULT, "Settings -> setByCode: Не задан параметр - код настройки");
                return false;
            } else {
                if (gettype($settingCode) != "string") {
                    Errors::push(Errors::ERROR_TYE_DEFAULT, "Settings -> setByCode: Неверно задан тип параметра - кодл настройки");
                    return false;
                } else {
                    if ($settingValue == null) {
                        Errors::push(Errors::ERROR_TYE_DEFAULT, "Settings -> setByCode: Не задан параметр - значение настройки");
                        return false;
                    } else {
                        $settingFound = false;
                        $settingIndex = 0;
                        $oldValue;
                        foreach (self::$settings as $key => $setting) {
                            if ($setting -> code == $settingCode) {
                                $settingFound = true;
                                $settingIndex = $key;
                                $oldValue = $setting -> value;
                                switch ($setting -> dataType) {
                                    case Krypton::DATA_TYPE_INTEGER:
                                        if (gettype($settingValue) != "integer") {
                                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Тип значения настройки не соответствует типу данных настройки");
                                            return false;
                                        } else
                                            $setting -> value = intval($settingValue);
                                        break;
                                    case Krypton::DATA_TYPE_STRING:
                                        if (gettype($settingValue) != "string") {
                                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Тип значения настройки не соответствует типу данных настройки");
                                            return false;
                                        } else
                                            $setting -> value = $settingValue;
                                        break;
                                    case Krypton::DATA_TYPE_FLOAT:
                                        if (gettype($settingValue) != "double") {
                                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Тип значения настройки не соответствует типу данных настройки");
                                            return false;
                                        } else
                                            $setting -> value = floatval($settingValue);
                                        break;
                                    case Krypton::DATA_TYPE_BOOLEAN:
                                        if (gettype($settingValue) != "boolean") {
                                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Тип значения настройки не соответствует типу данных настройки");
                                            return false;
                                        } else
                                            $setting -> value = boolval($settingValue);
                                        break;
                                }
                            }
                        }

                        if ($settingFound == false) {
                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Настройка с кодом '".$settingCode."' не найдена");
                            return false;
                        } else {
                            if (!DBManager::update_row("settings", ["value"], [$setting -> value], "code = '$settingCode'")) {
                                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Не удалось установить значение настройки");
                                self::$settings[$settingIndex] -> value = $oldValue;
                                return false;
                            } else
                                return true;
                        }
                    }
                }
            }
        }


    };

?>