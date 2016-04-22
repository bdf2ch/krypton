<?php

    class ModuleManager {
        private $modules = array();

        public static function load ($moduleTitle) {
            if ($moduleTitle != null) {
                if (gettype($moduleTitle) == "string") {
                    //
                } else
                    ErrorManager::add (
                        ERROR_TYPE_ENGINE,
                        ERROR_MODULE_LOAD_WRONG_TITLE_TYPE,
                        "Указан неверный тип параметра при загрузке модуля"
                    );
            } else
                ErrorManager::add (
                    ERROR_TYPE_ENGINE,
                    ERROR_MODULE_LOAD_NO_TITLE,
                    "Не указано наименование загружаемого модуля"
                );
        }
    };

?>