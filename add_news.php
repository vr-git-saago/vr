<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
	//echo $server_host;
	//muutujad sisestatud info meelespidamiseks
	$headline_memory = null;
	$content_memory = null;
	$author_memory = null;
	$news_input_error = null;
	//var_dump($_POST); // on olemas ka $_GET
	if(isset($_POST["news_submit"])){
		if(empty($_POST["news_title_input"])){
			// kui pealkiri ununeb küsime kas postis oli sisu ja author, kui on siis salvestame need muutujass, kui mitte siis väärtus null
			$content_memory = (isset($_POST['news_content_input']) ? $_POST['news_content_input'] : null);
			$author_memory = (isset($_POST['news_author_input']) ? $_POST['news_author_input'] : null);
			$news_input_error = "Uudise pealkiri on puudu! ";
		}
		if(empty($_POST["news_content_input"])){
			// kui pealkiri ununeb küsime kas postis oli sisu ja author, kui on siis salvestame need muutujass, kui mitte siis väärtus null
			$author_memory = (isset($_POST['news_author_input']) ? $_POST['news_author_input'] : null);
			$headline_memory = (isset($_POST['news_title_input']) ? $_POST['news_title_input'] : null);
			$news_input_error .= "Uudise tekst on puudu!";
		
		}
		
		if(empty($news_input_error)){
			// 3 input rida mis validaator funktsioonist läbi käivad
			$news_content_input = test_input($_POST["news_content_input"]);
			$news_title_input = test_input($_POST["news_title_input"]);
			$news_author_input = test_input($_POST["news_author_input"]);

			//Kui valideeritud salvestame andmebaasi
			store_news($_POST["news_title_input"], $_POST["news_content_input"], $_POST["news_author_input"]);
		}
	}
	
	function store_news($news_title, $news_content, $news_author){
		//echo $news_title .$news_content .$news_author;
		//echo $GLOBALS["server_host"];
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        //määrame suhtluse kodeeringu
        $conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("INSERT INTO vr21_news (vr21_news_news_title, vr21_news_news_content, vr21_news_news_author) VALUES (?,?,?)");
		echo $conn -> error;
		//i - integer   s - string   d - decimal
		$stmt -> bind_param("sss", $news_title, $news_content, $news_author);
		$stmt -> execute();
		$stmt -> close();
		$conn -> close();
	}
	// valideerimis funktsioon
	function test_input($user_data) { 
		$user_data = trim($user_data);
		$user_data = stripslashes($user_data);
		$user_data = htmlspecialchars($user_data);
		return $user_data;
	  }

	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Uudiste lisamine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label for="news_title_input">Uudise pealkiri</label>
		<br>
		<!--kuvame echoga value'ks muutuja kui näiteks uudise sisu ununeb-->
		<input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri" value='<?php echo $headline_memory; ?>'>
		<br>
		<label for="news_content_input">Uudise tekst</label>
		<br>
		<!--kuvame echoga muutuja kui näiteks uudise pealkiri ununeb-->
		<textarea id="news_content_input" name="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"><?php echo $content_memory; ?></textarea>
		<br>
		<label for="news_author_input">Uudise lisaja nimi</label>
		<br>
		<!--kuvame echoga value'ks muutuja kui näiteks uudise sisu ununeb-->
		<input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi" value='<?php echo $author_memory; ?>'>
		<br>
		<input type="submit" name="news_submit" value="Salvesta uudis!">
	</form>
	<p><?php echo $news_input_error; ?></p>

	<a href="show_news.php"><h2>Loe Uudiseid 👓📇</h2></a>
	<p><a href="?logout=1">Logi välja</a></p>
	
</body>
</html>