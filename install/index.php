<?php
    const ENGINE_INSTALL_MODE = 1;

    require_once "../serverside/libs/krypton/Krypton.class.php";
    require_once "../serverside/libs/krypton/config.php";
    require_once "../serverside/libs/krypton/Error.class.php";
    require_once "../serverside/libs/krypton/ErrorManager.class.php";
    require_once "../serverside/libs/krypton/Module.class.php";
    require_once "../serverside/libs/krypton/ModuleManager.class.php";
    require_once "../serverside/libs/krypton/DBManager.class.php";
    require_once "../serverside/libs/krypton/SessionManager.class.php";
    require_once "../serverside/libs/krypton/PropertiesManager.class.php";
    require_once "../serverside/libs/xtemplate/xtemplate.class.php";

    Krypton::install();
    PropertiesManager::install();
    SessionManager::install();

    $template = new XTemplate("../serverside/templates/install.html");
    $template -> parse("krypton_install");
    $template -> out("krypton_install");
?>