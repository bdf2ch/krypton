<?php

    class Errors extends Service {
        const ERROR_TYPE_DEFAULT = 1;
        const ERROR_TYPE_ENGINE = 2;
        const ERROR_TYPE_DATABASE = 3;
        const ERROR_TYPE_LDAP = 4;

        public static $errors = array();
        private static $lastError;


        public static function install () {}


        public static function init () {}

        /**
        * Добавляет ошибку в стек ошибок
        * @type - Тип ошибки
        * @message - Сообщение ошибки
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
                            self::$lastError = $error;
                            array_push(Errors::$errors, $error);
                            //$error -> send();
                            return $error;
                        }
                    }
                }
            }
        }



        /**
        * Возвращает все ошибки
        **/
        public static function getAll () {
            return self::$errors;
        }



        /**
        * Проверяет, является ли объект экземпляром класса Error
        * @error - объект для проверки
        **/
        public static function isError ($error) {
            if ($error == null && gettype($error) != "boolean" && $error != false) {
                die("Errors -> isError: Не задан параметр - объект для проверки");
            } else {
                if (gettype($error) != "object")
                    return false;
                else
                    return get_class($error) == "Error" ? true : false;
            }
        }


        public static function getLastError () {
            return self::$lastError;
        }

    };

?>