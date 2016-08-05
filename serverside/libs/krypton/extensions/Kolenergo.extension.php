<?php

    class Kolenergo implements ExtensionInterface {
        public static $id;
        //public static $description;
        //public static $url;
        public static $installed;
        public static $enabled = true;

        public static $title = "Колэнерго";
        public static $description = "Модуль портала Колэнерго";
        public static $url = "modules/app/krypton.app.kolenergo.js";

        public static $organizations = array();
        public static $departments = array();
        public static $divisions = array();



        public function __construct () {
            //parent::__construct();
            $class = get_called_class();
            self::$id = $class;
        }

        public function load () {
                    $class = get_called_class();

                    if (!Krypton::$app -> isExtensionLoaded($class)) {
                        switch (Krypton::getDBType()) {
                            case Krypton::DB_TYPE_MYSQL:

                                $result = DBManager::insert_row(
                                    "kr_app_extensions",
                                    ["extension_id", "extension_title", "extension_description", "extension_url", "enabled"],
                                    ["'".$class::$id."'", "'".$class::$title."'", "'".$class::$description."'", "'".$class::$url."'", 1]
                                );
                                if (!$result) {
                                    Errors::push(Errors::ERROR_TYPE_ENGINE, "Extension -> load: Не удалось подключить расширение '".$class."' к приложению");
                                    return false;
                                }

                                break;
                            case Krypton::DB_TYPE_ORACLE:
                                $id = DBManager::sequence_next("seq_extensions");
                                if (!$id) {
                                    Errors::push(Errors::ERROR_TYPE_ENGINE, "ExtensionInterface -> load: Не удалось получить следующее значение последовательности 'seq_extensions'");
                                    return false;
                                }

                                $result = DBManager::insert_row(
                                    "kr_app_extensions",
                                    ["id", "extension_id", "extension_title", "extension_description", "extension_url", "enabled"],
                                    [$id, "'".$class::$id."'", "'".$class::$title."'", "'".$class::$description."'", "'".$class::$url."'", 1]
                                );
                                if (!$result) {
                                    Errors::push(Errors::ERROR_TYPE_ENGINE, "Extension -> load: Не удалось подключить расширение '".$class."' к приложению");
                                    return false;
                                }

                                break;
                        }



                        $extension = Models::load("Extension", false);
                        $extension -> id -> value = $class::$id;
                        $extension -> title -> value = $class::$title;
                        $extension -> description -> value = $class::$description;
                        $extension -> url -> value = $class::$url;
                        array_push(Krypton::$app -> extensions, $extension);
                    } else
                        Extensions::get($class) -> init();

                    return true;
                }


                public function getUrl () {
                    return self::$url != "" ? self::$url : false;
                }


                public function setEnabled ($flag) {
                    if ($flag == null)
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "ExtensionInterface -> setEnabled");
                }



        public function install () {

            $result = DBManager::create_table("organizations");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось создать таблицу 'organizations'");
                return false;
            }

            $result = DBManager::create_table("departments");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось создать таблицу 'departments'");
                return false;
            }

            $result = DBManager::create_table("divisions");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось создать таблицу 'divisions'");
                return false;
            }

            switch (Krypton::getDBType()) {

                case Krypton::DB_TYPE_MYSQL:

                    $result = DBManager::add_column("kr_users", "department_id", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'department_id' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "division_id", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'division_id' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "organization_id", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'organization_id' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("organizations", "title", "varchar(500) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу 'organizations'");
                        return false;
                    }

                    $result = DBManager::add_column("departments", "title", "varchar(500) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу 'departments'");
                        return false;
                    }

                    $result = DBManager::insert_row("departments", ["title"], ["'Аппарат управления'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу 'departments'");
                        return false;
                    }

                    $result = DBManager::insert_row("departments", ["title"], ["'Северные электрические сети'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу 'departments'");
                        return false;
                    }

                    $result = DBManager::insert_row("departments", ["title"], ["'Центральные электрические сети'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу 'departments'");
                        return false;
                    }

                    $result = DBManager::add_column("divisions", "title", "varchar(500) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу 'divisions'");
                        return false;
                    }

                    $result = DBManager::add_column("divisions", "organization_id", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'organization_id' в таблицу 'divisions'");
                        return false;
                    }

                    $result = DBManager::add_column("divisions", "department_id", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'department_id' в таблицу 'divisions'");
                        return false;
                    }

                    $result = DBManager::add_column("divisions", "parent_id", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'parent_id' в таблицу 'divisions'");
                        return false;
                    }

                    break;

                case Krypton::DB_TYPE_ORACLE:

                    $result = DBManager::add_sequence("seq_organizations", 1, 1);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить последовательность 'seq_organizations'");
                        return false;
                    }

                    $result = DBManager::add_sequence("seq_departments", 1, 1);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить последовательность 'seq_departments'");
                        return false;
                    }

                    $result = DBManager::add_sequence("seq_divisions", 1, 1);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить последовательность 'seq_divisions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "department_id", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'department_id' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "division_id", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'division_id' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "organization_id", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'organization_id' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("organizations", "title", "VARCHAR2(500) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу 'organizations'");
                        return false;
                    }

                    $result = DBManager::add_column("departments", "title", "VARCHAR2(500) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу 'departments'");
                        return false;
                    }

                    $id = DBManager::sequence_next("seq_departments");
                    if (!$id) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удвлось получить следующее значение последовательности 'seq_departments'");
                        return false;
                    }

                    $result = DBManager::insert_row("departments", ["id", "title"], [$id, "'Аппарат управления'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу 'departments'");
                        return false;
                    }

                    $id = DBManager::sequence_next("seq_departments");
                    if (!$id) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удвлось получить следующее значение последовательности 'seq_departments'");
                        return false;
                    }

                    $result = DBManager::insert_row("departments", ["id", "title"], [$id, "'Северные электрические сети'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу 'departments'");
                        return false;
                    }

                    $id = DBManager::sequence_next("seq_departments");
                    if (!$id) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удвлось получить следующее значение последовательности 'seq_departments'");
                        return false;
                    }

                    $result = DBManager::insert_row("departments", ["id", "title"], [$id, "'Центральные электрические сети'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Kolenergo -> install: Не удалось добавить данные в таблицу 'departments'");
                        return false;
                    }

                    $result = DBManager::add_column("divisions", "title", "VARCHAR2(500) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'title' в таблицу 'divisions'");
                        return false;
                    }

                    $result = DBManager::add_column("divisions", "organization_id", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'organization_id' в таблицу 'divisions'");
                        return false;
                    }

                    $result = DBManager::add_column("divisions", "department_id", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'department_id' в таблицу 'divisions'");
                        return false;
                    }

                    $result = DBManager::add_column("divisions", "parent_id", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'parent_id' в таблицу 'divisions'");
                        return false;
                    }

                    break;
            }



            /*
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
            */

            return true;
        }



        public function isInstalled () {
            //$check = DBManager::is_column_exists("kr_users", "department_id");
            //return $check;
            return true;
        }



        public function init () {



            Krypton::$app -> addJavaScript("modules/app/krypton.app.kolenergo.js");

            //$departmentIdProperty = Models::extend("User1", "departmentId", new Field(array( "source" => "department_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));;
            //$divisionIdProperty = Models::extend("User1", "divisionId", new Field(array( "source" => "division_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            //$organizationIdProperty = Models::extend("User1", "organizationId", new Field(array( "source" => "organization_id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            //$ldapEnabledProperty = Models::extend("User1", "ldapEnabled", new Field(array( "source" => "ldap_enabled", "type" => Krypton::DATA_TYPE_BOOLEAN, "value" => true, "defaultValue" => true )));

            //if (!defined("ENGINE_ADMIN_MODE"))

            //if (Errors::isError($departmentIdProperty) && Errors::isError($divisionIdProperty))
            //    return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить свойства в класс User");

            API::add("test", "Kolenergo", "getDepartments");
            API::add("addOrganization", "Kolenergo", "addOrganization");
            API::add("editOrganization", "Kolenergo", "editOrganization");
            API::add("deleteOrganization", "Kolenergo", "deleteOrganization");
            API::add("addDivision", "Kolenergo", "addDivision");
            API::add("editDivision", "Kolenergo", "editDivision");
            API::add("deleteDivision", "Kolenergo", "deleteDivision");
            API::add("uploadUserPhoto", "Kolenergo", "uploadUserPhoto");
            API::add("login", "Kolenergo", "login");
            API::add("getUsersByDivisionId", "Kolenergo", "getUsersByDivisionId");




            if (!self::isInstalled())
                self::install();
            else {
                $organizations = DBManager::select("organizations", ["*"], "''");
                //echo("</br>ORGS = </br>");
                //var_dump($organizations);
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


            $title = $data -> title;
            $organization = Models::construct("Organization", false);

            switch (Krypton::getDBType()) {

                case Krypton::DB_TYPE_MYSQL:

                    $result = DBManager::insert_row("organizations", ["title"], ["'$title'"]);
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

                    $organization -> fromSource($result[0]);
                    break;

                case Krypton::DB_TYPE_ORACLE:

                    $id = DBManager::sequence_next("seq_organizations");
                    if (!$id) {
                        Errors:push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addOrganization: Не удалось получить следующее значенеи последовательности 'seq_organizations'");
                        return false;
                    }

                    $result = DBManager::insert_row("organizations", ["id", "title"], [$id, "'$title'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addOrganization: Не удалось добавить организацию");
                        return false;
                    }

                    $result = DBManager::select("organizations", ["*"], "id = $id");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addOrganization: Не удалось выбрать добавленную организацию");
                        return false;
                    }

                    $organization -> fromSource($result[0]);
                    break;
            }

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
            $title = $data -> title;

            $result = DBManager::update("organizations", ["title"], ["'$title'"], "id = $id");
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
        public static function getDepartments () {
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

            $organizationId = $data -> organizationId;
            $parentId = $data -> parentId;
            $title = $data -> title;
            $division = Models::construct("Division", false);

            switch (Krypton::getDBType()) {

                case Krypton::DB_TYPE_MYSQL:
                    $result = DBManager::insert_row("divisions", ["organization_id", "parent_id", "title"], [$organizationId, $parentId, "'$title'"]);
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

                    $division -> fromSource($result[0]);
                    break;

                case Krypton::DB_TYPE_ORACLE:
                    $id = DBManager::sequence_next("seq_divisions");
                    if (!$id) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addDivision: Не удалось получить следующее значенеи последовательности 'seq_divisions'");
                        return false;
                    }

                    $result = DBManager::insert_row("divisions", ["id", "organization_id", "parent_id", "title"], [$id, $organizationId, $parentId, "'$title'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addDivision: Не удалось добавить отдел");
                        return false;
                    }

                    $result = DBManager::select("divisions", ["*"], "id = $id");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addDivision: Не удалось выбрать добавленный отдел");
                        return false;
                    }

                    $division -> fromSource($result[0]);
                    break;
            }

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

            $id = $data -> id;
            $parentId = $data -> parentId;
            $title = $data -> title;
            $departmentId = $data -> departmentId;
            $division = Models::construct("Division", false);

            $result = DBManager::update("divisions", ["TITLE", "PARENT_ID", "DEPARTMENT_ID"], ["'$title'", $parentId, $departmentId], "ID = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> editDivision: Не удалось обновить информацию об отделе");
                return false;
            }

            $result = DBManager::select("divisions", ["*"], "ID = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> addDivision: Не удалось выбрать измененный отдел");
                return false;
            }

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





        public static function getUsersByDivisionId ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Kolenergo -> getChildrenByDivisionId: Не задан параметр - объект с информацией о выбранном отделе");
                return false;
            }

            $id = $data -> id;
            $result = new stdClass();
            $result -> divisions = array();
            $result -> users = array();

            $divisions = DBManager::select("kr_divisions", ["*"], "START WITH ID = $id CONNECT BY PRIOR ID = PARENT_ID");
            if (!$divisions) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> getChildrenByDivisionId: Не удалось выбрать дочерние отделы относительно отдела с идентификатором ".$id);
                return false;
            }

            $ids = "";
            for ($x = 0; $x < sizeof($divisions); $x++) {
                $division = Models::construct("Division", false);
                $division -> fromSource($divisions[$x]);
                array_push($result -> divisions, $division);
                $ids += $division -> id -> value;
                $ids += $x < sizeof($divisions) - 1 ? ", " : "";
            }
            $ids = "(".$ids.")";

            $users = DBManager::select("kr_users", ["*"], "DIVISION_ID IN ".$ids);
            if (!$users) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> getUsersByDivisionId: Не удалось выбрать пользователей по идентификаторам отделов");
                return false;
            }

            for ($i = 0; $i < sizeof($users); $i++) {
                $user = Models::construct("User1", false);
                $user -> fromSource($users[$i]);
                array_push($result -> users, $user);
            }

            return $result;
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

            $photo_url = "/uploads/users/".$userId."/".$file -> title -> value;
            $result = DBManager::update("kr_users", ["photo_url"], ["'".$photo_url."'"], "id = $userId");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> uploadUserPhoto: Не удалось обновить фото пользователя в БД");
                return false;
            }

            return $file;
        }



    };

?>