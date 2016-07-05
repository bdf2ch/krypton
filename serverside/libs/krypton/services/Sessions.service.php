<?php

    class Sessions {

        private static $session = false;
        private static $user = false;
        public static $items;



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
            //if (!defined("ENGINE_API")) {

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
                                if ($s != false) {
                                    $session = Models::load("Session", false);
                                    $session -> fromSource($s[0]);
                                    self::$session = $session;
                                } else
                                    self::$session = false;
                                //self::$session = $s != false ? new Session($s[0]["user_id"], $s[0]["token"], $s[0]["start"], $s[0]["end"]) : null;
                                setcookie("krypton_session", $token);
                            } else {
                                $session = Models::load("Session", false);
                                $session -> fromSource($s[0]);
                                self::$session = $session;
                            }
                            //var_dump(self::$session);
                            if (self::$session -> userId -> value != 0) {
                                $u = Users::getById(self::$session -> userId -> value);
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
                if (!defined("ENGINE_API_MODE")) {
                    $token = self::generate_token(32);
                    DBManager::insert_row("kr_sessions", ["token", "start", "end"], ["'".$token."'", time(), time() + Settings::getByCode("session_duration")]);
                    $s = DBManager::select("kr_sessions", ["*"], "token = '".$token."' LIMIT 1");
                    self::$session = $s != false ? new Session($s[0]["user_id"], $s[0]["token"], $s[0]["start"], $s[0]["end"]) : null;
                    setcookie("krypton_session", $token);
                }
            }

            //}
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
                        $currentSessionToken = self::getCurrentSession() -> token -> value;
                        if (!DBManager::update("kr_sessions", ["user_id"], [$userId], "token = '$currentSessionToken'")) {
                            return false;
                        } else {
                            if (self::$session != null)
                                self::$session -> userId -> value = $userId;
                            return true;
                        }
                    }
                }
            }
        }



        /**
        * Производит авторизацию пользователя
        * @email {string} - e-mail пользователя
        * @password {string} - пароль пользователя
        **/
        public static function login ($email, $password) {
            if ($email == null)
                return  Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Не задан параметр - e-mail пользователя");

            if (gettype($email) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Неверно задан тип параметра - e-mail пользователя");

            if ($password == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Не задан параметр - пароль");

            if (gettype($password) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Неверно задан тип параметра - пароль");

            $passwd = md5($password);
            $result = DBManager::select("kr_users", ["*"], "email = '$email' AND password = '$password' LIMIT 1");
            if (!$result)
                return $result;

            $user = Models::construct("User1", false);
            $user -> fromSource($result[0]);
            return $user;
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



        private function generateToken () {
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
            $success = false;
            $token = "";
            while (!$success) {
                for ($i = 0; $i < 16; $i++) {
                    $index = rand(0, count($arr) - 1);
                    $token .= $arr[$index];
                }
                if (!DBManager::select("kr_sessions", ["*"], "token =  '$token'"))
                    $success = true;
            }

            return $token;
        }



        public static function isValidToken ($token) {
            if ($token == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Sessions -> isValidToken: Не задан параметр - токен сессии пользователя");
            else
                if (gettype($token) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Sessions -> isValidToken: Неверно задан тип параметра - токен секссии пользователя");

            $result = DBManager::select("kr_sessions", ["*"], "token = '$token' LIMIT 1");
            if (!Errors::isError($result)) {
                return sizeof($result) > 0 ? true : false;
            } else
                return $result;
        }


    };

?>