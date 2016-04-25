<?php
    //function __autoload($className) {
    //        echo("serverside/libs/krypton/modules/".$className.".module.php"."</br>");
    //        include "serverside/libs/krypton/modules/".$className.".module.php";
            //throw new Exception("Unable to load $className.");
    //}

    class ModuleManager {
        private $modules = array();

        public static function load ($moduleTitle) {
            if ($moduleTitle != null) {
                if (gettype($moduleTitle) == "string") {
                    $module = new $moduleTitle();
                    $module -> init();
                } else {
                    ErrorManager::add (
                        ERROR_TYPE_ENGINE,
                        ERROR_MODULE_LOAD_WRONG_TITLE_TYPE,
                        "Указан неверный тип параметра при загрузке модуля"
                    ) -> send();
                    return false;
                }
            } else
                ErrorManager::add (
                    ERROR_TYPE_ENGINE,
                    ERROR_MODULE_LOAD_NO_TITLE,
                    "Не указано наименование загружаемого модуля"
                );
        }
    };

?>