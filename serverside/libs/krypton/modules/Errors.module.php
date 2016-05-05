<?php

    class Errors extends Module {
        const ERROR_TYPE_DEFAULT = 1;
        const ERROR_TYPE_ENGINE = 2;
        const ERROR_TYPE_DATABASE = 3;

        public static $errors = array();
        //private static $items = array();


        public static function install () {
            /* nothing to install */
        }

        public static function isInstalled () {}


        public function init () {
            /*
            self::$items = array(
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    1001,
                    "Errors -> add: Не задан параметр - дескриптор ошибки</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    1002,
                    "Errors -> add: Неверный тип параметра - дескриптор ошибки</br>"
                ),
                new Error(
                    self::ERROR_TYPE_DATABASE,
                    2000,
                    mysql_error()
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2001,
                    "DB: Не задан адрес сервера при подключении к БД</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2002,
                    "DB: Не задано имя пользователя при подключении к БД</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DATABASE,
                    2004,
                    "DB: Отсутствует соединение с БД</br>"
                ),
                new Error (
                    self::ERROR_TYPE_ENGINE,
                    2026,
                    "Settings -> add: Не удалось добавить настройку - модуль Krypton.Settings не установлен</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2027,
                    "DB -> select_mysql: Не задан параметр - наименование таблицы</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2028,
                    "DB -> select_mysql: Неверный тип параметра - наименование таблицы</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2029,
                    "DB -> select_mysql: Не задан параметр - массив столбцов</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2030,
                    "DB -> select_mysql: Неверный тип параметра - массив столбцов</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2032,
                    "DB -> select_mysql: Неверный тип параметра - условие выборки</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2040,
                    "DB -> update_row_mysql: Не задан параметр - наименование таблицы</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2041,
                    "DB -> update_row_mysql: Неверный тип параметра - наименование таблицы</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2042,
                    "DB -> update_row_mysql: Не задан параметр - массив столбцов</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2043,
                    "DB -> update_row_mysql: Неверный тип параметра - массив столбцов</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2044,
                    "DB -> update_row_mysql: Не задан параметр - массив значений</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2045,
                    "DB -> update_row_mysql: Неверный тип параметра - массив значений</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2046,
                    "DB -> update_row_mysql: Несоответствие количества элементов - массив столбцов и массив значений</br>"
                ),
                new Error (
                    self::ERROR_TYPE_DEFAULT,
                    2047,
                    "DB -> update_row_mysql: Неверный тип параметра - условие отбора</br>"
                )
            );
            */


            /*
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1003,
                "Errors -> push: Не указан параметр - код ошибки</br>"
            ));

            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1004,
                "Errors -> push: Неверный тип параметра - код ошибки</br>"
            ));

            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1005,
                "Errors -> add: Ошибка с таким кодом уже существует</br>"
            ));
            */



            /***** UserSession.class *****/

            /*
            self::add(new Error(
                self::ERROR_TYPE_DEFAULT,
                1068,
                "UserSession -> __construct: Не задан параметр - токен</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1069,
                "UserSession -> __construct: Неверный тип параметра - токен</br>"
            ));
            self::add(new Error(
                self::ERROR_TYPE_DEFAULT,
                1100,
                "UserSession -> __construct: Не задан параметр - токен</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1101,
                "UserSession -> __construct: Неверный тип параметра - токен</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1102,
                "UserSession -> __construct: Не задан параметр - начало сессии</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1103,
                "UserSession -> __construct: Неверный тип параметра - начало сессии</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1104,
                "UserSession -> __construct: Не задан параметр - конец сессии</br>"
            ));
            self::add( new Error (
                self::ERROR_TYPE_DEFAULT,
                1105,
                "UserSession -> __construct: Неверный тип параметра - конец сессии</br>"
            ));
            */



            /***** User.class *****/

            /*
            self::add(new Error(
                self::ERROR_TYPE_DEFAULT,
                1116,
                "User -> __construct: Не задан параметр - идентификатор пользователя</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1117,
                "User -> __construct: Неверный тип параметра - идентификатор пользователя</br>"
            ));
            self::add(new Error(
                self::ERROR_TYPE_DEFAULT,
                1106,
                "User -> __construct: Не задан параметр - фамилия пользователя</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1107,
                "User -> __construct: Неверный тип параметра - фамилия пользователя</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1108,
                "User -> __construct: Не задан параметр - имя пользователя</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1109,
                "User -> __construct: Неверный тип параметра - имя пользователя</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1110,
                "User -> __construct: Неверный тип параметра - отчество пользователя</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1111,
                "User -> __construct: Неверный тип параметра - должность пользователя</br>"
            ));
            self::add(new Error(
                self::ERROR_TYPE_DEFAULT,
                1112,
                "User -> __construct: Не задан параметр - email пользователя</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1113,
                "User -> __construct: Неверный тип параметра - email пользователя</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1114,
                "User -> __construct: Неверный тип параметра - телефон пользователя</br>"
            ));
            self::add(new Error (
                self::ERROR_TYPE_DEFAULT,
                1115,
                "User -> __construct: Неверный тип параметра - является ли администратором</br>"
            ));
            */

        }


        /**
        *
        *
        **/
        /*
        public static function add ($error) {
            if ($error == null) {
                self::push(1001);
                return false;
            } else {
                if(get_class($error) != "Error") {
                    self::push(1002);
                    return false;
                } else {
                    $errorFound = false;
                    foreach (self::$items as $key => $err) {
                        if ($err -> code == $error -> code) {
                            self::push(1005);
                            $errorFound = true;
                            return false;
                        }
                    }
                    if (!$errorFound)
                        array_push(self::$items, $error);
                    return true;
                }
            }
        }
        */


        /**
        *
        *
        **/
        public static function push ($type, $message) {
            if ($type == null) {
                $error = new Error(Errors::ERROR_TYPE_DEFAULT, "Errors -> push: Не задан параметр - тип ошибки");
                array_push(Errors::$errors, $error);
                $error -> send();
                return false;
            } else {
                if (gettype($type) != "integer") {
                    $error = new Error(Errors::ERROR_TYPE_DEFAULT, "Errors -> push: Неверно задан тип параметра - тип ошибки");
                    array_push(Errors::$errors, $error);
                    $error -> send();
                    return false;
                } else {
                    if ($message == null) {
                        $error = new Error(Errors::ERROR_TYPE_DEFAULT, "Errors -> push: Не задан параметр - текст ошибки");
                        array_push(Errors::$errors, $error);
                        $error -> send();
                        return false;
                    } else {
                        if (gettype($message) != "string") {
                            $error = new Error(Errors::ERROR_TYPE_DEFAULT, "Errors -> push: Неверно задан тип параметра - текст ошибки");
                            array_push(Errors::$errors, $error);
                            $error -> send();
                            return false;
                        } else {
                            $error = new Error($type, $message);
                            array_push(Errors::$errors, $error);
                            $error -> send();
                            return $error;
                        }
                    }
                }
            }
        }


        /**
        *
        **/
        public static function push_generic_mysql () {
            $error = new Error(
                self::ERROR_TYPE_DATABASE,
                mysql_errno(),
                mysql_error()
            );
            array_push(self::$errors, $error);
        }


        public static function get () {
            return self::$errors;
        }

    };

?>