<?php

    class SessionManager {

        public static function init () {
            if (!DBManager::is_table_exists_mysql("sessions")) {
                DBManager::create_table_mysql("sessions");
                DBManager::add_column_mysql("sessions", "user_id", "int(11) NOT NULL default 0");
                DBManager::add_column_mysql("sessions", "token", "varchar(50) NOT NULL");
                DBManager::add_column_mysql("sessions", "start", "int(11) NOT NULL default 0");
                DBManager::add_column_mysql("sessions", "end", "int(11) NOT NULL default 0");
            }
        }

    };

?>