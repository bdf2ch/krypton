<?php

    class Sessions implements Service {

        private static $session = false;
        private static $user = false;
        public static $items;



        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            $result = DBManager::create_table("kr_sessions");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удалось создать таблицу 'kr_sessions'");
                return false;
            }

            switch (Krypton::getDBType()) {

                case Krypton::DB_TYPE_MYSQL:

                    $result = DBManager::add_column("kr_sessions", "USER_ID", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удалось добавить столбец 'user_id' в таблицу 'kr_sessions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_sessions", "SESSION_TOKEN", "varchar(50) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удалось добавить столбец 'token' в таблицу 'kr_sessions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_sessions", "SESSION_START", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удалось добавить столбец 'start' в таблицу 'kr_sessions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_sessions", "SESSION_END", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удалось добавить столбец 'end' в таблицу 'kr_sessions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_sessions", "IP", "varchar(16) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'ip' в таблицу 'kr_sessions'");
                        return false;
                    }

                    break;

                case Krypton::DB_TYPE_ORACLE:

                    $result = DBManager::add_sequence("seq_sessions", 1, 1);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удалось добавить последовательность 'seq_sessions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_sessions", "USER_ID", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удалось добавить столбец 'user_id' в таблицу 'kr_sessions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_sessions", "SESSION_TOKEN", "VARCHAR2(50) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удалось добавить столбец 'token' в таблицу 'kr_sessions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_sessions", "SESSION_START", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удалось добавить столбец 'start' в таблицу 'kr_sessions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_sessions", "SESSION_END", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удалось добавить столбец 'end' в таблицу 'kr_sessions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "IP", "VARCHAR2(16)");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'ip' в таблицу 'kr_users'");
                        return false;
                    }

                    break;
            }

            $result = Settings::add("'krypton'", "'session_duration'", "'Продолжительность сессии'", "'Продолжительность сессии пользователя'", Krypton::DATA_TYPE_INTEGER, 7257600, 1);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> install: Не удвлось добавить настройку 'session_duration'");
                return false;
            }

            return true;
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
                $result = DBManager::select("kr_sessions", ["*"], "SESSION_TOKEN = '$token'");
                if (!$result) {
                    $newToken = self::generateToken();
                    $start = time();
                    $end = $start + Settings::getByCode("session_duration");


                    switch (Krypton::getDBType()) {
                        case Krypton::DB_TYPE_MYSQL:

                            $result = DBManager::insert_row("kr_sessions", ["SESSION_TOKEN", "SESSION_START", "SESSION_END"], ["'".$newToken."'", $start, $end]);
                            if (!$result) {
                                Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> init: Не удалось добавить новый токен");
                                return false;
                            }

                            break;
                        case Krypton::DB_TYPE_ORACLE:

                            $id = DBManager::sequence_next("seq_sessions");
                            if (!$id) {
                                Errors::push(Errors::Error_TYPE_ENGINE, "Sessions -> init: Не удалось получить следкющее значение последовательности 'seq_sessions'");
                                return false;
                            }

                            $result = DBManager::insert_row("kr_sessions", ["ID", "SESSION_TOKEN", "SESSION_START", "SESSION_END"], [$id, "'".$newToken."'", $start, $end]);
                            if (!$result) {
                                Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> init: Не удалось добавить новый токен");
                                return false;
                            }

                            break;
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
                    $session -> ip -> value = $_SERVER["REMOTE_ADDR"];
                    self::$session = $session;

                    if ($session -> userId -> value != 0) {
                        $result = DBManager::select("kr_users", ["*"], "ID = ".$session -> userId -> value);
                        if (!$result) {
                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> init: Пользователь с идентификатором ".$session -> userId -> value." не найден");
                            return false;
                        }

                        $user = Models::construct("User1", false);
                        $user -> fromSource($result[0]);
                        $user -> ip -> value = strval($_SERVER["REMOTE_ADDR"]);
                        self::$user = $user;
                    }

                    return true;
                }
            } else {
                $newToken = self::generateToken();
                $start = time();
                $end = $start + Settings::getByCode("session_duration");
                $organization = Kolenergo::getOrganizationByUserIP($_SERVER["REMOTE_ADDR"]);
                var_dump($organization);

                switch (Krypton::getDBType()) {
                    case Krypton::DB_TYPE_MYSQL:

                        $result = DBManager::insert_row("kr_sessions", ["SESSION_TOKEN", "SESSION_START", "SESSION_END"], ["'".$newToken."'", $start, $end]);
                        if (!$result) {
                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> init: Не удалось добавить новый токен");
                            return false;
                        }

                        break;

                    case Krypton::DB_TYPE_ORACLE:

                        $id = DBManager::sequence_next("seq_sessions");
                        if (!$id) {
                            Errors::push(Errors::Error_TYPE_ENGINE, "Sessions -> init: Не удвлось получить следующее значение последовательности 'seq_sessions'");
                            return false;
                        }

                        $result = DBManager::insert_row("kr_sessions", ["ID", "SESSION_TOKEN", "SESSION_START", "SESSION_END"], [$id, "'".$newToken."'", $start, $end]);
                        if (!$result) {
                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Sessions -> init: Не удалось добавить новый токен");
                            return false;
                        }

                        break;
                }



                $session = Models::construct("Session", false);
                $session -> token -> value = $newToken;
                $session -> start -> value = $start;
                $session -> end -> value = $end;
                $session -> ip -> value = $_SERVER["REMOTE_ADDR"];
                //$session -> organizationId -> value = $organization -> id -> value;
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
            $result = DBManager::update("kr_sessions", ["USER_ID"], [$id], "SESSION_TOKEN = '$token'");
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
                        if (!DBManager::update("kr_sessions", ["USER_ID"], [$userId], "SESSION_TOKEN = '$currentSessionToken'")) {
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
            $result = DBManager::select("kr_users", ["*"], "EMAIL = '$email' AND PASSWORD = '$password'");
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



        private static function generateToken () {
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
                if (!DBManager::select("kr_sessions", ["*"], "SESSION_TOKEN = '$token'"))
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

            $result = DBManager::select("kr_sessions", ["*"], "SESSION_TOKEN = '$token'");
            return $result != false ? true : false;
        }


    };

?>