<?php
    //echo("session module included</br>");
    class Session extends Module {

        public function _construct {
            parent::_construct();
        }

        public static function install () {
            echo("session install");
            if (!DBManager::is_table_exists_mysql("sessions")) {
                if (DBManager::create_table_mysql("sessions")) {
                    if (DBManager::add_column_mysql("sessions", "user_id", "int(11) NOT NULL default 0") &&
                        DBManager::add_column_mysql("sessions", "token", "varchar(50) NOT NULL") &&
                        DBManager::add_column_mysql("sessions", "start", "int(11) NOT NULL default 0") &&
                        DBManager::add_column_mysql("sessions", "end", "int(11) NOT NULL default 0")) {
                        if (DBManager::is_table_exists_mysql("settings")) {
                            if (PropertiesManager::add("''", "'session_duration'", "'Продолжительность сессии пользователя'", "'integer'", 2000, 1))
                                echo("Установка SessionManager выполнена успешно</br>");
                        }
                    } else
                        echo("Не удалось выполнить установку SessionManager");
                } else
                    echo("Не удалось выполнить установку SessionManager");
            }
        }


        public function init () {
            session_start();
            echo("session module init</br>");
            if (isset($_COOKIE["krypton_session"])) {
                echo("session identified: ".$_COOKIE["krypton_session"]);
            } else {
                $token = self::generate_token(32);
                DBManager::insert_row_mysql("sessions", ["token", "start", "end"], ["'".$token."'", time(), time() + PropertiesManager::getByCode("session_duration")]);
                setcookie("krypton_session", $token);
            }
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