<?php
//注册树模式

class Register{
    protected static $store = array();  //注册树对象数组
    
    /*
     * 注册树方法
     * @param $alias 别名
     * @param $obj 注册对象
     */
    static function set($alias,$obj){  
        self::$store[$alias] = $obj;
    }
    
    /*
     *获取对象方法
     * @param $name 对象方法名
     * @return 注册树里方法名对应的对象
     */
    static function get($name){
        return self::$store[$name];
    }
    
    //获取注册树
    static function getStore(){
        return self::$store;
    }
    
    /*
     * 取消对象方法
     * @param $name 对象方法名
     */
    function _unset($alias){
        unset(self::$store[$alias]);
    }
    
}

class A{
    public function __construct(){
        return '这是A类';
    }
}

class B{
    public function __construct(){
        return '这是B类';
    }
}

$obj_a = new A();
$obj_b = new B();
Register::set('a',$obj_a);  //注册A类
Register::set('b',$obj_b); //注册B类


var_dump(Register::getStore());
Register::_unset('b',$obj_b);
var_dump(Register::getStore());

