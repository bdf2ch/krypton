<?php

    class Files extends Service {
        private static $items = array();


        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            $result = DBManager::create_table("kr_files");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось создать таблицу с информацией о файлах");
                return false;
            }

            $result = DBManager::add_column("kr_files", "parent_id", "int(11) default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить столбец 'parent_id' в таблицу с информацией о файлах");
                return false;
            }

            $result = DBManager::add_column("kr_files", "title", "varchar(200) NOT NULL");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить столбец 'title' в таблицу с информацией о файлах");
                return false;
            }

            $result = DBManager::add_column("kr_files", "type", "varchar(200) NOT NULL");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить столбец 'type' в таблицу с информацией о файлах");
                return false;
            }

            $result = DBManager::add_column("kr_files", "size", "int(200) default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить столбец 'size' в таблицу с информацией о файлах");
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
            $files = DBManager::select("kr_files", ["*"], "''");
            if ($files != false) {
                foreach ($files as $key => $item) {
                    $file = Models::load("File", false);
                    $file -> fromSource($item);
                    array_push(self::$items, $file);
                }
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