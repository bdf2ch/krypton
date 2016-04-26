<?php

    class Errors extends Module {
        private $errors = array(
            new Error(
                ERROR_TYPE_DATABASE,
                20005,
                ""
            ),
            new Error (
                ERROR_TYPE_DATABASE,
                20006,
                ""
            )
        );

        public static function install () {
            /* nothing to install */
        }


        public static function init () {
            /* nothing to init */
        }


        public static function add ($errorCode) {
            if ($errorCode = null) {
                echo("Errors -> add: Не указан параметр - код ошибки");
                return false;
            } else {
                if (gettype($errorCode) != "integer") {
                    echo("Errors -> add: Неверный тип параметра - код ошибки");
                    return false;
                } else {
                    foreach (self::$errors as $key => $error) {
                        if ($error -> code == $errorCode) {
                            $error -> send();
                        }
                    }
                }
            }
        }

    };

?>