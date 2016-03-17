<?php
//工厂模式
namespace Factory;

class A{
    public function test(){
        return '这是A类';
    }
}

class B{
    public function test(){
        return '这是B类';
    }
}

class C{
    public function test(){
        return '这是C类';
    }
}

class callClass{
    public static function getobject($param){
        switch ($param) {
            case 'a':
                $obj = new A();
                return $obj;
                break;
            case 'b':
                $obj = new B();
                return $obj;
                break;
            case 'c':
                $obj = new C();
                return $obj;
                break;
        }
    }
}

$obj = callClass::getobject('b');
var_dump($obj->test());