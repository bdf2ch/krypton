<?php

    /*** Подключение библиотек и модулей ***/
    require_once "serverside/libs/krypton/Krypton.class.php";


    /*** Создание и инициализация нового приложения Krypton ***/
    $app = new Krypton("test app", "test app description");
    $app -> modules -> load("Session");





    echo("<br><br><br><br><br>");
    var_dump($app);
    echo("<br><br><br>");
    var_dump(ErrorManager::getAll());

    echo("<br><br><br>");
    var_dump(DBManager::$link);

?>