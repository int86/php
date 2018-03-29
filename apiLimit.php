<?php
session_start(); 
/*
一个用session限制接口在特定时间内请求次数的小脚本
*/

$timestamp = time();
$secret = 'visto2018';	//接口请求方令牌
$timeLimit = 60;  //限定时间
$limit = 10;  	//限定时间内可以请求的次数

if($secret){
	if(@$_SESSION[$secret]){
	$oldTimestamp = $_SESSION[$secret]['timestamp'];
	$oldcount = $_SESSION[$secret]['count'];

	if($timestamp - $oldTimestamp > $timeLimit){
			$arr = array('timestamp'=>$timestamp,'count'=>0);
			$_SESSION[$secret] = $arr;
	}else{
		if($oldcount > $limit){
			echo '你请求太频繁了';
		}else{


			$oldcount = $oldcount + 1;
			$_SESSION[$secret]['count'] = $oldcount;

			var_dump($_SESSION);
		}
	}


	}else{
		$arr = array('timestamp'=>$timestamp,'count'=>0);
		$_SESSION[$secret] = $arr;
	}
}


