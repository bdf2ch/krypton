(function () {
    angular
        .module("krypton.ui", [])
            .factory("$dateTimePicker", dateTimePickerFactory)
            .directive("uiDateTimePicker", dateTimePickerDirective2);
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
                var days = scope.days = new Array(35);
                var weekdays = scope.weekdays = [
                    ["Пн", "Понедельник"], ["Вт", "Вторник"],
                    ["Ср", "Среда"], ["Чт", "Четверг"] ,
                    ["Пт", "Пятница"], ["Сб", "Суббота"],
                    ["Вс", "Воскресение"]
                ];
                var months = scope.months =  [
                    "Январь" ,"Февраль", "Март",
                    "Апрель", "Май", "Июнь",
                    "Июль", "Август", "Сентябрь",
                    "Октябрь", "Ноябрь", "Декабрь"
                ];
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
                        //angular.element(fog[0]).css("display", "block");
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
                    //$log.log("picker instance = ", instance);
                    var picker = document.getElementById(instance.id);
                    //$log.log("picker element = ", picker);
                    angular.element(picker).css("display", "block");
                    redraw(dateTimePicker);
                    if (instance.isModal === true) {
                        var fog = document.getElementsByClassName("krypton-ui-fog");
                        angular.element(fog[0]).css("display", "block");
                        fog[0].classList.add("visible");

                        angular.element(fog[0]).on("mousedown", function () {
                           angular.element(fog[0]).css("display", "none");
                            scope.close();
                        });

                        //document.getElementsByTagName("body")[0].style.webkitFilter = "blur(2px)";
                    }
                };


                scope.close = function () {
                    var instance = $dateTimePicker.exists(angular.element(element).prop("id"));
                    $log.log("picker instance = ", instance);
                    var picker = document.getElementById(instance.id);
                    angular.element(picker).css("display", "none");
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

                                element.on("mousedown", function (event) {
                                    $log.log("MOUSEDOWN");
                                    scope.open();
                                });

                                var instance = $dateTimePicker.exists(angular.element(element).prop("id"));
                                //if (instance !== false) {
                                    var picker = document.getElementById(instance.id);
                                    picker.innerHTML = data;
                                    //document.body.appendChild(dateTimePicker);
                                    $compile(dateTimePicker)(scope);
                                //} else {

                                //}
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




    function dateTimePickerDirective2 ($log, $errors, $compile, $window, $document, $dateTimePicker) {
        return {
            restrict: "A",
            require: "ngModel",
            scope: {
                ngModel: "=",
                dateTimePickerModal: "="
            },
            link: function (scope, element, attrs, controller) {

                $log.log('directive start');

                var template =
                    "<div class='toolbar'>" +
                        "<div class='control'><button class='width-100 blue' ng-click='prevMonth()'>&larr;</button></div>" +
                        "<div class='content'>" +
                            "<select class='width-60 no-border' ng-model='selectedMonthIndex' ng-options='month[0] as month[1] for month in months'></select>" +
                            "<select class='width-40 no-border' ng-model='selectedYearIndex' ng-options='year as year for year in years'></select>" +
                        "</div>" +
                        "<div class='control'><button class='width-100 blue' ng-click='nextMonth()'>&rarr;</button></div>" +
                    "</div>" +
                    "<div class='weekdays'>" +
                        "<div class='day' ng-repeat='weekday in weekdays track by $index'>" +
                        "<span ng-if='settings.isModal === true'>{{ weekday[1] }}</span><span ng-if='settings.isModal === false'>{{ weekday[0] }}</span>" +
                        "</div>" +
                    "</div>" +
                    "<div class='day' ng-class='{\"sunday\": ($index + 1) % 7 === 0}' ng-repeat='day in days track by $index'>{{ $index }}" +
                    "</div>";

                var ctrl = scope.ctrl = controller;
                var days = scope.days = Array(35);
                var weekdays = scope.weekdays = [
                    ["Пн", "Понедельник"], ["Вт", "Вторник"],
                    ["Ср", "Среда"], ["Чт", "Четверг"] ,
                    ["Пт", "Пятница"], ["Сб", "Суббота"],
                    ["Вс", "Воскресение"]
                ];
                var months = scope.months =  [
                    [0, "Январь"] ,[1, "Февраль"], [2, "Март"],
                    [3, "Апрель"], [4, "Май"], [5, "Июнь"],
                    [6, "Июль"], [7, "Август"], [8, "Сентябрь"],
                    [9, "Октябрь"], [10, "Ноябрь"], [11, "Декабрь"]
                ];
                var currentDate = moment(new Date());
                var years = scope.years = [];
                for (var i = moment(currentDate).year(); i < moment(currentDate).year() + 5; i++) {
                    years.push(i);
                    if (i === moment(currentDate).year())
                        selectedYear = i;
                }

                var selectedMonthIndex = scope.selectedMonthIndex = currentDate.month();
                var selectedYear = scope.selectedYearIndex = moment(currentDate).year();


                var settings = scope.settings = {
                    isModal: scope.dateTimePicker !== null && scope.dateTimePickerModal !== undefined ? true : false,
                    isOpened: false,
                    title: "",
                    element: element
                };

                var redraw = function (elm) {
                    if (elm !== undefined) {
                        var elementWidth = angular.element(element).prop("clientWidth");
                        var elementHeight = angular.element(element).prop("clientHeight");
                        var elementLeft = angular.element(element).prop("offsetLeft");
                        var elementTop = angular.element(element).prop("offsetTop");
                        var containerWidth = angular.element(elm).prop("clientWidth");
                        var containerHeight = angular.element(elm).prop("clientHeight");
                        var windowWidth = $window.innerWidth;
                        var windowHeight = $window.innerHeight;
                        var left = 0;
                        var top = 0;
                        if (scope.settings.isModal === true) {
                            left = (windowWidth / 2) - (angular.element(elm).prop("clientWidth") / 2) + "px";
                            top = (windowHeight / 2) - (angular.element(elm).prop("clientHeight") / 2) + "px"
                        } else {
                            if (containerWidth > elementWidth) {
                                if ((elementLeft > (containerWidth - elementWidth) / 2) && (elementLeft < (windowWidth - elementLeft) + containerWidth / 2))
                                    left = elementLeft - ((containerWidth - elementWidth) / 2);
                            } else
                                left = angular.element(element).prop("offsetLeft") + "px";

                            top = (angular.element(element).prop("offsetTop") - angular.element(elm).prop("clientHeight")) - 10 + "px";
                        }
                        angular.element(elm).css("left", left);
                        angular.element(elm).css("top", top);
                    } else
                        return $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> dateTimePicker directive: Не задан параметр - HTML-элемент");
                };

                //$log.log("isModal is ", scope.dateTimePickerModal === null || scope.dateTimePickerModal === undefined ? "null" : "not null");
                
                controller.$parsers.push(function (value) {

                });

                controller.$formatters.push(function (value) {
                    return moment.unix(value).format("DD MMM YYYY, HH:mm");
                });


               


                scope.prevMonth = function () {
                    scope.selectedMonthIndex = --scope.selectedMonthIndex < 0 ? scope.selectedMonthIndex = 11 : scope.selectedMonthIndex;
                    moment(currentDate).subtract(1, "month");
                };


                scope.nextMonth = function () {
                    scope.selectedMonthIndex = ++scope.selectedMonthIndex > 11 ? 0 : scope.selectedMonthIndex;
                    moment(currentDate).add(1, "month");
                };


                var instance = $dateTimePicker.push(scope);
                var container = document.createElement("div");
                container.setAttribute("id", instance.id);
                container.className = "ui-date-time-picker2";
                container.innerHTML = template;
                document.body.appendChild(container);
                $compile(container)(scope);
                angular.element(element).css("cursor", "pointer");
                //angular.element(element).prop("disabled", "disabled");

                scope.open = function () {
                    angular.element(container).css("display", "block");
                    scope.settings.isOpened = true;
                    redraw(container);
                };


                scope.close = function () {
                    angular.element(container).css("display", "none");
                    scope.settings.isOpened = false;
                };


                container.addEventListener("DOMSubtreeModified", function () {
                    $log.log("redraw");
                    redraw(container);
                }, false);


                angular.element($window).bind("resize", function () {
                    redraw(container);
                });


                angular.element($document).bind("mousedown", function (event) {
                    if (scope.settings.isOpened === true && !container.contains(event.target) && event.target !== element[0])
                        scope.close();
                });

                element.on("mousedown", function () {
                    if (scope.settings.isOpened === false)
                        scope.open();
                });

                element.on("keydown", function (event) {
                    event.preventDefault();
                });
            }
        }
    };




    function dateTimePickerFactory ($log, $window, $document, $http, $errors, $factory, $compile, $rootScope) {
        var instances = [];
        var template = "";
        var isTemplateLoading = false;
        
        return {

            push: function (scope) {
                if (scope !== undefined) {
                    var instance = $factory({ classes: ["DateTimePicker"], base_class: "DateTimePicker" });
                    instance.id = "dateTimePicker" + instances.length;
                    instance.isModal = scope.settings.isModal;
                    instance.isOpened = scope.settings.isOpened;
                    instance.element = scope.settings.element;
                    instance.scope = scope;
                    instances.push(instance);
                    $log.log(instances);
                    return instance;
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> dateTimePicker directive: Не задан параметр - объект с настройками директивы");
            },


            show: function (elementId) {
                $log.log("instances before = ", instances);
                if (elementId !== undefined) {
                    var length = instances.length;
                    for (var i = 0; i < length; i++) {
                        if (instances[i].element.getAttribute("id") === elementId)
                            $log.log("Element with id = " + elementId + " found!");
                    }
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> dateTimePicker directive : Не задан параметр - идентификатор HTML_элемента");
            },







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
                    for (var i = 0; i < length; i++) {
                        var instanceFound = false;
                        if (angular.element(instances[i].element).prop("id") === elementId) {
                            $log.log("element with id = " + elementId + " found");
                            $log.log(instances[i]);
                            instances[i].scope.open();
                        }
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