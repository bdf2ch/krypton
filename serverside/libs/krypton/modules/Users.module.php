<?php

    class Users extends Module {

        private static $id = "kr_users";
        private static $users = array();


        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            if (!DBManager::is_table_exists_mysql(self::$id)) {
                echo("no users table</br>");
                if (DBManager::create_table_mysql(self::$id)) {
                    if (
                        DBManager::add_column_mysql(self::$id, "name", "varchar(200) NOT NULL") &&
                        DBManager::add_column_mysql(self::$id, "surname", "varchar(200) NOT NULL") &&
                        DBManager::add_column_mysql(self::$id, "fname", "varchar(200)") &&
                        DBManager::add_column_mysql(self::$id, "email", "varchar(200) NOT NULL") &&
                        DBManager::add_column_mysql(self::$id, "phone", "varchar(100)") &&
                        DBManager::add_column_mysql(self::$id, "position", "varchar(500)" &&
                        DBManager::add_column_mysql(self::$id, "is_admin", "int(11) NOT NULL default 0"))
                    )
                        echo("Установка модуля Krypton.Users выполнена успешно</br>");
                    else
                        echo("Не удалось выполнить установку модуля Krypton.Users</br>");
                } else
                    echo("Не удалось выполнить установку модуля Krypton.Users</br>");
            }
        }


        /**
        * Проверяет, установлен ли модуль в системе
        **/
        public static function isInstalled () {
            if (DBManager::is_table_exists_mysql(self::$id))
                return true;
            else
                return false;
        }


        /**
        * Производит инициализацию модуля
        **/
        public function init () {

            Errors::add(new Error (
                Errors::ERROR_TYPE_DEFAULT,
                5555,
                "Users -> getById: Не задан параметр - идентификатор пользователя</br>"
            ));
            Errors::add(new Error (
                Errors::ERROR_TYPE_DEFAULT,
                5556,
                "Users -> getById: Неверно задан тип параметра - идентификатор пользователя</br>"
            ));

        }


        /**
        * Возвращает информацию о пользователе по идентификатору пользователя
        * @userId - Идентификатор пользователя
        **/
        public static function getById ($id) {
            if ($id == null) {
                Errors::push(5555);
                return false;
            } else {
                if (gettype($id) != "integer") {
                    Errors::push(5556);
                    return false;
                } else {
                    $user = DBManager::select_mysql("kr_users", ["*"], "id = $id LIMIT 1");
                    return $user;
                }
            }
        }

    };

?>