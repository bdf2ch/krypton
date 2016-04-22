<?php

    class ErrorManager {

        private static $errors = array();

        public static function getAll () {
            return self::$errors;
        }

        public static function add ($errorType, $errorCode, $errorMessage) {
            $error = new Error($errorType, $errorCode, $errorMessage);
            array_push(ErrorManager::$errors, $error);
        }
    };

?>