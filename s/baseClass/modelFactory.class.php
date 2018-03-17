<?php
class modelFactory{
    static function M($modelName){
        return new $modelName();
    }
}