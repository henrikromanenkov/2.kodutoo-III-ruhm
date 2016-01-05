<?php

 	//echo$_POST["email"];
 	//echo$_POST["password"];
 	
 	// Loon andmebaasi ühenduse
 	require_once("../config.php");
 	$database = "if15_henrrom";
 	$mysqli = new mysqli($servername, $username, $password, $database);
 	
 	//muutujad errorite jaoks
 	
 	$log_email_error = "";
 	$user_email_error = "";
 	$log_password_error = "";
 	$user_password_error = "";
 	$lastname_error = "";
 	$firstname_error = "";
 	
 	//muutujad ab väärtuste jaoks
 	
 	$log_email = "";
 	$user_email = "";
 	$lastname = "";
 	$firstname = "";
 	$log_password = "";
 	$user_password = "";
 	
 	//kontrollime, et keegi vajutas input nuppu.
 	if($_SERVER["REQUEST_METHOD"] == "POST"){
 		
 		//echo "Keegi vajutas nuppu";
 		
 		
 		//keegi vajutas login nuppu
 		if(isset($_POST["login"])){
 			
 			echo "Vajutas login nuppu!";
 			
 			//kontrollin, et e-post ei ole tühi
 			if(empty($_POST["log_email"]) ){
 				$log_email_error = " See väli on kohustuslik.";
 			}else{
 		// puhastame muutuja võimalikest üleliigsetest sümbolitest		
 				$log_email = cleanInput($_POST["log_email"]);
 			
 			}	
 				
 			//kontrollin, et parool ei ole tühi
 			if(empty($_POST["log_password"]) ){
 				$log_password_error = "See väli on kohustuslik.";
 			}else{
 				$log_password = cleanInput($_POST["log_password"]);
 			}
 				
 			// Kui oleme siia jõudnud, võime kasutaja sisse logida
 			if($log_password_error == "" && $log_email_error == ""){
 				echo "Võib sisse logida! Kasutajanimi on ".$log_email." ja parool on ".$log_password;
 				
 				$hash = hash("sha512", $log_password);
 				
 				$stmt = $mysqli->prepare("SELECT id, email FROM user_info WHERE email=? AND password=?");
 				$stmt->bind_param("is", $id, $email);
 				
 				//muutujad tulemustele
 				$stmt->bind_result($id_from_db, $email_from_db);
 				$stmt->execute();
 				
 				//kontrolli, kas tulemus leiti
 				if($stmt->fetch()){
 					//ab'i oli midagi
 					echo "Email ja parool õiged, kasutaja id=".$id_from_db;
 					
 				}else{
 					//ei leidnud
 					echo "wrong credentials";
 				}
 				
 				$stmt->close();
 				
 			}
 			
 			
 			
 			//kontrollin et ei oleks ühtegi errorit
 			if($log_email_error == ""&& $log_password_error ==""){
 				
 				echo "kontrollin sisselogimist".$log_email." ja parool ";
 			}
 			
 			
 		
 		// keegi vajutas create  nuppu
 		}elseif(isset($_POST["create"])){
 			
 			echo "Vajutas create nuppu!";
 			
 			if(empty($_POST["user_email"]) ){
 				$user_email_error = " See väli on kohustuslik.";
 			}else{
 				$user_email = cleanInput($_POST["user_email"]);
 			}
 			
 			//kontrollin, et parool ei ole tühi
 			if(empty($_POST["user_password"]) ){
 				$user_password_error = "See väli on kohustuslik.";
 			}else{
 				
 				// kui oleme siia jõudnud, siis parool ei ole tühi
 				// kontrollin, et oleks vähemalt 8 sümbolit pikk
 				if(strlen($_POST["user_password"])<8) {	
 					$user_password_error = "Peab olema vähemalt 8 tähemärki pikk";
 				}else{
 					$user_password = cleanInput($_POST["user_password"]);
 				}
 			}
 
 			if(	$user_email_error == "" && $user_password_error == ""){
 				
 				// räsi paroolist, mille salvestame ab'i
 				$hash = hash("sha512", $user_password);
 				
 			}
 		
 			//kontrollin, et perekonnanimi ei ole tühi
 			if( empty($_POST["lastname"]) ) {
 				$lastname_error = "See väli on kohustuslik";
 			}else{
 				//kõik korras
 				//test_input eemaldab pahatahtlikud osad
 				$lastname = ($_POST["lastname"]);
 			
 				
 			}
 			if($lastname_error == ""){
 				echo "salvestan ab'i".$lastname;
 			}
 			
 			//valideerimine create user vormile
 			//kontrollin, et eesnimi ei ole tühi
 			if( empty($_POST["firstname"]) ) {
 				$firstname_error = "See väli on kohustuslik";
 			}else{
 				//kõik korras
 				//test_input eemaldab pahatahtlikud osad
 				$firstname = ($_POST["firstname"]);
 			
 				
 			}
 			if($firstname_error == ""){
 				echo "salvestan ab'i".$firstname;
				
				echo "Võib kasutajat luua! Kasutajanimi on ".$user_email." ja parool on ".$user_password;
 				
 				$stmt = $mysqli->prepare('INSERT INTO user_info (email, password, lastname, firstname) VALUES (?, ?, ?, ?)');
 				
 				// asendame küsimärgid. ss - s ons tring email, s on string password
 				
 				$stmt->bind_param("ssss", $user_email, $hash, $lastname, $firstname);
 				$stmt->execute();
 				$stmt->close();
 			}
 		
 		}	
 	}	//võtab ära tühikud, enterid ja tabid
 	function cleanInput($data) {
 		$data = trim($data);
 		$data = stripslashes($data);
 		$data = htmlspecialchars($data);
 		return $data;
 		
 	}
 	// paneme ühenduse kinni
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
 			<input name="log_email" type="email" placeholder="E-post" value="<?php echo $log_email; ?>">* <?php echo $log_email_error; ?> <br><br>
 			<input name="log_password" type="password" placeholder="Parool" value="<?php echo $log_password; ?>">* <?php echo $log_password_error; ?> <br><br>
 			<input name="login" type="submit" value="Log in"> 
 		</form>
 		
 	<h2>Create user</h2>
 	
 		<form action="login.php" method="post">
 			<input name="user_email" type="email" placeholder="E-post" value="<?php echo $user_email; ?>">* <?php echo $user_email_error; ?> <br><br>
 			<input name="user_password" type="password" placeholder="Parool" value="<?php echo $user_password; ?>">* <?php echo $user_password_error; ?> <br><br>
 			<input name="lastname" type="text" placeholder="Perekonnanimi" value="<?php echo $lastname; ?>">* <?php echo$lastname_error; ?><br><br>
 			<input name="firstname" type="text" placeholder="Eesnimi" value="<?php echo $firstname; ?>">* <?php echo$firstname_error; ?><br><br>
 			<input name="create" type="submit" value="Create">
 		</form>
 		
 		
 		
 <p><i>Lehe tegi Henrik, 2015a.</i></p>
 </body>     
 </html>