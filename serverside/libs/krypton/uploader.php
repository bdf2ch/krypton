<?php
    define("ENGINE_API_MODE", 1);
    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/config.php";

    $params = new stdClass();
    foreach ($_POST as $key => $param) {
        if ($key != "action")
            $params -> $key = $param;
    }



    $app = new Krypton(["Kolenergo", "LDAP"]);

    if (Sessions::getCurrentSession() == false) {
        return Errors::push(Errors::ERROR_TYPE_ACCESS, "Неавторизованный доступ");
    } else {
        $token = Sessions::getCurrentSession() -> token -> value;

        $result = Sessions::isValidToken($token);
        if ($result == null || $result == false)
            return Errors::push(Errors::ERROR_TYPE_ACCESS, "Недействительный токен");
        if ($_POST != null) {
                if (isset($_POST["action"])) {
                    $result = API::isEntryExists($_POST["action"]);
                    if ($result == null || $result == false)
                        return Errors::push(Errors::ERROR_TYPE_ENGINE, "fsdfsdf");
                    $result = API::call($_POST["action"], $params);
                    echo(json_encode($result));
                }
            } else
                echo("postdata is null");
        }

?>