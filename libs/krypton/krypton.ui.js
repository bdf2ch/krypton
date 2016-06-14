(function () {
    angular
        .module("krypton.ui", [])
            .factory("$dateTimePicker", dateTimePickerFactory)
            .directive("uiDateTimePicker", dateTimePickerDirective)
            .directive("uiModelField", modelFieldDirective);
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
            value: 0,
            isModal: false,
            isOpened: false,
            isTimeEnabled: false,
            isTroughNavigationEnabled: false,
            scope: {},

            open: function () {
                this.isOpened = true;
                this.scope.open();
            },

            close: function () {
                this.isOpened = false;
                this.scope.close();
            }
        });
    };


    /**
     * dateTimePicker directive
     * Виджет выбора даты / времени
     */
    function dateTimePickerDirective ($log, $errors, $compile, $window, $document, $dateTimePicker) {
        return {
            restrict: "A",
            require: "ngModel",
            scope: {
                ngModel: "=",
                dateTimePickerModal: "=",
                dateTimePickerEnableTime: "=",
                dateTimePickerEnableMinutes: "=",
                dateTimePickerThroughNavigation: "="
            },
            link: function (scope, element, attrs, controller) {

                var template =
                    "<div class='toolbar'>" +
                        "<div class='control'><button class='width-100 blue' ng-class='{ \"very-big\": settings.isModal === true }' ng-click='prev()'>&larr;</button></div>" +
                        "<div class='content'>" +
                            "<select class='width-60 no-border' ng-if='isInTimeSelectMode === false' ng-model='month' ng-options='month[0] as month[1] for month in months'></select>" +
                            "<select class='width-40 no-border' ng-if='isInTimeSelectMode === false' ng-model='year' ng-options='year as year for year in years'></select>" +
                            "<span class='selected-date width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === false && settings.isModal === false'>{{ value.format('DD.MM.YYYY, HH ч.') }}</span>" +
                            "<span class='selected-date width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === false && settings.isModal === true'>{{ value.format('DD MMM YYYY, HH ч.') }}</span>" +
                            "<span class='selected-date width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === true && settings.isModal === false'>{{ value.format('DD.MM.YYYY, HH:mm') }}</span>" +
                            "<span class='selected-date width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === true && settings.isModal === true'>{{ value.format('DD MMM YYYY, HH:mm') }}</span>" +
                        "</div>" +
                        "<div class='control'><button class='width-100 blue' ng-class='{ \"very-big\": settings.isModal === true }' ng-click='next()'>&rarr;</button></div>" +
                    "</div>" +
                    "<div class='weekdays'>" +
                        "<div class='day width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === false'>часы</div>" +
                        "<div class='day width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === true'>минуты</div>" +
                        "<div class='day' ng-if='isInTimeSelectMode === false' ng-repeat='weekday in weekdays track by $index'>" +
                            "<span ng-if='settings.isModal === true'>{{ weekday[1] }}</span><span ng-if='settings.isModal === false'>{{ weekday[0] }}</span>" +
                        "</div>" +
                    "</div>" +
                    "<div class='days-container' style='height:{{ height + \"px\" }}; max-height:{{ height }};'>" +
                        "<div class='day' ng-if='isInTimeSelectMode === false' ng-class='{\"sunday\": ($index + 1) % 7 === 0, \"not-this-month\": day.month() !== month, \"current\": day.date() === now.date() && day.month() === value.month()}' ng-repeat='day in days track by $index' ng-click='select(day.unix())'>{{ day.date() }}</div>" +
                        "<div style='line-height: {{ height / 4 + \"px\" }};' class='hour' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === false' ng-class='{\"current\": value.hours() === $index}' ng-repeat='hour in hours track by $index' ng-click='select($index)'>{{ hour }}</div>" +
                        "<div style='line-height: {{ height / 3 + \"px\" }};' class='minute' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === true' ng-class='{\"current\": value.minutes() === ($index * 5)}' ng-repeat='minute in minutes track by $index' ng-click='select(minute)'>{{ minute }}</div>" +
                    "</div>";

                var height = scope.height = 0;
                var calendarHeight = scope.calendarHeight = 0;
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
                    "00", "01", "02", "03",
                    "04", "05", "06", "07",
                    "08", "09", "10", "11",
                    "12", "13", "14", "15",
                    "16", "17", "18", "19",
                    "20", "21", "22", "23"
                ];
                var minutes = scope.minutes = [
                    "00", "05", "10", "15",
                    "20", "25", "30", "35",
                    "40", "45", "50", "55"
                ];

                var now = scope.now = moment(new Date());
                var date = scope.date = moment(now);
                var value = scope.value = moment(now);
                var day = scope.day = value.date();
                var month = scope.month = date.month();
                var year = scope.year = moment(value).year();
                //var hour = scope.hour = moment(value).hours();
                //var minute = scope.minute = moment(value).minutes();
                var years = scope.years = [];

                for (var i = moment(value).year() - 5; i < moment(value).year() + 5; i++) {
                    years.push(i);
                    if (i === moment(value).year())
                        selectedYear = i;
                }

                var isInTimeSelectMode = scope.isInTimeSelectMode = false;
                var isInMinutesSelectMode = scope.isInMinutesSelectMode = false;

                var settings = scope.settings = {
                    // Модальный режим отображения виджета
                    isModal: scope.dateTimePickerModal !== null && scope.dateTimePickerModal !== undefined ? true : false,
                    isOpened: false,
                    // Режим выбора времени
                    isTimeEnabled: scope.dateTimePickerEnableTime !== null && scope.dateTimePickerEnableTime !== undefined ? true : false,
                    // Сквозная навигация по часам и минутам
                    isTroughNavigationEnabled: scope.dateTimePickerThroughNavigation !== null && scope.dateTimePickerThroughNavigation !== undefined ? true : false,
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
                        return $errors.add(
                            ERROR_TYPE_DEFAULT,
                            "krypton.ui -> dateTimePicker directive: Не задан параметр - порядковый номер месяца"
                        );
                };


                var redraw = function (elm) {
                    if (elm !== undefined) {
                        var elementWidth = angular.element(element).prop("clientWidth");
                        var elementHeight = angular.element(element).prop("clientHeight");
                        var elementLeft = angular.element(element).prop("offsetLeft");
                        var elementTop = angular.element(element).prop("offsetTop");
                        var containerWidth = angular.element(elm).prop("clientWidth");
                        var containerHeight = angular.element(elm).prop("clientHeight");
                        var elementScrollTop = 0;
                        var elementScrollLeft = 0;
                        var windowWidth = $window.innerWidth;
                        var windowHeight = $window.innerHeight;
                        var left = 0;
                        var top = 0;

                        var parent = element[0].offsetParent;
                        //$log.log("parent = ", parent);
                        while (parent) {
                            //elementTop = elementTop + parent.offsetTop;
                            //elementLeft = elementLeft + parent.offsetLeft;
                            elementScrollLeft = elementScrollLeft + parent.scrollLeft;
                            elementScrollTop = elementScrollTop + parent.scrollTop;
                            parent = parent.offsetParent;
                        }
                        //$log.log("containerTop = ", elementScrollTop);
                        //$log.log("containerLeft = ", elementScrollLeft);
                        

                            //return {top: Math.round(top), left: Math.round(left), offsetX: Math.round(offsetX), offsetY: Math.round(offsetY)};
                        //};

                        //$log.log(angular.element($document).parent());
                        
                        if (scope.settings.isModal === true) {
                            left = (windowWidth / 2) - angular.element(elm).prop("clientWidth") / 2 + "px";
                            top = (windowHeight / 2) - ((angular.element(elm).prop("clientHeight")) / 2) + "px"
                        } else {
                            if (containerWidth > elementWidth) {
                                if ((elementLeft > (containerWidth - elementWidth) / 2) && (elementLeft < (windowWidth - elementLeft) + containerWidth / 2))
                                    left = elementLeft - ((containerWidth - elementWidth) / 2);
                            } else
                                left = angular.element(element).prop("offsetLeft") + "px";

                            if ((elementTop - containerHeight) + 10 < 0) {
                                top = elementTop + elementHeight + 10 + "px";
                            } else
                                top = angular.element(elm).prop("clientHeight") + elementScrollTop - 10 + "px";
                        }
                        angular.element(elm).css("left", left);
                        angular.element(elm).css("top", top);

                        if (scope.isInTimeSelectMode === true)
                            scope.height = scope.calendarHeight;
                        else {
                            scope.height = "auto";
                            scope.calendarHeight = scope.settings.isModal === true ? containerHeight - 80 : containerHeight - 55;
                        }
                        return true;
                    } else
                        return $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> dateTimePicker directive: Не задан параметр - HTML-элемент");
                };


                
                controller.$parsers.push(function (value) {

                });

                controller.$formatters.push(function (value) {
                    return scope.settings.isTimeEnabled === true ? moment.unix(value).format("DD MMM YYYY, HH:mm") : moment.unix(value).format("DD MMM YYYY");
                });


               


                scope.prev = function () {
                    if (scope.settings.isTimeEnabled === true && scope.isInTimeSelectMode === true) {
                        if (scope.isInMinutesSelectMode === false) {
                            if (scope.settings.isTroughNavigationEnabled === false) {
                                if (scope.value.hours() > 0 && scope.value.hours() <= 23)
                                    scope.value.subtract(1, "hours");
                                else if (scope.value.hours() === 0)
                                    scope.value.hours(23);
                            } else
                                scope.value.subtract(1, "hours");
                            $log.log(scope.value.format("DD.MM.YYYY HH:mm"));
                        } else {
                            if (scope.settings.isTroughNavigationEnabled === false) {
                                if (scope.value.minutes() > 0 && scope.value.minutes() <= 59)
                                    scope.value.subtract(5, "minutes");
                                else if (scope.value.minutes() === 0)
                                    scope.value.minutes(55);
                            } else
                                scope.value.subtract(5, "minutes");
                            $log.log(scope.value.format("DD.MM.YYYY HH:mm"));
                        }
                    } else {
                        scope.date.subtract(1, "months");
                        $log.log("currentDate = " + scope.value.format("DD.MM.YYYY"));
                        scope.month = scope.date.month();
                        scope.year = scope.date.year();
                        recalculate(scope.date.month() + 1);
                    }
                };


                scope.next = function () {
                    if (scope.settings.isTimeEnabled === true && scope.isInTimeSelectMode === true) {
                        if (scope.isInMinutesSelectMode === false) {
                            if (scope.settings.isTroughNavigationEnabled === false) {
                                if (scope.value.hours() >= 0 && scope.value.hours() < 23)
                                    scope.value.add(1, "hours");
                                else if (scope.value.hours() === 23)
                                    scope.value.hours(0);
                            } else
                                scope.value.add(5, "minutes");
                        } else
                            scope.value.add(5, "minutes");
                    } else {
                        scope.date.add(1, "months");
                        moment(scope.date).day(1);
                        $log.log("currentDate = " + moment(scope.date).format("DD.MM.YYYY"));
                        scope.month = moment(scope.date).month();
                        scope.year = moment(scope.date).year();
                        recalculate(scope.date.month() +1);
                    }
                };


                scope.select = function (value) {
                    if (value !== undefined) {
                        $log.log("selected value = ", value);
                        if (scope.settings.isTimeEnabled === true) {
                            if (scope.isInTimeSelectMode === false) {
                                var temp = moment.unix(value).hours(0).minutes(0).seconds(0);
                                scope.month = temp.month();
                                scope.day = temp.date();
                                scope.value.month(scope.month).date(scope.day).hours(0).minutes(0).seconds(0);
                                scope.ngModel = scope.value.unix();
                                scope.isInTimeSelectMode = true;
                                $log.log(scope.value.format("DD.MM.YYYY HH:mm"), scope.value.unix());
                            } else {
                                if (scope.isInMinutesSelectMode === false) {
                                    scope.value.hours(value).minutes(0).seconds(0);
                                    scope.ngModel = scope.value.unix();
                                    scope.isInMinutesSelectMode = true;
                                    $log.log(scope.value.format("DD.MM.YYYY HH:mm"), scope.value.unix());
                                } else {
                                    scope.value.minutes(parseInt(value));
                                    scope.ngModel = scope.value.unix();
                                    $log.log(scope.value.format("DD.MM.YYYY HH:mm"), scope.value.unix());
                                    scope.close();
                                }
                            }
                        } else {
                            var temp = moment.unix(value).hours(0).minutes(0).seconds(0);
                            scope.month = temp.month();
                            scope.day = temp.date();
                            scope.value.month(scope.month).date(scope.day).hours(0).minutes(0).seconds(0);
                            scope.ngModel = scope.value.unix();
                            $log.log(scope.value.format("DD.MM.YYYY HH:mm"), scope.value.unix());
                            scope.value = moment(new Date());
                            scope.close();
                        }
                    } else
                        $log.error("value = ", value);
                };


                var instance = $dateTimePicker.push(scope);
                var container = document.createElement("div");
                container.setAttribute("id", instance.id);
                container.className = "ui-date-time-picker2";
                if (scope.settings.isModal === true) {
                    container.classList.add("modal");
                    var fog = document.getElementsByClassName("krypton-ui-fog");
                    if (fog.length === 0) {
                        var fogElement = document.createElement("div");
                        fogElement.className = "krypton-ui-fog";
                        document.body.appendChild(fogElement);
                    }
                }
                container.innerHTML = template;
                document.body.appendChild(container);
                $compile(container)(scope);
                angular.element(element).css("cursor", "pointer");
                recalculate(scope.date.month() + 1);


                scope.open = function () {
                    if (scope.settings.isModal === true) {
                        var fog = document.getElementsByClassName("krypton-ui-fog");
                        document.body.style.overflow = "hidden";
                        fogElement.classList.add("visible");
                    }
                    angular.element(container).css("display", "block");
                    scope.settings.isOpened = true;
                    redraw(container);
                };


                scope.close = function () {
                    if (scope.settings.isModal === true) {
                        var fog = document.getElementsByClassName("krypton-ui-fog");
                        fog[0].classList.remove("visible");
                        document.body.style.overflow = "auto";
                    }
                    angular.element(container).css("display", "none");
                    scope.settings.isOpened = false;
                    scope.isInMinutesSelectMode = false;
                    scope.isInTimeSelectMode = false;
                    scope.value = new moment(new Date());
                    scope.$apply();
                };


                container.addEventListener("DOMSubtreeModified", function () {
                    redraw(container);
                }, false);


                angular.element($window).bind("resize", function () {
                    redraw(container);
                });


                angular.element($document).bind("mousedown", function (event) {
                    if (scope.settings.isOpened === true && container.contains(event.target) === false && event.target !== element[0])
                        scope.close();
                });


                element.on("mousedown", function () {
                    if (scope.settings.isOpened === false)
                        scope.open();
                });


                element.on("keydown", function (event) {
                    event.preventDefault();
                });

                angular.element(angular.element(element).parent()).on("scroll", function () {
                    $log.log("parent scrolled");
                    redraw(container);
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
    
    
    
    
    function modelFieldDirective ($log) {
        return {
            restrict: "E",
            require: "ngModel",
            template:
                "<div class='ui-data-field {{ class }}'>" +
                    "<div class='input-container'><input type='text'  ng-model='ngModel' ng-change='onChange()'/></div>" +
                    "<div class='controls-container'>" +
                        "<button class='green' title='{{ okTitle }}' ng-class='{\"disabled\": isOkButtonDisabled === true}' ng-disabled='isOkButtonDisabled === true'>&#10003;</button>" +
                        "<button class='red' title='{{ cancelTitle }}' ng-click='cancel()'>&times;</button>" +
                    "</div>" +
                "</div>",
            scope: {
                ngModel: "=",
                //class: "@",
                modelFieldCancelTitle: "@",
                modelFieldOkTitle: "@",
                modelFieldOnCancel: "&",
                modelFieldOnChange: "&"
            },

            link: function (scope, element, attrs, ctrl) {
                scope.controller = ctrl;
                var okTitle = scope.okTitle = scope.modelFieldOkTitle !== undefined && scope.modelFieldOkTitle !== null ? scope.modelFieldOkTitle : "";
                var cancelTitle = scope.cancelTitle = scope.modelFieldCancelTitle !== undefined && scope.modelFieldCancelTitle !== null ? scope.modelFieldCancelTitle : "";
                var isChanged = scope.isChanged = false;
                var isOkButtonDisabled = scope.isOkButtonDisabled = true;
                var oldValue = angular.copy(scope.ngModel);

                
                
                scope.onChange = function () {
                    $log.log("changed");
                    isChanged = true;
                    scope.isOkButtonDisabled = false;
                    if (scope.modelFieldOnChange !== null && scope.modelFieldOnChange !== undefined && typeof scope.modelFieldOnChange === "function") {
                        scope.modelFieldOnChange();
                    }
                    $log.log("changed model = ", scope.ngModel);
                };
                
                

                scope.cancel = function () {
                    $log.log("onCancel");
                    if (scope.modelFieldOnCancel !== null && scope.modelFieldOnCancel !== undefined) {
                        scope.modelFieldOnCancel();
                    }
                    if (isChanged === true)
                        scope.ngModel = oldValue;
                    scope.isChanged = false;
                    scope.isOkButtonDisabled = true;
                };
            }

        }
    };
})();