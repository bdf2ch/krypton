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
            isTimeEnabled: false,
            isMinutesEnabled: false,
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
                    $log.log("picker instance = ", instance);
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
                dateTimePickerModal: "=",
                dateTimePickerEnableTime: "=",
                dateTimePickerEnableMinutes: "="
            },
            link: function (scope, element, attrs, controller) {

                var template =
                    "<div class='toolbar'>" +
                        "<div class='control'><button class='width-100 blue' ng-click='prevMonth()'>&larr;</button></div>" +
                        "<div class='content'>" +
                            "<select class='width-60 no-border' ng-if='isInTimeSelectMode === false' ng-model='month' ng-options='month[0] as month[1] for month in months'></select>" +
                            "<select class='width-40 no-border' ng-if='isInTimeSelectMode === false' ng-model='year' ng-options='year as year for year in years'></select>" +
                            "<select class='width-100 no-border' ng-if='isInTimeSelectMode === true' ng-model='dayPart' ng-options='part[0] as part[1] for part in dayParts'></select>" +
                        "</div>" +
                        "<div class='control'><button class='width-100 blue' ng-click='nextMonth()'>&rarr;</button></div>" +
                    "</div>" +
                    "<div class='weekdays'>" +
                        "<div class='day width-100' ng-if='isInTimeSelectMode'>Выберите время - часы</div>" +
                        "<div class='day' ng-if='isInTimeSelectMode === false' ng-repeat='weekday in weekdays track by $index'>" +
                            "<span ng-if='settings.isModal === true'>{{ weekday[1] }}</span><span ng-if='settings.isModal === false'>{{ weekday[0] }}</span>" +
                        "</div>" +
                    "</div>" +
                    "<div class='days-container' style='height:{{ height }};'>" + 
                        "<div class='day' ng-if='isInTimeSelectMode === false' ng-class='{\"sunday\": ($index + 1) % 7 === 0, \"not-this-month\": day.month() !== month}' ng-repeat='day in days track by $index' ng-click='select(day.unix())'>{{ day.date() }}</div>" +
                        "<div class='hour' ng-if='isInTimeSelectMode === true' ng-class='{\"clock-face\": $index === 5 || $index === 6 || $index === 9 || $index === 10}' ng-repeat='hour in hours track by $index'>{{ hour }}</div>" +
                    "</div>";

                var height = 0;
                var ctrl = scope.ctrl = controller;
                var days = scope.days = [];
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
                var hours = scope.hours = [
                    10, 11, 12, 1,
                    9,  "", "", 2,
                    8,  "", "", 3,
                    7,  6,  5,  4
                ];
                var dayParts = scope.dayParts = [
                    [1, "До полуночи"],
                    [2, "После полуночи"]
                ];
                var now = moment(new Date());
                var date = moment(now);
                var month = scope.month = moment(date).month();
                var year = scope.year = moment(date).year();
                var years = scope.years = [];
                var dayPart = scope.dayPart = 1;
                for (var i = moment(date).year() - 5; i < moment(date).year() + 5; i++) {
                    years.push(i);
                    if (i === moment(date).year())
                        selectedYear = i;
                }

                var isInTimeSelectMode = scope.isInTimeSelectMode = false;
                var isInMinutesSelectMode = scope.isInMinutesSelectMode = false;


                var settings = scope.settings = {
                    isModal: scope.dateTimePickerModal !== null && scope.dateTimePickerModal !== undefined ? true : false,
                    isOpened: false,
                    isTimeEnabled: scope.dateTimePickerEnableTime !== null && scope.dateTimePickerEnableTime !== undefined ? true : false,
                    title: "",
                    element: element
                };


                $log.info("settings = ", scope.settings);


                var recalculate = function (monthNumber) {
                    if (monthNumber !== undefined) {
                        var daysInMonth = moment(monthNumber + "-" + scope.year, "MM-YYYY").daysInMonth();
                        var weekDayOfFirstDay = moment("01." + monthNumber + "." + scope.year, "DD.MM.YYYY").weekday();
                        var weekDayOfLastDay = moment(daysInMonth + "." + monthNumber + "." + scope.year, "DD.MM.YYYY").weekday();
                        var lastDayInSelectedMonth = "";
                        $log.log("in month number " + monthNumber + " - " + daysInMonth + " days");
                        $log.log("first day in month = ", weekDayOfFirstDay);
                        $log.log("last day in month = ", weekDayOfLastDay);

                        days.splice(0, days.length);
                        if (weekDayOfFirstDay > 0) {
                            var start = moment("01." + monthNumber + "." + scope.year + " 00:00", "DD.MM.YYYY HH:mm").subtract(weekDayOfFirstDay, "days");
                            $log.log("first day in calendar = ", moment(start).format("DD.MM.YYYY"));
                            for (var x = 0; x < weekDayOfFirstDay; x++) {
                                var day = moment(start).add(x, "days");
                                days.push(day);
                            }
                        }
                        for (var i = 1; i <= daysInMonth; i++) {
                            var day = moment(i + "." + monthNumber + "." + scope.year + " 00:00", "DD.MM.YYYY HH:mm");
                            days.push(day);
                            if (moment(day).date() === daysInMonth) {
                                lastDayInSelectedMonth = day;
                                $log.log("last day = ", moment(lastDayInSelectedMonth).format("DD.MM.YYYY"));
                            }

                            $log.log("last day in month2 = ", weekDayOfLastDay);

                        }
                        if (weekDayOfLastDay < 6) {
                            //$log.log("last day in calendar = ", moment(start).format("DD.MM.YYYY"));
                            var start = moment(lastDayInSelectedMonth).add(1, "days");
                            for (var i = 1; i <= (6 - weekDayOfLastDay); i++) {
                                var day = moment(lastDayInSelectedMonth).add(i, "days");
                                days.push(day);
                            }
                        }
                    } else
                        return $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> dateTimePicker directive: Не задан параметр - порядковый номер месяца");
                };


                var redraw = function (elm) {
                    if (elm !== undefined) {
                        var elementWidth = angular.element(element).prop("clientWidth");
                        var elementHeight = angular.element(element).prop("clientHeight");
                        var elementLeft = angular.element(element).prop("offsetLeft");
                        var elementTop = angular.element(element).prop("offsetTop");
                        var containerWidth = angular.element(elm).prop("clientWidth");
                        var containerHeight = angular.element(elm).prop("clientHeight");
                        var containerScrollTop = document.body.scrollTop;
                        var windowWidth = $window.innerWidth;
                        var windowHeight = $window.innerHeight;
                        var left = 0;
                        var top = 0;
                        if (scope.settings.isModal === true) {
                            left = (windowWidth / 2) - angular.element(elm).prop("clientWidth") / 2 + "px";
                            top = (windowHeight / 2) - ((angular.element(elm).prop("clientHeight")) / 2) + containerScrollTop + "px"
                        } else {
                            if (containerWidth > elementWidth) {
                                if ((elementLeft > (containerWidth - elementWidth) / 2) && (elementLeft < (windowWidth - elementLeft) + containerWidth / 2))
                                    left = elementLeft - ((containerWidth - elementWidth) / 2);
                            } else
                                left = angular.element(element).prop("offsetLeft") + "px";

                            if (((elementTop - containerHeight) - containerScrollTop) + 10 < 0) {
                                top = elementTop + elementHeight + 10 + "px";
                            } else
                                top = (angular.element(element).prop("offsetTop") - angular.element(elm).prop("clientHeight")) - 10 + "px";
                        }
                        angular.element(elm).css("left", left);
                        angular.element(elm).css("top", top);
                        scope.height = containerHeight - 35 - 20 + "px";
                        return true;
                    } else
                        return $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> dateTimePicker directive: Не задан параметр - HTML-элемент");
                };


                
                controller.$parsers.push(function (value) {

                });

                controller.$formatters.push(function (value) {
                    return scope.settings.isTimeEnabled === true ? moment.unix(value).format("DD MMM YYYY, HH:mm") : moment.unix(value).format("DD MMM YYYY");
                });


               


                scope.prevMonth = function () {
                    scope.date = moment(scope.date).subtract(1, "months");
                    moment(scope.date).day(1);
                    $log.log("currentDate = " + moment(scope.date).format("DD.MM.YYYY"));
                    scope.month = moment(scope.date).month();
                    $log.log(moment(scope.date).month());
                    scope.year = moment(scope.date).year();
                    recalculate(scope.month + 1);
                };


                scope.nextMonth = function () {
                    scope.date = moment(scope.date).add(1, "months");
                    moment(scope.date).day(1);
                    $log.log("currentDate = " + moment(scope.date).format("DD.MM.YYYY"));
                    scope.month = moment(scope.date).month();
                    scope.year = moment(scope.date).year();
                    recalculate(scope.month + 1);
                };


                scope.select = function (timestamp) {
                    if (timestamp !== undefined) {
                        $log.log("selected value = ", timestamp);
                        if (scope.settings.isTimeEnabled === true) {
                            if (scope.isInTimeSelectMode === false) {
                                scope.isInTimeSelectMode = true;

                                if (scope.settings.isMinutesEnabled === true) {

                                }
                            }

                            var startOfTheDay = moment.unix(timestamp).hours(0).minutes(0).seconds(0).unix();
                        } else
                            scope.ngModel = timestamp;
                    }
                };


                var instance = $dateTimePicker.push(scope);
                var container = document.createElement("div");
                container.setAttribute("id", instance.id);
                container.className = "ui-date-time-picker2";
                if (scope.settings.isModal === true)
                    container.classList.add("modal");
                container.innerHTML = template;
                document.body.appendChild(container);
                $compile(container)(scope);
                angular.element(element).css("cursor", "pointer");
                recalculate(scope.month + 1);


                scope.open = function () {
                    if (scope.settings.isModal === true) {
                        var fog = document.getElementsByClassName("krypton-ui-fog");
                        document.body.style.overflow = "hidden";
                        if (fog.length === 0) {
                            var fogElement = document.createElement("div");
                            fogElement.className = "krypton-ui-fog";
                            document.body.appendChild(fogElement);
                            angular.element(fogElement).css("display", "block");
                        } else
                            angular.element(fog[0]).css("display", "block")
                    }
                    angular.element(container).css("display", "block");
                    scope.settings.isOpened = true;
                    redraw(container);
                };


                scope.close = function () {
                    if (scope.settings.isModal === true) {
                        var fog = document.getElementsByClassName("krypton-ui-fog");
                        angular.element(fog[0]).css("display", "none");
                        document.body.style.overflow = "auto";
                    }
                    angular.element(container).css("display", "none");
                    scope.settings.isOpened = false;
                };


                container.addEventListener("DOMSubtreeModified", function () {
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