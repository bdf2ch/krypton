"use strict";


(function () {

    angular
        .module("krypton.app", [
            "ngRoute",
            "ngCookies",
            "krypton",
            "krypton.session" ]
        )
        .factory("$application", applicationFactory)

    angular.module("krypton.app").run(kryptonAppRun);

    function kryptonAppRun ($log, $classes, $session) {
        $log.log("krypton.app run...");
        if (krypton !== undefined) {
            $log.info(krypton);
            $session.setCurrentSession(krypton.session);
            $session.setCurrentUser(krypton.user);
        }
        
        $log.log("session: ", $session.getCurrentSession());
        $log.log("user: ", $session.getCurrentUser());
    };


    function applicationFactory () {

    };

})();
