<?php

    class Sessions {

        private static $session;
        private static $user;



        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            if (!DBManager::is_table_exists("kr_sessions")) {
                if (DBManager::create_table("kr_sessions")) {
                    if (DBManager::add_column("kr_sessions", "user_id", "int(11) NOT NULL default 0") &&
                        DBManager::add_column("kr_sessions", "token", "varchar(50) NOT NULL") &&
                        DBManager::add_column("kr_sessions", "start", "int(11) NOT NULL default 0") &&
                        DBManager::add_column("kr_sessions", "end", "int(11) NOT NULL default 0")
                    ) {
                        if (Settings::add("'krypton'", "'session_duration'", "'Продолжительность сессии'", "'Продолжительность сессии пользователя'", Krypton::DATA_TYPE_INTEGER, 2000, 1))
                            return true;
                        else {
                            Errors::push(Errors::Error_TYPE_ENGINE, "Session -> install: Не удалось добавить настройку");
                            return false;
                        }
                    } else {
                        Errors::push(Errors::Error_TYPE_ENGINE, "Session -> install: Не удалось создать структуру таблицы сессий");
                        return false;
                    }
                } else {
                    Errors::push(Errors::ERROR_TYPE_ENGINE, "Session -> install: Не удалось создать таблицу сессий");
                    return false;
                }
            } else
                return false;
        }



        /**
        * Проверяет, установлен ли модуль в системе
        **/
        //public static function isInstalled () {
        //    if (DBManager::is_table_exists("kr_sessions"))
        //        return true;
        //    else
        //        return false;
        //}



        /**
        * Производит инициализацию модуля
        **/
        public static function init () {
            global $db_host;
            global $db_user;
            global $db_password;

            //echo("</br>sessions init called</br>");

            if (isset($_COOKIE["krypton_session"])) {
                if (DBManager::is_connected()) {
                    if (!DBManager::connect($db_host, $db_user, $db_password)) {
                        return Errors::push(Errors::ERROR_TYPE_DATABASE, "Sessions -> init: Не удалось установить соединение с БД");
                    } else {
                        if (!DBManager::select_db("krypton")) {
                            return Errors::push(Errors::ERROR_TYPE_DATABASE, "Sessions -> init: Не удалось выбрать БД");
                        } else {
                            $s = DBManager::select("kr_sessions", ["*"], "token = '".$_COOKIE["krypton_session"]."' LIMIT 1");
                            if ($s == false) {
                                $token = self::generate_token(32);
                                DBManager::insert_row("kr_sessions", ["token", "start", "end"], ["'".$token."'", time(), time() + Settings::getByCode("session_duration")]);
                                $s = DBManager::select("kr_sessions", ["*"], "token = '".$token."' LIMIT 1");
                                self::$session = $s != false ? new Session($s[0]["user_id"], $s[0]["token"], $s[0]["start"], $s[0]["end"]) : null;
                                setcookie("krypton_session", $token);
                            } else {
                                self::$session = new Session (
                                    $s[0]["user_id"],
                                    $s[0]["token"],
                                    $s[0]["start"],
                                    $s[0]["end"]
                                );
                            }

                            if (self::$session -> userId != 0) {
                                $u = Users::getById(self::$session -> userId);
                                if ($u != false) {
                                    self::$user = $u;
                                }
                            }

                            //var_dump(self::getCurrentSession());
                        }
                    }
                } else
                    return Errors::push(ERROR_TYPE_DATABASE, "Sessions -> init: Отсутсвтует соединение с БД");
            } else {
                $token = self::generate_token(32);
                DBManager::insert_row("kr_sessions", ["token", "start", "end"], ["'".$token."'", time(), time() + Settings::getByCode("session_duration")]);
                $s = DBManager::select("kr_sessions", ["*"], "token = '".$token."' LIMIT 1");
                self::$session = $s != false ? new Session($s[0]["user_id"], $s[0]["token"], $s[0]["start"], $s[0]["end"]) : null;
                setcookie("krypton_session", $token);
            }
            //$this -> setLoaded(true);

            //var_dump(LDAP::login("kolu0897", "zx12!@#$"));
            //self::login("kolu0897", "zx12!@#$");
         }



        /**
        * Возвращает объект текущей сессии
        **/
        public static function getCurrentSession () {
           return self::$session;
        }



        /**
        * Устанавливает объект текущего пользователя по идентификатору пользователя
        * @id - Идентификатор пользователя
        * ! Требует наличия загруженного модуля UsersModule
        **/
        public static function setCurrentUserById ($id) {
            if ($id == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> setCurrentUserById: Не задан параметр - идентификатор пользователя");
                return false;
            } else {
                if (gettype($id) != "integer") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> setCurrentUserById: Неверно задан тип параметра - идентификатор пользователя");
                    return false;
                } else {
                    $user = Users::getById($id);
                    if (!$user) {
                        Errors::push(Errors::Error_TYPE_ENGINE, "Session -> setCurrentUserById: пользователь с идентификатором ".$id." не найден");
                        return false;
                    } else {
                        self::$user = $user;
                        return true;
                    }
                }
            }
        }



        /**
        * Возвращает объект текущего пользователя
        **/
        public static function getCurrentUser () {
           return self::$user;
        }



        public static function assignCurrentSessionToUser ($userId) {
            if ($userId == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> assignSessionToUser: Не задан параметр - идентификатор пользователя");
                return false;
            } else {
                if (gettype($userId) != "integer") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> assignSessionToUser: Неверно задан тип параметра - идентификатор пользователя");
                    return false;
                } else {
                    if (self::getCurrentSession() == null) {
                        Errors::push(Error_TYPE_ENGINE, "Session -> assignSessionToUser: Текущая сессия не определена");
                        return false;
                    } else {
                        $currentSessionToken = self::getCurrentSession() -> token;
                        if (!DBManager::update("kr_sessions", ["user_id"], [$userId], "token = '$currentSessionToken'")) {
                            return false;
                        } else {
                            if (self::$session != null)
                                self::$session -> userId = $userId;
                            return true;
                        }
                    }
                }
            }
        }



        public static function login ($login, $password) {
            if ($login == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Не задан параметр - логин пользователя");
                return false;
            } else {
                if (gettype($login) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Неверно задан тип параметра - логин пользователя");
                    return false;
                } else {
                    if ($password == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Не задан параметр - пароль");
                        return false;
                    } else {
                        if (gettype(strval($password)) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Неверно задан тип параметра - пароль");
                            return false;
                        } else {


                            if (LDAP::isInstalled()) {

                                if (self::getCurrentUser() != null) {
                                    //echo("current user is not null");
                                } else {
                                    $ADUser = LDAP::login($login, $password);
                                    var_dump($ADUser);
                                    if ($ADUser != false) {


                                            $isUserExists = Users::getByEmail($ADUser -> email);
                                            if ($isUserExists == false) {
                                                $addedUser = Users::add(
                                                    $ADUser -> name,
                                                    $ADUser -> fname,
                                                    $ADUser -> surname,
                                                    $ADUser -> position,
                                                    $ADUser -> email,
                                                    $ADUser -> phone,
                                                    $password,
                                                    false
                                                );
                                                if ($addedUser != false) {
                                                    self::setCurrentUserById($addedUser);
                                                    self::assignCurrentSessionToUser($addedUser);
                                                    $newUser = Users::getById($addedUser);
                                                    array_push(self::$items, $newUser);
                                                }

                                            }



                                    }
                                }

                            } else {

                            }


                            /*
                            if (LDAP::isInstalled() == true) {
                                if (self::getCurrentUser() != null) {
                                    if(LDAP::isLDAPEnabled(self::getCurrentUser() -> id) == true) {
                                        var_dump(LDAP::login($login, $password));
                                    } else {
                                        echo("LDAP is disabled for user id=".self::getCurrentUser() -> id."</br>");
                                    }
                                } else {
                                    echo("current user is null");
                                }



                            } else {
                                 echo("LDAP not installed</br>");

                                $encodedPassword = md5($password);
                                $user = DBManager::select("kr_users", ["*"], "email = '$login' AND password = '$encodedPassword' LIMIT 1");
                                var_dump($user);

                                if ($user != false) {

                                    $currentSessionToken = self::getCurrentSession() -> token;
                                    DBManager::update(self::$id, ["user_id"], [intval($user[0]["id"]), "token = "."'".$currentSessionToken."'"]);
                                    var_dump($user);
                                } else {

                                    echo(json_encode("Нет такого пользователя: ".$login.", ".$password));
                                }

                            }*/
                        }
                    }
                }
            }
        }



        /**
        * Генерирует уникальный ключ
        * @length - длина ключа
        **/
        private function generate_token ($length) {
            $arr = array(
                'a','b','c','d','e','f',
                'g','h','i','j','k','l',
                'm','n','o','p','r','s',
                't','u','v','x','y','z',
                'A','B','C','D','E','F',
                'G','H','I','J','K','L',
                'M','N','O','P','R','S',
                'T','U','V','X','Y','Z',
                '1','2','3','4','5','6',
                '7','8','9','0'
            );

            $pass = "";
            for($i = 0; $i < $length; $i++) {
                $index = rand(0, count($arr) - 1);
                $pass .= $arr[$index];
            }
            return $pass;
        }


    };

?>