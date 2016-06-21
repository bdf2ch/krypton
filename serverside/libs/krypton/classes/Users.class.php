<?php

    class Users {

        //public static $id = "kr_users";
        public static $description = "Users description";
        public static $clientSideExtensionUrl = "modules/app/krypton.app.users.js";
        private static $items = array();


        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            if (!DBManager::is_table_exists("kr_users")) {
                if (DBManager::create_table("kr_users")) {
                    if (
                        DBManager::add_column("kr_users", "name", "varchar(200) NOT NULL") &&
                        DBManager::add_column("kr_users", "surname", "varchar(200) NOT NULL") &&
                        DBManager::add_column("kr_users", "fname", "varchar(200) default ''") &&
                        DBManager::add_column("kr_users", "email", "varchar(200) NOT NULL") &&
                        DBManager::add_column("kr_users", "phone", "varchar(100) default ''") &&
                        DBManager::add_column("kr_users", "mobile_phone", "varchar(200) default ''") &&
                        DBManager::add_column("kr_users", "position", "varchar(500) default ''") &&
                        DBManager::add_column("kr_users", "password", "varchar(60) NOT NULL default ''") &&
                        DBManager::add_column("kr_users", "is_admin", "int(11) NOT NULL default 0")
                    ) {
                        return true;
                    } else {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось создать структуру таблицы с информацией о пользователях");
                        return false;
                    }
                } else {
                    Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось создать таблицу с информацией о пользователях");
                    return false;
                }
            }
        }



        /**
        * Проверяет, установлен ли модуль в системе
        **/
        /*
        public static function isInstalled () {
            if (DBManager::is_table_exists("kr_users"))
                return true;
            else
                return false;
        }
        */



        /**
        * Производит инициализацию модуля
        **/
        public static function init () {
            //echo("users init</br>");
            //self::add("ЛОЛ", "ЛОЛОВИЧ", "ЛОЛОВ", "ЛОЛОВИК", "lolov@kolenergo.ru", "111-333", "lolka", true);
            $users = DBManager::select("kr_users", ["*"], "''");
            if ($users != false) {
                foreach ($users as $key => $item) {
                    /*
                    $user = new User (
                        intval($item["id"]),
                        $item["surname"],
                        $item["name"],
                        $item["fname"],
                        $item["position"],
                        $item["email"],
                        $item["phone"],
                        boolval($item["is_admin"])
                    );
                    */
                    /*
                    $user = new User(array(
                        "id" => intval($item["id"]),
                        "surname" => $item["surname"],
                        "name" => $item["name"],
                        "fname" => $item["fname"],
                        "position" => $item["position"],
                        "email" => $item["email"],
                        "phone" => $item["phone"],
                        "mobile" => $item["mobile_phone"],
                        "isAdmin" => boolval($item["is_admin"])
                    ));
                    */

                    //$user = Models::construct("User1", false);
                    //$user -> fromSource($item);

                    $user = Models::load("User1", false);
                    $user -> fromSource($item);
                    //var_dump($user);

                    array_push(self::$items, $user);
                }
            }
        }



        public static function getAll () {
            return self::$items;
        }



        /**
        * Добавляет нового пользователя
        **/
        public static function add ($user) {
            if ($user == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - экземпляр класса User");
            else {
                if (gettype($user) != "object" && get_class($user) != "User")
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип парметра - экземпляр класса User");
                else {
                    $result = DBManager::insert_row(
                        "kr_users",
                        ["name", "surname", "fname", "email", "phone", "mobile_phone", "position", "password", "is_admin"],
                        ["'".$user -> name."'", "'".$user -> surname."'", "'".$user -> fname."'", "'".$user -> email."'", "'".$user -> phone."'", "'".$user -> mobile."'", "'".$user -> position."'", "'".md5($password)."'", intval($user -> isAdmin)]
                    );
                    if ($result == false)
                        return Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> add: ".mysql_errno()." - ".mysql_error());
                    else {
                        $id = mysql_insert_id();
                        $user -> id = intval($id);
                        return $id != null && $id != 0 ? $id : false;
                    }
                    array_push(self::$items, $user);
                }
            }
        }




        /*
        public static function add ($name, $fname, $surname, $position, $email, $phone, $password, $isAdmin) {
            if ($name == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - имя пользователя");
                return false;
            } else {
                if (gettype($name) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - имя пользователя");
                    return false;
                } else {
                    if ($fname == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - отчество пользователя");
                        return false;
                    } else {
                        if (gettype($fname) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - отчество пользователя");
                            return false;
                        } else {
                            if ($surname == null) {
                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - фамилия пользователя");
                                return false;
                            } else {
                                if (gettype($surname) != "string") {
                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - фамилия пользователя");
                                    return false;
                                } else {
                                    //if ($position == null) {
                                    //    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - должность пользователя");
                                    //    return false;
                                    //} else {
                                        if ($position != null && gettype($position) != "string") {
                                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - должность пользователя");
                                            return false;
                                        } else {
                                            if ($email == null) {
                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - email пользователя");
                                                return false;
                                            } else {
                                                if (gettype($email) != "string") {
                                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - email пользователя");
                                                    return false;
                                                } else {
                                                    if ($phone == null) {
                                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - телефон пользователя");
                                                        return false;
                                                    } else {
                                                        if (gettype($phone) != "string") {
                                                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - телефон пользователя");
                                                            return false;
                                                        } else {
                                                            if ($password == null) {
                                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - пароль пользователя");
                                                                return false;
                                                            } else {
                                                                if (gettype($password) != "string") {
                                                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - пароль пользователя");
                                                                    return false;
                                                                } else {
                                                                    if ($isAdmin == null && $isAdmin !== false) {
                                                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - является ли пользователь администратором");
                                                                        return false;
                                                                    } else {
                                                                        if (gettype($isAdmin) != "boolean") {
                                                                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип параметра - является ли пользователь администратором");
                                                                            return false;
                                                                        } else {
                                                                            //if (self::isInstalled() == true) {
                                                                                $result = DBManager::insert_row(
                                                                                    "kr_users",
                                                                                    ["name", "surname", "fname", "email", "phone", "position", "password", "is_admin"],
                                                                                    ["'".$name."'", "'".$surname."'", "'".$fname."'", "'".$email."'", "'".$phone."'", "'".$position."'", "'".md5($password)."'", intval($isAdmin)]
                                                                                );
                                                                                if ($result == false) {
                                                                                    Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> add: ".mysql_errno()." - ".mysql_error());
                                                                                    return false;
                                                                                } else {
                                                                                    $id = mysql_insert_id();
                                                                                    return $id != null && $id != 0 ? $id : false;
                                                                                }
                                                                            //}
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    //}
                                }
                            }
                        }
                    }
                }
            }
        }
        */



        /**
        * Возвращает информацию о пользователе по идентификатору пользователя
        * @userId - Идентификатор пользователя
        **/
        public static function getById ($id) {
            if ($id == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getById: Не задан параметр - идентификатор пользователя");
                return false;
            } else {
                if (gettype($id) != "integer") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getById: Неверно задан тип параметра - идентификатор пользователя");
                    return false;
                } else {
                    $u = DBManager::select("kr_users", ["*"], "id = $id LIMIT 1");
                    if ($u) {
                        /*
                        $user = new User(
                            intval($u[0]["id"]),
                            $u[0]["surname"],
                            $u[0]["name"],
                            $u[0]["fname"],
                            $u[0]["position"],
                            $u[0]["email"],
                            $u[0]["phone"],
                            boolval($u[0]["is_admin"])
                        );
                        */

                        /*
                        $user = new User(array(
                            "id" => intval($u[0]["id"]),
                            "surname" => $u[0]["surname"],
                            "name" => $u[0]["name"],
                            "fname" => $u[0]["fname"],
                            "position" => $u[0]["position"],
                            "email" => $u[0]["email"],
                            "phone" => $u[0]["phone"],
                            "mobile" => $u[0]["mobile_phone"],
                            "isAdmin" => boolval($u[0]["is_admin"])
                        ));
                        */

                        $user = Models::construct("User1", false);
                        $user -> fromSource($u[0]);

                        return $user;
                    } else
                        return false;
                }
            }
        }



        /**
        * Возвращает информацию о пользователе по email пользователя
        * @email - Email пользователя
        **/
        public static function getByEmail ($email) {
            if ($email == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getByEmail: Не задан параметр - email пользователя");
                return false;
            } else {
                if (gettype($email) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getByEmail: Неверно задан типа параметра - email пользователя");
                    return false;
                } else {
                    $user = DBManager::select("kr_users", ["*"], "email = '$email' LIMIT 1");
                    return $user;
                }
            }
        }


        private static function generate_password ($length) {

        }

    };

?>