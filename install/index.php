<?php
    define ("ENGINE_INSTALL_MODE", 1);

    require_once "../serverside/libs/krypton/config.php";

    Krypton::install();
    Settings::install();
    Sessions::install();
    Users::install();
    Files::install();
    Permissions::install();
    //Kolenergo::install();

    $template = new XTemplate("../serverside/templates/install.html");
    $template -> parse("krypton_install");
    $template -> out("krypton_install");
?>