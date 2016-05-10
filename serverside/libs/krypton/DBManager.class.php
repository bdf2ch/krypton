<?php

    class DBManager {
        public static $link;


        /**
        * Устанавливает соединение с БД
        * @dbhost - Адрес сервера БД
        * @dbuser - Имя пользователя БД
        * @dbpassword - Пароль пользователя БД
        **/
        public static function connect ($dbhost, $dbuser, $dbpassword) {
            if ($dbhost == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> connect: Не задан параметр - адрес сервера БД");
                return false;
            } else {
                if (gettype($dbhost) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> connect: Неверно задан тип параметра - адрес сервера БД");
                    return false;
                } else {
                    if ($dbuser == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> connect: Не задан параметр - пользователь БД");
                        return false;
                    } else {
                        if (gettype($dbuser) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> connect: Неверно задан тип параметра - пользователь БД");
                            return false;
                        } else {
                            if ($dbpassword != null && gettype($dbpassword) != "string") {
                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> connect: Неверно задан типа парметра - пароль пользователя БД");
                                return false;
                            } else {
                                switch (Krypton::getDBType()) {
                                    case Krypton::DB_TYPE_MYSQL:
                                        $link = mysql_connect($dbhost, $dbuser, $dbpassword);
                                        if (!$link) {
                                            Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> connect: ".mysql_errno()." - ".mysql_error());
                                            self::$link = null;
                                            return false;
                                        } else {
                                            self::$link = $link;
                                            $encoding = mysql_query("SET NAMES utf-8");
                                            if (!encoding) {
                                                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> connect: Не удалось установить кодировку соединения с БД - ".mysql_errno()." - ".mysql_error());
                                            }
                                            return $link;
                                        }
                                        break;
                                    case Krypton::DB_TYPE_ORACLE:
                                        $link = oci_connect($dbuser, $dbpassword, $dbhost, "AL32UTF8");
                                        if (!$link) {
                                            $error = oci_error();
                                            $message = $error != false ? $error["code"]." - ".$error["message"]." - ".$error["sqltext"] : "";
                                            Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> connect: ".$message);
                                            self::$link = null;
                                            return false;
                                        } else {
                                            self::$link = $link;
                                            return $link;
                                        }
                                        break;
                                }
                            }
                        }
                    }
                }
                /*
                if ($dbuser == null) {
                    Errors::push(2002);
                    return false;
                } else {
                    switch (Krypton::getDBType()) {
                        case Krypton::DB_TYPE_MYSQL:
                            $link = mysql_connect($dbhost, $dbuser, $dbpassword);
                            if (!$link) {
                                Errors::push_generic_mysql();
                                self::$link = null;
                                return false;
                            } else {
                                self::$link = $link;
                                return true;
                            }
                            break;
                        case Krypton::DB_TYPE_ORACLE:
                            $link = oci_connect($dbuser, $dbpassword, $dbhost);
                            break;
                    }

                }
                */
            }
        }



        /**
        * Проверяет, установлено ли соединение с БД
        **/
        public static function is_connected () {
            $result = self::$link != null ? true : false;
            return $result;
        }


        /**
        * Разрывает соединение с БД
        **/
        public static function disconnect () {
            if (!self::is_connected()) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> disconnect: Не удалось закрыть соединение с БД - соединение с БД отсутствует");
                return false;
            } else {
                switch (Krypton::getDBType()) {
                    case Krypton::DB_TYPE_MYSQL:
                        $result = mysql_close(self::$link);
                        if (!$result) {
                            Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> disconnect: ".mysql_errno()." - ".mysql_error());
                            return false;
                        } else {
                            self::$link = null;
                            return true;
                        }
                        break;
                    case KRYPTON::DB_TYPE_ORACLE:
                        $result = oci_close(self::$link);
                        if (!result) {
                            $error = oci_error();
                            $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                            Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> disconnect: ".$message);
                            return false;
                        } else {
                            self::$link = null;
                            return true;
                        }
                        break;
                }
            }
        }


        /**
        * Устанавливает кодировку соединения с БД MySQL
        * @title - Наименование кодировки
        **/
        /*
        public static function set_encoding ($title) {
            if ($title != null) {
                if (gettype($title) == "string") {
                    if (DBManager::$link != null) {
                        switch (Krypton::getDBType()) {
                            case Krypton::DB_TYPE_MYSQL:
                                $query = mysql_query("SET NAMES $title", DBManager::$link);
                                if (!$query) {
                                    ErrorManager::add (
                                        ERROR_TYPE_DATABASE,
                                        mysql_errno(),
                                        mysql_error()
                                    ) -> send();
                                    return false;
                                } else
                                return true;
                                break;
                            case Krypton::DB_TYPE_ORACLE:
                                break;
                        }
                    } else {
                        ErrorManager::add (
                            ERROR_TYPE_DATABASE,
                            ERROR_DB_NO_CONNECTION,
                            "Не удалось установить кодировку соединения - соединение отсутствует"
                        ) -> send();
                        return false;
                    }
                } else {
                    ErrorManager::add (
                        ERROR_TYPE_DATABASE,
                        ERROR_DB_ENCODING_WRONG_TITLE_TYPE,
                        "Задан неверный тип параметра при выборе БД - наименование БД"
                    ) -> send();
                    return false;
                }
            } else {
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_ENCODING_NO_TITLE,
                    "Не указан параметр при установке кодировки соединения"
                ) -> send();
                return false;
            }
        }
        */


        /**
        * Создает таблицу в БД MySQL
        * @dbName - Наименование таблицы
        **/
        public static function create_db_mysql($dbName) {
            if ($dbName != null) {
                if (gettype($dbName) == "string") {
                    if (DBManager::$link != null) {
                        $query = mysql_query("CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8 COLLATE utf8_general_ci", DBManager::$link);
                        if (!$query) {
                            ErrorManager::add (
                                ERROR_TYPE_DATABASE,
                                mysql_errno(),
                                mysql_error()
                            );
                            return false;
                        } else
                            return true;
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
                        if (!$query) {
                            ErrorManager::add (
                                Errors::ERROR_TYPE_DATABASE,
                                mysql_errno(),
                                mysql_error()
                            ) -> send();
                            return false;
                        } else
                            return true;
                    } else {
                        ErrorManager::add (
                            Errors::ERROR_TYPE_DATABASE,
                            ERROR_DB_NO_CONNECTION,
                            "Не удалось разорвать соединение с БД - соединение отсутствует"
                        ) -> send();
                        return false;
                    }
                } else {
                    ErrorManager::add (
                        Errors::ERROR_TYPE_DATABASE,
                        ERROR_DB_SELECT_DB_WRONG_TITLE_TYPE,
                        "Задан неверный тип параметра при выборе БД - наименование БД"
                    ) -> send();
                    return false;
                }
            } else {
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_SELECT_DB_NO_TITLE,
                    "Не указан параметр при выборе БД - наименование БД"
                ) -> send();
                return false;
            }
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
                        if (!$query) {
                            ErrorManager::add (
                                ERROR_TYPE_DATABASE,
                                mysql_errno(),
                                mysql_error()
                            ) -> send();
                            return false;
                        } else
                            return true;
                    } else {
                        ErrorManager::add (
                            ERROR_TYPE_DATABASE,
                            ERROR_DB_NO_CONNECTION,
                            "Не удалось разорвать соединение с БД - соединение отсутствует"
                        ) -> send();
                        return false;
                    }
                } else {
                    ErrorManager::add (
                        ERROR_TYPE_DATABASE,
                        ERROR_DB_TABLE_CREATE_WRONG_TITLE_TYPE,
                        "Задан неверный тип параметра при создании таблицы БД - наименование таблицы"
                    ) -> send();
                    return false;
                }
            } else {
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_TABLE_CREATE_NO_TITLE,
                    "Не указан параметр при создании таблицы - наименование таблицы"
                ) -> send();
                return false;
            }
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
                                        if (!$query) {
                                            ErrorManager::add (
                                                ERROR_TYPE_DATABASE,
                                                mysql_errno(),
                                                mysql_error()
                                            ) -> send();
                                            return false;
                                        } else
                                            return true;
                                    } else {
                                        ErrorManager::add (
                                            ERROR_TYPE_DATABASE,
                                            ERROR_DB_NO_CONNECTION,
                                            "Не удалось добавить столбец в таблицу - отсутствует соединение с БД"
                                        ) -> send();
                                        return false;
                                    }
                                } else {
                                    ErrorManager::add (
                                        ERROR_TYPE_DATABASE,
                                        ERROR_DB_COLUMN_ADD_WRONG_PROPERTIES_TYPE,
                                        "Задан неверный тип параметра при создании таблицы - наименование столбца"
                                    ) -> send();
                                    return false;
                                }
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



        public static function is_table_exists_mysql ($tableName) {
            if ($tableName != null) {
                if (gettype($tableName) == "string") {
                    if (DBManager::$link != null) {
                        $query = mysql_query("SELECT * FROM information_schema.tables WHERE table_name = '$tableName' LIMIT 1", DBManager::$link);
                        if (!$query) {
                            ErrorManager::add (
                                ERROR_TYPE_DATABASE,
                                mysql_errno(),
                                mysql_error()
                            ) -> send();
                            return false;
                        } else {
                            if (mysql_num_rows($query) > 0)
                                return true;
                            else
                                return false;
                        }
                    } else {
                        ErrorManager::add (
                            ERROR_TYPE_DATABASE,
                            ERROR_DB_NO_CONNECTION,
                            "Не удалось проверить наличие таблицы - отсутствует соединение с БД"
                        ) -> send();
                        return false;
                    }
                } else {
                    ErrorManager::add (
                        ERROR_TYPE_DATABASE,
                        ERROR_DB_TABLE_CHECK_WRONG_TITLE_TYPE,
                        "Задан неверный тип параметра при проверке существования таблицы - наименование таблицы"
                    ) -> send();
                    return false;
                }
            } else {
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_TABLE_CHECK_NO_TABLE_TITLE,
                    "Не указан параметр при проверке существования таблицы - наименование таблицы"
                ) -> send();
                return false;
            }
        }



        public static function insert_row_mysql ($tableName, $columns, $values) {
            if ($tableName != null) {
                if (gettype($tableName == "string")) {
                    if ($columns != null) {
                        if (gettype($columns) == "array") {
                            if ($values != null) {
                                if (gettype($values) == "array") {
                                    if (count($columns) == count($values)) {
                                        if (DBManager::$link != null) {
                                            $cols = "";
                                            foreach ($columns as $key => $column) {
                                                $cols .= $column;
                                                $cols .= $key < count($columns) - 1 ? ", " : "";
                                            }

                                            $vals = "";
                                            foreach ($values as $key => $val) {
                                                $vals .= $val;
                                                $vals .= $key < count($values) - 1 ? ", " : "";
                                            }

                                            $query = mysql_query("INSERT INTO $tableName ($cols) VALUES ($vals)", DBManager::$link);
                                            if (!$query) {
                                                /*
                                                ErrorManager::add (
                                                    ERROR_TYPE_DATABASE,
                                                    mysql_errno(),
                                                    mysql_error()
                                                ) -> send();
                                                */

                                                return false;
                                            } else
                                                return true;
                                        } else
                                            /*
                                            ErrorManager::add (
                                                ERROR_TYPE_DATABASE,
                                                ERROR_DB_NO_CONNECTION,
                                                "Не удалось проверить наличие таблицы - отсутствует соединение с БД"
                                            ) -> send();
                                            */
                                            return false;
                                    } else
                                        /*
                                        ErrorManager::add (
                                            ERROR_TYPE_DATABASE,
                                            ERROR_DB_DATA_INSERT_COLUMNS_VALUES_MISMATCH,
                                            "Не совпадает количество столбцов и значений при добавлении данных в БД"
                                        ) -> send();
                                        */
                                        return false;
                                } else
                                    /*
                                    ErrorManager::add (
                                        ERROR_TYPE_DATABASE,
                                        ERROR_DB_DATA_INSERT_WRONG_VALUES_TYPE,
                                        "Задан неверный тип параметра при добавлении данных - массив значений"
                                    ) -> send();
                                    */
                                    return false;
                            } else
                                /*
                                ErrorManager::add (
                                    ERROR_TYPE_DATABASE,
                                    ERROR_DB_DATA_INSERT_NO_VALUES,
                                    "Не указан параметр при добавлении данных - массив значений"
                                ) -> send();
                                */
                                return false;
                        } else
                            /*
                            ErrorManager::add (
                                ERROR_TYPE_DATABASE,
                                ERROR_DB_DATA_INSERT_WRONG_COLUMNS_TYPE,
                                "Задан неверный тип параметра при добавлении данных - массив столбцов"
                            ) -> send();
                            */
                            return false;
                    } else
                        /*
                        ErrorManager::add (
                            ERROR_TYPE_DATABASE,
                            ERROR_DB_DATA_INSERT_NO_COLUMNS,
                            "Не указан параметр при добавлении данных - массив столбцов"
                        ) -> send();
                        */
                        return false;
                } else
                    /*
                    ErrorManager::add (
                        ERROR_TYPE_DATABASE,
                        ERROR_DB_TABLE_CHECK_WRONG_TITLE_TYPE,
                        "Задан неверный тип параметра при добавлении данных - наименование таблицы"
                    ) -> send();
                    */
                    return false;

            } else
                /*
                ErrorManager::add (
                    ERROR_TYPE_DATABASE,
                    ERROR_DB_DATA_INSERT_NO_TABLE_TITLE,
                    "Не указан параметр при добавлении данных - наименование таблицы"
                ) -> send();
                */
                return false;
        }


        /**
        * Выполняет обновление данных в БД MySQL
        * @table - Наименование таблицы
        * @columns - Массив наименование столбцов таблицы
        * @values - Массив значений
        * [@condition] - Условие выборки
        **/
        public static function update_row_mysql ($table, $columns, $values, $condition) {
            if ($table == null) {
                Errors::push(2040);
                return false;
            } else {
                if (gettype($table) != "string") {
                    Errors::push(2041);
                    return false;
                } else {
                    if ($columns == null) {
                        Errors::push(2042);
                        return false;
                    } else {
                        if (gettype($columns) != "array") {
                            Errors::push(2043);
                            return false;
                        } else {
                            if ($values == null) {
                                Errors::push(2044);
                                return false;
                            } else {
                                if (gettype($values) != "array") {
                                    Errors::push(2045);
                                    return false;
                                } else {
                                    if (count($columns) != count($values)) {
                                        Errors::push(2046);
                                        return false;
                                    } else {
                                        if ($condition != null && gettype($condition) != "string") {
                                            Errors::push(2047);
                                            return false;
                                        } else {
                                            if (DBManager::$link == null) {
                                                Errors::push(2004);
                                                return false;
                                            } else {
                                                $colsAndVals = "";
                                                foreach ($columns as $colkey => $column) {
                                                    $colsAndVals .= $column."=".$values[$colkey];
                                                    $colsAndVals .= $colkey < count($columns) - 1 ? ", " : "";
                                                }
                                                $colsAndVals .= $condition != null ? " WHERE $condition" : "";
                                                echo("</br></br>colsAndVals: ".$colsAndVals."</br>");
                                                $query = mysql_query("UPDATE $table SET $colsAndVals", DBManager::$link);
                                                if ($query == false) {
                                                    Errors::push_generic_mysql();
                                                    return false;
                                                } else {
                                                    return true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        /**
        * Выполняет выборку данных из БД MySQL
        * @table - Наименование таблицы
        * @columns - Массив наименований столбцов таблицы
        * $condition - Условие выборки
        **/
        public static function select_mysql ($table, $columns, $condition) {
            if ($table == null) {
                Errors::push(2027);
                return false;
            } else {
                if (gettype($table) != "string") {
                    Errors::push(2028);
                    return false;
                } else {
                    if ($columns == null) {
                        Errors::push(2029);
                        return false;
                    } else {
                        if (gettype($columns) != "array") {
                            Errors::push(2030);
                            return false;
                        } else {
                            if ($condition != null && gettype($condition) != "string") {
                                Errors::push(2032);
                                return false;
                            } else {
                                if (!self::is_connected()) {
                                    Errors::push(2004);
                                    return false;
                                } else {
                                    $cols = "";
                                    foreach ($columns as $key => $column) {
                                        $cols .= $column;
                                        $cols .= $key < count($columns) - 1 ? ", " : "";
                                    }
                                    $cond = $condition != null && $condition != "''" ? " WHERE ".$condition : "";
                                    $query = mysql_query("SELECT $cols FROM $table".$cond, self::$link);
                                    if (!$query) {
                                        Errors::push_generic_mysql();
                                        return false;
                                    } else {
                                        $result = array();
                                        for ($i = 0; $i < mysql_num_rows($query); $i++) {
                                            $fetched = mysql_fetch_assoc($query);
                                            array_push($result, $fetched);
                                        }
                                        return $result;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

    };

?>