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
            id: 0,
            element: 0,
            title: "",
            modelValue: 0,
            isVisible: false,
            isModal: false,
            isOpened: false,

            open: function () {
                this.isOpened = true;
            },

            close: function () {
                this.isOpened = false;
            }
        });
    };



    function dateTimePickerDirective ($log, $http, $compile, $rootScope, $window, $dateTimePicker) {
        return {
            restrict: "A",
            requires: "ngModel",
            //templateUrl: "templates/ui/date-time-picker.html",
            scope: {
                dateTimePickerModelValue: "&",
                dateTimePickerModal: "=",
                dateTimePickerTitle: "@",
                dateTimePickerEnableTime: "=",
                dateTimePickerOpened: "="
            },
            link: function (scope, element, attrs, controller) {
                var dateTimePicker;
                var days = scope.days = new Array(35);
                var weekdays = scope.weekdays = [["Пн", "Понедельник"], ["Вт", "Вторник"], ["Ср", "Среда"], ["Чт", "Четверг"] , ["Пт", "Пятница"], ["Сб", "Суббота"], ["Вс", "Воскресение"]];
                var months = scope.months =  ["Январь" ,"Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
                var isModal = scope.isModal = false;
                var selectedMonthIndex = scope.selectedMonthIndex = 1;


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


                if (scope.dateTimePickerModal !== undefined) {
                    switch (scope.dateTimePickerModal) {
                        case true:
                            scope.isModal = true;
                            var fog = document.createElement("div");
                            fog.className = "krypton-ui-fog";
                            document.body.appendChild(fog);
                            break;
                        default:
                            scope.isModal = false;
                            break;
                    }
                }

                scope.open = function () {
                    scope.dateTimePickerOpened = true;
                    scope.$apply();
                };

                $http.get("templates/ui/date-time-picker.html")
                    .success(function (data) {
                        if (data !== undefined) {
                            dateTimePicker = document.createElement("div");
                            dateTimePicker.className = scope.isModal === true ? "krypton-ui-date-time-picker modal" : "krypton-ui-date-time-picker";
                            dateTimePicker.innerHTML = data;
                            document.body.appendChild(dateTimePicker);
                            $compile(dateTimePicker)(scope);
                            $dateTimePicker.add(scope);

                            dateTimePicker.addEventListener("DOMSubtreeModified", function (event) {
                                redraw(dateTimePicker);
                            }, false);

                            angular.element($window).bind("resize", function () {
                                redraw(dateTimePicker);
                            });
                        }
                    });
            }
        }
    };



    function dateTimePickerFactory ($log, $window, $document, $errors, $factory, $compile, $rootScope) {
        var instances = [];
        
        return {
            /**
             * Добавляет новый элемент в стек
             * @param parameters - Набор параметров инициализации
             * @returns {*}
             */
            add: function (parameters) {
                if (parameters !== undefined) {
                    if (typeof parameters === "object") {
                        if (parameters["element"] !== undefined) {

                            var element = document.getElementById(parameters["element"]);
                            $log.log("element = ", element);
                            if (element !== undefined && element !== null) {
                                var dateTimePicker = $factory({classes: ["DateTimePicker"], base_class: "DateTimePicker"});
                                dateTimePicker.id = "dateTimePicker" + instances.length + 1;
                                dateTimePicker.element = element;
                                element.setAttribute("ui-date-time-picker", "");
                                element.setAttribute("ui-date-time-picker-opened", false);
                                //element.setAttribute("ng-if", "isOpened === true");

                                for (var param in parameters) {
                                    if (dateTimePicker.hasOwnProperty(param)) {
                                        switch (param) {
                                            case "modelValue":
                                                dateTimePicker.modelValue = parameters[param];
                                                element.setAttribute("date-time-picker-model-value", dateTimePicker.modelValue);
                                                break;
                                            case "isModal":
                                                if (typeof parameters[param] === "boolean") {
                                                    dateTimePicker.isModal = parameters[param];
                                                    if (dateTimePicker.isModal === true)
                                                        element.setAttribute("date-time-picker-modal", dateTimePicker.isModal);
                                                } else
                                                    return $errors.add(ERROR_TYPE_DEFAULT, "$calendar -> add: Неверно задан тип параметра - модальный режим");
                                                break;
                                            case "title":
                                                dateTimePicker.title = parameters[param] !== undefined && parameters[param] !== "" ? parameters[param] : "";
                                                if (dateTimePicker.title !== "")
                                                    element.setAttribute("date-time-picker-title", dateTimePicker.title);
                                                break;
                                        }
                                    }
                                }

                                instances.push(dateTimePicker);
                                $log.info("cal = ", dateTimePicker);
                                $compile(dateTimePicker.element)($rootScope.$new());
                                return dateTimePicker;
                            } else
                                return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Элемент с идентификатором '" + parameters[param] + "' не найден");
                        } else
                            return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Не задан целевой элемент");
                    } else 
                        return $errors.add(ERROR_TYPE_DEFAULT, "$calendar -> add: Неверно задан тип параметра инициализации");                     
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$calendar -> add: Не заданы параметры инициализации");
            },

            
            
            open: function (elementId) {
                if (elementId !== undefined) {
                    var length = instances.length;
                    for (var i = 0; i < length; i++) {
                        var instanceFound = false;
                        if (instances[i].element.id === elementId) {
                            $log.log("element with id = " + elementId + " found");
                            instances[i].open();
                        }
                    }
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$calendar -> open: Не задан параметр - идентификатор элемента");
            }


        }
    }
})();