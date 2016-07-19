<?php
    define("ENGINE_API_MODE", 1);
    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/config.php";

    $app = new Krypton(["Kolenergo", "LDAP"]);
    $params = new stdClass();

    //var_dump($_POST);

    if (isset($_POST) && sizeof($_POST) > 0) {
        foreach ($_POST as $key => $param) {
            $params -> $key = $param;
        }
    } else
        $params = json_decode(file_get_contents("php://input"));

     //var_dump($params);



    if (Sessions::getCurrentSession() == false) {
        return Errors::push(Errors::ERROR_TYPE_ACCESS, "Неавторизованный доступ");
    } else {
        $token = Sessions::getCurrentSession() -> token -> value;

        $result = Sessions::isValidToken($token);
        if ($result == null || $result == false)
            return Errors::push(Errors::ERROR_TYPE_ACCESS, "Недействительный токен");

            if (isset($params -> action)) {
                $result = API::isEntryExists($params -> action);
                if ($result == null || $result == false)
                    return Errors::push(Errors::ERROR_TYPE_ENGINE, "fsdfsdf");

                $answer = new stdClass();
                $answer -> errors = array();
                $errorsCount = sizeof(Errors::getAll());
                $result = API::call($params -> action, $params);
                $answer -> result = $result;

                if (sizeof(Errors::getAll()) > $errorsCount)
                    $answer -> errors = array_slice(Errors::getAll(), $errorsCount - 1);

                echo(json_encode($answer));
            }
    }

?>