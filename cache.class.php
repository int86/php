<?php
//底层cache类 

interface cache {

    //获取缓存类唯一实例
    public static function getInstance();

    //设置缓存
    public function set($key, $value, $exp);

    //获取缓存
    public function get($key);

    //替换缓存
    public function replace($key, $value);

    //删除缓存
    public function delete($key);

    //检查缓存服务是否可用
    public function isAvailableToUseCache();
}

//memcache
class memcacheForGoolink implements cache {

    private $ip = "";  //国内
    private $port = "";    //国内
    private $hk_ip = "";  //hk
    private $hk_port = "";   //hk
    private static $_instance;
    private $link;
    public $switch = 1;   //memcache开关 1=开启 0=关闭

    private function __construct() {
        $ip = $this->ip;
        $port = $this->port;
        if (class_exists('Memcache')) {   //如果该台服务器的php支持memcache
            try {
                $mem = new Memcache;
                if($this->isThisServerInChina()){
                     $mem->pconnect($ip, $port);
                }else{
                    $mem->pconnect($hk_ip, $hk_port);
                }
               
            } catch (Exception $exc) {
                LogMy("memecache连接出错");
            }
        } else {
            $mem = 0;
        }
        $this->link = $mem;
    }

    //判断服务器是否在中国
    public function isThisServerInChina() {
        $opts = array(
            'http' => array(
                'method' => "GET",
                'timeout' => 0.2,
            ));
        $context = stream_context_create($opts);
        @$html = file_get_contents('https://www.google.com.hk', false, $context);
        if (!$html) {
            return true;
        } else {
            return false;
        }
    }

    //获取唯一实例对象
    public static function getInstance() {
        //检测类是否被实例化  
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new memcacheForGoolink();
        }
        return self::$_instance;
    }

    //定义私有的__clone()方法，确保单例类不能被复制或克隆  
    private function __clone() {
        
    }

    //是否可以用memcache
    public function isAvailableToUseCache() {
//        if($this->link && $this->link->getStats())
        if ($this->link && $this->link->getStats()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
      add方法，当key存在时，返回false
      @param $key :期望替换值的元素的key。
      @param $value:将要存储的新的值，字符串和数值直接存储，其他类型序列化后存储。
      @param $flag :使用MEMCACHE_COMPRESSED指定对值进行压缩(使用zlib)。
      @param: $exp :当前写入缓存的数据的失效时间。如果此值设置为0表明此数据永不过期。你可以设置一个UNIX时间戳或 以秒为单位的整数（从当前算起的时间差）来说明此数据的过期时间，但是在后一种设置方式中，不能超过 2592000秒（30天）
     */

    public function add($key, $value, $flag = 0, $exp = 7200) {
        return $this->link->add($key, $value, $flag, $exp);
    }

    /*
      set方法，当key存在时，返回true，原来key的值被覆盖
      @param $key :将要分配给变量的key。
      @param $value:将要存储的新的值，字符串和数值直接存储，其他类型序列化后存储。
      @param $flag :使用MEMCACHE_COMPRESSED指定对值进行压缩(使用zlib)。
      @param: $exp :当前写入缓存的数据的失效时间。如果此值设置为0表明此数据永不过期。你可以设置一个UNIX时间戳或 以秒为单位的整数（从当前算起的时间差）来说明此数据的过期时间，但是在后一种设置方式中，不能超过 2592000秒（30天）
     */

    public function set($key, $value, $exp = 7200, $flag = 0) {
        return $this->link->set($key, $value, $flag, $exp);
    }

    /*
      replace方法 当key不存在时，返回false
      @param $key :要设置值的key。
      @param $value:将要存储的新的值，字符串和数值直接存储，其他类型序列化后存储。
      @param $flag :使用MEMCACHE_COMPRESSED指定对值进行压缩(使用zlib)。
      @param: $exp :当前写入缓存的数据的失效时间。如果此值设置为0表明此数据永不过期。你可以设置一个UNIX时间戳或 以秒为单位的整数（从当前算起的时间差）来说明此数据的过期时间，但是在后一种设置方式中，不能超过 2592000秒（30天）
     */

    public function replace($key, $value, $flag = 0, $exp = 7200) {
        return $this->link->replace($key, $value, $flag, $exp);
    }

    /*
      获取单条信息
      @param $key要获取值的key或key数组。
     */

    public function get($key) {
        return $this->link->get($key);
    }

    /*
     * 从服务端删除一个元素
     * @param $key 要删除的元素的key。
     * @param $timeout 删除该元素的执行时间。如果值为0,则该元素立即删除，如果值为30,元素会在30秒内被删除。
     */

    public function delete($key, $timeout = 0) {
        return $this->link->delete($key, $timeout);
    }

    //获取服务器状态
    private function getStats() {
        return $this->link->getStats();
    }

    //清洗（删除）已经存储的所有的元素
    public function flush() {
        return $this->link->flush();
    }

}

//redis连接
class redisForGoolink implements cache {

    public function delete($key) {
        
    }

    public function get($key) {
        
    }

    public function replace($key, $value) {
        
    }

    public function set($key, $value, $exp) {
        
    }

    public static function getInstance() {
        
    }

    public function isAvailableToUseCache() {
        
    }

}

//mongoDB
class mongoDBForGoolink implements cache {

    public function delete($key) {
        
    }

    public function get($key) {
        
    }

    public function isAvailableToUseCache() {
        
    }

    public function replace($key, $value) {
        
    }

    public function set($key, $value, $exp) {
        
    }

    public static function getInstance() {
        
    }

}

//cache工厂类，缓存入口
class cacheFactory {

    public static function getobject($param) {
        switch ($param) {
            case "memcache":
                $obj = memcacheForGoolink::getInstance();
                return $obj;
                break;
            case "redis":
                $obj = redisForGoolink::getInstance();
                return $obj;
                break;
            case "mongoDB":
                $obj = mongoDBForGoolink::getInstance();
                return $obj;
                break;
            //and so on....
            default:
                $obj = memcacheForGoolink::getInstance();
                return $obj;
                break;
        }
    }

}
