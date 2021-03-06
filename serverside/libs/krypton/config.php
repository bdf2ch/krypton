<?php

    //$db_host = "127.0.0.1";
    //$db_name = "krypton";
    //$db_user = "root";
    //$db_password = "l1mpb1zk1t";

    $db_host = "10.50.0.70/PDBKOLENERGO";
    $db_user = "kolenergo";
    $db_password = "kolenergo";

    $ldap_host = "10.50.0.1";

    $extensions_dir = $_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR."serverside".DIRECTORY_SEPARATOR."libs".DIRECTORY_SEPARATOR."krypton".DIRECTORY_SEPARATOR."extensions";

    function __autoload($class) {
        if (defined("ENGINE_INSTALL_MODE")) {
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php"))
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php";
                            //else if (file_exists($_SERVER["DOCUMENT_ROOT"])."/serverside/libs/krypton/models/".$class.".model.php")
                            //    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/models/".$class.".model.php";
                else if (file_exists($_SERVER["DOCUMENT_ROOT"])."/serverside/libs/krypton/services/".$class.".service.php") {
                    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/services/".$class.".service.php";
                Services::register($class);
                }
        }

        if (!defined("ENGINE_API_MODE") && !defined("ENGINE_INSTALL_MODE")) {
            //var_dump(Krypton::$extensions);

            //if (in_array($class, Krypton::$extensions))
            //    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$class.".extension.php";
            //else {
                if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php"))
                    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php";
                //else if (file_exists($_SERVER["DOCUMENT_ROOT"])."/serverside/libs/krypton/models/".$class.".model.php")
                //    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/models/".$class.".model.php";
                else if (file_exists($_SERVER["DOCUMENT_ROOT"])."/serverside/libs/krypton/services/".$class.".service.php") {
                    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/services/".$class.".service.php";
                    Services::register($class);
                }
            //}
        }

        if (defined("ENGINE_API_MODE")) {
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php"))
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/classes/".$class.".class.php";
            else if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$class.".extension.php"))
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$class.".extension.php";
            else if (file_exists($_SERVER["DOCUMENT_ROOT"])."/serverside/libs/krypton/services/".$class.".service.php") {
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/services/".$class.".service.php";
                Services::register($class);
            }
        }
    }

?>