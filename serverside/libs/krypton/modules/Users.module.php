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
                        DBManager::add_column_mysql(self::$id, "position", "varchar(500)") &&
                        DBManager::add_column_mysql(self::$id, "password", "varchar(60) NOT NULL default ''") &&
                        DBManager::add_column_mysql(self::$id, "is_admin", "int(11) NOT NULL default 0")
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
            self::add("'ТЕСТ'", "'ТЕСТОВИЧ'", "'ТЕСТОВ'", "'ТЕЛЕФОНИСТ'", "'testov@kolenergo.ru'" ,"'666-777'", "'testov'", "'qwerty'", true);
        }



        /**
        * Добавляет нового пользователя
        **/
        public static function add ($name, $fname, $surname, $position, $email, $phone, $login, $password, $isAdmin) {
            if ($name == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - имя пользователя");
                return false;
            } else {
                if (gettype($name) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - имя пользователя");
                    return false;
                } else {
                    if ($fname == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - отчество пользователя");
                        return false;
                    } else {
                        if (gettype($fname) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - отчество пользователя");
                            return false;
                        } else {
                            if ($surname == null) {
                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - фамилия пользователя");
                                return false;
                            } else {
                                if (gettype($surname) != "string") {
                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - фамилия пользователя");
                                    return false;
                                } else {
                                    if ($position == null) {
                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - должность пользователя");
                                        return false;
                                    } else {
                                        if (gettype($position) != "string") {
                                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - должность пользователя");
                                            return false;
                                        } else {
                                            if ($email == null) {
                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - email пользователя");
                                                return false;
                                            } else {
                                                if (gettype($email) != "string") {
                                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - email пользователя");
                                                    return false;
                                                } else {
                                                    if ($phone == null) {
                                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - телефон пользователя");
                                                        return false;
                                                    } else {
                                                        if (gettype($phone) != "string") {
                                                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - телефон пользователя");
                                                            return false;
                                                        } else {
                                                            if ($login == null) {
                                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - логин пользователя");
                                                                return false;
                                                            } else {
                                                                if (gettype($login) != "string") {
                                                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - логин пользователя");
                                                                    return false;
                                                                } else {
                                                                    if ($password == null) {
                                                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - пароль пользователя");
                                                                        return false;
                                                                    } else {
                                                                        if (gettype($password) != "string") {
                                                                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - пароль пользователя");
                                                                            return false;
                                                                        } else {
                                                                            if ($isAdmin == null && $isAdmin !== false) {
                                                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - является ли пользователь администратором");
                                                                                return false;
                                                                            } else {
                                                                                if (gettype($isAdmin) != "boolean") {
                                                                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - является ли пользователь администратором");
                                                                                    return false;
                                                                                } else {
                                                                                    if (self::isInstalled() == true) {
                                                                                        $result = DBManager::insert_row_mysql(
                                                                                            self::$id,
                                                                                            ["name", "surname", "fname", "email", "phone", "position", "login", "password", "is_admin"],
                                                                                            [$name, $surname, $fname, $email, $phone, $position, $login, "'".md5($password)."'", intval($isAdmin)]
                                                                                        );
                                                                                        if ($result == false) {
                                                                                            Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> add: ".mysql_errno()." - ".mysql_error());
                                                                                            return false;
                                                                                        } else {
                                                                                            $id = mysql_insert_id();
                                                                                            return $id != null && $id != 0 ? $id : false;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
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


        private static function generate_password ($length) {

        }

    };

?>