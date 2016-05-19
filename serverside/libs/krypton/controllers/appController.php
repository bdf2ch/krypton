<?php
    if (!defined("ENGINE_INSTALL_MODE")) {
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/config.php";
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Error.class.php";
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Errors.class.php";
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Module.class.php";
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/ModuleManager.class.php";

        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/ControllerAction.class.php";
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Controller.class.php";

        //require_once "serverside/libs/krypton/DBManager.class.php";
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Session.class.php";
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Sessions.class.php";
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/User.class.php";
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Setting.class.php";
        require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Settings.class.php";
        //require_once "serverside/libs/xtemplate/xtemplate.class.php";

        function __autoload($className) {
            //echo("serverside/libs/krypton/modules/".$className.".module.php"."</br>");
            include $_SERVER["DOCUMENT_ROOT"]."serverside/libs/krypton/modules/".$className.".module.php";
        }
    }

    //$controller = new Controller();
    //$controller::add("test", "Sessions", "getCurrentSession");
    //$controller::listen();


    class AppController extends Controller {

        public static function test () {

        }

    };

    $controller = new AppController();
?>