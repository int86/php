<?php

//策略模式
//1.首先声明一个接口文件，并定义接口有哪些行为
interface UserStrategy {

    function showAD();

    function showCategory();
}

//女性用户
class FemaleUserStrategy implements UserStrategy {

    public function showAD() {
        echo "2016新款女装";
    }

    public function showCategory() {
        echo '女装';
    }

}

//男性用户
class MaleUserStrategy implements UserStrategy {

    public function showAD() {
        echo "2016新款男装";
    }

    public function showCategory() {
        echo '男装';
    }

}

//首页文件
class Page {

    protected $strategy;

    public function index() {
        echo 'AD:';
        $this->strategy->showAD();
        echo '<br>';
        
        echo "Category:";
        $this->strategy->showCategory();
    }
    
    public function setStrategy(\UserStrategy $strategy){
        $this->strategy = $strategy;
    }

}

$page = new Page();
if(isset($_GET['female'])){
    $strategy = new \FemaleUserStrategy();
}else{
    $strategy = new \MaleUserStrategy();
}
$page->setStrategy($strategy);
$page->index();
