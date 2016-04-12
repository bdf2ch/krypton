<?php
    constant DEFAULT_ERROR = 0;
    constant ENGINE_ERROR = 1;
    constant DATABASE_ERROR = 2;

    class Error {
        public $type = DEFAULT_ERROR;
        public $code = 0;
        public $message = "";

        /* Конструктор объекта */
        public function __construct ($errorType, $errorCode, $errorMessage) {
            if ($errorType != null) {
                if ($errorType == DEFAULT_ERROR || $errorType == ENGINE_ERROR || $errorType == DATABASE_ERROR) {
                    $this -> type = $errorType;
                    if ($errorCode != null)
                        $this -> code;
                    if ($errorMessage != null)
                        $this -> $errorMessage;
                }
            }
        }

    };

?>