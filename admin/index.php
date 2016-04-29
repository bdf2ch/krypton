<?php
    define("KRYPTON_ADMIN", 1);

    /*** Подключение библиотек и модулей ***/
    require_once "../serverside/libs/krypton/Krypton.class.php";


    /*** Создание и инициализация нового приложения Krypton ***/
    $app = new Krypton("test app", "test app description");
    $app -> init();
    $app -> modules -> load("Errors");
    $app -> modules -> load("Settings");
    $app -> modules -> load("Session");
    $app -> modules -> load("LDAP");
    $app -> modules -> load("Users");
    $app -> display();

?>