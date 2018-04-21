<?php

class msgDetailController{
    private function getModel(){
        return modelFactory::M("msgDetailModel");
    }
    function getMsgDetail(){
        $model=$this->getModel();

        $id=$_GET['id'];

        $data=$model->getOneMsg("select forumContent.*,forumUser.name from forumContent inner join forumUser on forumContent.userID=forumUser.id where forumcontent.id=$id");
        echo json_encode($data);
    }
    function getConversion(){
        $model=$this->getModel();
        $id=$_GET['id'];
        $data=$model->getOneMsg("select * from forumMsg where articleId=$id");
        $msg=array();
        if($data==false){
            $msg['status']=404;
            $msg['msg']='目前还没有评论';
        }else{
            $str=substr($data['msg'],0,-1);
            $arr=explode("@",$str);
            foreach($arr as $key=>$value){
                $all=explode("%",$value);
                $msg[]=array("msg"=>$all[0],"time"=>$all[1]);
            }
        }
        echo json_encode($msg);
    }
    function addOneComment(){
        $model=$this->getModel();
        $name=$_POST['name'];
        $comment=$_POST['comment'];
        $articleId=$_POST['articleId'];
        $hasComment=$_POST['hasComment'];
        $date=date("Y-m-d h:i");
        $result=array();
        if($_SESSION['name']['name']!=$name){
            $result['status']=404;
            $result['msg']='不好意思请登录';
        }else{
            $realComment=$name.'评论：'.$comment.'%'.$date.'@';
            if($hasComment==="true"){
                $oldMsg=$model->getOneData("select msg from forumMsg where articleId=$articleId");
                $addNewMsg=$oldMsg.$realComment;
                $data=$model->addOne("update forumMsg set msg='$addNewMsg' where articleId=$articleId");
                if($data==true){
                    $result['status']=200;
                    $result['msg']='添加成功';
                }else{
                    $result['status']=500;
                    $result['msg']='服务器内部错误';
                }
            }else{
                $data=$model->addOne("insert into forumMsg values(null,$articleId,'$realComment')");
                if($data==true){
                    $result['status']=200;
                    $result['msg']='新增成功';
                }else{
                    $result['status']=500;
                    $result['msg']='服务器内部错误';
                }
            }
        }
        echo json_encode($result);
    }
    function addOneReplay(){
        $name=$_POST['name'];
        $replay=$_POST['replay'];
        $articleId=$_POST['articleId'];
        $date=date("Y-m-d h:i");
        $model=$this->getModel();
        $result=array();
        if($_SESSION['name']['name']!=$name){
            $result['status']=404;
            $result['msg']='不好意思请登录';
        }else{
            $realReplay=$replay.'%'.$date.'@';
            $oldMsg=$model->getOneData("select msg from forumMsg where articleId=$articleId");
            $addNewMsg=$oldMsg.$realReplay;
            $data=$model->addOne("update forumMsg set msg='$addNewMsg' where articleId=$articleId");
            if($data==true){
                $result['status']=200;
                $result['msg']='回复成功';
            }else{
                $result['status']=500;
                $result['msg']='服务器内部错误';
            }
        }
        echo json_encode($result);
    }
}