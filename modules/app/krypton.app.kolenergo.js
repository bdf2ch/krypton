"use strict";

(function () {
    angular
        .module("krypton.app.kolenergo", ["krypton"])
        .factory("$kolenergo", kolenergoFactory)
        .controller("UserAccountController", UserAccountController)
        .controller("LoginController", LoginController)
        .controller("PhoneBookController", PhoneBookController)
        .filter("byOrganizationId", byOrganizationIdFilter)
        .filter("byDepartmentId", byDepartmentIdFilter)
        .filter("byDivisionId", byDivisionIdFilter)
        .filter("byATSId", byATSIdFilter)
        .filter("byUserId", byUserIdFilter)
        .filter("phoneBook", phoneBookFilter)
        .directive("notZero", notZeroDirective)
        .run(kolenergoRun);



    function kolenergoFactory ($log, $classes, $factory, $errors, $navigation, $http) {
        /**
         * Organization
         * Набор свойств и методов, описывающих организацию
         */
        $classes.add("Organization", {
            __dependencies__: [],
            __icon__: "",
            id: new Field ({ source: "ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            title: new Field ({ source: "TITLE", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true }),
            subnetMask: new Field({ source: "SUBNET_MASK", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true })
        });

        /**
         * Department
         * Набор свойств и методов, описывающих производственное отделение
         */
        $classes.add("Department", {
            __dependencies__: [],
            __icon__: "",
            id: new Field({ source: "ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            organizationId: new Field({ source: "ORGANIZATION_ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true }),
            title: new Field({ source: "TITLE", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true })
        });

        /**
         * Division
         * Набор свойств и методов, описывающих структурное подразделение
         */
        $classes.add("Division", {
            __dependencies__: [],
            id: new Field({ source: "ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            organizationId: new Field({ source: "ORGANIZATION_ID", type: DATA_TYPE_INTEGER, default_value: 0, value: 0, backupable: true }),
            departmentId: new Field({ source: "DEPARTMENT_ID", type: DATA_TYPE_STRING, value: 0, default_value: 0, backupable: true }),
            parentId: new Field({ source: "PARENT_ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true }),
            title: new Field({ source: "TITLE", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true }),
            path: new Field({ source: "PATH", type: DATA_TYPE_STRING, default_value: "", value: "", backupable: true })
        });

        /**
         * ATS
         * Набор свойств и методов, описывающих АТС
         */
        $classes.add("ATS", {
            __dependencies__: [],
            id: new Field({ source: "ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            organizationId: new Field({ source: "ORGANIZATION_ID", type: DATA_TYPE_INTEGER, default_value: 0, value: 0, backupable: true }),
            title: new Field({ source: "TITLE", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true })
        });

        /**
         * ATSCode
         * Набор свойств и методов, описывающихкод выхода с одной АТС на другую
         */
        $classes.add("ATSCode", {
            __dependencies__: [],
            id: new Field({ source: "ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            atsId: new Field({ source: "ATS_ID", type: DATA_TYPE_INTEGER, default_value: 0, value: 0, backupable: true }),
            targetAtsId: new Field({ source: "TARGET_ATS_ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true }),
            code: new Field({ source: "CODE", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true })
        });

        /**
         * Phone
         * Набор свойств и методов, описывающихкод выхода с одной АТС на другую
         */
        $classes.add("Phone", {
            __dependencies__: [],
            id: new Field({ source: "ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            atsId: new Field({ source: "ATS_ID", type: DATA_TYPE_INTEGER, default_value: 0, value: 0, backupable: true }),
            userId: new Field({ source: "USER_ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true }),
            phone: new Field({ source: "PHONE", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true })
        });

        var organizations = [];
        var departments = [];
        var divisions = [];
        var ats = [];
        var codes = [];
        var phones = [];

        var currentOrganization = undefined;
        var newOrganization = $factory({ classes: ["Organization", "Model", "Backup", "States"], base_class: "Organization" });
        var currentDepartment = undefined;
        var newDepartment = $factory({ classes: ["Department", "Model", "Backup", "States"], base_class: "Department" });
        var currentDivision = undefined;
        var newDivision = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });
        var currentATS = undefined;
        var newATS = $factory({ classes: ["ATS", "Model", "Backup", "States"], base_class: "ATS" });
        var currentATSCode = undefined;
        var newATSCode = $factory({ classes: ["ATSCode", "Model", "Backup", "States"], base_class: "ATSCode" });
        var currentPhone = undefined;
        var newPhone = $factory({ classes: ["Phone", "Model", "Backup", "States"], base_class: "Phone" });
        
        return {


            init: function () {
                newOrganization._backup_.setup();
                newDepartment._backup_.setup();
                newDivision._backup_.setup();
                newATS._backup_.setup();
                newATSCode._backup_.setup();
                newPhone._backup_.setup();

                $classes.getAll().User.organizationId = new Field({ source: "ORGANIZATION_ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true });
                $classes.getAll().User.departmentId = new Field({ source: "DEPARTMENT_ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true });
                $classes.getAll().User.divisionId = new Field({ source: "DIVISION_ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true });
                $classes.getAll().User.atsId = new Field({ source: "ATS_ID", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true });

                if (window.krypton !== null && window.krypton !== undefined) {
                    if (window.krypton.organizations !== null && window.krypton.organizations !== undefined) {
                        var length = window.krypton.organizations.length;
                        for (var i = 0; i < length; i++) {
                            var organization = $factory({ classes: ["Organization", "Model", "Backup", "States"], base_class: "Organization" });
                            organization._model_.fromAnother(window.krypton.organizations[i]);
                            organization._backup_.setup();
                            //$log.log(organization._backup_.data);
                            organizations.push(organization);
                        }
                        $log.log("organizations = ", organizations);
                    }

                    if (window.krypton.departments !== null && window.krypton.departments !== undefined) {
                        var length = window.krypton.departments.length;
                        for (var i = 0; i < length; i++) {
                            var department = $factory({ classes: ["Department", "Model", "Backup", "States"], base_class: "Department" });
                            department._model_.fromAnother(window.krypton.departments[i]);
                            department._backup_.setup();
                            $log.log(department._backup_.data);
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

                    if (window.krypton.ats !== null && window.krypton.ats !== undefined) {
                        var length = window.krypton.ats.length;
                        for (var i = 0; i < length; i++) {
                            var temp = $factory({ classes: ["ATS", "Model", "Backup", "States"], base_class: "ATS" });
                            temp._model_.fromAnother(window.krypton.ats[i]);
                            temp._backup_.setup();
                            ats.push(temp);
                        }
                        $log.log("ats = ", ats);
                    }

                    if (window.krypton.atsCodes !== null && window.krypton.atsCodes !== undefined) {
                        var length = window.krypton.atsCodes.length;
                        for (var i = 0; i < length; i++) {
                            var temp = $factory({ classes: ["ATSCode", "Model", "Backup", "States"], base_class: "ATSCode" });
                            temp._model_.fromAnother(window.krypton.atsCodes[i]);
                            temp._backup_.setup();
                            codes.push(temp);
                        }
                        $log.log("ats codes = ", codes);
                    }
                }

                //$classes.getAll().User.departmentId = new Field({ source: "departementId", type: "integer", value: 0, default_value: 0, backupable: true, displayable: true, title: "Произв. отделение" });

                var temp = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
                temp.init({
                    id: "companies",
                    parentId: "",
                    url: "/company",
                    templateUrl: "../../templates/admin/kolenergo/company.html",
                    controller: companyController,
                    title: "Организации",
                    description: "Управление структурой организаций",
                    icon: "fa-building"
                });
                $navigation.add(temp);
            },
            

            departments: {

                /**
                 * Возвращает массив всех производственных отделений
                 * @returns {Array}
                 */
                getAll: function () {
                    return departments;
                },

                /**
                 * Возвращает текущее производственное отделение
                 * @returns {Department / undefined}
                 */
                getCurrent: function () {
                    return currentDepartment;
                },

                /**
                 * Возвращает новое производственное отделение
                 * @returns {*}
                 */
                getNew: function () {
                    return newDepartment;
                },

                /**
                 * Выбирает производственное отделение по идентификатору
                 * @param departmentId {number} - идентификатор производственного отделения
                 * @returns {boolean}
                 */
                select: function (departmentId) {
                    if (departmentId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> departments -> select: Не задан параметр - идентификатор производственного отделения");
                        return false;
                    }

                    var length = departments.length;
                    for (var i = 0; i < length; i++) {
                        if  (departments[i].id.value === departmentId) {
                            if (departments[i]._states_.selected() === false) {
                                departments[i]._states_.selected(true);
                                currentDepartment = departments[i];
                                newDivision.departmentId.value = departmentId;
                            } else {
                                departments[i]._states_.selected(false);
                                currentDepartment = undefined;
                                newDivision.departmentId.value = 0;
                            }
                        } else
                            departments[i]._states_.selected(false);
                    }

                    $log.log("current dep = ", currentDepartment);

                    return true;
                },

                /**
                 * Добавляет новое производственное отделение
                 * @param department {Department} - объект с информацией о новом производственном отделении
                 * @param callback {function} - callback-функция
                 * @returns {boolean}
                 */
                add: function (callback) {
                    /*
                    if (department === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> departments -> add: Не задан параметр - объект с информацией о новом производственном отделении");
                        return false;
                    }
                    */

                    var params = {
                        action: "addDepartment",
                        organizationId: newDepartment.organizationId.value,
                        title: newDepartment.title.value
                    };

                    newDepartment._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            newDepartment._states_.loading(false);
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    var department = $factory({ classes: ["Department", "Model", "Backup", "States"], base_class: "Department" });
                                    department._model_.fromAnother(data.result);
                                    department._backup_.setup();
                                    departments.push(department);
                                    newDepartment.title.value = "";
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback(department);

                                    return true;
                                }
                            }
                        });

                    return false;    
                },

                /**
                 * Сохраняет изменения отредактированного производственного отделения
                 * @param callback {function} - callback-функция
                 * @returns {boolean}
                 */
                edit: function (callback) {
                    if (currentDepartment === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> departments -> edit: Не выбрано текущее производственное отделение");
                        return false;
                    }

                    var params = {
                        action: "editDepartment",
                        id: currentDepartment.id.value,
                        organizationId: currentDepartment.organizationId.value,
                        title: currentDepartment.title.value
                    };

                    currentDepartment._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    currentDepartment._backup_.setup();
                                    currentDepartment._states_.loading(false);
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback();
                                    return true;
                                }
                            }
                        });

                    return false;
                },

                /**
                 * Удаляет производственное отделение с заданным идентификатором
                 * @param id {number} - идентификатор производственного отделения
                 * @returns {boolean}
                 */
                delete: function (callback) {
                    if (currentDepartment === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> departments -> delete: Не выбрано текущее производственное отделение");
                        return false;
                    }

                    var params = {
                        action: "deleteDepartment",
                        id: currentDepartment.id.value
                    };

                    currentDepartment._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            currentDepartment._states_.loading(false);
                            $log.log(data);
                            if (data !== undefined && data !== null) {
                                $errors.checkResponse(data);
                                if (data.result !== false) {
                                    if (JSON.parse(data.result) === true) {
                                        var length = departments.length;
                                        for (var i = 0; i < length; i++) {
                                            if (departments[i].id.value === currentDepartment.id.value) {
                                                currentDepartment._states_.loading(false);
                                                currentDepartment = undefined;
                                                departments.splice(i, 1);
                                                if (callback !== undefined && typeof callback === "function")
                                                    callback();
                                                return true;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                }
            },

            
            organizations: {
                /**
                 * Возвращает массив всех организаций
                 * @returns {Array}
                 */
                getAll: function () {
                    return organizations;
                },

                /**
                 * Возвращает текущую организацию
                 * @returns {undefined}
                 */
                getCurrent: function () {
                    return currentOrganization;
                },

                getNew: function () {
                    return newOrganization;
                },

                getById: function (organizationId) {
                    if (organizationId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> organizations -> getById: Не задан параметр - идентификатор организации");
                        return false;
                    }

                    var length = organizations.length;
                    for (var i = 0; i < length; i++) {
                        if (organizations[i].id.value === organizationId)
                            return organizations[i];
                    }

                    return false;
                },

                /**
                 * Выбирает организацию по идентификатору
                 * @param organizationId {number} - идентификатор организации
                 * @callback {function} - callback-функция
                 * @returns {boolean}
                 */
                select: function (organizationId, callback) {
                    if (organizationId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> organizations -> select: Не задан параметр - идентификатор организации");
                        return false;
                    }

                    var length = organizations.length;
                    for (var i = 0; i < length; i++) {
                        if  (organizations[i].id.value === organizationId) {
                            if (organizations[i]._states_.selected() === false) {
                                organizations[i]._states_.selected(true);
                                currentOrganization = organizations[i];
                                newDepartment.organizationId.value = currentOrganization.id.value;
                                newDivision.organizationId.value = currentOrganization.id.value;
                                newATS.organizationId.value = currentOrganization.id.value;
                            } else {
                                organizations[i]._states_.selected(false);
                                currentOrganization = undefined;
                                newDepartment.organizationId.value = 0;
                                newDivision.organizationId.value = 0;
                                newATS.organizationId.value = 0;
                            }
                        } else
                            organizations[i]._states_.selected(false);
                    }

                    $log.log("current org = ", currentOrganization);
                    if (callback !== undefined && typeof callback === "function")
                        callback(currentOrganization);

                    return true;
                },

                /**
                 * Добавляет новую организацию
                 * @callback {function} - callback-функция
                 * @returns {Organization / boolean}
                 */
                add: function (callback) {
                    /*
                    if (organization === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> organizations -> add: Не задан параметр - объект с информацией о новой организации");
                        return false;
                    }
                    */

                    var params = {
                        action: "addOrganization",
                        title: newOrganization.title.value,
                        subnetMask: newOrganization.subnetMask.value
                    };

                    newOrganization._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            newOrganization._states_.loading(false);
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    var organization = $factory({ classes: ["Organization", "Model", "Backup", "States"], base_class: "Organization" });
                                    organization._model_.fromAnother(data.result);
                                    organization._backup_.setup();
                                    organizations.push(organization);
                                    newOrganization._backup_.restore();
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback(organization);

                                    return true;
                                }
                            }
                        });

                    return false;
                },

                edit: function (callback) {
                    if (currentOrganization === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> organizations -> edit: Не выбрана текущая организация");
                        return false;
                    }

                    var params = {
                        action: "editOrganization",
                        id: currentOrganization.id.value,
                        title: currentOrganization.title.value,
                        subnetMask: currentOrganization.subnetMask.value
                    };

                    currentOrganization._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    currentOrganization._backup_.setup();
                                    currentOrganization._states_.loading(false);
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback();
                                    return true;
                                }
                            }
                        });

                    return false;
                },

                /**
                 * Удаляет организацию с заданным идентификатором
                 * @param id {number} - идентификатор организации
                 * @returns {boolean}
                 */
                delete: function (callback) {
                    if (currentOrganization === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> organizations -> delete: Не выбрана текущая организация");
                        return false;
                    }

                    var params = {
                        action: "deleteOrganization",
                        id: currentOrganization.id.value
                    };

                    currentOrganization._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            currentOrganization._states_.loading(false);
                            $log.log(data);
                            if (data !== undefined && data !== null) {
                                $errors.checkResponse(data);
                                if (data.result !== false) {
                                    if (JSON.parse(data.result) === true) {
                                        var length = organizations.length;
                                        for (var i = 0; i < length; i++) {
                                            if (organizations[i].id.value === currentOrganization.id.value) {
                                                currentOrganization._states_.loading(false);
                                                currentOrganization = undefined;
                                                organizations.splice(i, 1);
                                                if (callback !== undefined && typeof callback === "function")
                                                    callback();
                                                return true;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                },

            },

            divisions: {

                /**
                 * Возвращает массив всех отделов
                 * @returns {Array}
                 */
                getAll: function () {
                    return divisions;
                },

                getById: function (id) {
                    if (id === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> divisions -> getById: Не задана параметр - идентификатор структурного подразделения");
                        return false;
                    }

                    var length = divisions.length;
                    for (var i = 0; i < length; i++) {
                        if (divisions[i].id.value === id)
                            return divisions[i];
                    }

                    return false;
                },

                getByOrganizationId: function (organizationId, callback) {
                    $log.log("org id = ", organizationId);
                    if (organizationId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> divisions -> getByOrganizationId: Не задан параметр - идентификатор организации");
                        return false;
                    }

                    var result = [];
                    var length = divisions.length;
                    for (var i = 0; i < length; i++) {
                        if (divisions[i].organizationId.value === organizationId)
                            result.push(divisions[i]);
                    }

                    if (callback !== undefined && typeof callback === "function")
                        callback(result);

                    return true;
                },

                /**
                 * Возвращает текущий отдел
                 * @returns {Division / undefined}
                 */
                getCurrent: function () {
                    return currentDivision;
                },

                /**
                 * Возвращает новое структурное подразделение
                 * @returns {Division}
                 */
                getNew: function () {
                    return newDivision;
                },

                /**
                 * Выбирает отдел по идентификатору
                 * @param divisionId {number} - идентификатор отдела
                 * @returns {boolean}
                 */
                select: function (divisionId) {
                    if (divisionId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> divisions -> select: Не задан параметр - идентификатор отдела");
                        return false;
                    }

                    var length = divisions.length;
                    for (var i = 0; i < length; i++) {
                        if  (divisions[i].id.value === divisionId) {
                            $log.log("div found", divisions[i]);
                            if (divisions[i]._states_.selected() === false) {
                                divisions[i]._states_.selected(true);
                                currentDivision = divisions[i];
                                newDivision.parentId.value = divisionId;
                                $log.log("cur div = ", currentDivision);
                            } else {
                                divisions[i]._states_.selected(false);
                                currentDivision = undefined;
                                newDivision.parentId.value = 0;
                            }
                        } else
                            divisions[i]._states_.selected(false);
                    }

                    return true;
                },

                /**
                 * Добавляет новый отдел
                 * @returns {boolean}
                 */
                add: function (callback) {
                    /*
                    if (division == undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> divisions -> add: Не задан параметр - объект с информацией о новом отделе");
                        return false;
                    }
                    */

                    var params = {
                        action: "addDivision",
                        organizationId: newDivision.organizationId.value,
                        departmentId: newDivision.departmentId.value,
                        parentId: newDivision.parentId.value,
                        title: newDivision.title.value,
                        path: newDivision.path.value
                    };

                    newDivision._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            newDivision._states_.loading(false);
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    var division = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });
                                    division._model_.fromAnother(data.result);
                                    division._backup_.setup();
                                    divisions.push(division);
                                    newDivision.title.value = "";
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback(division);
                                    return true;
                                }
                            }
                        });

                    return false;
                },

                edit: function (callback) {
                    if (currentDivision === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> divisions -> edit: Не выбрано текущее структурное подразделение");
                        return false;
                    }

                    var params = {
                        action: "editDivision",
                        id: currentDivision.id.value,
                        departmentId: currentDivision.departmentId.value,
                        parentId: currentDivision.parentId.value,
                        title: currentDivision.title.value
                    };

                    currentDivision._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    currentDivision._backup_.setup();
                                    currentDivision._states_.loading(false);
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback(currentDivision);
                                    return true;
                                }
                            }
                        });

                    return false;
                },

                /**
                 * Удаляет структурное подразделение с заданным идентификатором
                 * @callback {function} - callback-функция
                 * @returns {boolean}
                 */
                delete: function (callback) {
                    if (currentDivision === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> divisions -> delete: Не выбрано текущее структурное подразделение");
                        return false;
                    }

                    var params = {
                        action: "deleteDivision",
                        id: currentDivision.id.value
                    };

                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            currentDivision._states_.loading(false);
                            $log.log(data);
                            if (data !== undefined && data !== null) {
                                $errors.checkResponse(data);
                                if (data.result !== false) {
                                    if (JSON.parse(data.result) === true) {
                                        var length = divisions.length;
                                        for (var i = 0; i < length; i++) {
                                            if (divisions[i].id.value === currentDivision.id.value) {
                                                currentDivision._states_.loading(false);
                                                currentDivision = undefined;
                                                divisions.splice(i, 1);
                                                if (callback !== undefined && typeof callback === "function")
                                                    callback();
                                                return true;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                }
            },

            ats: {
                getAll: function () {
                    return ats;
                },

                getCurrent: function () {
                    return currentATS;
                },

                getNew: function () {
                    return newATS;
                },

                getById: function (atsId) {
                    if (atsId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> ats -> getById: Не задан идентификатор АТС");
                        return false;
                    }

                    var length = ats.length;
                    for (var i = 0; i < length; i++) {
                        if (ats[i].id.value === atsId)
                            return ats[i];
                    }

                    return false;
                },

                select: function (atsId) {
                    if (atsId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> ats -> select: Не задан параметр - идентификатор АТС");
                        return false;
                    }

                    var length = ats.length;
                    for (var i = 0; i < length; i++) {
                        if  (ats[i].id.value === atsId) {
                            $log.log("div found", ats[i]);
                            if (ats[i]._states_.selected() === false) {
                                ats[i]._states_.selected(true);
                                currentATS = ats[i];
                                newATSCode.atsId.value = currentATS.id.value;
                                $log.log("cur ats = ", currentATS);
                            } else {
                                ats[i]._states_.selected(false);
                                currentATS = undefined;
                                newATSCode.atsId.value = 0;
                            }
                        } else
                            ats[i]._states_.selected(false);
                    }

                    return true;
                },

                add: function (callback) {
                    var params = {
                        action: "addATS",
                        organizationId: newATS.organizationId.value,
                        title: newATS.title.value
                    };

                    newATS._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            newATS._states_.loading(false);
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    var temp = $factory({ classes: ["ATS", "Model", "Backup", "States"], base_class: "ATS" });
                                    temp._model_.fromAnother(data.result);
                                    temp._backup_.setup();
                                    ats.push(temp);
                                    newATS._backup_.restore();
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback(temp);
                                    return true;
                                }
                            }
                        });

                    return false;
                },

                edit: function (callback) {
                    if (currentATS === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> ats -> edit: Не выбрана текущая АТС");
                        return false;
                    }

                    var params = {
                        action: "editATS",
                        id: currentATS.id.value,
                        title: currentATS.title.value
                    };

                    currentATS._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    currentATS._backup_.setup();
                                    currentATS._states_.loading(false);
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback();
                                    return true;
                                }
                            }
                        });

                    return false;
                }
            },

            codes: {
                getAll: function () {
                    return codes;
                },

                getCurrent: function () {
                    return currentATSCode;
                },

                getNew: function () {
                    return newATSCode;
                },

                select: function (codeId) {
                    if (codeId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> codes -> select: Не задан параметр - идентификатор кода АТС");
                        return false;
                    }

                    var length = codes.length;
                    for (var i = 0; i < length; i++) {
                        if  (codes[i].id.value === codeId) {
                            $log.log("code found", codes[i]);
                            if (codes[i]._states_.selected() === false) {
                                codes[i]._states_.selected(true);
                                currentATSCode = codes[i];
                                $log.log("cur code = ", currentATSCode);
                            } else {
                                codes[i]._states_.selected(false);
                                currentATSCode = undefined;
                            }
                        } else
                            codes[i]._states_.selected(false);
                    }

                    return true;
                },

                add: function (callback) {
                    var params = {
                        action: "addATSCode",
                        atsId: newATSCode.atsId.value,
                        targetAtsId: newATSCode.targetAtsId.value,
                        code: newATSCode.code.value
                    };

                    newATSCode._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            newATSCode._states_.loading(false);
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    var temp = $factory({ classes: ["ATSCode", "Model", "Backup", "States"], base_class: "ATSCode" });
                                    temp._model_.fromAnother(data.result);
                                    temp._backup_.setup();
                                    codes.push(temp);
                                    newATSCode._backup_.restore();
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback(temp);
                                    return true;
                                }
                            }
                        });

                    return false;
                },

                edit: function (callback) {
                    if (currentATSCode === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> codes -> edit: Не выбран текущий код АТС");
                        return false;
                    }

                    var params = {
                        action: "editATSCode",
                        id: currentATSCode.id.value,
                        targetAtsId: currentATSCode.targetAtsId.value,
                        code: currentATSCode.code.value
                    };

                    currentATSCode._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    currentATSCode._backup_.setup();
                                    currentATSCode._states_.loading(false);
                                    if (callback !== undefined && typeof callback === "function")
                                        callback();
                                    return true;
                                }
                            }
                        });

                    return false;
                }
            },

            phones: {
                getAll: function () {
                    return phones;
                },

                getCurrent: function () {
                    return currentPhone;
                },

                getNew: function () {
                    return newPhone;
                },

                getByUserId: function (userId) {
                    if (userId === null) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> phones -> getByUserId: Не задан параметр - идентификатор пользователя");
                        return false;
                    }

                    var result = [];
                    var length = phones.length;
                    for (var i = 0; i < length; i++) {
                        if (phones[i].userId.value === userId)
                            result.push(phones[i]);
                    }

                    return result;
                },

                select: function (phoneId) {
                    if (phoneId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> phones -> select: Не задан параметр - идентификатор телефона");
                        return false;
                    }

                    var length = phones.length;
                    for (var i = 0; i < length; i++) {
                        if  (phones[i].id.value === phoneId) {
                            $log.log("phone found", phones[i]);
                            if (phones[i]._states_.selected() === false) {
                                phones[i]._states_.selected(true);
                                currentPhone = phones[i];
                            } else {
                                phones[i]._states_.selected(false);
                                currentPhone = undefined;
                            }
                        } else
                            phones[i]._states_.selected(false);
                    }

                    return true;
                },
                
                append: function (phone) {
                    if (phone === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> phones -> add: Не задан параметр - телефон пользователя");
                        return false;
                    }
                    
                    phones.push(phone);
                },

                add: function (callback) {
                    var params = {
                        action: "addPhone",
                        atsId: newPhone.atsId.value,
                        userId: newPhone.userId.value,
                        phone: newPhone.phone.value
                    };

                    newPhone._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            newPhone._states_.loading(false);
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    var temp = $factory({ classes: ["Phone", "Model", "Backup", "States"], base_class: "Phone" });
                                    temp._model_.fromAnother(data.result);
                                    temp._backup_.setup();
                                    phones.push(temp);
                                    newPhone._backup_.restore();
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback(temp);
                                    return true;
                                }
                            }
                        });

                    return false;    
                },

                edit: function (callback) {
                    if (currentPhone === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> phones -> edit: Не выбран текущий телефонС");
                        return false;
                    }

                    var params = {
                        action: "editPhone",
                        id: currentPhone.id.value,
                        atsId: currentPhone.atsId.value,
                        phone: currentPhone.phone.value
                    };

                    currentPhone._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    currentPhone._backup_.setup();
                                    currentPhone._states_.loading(false);
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback();
                                    return true;
                                }
                            }
                        });

                    return false;
                },

                /**
                 * Удаляет текущий телефон
                 * @callback {function} - callback-функция
                 * @returns {boolean}
                 */
                delete: function (callback) {
                    if (currentPhone === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> phones -> delete: Не выбран текущий телефон");
                        return false;
                    }

                    var params = {
                        action: "deletePhone",
                        id: currentPhone.id.value
                    };

                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            currentPhone._states_.loading(false);
                            $log.log(data);
                            if (data !== undefined && data !== null) {
                                $errors.checkResponse(data);
                                if (data.result !== false) {
                                    if (JSON.parse(data.result) === true) {
                                        var length = phones.length;
                                        for (var i = 0; i < length; i++) {
                                            if (phones[i].id.value === currentPhone.id.value) {
                                                currentPhone._states_.loading(false);
                                                currentPhone = undefined;
                                                phones.splice(i, 1);
                                                if (callback !== undefined && typeof callback === "function")
                                                    callback();
                                                return true;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                },

                clear: function () {
                    phones = [];
                    return true;
                }
            },

            phonebook: {
                getUsersByDivisionId: function (id, callback) {
                    if (id === undefined) {
                        $errors.push(ERROR_TYPE_DEFAULT, "$kolenergo -> getUsersByDivisionId: Не задан парметр - идентификатор структурного подразделения");
                        return false;
                    }

                    var params = {
                        action: "getUsersByDivisionId",
                        id: id
                    };

                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    var users = [];
                                    var length = data.result.users.length;
                                    for (var i = 0; i < length; i++) {
                                        var user = $factory({ classes: ["User", "Model", "Backup", "States"], base_class: "User" });
                                        user._model_.fromAnother(data.result.users[i]);
                                        users.push(user);
                                    }
                                    if (callback !== undefined && typeof callback === "function")
                                        callback(users);
                                }
                            }
                        });
                }
            },



            /**
             * Возвращает массив всех производственных отделений
             * @returns {Array}
             */
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
                    $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> addDivision: Не задан параметр - объект с информацией о новом отделе");
                    return false
                }
            }
        }
    };



    function kolenergoRun ($log, $kolenergo, $classes, $http, $tree) {
        $log.log("krypton.app.kolenergo run...");
        $kolenergo.init();

        //var length = $kolenergo.divisions.getAll().length;
        //for (var i = 0; i < length; i++) {
        //    var item = $kolenergo.divisions.getAll()[i];
        //    $tree.addItem("test", item);
       // }
    };


    function UserAccountController ($log, $scope, $session, $kolenergo, $http, $factory) {
        $scope.user = $session.getCurrentUser();
        $scope.kolenergo = $kolenergo;
        
        $scope.uploaderData = {
            action: "uploadUserPhoto",
            userId: $session.getCurrentUser().id.value
        };


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
        
        
        $scope.onCompleteUploadUserPhoto = function (data) {
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
                $session.getCurrentUser().photo.value = file.url.value;
            }

        };
    };




    function companyController ($log, $scope, $kolenergo, $filter, $modals, $rootScope, $factory, $http, $hierarchy, $errors, $tree) {
        $scope.kolenergo = $kolenergo;
        $scope.modals = $modals;
        $scope.tree = [];
        $scope.submitted = false;


        $scope.selectOrganization = function (organizationId) {
            $log.log("org select callback");
            $kolenergo.organizations.select(organizationId, function (org) {
                if (org !== undefined) {
                    $kolenergo.divisions.getByOrganizationId(organizationId, function (divisions) {
                        $log.log("total divisions = ", divisions.length);
                        var length = divisions.length;
                        for (var x = 0; x < length; x++) {
                            $tree.addItem("test-tree", divisions[x]);
                        }
                    });
                } else
                    $tree.clear("test-tree");
            });
        };
        
        

        /**
         * Открывает модальное окно добавления новой организации
         */
        $scope.openAddOrganizationModal = function () {
            $modals.open("new-organization-modal");
            //$scope.newOrganization._backup_.restore();
            //$scope.newOrganization._backup_.setup();
        };



        /**
         * Закрывает модальное окно добавления новой организации
         */
        $scope.closeAddOrganizationModal = function () {
            $log.log("org add close");
            //$scope.newOrganization._backup_.restore();
            //$log.log("new org title = ", $scope.newOrganization.title.value);
            $scope.new_organization.$setValidity();
            $scope.new_organization.$setPristine();
            $scope.submitted = false;
        };



        /**
         * Добавляет новую организацию
         */
        $scope.addOrganization = function () {
            $scope.submitted = true;
            if ($scope.new_organization.$valid) {
                //$scope.newOrganization._states_.loading(true);
                $kolenergo.organizations.add(function () {
                    $modals.close();
                    //$scope.newOrganization._states_.loading(false);
                });
            }
        };


        
        /**
         * Открывает модальное окно редактирования организации
         */
        $scope.openEditOrganizationModal = function () {
            $modals.open("edit-organization-modal");
        };



        /**
         * Закрывает модальное окно редактирования организации
         */
        $scope.closeEditOrganizationModal = function () {
            $kolenergo.organizations.getCurrent()._backup_.restore();
            $scope.edit_organization.$setValidity();
            $scope.edit_organization.$setPristine();
            $scope.submitted = false;
        };


        
        /**
         * Сохраняет изменения измененнной организации
         */
        $scope.editOrganization = function () {
            $scope.submitted = true;
            if ($scope.edit_organization.$valid) {
                $kolenergo.organizations.edit(function () {
                    $modals.close();
                });
            }
            $log.log("errors = ", $kolenergo.organizations.getCurrent().errors);
        };



        /**
         * Открывает модальное окно удаления организации
         */
        $scope.openDeleteOrganizationModal = function () {
            $modals.open("delete-organization-modal");
        };



        /**
         * Удаляет организацию
         */
        $scope.deleteOrganization = function () {
            $kolenergo.organizations.delete(function () {
                $modals.close();
            });
        };


        $scope.selectDepartment = function (departmentId) {
            //if ($kolenergo.departments.getCurrent() === undefined) {
            //    $kolenergo.departments.select(departmentId);
                //$scope.tree = $kolenergo.divisions.getByOrganizationId(organizationId);
                //for (var x = 0; x < $scope.tree.length; x++) {
                //    $tree.addItem("test-tree", $scope.tree[x]);
                //}
            //} else {
                $kolenergo.departments.select(departmentId);
            //$scope.newDivision.departmentId.value = departmentId;
                //$tree.clear("test-tree");
                //$scope.tree = [];
            //}
        };


        /**
         * Открывает модальное окно добавления нового производственного отделения
         */
        $scope.openAddDepartmentModal = function () {
            $modals.open("new-department-modal");
            //$scope.newDepartment._backup_.restore();
            //$scope.newDepartment._backup_.setup();
        };



        /**
         * Закрывает модальное окно добавления нового производственного отделения
         */
        $scope.closeAddDepartmentModal = function () {
            //$scope.newDepartment._backup_.restore();
            $scope.new_department.$setValidity();
            $scope.new_department.$setPristine();
            $scope.submitted = false;
        };



        /**
         * Добавляет новое производственное отделение
         */
        $scope.addDepartment = function () {
            $scope.submitted = true;
            if ($scope.new_department.$valid) {
                //$scope.newDepartment._states_.loading(true);
                $kolenergo.departments.add(function () {
                    $modals.close();
                    //$scope.newDepartment._states_.loading(false);
                });
            }
        };


        /**
         * Открывает модальное окно редактирования производственного отделения
         */
        $scope.openEditDepartmentModal = function () {
            $modals.open("edit-department-modal");
        };



        /**
         * Закрывает модальное окно редактирования производственного отделения
         */
        $scope.closeEditDepartmentModal = function () {
            $kolenergo.departments.getCurrent()._backup_.restore();
            $scope.edit_department.$setValidity();
            $scope.edit_department.$setPristine();
            $scope.submitted = false;
        };



        /**
         * Сохраняет изменения измененнного производственного отделения
         */
        $scope.editDepartment = function () {
            $scope.submitted = true;
            if ($scope.edit_department.$valid) {
                $kolenergo.departments.edit(function () {
                    $modals.close();
                });
            }
        };


        /**
         * Открывает модальное окно удаления производственного отделения
         */
        $scope.openDeleteDepartmentModal = function () {
            $modals.open("delete-department-modal");
        };



        /**
         * Удаляет производственное отделение
         */
        $scope.deleteDepartment = function () {
            $kolenergo.departments.delete(function () {
                $modals.close();
            });
        };

        

        $scope.openAddNewDivisionModal = function () {
            $modals.open("new-division-modal");
            //if ($kolenergo.organizations.getCurrent() !== undefined)
            //    $scope.newDivision.organizationId.value = $kolenergo.organizations.getCurrent().id.value;
        };



        $scope.closeAddNewDivisionModal = function () {
            //$scope.newDivision._backup_.restore();
            $scope.edit_organization.$setValidity();
            $scope.edit_organization.$setPristine();
            $scope.submitted = false;
            //if ($kolenergo.divisions.getCurrent() !== undefined)
            //    $scope.newDivision.parentId.value = $kolenergo.divisions.getCurrent().id.value;
        };
        
        
        
        $scope.addDivision = function () {
            $scope.submitted = true;
            if ($scope.new_division.$valid) {
                $kolenergo.divisions.add(function (div) {
                    //$scope.newDivision._backup_.restore();
                    //if ($kolenergo.divisions.getCurrent() !== undefined)
                    //    $scope.newDivision.parentId.value = $kolenergo.divisions.getCurrent().id.value;
                    $tree.addItem("test-tree", div);
                    $modals.close();
                });
            }

        };
        
        
        
        $scope.selectDivision = function (division) {
            if (division !== undefined) {
                $kolenergo.divisions.select(division.id.value);
                //if (division._states_.selected() === true)
                    //$scope.newDivision.parentId.value = division.id.value;
                //else
                //    $scope.newDivision.parentId.value = 0;
                //$log.log("new div parentId = ", $scope.newDivision.parentId.value);
            }
        };



        $scope.openEditDivisionModal = function () {
            $modals.open("edit-division-modal");
        };



        $scope.closeEditDivisionModal = function () {
            $scope.edit_division.$setValidity();
            $scope.edit_division.$setPristine();
            $scope.submitted = false;
            $kolenergo.divisions.getCurrent()._backup_.restore();
            $kolenergo.divisions.getCurrent()._states_.changed(false);
        };



        $scope.editDivision = function () {
            //$scope.submitted = true;
            if ($scope.edit_division.$valid) {
                $kolenergo.divisions.edit(function () {
                    $modals.close();
                });
            } else
                $scope.submitted = true;
            //$scope.submitted = false;
        };



        $scope.openDeleteDivisionModal = function () {
            $modals.open("delete-division-modal");
        };



        /**
         * Удаляет структурное подразделение
         */
        $scope.deleteDivision = function () {
            var id = $kolenergo.divisions.getCurrent().id.value;
            $kolenergo.divisions.delete(function () {
                $modals.close("delete-division-modal");
                $tree.deleteItem("test-tree", id);
            });
        };


    };
    
    
    
    
    function LoginController ($scope, $log,  $errors, $http, $factory, $location, $session) {
        $scope.login = "";
        $scope.password = "";
        $scope.errors = [];
        $scope.loading = false;


        $scope.send = function () {
            $scope.errors.splice(0, $scope.errors.length);

            if ($scope.login === "")
                $scope.errors.push("Вы не указали учетную запись");

            if ($scope.password === "")
                $scope.errors.push("Вы не указали пароль");

            if ($scope.errors.length === 0) {
                $scope.loading = true;
                var params = {
                    action: "login",
                    login: $scope.login,
                    password: $scope.password
                };
                $http.post("serverside/libs/krypton/api.php", params)
                    .success (function (data) {
                        $log.log(data);
                        $scope.loading = false;

                        $errors.checkResponse(data);


                        if (data.result !== false) {
                            var user = $factory({ classes: ["User", "Model", "Backup", "States"], base_class: "User" });
                            user._model_.fromAnother(data.result);
                            user._backup_.setup();
                            $log.log(user);
                            $session.setCurrentUser(user);
                            $location.url("\account");
                        } else {
                            $scope.errors.push("Пользователь не найден");
                        }

                    });
            }
        };
    };





    function PhoneBookController ($scope, $log, $users, $kolenergo, $tree) {
        $scope.users = $users;
        $scope.kolenergo = $kolenergo;
        $scope.search = "";
        $scope.contacts = [];

        $scope.div = 0;

        //$scope.divs = $kolenergo.divisions.getByOrganizationId(8);
        //$log.log("divs = ", $scope.divs);
        //var length = $scope.divs.length;
        //for (var i = 0; i < length; i++) {
        //    $tree.addItem("test", $scope.divs[i]);
        //
        //}



        $scope.selectDivision = function (div) {
            $log.log("selected = ", div.id.value);
            //$kolenergo.divisions.select(div.id.value);
            //if ($scope.div !== div.id.value)
            //    $scope.div = div.id.value;
            //else
            //    $scope.div = 0;
            $scope.contacts = [];
            $kolenergo.phonebook.getUsersByDivisionId(div.id.value, function (result) {
                $scope.contacts = result;
                $log.log("contacts = ", $scope.contacts);
            });
        };
    };




    function byOrganizationIdFilter () {
        return function (input, id) {
            if (id !== undefined || id !== 0) {
                var result = [];
                var length = input.length;
                for (var i = 0; i < length; i++) {
                    if (input[i].organizationId.value === id)
                        result.push(input[i]);
                }
                return result;
            }
        }
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


    function byDivisionIdFilter ($log, $kolenergo) {
        return function (input, id) {
            if (id !== -1 && id !== undefined) {
                var divs = [];
                var result = [];

                var divLength = $kolenergo.divisions.getAll().length;
                for (var x = 0; x < divLength; x++) {
                    var div = $kolenergo.divisions.getAll()[x];
                    if (div.path.value.indexOf(id) !== -1)
                        divs.push(div);
                }

                var length = input.length;
                for (var i = 0; i < length; i++) {
                    var selected = divs.length;
                    for (var z = 0; z< selected; z++) {
                        if (input[i].divisionId.value === divs[z].id.value)
                            result.push(input[i]);
                    }
                }
                $log.log("filtered ", result.length);
                return result;
            } else
                return input;
        }
    };

    function byATSIdFilter ($log) {
        return function (input, id) {
            if (id !== undefined || id !== 0) {
                var result = [];
                var length = input.length;
                for (var i = 0; i < length; i++) {
                    if (input[i].atsId.value === id)
                        result.push(input[i]);
                }
                    return result;
            } else 
                return [];
        }
    };

    function byUserIdFilter () {
        return function (input, id) {
            if (id !== undefined || id !== 0) {
                var result = [];
                var length = input.length;
                for (var i = 0; i < length; i++) {
                    if (input[i].userId.value === id)
                        result.push(input[i]);
                }
                return result;
            }
        }
    };

    
    function phoneBookFilter ($log, $kolenergo) {
        return function (input, search) {
            var result = [];

            if ($kolenergo.divisions.getCurrent() === undefined && search.length > 2) {
                var length = input.length;
                for (var i = 0; i < length; i++) {
                    var temp = input[i].search.toLowerCase();
                    if (temp.indexOf(search.toLowerCase()) !== -1)
                        result.push(input[i]);
                }
                return result;
            }
            else if ($kolenergo.divisions.getCurrent() === undefined) {

            } else if ($kolenergo.divisions.getCurrent() !== undefined && search.length > 2) {
                var length = input.length;
                for (var i = 0; i < length; i++) {
                    var temp = input[i].search.toLowerCase();
                    if (temp.indexOf(search.toLowerCase()) !== -1)
                        result.push(input[i]);
                }
                return result;
            } else if ($kolenergo.divisions.getCurrent() !== undefined && search.length === 0) {
                return input;
            }
        }
    };



    function notZeroDirective ($log) {
        return {
            restrict: "A",
            require: "?ngModel",
            link: function (scope, element, attrs, ngModel) {
                if (!ngModel)
                    return;

                $log.log(ngModel);


                scope.$watch(attrs.ngModel, function (val) {
                    $log.log("ngModel = ", val);
                    if (val === 0)
                        ngModel.$setValidity("zero", false);
                    else
                        ngModel.$setValidity("zero", true);
                });

            }
        }
    };


})();
