"use strict";


(function () {
    angular
        .module("krypton.admin", ["ngRoute", "ngCookies", "ngSanitize", "angularFileUpload", "krypton", "krypton.ui", "krypton.app.kolenergo"])
        .config(function ($routeProvider,$sceProvider) {
            $sceProvider.enabled(false);

            $routeProvider
                .when("/users/new",
                    {
                        templateUrl: "../templates/admin/users/new-user.html",
                        controller: newUserController
                    }
                )
                .when("/users/:userId",
                    {
                        templateUrl: "../templates/admin/users/edit-user.html",
                        controller: AdminEditUserController
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
        
        $scope.gotoEditUser = function () {
            $location.url("/users/" + $users.users.getCurrent().id.value);
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
    
    
    function AdminEditUserController ($scope, $log, $routeParams, $factory, $users, $navigation, $kolenergo) {
        $scope.users = $users;
        $scope.kolenergo = $kolenergo;


        if ($routeParams.userId !== undefined) {
            $log.log("params = ", $routeParams);
            if ($users.users.getCurrent() === undefined || $users.users.getCurrent().id.value !== parseInt($routeParams.userId)) {
                $users.users.select(parseInt($routeParams.userId));
                $scope.uploaderData = {
                    action: "uploadUserPhoto",
                    userId: $users.users.getCurrent().id.value
                };
            }

            $navigation.getCurrent().title = $users.users.getCurrent().name.value + " " + $users.users.getCurrent().surname.value;
            $log.log("current user = ", $users.users.getCurrent());
        }
        
        $scope.editUser = function () {
            var length = $users.users.getCurrent().phones.length;
            var phone = "";
            for (var i = 0; i < length; i++) {
                phone += $users.users.getCurrent().phones[i];
                phone += i < length - 1 ? "," : "";
            }
            $log.log("phone = ", phone);
            //$users.users.edit()
        };

        $scope.onCompleteUploadPhoto = function (data) {
            $log.log("photo upload complete");
            $log.log(data);
            //$http.post("serverside/libs/krypton/uploader.php", $scope.uploaderData)
            //    .success(function (data) {
            //        if (data !== undefined) {
            //            $log.log(data);
            //        }
            //    });
            if (data.result !== false) {
                var file = $factory({ classes: ["File", "Model", "States"], base_class: "File" });
                file._model_.fromAnother(data.result);
                $users.users.getCurrent().photo.value = file.url.value;
            }

        };
        
        $scope.addPhone = function () {
            $users.users.getCurrent().phones.length = $users.users.getCurrent().phones.length + 1;
        };
    };




    function settingsController ($log, $scope, $settings) {
        $scope.settings = $settings;
    };

    
    
    function filesController ($scope, $log, $factory) {
        
    };


    function AdminPermissionRulesController ($scope, $log, $permissions, $factory, $modals) {
        $scope.permissions = $permissions;
        $scope.newPermissionRule = $factory({ classes: ["PermissionRule", "Model", "Backup", "States"], base_class: "PermissionRule" });

        $scope.openAddPermissionRuleModal = function () {
            $scope.newPermissionRule._backup_.setup();
            $modals.open("add-permission-rule");
        };

        $scope.closeAddPermissionRuleModal = function () {
            $scope.newPermissionRule._backup_.restore();
        };

        $scope.addPermissionRule = function () {
            $permissions.rules.add($scope.newPermissionRule, function () {
                $scope.newPermissionRule._backup_.restore();
                $modals.close();
            });
        };

        $scope.openEditPermissionRuleModal = function () {
            $modals.open("edit-permission-rule");
        };

        $scope.closeEditPermissionRuleModal = function () {
            $permissions.rules.getCurrent()._backup_.restore();
        };
        
        $scope.editPermissionRule = function () {
            $permissions.rules.edit(function () {
                $modals.close();
            });
        };

    };


    function AdminTelephonesController ($log, $scope, $users) {
        $scope.user = $users;
    };




    function kryptonAdminRun ($log, $location, $navigation, $factory, $rootScope, $users, $permissions, $http, $settings, $session) {
        $log.log("krypton admin run");
        $log.log(window.krypton);
        //$location.url("/users");
        $rootScope.navigation = $navigation;
        $rootScope.session = $session;

        $session.init();
        $settings.init();
        $users.init();
        $permissions.init();
        
        
        var temp = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp.init({
            id: "users",
            parentId: "",
            url: "/users",
            templateUrl: "../../templates/admin/users/users.html",
            controller: usersController,
            title: "Пользователи",
            description: "Управление пользователями",
            icon: "fa-user"
        });
        $navigation.add(temp);

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    id: "edit-user",
                    parentId: "users",
                    url: "/users/:userId",
                    templateUrl: "../../templates/admin/users/edit-user.html",
                    controller: AdminEditUserController,
                    //resolve: {
                    //    "kolenergo": function ($kolenergo) {

                        //}
                    //},
                    title: "Редактирование пользователя",
                    description : "Редактирование данных пользователя"
                }));

        var temp2 = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp2.init({
            id: "dashboard",
            parentId: "",
            url: "/dashboard",
            templateUrl: "../../templates/admin/dashboard/dashboard.html",
            controller: dashboardController,
            title: "Дашборд",
            description: "Панель управления",
            icon: "fa-home",
            isDefault: true
        });
        $navigation.add(temp2);

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    id: "settings",
                    parentId: "",
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
                    id: "permissions",
                    parentId: "",
                    url: "/permissions",
                    templateUrl: "../../templates/admin/permissions/permissions.html",
                    controller: AdminPermissionRulesController,
                    title: "Контроль доступа",
                    description : "Управление правилами доступа к данным",
                    icon: "fa-shield"
                }));

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    id: "phones",
                    parentId: "",
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