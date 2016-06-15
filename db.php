<?php
	//quick easy function that turns a verbose database connection into one line. returns a connected PDO instance
	//usage: $pdoinstancenamehere = connectDB();
	function connectDB(){
		$servername = "127.0.0.1";
		$username = "root";
		$password = "blackteam";
		$conn;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=blackteam", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//echo "Connected successfully"."<br>"; 
		}
		catch(PDOException $e){
	 		//echo "Connection failed: " . $e->getMessage();
		}
		return $conn;
	}
?>

