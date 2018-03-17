<?php
class registerController{
    private function getModel(){
        return modelFactory::M("registerModel");
    }
    function verifyName(){
        $name=$_POST['name'];
        $model=$this->getModel();
        $id=$model->verifyName("select id from forumuser where name='$name'");
        $result=array();
        if(isset($id)){
            $result['result']='该账号已存在';
            $result['color']='red';
        }else{
            $result['result']='该账号可用';
            $result['color']='green';
        }
        echo json_encode($result);
    }
    function addOne(){
        $name=$_POST['name'];
        $account=$_POST['account'];
        $password=$_POST['password'];
        $model=$this->getModel();
        $data=$model->addOne("insert into forumuser values(null,'$name','$account','$password')");
        $result=array();
        if($data===true){
            $result['status']=200;
            $result['msg']='添加成功';
        }else{
            $result['status']=500;
            $result['msg']='添加失败';
        }
        echo json_encode($result);
    }
}