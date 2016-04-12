<?php

    /*** Подключение библиотек и модулей ***/

    require_once "serverside/libs/krypton/Krypton.class.php";
    require_once "serverside/libs/krypton/Module.class.php";
    require_once "serverside/libs/krypton/Error.class.php";



    /*** Создание и инициализация нового приложения Krypton ***/
    $app = new Krypton("test app", "test app description");



    var_dump($app);

?>