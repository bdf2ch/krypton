<?php

    class Users extends Module {

        private $id = "users";
        private static $users = array();
        public static $errorIndex = 4000;


        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            if (!DBManager::is_table_exists_mysql("users")) {
                echo("no users table</br>");
                if (DBManager::create_table_mysql("users")) {
                    if (
                        DBManager::add_column_mysql("users", "name", "varchar(200) NOT NULL") &&
                        DBManager::add_column_mysql("users", "surname", "varchar(200) NOT NULL") &&
                        DBManager::add_column_mysql("users", "fname", "varchar(200)") &&
                        DBManager::add_column_mysql("users", "email", "varchar(200) NOT NULL") &&
                        DBManager::add_column_mysql("users", "phone", "varchar(100)") &&
                        DBManager::add_column_mysql("users", "position", "varchar(500)")
                    ) echo("Установка модуля Krypton.Users выполнена успешно</br>");
                }
                    echo("Не удалось выполнить установку модуля Krypton.Users</br>");
                //else
                //    echo("Не удалось выполнить установку модуля Krypton.Users</br>");
            }
        }


        /**
        * Проверяет, установлен ли модуль в системе
        **/
        public static function isInstalled () {
            if (DBManager::is_table_exists_mysql("users"))
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
        public static function getById ($userId) {
            if ($userId == null) {
                Errors::push(5555);
                return false;
            } else {
                if (gettype($userId) != "integer") {
                    Errors::push(5556);
                    return false;
                } else {
                    $user = DBManager::select_mysql("users", ["*"], "id = $userId LIMIT 1");
                    return $user;
                }
            }
        }

    };

?>