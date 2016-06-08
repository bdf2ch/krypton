<?php

    class Extension {
        public $id;
        public $description;
        public $clientSideExtensionUrl;

        public function __construct ($extId, $extDescription, $extClientSideUrl) {
            if ($extId == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extension -> __construct: Не задан параметр - идентификатор расширения");
                return false;
            } else {
                if (gettype($extId) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extension -> __construct: Неверно задан тип параметра - идентификатор расширения");
                    return false;
                } else {
                    $this -> id = $extId;

                    if ($extDescription != null && gettype($extDescription) != "string")
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extension -> __construct: Неверно задан тип параметра - описание расширения");
                    else
                        $this -> description = $extDescription;

                    if ($extClientSideUrl != null && gettype($extClientSideUrl) != "string")
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extension -> __construct: Неверно задан тип параметра - url расширения для клиентской части приложения");
                    else
                        $this -> clientSideExtensionUrl = $extClientSideUrl;
                }
            }
        }

    };

?>