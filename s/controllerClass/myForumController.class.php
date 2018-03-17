<?php

class myForumController{
    private function getModel(){
        return modelFactory::M("myForumModel");
    }
    function getMyMsg(){
        $start=$_GET['start']?$_GET['start']:0;
        $name=$_GET['name'];
        if($_SESSION['name']['name']!=$name){
            $result['status']=404;
            $result['err']='该用户不是目前登录的用户';
            echo json_encode($result);
            exit();
        }
        $count=8;
        $model=$this->getModel();
        $data=$model->getMyMsg("select forumContent.*,forumUser.name from forumContent inner join forumUser on forumContent.userID=forumUser.id where name='$name' order by id desc limit $start,$count");
        echo json_encode($data);
    }
    function delOne(){
        $id=$_GET['id'];
        $name=$_GET['name'];
        $model=$this->getModel();
        $author=$model->getOneData("select forumUser.name from forumContent inner join forumUser on forumContent.userID=forumUser.id where forumContent.id=$id");
        if($_SESSION['name']['name']!=$name){
            $result['status']=404;
            $result['err']='该用户不是目前登录的用户';
            echo json_encode($result);
            exit();
        }elseif($name!=$author){
            $result['status']=404;
            $result['err']='该文章不是你写的，你没权利删除该文章！';
            echo json_encode($result);
            exit();
        }
        $headImg=$model->getOneData("select headImg from forumContent where id=$id");
        $headImgPath="../img/headImg/$headImg";
        unlink($headImgPath);//删除首页图片
        $content=$model->getOneData("select content from forumContent where id=$id");
        preg_match_all('/<img\s+(.*?)src\s*=\s*["][^"]*["]/',$content,$contentImg);
        foreach($contentImg[0] as $key=>$value){
            $src='.'.substr($value,strpos($value,'"'));
            $realSrc=preg_replace('/"/','',$src);
            unlink($realSrc);//删除内容图片
        }
        $data=$model->delOne("delete from forumContent where id=$id");
        if($data===true){
            $result['status']=200;
            $result['result']='删除成功';
            echo json_encode($result);
        }else{
            $result['status']=500;
            $result['result']='数据库错误';
            echo json_encode($result);
        }
    }
    function getModifyData(){
        $model=$this->getModel();
        $id=$_GET['id'];
        $name=$_GET['name'];
        $author=$model->getOneData("select forumUser.name from forumContent inner join forumUser on forumContent.userID=forumUser.id where forumContent.id=$id");
        if($_SESSION['name']['name']!=$name){
            $result['status']=404;
            $result['err']='该用户不是目前登录的用户';
            echo json_encode($result);
            exit();
        }elseif($name!=$author){
            $result['status']=404;
            $result['err']='该文章不是你写的，你没权利编辑该文章！';
            echo json_encode($result);
            exit();
        }

        $data=$model->getModifyData("select * from forumContent where id=$id");
        $data['status']=200;
        echo json_encode($data);
    }
    function getOneMsgDetail(){
        $id=$_GET['id'];
        $model=$this->getModel();
        $data=$model->getModifyData("select forumContent.*,forumUser.name from forumContent inner join forumUser on forumContent.userID=forumUser.id where forumContent.id=$id");
        echo json_encode($data);
    }
    function modifyMsg(){
        $id=$_POST['id'];
        $header=$_POST['header'];
        @$newHeadImg=$_FILES['newHeadImg'];
        $oldHeadImg=$_POST['oldHeadImg'];
        $description=$_POST['description'];
        $content=$_POST['content'];
        $name=$_POST['name'];
        $delImg=$_POST['delImg'];
        $time=date("Y-m-d h:i:s");

        $model=$this->getModel();

        $delImgArr=explode(",",$delImg);
        if(!empty($delImg)){
            foreach($delImgArr as $key=>$value){
                unlink($value);
            }
        }

        if($newHeadImg==null){//=null说明没更新封面
            $data=$model->modifyOne("update forumContent set header='$header',content='$content',time='$time',description='$description' where id=$id");
            if($data===true){
                $result['status']=200;
                $result['result']='操作成功';
                echo json_encode($result);
            }
        }else{

            unlink("../img/headImg/$oldHeadImg");

            $newHeadImgName=$newHeadImg['name'];

            if($newHeadImg['error']===0){
                $tp = array("image/gif","image/pjpeg","image/jpeg","image/png");
                if(in_array($newHeadImg['type'],$tp)){
                    $path='../img/headImg/'.$newHeadImg['name'];
                    $tmpName=$newHeadImg['tmp_name'];
                    if(!file_exists($path)){
                        move_uploaded_file($tmpName,$path);
                    }else{
                        $result['status']=404;
                        $result['result']='封面图片请重新命名！';
                        echo json_encode($result);
                        exit();
                    }
                }
            }

            $data=$model->modifyOne("update forumContent set header='$header',content='$content',time='$time',description='$description',headImg='$newHeadImgName' where id=$id");
            if($data===true){
                $result['status']=200;
                $result['result']='操作成功';
                echo json_encode($result);
            }
        }
    }
}