<?php

    /*** Подключение библиотек и модулей ***/
    require_once "serverside/libs/krypton/classes/Krypton.class.php";


    /*** Создание и инициализация нового приложения Krypton ***/
    $app = new Krypton(["LDAP", "Kolenergo"]);


    var_dump(Extensions::get("Kolenergo") -> login("kolu0897", "zx12!@#$"));

    $app -> start();

?>