<?php

    class Settings {
        private static $items = array();


        /**
        * Производит установку подсистемы
        **/
        public static function install () {
            $result = DBManager::create_table("kr_settings");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось создать таблицу с настройками");
                return false;
            }

            $result = DBManager::add_column("kr_settings", "extension_id", "varchar(200) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'extension_id' в таблицу с настройками");
                return false;
            }

            $result = DBManager::add_column("kr_settings", "code", "varchar(200) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'code' в таблицу с настройками");
                return false;
            }

            $result = DBManager::add_column("kr_settings", "title", "varchar(200) NOT NULL");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'title' в таблицу с настройками");
                return false;
            }

            $result = DBManager::add_column("kr_settings", "description", "varchar(200) NOT NULL");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'description' в таблицу с настройками");
                return false;
            }

            $result = DBManager::add_column("kr_settings", "type", "int(11) NOT NULL default 2");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'type' в таблицу с настройками");
                return false;
            }

            $result = DBManager::add_column("kr_settings", "value", "varchar(500) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'value' в таблицу с настройками");
                return false;
            }

            $result = DBManager::add_column("kr_settings", "is_system", "int(11) NOT NULL default 1");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'is_system' в таблицу с настройками");
                return false;
            }



            if (!DBManager::is_table_exists("kr_settings")) {
                if (DBManager::create_table("kr_settings")) {
                    if (DBManager::add_column("kr_settings", "module_id", "varchar(200) NOT NULL default ''") &&
                        DBManager::add_column("kr_settings", "code", "varchar(200) NOT NULL default ''") &&
                        DBManager::add_column("kr_settings", "title", "varchar(200) NOT NULL") &&
                        DBManager::add_column("kr_settings", "description", "varchar(200) default ''") &&
                        DBManager::add_column("kr_settings", "type", "int(11) NOT NULL default 2") &&
                        DBManager::add_column("kr_settings", "value", "varchar(500) NOT NULL default ''") &&
                        DBManager::add_column("kr_settings", "is_system", "int(11) NOT NULL default 1")
                    ) {
                        if (Settings::add("'krypton'", "'app_title'", "'Наименование приложения'", "''", Krypton::DATA_TYPE_STRING, "''", 1) &&
                            Settings::add("'krypton'", "'app_description'", "'Описание приложения'", "''", Krypton::DATA_TYPE_STRING, "''", 1) &&
                            Settings::add("'krypton'", "'app_debug_mode'", "'Режим отладки'", "'Приложение находится в режиме отладки'", Krypton::DATA_TYPE_BOOLEAN, 0, 1) &&
                            Settings::add("'krypton'", "'app_construction_mode'", "'Сервисный режим'", "'Приложение находится в сервисном режиме'", Krypton::DATA_TYPE_BOOLEAN, 0, 1)
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

        //public static function isInstalled () {
        //    if (DBManager::is_table_exists("kr_settings"))
        //        return true;
        //    else
        //        return false;
        //}


        /**
        * Производит инициализацию подсистемы
        **/
        public static function init () {
            //echo("settings init</br>");
            $settings = DBManager::select("kr_settings", ["*"], "''");
            if ($settings != false) {
                foreach ($settings as $key => $item) {
                    $setting = new Setting (
                        $item["module_id"],
                        $item["code"],
                        $item["title"],
                        intval($item["type"]),
                        $item["value"],
                        $item["description"],
                        $item["is_system"]
                    );
                    array_push(self::$items, $setting);
                }
            }
        }



        /**
        * Возвращает все настройки системы
        **/
        public static function getAll () {
            return self::$items;
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
                    "kr_settings",
                    ["module_id", "code", "title", "description", "type", "value", "is_system"],
                    [$moduleTitle, $settingCode, $settingTitle, $settingDescription, $settingDataType, $settingValue, $settingIsSystem]
            )) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> add: Не удалось добавить настройку в систему");
                return false;
            } else {
                $setting = new Setting(
                    $moduleTitle,
                    $settingCode,
                    $settingTitle,
                    $settingDataType,
                    $settingValue,
                    $settingDescription,
                    $settingIsSystem
                );
                array_push(self::$items, $setting);
                return true;
            }
        }



        public static function addSetting ($setting) {
            if ($setting == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Не задан параметр - настройка");
                return false;
            }

            if (get_class($setting) != "Setting") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Неверно задан тип параметра - настройка");
                return false;
            }

            $result = DBManager::insert_row(
                "kr_settings",
                ["extension_id", "code", "title", "description", "type", "value", "is_system"],
                [
                    "'".$setting -> extensionId -> value."'",
                    "'".$setting -> code -> value."'",
                    "'".$setting -> title -> value."'",
                    "'".$setting -> description -> value."'",
                    $setting -> type -> value,
                    intval($setting -> isSystem -> value)
                 ]
            );
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> add: Не удалось добавить настройку '".$setting -> code -> value."'");
                return false;
            }

            array_push(self::$items, $setting);
            return true;
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
                    foreach (self::$items as $key => $setting) {
                        if ($setting -> code == $settingCode)
                            return self::$items[$key] -> value;
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
                    if ($settingValue == null && gettype($settingValue) != "boolean") {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> setByCode: Не задан параметр - значение настройки");
                        return false;
                    } else {
                        $settingFound = false;
                        $settingIndex = 0;
                        $oldValue = "";
                        $newValue = "";

                        foreach (self::$items as $key => $setting) {
                            if ($setting -> code == $settingCode) {
                                $settingFound = true;
                                $settingIndex = $key;
                                $oldValue = $setting -> value;
                                switch ($setting -> dataType) {
                                    case Krypton::DATA_TYPE_INTEGER:
                                        if (gettype($settingValue) != "integer") {
                                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Тип значения настройки не соответствует типу данных настройки");
                                            return false;
                                        } else {
                                            $setting -> value = $settingValue;
                                            $newValue = $settingValue;
                                        }
                                        break;
                                    case Krypton::DATA_TYPE_STRING:
                                        if (gettype($settingValue) != "string") {
                                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Тип значения настройки не соответствует типу данных настройки");
                                            return false;
                                        } else {
                                            $setting -> value = $settingValue;
                                            $newValue = "'".$settingValue."'";
                                        }
                                        break;
                                    case Krypton::DATA_TYPE_FLOAT:
                                        if (gettype($settingValue) != "double") {
                                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Тип значения настройки не соответствует типу данных настройки");
                                            return false;
                                        } else {
                                            $setting -> value = $settingValue;
                                            $newValue = $settingValue;
                                        }
                                        break;
                                    case Krypton::DATA_TYPE_BOOLEAN:
                                        if (gettype($settingValue) != "boolean") {
                                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Тип значения настройки не соответствует типу данных настройки");
                                            return false;
                                        } else {
                                            $setting -> value = $settingValue;
                                            $newValue = intval($settingValue);
                                        }
                                        break;
                                }
                            }
                        }

                        if ($settingFound == false) {
                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Настройка с кодом '".$settingCode."' не найдена");
                            return false;
                        } else {
                            if (!DBManager::update("kr_settings", ["value"], [$newValue], "code = '$settingCode'")) {
                                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> setByCode: Не удалось установить значение настройки");
                                self::$items[$settingIndex] -> value = $oldValue;
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