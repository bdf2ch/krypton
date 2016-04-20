<?php
    require_once "serverside/libs/krypton/Error.class.php";



    function isError ($result) {
        if ($result != null) {
            if (get_class($result) == "Error")
                return true;
            else
                return false;
        } else
            return false;
    }



    function connect ($dbhost, $dbuser, $dbpassword) {
        if ($dbhost != null && $dbuser != null && $dbpassword != null) {
            $link = mysql_connect($dbhost, $dbuser, $dbpassword);
            if (!$link) {
                $error = new Error(DATABASE_ERROR, mysql_errno(), mysql_error());
                return $error;
            } else
                return $link;
        }
    }



    function disconnect ($link) {
        if ($link != null) {
            if (!mysql_close($link)) {
                $error = new Error(DATABASE_ERROR, mysql_errno(), mysql_error());
                return $error;
            } else
                return true;
        }
    }
?>