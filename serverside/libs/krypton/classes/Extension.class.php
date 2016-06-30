<?php

    class Extension {
        public $id;
        public $title;
        public $description = "";
        public $url = "";
        public $enabled = false;

        public function __construct ($id, $title, $url, $description) {
            if ($id == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extension -> __construct: Не задан параметр - идентификатор расширения");
            else
                if (gettype($id) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extension -> __construct: Неверно задан тип параметра - идентификатор расширения");
            $this -> id = $id;


            if ($title == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extension -> __construct: Не задан параметр - наименование расширения");
            else
                if (gettype($title) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extension -> __construct: Неверно задан тип параметра - наименование расширения");
            $this -> title = $title;


            if ($url != null && gettype($url) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extension -> __construct: Неверно задан тип параметра - описание расширения");
            else
                $this -> url = $url;


            if ($description != null && gettype($description) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extension -> __construct: Неверно задан тип параметра - описание расширения");
            else
                $this -> description = $description;
        }


        public function enable () {
            $result = DBManager::update("kr_extensions", ["enabled"], [1], "id = '".$this -> id."'");
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_DATABASE, "Extension -> enable: Не удалось включить расширение '".$this -> id."'");

            $this -> enabled = true;
        }




        public function getUrl () {

            if (defined("ENGINE_ADMIN_MODE"))
                return "../../".$this -> url;
            else
                return $this -> url;


            //return defined("ENGINE_ADMIN_MODE") ? "../../".$this -> url : $this -> url;
        }

    };

?>