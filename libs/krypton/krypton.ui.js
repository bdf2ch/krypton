(function () {
    angular
        .module("krypton.ui", [])
            .factory("$dateTimePicker", dateTimePickerFactory)
            .directive("uiDateTimePicker", dateTimePickerDirective);
    angular.module("krypton.ui").run(kryptonUIRun);
    
    
    
    function kryptonUIRun ($log, $classes) {
        $log.log("krypton.ui run...");
        /**
         * DateTimePicker
         * Набор свойств и методов, описывающих элемент интрефейса - календарь
         */
        $classes.add("DateTimePicker", {
            __dependencies__: [],
            id: "",
            element: 0,
            title: "",
            modelValue: 0,
            isVisible: false,
            isModal: false,
            isOpened: false,
            scope: {},

            open: function () {
                this.isOpened = true;
                this.scope.open();
            },

            close: function () {
                this.isOpened = false;
            }
        });
    };



    function dateTimePickerDirective ($log, $http, $compile, $rootScope, $window, $dateTimePicker) {
        return {
            restrict: "A",
            //require: "ngModel",
            scope: {
                dateTimePickerModelValue: "&",
                dateTimePickerModal: "@",
                dateTimePickerTitle: "@",
                dateTimePickerEnableTime: "=",
                dateTimePickerOpened: "="
            },
            link: function (scope, element, attrs, controller) {
                $log.log("dtp directive");

                var days = scope.days = new Array(35);
                var weekdays = scope.weekdays = [["Пн", "Понедельник"], ["Вт", "Вторник"], ["Ср", "Среда"], ["Чт", "Четверг"] , ["Пт", "Пятница"], ["Сб", "Суббота"], ["Вс", "Воскресение"]];
                var months = scope.months =  ["Январь" ,"Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
                var selectedMonthIndex = scope.selectedMonthIndex = 1;
                
                var isOpened = scope.isOpened = false;
                var isModal = scope.isModal = false;
                var title = scope.title = "";
                var value = scope.value = undefined;
                var instance = $dateTimePicker.exists(angular.element(element).prop("id"));





                var dateTimePicker = document.createElement("div");
                //document.body.appendChild(dateTimePicker);
                if (instance !== false) {
                    //var dateTimePicker = document.createElement("div");
                    instance.scope = scope;
                    scope.isOpened = instance.isOpened;
                    scope.isModal = instance.isModal;
                    scope.title = instance.title;
                    scope.value = instance.modelValue;
                    dateTimePicker.setAttribute("id", instance.id);
                    document.body.appendChild(dateTimePicker);
                } else {
                    var picker = $dateTimePicker.add({
                        element: angular.element(element).prop("id"),
                        isModal: scope.dateTimePickerModal !== null ? true : false,
                        modelValue: scope.dateTimePickerModelValue,
                        title: scope.dateTimePickerTitle
                    });
                    picker.scope = scope;
                    scope.isOpened = picker.isOpened;
                    scope.isModal = picker.isModal;
                    scope.title = picker.title;
                    scope.value = picker.modelValue;
                    dateTimePicker.setAttribute("id", picker.id);
                    //var dateTimePicker = document.createElement("div");
                    //document.body.appendChild(dateTimePicker);
                }


                if (scope.isModal === true) {
                    dateTimePicker.className = "krypton-ui-date-time-picker modal";
                    var fog = document.getElementsByClassName("krypton-ui-fog");
                    if (fog.length === 0) {
                        var fog = document.createElement("div");
                        fog.className = "krypton-ui-fog";
                        document.body.appendChild(fog);    
                    } else {
                        angular.element(fog[0]).css("display", "block");
                    }
                } else {
                    dateTimePicker.className = "krypton-ui-date-time-picker";
                }
                
                var redraw = function (element) {
                    if (element !== undefined) {
                        var width = angular.element(element).prop("clientWidth");
                        var height = angular.element(element).prop("clientHeight");
                        var top = ($window.innerHeight / 2) - height / 2;
                        var left = ($window.innerWidth / 2) - width / 2;

                        angular.element(element).css("left", left + "px");
                        angular.element(element).css("top", top + "px");
                    }
                };



                scope.open = function () {
                    var instance = $dateTimePicker.exists(angular.element(element).prop("id"));
                    $log.log("picker instance = ", instance);
                    var picker = document.getElementById(instance.id);
                    //$log.log("picker element = ", picker);
                    angular.element(picker).css("display", "block");
                    if (instance.isModal === true) {
                        var fog = document.getElementsByClassName("krypton-ui-fog");
                        //angular.element(fog[0]).css("display", "block");
                        fog[0].classList.add("visible");
                    }
                };

                


                //if ($dateTimePicker.getTemplate() === "" && $dateTimePicker.loading() === false) {
                    //$dateTimePicker.loading(true);
                    $http.get("templates/ui/date-time-picker.html")
                        .success(function (data) {
                            $dateTimePicker.loading(false);
                            if (data !== undefined) {
                                $dateTimePicker.setTemplate(data);

                               // dateTimePicker.innerHTML = data;
                                dateTimePicker.addEventListener("DOMSubtreeModified", function (event) {
                                    redraw(dateTimePicker);
                                }, false);
                                angular.element($window).bind("resize", function () {
                                    redraw(dateTimePicker);
                                });

                                var instance = $dateTimePicker.exists(angular.element(element).prop("id"));
                                if (instance !== false) {
                                    var picker = document.getElementById(instance.id);
                                    picker.innerHTML = data;
                                    //document.body.appendChild(dateTimePicker);
                                    $compile(dateTimePicker)(scope);
                                } else {

                                }
                            }
                        });
                //} else {
                 //   dateTimePicker.innerHTML = $dateTimePicker.getTemplate();
                 //   dateTimePicker.addEventListener("DOMSubtreeModified", function (event) {
                 //       redraw(dateTimePicker);
                 //   }, false);
                 //   angular.element($window).bind("resize", function () {
                 //       redraw(dateTimePicker);
                 //   });
                    //if (instance === false) {
                 //       document.body.appendChild(dateTimePicker);
                 //       $compile(dateTimePicker)(scope);
                    //}

                //}


            }
        }
    };



    function dateTimePickerFactory ($log, $window, $document, $http, $errors, $factory, $compile, $rootScope) {
        var instances = [];
        var template = "";
        var isTemplateLoading = false;
        
        return {
            /**
             * Добавляет новый элемент в стек
             * @param parameters - Набор параметров инициализации
             * @returns {*}
             */
            add: function (parameters) {
                if (parameters !== undefined) {
                    if (typeof parameters === "object") {
                        if (parameters.element !== undefined) {

                            var element = document.getElementById(parameters.element);
                            $log.log("element = ", element);
                            if (element !== undefined && element !== null) {
                                var picker = $factory({classes: ["DateTimePicker"], base_class: "DateTimePicker"});
                                picker.id = "dateTimePicker" + instances.length;
                                picker.element = element;

                                //var instance = this.exists(angular.element(picker.element).prop("id"));

                                //if (element.classList.contains("ng-isolate-scope") === true)
                                    element.setAttribute("ui-date-time-picker", "");
                                ///element.setAttribute("ui-date-time-picker-opened", false);
                                //element.setAttribute("ng-if", "isOpened === true");

                                for (var param in parameters) {
                                    if (picker.hasOwnProperty(param)) {
                                        switch (param) {
                                            case "modelValue":
                                                picker.modelValue = parameters[param];
                                                //element.setAttribute("date-time-picker-model-value", dateTimePicker.modelValue);
                                                break;
                                            case "isModal":
                                                if (typeof parameters[param] === "boolean") {
                                                    picker.isModal = parameters[param];
                                                    //if (picker.isModal === true)
                                                    //    element.setAttribute("date-time-picker-modal", picker.isModal);
                                                } else
                                                    return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Неверно задан тип параметра - модальный режим");
                                                break;
                                            case "title":
                                                picker.title = parameters[param] !== undefined && parameters[param] !== "" ? parameters[param] : "";
                                                //if (picker.title !== "")
                                                //    element.setAttribute("date-time-picker-title", picker.title);
                                                break;
                                        }
                                    }
                                }


                                instances.push(picker);
                                $log.log(instances);
                                //element.setAttribute("ui-date-time-picker", "");
                                $log.info("dtp = ", picker);
                                $compile(picker.element)($rootScope.$new());

                                return picker;
                            } else
                                return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Элемент с идентификатором '" + parameters[param] + "' не найден");
                        } else
                            return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Не задан целевой элемент");
                    } else 
                        return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Неверно задан тип параметра инициализации");
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Не заданы параметры инициализации");
            },

            
            
            open: function (elementId) {
                if (elementId !== undefined) {
                    var length = instances.length;
                    $log.log("instances defore", instances);
                    for (var i = 0; i < length; i++) {
                        var instanceFound = false;
                        if (angular.element(instances[i].element).prop("id") === elementId) {
                            instanceFound = true;
                            $log.log("element with id = " + elementId + " found");
                            $log.log(instances[i]);
                            instances[i].scope.open();
                        }
                        if (instanceFound === false)
                            $log.log("Element " + elementId + " not found");
                    }
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> open: Не задан параметр - идентификатор элемента");
            },


            /**
             * Проверяет на наличие экземпляра по идентификатору элемента
             * @param elementId - Идкнтификатор элемента
             * @returns {*}
             */
            exists: function (elementId) {
                if (elementId !== undefined) {
                    $log.log("elementId = ", elementId.toString());
                    var length = instances.length;
                    for (var i = 0; i < length; i++) {
                        $log.log("instance element = ", instances[i].element.getAttribute("id"));
                        if (instances[i].element.getAttribute("id").toString() === elementId) {
                            $log.log("founded instance = ", instances[i]);
                            return instances[i];
                        }
                    }
                    return false;
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> exists: Не задан параметр - идентификатор элкмкнта");
            },


            getTemplate: function () {
                return template;
            },


            setTemplate: function (tpl) {
                if (tpl !== undefined)
                    template = tpl;
                else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> setTemplate: Не задан параметр - содержимое шаблона");
            },

            loading: function (flag) {
                if (flag !== undefined) {
                    if (typeof flag === "boolean") {
                        isTemplateLoading = flag;
                        return isTemplateLoading;
                    } else
                        return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> loading: Неверно задан тип параметр - флаг процесса загрузки шаблона");
                } else
                    return isTemplateLoading;
            }


        }
    }
})();