angular.module('myModule', ['ng'])
    .controller('indexCtrl', function ($scope, $http) {
        // $scope.test={a:123,b:1234};
        $http.get("./php/index.php?addr=index&act=getAllMsg").success(function (data) {
            $scope.allMessage = data;
        })

    }).controller('login', function () {

    }).run(function ($http) {
        //配置http post 默认的请求头部
        $http.defaults.headers.post = {'Content-Type': 'application/x-www-form-urlencoded'};
    });