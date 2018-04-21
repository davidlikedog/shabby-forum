<?php
class loginInController{
    private function getModel(){
        return modelFactory::M("loginInModel");
    }
    function verify(){
        $account=$_POST['account'];
        $password=$_POST['password'];
        $model=$this->getModel();
        $data=$model->verify("select name from forumuser where account=$account and password=$password;");
        $result=array();
        if($data){
            $result['status']=200;
            $result['name']=$data;
            $_SESSION['name']=$result;
        }else{
            $result['status']=404;
        }
        echo json_encode($result);
    }
    function offLine(){
        unset($_SESSION);
        $result=array(
            'msg'=>'注销成功'
        );
        echo json_encode($result);
    }
}