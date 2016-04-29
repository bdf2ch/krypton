<?php

    class Session extends Module {
        private static $current;
        private static $user;

        public static function install () {
            if (!DBManager::is_table_exists_mysql("sessions")) {
                if (DBManager::create_table_mysql("sessions")) {
                    if (DBManager::add_column_mysql("sessions", "user_id", "int(11) NOT NULL default 0") &&
                        DBManager::add_column_mysql("sessions", "token", "varchar(50) NOT NULL") &&
                        DBManager::add_column_mysql("sessions", "start", "int(11) NOT NULL default 0") &&
                        DBManager::add_column_mysql("sessions", "end", "int(11) NOT NULL default 0")) {
                        if (DBManager::is_table_exists_mysql("settings")) {
                            if (Settings::add("'session'", "'session_duration'", "'Продолжительность сессии'", "'Продолжительность сессии пользователя'", "'integer'", 2000, 1))
                                echo("Установка модуля Session выполнена успешно</br>");
                        }
                    } else
                        echo("Не удалось выполнить установку SessionManager");
                } else
                    echo("Не удалось выполнить установку SessionManager");
            }
        }


        public static function isInstalled () {

        }


        public function init () {
            session_start();
            if (isset($_COOKIE["krypton_session"])) {
                $s = DBManager::select_mysql("sessions", ["*"], "token = '".$_COOKIE["krypton_session"]."' LIMIT 1");
                self::$current = $s != false ? new UserSession($s[0]["user_id"], $s[0]["token"], $s[0]["start"], $s[0]["end"]) : null;
                if (self::$current != null && self::$current -> userId != 0) {
                    $u = Users::getById(self::$current -> userId);
                    self::$user = $u != false ? new User($u[0]["surname"], $u[0]["name"], $u[0]["fname"], $u[0]["position"], $u[0]["email"], $u[0]["phone"]) : null;
               }
            } else {
                $token = self::generate_token(32);
                DBManager::insert_row_mysql("sessions", ["token", "start", "end"], ["'".$token."'", time(), time() + Settings::getByCode("session_duration")]);
                setcookie("krypton_session", $token);
            }
            $this -> setLoaded(true);
         }


        public static function getCurrentSession () {
           return self::$current;
        }


        public static function getCurrentUser () {
           return self::$user;
        }


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