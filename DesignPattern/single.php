<?php
//单例模式

class DB{
    protected static $db = null; 
    
    private function __construct() {  //把构造方法设为私有
            echo 'this is single patten';
    }
    
    //私有克隆函数，防止外办克隆对象
    private function __clone() {
    }
    
    static function getInstance(){
        if(self::$db){
            return self::$db;   //如果已经实例化过，返回对象
        }else{
             self::$db =  new self();  //实例化自己，赋值到$db
             return self::$db;
        }
       
    }
    
    public function getClassName(){
        return __CLASS__;
    }
}

$obj = DB::getInstance();
var_dump($obj->getClassName());
