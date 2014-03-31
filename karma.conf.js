// http://karma-runner.github.io/0.8/config/configuration-file.html
basePath = './';

files = [
    JASMINE,
    JASMINE_ADAPTER,
    'web/static/js/jquery.min.js',
    'web/static/vendor/angular/angular.js',
    'web/static/vendor/angular/angular-*.js',
    'web/static/js/app/modules/Application.js',
    'web/static/js/**/*.js',
    'web/static/vendor/**/*.js',
    'tests-javascript/unit/**/*.js'
];

autoWatch = true;

browsers = ['Chrome'];

junitReporter = {
    outputFile: 'test_out/unit.xml',
    suite: 'unit'
};

reporters= ['dots',"progress","coverage"]