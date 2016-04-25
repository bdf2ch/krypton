<?php

    class Session extends Module {

        public function install () {
         /**/
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