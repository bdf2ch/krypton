<?php

    class Errors {
        const ERROR_TYPE_DEFAULT = 1;
        const ERROR_TYPE_ENGINE = 2;
        const ERROR_TYPE_DATABASE = 3;
        const ERROR_TYPE_LDAP = 4;

        public static $errors = array();



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
                            array_push(Errors::$errors, $error);
                            $error -> send();
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
            if ($error == null) {
                die("Errors -> isError: Не задан параметр - объект для проверки");
            } else {
                if (gettype($error) != "object")
                    die("Errors -> isError: Неверно задан тип параметра - объект дял проверки");
                else
                    return get_class($error) == "Error" ? true : false;
            }
        }

    };

?>