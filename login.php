<?php

	require_once("../config.php");
	$database = "if15_henrrom";
	$mysqli = new mysqli($servername, $username, $password, $database);
	

	
	$email_error = "";
	$password_error = "";
	
	$email = "";
	$password = "";
	
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		
		
		
		if(isset($_POST["login"])){
			
			echo "Vajutas login nuppu!";
			
			if(empty($_POST["email"]) ){
				$email_error = " See väli on kohustuslik.";
			}else{		
				$email = cleanInput($_POST["email"]);
			
			}	
				
			if(empty($_POST["password"]) ){
				$password_error = "See väli on kohustuslik.";
			}else{
				$password = cleanInput($_POST["password"]);
			}
				
			if($password_error == "" && $email_error == ""){
				echo "Võib sisse logida! Kasutajanimi on ".$email." ja parool on ".$password;
				
				$hash = hash("sha512", $password);
				
				$stmt = $mysqli->prepare("SELECT id, email FROM user_sample WHERE email=? AND password=?");
				$stmt->bind_param("ss", $email, $hash);
				$stmt->bind_result($id_from_db, $email_from_db);
				$stmt->execute();
				
				if($stmt->fetch()){
					echo "Email ja parool õiged, kasutaja id=".$id_from_db;
					
				}else{
					echo "wrong credentials";
				}
				
				$stmt->close();
				
			}
			
			if($email_error == ""&& $password_error ==""){
				
				echo "kontrollin sisselogimist".$email." ja parool ";
			}	
		
		
		}elseif(isset($_POST["create"])){
			
			echo "Vajutas create nuppu!";
			
			if(empty($_POST["email"]) ){
				$email_error = " See väli on kohustuslik.";
			}else{
				$email = cleanInput($_POST["email"]);
			}
			
			if(empty($_POST["password"]) ){
				$password_error = "See väli on kohustuslik.";
			}else{
				
				if(strlen($_POST["password"])<8) {	
					$password_error = "Peab olema vähemalt 8 tähemärki pikk";
				}else{
					$password = cleanInput($_POST["password"]);
				}
			}

			if(	$email_error == "" && $password_error == ""){

				$hash = hash("sha512", $password);
				
				echo "Võib kasutajat luua! Kasutajanimi on ".$email." ja parool on ".$password. "ja räsi on" .$hash;
				
				$stmt = $mysqli->prepare('INSERT INTO user_sample (email, password) VALUES (?, ?)');
				$stmt->bind_param("ss", $email, $hash);
				$stmt->execute();
				$stmt->close();
			}
		
		}	
	}	
	function cleanInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
		
	}
	
	$mysqli->close();
?>  
<?php
	$page_title = "Sisselogimise leht";
	$page_file_name = "login.php";
?>  
<!DOCTYPE html>                                               
<html>
<head>
	<title><?php echo $page_title; ?></title>
	
</head>
<body>
	
	<h2>Log in</h2>
		<form action="login.php" method="post">
			<input name="email" type="email" placeholder="E-post" value="<?php echo $email; ?>">* <?php echo $email_error; ?> <br><br>
			<input name="password" type="password" placeholder="Parool" value="<?php echo $password; ?>">* <?php echo $password_error; ?> <br><br>
			<input name="login" type="submit" value="Log in"> 
		</form>
		
	<h2>Create user</h2>
	
		<form action="login.php" method="post">
			<input name="email" type="email" placeholder="E-post" value="<?php echo $email; ?>">* <?php echo $email_error; ?> <br><br>
			<input name="password" type="password" placeholder="Parool" value="<?php echo $password; ?>">* <?php echo $password_error; ?> <br><br>
			<input name="create" type="submit" value="Create">
		</form>
		
		
		
<p><i>Lehe tegi Henrik, 2015a.</i></p>
</body>     
</html>