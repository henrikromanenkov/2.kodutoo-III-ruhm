<?php
	//echo$_POST["email"];
	//echo$_POST["password"];
	
	// Loon andmebaasi ühenduse
	require_once("../config.php");
	$database = "if15_henrrom";
	$mysqli = new mysqli($servername, $username, $password, $database);
	
	//muutujad errorite jaoks
	
	$email_error = "";
	//$email_error = "";
	$password_error = "";
	//$password_error = "";
	//$lastname_error = "";
	//$firstname_error = "";
	
	//muutujad ab väärtuste jaoks
	
	$email = "";
	//$email = ""
	//$lastname = "";
	//$firstname = "";
	//$password = "";
	$password = "";
	
	//kontrollime, et keegi vajutas input nuppu.
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		
		//echo "Keegi vajutas nuppu";
		
		
		//keegi vajutas login nuppu
		if(isset($_POST["login"])){
			
			echo "Vajutas login nuppu!";
			
			//kontrollin, et e-post ei ole tühi
			if(empty($_POST["email"]) ){
				$email_error = " See väli on kohustuslik.";
			}else{
			// puhastame muutuja võimalikest üleliigsetest sümbolitest		
				$email = cleanInput($_POST["email"]);
			
			}	
				
			//kontrollin, et parool ei ole tühi
			if(empty($_POST["password"]) ){
				$password_error = "See väli on kohustuslik.";
			}else{
				$password = cleanInput($_POST["password"]);
			}
				
			// Kui oleme siia jõudnud, võime kasutaja sisse logida
			if($password_error == "" && $email_error == ""){
				echo "Võib sisse logida! Kasutajanimi on ".$email." ja parool on ".$password;
				
				$hash = hash("sha512", $password);
				
				$stmt = $mysqli->prepare("SELECT id, email FROM user_sample WHERE email=? AND password=?");
				$stmt->bind_param("ss", $email, $hash);
				
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
			if($email_error == ""&& $password_error ==""){
				
				echo "kontrollin sisselogimist".$email." ja parool ";
			}	
		// login if end	
		
		
		// keegi vajutas create  nuppu
		}elseif(isset($_POST["create"])){
			
			echo "Vajutas create nuppu!";
			
			if(empty($_POST["email"]) ){
				$email_error = " See väli on kohustuslik.";
			}else{
				$email = cleanInput($_POST["email"]);
			}
			
			//kontrollin, et parool ei ole tühi
			if(empty($_POST["password"]) ){
				$password_error = "See väli on kohustuslik.";
			}else{
				
				// kui oleme siia jõudnud, siis parool ei ole tühi
				// kontrollin, et oleks vähemalt 8 sümbolit pikk
				if(strlen($_POST["password"])<8) {	
					$password_error = "Peab olema vähemalt 8 tähemärki pikk";
				}else{
					$password = cleanInput($_POST["password"]);
				}
			}

			if(	$email_error == "" && $password_error == ""){
				
				// räsi paroolist, mille salvestame ab'i
				$hash = hash("sha512", $password);
				
				echo "Võib kasutajat luua! Kasutajanimi on ".$email." ja parool on ".$password. "ja räsi on" .$hash;
				
				$stmt = $mysqli->prepare('INSERT INTO user_sample (email, password) VALUES (?, ?)');
				
				// asendame küsimärgid. ss - s ons tring email, s on string password
				
				$stmt->bind_param("ss", $email, $hash);
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
			<input name="email" type="email" placeholder="E-post" value="<?php echo $email; ?>">* <?php echo $email_error; ?> <br><br>
			<input name="password" type="password" placeholder="Parool" value="<?php echo $password; ?>">* <?php echo $password_error; ?> <br><br>
			<input name="login" type="submit" value="Log in"> 
		</form>
		
	<h2>Create user</h2>
	
		<form action="login.php" method="post">
			<input name="email" type="email" placeholder="E-post" value="<?php echo $email; ?>">* <?php echo $email_error; ?> <br><br>
			<input name="password" type="password" placeholder="Parool" value="<?php echo $password; ?>">* <?php echo $password_error; ?> <br><br>
			<!--<input name="lastname" type="text" placeholder="Perekonnanimi" value="<?php echo $lastname; ?>">* <?php echo$lastname_error; ?><br><br>
			<input name="firstname" type="text" placeholder="Eesnimi" value="<?php echo $firstname; ?>">* <?php echo$firstname_error; ?><br><br>-->
			<input name="create" type="submit" value="Create">
		</form>
		
		
		
<p><i>Lehe tegi Henrik, 2015a.</i></p>
</body>     
</html>