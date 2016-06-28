"use strict";

(function () {
    angular
        .module("krypton.app.kolenergo", ["krypton"])
        .factory("$kolenergo", kolenergoFactory)
        .run(kolenergoRun)
        .controller("UserAccountController", UserAccountController);



    function kolenergoFactory ($log, $classes, $factory, $errors, $navigation) {
        /**
         * Department
         * Набор свойств и методов, описывающих производственное отделение
         */
        $classes.add("Department", {
            __dependencies__: [],
            __icon__: "",
            id: new Field({ source: "id", type: "integer", value: 0, default_value: 0 }),
            title: new Field({ source: "title", type: "string", value: "", default_value: "", backupable: true })
        });

        /**
         * Division
         * Набор свойств и методов, описывающих структурное подразделение
         */
        $classes.add("Division", {
            __dependencies__: [],
            id: new Field({ source: "id", type: "integer", value: 0, default_value: 0 }),
            departmentId: new Field({ source: "department_id", type: "integer", value: 0, default_value: 0, backupable: true }),
            parentId: new Field({ source: "parent_id", type: "integer", value: 0, default_value: 0, backupable: true }),
            title: new Field({ source: "title", type: "integer", value: 0, default_value: 0, backupable: true })
        });

        var departments = [];
        var divisions = [];

        return {
            init: function () {
                if (window.krypton !== null && window.krypton !== undefined) {
                    if (window.krypton.departments !== null && window.krypton.departments !== undefined) {
                        var length = window.krypton.departments.length;
                        for (var i = 0; i < length; i++) {
                            var department = $factory({ classes: ["Department", "Model", "Backup", "States"], base_class: "Department" });
                            department._model_.fromAnother(window.krypton.departments[i]);
                            department._backup_.setup();
                            departments.push(department);
                        }
                        $log.log("departments = ", departments);
                    }
                }

                $classes.getAll().User.departmentId = new Field({ source: "departementId", type: "integer", value: 0, default_value: 0, backupable: true, displayable: true, title: "Произв. отделение" });

                var temp = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
                temp.init({
                    url: "/company",
                    templateUrl: "../../templates/admin/kolenergo/company.html",
                    controller: companyController,
                    title: "Структура организации",
                    description: "Управление организационной структурой предприятия"
                });
                $navigation.add(temp);
            },
            
            getDepartments: function () {
                return departments;
            },

            getDepartmentById: function (id) {
                if (id !== undefined) {
                    var length = departments.length;
                    for (var i = 0; i < length; i++) {
                        if (departments[i].id.value === id)
                            return departments[i];
                    }
                    return false;
                } else {  
                    $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> getDepartmentById: Не задан параметр - идентификатор производственного отделения");
                    return false
                }
            },

            getDivisions: function () {
                return divisions;
            }
        }
    };



    function kolenergoRun ($log, $kolenergo, $classes) {
        $log.log("krypton.app.kolenergo run...");
        $kolenergo.init();
    };


    function UserAccountController ($log, $scope, $session, $kolenergo) {
        $scope.user = $session.getCurrentUser();
        $scope.kolenergo = $kolenergo;


        $scope.department = {
            editing: false,
            loading: false,
            edit: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.editing = flag;
                else
                    return this.editing;
            },
            cancel: function () {
                if (this.edit() === true) {
                    this.edit(false);
                    $scope.user.departmentId._change_(false);
                    $scope.user.departmentId._backup_();
                }
            },
            save: function () {
                if (this.edit() === true) {

                }
            }
        };
        
        
        
        $scope.position = {
            editing: false,
            adding: false,
            loading: false,
            edit: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.editing = flag;
                else
                    return this.editing;
            },
            cancel: function () {
                if (this.edit() === true) {
                    this.edit(false);
                    $scope.user.position._change_(false);
                    $scope.user.position._backup_();
                }
            },
            save: function () {
                if (this.edit() === true) {

                }
            }
        };



        $scope.phone = {
            editing: false,
            adding: false,
            loading: false,
            new: "",
            edit: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.editing = flag;
                else
                    return this.editing;
            },
            add: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.adding = flag;
                else
                    return this.adding;
            },
            cancel: function () {
                if (this.edit() === true) {
                    this.edit(false);
                    $scope.user.phone._change_(false);
                    $scope.user.phone._backup_();
                }
                if (this.add() === true) {
                    this.add(false);
                    this.new = "";
                }
            },
            save: function () {
                if (this.edit() === true) {

                }
                if (this.add() === true) {

                }
            }
        };


        $scope.mobile = {
            editing: false,
            adding: false,
            loading: false,
            new: "",
            edit: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.editing = flag;
                else
                    return this.editing;
            },
            add: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.adding = flag;
                else
                    return this.adding;
            },
            cancel: function () {
                if (this.edit() === true) {
                    this.edit(false);
                    $scope.user.mobile._change_(false);
                    $scope.user.mobile._backup_();
                }
                if (this.add() === true) {
                    this.add(false);
                    this.new = "";
                }
            },
            save: function () {
                if (this.edit() === true) {

                }
                if (this.add() === true) {

                }
            }
        };





        $scope.onChange = function () {
            $log.log("onChange from scope, ", $scope.user.position.value);
        };

        $scope.cancel = function () {
            $log.log("cancel called");
            $scope.user._states_.editing(false);
        };
    };




    function companyController ($scope) {
        $scope.company = [
            {
                id: 1, 
                parentId: 0
            }, 
            {id: 2, parentId: 0}, 
            {id: 3, parentId: 0}, 
            {id: 4, parentId: 1}, 
            {id: 5, parentId: 1}, 
            {id: 6, parentId: 1}, 
            {id: 7, parentId: 2}, 
            {id: 8, parentId: 2}, 
            {id: 9, parentId: 2}, 
            {id: 10, parentId: 3}
        ];
    };
})();
