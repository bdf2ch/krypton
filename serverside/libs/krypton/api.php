<?php
    define("ENGINE_API_MODE", 1);
    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/config.php";

    if (!DBManager::is_connected()) {
        if (Errors::isError(DBManager::connect($db_host, $db_user, $db_password))) {
            echo(json_encode(Errors::push(Errors::ERROR_TYPE_DATABASE, "API: Не удалось установить соединение с БД")));
            return false;
        }

        if (Errors::isError(DBManager::select_db("krypton"))) {
            echo(json_encode(Errors::push(Errors::ERROR_TYPE_DATABASE, "API: не удвлось выбрать БД")));
            return false;
        }

        Settings::init();
        Sessions::init();
        Users::init();
        Extensions::load("Kolenergo");

        if (Sessions::getCurrentSession() == false) {
            echo(json_encode(Errors::push(Errors::ERROR_TYPE_DEFAULT, "Неавторизированный доступ")));
            return false;
        } else {
            $postdata = json_decode(file_get_contents('php://input'));

            if (isset($postdata -> action)) {
                $isEntryExists = API::getEntry($postdata -> action);
                if (Errors::isError($isEntryExists)) {
                    echo(json_encode($isEntryExists));
                    return false;
                } else {
                    $result = API::call($postdata -> action, $postdata -> data);
                    echo(json_encode($result));
                }
            }
        }


    }

?>