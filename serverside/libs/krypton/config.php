<?php

    $db_host = "127.0.0.1";
    $db_name = "krypton";
    $db_user = "root";
    $db_password = "";

    $ldap_host = "10.50.0.1";

     function __autoload($class) {
                    $modulePos = strpos($class, "Module");
                    if ($modulePos != false) {
                        $moduleTitle = substr($class, 0, $modulePos);
                        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/modules/".$moduleTitle.".module.php";
                    } else {
                        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/".$class.".class.php";
                        if (method_exists($class, "init") && is_callable($class."::init"))
                            call_user_func($class."::init");
                    }
                }

?>