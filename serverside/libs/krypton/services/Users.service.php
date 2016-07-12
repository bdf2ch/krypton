<?php

    class Users extends Service {

        //public static $id = "kr_users";
        public static $description = "Users description";
        public static $clientSideExtensionUrl = "modules/app/krypton.app.users.js";
        private static $items = array();
        private static $groups = array();





        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            $result = DBManager::create_table("kr_user_groups");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось создать таблицу с информацией о группах пользователей");
                return false;
            }

            $result = DBManager::add_column("kr_user_groups", "title", "varchar(500) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'title' в таблицу с информацией о группах пользователей");
                return false;
            }

            $result = DBManager::insert_row("kr_user_groups", ["title"], ["'Администраторы'"]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> install: Не удалось добавить данные в таблицу групп пользователей");
               return false;
            }

            $result = DBManager::insert_row("kr_user_groups", ["title"], ["'Редакторы'"]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> install: Не удалось добавить данные в таблицу групп пользователей");
                return false;
            }

            $result = DBManager::create_table("kr_users");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось создать таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "user_group_id", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'user_group_id' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "name", "varchar(200) NOT NULL");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'name' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "surname", "varchar(200) NOT NULL");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'surname' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "fname", "varchar(200) default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'fname' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "email", "varchar(200) NOT NULL");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'email' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "phone", "varchar(100) default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'phone' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "mobile_phone", "varchar(200) default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'mobile_phone' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "position", "varchar(500) default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'position' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "photo_url", "varchar(500) default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'photo_url' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "password", "varchar(60) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'password' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::add_column("kr_users", "is_admin", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'is_admin' в таблицу с информацией о пользователях");
                return false;
            }

            $result = DBManager::insert_row("kr_users", ["user_group_id", "surname", "name", "fname", "is_admin"], [1, "'Admin'", "'Admin'", "'Admin'", 1]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> install: Не удалось добавить данные в таблицу пользователей");
                return false;
            }

            return true;
        }



        /**
        * Проверяет, установлен ли модуль в системе
        **/
        /*
        public static function isInstalled () {
            if (DBManager::is_table_exists("kr_users"))
                return true;
            else
                return false;
        }
        */



        /**
        * Выполняет инициализацию модуля
        **/
        public static function init () {
            $users = DBManager::select("kr_users", ["*"], "''");
            if ($users != false) {
                foreach ($users as $key => $item) {
                    $user = Models::load("User1", false);
                    $user -> fromSource($item);
                    array_push(self::$items, $user);
                }
            }

            API::add("addUserGroup", "Users", "addGroup");
        }





        /**
        * Возвращает массив всех групп пользователей
        **/
        public static function getGroups () {
            return self::$groups;
        }



        public static function addGroup ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> addGroup: Не задан параметр - объект с информацией о добавляемой группе пользователей");
                return false;
            }
        }



        public static function getAll () {
            return self::$items;
        }



        /**
        * Добавляет нового пользователя
        **/
        public static function add ($user) {
            if ($user == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - экземпляр класса User");
                return false;
            }

            if (get_class($user) != "User1") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип парметра - экземпляр класса User");
                    return false;
            }

            $result = DBManager::insert_row(
                "kr_users",
                ["surname", "name", "fname", "position", "email", "phone", "mobile_phone", "password", "is_admin"],
                ["'".$user -> surname -> value."'", "'".$user -> name -> value."'", "'".$user -> fname -> value."'", "'".$user -> position -> value."'", "'".$user -> email -> value."'", "'".$user -> phone -> value."'", "'".$user -> mobile -> value."'", "'".md5($password)."'", intval($user -> isAdmin -> value)]
            );
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> add: Не удалось добавить пользователя");
                return false;
            }

            $id = mysql_insert_id();
            $result = DBManager::select("kr_users", ["*"], "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> add: Не удалось выбрать добавленного пользователя");
                return false;
            }

            $newUser = Models::construct("User1", false);
            $newUser -> fromSource($result[0]);
            array_push(self::$items, $newUser);
            return $newUser;
        }





        /**
        * Возвращает информацию о пользователе по идентификатору пользователя
        * @userId - Идентификатор пользователя
        **/
        public static function getById ($id) {
            if ($id == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getById: Не задан параметр - идентификатор пользователя");
                return false;
            }

            if (gettype($id) != "integer") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getById: Неверно задан тип параметра - идентификатор пользователя");
                return false;
            }

            $result = DBManager::select("kr_users", ["*"], "id = $id LIMIT 1");
            if (!$result)
                return $result;

            $user = Models::construct("User1", false);
            $user -> fromSource($result[0]);
            return $user;
        }





        /**
        * Возвращает информацию о пользователе по email пользователя
        * @email {string} - email пользователя
        **/
        public static function getByEmail ($email) {
            if ($email == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getByEmail: Не задан параметр - email пользователя");
                return false;
            }

            if (gettype($email) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getByEmail: Неверно задан типа параметра - email пользователя");
                return false;
            }

            $result = DBManager::select("kr_users", ["*"], "email = '$email' LIMIT 1");
            if (!$result)
                return $result;

            $user = Models::construct("User1", false);
            $user -> fromSource($result[0]);
            return $user;
        }


        public static function uploadPhoto () {}

    };

?>