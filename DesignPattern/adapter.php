<?php
//适配器模式
//适配器模式（有时候也称包装样式或者包装）将一个类的接口适配成用户所期待的。一个适配允许通常因为接口不兼容而不能在一起工作的类工作在一起，做法是将类自己的接口包裹在一个已存在的类中。
//1.首先声明一个接口，并且定义接口都有哪些行为
interface IDatabase{
    function connect($host,$user,$passwd,$dbname);
    function query($sql);
    function close();
}


//mysql
class Mysql implements IDatabase{
    protected $conn;
    
    public function close() {
        mysql_close($this->conn);
    }

    public function connect($host, $user, $passwd, $dbname) {
        $conn = mysql_connect($host, $user, $passwd);
        mysql_select_db($dbname);
        $this->conn = $conn;
    }

    public function query($sql) {
       $res = mysql_query($sql,$this->conn);
       return $res;
    }

}

//mysqli
class Mysqli implements IDatabase{
    protected $conn;
    public function close() {
        mysqli_close($this->conn);
    }

    public function connect($host, $user, $passwd, $dbname) {
        $conn = mysqli_connect($host, $user, $passwd,$dbname);
        $this->conn = $conn;
    }

    public function query($sql) {
        return mysqli_query($this->conn, $sql);
    }

}

//PDO
class PDO implements IDatabase{
   protected $conn;
    
    public function close() {
        unset($this->conn);
    }

    public function connect($host, $user, $passwd, $dbname) {
       $conn = new \PDO("mysql:host=$host;dbname = $dbname", $user, $passwd);
       $this->conn = $conn;
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

}



$db = new mysql();      //mysql
//$db = new Mysqli();   //mysqli
//$db = new PDO();         //PDO
$db->connect('127.0.0.1', 'root', '', 'test');
$db->query('show databases');
$db->close();