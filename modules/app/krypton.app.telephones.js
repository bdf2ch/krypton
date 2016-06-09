"use strict";


(function () {
    angular
        .module("krypton.app.telephones", [])
        .controller("TelephonesController", TelephonesController);



    function TelephonesController ($log, $scope, $users) {
        $scope.person = "";
        $scope.users = $users;
    };
})();
