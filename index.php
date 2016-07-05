<?php

    /*** Подключение библиотек и модулей ***/
    require_once "serverside/libs/krypton/classes/Krypton.class.php";

    /*** Создание и инициализация нового приложения Krypton ***/
    $app = new Krypton(["LDAP", "Kolenergo"]);

    var_dump(Extensions::get("Kolenergo") -> login("kolu0897", "zx12!@#$"));

    Krypton::$app -> addInitData("application", json_encode(Krypton::$app));
    Krypton::$app -> addInitData("extensions", json_encode(Krypton::$app -> extensions));
    Krypton::$app -> addInitData("session", json_encode(Sessions::getCurrentSession()));
    Krypton::$app -> addInitData("user", json_encode(Sessions::getCurrentUser()));
    Krypton::$app -> addInitData("settings", json_encode(Settings::getAll()));
    Krypton::$app -> addInitData("errors", json_encode(Errors::getAll()));
    Krypton::$app -> addInitData("users", json_encode(Users::getAll()));
    Krypton::$app -> addInitData("departments", json_encode(Extensions::get("Kolenergo") -> getDepartments()));
    Krypton::$app -> addInitData("divisions", json_encode(Extensions::get("Kolenergo") -> getDivisions()));

    $app -> start();

?>