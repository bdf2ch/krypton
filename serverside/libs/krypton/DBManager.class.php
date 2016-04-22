<?php

    class DBManager {
        public static $link;


        /**
        * Устанавливает соединение с БД MySQL
        * @dbhost - Адрес сервера БД
        * @dbuser - Имя пользователя БД
        * @dbpassword - Пароль пользователя БД
        **/
        public static function connect_mysql ($dbhost, $dbuser, $dbpassword) {
            if ($dbhost != null) {
                if ($dbuser != null) {
                    $link = mysql_connect($dbhost, $dbuser, $dbpassword);
                    if (!$link) {
                        ErrorManager::add (
                            ERROR_TYPE_DATABASE,
                            mysql_errno(),
                            mysql_error()
                        );
                        DBManager::$link = null;
                    } else
                        DBManager::$link = $link;
                } else
                    ErrorManager::add (
                        ERROR_TYPE_DATABASE,
                        ERROR_DB_CONNECTION_NO_USERNAME,
                        "Не указано имя пользователя при подключении к БД"
                    );
            } else
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_CONNECTION_NO_HOST,
                    "Не указан адрес сервера при подключении к БД"
                );
        }



        /**
        * Разрывает соединение с БД MySQL
        **/
        public static function disconnect_mysql () {
            if (DBManager::$link != null) {
                $result = mysql_close(DBManager::$link);
                if (!$result) {
                    ErrorManager::add (
                        ERROR_TYPE_DATABASE,
                        mysql_errno(),
                        mysql_error()
                    );
                } else
                    DBManager::$link = null;
            } else
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_NO_CONNECTION,
                    "Не удалось разорвать соединение с БД - соединение отсутствует"
                );
        }



        /**
        * Создает таблицу в БД MySQL
        * @dbName - Наименование таблицы
        **/
        public static function create_db_mysql($dbName) {
            if ($dbName != null) {
                if (gettype($dbName) == "string") {
                    if (DBManager::$link != null) {
                        $query = mysql_query("CREATE DATABASE IF NOT EXISTS $dbName", DBManager::$link);
                        if (!$query)
                            ErrorManager::add (
                                ERROR_TYPE_DATABASE,
                                mysql_errno(),
                                mysql_error()
                            );
                    } else
                        ErrorManager::add (
                            ERROR_TYPE_DATABASE,
                            ERROR_DB_NO_CONNECTION,
                            "Не удалось создать БД - соединение отсутствует с БД"
                        );
                } else
                    ErrorManager::add (
                        ERROR_TYPE_DATABASE,
                        ERROR_DB_CREATE_WRONG_TITLE_TYPE,
                        "Задан неверный тип параметра при создании БД - наименование БД"
                    );
            } else
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_CREATE_NO_TITLE,
                    "Не указан параметр при создании БД - наименование БД"
                );
        }



        /**
        * Выбирает текущую БД в БД MySQL
        * @dbName - Наименование БД
        **/
        public static function select_db_mysql($dbName) {
            if ($dbName != null) {
                if (gettype($dbName) == "string") {
                    if (DBManager::$link != null) {
                        $query = mysql_query("USE $dbName", DBManager::$link);
                        if (!$query)
                            ErrorManager::add (
                                ERROR_TYPE_DATABASE,
                                mysql_errno(),
                                mysql_error()
                            );
                    } else
                        ErrorManager::add (
                            ERROR_TYPE_DATABASE,
                            ERROR_DB_NO_CONNECTION,
                            "Не удалось разорвать соединение с БД - соединение отсутствует"
                        );
                } else
                    ErrorManager::add (
                        ERROR_TYPE_DATABASE,
                        ERROR_DB_SELECT_DB_WRONG_TITLE_TYPE,
                        "Задан неверный тип параметра при выборе БД - наименование БД"
                    );
            } else
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_SELECT_DB_NO_TITLE,
                    "Не указан параметр при выборе БД - наименование БД"
                );
        }



        /**
        * Создает таблицу в текщей БД в БД MySQL
        * @tableName - Наименование таблицы
        **/
        public static function create_table_mysql ($tableName) {
            if ($tableName != null) {
                if (gettype($tableName) == "string") {
                    if (DBManager::$link != null) {
                        $query = mysql_query("CREATE TABLE IF NOT EXISTS $tableName (id INT(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id))", DBManager::$link);
                        if (!$query)
                            ErrorManager::add (
                                ERROR_TYPE_DATABASE,
                                mysql_errno(),
                                mysql_error()
                            );
                    } else
                        ErrorManager::add (
                            ERROR_TYPE_DATABASE,
                            ERROR_DB_NO_CONNECTION,
                            "Не удалось разорвать соединение с БД - соединение отсутствует"
                        );
                } else
                    ErrorManager::add (
                        ERROR_TYPE_DATABASE,
                        ERROR_DB_TABLE_CREATE_WRONG_TITLE_TYPE,
                        "Задан неверный тип параметра при создании таблицы БД - наименование таблицы"
                    );
            } else
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_TABLE_CREATE_NO_TITLE,
                    "Не указан параметр при создании таблицы - наименование таблицы"
                );
        }



        public static function add_column_mysql ($tableName, $columnName, $columnDefinition) {
            if ($tableName != null) {
                if (gettype($tableName) == "string") {
                    if ($columnName != null) {
                        if (gettype($columnName == "string")) {

                            if ($columnDefinition != null) {
                                if (gettype($columnDefinition) == "string") {
                                    if (DBManager::$link != null) {
                                        $query = mysql_query("ALTER TABLE $tableName ADD COLUMN $columnName $columnDefinition", DBManager::$link);
                                        if (!$query)
                                            ErrorManager::add (
                                                ERROR_TYPE_DATABASE,
                                                mysql_errno(),
                                                mysql_error()
                                            );
                                    } else
                                        ErrorManager::add (
                                            ERROR_TYPE_DATABASE,
                                            ERROR_DB_NO_CONNECTION,
                                            "Не удалось добавить столбец в таблицу - отсутствует соединение с БД"
                                        );
                                } else
                                    ErrorManager::add (
                                        ERROR_TYPE_DATABASE,
                                        ERROR_DB_COLUMN_ADD_WRONG_PROPERTIES_TYPE,
                                        "Задан неверный тип параметра при создании таблицы - наименование столбца"
                                    );
                            } else
                                ErrorManager::add (
                                    ERROR_TYPE_DATABASE,
                                    ERROR_DB_COLUMN_ADD_NO_PROPERTIES,
                                    "Не указан параметр при добавлении столбца в таблицу - параметры столбца"
                                );
                        } else
                            ErrorManager::add (
                                ERROR_TYPE_DATABASE,
                                ERROR_DB_COLUMN_ADD_WRONG_COLUMN_TYPE,
                                "Задан неверный тип параметра при создании таблицы - наименование столбца"
                            );
                    } else
                        ErrorManager::add (
                            ERROR_TYPE_DATABASE,
                            ERROR_DB_COLUMN_ADD_NO_COLUMN_TITLE,
                            "Не указан параметр при добавлении столбца в таблицу - наименование столбца"
                        );
                } else
                    ErrorManager::add (
                        ERROR_TYPE_DATABASE,
                        ERROR_DB_COLUMN_ADD_WRONG_TITLE_TYPE,
                        "Задан неверный тип параметра при создании таблицы - наименование таблицы"
                    );
            } else
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_COLUMN_ADD_NO_TABLE_TITLE,
                    "Не указан параметр при добавлении столбца в таблицу - наименование таблицы"
                );
        }

    };

?>