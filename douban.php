<?php
$con = mysql_connect('localhost','root','root');
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("douban",$con);

function movie($num){
	$url = "http://api.douban.com/v2/movie/subject/".$num;
	@$data = file_get_contents($url);
	if($data){
		$data = json_decode($data,true);
		$subject_id = $data['id'];
		$title = $data['title'];
		$alt = $data['alt'];
		$rating = $data['rating']['average'];
		$directors = $data['directors']['0']['name'];
		$casts = $data['casts']['0']['name'];
		$genres = $data['genres']['0'];
		$year=$data['year'];
		$countries = $data['countries']['0'];
		$sql = "INSERT INTO movie(subject_id,title,alt,rating,directors,casts,genres,year,countries)VALUES('$subject_id','$title','$alt','$rating','$directors','$casts','$genres','$year','$countries')";

		$res = mysql_query($sql);
		if($res){
			// continue;
			print 1;
		}else{
			echo mysql_error();
		}
	}

}

$i = 8000000;
while(1){
	movie($i);
	$i++;
}
	

	



