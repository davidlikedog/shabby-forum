angular.module('myModule', ['ng','ngRoute'])
.controller("isLoginInCtrl", function ($scope, $http) {
    var status = sessionStorage.getItem("status");
    var name = sessionStorage.getItem("name");
    $scope.name = name ? name : '未登录';
    $scope.loginIn = false;
    $scope.offLine = true;
    if (status === "200" && name !== null) {
        $scope.loginIn = true;
        $scope.offLine = false;
    }
    $scope.unsetUser = function () {
        sessionStorage.removeItem("status");
        sessionStorage.removeItem("name");
        $http.get("./s/server.php?addr=loginIn&act=offLine").success(function (data) {
            alert(data.msg);
            window.location.href = "";
        })
    }
}).controller('indexCtrl', function ($scope, $http) {
    $scope.isLoading=true;
    $scope.hasMore=true;
    $http.get("./s/server.php?addr=index&act=getAllMsg&start=0").success(function (data) {
        $scope.allMessage = data;
        $scope.isLoading=false;
    });
    $scope.getMore=function() {
        $scope.isLoading=true;
        $http.get("./s/server.php?addr=index&act=getAllMsg&start="+$scope.allMessage.length+"").success(function(data) {
            if(data.length<8) {
                $scope.hasMore=false;
            }
            $scope.allMessage = $scope.allMessage.concat(data);
            $scope.isLoading=false;
        })
    };
}).controller('myForumCtrl',function($scope, $http){
    $scope.isLoading=true;
    $scope.hasMore=true;
    var name = sessionStorage.getItem("name");
    var status = sessionStorage.getItem("status");
    if (status === "200" && name !== null){
        $http.get('./s/server.php?addr=myForum&act=getMyMsg&start=0&name='+name+'').success(function(data) {
            if(data.status===404) {
                alert('对不起，请登录！');
                window.location.href = "";
            }else{
                $scope.myMsg = data;
                $scope.isLoading=false;
            }
        });
        $scope.getMore=function() {
            $scope.isLoading=true;
            $http.get('./s/server.php?addr=myForum&act=getMyMsg&start='+$scope.myMsg.length+'&name='+name+'').success(function(data) {
                if(data.length<8) {
                    $scope.hasMore=false;
                }
                $scope.myMsg = $scope.myMsg.concat(data);
                $scope.isLoading=false;
            })
        };
        $scope.modify=function(id,$event) {
            $event.stopPropagation();
            window.location.href = "#/modifyForum?id="+id.n.id;
        };
        $scope.enter=function(id,$event) {
            $event.stopPropagation();
            window.location.href="#/myMsgDetail?id="+id.n.id;
        };
        $scope.delOne=function(id,$event) {
            $event.stopPropagation();
            if(window.confirm("你确定删除它吗？")){
                var delId = id.n.id;
                $http.get('./s/server.php?addr=myForum&act=delOne&id='+delId+'&name='+name).success(function(data) {
                    if(data.status===200) {
                        alert(data.result+' 立即返回‘我的发布’页面');
                        window.location.href = "#/myForum?t="+Math.floor(Math.random());
                    }else if(data.status===404){
                        alert(data.err+' 立即返回主页面');
                        window.location.href = "";
                    }
                })
            }
        };
    }
}).controller('modifyForumCtrl',function($scope, $http, $routeParams) {
    var E = window.wangEditor;
    var editor = new E('#div1','#div2');
    editor.customConfig.menus = [
        'head',
        'bold',
        'italic',
        'underline',
        'link',
        'justify',
        'image',
        'code'
    ];
    var imgs = [];
    editor.customConfig.uploadImgServer = './s/server.php?addr=addMsg&act=uploadImg';
    editor.customConfig.uploadImgHooks = {
        success: function (xhr, editor, result) {
            for (var i = 0; i < result.data.length; i++) {
                imgs.push(result.data[i]);
            }
        }
    };
    editor.create();

    var data = new FormData();
    var newHeadImg = null;
    var oldHeadImg = '';
    $scope.reader = new FileReader();
    $scope.imgSrc = null;
    $scope.img_upload=function(files) {
        newHeadImg = files[0];
        $scope.reader.readAsDataURL(files[0]);  //FileReader的方法，把图片转成base64
        $scope.reader.onload = function(ev) {
            $scope.$apply(function(){
                $scope.imgSrc = ev.target.result;  //接收base64
            });
        };
    };

    var name = sessionStorage.getItem("name");
    var status = sessionStorage.getItem("status");

    var arr=null;
    var reg=/<img\s+(.*?)src\s*=\s*['"]([^'"]*)['"]/g;

    if(status === "200" && name !== null) {
        $http.get('./s/server.php?addr=myForum&act=getModifyData&id='+$routeParams.id+'&name='+name).success(function(data) {
            if(data.status===404) {
                alert(data.err + ' 立即返回主页面');
                window.location.href = "";
            }else if(data.status===200) {
                $scope.header = data.header;
                $scope.imgSrc = './img/headImg/' + data.headImg;
                oldHeadImg += data.headImg;
                $scope.description = data.description;
                editor.txt.html(data.content);
                while((arr=reg.exec(data.content))!=null){
                    imgs.push(RegExp.$2);
                }
            }
        });

        $scope.modifyOne=function() {

            var uploadImg = [];
            while((arr=reg.exec(editor.txt.html()))!=null){
                uploadImg.push(RegExp.$2);
            }
            var uploadObj={};
            for(var t=0;t<uploadImg.length;t++){
                uploadObj[uploadImg[t]]=uploadImg[t];
            }
            var elseImg=[];
            for(var s=0;s<imgs.length;s++){
                if(uploadObj[imgs[s]]===undefined){
                    elseImg.push('.'+imgs[s]);
                }
            }
            var delImg = elseImg.join(",");

            console.log(imgs);
            console.log(elseImg);

            data.append('id', $routeParams.id);
            data.append('header', $scope.header);
            data.append('newHeadImg', newHeadImg);
            data.append('oldHeadImg', oldHeadImg);
            data.append('description', $scope.description);
            data.append('content', editor.txt.html());
            data.append('name', name);
            data.append('delImg', delImg);

            $http({
                method: 'post',
                url: './s/server.php?addr=myForum&act=modifyMsg',
                data:data,
                headers: {'Content-Type': undefined},
                transformRequest: angular.identity
            }).success(function(data) {
                if(data.status===200) {
                    alert(data.result + ' 立即返回主页面');
                    window.location.href = '';
                }else{
                    alert('修改失败，立即返回主页面');
                    window.location.href = '';
                }
            })
        };
    }else{
        alert('请登录');
        window.location.href = "";
    }

}).controller('loginCtrl', function ($scope, $http) {
    $scope.account = '';
    $scope.password = '';
    $scope.submit = function () {
        if ($scope.account === '') {
            alert('账号不能为空');
        } else if ($scope.password === '') {
            alert('密码不能为空')
        } else {
            $http({
                method: 'post',
                url: './s/server.php?addr=loginIn&act=verify',
                data: {account: $scope.account, password: $scope.password},
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data);
                }
            }).success(function (data) {
                if(data.status===200) {
                    var keyStatus = "status";
                    var keyName = "name";
                    sessionStorage.setItem(keyStatus, "200");
                    sessionStorage.setItem(keyName, data.name);
                    window.location.href = "";
                }else{
                    alert('登录失败');
                    $scope.account = '';
                    $scope.password = '';
                    window.location.href = "#/loginIn";
                }
            })
        }
    }
}).controller('myMsgDetail',function($scope, $http, $routeParams) {
    var id = $routeParams.id;
    var name = sessionStorage.getItem("name");
    var status = sessionStorage.getItem("status");
    if(status === "200" && name !== null) {
        $http.get('./s/server.php?addr=myForum&act=getOneMsgDetail&id=' + id).success(function(data) {
            $scope.id = data.id;
            $scope.myMsg = data;
        });
        $scope.modify=function() {
            window.location.href = "#/modifyForum?id="+$scope.id;
        };
        $scope.delOne=function() {
            if(window.confirm("你确定删除它吗？")){
                $http.get('./s/server.php?addr=myForum&act=delOne&id='+$scope.id).success(function(data) {
                    if(data.status===200) {
                        alert(data.result+'立即返回‘我的发布’页面');
                        window.location.href = "#/myForum";
                    }else if(data.status===404){
                        alert(data.err+' 立即返回主页面');
                        window.location.href = "";
                    }
                })
            }
        };
    }else{
        alert('请登录');
        window.location.href = "";
    }

}).controller('msgDetailCtrl', function ($scope, $http, $routeParams) {
    $scope.showButton=false;
    $scope.showReplay = false;
    $scope.hasComment=false;
    $scope.replays = '';
    var name = sessionStorage.getItem("name");
    var status = sessionStorage.getItem("status");
    $http.get("./s/server.php?addr=msgDetail&act=getMsgDetail&id=" + $routeParams.id).success(function (data) {
        $scope.msg = data;
    });
    $http.get("./s/server.php?addr=msgDetail&act=getConversion&id=" + $routeParams.id).success(function(data) {
        if(data.status===404) {
            $scope.conversion = [{mgs:'目前还没有评论',time:''}];
        }else{
            $scope.conversion = data;
            $scope.show = true;
            $scope.hasComment = true;
            $scope.showButton = true;
        }
    });
    $scope.replay=function(id) {
        if(name){
            $scope.showReplay = true;
            var str=id.$parent.n.msg;
            var end=str.indexOf("评论");
            var whoYouReplay=str.slice(0,end);
            $scope.replays=name + '评论' + whoYouReplay + '：';
        }else{
            alert("请登录");
        }
    };
    $scope.comment = '';
    $scope.addComment=function() {
        if(status === "200" && name !== null) {
            $http({
                method: 'post',
                url: './s/server.php?addr=msgDetail&act=addOneComment',
                data: {comment: $scope.comment, name: name, articleId:$routeParams.id, hasComment:$scope.hasComment},
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data);
                }
            }).success(function(data) {
                if(data.status===200) {
                    $http.get("./s/server.php?addr=msgDetail&act=getConversion&id=" + $routeParams.id).success(function(datas) {
                        $scope.conversion = datas;
                    });
                }else{
                    alert(data.msg);
                }
            });
        }else{
            alert('请登录');
            window.location.href = '';
        }
    };
    $scope.addReplay=function(msg){
        if(status === "200" && name !== null){
            $http({
                method: 'post',
                url: './s/server.php?addr=msgDetail&act=addOneReplay',
                data: {replay: msg.replays, name: name, articleId: $routeParams.id},
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data);
                }
            }).success(function(data){
                if(data.status===200) {
                    $scope.showReplay = false;
                    $http.get("./s/server.php?addr=msgDetail&act=getConversion&id=" + $routeParams.id).success(function(datas) {
                        $scope.conversion = datas;
                    });
                }else{
                    alert(data.msg);
                }
            });
        }else{
            alert('请登录');
        }
    }
}).controller('addOneCtrl', function ($scope, $http) {

    var E = window.wangEditor;
    var editor = new E('#div1','#div2');
    editor.customConfig.menus = [
        'head',
        'bold',
        'italic',
        'underline',
        'link',
        'justify',
        'image',
        'code'
    ];
    var imgs = [];
    editor.customConfig.uploadImgServer = './s/server.php?addr=addMsg&act=uploadImg';
    editor.customConfig.uploadImgHooks = {
        success: function (xhr, editor, result) {
            for (var i = 0; i < result.data.length; i++) {
                imgs.push(result.data[i]);
            }
        }
    };
    editor.create();

    var data = new FormData();
    var headImg = null;
    $scope.reader = new FileReader();
    $scope.imgSrc = null;
    $scope.haveHeadImg = false;
    $scope.img_upload=function(files) {
        headImg = files[0];
        $scope.reader.readAsDataURL(files[0]);  //FileReader的方法，把图片转成base64
        $scope.reader.onload = function(ev) {
            $scope.$apply(function(){
                $scope.imgSrc = ev.target.result;  //接收base64
                $scope.haveHeadImg = true;
            });
        };
    };

    var name = sessionStorage.getItem("name");
    var status = sessionStorage.getItem("status");
    $scope.isLoading=false;
    if (status === "200" && name !== null) {
        $scope.header = "";
        $scope.description = "";
        $scope.addOne = function () {
            if ($scope.header === "") {
                alert('标题不能为空');
            } else if ($scope.description === "") {
                alert('描述不能为空');
            } else if (editor.txt.html() === "") {
                alert('内容不能为空');
            } else {
                var reg=/<img\s+(.*?)src\s*=\s*['"]([^'"]*)['"]/g;
                var arr=null;
                var uploadImg=[];
                while((arr=reg.exec(editor.txt.html()))!=null){
                    uploadImg.push(RegExp.$2);
                }
                var uploadObj={};
                for(var t=0;t<uploadImg.length;t++){
                    uploadObj[uploadImg[t]]=uploadImg[t];
                }
                var elseImg=[];
                for(var s=0;s<imgs.length;s++){
                    if(uploadObj[imgs[s]]===undefined){
                        elseImg.push('.'+imgs[s]);
                    }
                }
                var delImg = elseImg.join(",");

                // console.log(imgs);
                // console.log(delImg);
                // console.log(editor.txt.html());
                // console.log(headImg);
                data.append('headImg', headImg);
                data.append('header', $scope.header);
                data.append('description', $scope.description);
                data.append('content', editor.txt.html());
                data.append('name', name);
                data.append('delImg', delImg);

                $scope.isLoading = true;
                $http({
                    method: 'post',
                    url: './s/server.php?addr=addMsg&act=addMsg',
                    data:data,
                    headers: {'Content-Type': undefined},
                    transformRequest: angular.identity
                }).success(function (data) {
                    if(data.status!==200){
                        alert(data.result);
                        console.log('有错误');
                    }else{
                        alert(data.result + '立即返回主页');
                        window.location.href = "";
                    }
                    $scope.isLoading=false;
                })
            }
        }
    } else {
        window.location.href = "#/loginIn";
    }
}).controller('registerCtrl', function ($scope, $http) {
    var allowSubmit = false;
    $scope.name = '';
    $scope.verify = function () {
        if ($scope.name === '') {
            $scope.result = {color: 'red', result: '名字不能为空'};
            allowSubmit = false;
        } else {
            $http({
                method: 'post',
                url: './s/server.php?addr=register&act=verifyName',
                data: {name: $scope.name},
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data);
                }
            }).success(function (data) {
                $scope.result = data;
                if (data.color === "green") {
                    allowSubmit = true;
                }
            })
        }
    };
    $scope.account = '';
    $scope.chickAccount = function () {
        if ($scope.account === '') {
            $scope.accountColor = 'red';
            $scope.accountText = '账号不能为空';
            allowSubmit = false;
        } else {
            $scope.accountColor = 'green';
            $scope.accountText = '通过';
            allowSubmit = true;
        }
    };
    $scope.password = '';
    $scope.chickPass = function () {
        if ($scope.password === '') {
            $scope.passColor = 'red';
            $scope.passText = '密码不能为空';
            allowSubmit = false;
        } else {
            $scope.passColor = 'green';
            $scope.passText = '通过';
            allowSubmit = true;
        }
    };
    $scope.repeatPassword = '';
    $scope.chickRepPass = function () {
        if ($scope.repeatPassword === '') {
            $scope.repPassColor = 'red';
            $scope.repPassText = '重复密码不能为空';
            allowSubmit = false;
        } else if ($scope.password !== $scope.repeatPassword) {
            $scope.repPassColor = 'red';
            $scope.repPassText = '密码不一致';
            allowSubmit = false;
        } else {
            $scope.repPassColor = 'green';
            $scope.repPassText = '通过';
            allowSubmit = true;
        }
    };
    $scope.submit = function () {
        if (allowSubmit) {
            $http({
                method: 'post',
                url: './s/server.php?addr=register&act=addOne',
                data: {name: $scope.name, account: $scope.account, password: $scope.password},
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data);
                }
            }).success(function (data) {
                console.log(data);
                if (data.status === 200) {
                    alert(data.msg + '立刻转入主页面');
                    $http({
                        method: 'post',
                        url: './s/server.php?addr=loginIn&act=verify',
                        data: {account: $scope.account, password: $scope.password},
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        transformRequest: function (data) {
                            return $.param(data);
                        }
                    }).success(function (data) {
                        var keyStatus = "status";
                        var keyName = "name";
                        sessionStorage.setItem(keyStatus, "200");
                        sessionStorage.setItem(keyName, data.name);
                        window.location.href = "";
                    })
                } else {
                    alert(data.msg);
                }
            })
        }
    }
}).config(function($routeProvider) {
    $routeProvider.when('/loginIn',{
        templateUrl:'tem/loginIn.html',
        controller:'loginCtrl'
    }).when('/index',{
        templateUrl:'tem/main.html',
        controller:'indexCtrl'
    }).when('/myForum',{
        templateUrl:'tem/myForum.html',
        controller:'myForumCtrl'
    }).when('/register',{
        templateUrl:'tem/register.html',
        controller:'registerCtrl'
    }).when('/addForum',{
        templateUrl:'tem/addForum.html',
        controller:'addOneCtrl'
    }).when('/msgDetail',{
        templateUrl:'tem/msgDetail.html',
        controller:'msgDetailCtrl'
    }).when('/myMsgDetail',{
        templateUrl:'tem/myMsgDetail.html',
        controller:'myMsgDetail'
    }).when('/modifyForum',{
        templateUrl:'tem/modifyForum.html',
        controller:'modifyForumCtrl'
    }).otherwise({
        redirectTo:'/index'
    })
}).filter('to_trusted', function ($sce) {
    return function (text) {
        return $sce.trustAsHtml(text);
    }
});