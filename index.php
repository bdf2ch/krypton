<?php

    /*** Подключение библиотек и модулей ***/
    require_once "serverside/libs/krypton/classes/Krypton.class.php";


    /*** Создание и инициализация нового приложения Krypton ***/
    //$app = new Krypton("tezt application", "tezt application description", Krypton::DB_TYPE_MYSQL);
    $app = new Krypton(array(
        "title" => "new test app",
        "description" => "new description",
        "extensions" => array(
            "Kolenergo",
            "LDAP",
            //"News",
            //"Telephones"
        )
    ));

    $app -> start();


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