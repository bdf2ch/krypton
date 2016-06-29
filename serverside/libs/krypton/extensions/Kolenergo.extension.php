<?php

    class Kolenergo extends ExtensionInterface {
        public static $id = "krypton.app.kolenergo";
        public static $title = "Колэнерго";
        public static $description = "Модуль портала Колэнерго";
        public static $url = "modules/app/krypton.app.kolenergo.js";

        public static $departments = array();
        public static $divisions = array();



        public static function install () {
            $result = DBManager::is_table_exists("kr_users");
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Таблица с пользователями не найдена");

            $result = DBManager::add_column("kr_users", "department_id", "int(11) NOT NULL default 0");
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'department_id' в таблицу ползователей");

            $result = DBManager::add_column("kr_users", "division_id", "int(11) NOT NULL default 0");
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'division_id' в таблицу ползователей");

            $result = Models::extend("User1", "departmentId", new Field(array( "source" => "departmentId", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            if (Errors::isError($result))
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить свойство 'departmentId' в класс User");

            $result = Models::extend("User1", "divisionId", new Field(array( "source" => "divisionId", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            if (Errors::isError($result))
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить свойство 'divisionId' в класс User");

            $result = DBManager::create_table("departments");
            if (!result)
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось создать таблицу производственных отделений");

            $result = DBManager::add_column("departments", "title", "varchar(500) NOT NULL default ''");
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу производственных отделений");

            $result = DBManager::insert_row("departments", ["title"], ["'Аппарат управления'"]);
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу производственных отделений");

            $result = DBManager::insert_row("departments", ["title"], ["'Северные электрические сети'"]);
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу производственных отделений");

            $result = DBManager::insert_row("departments", ["title"], ["'Центральные электрические сети'"]);
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу производственных отделений");

            $result = DBManager::create_table("divisions");
            if (!result)
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось создать таблицу отделов");

            $result = DBManager::add_column("divisions", "title", "varchar(500) NOT NULL default ''");
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу отделов");

            $result = DBManager::add_column("divisions", "department_id", "int(11) NOT NULL default 0");
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'department_id' в таблицу отделов");

            $result = DBManager::add_column("divisions", "parent_id", "int(11) NOT NULL default 0");
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'parent_id' в таблицу отделов");

            $result = DBManager::insert_row("divisions", ["department_id", "parent_id", "title"], [1, 0, "'Отдел аппарата управления'"]);
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу отделов");

            $result = DBManager::insert_row("divisions", ["department_id", "parent_id", "title"], [2, 0, "'Отдел северных сетей'"]);
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу отделов");

            $result = DBManager::insert_row("divisions", ["department_id", "parent_id", "title"], [3, 0, "'Отдел северных сетей'"]);
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу отделов");

            return true;
        }



        public static function isInstalled () {
            $check = DBManager::is_column_exists("kr_users", "department_id");
            return $check;
        }



        public static function init () {
            //echo("kolenergo init</br>");

            $departmentIdProperty = Models::extend("User1", "departmentId", new Field(array( "source" => "department_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));;
            $divisionIdProperty = Models::extend("User1", "divisionId", new Field(array( "source" => "division_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));

            //if (!defined("ENGINE_ADMIN_MODE"))

            if (Errors::isError($departmentIdProperty) && Errors::isError($divisionIdProperty))
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить свойства в класс User");

            API::add("test", "Kolenergo", "getDepartments");

            if (!self::isInstalled())
                self::install();
            else {
                $departments = DBManager::select("departments", ["*"], "''");
                if ($departments != false) {
                    foreach ($departments as $key => $item) {
                        $department = Models::load("Department", false);
                        $department -> fromSource($item);
                        array_push(self::$departments, $department);
                    }
                }

                $divisions = DBManager::select("divisions", ["*"], "''");
                if ($divisions != false) {
                    foreach ($divisions as $key => $item) {
                        $division = Models::load("Division", false);
                        $division -> fromSource($item);
                        array_push(self::$divisions, $division);
                    }
                }
            }
        }



        public static function getDepartments () {
            return self::$departments;
        }



        public static function getDivisions () {
            return self::$divisions;
        }

    };

?>