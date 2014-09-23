/**
 * @copyrights 2014 mparaiso <mparaiso@online.fr>
 */
angular.module("ApplicationFilters", [])
        .filter('capitalize', function() {
            /*
             * @param String string
             */
            return function(text) {
                return text.trim().replace(/^(\w)/, function(match, firstLetter) {
                    return firstLetter.toUpperCase();
                });
            }
        })
        .filter("trim", function() {
            return function(text, len) {
                if (typeof text === "string" &&
                        typeof len === "number" && text.length > len) {
                    return text.substr(0, len - 1) + "...";
                }
                return text;
            };
        });