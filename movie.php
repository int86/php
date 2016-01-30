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
    foreach ($urlArr as $url) {
        $arr = explode('/', $url);
        $subjectID = $arr[4];
        if (!checkUrlExis($subjectID)) {
            $data = getMovieInfo($url, $subjectID);
            $res = insertDB($data);
            sleep(1);
        }
    }
}

//正则匹配所有字段信息
function getMovieInfo($url, $subjectID = '') {
    $html = file_get_contents($url);
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
    $html = file_get_contents($url);
    preg_match_all('/http:\/\/movie\.douban\.com\/subject\/\d+/', $html, $m9);
    $urlArr = array_unique($m9[0]); //去重
    return $urlArr;
}

//执行
$url = 'http://movie.douban.com/subject/3292949/'; //起始地址
main($url);
?>
