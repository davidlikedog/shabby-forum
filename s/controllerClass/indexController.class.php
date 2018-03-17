<?php

class indexController{
    private function getModel(){
        return modelFactory::M("indexModel");
    }
    function getAllMsg(){
        $start=$_GET['start']?$_GET['start']:0;
        $count=8;
        $model=$this->getModel();
        $data=$model->getAllMsg("select forumContent.*,forumUser.name from forumContent inner join forumUser on forumContent.userID=forumUser.id order by id desc limit $start,$count");
        echo json_encode($data);
    }
}