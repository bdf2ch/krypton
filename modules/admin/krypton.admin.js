"use strict";


(function () {
    angular
        .module("krypton.admin", ["ngRoute", "ngCookies", "ngSanitize", "angularFileUpload", "krypton", "krypton.ui", "krypton.app.kolenergo"])
        .config(function ($routeProvider,$sceProvider) {
            $sceProvider.enabled(false);

            $routeProvider.when("/users/new",
                {
                templateUrl: "../templates/admin/users/new-user.html",
                controller: newUserController
                }
            );
        })
        .run(kryptonAdminRun);



    function dashboardController ($log, $scope) {

    };





    function usersController ($log, $scope, $http, $factory, $users, $modals, $kolenergo, $location) {
        $scope.users = $users;
        $scope.modals = $modals;
        $scope.kolenergo = $kolenergo;

        $scope.newUserGroup = $factory({ classes: ["UserGroup", "Model", "Backup", "States"], base_class: "UserGroup" });


        $scope.gotoAddUser = function () {
            $location.url("/users/new");
        };

        $scope.openNewUserGroupModal = function () {
            $modals.open("new-user-group");
            //$scope.newUserGroup._backup_.restore();
            $scope.newUserGroup._backup_.setup();

        };



        $scope.closeNewUserGroupModal = function () {
            $log.log("close");
            $scope.newUserGroup._backup_.restore();
        };



        $scope.addUserGroup = function () {
            var params = {
                title: $scope.newUserGroup.title.value
            };
            $http.post("/serverside/libs/krypton/api.php", { action: "addUserGroup", data: params })
                .success(function (data) {
                    if (data !== undefined) {
                        var group = $factory({ classes: ["UserGroup", "Model", "Backup", "States"], base_class: "UserGroup" });
                        group._model_.fromAnother(data);
                        group._backup_.setup();
                        $users.addGroup(group);
                        $modals.close();
                    }
                });
        };



        $scope.openEditUserGroupModal = function () {
            $modals.open("edit-user-group");
        };



        $scope.closeEditUserGroupModal = function () {
            $users.groups.getCurrent()._backup_.restore();
            $users.groups.getCurrent()._states_.changed(false);
        };



        $scope.editUserGroup = function () {
            var params = {
                id: $users.groups.getCurrent().id.value,
                title: $users.groups.getCurrent().title.value
            };
            $http.post("/serverside/libs/krypton/api.php", { action: "editUserGroup", data: params })
                .success(function (data) {
                    if (data !== undefined) {
                        var group = $factory({ classes: ["UserGroup", "Model", "Backup", "States"], base_class: "UserGroup" });
                        group._model_.fromAnother(data);
                        $users.groups.getCurrent()._model_.fromAnother(group);
                        $users.groups.getCurrent()._backup_.setup();
                        $modals.close();
                    }
                });
        };



        /**
         * Открывает модальное окно удаления группы пользователей
         */
        $scope.openDeleteUserGroupModal = function () {
            $modals.open("delete-user-group-modal");
        };



        /**
         * Удаляет группу пользователей
         */
        $scope.deleteUserGroup = function () {
            var params = {
                id: $users.groups.getCurrent().id.value
            };
            $http.post("/serverside/libs/krypton/api.php", {action: "deleteUserGroup", data : params })
                .success(function (data) {
                    $log.log(data);
                    if (data !== undefined && data !== null) {
                        if (JSON.parse(data) === true) {
                            $users.groups.delete(params.id);
                            $modals.close("delete-user-group-modal");
                        }
                    }
                });
        };
    };




    function newUserController ($scope, $log, $users) {

    };




    function settingsController ($log, $scope, $settings) {
        $scope.settings = $settings;
    };

    
    
    function filesController ($scope, $log, $factory) {
        
    };


    function AdminTelephonesController ($log, $scope, $users) {
        $scope.user = $users;
    };




    function kryptonAdminRun ($log, $location, $navigation, $factory, $rootScope, $users, $http, $settings, $session) {
        $log.log("krypton admin run");
        $log.log(window.krypton);
        //$location.url("/users");
        $rootScope.navigation = $navigation;
        $rootScope.session = $session;

        $session.init();
        $settings.init();
        $users.init();
        
        
        var temp = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp.init({
            url: "/users",
            templateUrl: "../../templates/admin/users/users.html",
            controller: usersController,
            title: "Пользователи",
            description: "Управление пользователями",
            icon: "fa-user"
        });
        $navigation.add(temp);

        var temp2 = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp2.init({
            url: "/dashboard",
            templateUrl: "../../templates/admin/dashboard/dashboard.html",
            controller: dashboardController(),
            title: "Дашборд",
            description: "Панель управления",
            icon: "fa-home",
            isDefault: true
        });
        $navigation.add(temp2);

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    url: "/settings",
                    templateUrl: "../../templates/admin/settings/settings.html",
                    controller: settingsController,
                    title: "Настройки",
                    description : "Управление настройками системы и модулей",
                    icon: "fa-cog"
                }));

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    url: "/phones",
                    templateUrl: "../../templates/admin/kolenergo/phones.html",
                    controller: AdminTelephonesController,
                    title: "Тел. справочник",
                    description : "Управление настройками системы и модулей",
                    icon: "fa-phone"
                }));


        $http.post("../../serverside/libs/krypton/api.php", { action: "test", data: {} })
            .success(function (data) {
                $log.log(data);
            });

        $log.log(window.krypton);
    };
})();