<?php

    class Files extends Service {
        private static $items = array();


        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            $result = DBManager::create_table("kr_files");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось создать таблицу с информацией о файлах");
                return false;
            }

            $result = DBManager::add_column("kr_files", "parent_id", "int(11) default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить столбец 'parent_id' в таблицу с информацией о файлах");
                return false;
            }

            $result = DBManager::add_column("kr_files", "title", "varchar(200) NOT NULL");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить столбец 'title' в таблицу с информацией о файлах");
                return false;
            }

            $result = DBManager::add_column("kr_files", "type", "varchar(200) NOT NULL");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить столбец 'type' в таблицу с информацией о файлах");
                return false;
            }

            $result = DBManager::add_column("kr_files", "size", "int(200) default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить столбец 'size' в таблицу с информацией о файлах");
                return false;
            }

            $result = DBManager::add_column("kr_files", "is_folder", "int(200) default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить столбец 'is_folder' в таблицу с информацией о файлах");
                return false;
            }

            $result = Settings::add("'krypton'", "'upload_folder'", "'Папка для загрузки файлов'", "'Папка дял загрузки и хранения пользовательских файлов'", Krypton::DATA_TYPE_STRING, "'uploads'", 1);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить настройку");
                return false;
            }

            return true;
        }





        /**
        * Проверяет, установлен ли модуль в системе
        **/

        public static function isInstalled () {
            return DBManager::is_table_exists("kr_files");
        }





        /**
        * Выполняет инициализацию модуля
        **/
        public static function init () {
            $files = DBManager::select("kr_files", ["*"], "''");
            if ($files != false) {
                foreach ($files as $key => $item) {
                    $file = Models::load("File", false);
                    $file -> fromSource($item);
                    array_push(self::$items, $file);
                }
            }
        }



        public static function getAll () {
            return self::$items;
        }
    }

?>