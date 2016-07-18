<?php
    define("ENGINE_API_MODE", 1);
    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/config.php";

    //echo("this is api");

    $app = new Krypton(["Kolenergo", "LDAP"]);
    //Settings::init();
    //Sessions::init();
    //Users::init();
    //Extensions::load("Kolenergo");

    //$api = new API();

    if (Sessions::getCurrentSession() == false) {
        return Errors::push(Errors::ERROR_TYPE_ACCESS, "Неавторизованный доступ");
    } else {
        $token = Sessions::getCurrentSession() -> token -> value;

        $result = Sessions::isValidToken($token);
        if ($result == null || $result == false)
            return Errors::push(Errors::ERROR_TYPE_ACCESS, "Недействительный токен");

        $postdata = json_decode(file_get_contents("php://input"));
        if ($postdata != null) {
            if (isset($postdata -> action)) {
                //echo($postdata -> action);

                $result = API::isEntryExists($postdata -> action);
                if ($result == null || $result == false)
                    return Errors::push(Errors::ERROR_TYPE_ENGINE, "fsdfsdf");

                $result = API::call($postdata -> action, $postdata -> data);
                echo(json_encode($result));
            }
        } else
            echo("postdata is null");
    }

?>