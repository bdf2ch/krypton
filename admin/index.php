<?php
    define("ENGINE_ADMIN_MODE", 1);

    /*** Подключение библиотек и модулей ***/
    require_once "../serverside/libs/krypton/classes/Krypton.class.php";

    /*** Создание и инициализация нового приложения Krypton ***/
    $app = new Krypton(["LDAP", "Kolenergo"]);

    Krypton::$app -> addInitialData("application", json_encode(Krypton::$app));
    Krypton::$app -> addInitialData("extensions", json_encode(Krypton::$app -> extensions));
    Krypton::$app -> addInitialData("session", json_encode(Sessions::getCurrentSession()));
    Krypton::$app -> addInitialData("user", json_encode(Sessions::getCurrentUser()));
    Krypton::$app -> addInitialData("settings", json_encode(Settings::getAll()));
    Krypton::$app -> addInitialData("errors", json_encode(Errors::getAll()));
    Krypton::$app -> addInitialData("userGroups", json_encode(Users::getGroups()));
    Krypton::$app -> addInitialData("users", json_encode(Users::getInitialData()));
    Krypton::$app -> addInitialData("permissionRules", json_encode(Permissions::getAllRules()));
    Krypton::$app -> addInitialData("organizations", json_encode(Extensions::get("Kolenergo") -> getOrganizations()));
    Krypton::$app -> addInitialData("departments", json_encode(Extensions::get("Kolenergo") -> getDepartments()));
    Krypton::$app -> addInitialData("divisions", json_encode(Extensions::get("Kolenergo") -> getDivisions()));
    Krypton::$app -> addInitialData("ats", json_encode(Extensions::get("Kolenergo") -> getATS()));
    Krypton::$app -> addInitialData("atsCodes", json_encode(Extensions::get("Kolenergo") -> getATSCodes()));

    //var_dump(Sessions::getCurrentUser());

    $app -> start();
?>