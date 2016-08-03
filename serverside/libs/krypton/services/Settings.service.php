<?php

    class Settings implements Service {
        private static $items = array();


        /**
        * Производит установку сервиса
        **/
        public static function install () {
            $result = DBManager::create_table("kr_settings");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось создать таблицу с настройками 'kr_settings'");
                return false;
            }

            switch (Krypton::getDBType()) {

                case Krypton::DB_TYPE_MYSQL:

                    $result = DBManager::add_column("kr_settings", "extension_id", "varchar(200) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'extension_id' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "code", "varchar(200) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'code' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "title", "varchar(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'title' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "description", "varchar(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'description' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "type", "int(11) NOT NULL default 2");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'type' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "value", "varchar(500) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'value' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "is_system", "int(11) NOT NULL default 1");
                     if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'is_system' в таблицу 'kr_settings'");
                        return false;
                    }

                    break;

                case Krypton::DB_TYPE_ORACLE:

                    $result = DBManager::add_column("kr_settings", "extension_id", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'extension_id' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "code", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'code' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "title", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'title' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "description", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'description' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "type", "INT DEFAULT 2 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'type' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "value", "VARCHAR2(500) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'value' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_settings", "is_system", "INT DEFAULT 1 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить столбец 'is_system' в таблицу 'kr_settings'");
                        return false;
                    }

                    $result = DBManager::add_sequence("seq_settings", 1, 1);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> install: Не удалось добавить последовательность 'seq_settings'");
                        return false;
                    }

                    break;
            }

            return true;
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
                        $item["EXTENSION_ID"],
                        $item["CODE"],
                        $item["TITLE"],
                        intval($item["TYPE"]),
                        $item["VALUE"],
                        $item["DESCRIPTION"],
                        $item["IS_SYSTEM"]
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
        public static function add ($extensionId, $code, $title, $description, $type, $value, $isSystem) {
            if ($extensionId == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Не задан парметр - идентификатор расширения");
                return false;
            }

            if (gettype($extensionId) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Неверно задан тип параметра - идентификатор расширения");
                return false;
            }

            if ($code == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Не задан парметр - код настройки");
                return false;
            }

            if (gettype($code) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Неверно задан тип параметра - код настройки");
                return false;
            }

            if ($title == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Не задан парметр - наименование настройки");
                return false;
            }

            if (gettype($title) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Неверно задан тип параметра - наименование настройки");
                return false;
            }

            if ($description == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Не задан парметр - описание настройки");
                return false;
            }

            if (gettype($description) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Неверно задан тип параметра - описание настройки");
                return false;
            }

            if ($type == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Не задан парметр - тип значения настройки");
                return false;
            }

            if (gettype($type) != "integer") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Неверно задан тип параметра - тип значения настройки");
                return false;
            }

            if ($type != Krypton::DATA_TYPE_INTEGER && $type != Krypton::DATA_TYPE_FLOAT && $type != Krypton::DATA_TYPE_STRING && $type != Krypton::DATA_TYPE_BOOLEAN) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Settings -> add: Неверно задано значение параметра - тип значения настройки");
                return false;
            }

            switch (Krypton::getDBType()) {
                case Krypton::DB_TYPE_MYSQL:
                    $result = DBManager::insert_row(
                        "kr_settings",
                        ["extension_id", "code", "title", "description", "type", "value", "is_system"],
                        [$extensionId, $code, $title, $description, $type, $value, $isSystem]
                    );
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> add: Не удалось добавить настройку '".$code."'");
                        return false;
                    }

                    return true;
                    break;

                case Krypton::DB_TYPE_ORACLE:
                    $seq = DBManager::sequence_next("seq_settings");
                    if (!$seq) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> add: Не удалось получить следующее значение последовательности 'seq_settings'");
                        return false;
                    }

                    $result = DBManager::insert_row(
                        "kr_settings",
                        ["id", "extension_id", "code", "title", "description", "type", "value", "is_system"],
                        [$seq, $extensionId, $code, $title, $description, $type, $value, $isSystem]
                    );
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Settings -> add: Не удалось добавить настройку '".$code."'");
                        return false;
                    }
                    break;
            }

            return true;


            /*
            if (!DBManager::insert_row (
                    "kr_settings",
                    ["extension_id", "code", "title", "description", "type", "value", "is_system"],
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
            */
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