"use strict";

(function () {
    angular
        .module("krypton.app.kolenergo", ["krypton"])
        .factory("$kolenergo", kolenergoFactory)
        .controller("UserAccountController", UserAccountController)
        .filter("byDepartmentId", byDepartmentIdFilter)
        .run(kolenergoRun);



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
            title: new Field({ source: "title", type: "string", value: "", default_value: "", backupable: true })
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

                    if (window.krypton.divisions !== null && window.krypton.divisions !== undefined) {
                        var length = window.krypton.divisions.length;
                        for (var i = 0; i < length; i++) {
                            var division = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });
                            division._model_.fromAnother(window.krypton.divisions[i]);
                            division._backup_.setup();
                            divisions.push(division);
                        }
                        $log.log("divisions = ", divisions);
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
            },

            getDivisionById: function (id) {
                if (id !== undefined) {
                    var length = divisions.length;
                    for (var i = 0; i < length; i++) {
                        if (divisions[i].id.value === id)
                            return divisions[i];
                    }
                    return false;
                } else {
                    $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> getDivisionById: Не задан параметр - идентификатор структурного подразделения");
                    return false
                }
            },

            addDivision: function (division) {
                if (division !== undefined) {
                    divisions.push(division);
                    return true;
                } else {
                    $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> addDivision: Не задан параметр - объект нового отдела");
                    return false
                }
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




    function companyController ($log, $scope, $kolenergo, $filter, $modals, $rootScope, $factory, $http) {
        $scope.kolenergo = $kolenergo;
        $scope.$modals = $modals;
        $scope.departments = $kolenergo.getDepartments();
        $scope.divisions = $kolenergo.getDivisions();
        $scope.currentDepartmentId = undefined;
        $scope.currentDivision = undefined;
        $scope.newDivision = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });
        $scope.newDivision._backup_.setup();



        $scope.onSelectDepartment = function (departmentId) {
            $log.log("onSelect fired");
            if (departmentId !== undefined) {
                var length = $kolenergo.getDepartments().length;
                for (var i = 0; i < length; i++) {
                    var department = $kolenergo.getDepartments()[i];
                    if (department.id.value === departmentId) {
                        department._states_.selected(true);
                        $scope.currentDepartment = department;
                    } else
                        department._states_.selected(false);
                }
                $scope.divisions = $filter("byDepartmentId")($scope.divisions, department.id.value);
                $log.log("filter = ", $scope.divisions);
            }
        };


        
        $scope.onSelectDivision = function (division) {
            if (division !== undefined) {
                //$log.log("onSelectHierarchyItem", division);
                $scope.currentDivision = division;
                $log.log("current division = ", $scope.currentDivision);
                $scope.newDivision.parentId.value = $scope.currentDivision.id.value;
            } else {
                $scope.currentDivision = undefined;
                $scope.newDivision.parentId.value = 0;
            }
        };


        
        $scope.onAddNewDivision = function () {
            $log.log("add division called");
            //$modals.open("test");
            var data = {
                title: $scope.newDivision.title.value,
                parentId: $scope.newDivision.parentId.value
            };
            $http.post("/serverside/libs/krypton/api.php", {action: "addDivision", data : data })
                .success(function (data) {
                    if (data !== undefined) {
                        $log.log(data);
                        var division = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });
                        division._model_.fromAnother(data);
                        division._backup_.setup();
                        $kolenergo.addDivision(division);
                        $modals.close("new-division-modal");
                    }
                });
        };


        $scope.company = [
            {
                id: 1, 
                parentId: 0,
                title: "Отдел " + this.id
            }, 
            {id: 2, parentId: 0, title: "Отдел " + this.id},
            {id: 3, parentId: 0, title: "Отдел " + this.id},
            {id: 4, parentId: 1, title: "Отдел " + this.id},
            {id: 5, parentId: 1, title: "Отдел " + this.id},
            {id: 6, parentId: 1, title: "Отдел " + this.id},
            {id: 7, parentId: 2, title: "Отдел " + this.id},
            {id: 8, parentId: 2, title: "Отдел " + this.id},
            {id: 9, parentId: 2, title: "Отдел " + this.id},
            {id: 10, parentId: 3, title: "Отдел " + this.id},
            {id: 11, parentId: 10, title: "Отдел " + this.id}
        ];
        


        $scope.onAddDivision = function () {
            $modals.open("new-division-modal");
        };

        $scope.onCancelAddNewDivision = function () {
            $log.log("closed");
            $scope.newDivision._backup_.restore();
            if ($scope.currentDivision !== undefined)
                $scope.newDivision.parentId.value = $scope.currentDivision.id.value;
            //$modals.close("test-modal");
        };


        $scope.onEditDivision = function () {
            $modals.open("edit-division-modal");
        };

        $scope.editDivision = function () {
            var data = {
                id: $scope.currentDivision.id.value,
                title: $scope.currentDivision.title.value,
                parentId: $scope.currentDivision.parentId.value
            };
            $http.post("/serverside/libs/krypton/api.php", {action: "editDivision", data : data })
                .success(function (data) {
                    if (data !== undefined) {
                        $log.log(data);
                        var division = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });
                        division._model_.fromAnother(data);
                        $scope.currentDivision._model_.fromAnother(division);
                        $scope.currentDivision._backup_.setup();
                        $log.log("updated division = ", $scope.currentDivision);
                        $modals.close("edit-division-modal");
                    }
                });
        };

        $scope.onCancelEditDivision = function () {
            $scope.currentDivision._backup_.restore();
            $scope.currentDivision._states_.changed(false);
        };
    };


    function byDepartmentIdFilter () {
        return function (input, id) {
            if (id !== undefined || id !== 0) {
                var result = [];
                var length = input.length;
                for (var i = 0; i < length; i++) {
                    if (input[i].departmentId.value === id)
                        result.push(input[i]);
                }
                return result;
            }
        }
    };
})();
