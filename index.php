<?php

    /*** Подключение библиотек и модулей ***/
    require_once "serverside/libs/krypton/Krypton.class.php";


    /*** Создание и инициализация нового приложения Krypton ***/
    $app = new Krypton("test app", "test app description", Krypton::DB_TYPE_MYSQL);
    //$app -> modules -> load("Errors");
    //$app -> modules -> load("Settings");
    //$app -> modules -> load("Session");
    $app -> modules -> load("LDAPModule");
    $app -> modules -> load("UsersModule");
    $app -> start();

    //print_r($_SERVER["REQUEST"]);

    //var_dump(method_exists("Sessions", "install"));





    //echo("<br><br><br><br><br>");
    //var_dump($app);
    //echo("<br><br><br>");
    //var_dump(Errors::get());

    //echo("<br><br><br>");
    //var_dump(DBManager::$link);

    //echo("<br><br><br>");
     //   var_dump("is_connected: ".DBManager::is_connected());

    //echo("<br><br><br>");
    //    var_dump(Settings::getByCode("session_duration"));

?>