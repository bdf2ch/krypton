<?php

    $db_host = "127.0.0.1";
    $db_name = "krypton";
    $db_user = "root";
    $db_password = "";

    $ldap_host = "10.50.0.1";

    function __autoload($class) {
        if (!defined("ENGINE_INSTALL_MODE") && !defined("ENGINE_API_MODE")) {
            //var_dump(Krypton::$extensions);
            if (in_array($class, Krypton::$extensions))
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$class.".extension.php";
            else {
                if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php"))
                    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php";
                else if (file_exists($_SERVER["DOCUMENT_ROOT"])."/serverside/libs/krypton/models/".$class.".model.php")
                    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/models/".$class.".model.php";
            }
        } else if (defined("ENGINE_API_MODE")) {
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php"))
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php";
            else if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$class.".extension.php"))
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$class.".extension.php";
        } else {
            require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php";
        }
    }

?>