<?php
    /*** Подключение библиотек и модулей ***/
    require_once "../serverside/libs/krypton/classes/Krypton.class.php";


    /*** Создание и инициализация нового приложения Krypton ***/
    //$app = new Krypton("tezt application", "tezt application description", Krypton::DB_TYPE_MYSQL);
    $app = new Krypton(
        array(
            "title" => "new test app",
            "description" => "new description",
            "extensions" => array(
                "Kolenergo",
                "LDAP"
            )
        )
    );

    $app -> start();
?>