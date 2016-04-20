<?php

    class DB {
        private $link;


        public function getLink () {
            return $this -> link;
        }


        public function connect ($dbhost, $dbuser, $dbpassword) {
            if ($dbhost != null && $dbuser != null && $dbpassword != null) {
                $link = mysql_connect($dbhost, $dbuser, $dbpassword);
                if (!$link) {
                    $error = new Error(DATABASE_ERROR, mysql_errno(), mysql_error());
                    return $error;
                } else
                    return $link;
            }
        }


        public function disconnect () {
            if ($this -> link != null) {
                if (!mysql_close($this -> link)) {
                    $error = new Error(DATABASE_ERROR, mysql_errno(), mysql_error());
                    return $error;
                } else
                    return true;
            }
        }


    }

?>