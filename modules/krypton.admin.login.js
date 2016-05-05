"use strict";

(function () {
    angular
        .module("krypton.admin.login", ["krypton", "krypton.session"])
        .controller("KryptonAdminLoginController", KryptonAdminLoginController);


    function KryptonAdminLoginController ($log, $scope, $http) {
        $scope.userName = "";
        $scope.password = "";
        $scope.inRemindPasswordMode = false;
        $scope.isPasswordSent = false;
        $scope.errors = [];

        $scope.remindPasswordMode = function (flag) {
            if (flag !== undefined && typeof(flag) === "boolean") {
                $scope.inRemindPasswordMode = flag;
                $scope.errors.splice(0, $scope.errors.length);
            }
            return $scope.inRemindPasswordMode;
        };

        $scope.login = function () {
            $scope.errors.splice(0, $scope.errors.length);
            if ($scope.userName === "") {
                $scope.errors.push("Вы не указали имя пользователя");
            }
            if ($scope.password === "") {
                $scope.errors.push("Вы не указали пароль");
            }
            if ($scope.userName !== "" && $scope.password !== "") {
                
            }
        };

        $scope.remindPassword = function () {
            $scope.errors.splice(0, $scope.errors.length);
            if ($scope.userName === "") {
                $scope.errors.push("UserName not specified");
                return false;
            } else {

            }
        };
    };

})();
