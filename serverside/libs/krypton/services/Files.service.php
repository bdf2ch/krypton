<?php

    class Files implements Service {
        private static $items = array();


        /**
        * Производит установку модуля в системе
        **/
        public static function install () {

            /*
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

            //$result = Settings::add("'krypton'", "'upload_folder'", "'Папка для загрузки файлов'", "'Папка дял загрузки и хранения пользовательских файлов'", Krypton::DATA_TYPE_STRING, "'uploads'", 1);
            //if (!$result) {
            //    Errors::push(Errors::ERROR_TYPE_ENGINE, "Files -> install: Не удалось добавить настройку");
            //    return false;
            //}

            $result = self::create_folder("uploads");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_FILE, "Files -> install: Не удалось создать папку 'uploads' в корневом каталоге на сервере");
                return false;
            }
            */
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
            /*
            $files = DBManager::select("kr_files", ["*"], "''");
            if ($files != false) {
                foreach ($files as $key => $item) {
                    $file = Models::load("File", false);
                    $file -> fromSource($item);
                    array_push(self::$items, $file);
                }
            }
            */

            API::add("upload", "Files", "upload");
        }



        public static function getAll () {
            return self::$items;
        }





        /**
        * Проверяет, существует ли папка на сервере
        * @title {string} - наименование папки
        **/
        public static function is_folder_exists ($title) {
            if ($title == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Files -> is_folder_exists: Не задан параметр - наименование папки");
                return false;
            }

            if (gettype($title) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Files -> is_folder_exists: Неверно задан тип параметра - наименование папки");
                return false;
            }

            if (!file_exists($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.$title))
                return false;

            if (!is_dir($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.$title))
                return false;

            return true;
        }





        /**
        * Создает папку относительно корневого каталога
        * @title {string} - наименование папки
        **/
        public function create_folder ($title) {
            if ($title == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Files -> create_folder: Не задан параметр - наименование папки");
                return false;
            }

            if (gettype($title) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Files -> create_folder: Неверно задан тип парметра - наименование папки");
                return false;
            }

            $path = $_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.$title;
            if (!mkdir($path, 0777, true)) {
                return Errors::push(Errors::ERROR_TYPE_FILE, "Files -> create_folder: Не удалось создать папку '".$path."'");
                return false;
            }

            return true;
        }





        /**
        * Загружает файл на сервер
        * @destination {string} - путь расположения загружаемого файла
        **/
        public static function upload ($destination) {
            if ($destination == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Files -> upload: Не задан параметр - расположение загружаемого файла");
                return false;
            }

            if (gettype($destination) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Files -> upload: Неверно задан типа параметра - расположение загружаемого файла");
                return false;
            }

            if (!isset($_FILES["file"]))
                return false;

            if ($_FILES["file"]["size"] == 0) {
                Errors::push(Errors::ERROR_TYPE_FILE_UPLOAD, "Files -> upload: Размер загружаемого файла равен 0");
                return false;
            }

            $folder = $_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.$destination;
            if (!file_exists($folder)) {
                Errors::push(Errors::ERROR_TYPE_FILE, "Files -> upload: Путь загрузки файла  '".$folder."' не найден");
                return false;
            }

            if (!is_dir($folder)) {
                Errors::push(Errors::ERROR_TYPE_FILE, "Files -> upload: Путь загрузки файла '".$folder."' не является директорией");
                return false;
            }

            //$name = iconv("windows-1251", "UTF-8", $_FILES["file"]['name']);
            $encoding = mb_detect_encoding($_FILES["file"]["name"]);
            $name = mb_convert_encoding($_FILES["file"]["name"], "UTF-8", $encoding);
            $tmpName  = $_FILES["file"]['tmp_name'];
            $size = $_FILES["file"]['size'];
            $type = $_FILES["file"]['type'];
            $url = DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.$destination.DIRECTORY_SEPARATOR.$name;

            if (!file_exists($folder)) {
                Errors::push(Errors::ERROR_TYPE_FILE, "Files -> upload: Папка по адресу '".$folder."' не найдена");
                return false;
            }

            $file = $folder.DIRECTORY_SEPARATOR.$name;
            if (!move_uploaded_file($tmpName, $file)) {
                Errors::push(Errors::ERROR_TYPE_FILE, "Files -> upload: Не удалось скопировать загружаемый файл в папку '".$file."'");
                return false;
            }

            $uploadedFile = Models::construct("File", false);
            $uploadedFile -> title -> value = $name;
            $uploadedFile -> type -> value = $type;
            $uploadedFile -> size -> value = $size;
            $uploadedFile -> isFolder -> value = false;
            $uploadedFile -> url -> value = $url;

            return $uploadedFile;

            //$file = fopen($tmpName, "r");
            //if (!$file) {
            //    Errors::push(Errors::ERROR_TYPE_FILE, "Files -> upload: Не удалось открыть файл '".$tmpName."'");
            //    return false;
            //}

            //$content = fread($file, filesize($tmpName));
            //if (!$content) {
            //    Errors::push(Errors::ERROR_TYPE_FILE, "Files -> upload: Не удалось прочитатьданные из файла '".$tmpName."'");
            //    return false;
            //}

            //fclose($fp);
        }
    }

?>