<?php
class addMsgController{
    private function getModel(){
        return modelFactory::M("addMsgModel");
    }
    function uploadImg(){
        if(isset($_FILES)){
            $allImg=$_FILES;
            $allName=array();
            $result=array();

            $tp = array("image/gif","image/pjpeg","image/jpeg","image/png");
            foreach ($allImg as $key=>$value){
                if(!in_array($value['type'], $tp)){//文件类型不符合要求
                    $result['errno']=1;
                    $result['data']=$allName;
                    $result['msg']='文件类型不符合要求';
                    break;
                }elseif ($value['error']==0){//图片本身错误
                    $name=$value['name'];
                    $path="../img/$name";
                    $tmpName=$value['tmp_name'];
                    if(file_exists($path)){//图片已存在
                        $result['errno']=1;
                        $result['data']=$allName;
                        $result['msg']='文件已存在';
                        break;
                    }else{//成功上传文件
                        move_uploaded_file($tmpName, $path);
                        $allName[]=substr($path,1);
                        $result['errno']=0;
                        $result['data']=$allName;
                    }
                }
            }
            if($result['errno']==1){//如果文件有一个上传失败，就把已经上传的图片全部删除掉
                foreach($allImg as $key=>$value){
                    $path='.'.$value['name'];
                    if(file_exists($path)){
                        unlink($path);
                    }
                }
            }
            echo json_encode($result);
        }
    }
    function addMsg(){
        $name=$_POST['name'];
        if($_SESSION['name']['name']!=$name){
            $result['status']=404;
            $result['err']='该用户不是目前登录的用户';
            echo json_encode($result);
            exit();
        }

        $header=$_POST['header'];
        $description=$_POST['description'];
        $content=$_POST['content'];
        $time=date("Y-m-d h:i:s");
        $headImg=$_FILES['headImg'];
        $headImgName=$headImg['name'];
        $delImg=$_POST['delImg'];//删除多余的图片
//        echo $delImg;

        $result=array();

        if($headImg['error']===0){
            $tp = array("image/gif","image/pjpeg","image/jpeg","image/png");
            if(in_array($headImg['type'],$tp)){
                $path='../img/headImg/'.$headImg['name'];
                $tmpName=$headImg['tmp_name'];
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

        $delImgArr=explode(",",$delImg);
        if(!empty($delImg)){
            foreach($delImgArr as $key=>$value){
                unlink($value);
            }
        }

        $model=$this->getModel();
        $id=$model->getId("select id from forumuser where name='$name'");
        $data=$model->addMsg("insert into forumcontent values(null,'$id','$content','$header','$time','$description','$headImgName')");
        if($data===true){
            $result['status']=200;
            $result['result']='操作成功';
            echo json_encode($result);
        }else{
            $result['status']=500;
            $result['result']='数据库错误';
            echo json_encode($result);
        }
    }
}