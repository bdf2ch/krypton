"use strict";


(function () {
    angular
        .module("krypton.admin", ["ngRoute", "ngCookies", "krypton", "krypton.ui", "krypton.app.kolenergo"])
        .config(function ($routeProvider) {
        })
        .run(kryptonAdminRun);



    function dashboardController ($log, $scope) {

    };



    function usersController ($log, $scope, $users, $kolenergo) {
        $scope.users = $users;
        $scope.kolenergo = $kolenergo;
    };



    function kryptonAdminRun ($log, $location, $navigation, $factory, $rootScope, $users, $http) {
        $log.log("krypton admin run");
        //$location.url("/users");
        $rootScope.navigation = $navigation;

        $users.init();
        
        
        var temp = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp.init({
            url: "/users",
            templateUrl: "../../templates/admin/users/users.html",
            controller: usersController,
            title: "Пользователи",
            description: "Управление пользователями"
        });
        $navigation.add(temp);

        var temp2 = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp2.init({
            url: "/dashboard",
            templateUrl: "../../templates/admin/dashboard/dashboard.html",
            controller: dashboardController(),
            title: "Дашборд",
            description: "Панель управления",
            isDefault: true
        });
        $navigation.add(temp2);


        $http.post("../../serverside/libs/krypton/api.php", { action: "test", data: {} })
            .success(function (data) {
                $log.log(data);
            });

        $log.log(window.krypton);
    };
})();