<?php

    class Kolenergo extends ExtensionInterface {
        public static $id = "krypton.app.kolenergo";
        public static $title = "Колэнерго";
        public static $description = "Модуль портала Колэнерго";
        public static $url = "modules/app/krypton.app.kolenergo.js";

        public static $departments = array();



        public static function install () {
            if (DBManager::is_table_exists("kr_users")) {
                if (
                    DBManager::add_column("kr_users", "department_id", "int(11) NOT NULL default 0") &&
                    DBManager::add_column("kr_users", "division_id", "int(11) NOT NULL default 0")
                ) {
                    $departmentIdProperty = Models::extend("User1", "departmentId", new Field(array( "source" => "departmentId", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));;
                    $divisionIdProperty = Models::extend("User1", "divisionId", new Field(array( "source" => "divisionId", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
                    if (Errors::isError($departmentIdProperty) && Errors::isError($divisionIdProperty))
                        return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить свойства в класс User");
                    else {
                        if (DBManager::create_table("departments")) {
                             if (
                                DBManager::add_column("departments", "title", "varchar(500) NOT NULL default ''")
                             ) {
                                if (
                                    DBManager::insert_row("departments", ["title"], ["'Аппарат управления'"]) &&
                                    DBManager::insert_row("departments", ["title"], ["'Северные электрические сети'"]) &&
                                    DBManager::insert_row("departments", ["title"], ["'Центральные электрические сети'"])
                                )
                                    return true;
                                else
                                    return Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу производственных отделений");
                             } else
                                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбцы в таблицу производственных отделений");
                        } else
                            return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось создать таблицу производственных отделений");
                    }
                } else
                    return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбцы в таблицу ползователей");
            } else
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Таблица с пользователями не найдена");

        }



        public static function isInstalled () {
            $check = DBManager::is_column_exists("kr_users", "department_id");
            return Errors::isError($check) == true ? false : $check;
        }



        public static function init () {
            echo("kolenergo init</br>");

            $departmentIdProperty = Models::extend("User1", "departmentId", new Field(array( "source" => "department_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));;
            $divisionIdProperty = Models::extend("User1", "divisionId", new Field(array( "source" => "division_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));

            if (!defined("ENGINE_ADMIN_MODE"))

            if (Errors::isError($departmentIdProperty) && Errors::isError($divisionIdProperty))
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить свойства в класс User");

            if (self::isInstalled() == true) {
                $departments = DBManager::select("departments", ["*"], "''");
                if ($departments != false) {
                    foreach ($departments as $key => $item) {
                        $department = Models::load("Department", false);
                        $department -> fromSource($item);
                        array_push(self::$departments, $department);
                    }
                }
                //var_dump(self::$departments);
            } else
                self::install();
        }



        public static function getDepartments () {
            return self::$departments;
        }

    };

?>