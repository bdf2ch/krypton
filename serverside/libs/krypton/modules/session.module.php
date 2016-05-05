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
                self::$current = $s != false ? new UserSession($s[0]["user_id"], $s[0]["token"], $s[0]["start"], $s[0]["end"]) : null;

                if (self::$current != null && self::$current -> userId != 0) {
                    $u = Users::getById(self::$current -> userId);
                    self::$user = $u != false ? new User(intval($u[0]["id"]), $u[0]["surname"], $u[0]["name"], $u[0]["fname"], $u[0]["position"], $u[0]["email"], $u[0]["phone"], $u[0]["login"], boolval($u[0]["is_admin"])) : null;
               }
            } else {
                $token = self::generate_token(32);
                DBManager::insert_row_mysql(self::$id, ["token", "start", "end"], ["'".$token."'", time(), time() + Settings::getByCode("session_duration")]);
                $s = DBManager::select_mysql(self::$id, ["*"], "token = '".$token."' LIMIT 1");
                self::$current = $s != false ? new UserSession($s[0]["user_id"], $s[0]["token"], $s[0]["start"], $s[0]["end"]) : null;
                setcookie("krypton_session", $token);
            }
            $this -> setLoaded(true);
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



        public static function login ($userName, $password) {
            if ($userName == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Не задан параметр - имя пользователя");
                return false;
            } else {
                if (gettype($userName) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Session -> login: Неверно задан тип параметра - имя пользователя");
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
                            if (LDAP::isInstalled() != true) {
                                Errors::push(Errors::ERROR_TYPE_ENGINE, "Session -> login: Модуль Krypton.LDAP не установлен");
                                return false;
                            } else {
                                $u = self::getCurrentUser();
                                if ($u != null && $u -> id != 0) {
                                    $ldap_enabled - LDAP::isLDAPEnabled($u -> id);
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