<?php
    //const ERROR_TYPE_DEFAULT = 0;
    //const ERROR_TYPE_ENGINE = 1;
    //const ERROR_TYPE_DATABASE = 2;

    const ERROR_APP_WRONG_TITLE_TYPE = 000001;
    const ERROR_APP_WRONG_DESCRIPTION_TYPE = 000002;
    const ERROR_MODULE_LOAD_NO_TITLE = 000003;
    const ERROR_MODULE_LOAD_WRONG_TITLE_TYPE = 000004;
    const ERROR_ENGINE_GET_PROPERTY_NO_TITLE = 0005;
    const ERROR_ENGINE_GET_PROPERTY_WRONG_TITLE_TYPE = 0006;

    const ERROR_DB_CONNECTION_NO_HOST = 2001;
    const ERROR_DB_CONNECTION_NO_USERNAME = 2002;
    const ERROR_DB_CONNECTION_NO_PASSWORD = 2003;
    const ERROR_DB_NO_CONNECTION = 2004;
    const ERROR_DB_CREATE_NO_TITLE = 2005;
    const ERROR_DB_CREATE_WRONG_TITLE_TYPE = 2006;
    const ERROR_DB_TABLE_CREATE_NO_TITLE = 2007;
    const ERROR_DB_TABLE_CREATE_WRONG_TITLE_TYPE = 2008;
    const ERROR_DB_SELECT_DB_NO_TITLE = 2009;
    const ERROR_DB_SELECT_DB_WRONG_TITLE_TYPE = 2010;
    const ERROR_DB_COLUMN_ADD_NO_TABLE_TITLE = 2011;
    const ERROR_DB_COLUMN_ADD_WRONG_TITLE_TYPE = 2012;
    const ERROR_DB_COLUMN_ADD_NO_COLUMN_TITLE = 2013;
    const ERROR_DB_COLUMN_ADD_WRONG_COLUMN_TYPE = 2014;
    const ERROR_DB_COLUMN_ADD_NO_PROPERTIES = 2015;
    const ERROR_DB_COLUMN_ADD_WRONG_PROPERTIES_TYPE = 2016;
    const ERROR_DB_TABLE_CHECK_NO_TABLE_TITLE = 2017;
    const ERROR_DB_DATA_INSERT_NO_TABLE_TITLE = 2018;
    const ERROR_DB_DATA_INSERT_NO_COLUMNS = 2019;
    const ERROR_DB_DATA_INSERT_WRONG_COLUMNS_TYPE = 2020;
    const ERROR_DB_DATA_INSERT_NO_VALUES = 2021;
    const ERROR_DB_DATA_INSERT_WRONG_VALUES_TYPE = 2022;
    const ERROR_DB_DATA_INSERT_COLUMNS_VALUES_MISMATCH = 2023;
    const ERROR_DB_ENCODING_NO_TITLE = 2024;
    const ERROR_DB_ENCODING_WRONG_TITLE_TYPE = 2025;
    const ERROR_DB_SETTINGS_TABLE_DOES_NOT_EXISTS = 2026;
    const ERROR_DB_SELECT_NO_TABLE_TITLE = 2027;
    const ERROR_DB_SELECT_WRONG_TITLE_TYPE = 2028;
    const ERROR_DB_SELECT_NO_COLUMNS = 2029;
    const ERROR_DB_SELECT_WRONG_COLUMNS_TYPE = 2030;
    const ERROR_DB_SELECT_NO_CONDITION = 2031;
    const ERROR_DB_SELECT_WRONG_CONDITION_TYPE = 2032;



    class Error {
        public $type = Errors::ERROR_TYPE_DEFAULT;
        //public $code = 0;
        public $message = "";
        public $timestamp = 0;

        /* Конструктор объекта */
        public function __construct ($errorType, $errorMessage) {
            if ($errorType != null) {
                if ($errorType == Errors::ERROR_TYPE_DEFAULT || $errorType == Errors::ERROR_TYPE_ENGINE || $errorType == Errors::ERROR_TYPE_DATABASE) {
                    $this -> type = $errorType;
                    $this -> timestamp = time();
                    //if ($errorCode != null)
                    //    $this -> code = $errorCode;
                    if ($errorMessage != null)
                        $this -> message = $errorMessage;
                } else
                    die("Error -> __construct: Тип ошибки указан неверно");
            } else
                die("Error -> __construct: Не указан тип ошибки");
        }


        public function send () {
            var_dump($this);
        }

    };

?>