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
                                        //$con1 = oci_connect('purchases', 'PURCHASES', '192.168.50.52/ORCLWORK', 'AL32UTF8');
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
                                return true;
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
                                return true;
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
                                 $query = "CREATE TABLE $tableName (ID INT NOT NULL, PRIMARY KEY (ID))";
                                 $statement = oci_parse(self::$link, $query);

                                 $result = oci_execute($statement, OCI_DEFAULT);
                                 if (!$result) {
                                     $error = oci_error();
                                     $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                                     Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> create_table: ".$message);
                                     return false;
                                 } else
                                    return true;
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
                                                $query = "ALTER TABLE $tableName ADD ($columnName $columnDefinition)";
                                                $statement = oci_parse(self::$link, $query);

                                                $result = oci_execute($statement, OCI_DEFAULT);
                                                if (!$result) {
                                                    $error = oci_error();
                                                    $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                                                    Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> add_column: ".$message);
                                                    return false;
                                                } else
                                                    return true;
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
        * Производит проверку на наличие таблицы в БД
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
        * Производит проверку на наличие столбца в таблице
        * @table - наименование таблицы
        * @column - наименование столбца
        **/
        public static function is_column_exists ($table, $column) {
            if ($table == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "DBManager -> is_column_exists: Не задан параметр - наименование таблицы");
            else {
                if (gettype($table) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "DBManager -> is_column_exists: Неверно задан тип параметра - наименование таблицы");
            }

            if ($column == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "DBManager -> is_column_exists: Не задан параметр - наименование столбца");
            else {
                if (gettype($column) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "DBManager -> is_column_exists: Неверно задан тип параметра - наименование столбца");
            }

            if (!self::is_connected())
                return Errors::push(Errors::ERROR_TYPE_DATABASE, "DBManager -> is_column_exists: Отсутствует соединение с БД");
            else {
                switch (Krypton::getDBType()) {
                    case Krypton::DB_TYPE_MYSQL:
                        $query = mysql_query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'krypton' AND TABLE_NAME = '$table' AND COLUMN_NAME = '$column'", self::$link);
                        if (!$query)
                            return Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> is_table_exists: ".mysql_errno()." - ".mysql_error());
                        else
                            return mysql_num_rows($query) > 0 ? true : false;
                        break;
                    case Krypton::DB_TYPE_ORACLE:
                        break;
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

                                            switch (Krypton::getDBType()) {
                                                case Krypton::DB_TYPE_MYSQL:

                                                    $query = mysql_query("INSERT INTO $tableName ($cols) VALUES ($vals)", DBManager::$link);
                                                    if (!$query) {
                                                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_row: ".mysql_errno()." - ".mysql_error());
                                                        return false;
                                                    } else
                                                        return true;

                                                    break;

                                                case Krypton::DB_TYPE_ORACLE:

                                                     $query = "INSERT INTO $tableName ($cols) VALUES ($vals)";
                                                     $statement = oci_parse(self::$link, $query);

                                                     $result = oci_execute($statement, OCI_DEFAULT);
                                                     if (!$result) {
                                                         $error = oci_error();
                                                         $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                                                         Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_row: ".$message);
                                                         return false;
                                                     } else {
                                                        $result = oci_commit(self::$link);
                                                        if (!$result) {
                                                            $error = oci_error();
                                                            $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                                                            Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_row: ".$message);
                                                            return false;
                                                        }

                                                        return true;
                                                     }

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

                                                $colsAndVals = "";
                                                foreach ($columns as $colkey => $column) {
                                                    $colsAndVals .= $column."=".$values[$colkey];
                                                    $colsAndVals .= $colkey < count($columns) - 1 ? ", " : "";
                                                }
                                                $colsAndVals .= $condition != null ? " WHERE $condition" : "";

                                                switch (Krypton::getDBType()) {
                                                    case Krypton::DB_TYPE_MYSQL:
                                                        $query = mysql_query("UPDATE $table SET $colsAndVals", DBManager::$link);
                                                        if (!$query) {
                                                            Errors::push(Errors::ERROR_TYPE_DATABASE, "DBManager -> update: ".mysql_errno().": ".mysql_error());
                                                            return false;
                                                        } else
                                                            return true;

                                                        break;

                                                    case Krypton::DB_TYPE_ORACLE:

                                                        $query = "UPDATE $table SET $colsAndVals";
                                                        $statement = oci_parse(self::$link, $query);

                                                        $result = oci_execute($statement, OCI_DEFAULT);
                                                        if (!$result) {
                                                            $error = oci_error();
                                                            $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                                                            Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_row: ".$message);
                                                            return false;
                                                        } else {
                                                            $result = oci_commit(self::$link);
                                                            if (!$result) {
                                                                $error = oci_error();
                                                                $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                                                                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_row: ".$message);
                                                                return false;
                                                            } else
                                                                return true;
                                                        }

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
        * Выполняет удаление данных из БД
        * @table {string} - наименование таблицы
        * @condition {string} - условие
        **/
        public static function delete ($table, $condition) {
            if ($table == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> delete: Не задан параметр - наименование таблицы");
                return false;
            }

            if (gettype($table) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> delete: Неверно задан тип параметра - наименование таблицы");
                return false;
            }

            if ($condition == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> delete: Не задан параметр - условие");
                return false;
            }

            if (gettype($condition) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> delete: Неверно задан тип параметра - условие");
                return false;
            }

            if (self::is_connected() == false) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> select: Отсутствует соединение с БД");
                return false;
            }

            switch (Krypton::getDBType()) {

                case Krypton::DB_TYPE_MYSQL:
                    $query = mysql_query("DELETE FROM $table WHERE $condition", self::$link);
                    if (!$query) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> delete: ".mysql_errno()." - ".mysql_error());
                        return false;
                    } else
                        return true;
                    break;

                case Krypton::DB_TYPE_ORACLE:
                    $query = "DELETE FROM $table WHERE $condition";
                    $statement = oci_parse(self::$link, $query);
                    $result = oci_execute($statement, OCI_DEFAULT);
                    if (!$result) {
                        $error = oci_error();
                        $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_row: ".$message);
                        return false;
                    } else {
                        $result = oci_commit(self::$link);
                        if (!$result) {
                            $error = oci_error();
                            $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                            Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_row: ".$message);
                            return false;
                        } else
                            return true;
                    }
                    break;
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
            }

            if (gettype($table) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select: Неверно задан тип параметра - наименование таблицы");
                return false;
            }

            if ($columns == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select: Не задан параметр - массив столбцов");
                return false;
            }

            if (gettype($columns) != "array") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select: Неверно задан типа параметра - массив столбцов");
                return false;
            }

            if ($condition == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select: Не задан параметр - условие выборки");
                return false;
            }

            if (gettype($condition) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select: Неверно задан тип параметра - условие выборки");
                return false;
            }

            if (!self::is_connected()) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> select: Отсутствует соединение с БД");
                return false;
            }

            $cols = "";
            //$cond = $condition != "''" ? " WHERE ".$condition : "";
            //$cond .= $condition != "''" && strpos($condition, "!NOWHERE!") != false ? " ".$condition : " WHERE ".$condition;
            $cond = "";
            if ($condition != "''") {
                $start = strpos($condition, "!NOWHERE!");
                //echo("start = ".var_dump($start)."</br>");
                if (gettype($start) != "boolean")
                    $cond .= " ".substr($condition, ($start + 9));
                else
                    $cond .= " WHERE ".$condition;
            } else
                $cond = "";

            //echo("condition = ".$cond."</br>");
            foreach ($columns as $key => $column) {
                $cols .= $column;
                $cols .= $key < count($columns) - 1 ? ", " : "";
            }


            switch (Krypton::getDBType()) {
                case Krypton::DB_TYPE_MYSQL:

                    $query = mysql_query("SELECT $cols FROM $table".$cond, self::$link);
                    if (!$query) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> select: ".mysql_errno()." - ".mysql_error());
                        return false;
                    }

                    $result = array();
                    for ($i = 0; $i < mysql_num_rows($query); $i++) {
                        $fetched = mysql_fetch_assoc($query);
                        array_push($result, $fetched);
                    }

                    return $result;
                    break;

                case Krypton::DB_TYPE_ORACLE:

                    $query = "SELECT $cols FROM $table".$cond;
                    $statement = oci_parse(self::$link, $query);

                    $result = oci_execute($statement, OCI_DEFAULT);
                    if (!$result) {
                        $error = oci_error();
                        $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> select: ".$message);
                        return false;
                    }

                    $result = array();
                    $rows = oci_fetch_all($statement, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

                    return $result;
                    break;
            }


            /*
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
            */
        }



        public static function insert_file ($table, $column, $title) {
            if ($table == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_file: Не задан параметр - наименование таблицы");
                return false;
            }

            if (gettype($table) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_file: Неверно задан тип параметра - наименование таблицы");
                return false;
            }

            if ($column == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_file: Не задан параметр - наименование столбца");
                return false;
            }

            if (gettype($column) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_file: Неверно задан тип параметра - наименование столбца");
                return false;
            }

            if ($title == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_file: Не задан параметр - имя загружаемого файла");
                return false;
            }

            if (gettype($title) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> insert_file: Неверно задан тип параметра - имя загружаемого файла");
                return false;
            }

            if (!self::is_table_exists($table)) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_file: Таблица '".$table."' не найдена");
                return false;
            }

            if (!self::is_column_exists($table, $column)) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_file: Столбец '".$column."' в таблице '".$table."' не найден");
                return false;
            }

            if (!self::is_connected()) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> insert_file: Отсутствует соединение с БД");
                return false;
            }




        }





        /**
        * Добавялет последовательность в БД Oracle
        * @title {string} - наименование последовательности
        * @start {integer} - начальное значение последовательности
        * @step {integer} - шаг последовательности
        **/
        public static function add_sequence ($title, $start, $step) {
            if ($title == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_sequence: Не задан параметр - наименование последовательности");
                return false;
            }

            if (gettype($title) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_sequence: Неверно задан тип параметр - наименование последовательности");
                return false;
            }

            if ($start == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_sequence: Не задан парметр - начальное значение последовательности");
                return false;
            }

            if (gettype($start) != "integer") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_sequence: Неверно задан тип парметра - начальное значение последовательности");
                return false;
            }

            if ($step == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_sequence: Не задан парметр - шаг последовательности");
                return false;
            }

            if (gettype($step) != "integer") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_sequence: Неверно задан тип парметра - шаг последовательности");
                return false;
            }

            if (Krypton::getDbType() !== Krypton::DB_TYPE_ORACLE) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> add_sequence: Последовательности доступны только при использовании СУБД Oracle");
                return false;
            }

            if (!self::is_connected()) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> add_sequence: Отсутствует соединение с БД");
                return false;
            }

            $query = "CREATE SEQUENCE $title START WITH $start INCREMENT BY $step NOCACHE NOCYCLE";
            $statement = oci_parse(self::$link, $query);

            $result = oci_execute($statement, OCI_DEFAULT);
            if (!$result) {
                $error = oci_error();
                $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> add_sequence: ".$message);
                return false;
            } else
                return true;
        }




        public static function sequence_next ($title) {
            if ($title == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> sequence_next: Не задан параметр - наименование последовательности");
                return false;
            }

            if (gettype($title) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> sequence_next: Неверно задан тип параметра - наименование последовательности");
                return false;
            }

            if (Krypton::getDBType() != Krypton::DB_TYPE_ORACLE) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> sequence_next: Последовательности доступны только при использовании СУБД Oracle");
                return false;
            }

            $query = "SELECT $title.nextval FROM DUAL";
            $statement = oci_parse(self::$link, $query);

            $result = oci_execute($statement, OCI_DEFAULT);
            if (!$result) {
                $error = oci_error();
                $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> sequence_next: ".$message);
                return false;
            } else {
                $res = oci_fetch_assoc($statement);
                $seq = $res["NEXTVAL"];
                return intval($seq);
            }
        }



        public static function select_connect_by_prior ($table, $columns, $condition, $key, $parentKey, $start) {
            if ($table == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Не задан параметр - наименование таблицы");
                return false;
            }

            if (gettype($table) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Неверно задан тип параметра - наименование таблицы");
                return false;
            }

            if ($columns == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Не задан параметр - массив столбцов");
                return false;
            }

            if (gettype($columns) != "array") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Неверно задан тип параметра - массив столбцов");
                return false;
            }

            if ($condition == null && $condition != "") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Не задан параметр - условие выборки");
                return false;
            }

            if (gettype($condition) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Неверно задан тип параметра - условие выборки");
                return false;
            }

            if ($key == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Не задан параметр - поле связи");
                return false;
            }

            if (gettype($key) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Неверно задан тип параметра - поле связи");
                return false;
            }

            if ($parentKey == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Не задан параметр - поле связи родителя");
                return false;
            }

            if (gettype($parentKey) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Неверно задан тип параметра - поле связи родителя");
                return false;
            }

            //if ($start != null) {
            //    Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> select_connect_by_prior: Неверно задан тип параметра - значение ключа связи корневого элемента");
            //    return false;
            //}

            if (Krypton::getDBType() !== Krypton::DB_TYPE_ORACLE) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> select_connect_by_prior: Выборка иерархических данных доступна только для СУБД Oracle");
                return false;
            }

            if (!self::is_connected()) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> select_connect_by_prior: Отсутствует соединение с БД");
                return false;
            }

            $cols = "";
            foreach ($columns as $index => $col) {
                $cols .= $col;
                $cols .= $index < sizeof($columns) - 1 ? ", " : "";
            }

            $query = "SELECT $cols FROM $table ";
            $query .= $condition != '' ? "WHERE ".$condition." " : '';
            $query .= $start != null && $start != "" ? "START WITH $key = $start " : "";
            $query .= "CONNECT BY PRIOR $key = $parentKey";
            //echo($query);
            $statement = oci_parse(self::$link, $query);

            $result = oci_execute($statement, OCI_DEFAULT);
            if (!$result) {
                $error = oci_error();
                $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> select_connect_by_prior: ".$message);
                return false;
            } else {
                $tree = array();
                $rows = oci_fetch_all($statement, $tree, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                return $tree;
            }
        }



        public static function count ($table) {
            if ($table == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> count: Не задан параметр - наименование таблицы");
                return false;
            }

            if (gettype($table) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> count: Неверно задан тип параметра - наименование таблицы");
                return false;
            }

            if (!self::is_connected()) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> count: Отсутствует соединение с БД");
                return false;
            }

            switch (Krypton::getDBType()) {
                case Krypton::DB_TYPE_MYSQL:
                    $result = mysql_query("SELECT COUNT(*) AS TOTAL FROM $table");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> count: ".mysql_errno()." - ".mysql_error());
                        return false;
                    }

                    $result = mysql_fetch_assoc($query);
                    return intval($result["TOTAL"]);
                    break;
                case Krypton::DB_TYPE_ORACLE:
                    $query = "SELECT COUNT(*) AS TOTAL FROM $table";
                    $statement = oci_parse(self::$link, $query);

                    $result = oci_execute($statement, OCI_DEFAULT);
                    if (!$result) {
                        $error = oci_error();
                        $message = $error != false ? $error["code"]." - ".$error["message"]: "";
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> count: ".$message);
                        return false;
                    } else {
                        $res = oci_fetch_assoc($statement);
                        $total = $res["TOTAL"];
                        return intval($total);
                    }
                    break;
            }
        }



        public static function sql ($sql) {
            if ($sql == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> sql: Не задан параметр - выражение sql");
                return false;
            }

            if (gettype($sql) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "DB -> sql: Неверно задан тип параметра - выражение sql");
                return false;
            }

            if (!self::is_connected()) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "DB -> sql: Отсутствует соединение с БД");
                return false;
            }

            switch (Krypton::getDBType()) {
                case Krypton::DB_TYPE_MYSQL:
                    break;
                case Krypton::DB_TYPE_ORACLE:
                    break;
            }
        }

    };

?>