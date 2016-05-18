<?php
    define ("ENGINE_INSTALL_MODE", 1);

    function __autoload($className) {
        echo("serverside/libs/krypton/modules/".$className.".module.php"."</br>");
        include "../serverside/libs/krypton/modules/".$className.".module.php";
    }

    require_once "../serverside/libs/krypton/Krypton.class.php";
    require_once "../serverside/libs/krypton/config.php";
    require_once "../serverside/libs/krypton/Error.class.php";
    require_once "../serverside/libs/krypton/Module.class.php";
    require_once "../serverside/libs/krypton/DBManager.class.php";
    require_once "../serverside/libs/krypton/Settings.class.php";
    require_once "../serverside/libs/krypton/Sessions.class.php";
    require_once "../serverside/libs/xtemplate/xtemplate.class.php";

    //Errors::install();
    Krypton::install();
    //Settings::install();
    //Session::install();
    Users::install();
    LDAP::install();

    $template = new XTemplate("../serverside/templates/install.html");
    $template -> parse("krypton_install");
    $template -> out("krypton_install");
?>