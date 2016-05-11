<?php

    class Errors extends Module {
        const ERROR_TYPE_DEFAULT = 1;
        const ERROR_TYPE_ENGINE = 2;
        const ERROR_TYPE_DATABASE = 3;
        const ERROR_TYPE_LDAP = 4;

        public static $errors = array();
        //private static $items = array();


        public static function install () {
            /* nothing to install */
        }

        public static function isInstalled () {}


        public function init () {

        }



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