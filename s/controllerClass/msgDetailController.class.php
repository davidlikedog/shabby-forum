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
}