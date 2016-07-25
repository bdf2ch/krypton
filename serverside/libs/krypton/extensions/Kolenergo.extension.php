<?php

    class Kolenergo extends ExtensionInterface {
        public static $title = "Колэнерго";
        public static $description = "Модуль портала Колэнерго";
        public static $url = "modules/app/krypton.app.kolenergo.js";

        public static $organizations = array();
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

            $result = DBManager::create_table("organizations");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось создать таблицу организаций");
                return false;
            }

            $result = DBManager::add_column("organizations", "title", "varchar(500) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу организаций");
                return false;
            }

            $result = DBManager::create_table("departments");
            if (!$result) {
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
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось создать таблицу отделов");
                return false;
            }

            $result = DBManager::add_column("divisions", "title", "varchar(500) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу отделов");
                return false;
            }

            $result = DBManager::add_column("divisions", "organization_id", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'organization_id' в таблицу отделов");
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



            Krypton::$app -> addJavaScript("modules/app/krypton.app.kolenergo.js");

            $departmentIdProperty = Models::extend("User1", "departmentId", new Field(array( "source" => "department_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));;
            $divisionIdProperty = Models::extend("User1", "divisionId", new Field(array( "source" => "division_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            $ldapEnabledProperty = Models::extend("User1", "ldapEnabled", new Field(array( "source" => "ldap_enabled", "type" => Krypton::DATA_TYPE_BOOLEAN, "value" => true, "defaultValue" => true )));

            //if (!defined("ENGINE_ADMIN_MODE"))

            if (Errors::isError($departmentIdProperty) && Errors::isError($divisionIdProperty))
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить свойства в класс User");

            API::add("test", "Kolenergo", "getDepartments");
            API::add("addOrganization", "Kolenergo", "addOrganization");
            API::add("editOrganization", "Kolenergo", "editOrganization");
            API::add("deleteOrganization", "Kolenergo", "deleteOrganization");
            API::add("addDivision", "Kolenergo", "addDivision");
            API::add("editDivision", "Kolenergo", "editDivision");
            API::add("deleteDivision", "Kolenergo", "deleteDivision");
            API::add("uploadUserPhoto", "Kolenergo", "uploadUserPhoto");
            API::add("login", "Kolenergo", "login");




            if (!self::isInstalled())
                self::install();
            else {
                $organizations = DBManager::select("organizations", ["*"], "''");
                if ($organizations != false) {
                    foreach ($organizations as $key => $item) {
                        $organization = Models::load("Organization", false);
                        $organization -> fromSource($item);
                        array_push(self::$organizations, $organization);
                    }
                }

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





        /**
        * Возвращает массив организаций
        **/
        public function getOrganizations () {
            return self::$organizations;
        }





        /**
        * Добавляет организацию
        * @data {object} - объект с информацией о добавляемой организации
        **/
        public function addOrganization ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> addOrganization: Не задан рарметр - объект с информацией об организации");
                return false;
            }

            $result = DBManager::insert_row("organizations", ["title"], ["'".$data -> title."'"]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addOrganization: Не удалось добавить организацию");
                return false;
            }

            $id = mysql_insert_id();
            $result = DBManager::select("organizations", ["*"], "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addOrganization: Не удалось выбрать добавленную организацию");
                return false;
            }

            $organization = Models::construct("Organization", false);
            $organization -> fromSource($result[0]);
            array_push(self::$organizations, $organization);

            return $organization;
        }





        /**
        * Сохраняет изменения в измененнной организации
        * @data {object} - объект с информацией о редактируемой организации
        **/
        public function editOrganization ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> editOrganization: Не задан рарметр - объект с информацией об организации");
                return false;
            }

            $id = $data -> id;
            $result = DBManager::update("organizations", ["title"], ["'".$data -> title."'"], "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> editOrganization: Не удалось сохранить изменения измененной организации");
                return false;
            }

            $result = DBManager::select("organizations", ["*"], "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> editOrganization: Не удалось выбрать отредактированную организацию");
                return false;
            }

            $organization = Models::construct("Organization", false);
            $organization -> fromSource($result[0]);

            return $organization;
        }





        /**
        * Удаляет организацию
        * @data {object} - объект с информацией об удаляемой организации
        **/
        public function deleteOrganization ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> deleteOrganization: Не задан рарметр - объект с информацией об организации");
                return false;
            }

            $id = $data -> id;
            $result = DBManager::delete("organizations", "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> deleteOrganization: Не удалось удалить организацию");
                return false;
            }

            return true;
        }




        /**
        * Возвращает массив производственных отделений
        **/
        public function getDepartments () {
            return self::$departments;
        }





        /**
        * Возвращает массив отделов
        **/
        public static function getDivisions () {
            return self::$divisions;
        }





        /**
        * Добавляет отдел
        * @data {object} - объект с данными добавляемого отдела
        **/
        public static function addDivision ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "kolenergo -> addDivision: Не задан параметр - объект с информацией о добавляемом отделе");
                return false;
            }

            $result = DBManager::insert_row("divisions", ["organization_id", "parent_id", "title"], [$data -> organizationId, $data -> parentId, "'".$data -> title."'"]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addDivision: Не удалось добавить отдел");
                return false;
            }

            $id = mysql_insert_id();
            $result = DBManager::select("divisions", ["*"], "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addDivision: Не удалось выбрать добавленный отдел");
                return false;
            }

            $division = Models::construct("Division", false);
            $division -> fromSource($result[0]);
            array_push(self::$divisions, $division);

            return $division;
        }





        /**
        * Сохраняет изменения в измененном отделе
        * @data {object} - объект с информацией об изменяемом отделе
        **/
        public static function editDivision ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "kolenergo -> editDivision: Не задан параметр - объект с информацией о редактируемом отделе");
                return false;
            }

            $result = DBManager::update("divisions", ["title", "parent_id"], ["'".$data -> title."'", $data -> parentId], "id = ".$data -> id);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> editDivision: Не удалось обновить информацию об отделе");
                return false;
            }

            $result = DBManager::select("divisions", ["*"], "id = ".$data -> id);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addDivision: Не удалось выбрать измененный отдел");
                return false;
            }

            $division = Models::construct("Division", false);
            $division -> fromSource($result[0]);

            return $division;
        }





        /**
        * Удаляет отдел
        * @data {object} - объект с информацией об удаляемом отделе
        **/
        public static function deleteDivision ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> deleteDivision: Не задан параметр - объект с информацией об удаляемом отделе");
                return false;
            }

            $id = $data -> id;
            $result = DBManager::select("divisions", ["*"], "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> deleteDivision: Не удалось выбрать информацию об удаляемом отделе");
                return false;
            }

            $division = Models::construct("Division", false);
            $division -> fromSource($result[0]);

            $result = DBManager::delete("divisions", "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> deleteDivision: Не удалось удалить отдел");
                return false;
            }

            $result = DBManager::update("divisions", ["parent_id"], [$division -> parentId -> value], "parent_id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> deleteDivision: Не удалось обнулить родительский отдел в дочерних отделах удаляемого отдела");
                return false;
            }

            $result = DBManager::update("kr_users", ["division_id"], [0], "division_id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> deleteDivision: Не удалось обновить информацию о пользователях, принадлежащих удаляемому отделу");
                return false;
            }

            return true;
        }





        public static function login ($params) {
            $errors = array();

            if ($params == null) {
                array_push($errors, Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> login: Не задан параметр -  объект с данными"));
                return false;
            }

            if ($params -> login == null) {
                array_push(Errors::push($errors, Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> login: Не задан параметр - логин пользователя"));
                return false;
            }

            if (gettype($params -> login) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> login: Неверно задан тп параметра - логин пользователя");
                return false;
            }

            if ($params -> password == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> login: Не задан параметр - пароль пользователя");
                return false;
            }

            if (gettype($params -> password) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> login: Неверно задан тип параметра - пароль пользователя");
                return false;
            }

            if (Extensions::get("LDAP") -> get("enabled")) {
                $user = Extensions::get("LDAP") -> login($params -> login, $params -> password);
                if ($user != false) {
                    $result = Users::getByEmail($user -> email -> value);
                    if (!$result) {
                        $newUser = Users::add($user);
                        if ($newUser != false)
                            Sessions::setCurrentUserById($newUser -> id -> value);
                        return $newUser;
                    }

                    Sessions::setCurrentUserById($result -> id -> value);
                    return $user;
                } else {
                    Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> login: Не удалось пройти авторизацию AD");
                    return false;
                }
            } else {
                $result = Sessions::login($params -> login, $params -> password);
                return $result;
            }
        }





        public static function uploadUserPhoto ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> uploadUserPhoto: Не задан параметр - объект с данными о загружаемом фото");
                return false;
            }

            $userId = $data -> userId;
            $result = Files::is_folder_exists("users".DIRECTORY_SEPARATOR.$userId);
            if (!$result) {
                $result = Files::create_folder("users".DIRECTORY_SEPARATOR.$userId);
                if (!$result) {
                    Errors::push(Errors::ERROR_TYPE_FILE, "Kolenergo -> uploadUserPhoto: Не удалось создать папку 'users/".$userId."'");
                    return false;
                }
            }

            $file = Files::upload("users".DIRECTORY_SEPARATOR.$userId);
            if (!$file) {
                Errors::push(Errors::ERROR_TYPE_FILE, "Koelnergo -> uploadUserPhoto: Не удалось загрузить фото пользователя");
                return false;
            }

            $photo_url = "uploads/users/".$userId."/".$file -> title -> value;
            $result = DBManager::update("kr_users", ["photo_url"], ["'".$photo_url."'"], "id = $userId");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> uploadUserPhoto: Не удалось обновить фото пользователя в БД");
                return false;
            }

            return $file;
        }



    };

?>