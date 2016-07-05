<?php

    class Kolenergo extends ExtensionInterface {
        public static $title = "Колэнерго";
        public static $description = "Модуль портала Колэнерго";
        public static $url = "modules/app/krypton.app.kolenergo.js";

        public static $departments = array();
        public static $divisions = array();



        public function __construct () {
            parent::__construct();
        }



        public function install () {
            $result = DBManager::is_table_exists("kr_users");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Таблица с пользователями не найдена");
                return false;
            }

            $result = DBManager::add_column("kr_users", "department_id", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'department_id' в таблицу ползователей");
                return false;
            }

            $result = DBManager::add_column("kr_users", "division_id", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'division_id' в таблицу ползователей");
                return false;
            }

            $result = Models::extend("User1", "departmentId", new Field(array( "source" => "departmentId", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            if (Errors::isError($result)) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить свойство 'departmentId' в класс User");
                return false;
            }

            $result = Models::extend("User1", "divisionId", new Field(array( "source" => "divisionId", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            if (Errors::isError($result)) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить свойство 'divisionId' в класс User");
                return false;
            }

            $result = DBManager::create_table("departments");
            if (!result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось создать таблицу производственных отделений");
                return false;
            }

            $result = DBManager::add_column("departments", "title", "varchar(500) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу производственных отделений");
                return false;
            }

            $result = DBManager::insert_row("departments", ["title"], ["'Аппарат управления'"]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу производственных отделений");
                return false;
            }

            $result = DBManager::insert_row("departments", ["title"], ["'Северные электрические сети'"]);
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу производственных отделений");

            $result = DBManager::insert_row("departments", ["title"], ["'Центральные электрические сети'"]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу производственных отделений");
                return false;
            }

            $result = DBManager::create_table("divisions");
            if (!result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось создать таблицу отделов");
                return false;
            }

            $result = DBManager::add_column("divisions", "title", "varchar(500) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу отделов");
                return false;
            }

            $result = DBManager::add_column("divisions", "department_id", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'department_id' в таблицу отделов");
                return false;
            }

            $result = DBManager::add_column("divisions", "parent_id", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'parent_id' в таблицу отделов");
                return false;
            }

            $result = DBManager::insert_row("divisions", ["department_id", "parent_id", "title"], [1, 0, "'Отдел аппарата управления'"]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу отделов");
                return false;
            }

            $result = DBManager::insert_row("divisions", ["department_id", "parent_id", "title"], [2, 0, "'Отдел северных сетей'"]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу отделов");
                return false;
            }

            $result = DBManager::insert_row("divisions", ["department_id", "parent_id", "title"], [3, 0, "'Отдел северных сетей'"]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу отделов");
                return false;
            }

            return true;
        }



        public function isInstalled () {
            $check = DBManager::is_column_exists("kr_users", "department_id");
            return $check;
        }



        public function init () {
            //echo("kolenergo init</br>");

            Krypton::$app -> addJavaScript("modules/app/krypton.app.kolenergo.js");

            $departmentIdProperty = Models::extend("User1", "departmentId", new Field(array( "source" => "department_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));;
            $divisionIdProperty = Models::extend("User1", "divisionId", new Field(array( "source" => "division_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            $ldapEnabledProperty = Models::extend("User1", "ldapEnabled", new Field(array( "source" => "ldap_enabled", "type" => Krypton::DATA_TYPE_BOOLEAN, "value" => true, "defaultValue" => true )));

            //if (!defined("ENGINE_ADMIN_MODE"))

            if (Errors::isError($departmentIdProperty) && Errors::isError($divisionIdProperty))
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить свойства в класс User");

            API::add("test", "Kolenergo", "getDepartments");
            API::add("addDivision", "Kolenergo", "addDivision");



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



        public function getDepartments () {
            return self::$departments;
        }



        public static function getDivisions () {
            return self::$divisions;
        }



        public static function addDivision ($data) {

        }



        public function login ($login, $password) {
            if ($login == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> login: Не задан параметр - логин пользователя");
                return false;
            }

            if (gettype($login) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> login: Неверно задан тп параметра - логин пользователя");
                return false;
            }

            if ($password == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> login: Не задан параметр - пароль пользователя");
                return false;
            }

            if (gettype($password) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> login: Неверно задан тп параметра - пароль пользователя");
                return false;
            }

            if (Extensions::get("LDAP") -> get("enabled")) {

                $user = Extensions::get("LDAP") -> login($login, $password);
                if ($user != false) {
                    $newUser = Users::add($user);
                    Sessions::assignCurrentSessionToUser($newUser -> id -> value);
                }


            } else {
                $result = Sessions::login($login, $password);
            }

            if ($result == true) {
                $result = Extensions::get("LDAP") -> login($login, $password);
            } else {
                //$result = Sessions::login($login, $password);
                //if (!Errors::isError($result) && )
            }

            if (!Errors::isError($result) && $result == true)
                return false;

            return $result;
        }



    };

?>