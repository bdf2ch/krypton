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
                        if (Settings::add("'krypton'", "'session_duration'", "'Продолжительность сессии'", "'Продолжительность сессии пользователя'", Krypton::DATA_TYPE_INTEGER, 7257600, 1))
                            return true;
                        else {
                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Session -> install: Не удалось добавить настройку");
                            return false;
                        }
                    } else {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Session -> install: Не удалось создать структуру таблицы сессий");
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
        * Выполняет инициализацию сервиса
        **/
        public static function init () {
            global $db_host;
            global $db_user;
            global $db_password;

            if (isset($_COOKIE["krypton_session"])) {
                $token = $_COOKIE["krypton_session"];
                $result = DBManager::select("kr_sessions", ["*"], "token = '$token' LIMIT 1");
                if (!$result) {
                    $newToken = self::generateToken();
                    $start = time();
                    $end = $start + Settings::getByCode("session_duration");

                    $result = DBManager::insert_row("kr_sessions", ["token", "start", "end"], ["'".$newToken."'", $start, $end]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> init: Не удалось добавить новый токен");
                        return false;
                    }

                    $session = Models::construct("Session", false);
                    $session -> token -> value = $newToken;
                    $session -> start -> value = $start;
                    $session -> end -> value = $end;
                    self::$session = $session;

                    return true;
                } else {
                    $session = Models::construct("Session", false);
                    $session -> fromSource($result[0]);
                    self::$session = $session;

                    if ($session -> userId -> value != 0) {
                        $result = DBManager::select("kr_users", ["*"], "id = ".$session -> userId -> value);
                        if (!$result) {
                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> init: Пользователь с идентификатором ".$session -> userId -> value." не найден");
                            return false;
                        }

                        $user = Models::construct("User1", false);
                        $user -> fromSource($result[0]);
                        self::$user = $user;
                    }

                    return true;
                }
            } else {
                $newToken = self::generateToken();
                $start = time();
                $end = $start + Settings::getByCode("session_duration");

                $result = DBManager::insert_row("kr_sessions", ["token", "start", "end"], ["'".$newToken."'", $start, $end]);
                if (!$result) {
                    Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> init: Не удалось добавить новый токен");
                    return false;
                }

                $session = Models::construct("Session", false);
                $session -> token -> value = $newToken;
                $session -> start -> value = $start;
                $session -> end -> value = $end;
                self::$session = $session;
                setcookie("krypton_session", $newToken, $end, "/", $_SERVER["SERVER_NAME"]);

                return true;
            }
        }



        /**
        * Возвращает объект текущей сессии
        **/
        public static function getCurrentSession () {
           return self::$session;
        }



        /**
        * Устанавливает объект текущего пользователя по идентификатору пользователя
        * @id {integer} - идентификатор пользователя
        **/
        public static function setCurrentUserById ($id) {
            if ($id == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Sessions -> setCurrentUserById: Не задан параметр - идентификатор пользователя");
                return false;
            }

            if (gettype($id) != "integer") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Sessions -> setCurrentUserById: Неверно задан тип параметра - идентификатор пользователя");
                return false;
            }

            $user = Users::getById($id);
            if (!$user) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> setCurrentUserById: пользователь с идентификатором ".$id." не найден");
                return false;
            }

            $token = self::getCurrentSession() -> token -> value;
            $result = DBManager::update("kr_sessions", ["user_id"], [$id], "token = '$token'");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> setCurrentUserById: Не удалось установить текущего пользователя");
                return false;
            }

            self::$user = $user;
            return true;
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
                for ($i = 0; $i < 32; $i++) {
                    $index = rand(0, count($arr) - 1);
                    $token .= $arr[$index];
                }
                if (!DBManager::select("kr_sessions", ["*"], "token =  '$token'"))
                    $success = true;
            }

            return $token;
        }



        public static function isValidToken ($token) {
            if ($token == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Sessions -> isValidToken: Не задан параметр - токен сессии пользователя");
                return null;
            }

            if (gettype($token) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Sessions -> isValidToken: Неверно задан тип параметра - токен секссии пользователя");
                return null;
            }

            $result = DBManager::select("kr_sessions", ["*"], "token = '$token' LIMIT 1");
            return $result != false ? true : false;
        }


    };

?>