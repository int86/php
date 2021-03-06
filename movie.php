<?php

ini_set("max_execution_time", 0);
$con = mysql_connect("localhost", "root", "");
if (!$con) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db("douban", $con);
mysql_query("SET NAMES utf8");

//主程序
function main($url) {
    $urlArr = getAllUrl($url);
    // var_dump($urlArr);exit;

    foreach ($urlArr as $key => $url) {
        $arr = explode('/', $url);
        $subjectID = $arr[4];
        // var_dump($subjectID);exit;
        if (!checkUrlExis($subjectID)) {
            $data = getMovieInfoByApi($subjectID);
            if($data['name']!=''){
                 $res = insertDB($data);
            }
            if(count($urlArr) == ($key+1)){
                foreach($urlArr as $url){
                    $header = get_headers($url);
                    if(!preg_match('/200/',$header[0])){
                       continue;
                    }else{
                        main($url);
                    }
                }
            }
            sleep(1);
        }
    }
    

}

//从页面正则匹配所有字段信息
function getMovieInfoByPreg($url, $subjectID = '') {
    $html = getHtml($url);
    preg_match_all('/(?<=dBy">)[^<=dBy">].*?[^<\/a>](?=<\/a>)/', $html, $m1);  //导演
    $director = '';
    foreach ($m1[0] as $v) {
        $director .= '/' . $v;
    }
    $director = addslashes($director);
    preg_match_all('/(?<=starring">)[^starring">].*?[^<\/a>](?=<\/a>)/', $html, $m2); //主演
    $star = '';
    foreach ($m2[0] as $v) {
        $star .= '/' . $v;
    }
    $star = addslashes($star);
    preg_match_all('/(?<=celebrity\/\d{7}\/\">)[^celebrity\/\d{7}\/\">].*?[^<\/a>](?=<\/a>)/', $html, $m3); //编剧
    $screenwriter = '';
    foreach ($m3[0] as $v) {
        $screenwriter .= '/' . $v;
    }
    $screenwriter = addslashes($screenwriter);
    preg_match('/(?<=content=")[12][0-9]{3}-[0-9]{2}-[0-9]{2}/', $html, $m4); //年份
    preg_match('/(?<=制片国家\/地区:<\/span>).*(?=<br\/>)/', $html, $m5); //国家/地区
    preg_match('/(?<=average">).*(?=<\/strong>)/', $html, $m6); //评分
    preg_match('/(?<=<title>\s).*(?=\s<\/title>)/', $html, $m7); //电影名字
    $name = str_replace('(豆瓣)', '', $m7[0]);
    preg_match('/(?<=genre">).*?(?=<\/span>)/', $html, $m8); //电影类型

    $data = array(
        'subjectID' => $subjectID,
        'name' => $name,
        'score' => $m6[0],
        'type' => $m8[0],
        'director' => $director,
        'star' => $star,
        'screenwriter' => $screenwriter,
        'year' => $m4[0],
        'country' => $m5[0]
    );

    return $data;
}


//从豆瓣接口读取电影信息
function getMovieInfoByApi($subjectID){
    $url = "http://api.douban.com/v2/movie/subject/".$subjectID;
    $html = getHtml($url);
    $html = preg_replace('/&quot;/', '"', $html);
    $data = json_decode($html,true);

    $star = '';
    foreach ($data['casts'] as $v) {
        $star .= $v['name'].'/'; 
    }

    $screenwriter = '';
    if(isset($data['writers'])){
            foreach ($data['writers'] as $value) {
                $screenwriter .= $v['name'].'/'; 
            }

    }

    $data = array(
        'subjectID'=>$subjectID,
        'name' =>$data['title'],
        'score' =>$data['rating']['average'],
        'type'=>implode('/', $data['genres']),
        'director'=>$data['directors'][0]['name'],
        'star'=>$star,
        'screenwriter' =>$screenwriter,
        'year' =>$data['year'],
        'country'=>implode('/', $data['countries'])
        );

    
    return $data;

}

//查看该信息在数据库是否已经存在
function checkUrlExis($subjectID) {
    $sql = "select * from movie where subjectID = '$subjectID'";
    $rt = mysql_fetch_array(mysql_query($sql));
    return $rt;
}

//插入数据库
function insertDB($data) {
    $subjectID = $data['subjectID'];
    $name = $data['name'];
    $score = $data['score'];
    $type = $data['type'];
    $director = $data['director'];
    $star = $data['star'];
    $screenwriter = $data['screenwriter'];
    $year = $data['year'];
    $country = $data['country'];
    $sql = "INSERT INTO movie (subjectID, name,score,type,director,star,screenwriter,year,country)VALUES ('$subjectID', '$name', '$score','$type','$director','$star','$screenwriter','$year','$country')";
    $res = mysql_query($sql);
    echo mysql_error();
    return $res;
}

//匹配此页面下所有有电影链接地址
function getAllUrl($url) {
    $html = getHtml($url);
    preg_match_all('/http:\/\/movie\.douban\.com\/subject\/\d+/', $html, $m9);
    $urlArr = array_unique($m9[0]); //去重
    return $urlArr;
}

//模拟浏览器
function getHtml($url){
    $header = FormatHeader($url); 
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    $html = curl_exec($ch);
    curl_close($ch);
    return htmlspecialchars($html);
     
}

//构造请求头
function FormatHeader($url) 
{ 
// 解析url 
$temp = parse_url($url); 
$query = isset($temp['query']) ? $temp['query'] : ''; 
$path = isset($temp['path']) ? $temp['path'] : '/'; 

$header = array ( 
"Host: {$temp['host']}", 
"Content-Type: text/javascript; charset=utf-8", 
'Accept: */*', 
'Accept-Language:zh-CN,zh;q=0.8',
// 'Accept-Encoding:gzip, deflate, sdch',
"Referer: http://{$temp['host']}/", 
'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2540.0 Safari/537.36', 
// "Content-length: 380", 
"Connection: keep-alive" 
); 
// var_dump($header);exit;
return $header; 
} 

//执行
$url = 'http://movie.douban.com/'; //起始地址
main($url);
?>
