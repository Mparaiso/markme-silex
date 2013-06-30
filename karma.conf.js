// http://karma-runner.github.io/0.8/config/configuration-file.html
basePath = './';

files = [
    JASMINE,
    JASMINE_ADAPTER,
    'www/static/js/jquery.min.js',
    'www/static/vendor/angular/angular.js',
    'www/static/vendor/angular/angular-*.js',
    'www/static/js/app/modules/Application.js',
    'www/static/js/**/*.js',
    'www/static/vendor/**/*.js',
    'tests-javascript/unit/**/*.js'
];

autoWatch = true;

browsers = ['Chrome'];

junitReporter = {
    outputFile: 'test_out/unit.xml',
    suite: 'unit'
};

reporters= ['dots',"progress","coverage"]