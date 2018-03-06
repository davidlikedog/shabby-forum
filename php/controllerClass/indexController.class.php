<?php

class indexController{
    private function getModel(){
        return modelFactory::M("indexModel");
    }
    function getAllMsg(){
        $model=$this->getModel();
        $data=$model->getAllMsg("select forumContent.*,forumUser.name from forumContent inner join forumUser on forumContent.userID=forumUser.id order by id");
        echo json_encode($data);
    }
}