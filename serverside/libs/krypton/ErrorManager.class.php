<?php

    class ErrorManager {

        private $errors = array();

        public function get () {
            return $this -> errors;
        };

        public function add ($errorType, $errorCode, $errorMessage) {
            $error = new Error($errorType, $errorCode, $errorMessage);
            array_push($this -> errors, $error);
        }
    }

?>