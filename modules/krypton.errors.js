"use strict";

var kryptonErrorsModule = angular.module("krypton.errors", [])
    .config(function ($provide) {
        $provide.factory("$errors", ["$log", function ($log) {
            var module = {};

            return module;
        }]);
    })
    .run(function () {

    });