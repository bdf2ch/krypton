<?php

    class Session extends Module {

        private static $id = "kr_sessions";
        private static $current;
        private static $user;



        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            if (!DBManager::is_table_exists_mysql(self::$id)) {
                if (DBManager::create_table_mysql(self::$id)) {
                    if (DBManager::add_column_mysql(self::$id, "user_id", "int(11) NOT NULL default 0") &&
                        DBManager::add_column_mysql(self::$id, "token", "varchar(50) NOT NULL") &&
                        DBManager::add_column_mysql(self::$id, "start", "int(11) NOT NULL default 0") &&
                        DBManager::add_column_mysql(self::$id, "end", "int(11) NOT NULL default 0")) {
                        if (Settings::isInstalled()) {
                            if (Settings::add("'".self::$id."'", "'session_duration'", "'Продолжительность сессии'", "'Продолжительность сессии пользователя'", "'integer'", 2000, 1))
                                echo("Установка модуля Session выполнена успешно</br>");
                        }
                    } else
                        echo("Не удалось выполнить установку SessionManager");
                } else
                    echo("Не удалось выполнить установку SessionManager");
            }
        }



        /**
        * Проверяет, установлен ли модуль в системе
        **/
        public static function isInstalled () {
            if (DBManager::is_table_exists_mysql(self::$id))
                return true;
            else
                return false;
        }



        /**
        * Производит инициализацию модуля
        **/
        public function init () {
            session_start();
            if (isset($_COOKIE["krypton_session"])) {
                $s = DBManager::select_mysql(self::$id, ["*"], "token = '".$_COOKIE["krypton_session"]."' LIMIT 1");
                if ($s == false) {
                     $token = self::generate_token(32);
                     DBManager::insert_row_mysql(self::$id, ["token", "start", "end"], ["'".$token."'", time(), time() + Settings::getByCode("session_duration")]);
                     $s = DBManager::select_mysql(self::$id, ["*"], "token = '".$token."' LIMIT 1");
                     self::$current = $s != false ? new UserSession($s[0]["user_id"], $s[0]["token"], $s[0]["start"], $s[0]["end"]) : null;
                     setcookie("krypton_session", $token);
                } else {
                    self::$current = new UserSession(
                        $s[0]["user_id"],
                        $s[0]["token"],
                        $s[0]["start"],
                        $s[0]["end"]
                    );
                }

                if (self::$current -> userId != 0) {
                    $u = Users::getById(self::$current -> userId);
                    if ($u != false) {
                        self::$user = new User (
                            intval($u[0]["id"]),
                            $u[0]["surname"],
                            $u[0]["name"],
                            $u[0]["fname"],
                            $u[0]["position"],
                            $u[0]["email"],
                            $u[0]["phone"],
                            boolval($u[0]["is_admin"])
                        );
                    }
                }
            } else {
                $token = self::generate_token(32);
                DBManager::insert_row_mysql(self::$id, ["token", "start", "end"], ["'".$token."'", time(), time() + Settings::getByCode("session_duration")]);
                $s = DBManager::select_mysql(self::$id, ["*"], "token = '".$token."' LIMIT 1");
                self::$current = $s != false ? new UserSession($s[0]["user_id"], $s[0]["token"], $s[0]["start"], $s[0]["end"]) : null;
                setcookie("krypton_session", $token);
            }
            $this -> setLoaded(true);

            self::login("testov@kolenergo.ru", "qwerty");
         }



        /**
        * Возвращает объект текущей сессии
        **/
        public static function getCurrentSession () {
           return self::$current;
        }



        /**
        * Возвращает объект текущего пользователя
        **/
        public static function getCurrentUser () {
           return self::$user;
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
                        if (gettype($password) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Неверно задан тип параметра - пароль");
                            return false;
                        } else {

                            if (LDAP::isInstalled() == true) {
                                /***** Если модуль Krypton.LDAP установлен *****/
                                if(LDAP::isLDAPEnabled(self::getCurrentUser() -> id) == true) {

                                } else {
                                     echo("LDAP is disabled for user id=".self::getCurrentUser() -> id."</br>");

                                }
                            } else {
                                 echo("LDAP not installed</br>");
                                /***** Если модуль Krypton.LDAP не установлен *****/
                                $encodedPassword = md5($password);
                                $user = DBManager::select_mysql("kr_users", ["*"], "email = '$login' AND password = '$encodedPassword' LIMIT 1");
                                var_dump($user);

                                if ($user != false) {
                                    /**** Пользователь найден *****/
                                    $currentSessionToken = self::getCurrentSession() -> token;
                                    DBManager::update_row_mysql(self::$id, ["user_id"], [intval($user[0]["id"])]);
                                    var_dump($user);
                                } else {
                                    /***** Пользователь не найден *****/
                                    echo(json_encode("Нет такого пользователя: ".$login.", ".$password));
                                }
                            }
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