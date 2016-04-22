<?php

    class SessionManager {

        public static function init () {
            DBManager::create_table_mysql("sessions");
        }

    }

?>