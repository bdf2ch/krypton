<?php
    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Controller.class.php";


    class AppController extends Controller {

        public function __construct () {
            parent::__construct(__CLASS__);
            Settings::init();
        }

        public function test ($first, $second) {
            echo("test called with first = ".$first." & second = ".$second." user = ".json_encode(Sessions::getCurrentUser()));
        }

    };

    $controller = new AppController();
?>