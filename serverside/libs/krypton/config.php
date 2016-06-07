<?php

    $db_host = "127.0.0.1";
    $db_name = "krypton";
    $db_user = "root";
    $db_password = "";

    $ldap_host = "10.50.0.1";

    function __autoload($class) {
        if (in_array($class, Krypton::$extensions))
            require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$class.".extension.php";
        else {
            require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php";
                if (method_exists($class, "init") && is_callable($class."::init"))
                    call_user_func($class."::init");
        }
    }

?>