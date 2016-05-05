<?php

    class ErrorManager {

        private static $errors = array();

        public static function getAll () {
            return self::$errors;
        }

        public static function add ($type, $message) {
            $error = new Error($type, $message);
            array_push(ErrorManager::$errors, $error);
            return $error;
        }
    };

?>