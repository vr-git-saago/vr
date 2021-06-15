<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
	require_once "fnc_general.php";
	require_once "fnc_image.php";
	require_once "classes/Upload_photo.class.php";
	//echo $server_host;
	//muutujad sisestatud info meelespidamiseks
	$headline_memory = null;
	$content_memory = null;
	$author_memory = null;
	$news_input_error = null;
	//var_dump($_POST); // on olemas ka $_GET
	// Fotoga seotud muutujad
	$photo_upload_error = null;
	$image_file_type = null;
	$image_file_name = null;
	$file_size_limit = 1 * 1024 * 1024;
	$image_max_w = 600;
	$image_max_h = 400;
	$image_thumbnail_size = 100;
	$notice = null;
	$watermark = "../images/vr_watermark.png";

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
		// Foto osa
		//Võtame kasutusele Upload_photo klassi
		$photo_upload = new Upload_photo($_FILES["file_input"],$file_size_limit);
		$photo_upload_error .= $photo_upload->photo_upload_error;

		if(empty($photo_upload->photo_upload_error)){
		
			//suuruse muutmine
			$photo_upload->resize_photo($image_max_w, $image_max_h);

			//salvestame pikslikgumi faili
			// ja muutuja andmebaasi faili nime jaoks.
			$image_file_name = $photo_upload->generate_filename();
			$target_file = "../news_photos_normal/" .$image_file_name;
			$result = $photo_upload->save_image_to_file($target_file, false);
			if($result == 1) {
				$notice = "Vähendatud pilt laeti üles! ";
			} else {
				$photo_upload_error = "Vähendatud pildi salvestamisel tekkis viga!";
			}
			
			// originaal faili puhul kasutan näitena orginaal nime
			$target_file = "../news_photos_orig/" .$_FILES["file_input"]["name"];
			$result = $photo_upload->save_image_to_file($target_file, true);
			if($result == 1){
				$notice .= " Originaalfoto üleslaadimine õnnestus!";
			} else {
				$photo_upload_error .= " Originaalfoto üleslaadimine ebaõnnestus!";
			}

			$photo_upload_error = $photo_upload->photo_upload_error;
			unset($photo_upload);
			//kui kõik hästi, salvestame pildi info andmebaasi!!!
			if($photo_upload_error == null){
				$result = null;
				$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
				$stmt = $conn->prepare("INSERT INTO vr21_news_photos (vr21_news_photos_userid, vr21_news_photos_filename, vr21_news_photos_alttext) VALUES (?, ?, ?)");
				echo $conn->error;
				$stmt->bind_param("iss", $_SESSION["user_id"], $image_file_name, $_POST["alt_text"]);
				if($stmt->execute()){
					$result = 1;
				} else {
					$result = $stmt->error;
				}
				$photo_id = $conn->insert_id;
				$stmt->close();
				$conn->close();
				if($result == 1){
					$notice .= " Pildi andmed lisati andmebaasi!";
				} else {
					$photo_upload_error = "Pildi andmete lisamisel andmebaasi tekkis tehniline tõrge: " .$result;
				}
			}
			
		}
		
		if(empty($news_input_error)){
			// 3 input rida mis validaator funktsioonist läbi käivad
			$news_content_input = test_input($_POST["news_content_input"]);
			$news_title_input = test_input($_POST["news_title_input"]);
			$news_author_input = test_input($_POST["news_author_input"]);

			//Kui valideeritud salvestame andmebaasi
			store_news($_POST["news_title_input"], $_POST["news_content_input"], $_POST["news_author_input"], $photo_id);
		}
	}
	function store_news($news_title, $news_content, $news_author, $news_photo_id){
		//echo $news_title .$news_content .$news_author;
		//echo $GLOBALS["server_host"];
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        //määrame suhtluse kodeeringu
        $conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("INSERT INTO vr21_news (vr21_news_news_title, vr21_news_news_content, vr21_news_news_author, vr21_news_photo_id) VALUES (?,?,?,?)");
		echo $conn -> error;
		//i - integer   s - string   d - decimal
		$stmt -> bind_param("sssi", $news_title, $news_content, $news_author, $news_photo_id);
		$stmt -> execute();
		$stmt -> close();
		$conn -> close();
	}
	// valideerimis funktsioon
	// function test_input($user_data) { 
	// 	$user_data = trim($user_data);
	// 	$user_data = stripslashes($user_data);
	// 	$user_data = htmlspecialchars($user_data);
	// 	return $user_data;
	//   }

	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Limelight&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">  
    <link rel="stylesheet" href="style.css">
</head>
<body class="back">
<div class="container">
	<h1>Uudiste lisamine</h1>
	<div class="nupu-kast">
	<div class="nupuke"><a href="show_news.php">Loe Uudiseid 👓📇</a></div>
	<div class="nupuke"><a href="home.php">Koju 🏠</a></div>
    <div class="nupuke"><a href="?logout=1">Logi välja 🔑</a></div>
    </div>
	<p>See leht on valminud õppetöö raames!</p>
	<p>Kasutaja: <?php echo $_SESSION['user_firstname'] .' ' .$_SESSION['user_lastname']; ?></p>
	<hr>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
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
		<br>
		<div class ="file-input">
			<label class="file_input_lable" for="file_input">Vali foto fail!</label>
			<input class="file" id="file_input" name="file_input" type="file">
			<p class="file-name"></p>
		</div>
		<br>
		<br>
		<br>
		<label for="alt_input">Alternatiivtekst ehk pildi selgitus:</label>
		<br>
		<input id="alt_text" name="alt_text" type="text" placeholder="Pildid on...?">
		<br>
		<input class="color" type="submit" name="news_submit" value="Salvesta uudis!">
	</form>
	<p id="notice"><?php echo $photo_upload_error; echo $notice ?></p>
	<p><?php echo $news_input_error; ?></p>
</div>
<script>
	const file = document.querySelector('#file_input');
	file.addEventListener('change', (e) => {
		// vali fail
		const [file] = e.target.files;
		// faili nimi ja suurus
		const { name: fileName, size } = file;
		// konverteerime faili suuruse baitidest kilibaitideks
		const fileSize = (size / 1000).toFixed(2);
		// ninh lisame selle lehele
		const fileNameAndSize = `Valisid faili nimega: ${fileName} - ${fileSize}KB`;
		document.querySelector('.file-name').textContent = fileNameAndSize;
	});
</script>
</body>
</html>