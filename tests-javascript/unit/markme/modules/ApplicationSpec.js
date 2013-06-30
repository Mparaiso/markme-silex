/**
 * TEST :  APPLICATION MODULE
 */
describe("Application", function () {
    var Url,UserService;
    UserService = {
        getCurrentUser: function (callback) {
            return callback({user: "user"});
        }
    }
    Url = {
        getBase: function () {
            return "/base-uri"
        }
    }

    beforeEach(module("Application"));
    beforeEach(module(function ($provide) {
        $provide.value("Url",Url);
        $provide.value("UserService", UserService);
    }))

    /// MainController test

    describe('MainController', function () {
        var mainScope, url;
        /**
         * @see http://docs.angularjs.org/guide/dev_guide.mvc.understanding_controller
         * @see http://sravi-kiran.blogspot.fr/2013/04/UnitTestingAngularJsControllerUsingJasmine.html
         */
        beforeEach(inject(function ($rootScope, $controller) {
            mainScope = $rootScope.$new();
            // on peut passer des mocks de services dans l'argument 2 de $controller function
            // au lieu d'utiliser $provide , voir ci dessus
            var mainCtrl = $controller("MainController", {$scope: mainScope/*, Url: Url*/});
        }));

        // @see http://pivotal.github.io/jasmine/#section-Spies

        it('should have the correct maxSizeUpload', function () {
            expect(mainScope.maxSizeUpload).toBe('5M');
        });

        it("should have the correct base Url", function () {
            expect(mainScope.baseUrl).toEqual("/base-uri")
        });

        it("should have the correct user", function () {
            expect(mainScope.user).toEqual("user")
        })
    });
});