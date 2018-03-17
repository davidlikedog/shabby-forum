<?php
session_start();

$addr=isset($_GET['addr'])?$_GET['addr']:'index';
$act=isset($_GET['act'])?$_GET['act']:'getAllMsg';

define("DS", DIRECTORY_SEPARATOR);
define("ROOT", __DIR__.DS);
define("BASECLASS", ROOT.'baseClass'.DS);
define("CTRL_PATH", ROOT.'controllerClass'.DS);
define("MODEL_PATH", ROOT.'modelClass'.DS);

function __autoload($class){
    $allBaseClass=array('MYSQLDB','MYSQL','modelFactory','baseController');
    if (in_array($class, $allBaseClass)){
        include BASECLASS.$class.'.class.php';
    }elseif (substr($class, -5)=='Model'){
        include MODEL_PATH.$class.'.class.php';
    }elseif (substr($class, -10)=='Controller'){
        include CTRL_PATH.$class.'.class.php';
    }
}

$controller=$addr.'Controller';
$users=new $controller();
$data=$users->$act();


