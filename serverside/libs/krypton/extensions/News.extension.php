<?php

    class News extends ExtensionInterface {
        public static $id = "kr_news";
        public static $description = "news description";
        public static $clientSideExtensionUrl = "modules/app/krypton.app.news.js";



        public static function install () {}

        public static function isInstalled () {}

        public static function init () {}
    };

?>