<?php
	$user_ip = $_SERVER['REMOTE_ADDR'];
	$entry_time = time();
	$exit_time = time()+10;	
	$url2 = $_REQUEST['url'];
	$url = $_SERVER ['HTTP_REFERER'];
	//echo $url2;
	$con = mysqli_connect('127.0.0.1','root','blackteam','blackteam');
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$create = "CREATE TABLE ViewerCount (user_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
user_ip VARCHAR(16) NOT NULL,entry_time VARCHAR(32) NOT NULL,exit_time VARCHAR(32) NOT NULL, url VARCHAR(2083) NOT NULL)";
	mysqli_query($con,$create);
	
	$find = "SELECT user_ip FROM ViewerCount WHERE user_ip='$user_ip'";
	if (mysqli_num_rows(mysqli_query($con,$find)) == 0) {
		$insert = "INSERT INTO ViewerCount (user_id,user_ip,entry_time,exit_time,url) VALUES (0,'$user_ip','$entry_time','$exit_time','$url')";
		mysqli_query($con,$insert);
	}
	
	function total_guests($con, $url) 
	{
		$que="SELECT * FROM ViewerCount WHERE url = '$url'";
		$re=mysqli_query($con,$que);

	 	$total = mysqli_num_rows($re);
		//$online='Online Guests('.$total.')';
		
		return $total;
		}
	
	echo "Viewing this page: " . total_guests($con, $url);
	
	$que="DELETE FROM ViewerCount WHERE exit_time<'$entry_time'";
	mysqli_query($con,$que);
	if (!mysqli_query($con,$que)) {
		echo ("Error description: " . mysqli_error($con));
	}
	
	mysqli_close($con);
	
	//echo $user_ip;
?>