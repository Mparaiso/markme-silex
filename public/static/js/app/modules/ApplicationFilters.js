
var ApplicationFilters = angular.module("ApplicationFilters",[]);

ApplicationFilters.filter("trim",function(){
    return function(text,len){
        if(typeof text === "string" && 
            typeof len === "number" && text.length>len){
            return text.substr(0,len-1)+"...";
        }
        return text;
    };
});