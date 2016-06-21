<?php
    define("KRYPTON_API", 1);
    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/config.php";

    if (!DBManager::is_connected()) {
        if (Errors::isError(DBManager::connect($db_host, $db_user, $db_password)))
            return Errors::push(Errors::ERROR_TYPE_DATABASE, "API: Не удалось установить соединение с БД");

        if (Errors::isError(DBManager::select_db("krypton")))
            return Errors::push(Errors::ERROR_TYPE_DATABASE, "API: не удвлось выбрать БД");

        Settings::init();
        Sessions::init();
        Users::init();

            $postdata = json_decode(file_get_contents('php://input'));

            if (isset($postdata -> action)) {
                switch ($postdata -> action) {
                    case "getAdminTemplate":
                        Krypton::getAdminTemplate();
                        break;
                }
            }

    }

?>