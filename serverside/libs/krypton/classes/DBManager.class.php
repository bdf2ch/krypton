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
                                            $encoding = mysql_query("SET NAMES utf8");
                                            if (!$encoding) {
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
        * Создает БД
        * @dbName - Наименование таблицы
        **/
        public static function create_db($dbName) {
            if ($dbName == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> create_db: Не задан параметр - наименование БД");
                return false;
            } else {
                if (gettype($dbName) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> create_db: Неверно задан тип параметра - наименование БД");
                    return false;
                } else {
                    if (self::is_connected() == false) {
                        Errors:push(Errors::ERROR_TYPE_DATABASE, "DB -> create_db: Отсутствует соединение с БД");
                        return false;
                    } else {
                        switch (Krypton::getDBType()) {
                            case Krypton::DB_TYPE_MYSQL:
                                $query = mysql_query("CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8 COLLATE utf8_general_ci", self::$link);
                                if (!$query) {
                                    Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> create_db: ".mysql_errno()." - ".mysql_error());
                                    return false;
                                } else
                                    return true;
                                break;
                            case Krypton::DB_TYPE_ORACLE:
                                break;
                        }
                    }
                }
            }
        }



        /**
        * Выбирает текущую БД
        * @dbName - Наименование БД
        **/
        public static function select_db($dbName) {
            if ($dbName == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_db: Не задан параметр - наименование БД");
                return false;
            } else {
                if (gettype($dbName) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_db: Неверно задан тип параметра - наименование БД");
                    return false;
                } else {
                    if (self::is_connected() == false) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> select_db: Отсутствует соединение с БД");
                        return false;
                    } else {
                        switch (Krypton::getDBType()) {
                            case Krypton::DB_TYPE_MYSQL:
                                $query = mysql_query("USE $dbName", self::$link);
                                if (!$query) {
                                    Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> select_db: ".mysql_errno()." - ".mysql_error());
                                    return false;
                                } else
                                    return true;
                                break;
                            case Krypton::DB_TYPE_ORACLE:
                                break;
                        }
                    }
                }
            }
        }



        /**
        * Создает таблицу в текщей БД
        * @tableName - Наименование таблицы
        **/
        public static function create_table ($tableName) {
            if ($tableName == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> create_table: Не задан параметр - наименование таблицы");
                return false;
            } else {
                if (gettype($tableName) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> crate_table: Неверно задан тип параметра - наименование таблицы");
                    return false;
                } else {
                    if (self::is_connected() == false) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> create_table: Отсутствуете соединение с БД");
                        return false;
                    } else {
                        switch (Krypton::getDBType()) {
                            case Krypton::DB_TYPE_MYSQL:
                                $query = mysql_query("CREATE TABLE IF NOT EXISTS $tableName (id INT(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id))", self::$link);
                                if (!$query) {
                                    Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> create_table: ".mysql_errno()." - ".mysql_error());
                                    return false;
                                } else
                                    return true;
                                break;
                            case Krypton::DB_TYPE_ORACLE:
                                break;
                        }
                    }
                }
            }
        }


        /**
        * Добавляет столбец в таблицу
        * @tableName - Наименование таблицы
        * @columnName - Наименование столбца
        * @columnDefinition - Спецификации столбца
        **/
        public static function add_column ($tableName, $columnName, $columnDefinition) {
            if ($tableName == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_column: Не задан параметр - наименование таблицы");
                return false;
            } else {
                if (gettype($tableName) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_column: Неверно задан тип параметра - наименование таблицы");
                    return false;
                } else {
                    if ($columnName == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_column: Не задан параметр - наименование столбца");
                        return false;
                    } else {
                        if (gettype($columnName) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_column: Неверно задан тип параметра - наименование столбца");
                            return false;
                        } else {
                            if ($columnDefinition == null) {
                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_column: Не задан параметр - спецификации столбца");
                                return false;
                            } else {
                                if (gettype($columnDefinition) != "string") {
                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_column: Неверно задан тип параметра - спецификации столбца");
                                    return false;
                                } else {
                                    if (self::is_connected() == false) {
                                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> add_column: Отсутствует соединение с БД");
                                        return false;
                                    } else {
                                        switch (Krypton::getDBType()) {
                                            case Krypton::DB_TYPE_MYSQL:
                                                $query = mysql_query("ALTER TABLE $tableName ADD COLUMN $columnName $columnDefinition", self::$link);
                                                if (!$query) {
                                                    Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> add_column: ".mysql_errno()." - ".mysql_error());
                                                    return false;
                                                } else
                                                    return true;
                                                break;
                                            case Krypton::DB_TYPE_ORACLE:
                                                break;
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
        * производит проверку на наличие таблицы в БД
        * @tableName - Наименование таблицы
        **/
        public static function is_table_exists ($tableName) {
            if ($tableName == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> is_table_exists: не задан параметр - наименование таблицы");
                return false;
            } else {
                if (gettype($tableName) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> is_table_exists: Неверно задан тип параметра - наименование таблицы");
                    return false;
                } else {
                    if (!self::is_connected()) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> is_table_exists: Отсутствует соединение с БД");
                        return false;
                    } else {
                        switch (Krypton::getDBType()) {
                            case Krypton::DB_TYPE_MYSQL:
                                $query = mysql_query("SELECT * FROM information_schema.tables WHERE table_name = '$tableName' LIMIT 1", DBManager::$link);
                                if (!$query) {
                                    Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> is_table_exists: ".mysql_errno()." - ".mysql_error());
                                    return false;
                                } else
                                    return mysql_num_rows($query) > 0 ? true : false;
                                break;
                            case Krypton::DB_TYPE_ORACLE:
                                break;
                        }
                    }
                }
            }
        }



        /**
        * Выполняет добавление данных в БД
        * @tableName - Наименование таблицы
        * @columns - Массив наименований столбцов таблицы
        * @values - Массив значений
        **/
        public static function insert_row ($tableName, $columns, $values) {
            if ($tableName == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_row: Не задан параметр - наименование таблицы");
                return false;
            } else {
                if (gettype($tableName) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_row: Неверно задан тип параметра - наименование таблицы");
                    return false;
                } else {
                    if ($columns == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_row: Не задан параметр - массив столбцов");
                        return false;
                    } else {
                        if (gettype($columns) != "array") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_row: Неверно задан тип параметра - массив столбцов");
                            return false;
                        } else {
                            if ($values == null) {
                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_row: Не задан параметр - массив значений");
                                return false;
                            } else {
                                if (gettype($values) != "array") {
                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_row: Неверно задан тип параметра - массив значений");
                                    return false;
                                } else {
                                    if (count($columns) != count($values)) {
                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_row: Количество столбцов не соответствует количеству значений");
                                        return false;
                                    } else {
                                        if (self::is_connected() == false) {
                                            Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_row: Отсутствует соединение с БД");
                                            return false;
                                        } else {
                                            switch (Krypton::getDBType()) {
                                                case Krypton::DB_TYPE_MYSQL:
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
                                                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_row: ".mysql_errno()." - ".mysql_error());
                                                        return false;
                                                    } else
                                                        return true;
                                                    break;
                                                case Krypton::DB_TYPE_ORACLE:
                                                    break;
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
        * Выполняет обновление данных в БД
        * @table - Наименование таблицы
        * @columns - Массив наименование столбцов таблицы
        * @values - Массив значений
        * [@condition] - Условие выборки
        **/
        public static function update ($table, $columns, $values, $condition) {
            if ($table == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> update_row: Не задан параметр - наменование таблицы");
                return false;
            } else {
                if (gettype($table) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> update_row: Неверно задан тип параметра - наименование таблицы");
                    return false;
                } else {
                    if ($columns == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> update_row: Не задан параметр - массив столбцов");
                        return false;
                    } else {
                        if (gettype($columns) != "array") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> update_row: Неверно задан тип параметра - массив столбцов");
                            return false;
                        } else {
                            if ($values == null) {
                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> update_row: Не задан параметр - массив значений");
                                return false;
                            } else {
                                if (gettype($values) != "array") {
                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> update_row: Неверно задан тип параметра - массив значений");
                                    return false;
                                } else {
                                    if (count($columns) != count($values)) {
                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> update_row: Количество столбцов не соответствует количеству значений");
                                        return false;
                                    } else {
                                        if ($condition != null && gettype($condition) != "string") {
                                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> update_row: Неверно задан тип параметра - условие");
                                            return false;
                                        } else {
                                            if (DBManager::$link == null) {
                                                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> update_row: Отсутствует соединение с БД");
                                                return false;
                                            } else {
                                                switch (Krypton::getDBType()) {
                                                    case Krypton::DB_TYPE_MYSQL:
                                                        $colsAndVals = "";
                                                        foreach ($columns as $colkey => $column) {
                                                            $colsAndVals .= $column."=".$values[$colkey];
                                                            $colsAndVals .= $colkey < count($columns) - 1 ? ", " : "";
                                                        }
                                                        $colsAndVals .= $condition != null ? " WHERE $condition" : "";
                                                        // echo("</br></br>colsAndVals: ".$colsAndVals."</br>");
                                                        $query = mysql_query("UPDATE $table SET $colsAndVals", DBManager::$link);
                                                        if ($query == false) {
                                                            //Errors::push_generic_mysql();
                                                            Errors::push(Errors::ERROR_TYPE_DATABASE, "DBManager -> update: ".mysql_errno().": ".mysql_error());
                                                            return false;
                                                        } else {
                                                            return true;
                                                        }
                                                        break;
                                                    case Krypton::DB_TYPE_ORACLE:
                                                        break;
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
        * Выполняет выборку данных из БД
        * @table - Наименование таблицы
        * @columns - Массив наименований столбцов таблицы
        * $condition - Условие выборки
        **/
        public static function select ($table, $columns, $condition) {
            if ($table == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select: Не задан параметр - наименование таблицы");
                return false;
            } else {
                if (gettype($table) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select: Неверно задан тип параметра - наименование таблицы");
                    return false;
                } else {
                    if ($columns == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select: Не задан параметр - массив столбцов");
                        return false;
                    } else {
                        if (gettype($columns) != "array") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select: Неверно задан типа параметра - массив столбцов");
                            return false;
                        } else {
                            if ($condition != null && gettype($condition) != "string") {
                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select: Неверно задан тип параметра - условие");
                                return false;
                            } else {
                                if (!self::is_connected()) {
                                    Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> select: Отсутствует соединение с БД");
                                    return false;
                                } else {
                                    switch (Krypton::getDBType()) {
                                        case Krypton::DB_TYPE_MYSQL:
                                            $cols = "";
                                            foreach ($columns as $key => $column) {
                                                $cols .= $column;
                                                $cols .= $key < count($columns) - 1 ? ", " : "";
                                            }
                                            $cond = $condition != null && $condition != "''" ? " WHERE ".$condition : "";
                                            $query = mysql_query("SELECT $cols FROM $table".$cond, self::$link);
                                            if (!$query) {
                                                Errors::push(Errors::ERROR_TYPE_DATABASE, "DBManager -> select: ".mysql_errno()." - ".mysql_error());
                                                return false;
                                            } else {
                                                $result = array();
                                                for ($i = 0; $i < mysql_num_rows($query); $i++) {
                                                    $fetched = mysql_fetch_assoc($query);
                                                    array_push($result, $fetched);
                                                }
                                                return $result;
                                            }
                                            break;
                                        case Krypton::DB_TYPE_ORACLE:
                                            break;
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