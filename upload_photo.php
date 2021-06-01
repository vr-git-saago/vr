<?php
require_once "usesession.php";
require_once "../../../conf.php";
require_once "fnc_general.php";
require_once "fnc_image.php";
require_once "classes/Upload_photo.class.php";

$photo_upload_error = null;
$image_file_type = null;
$image_file_name = null;
$file_size_limit = 1 * 1024 * 1024;
$image_max_w = 600;
$image_max_h = 400;
$image_thumbnail_size = 100;
$notice = null;
$watermark = "../images/vr_watermark.png";

if(isset($_POST["photo_submit"])){
	
	//Võtame kasutusele Upload_photo klassi
	$photo_upload = new Upload_photo($_FILES["file_input"],$file_size_limit);
	$photo_upload_error .= $photo_upload->photo_upload_error;

	if(empty($photo_upload->photo_upload_error)){
	
		//suuruse muutmine
		$photo_upload->resize_photo($image_max_w, $image_max_h);

		// lisan vesimärgi
		$photo_upload->add_watermark($watermark);

		// lisame vesimärgina ka pildistamise kuupäeva, kui on
		// saadan ka sobiva fondi 
		$font = "fonts/arialbd.ttf";
		$photo_upload->photographed_date($font);
		
		//salvestame pikslikgumi faili
		// ja muutuja andmebaasi faili nime jaoks.
		$image_file_name = $photo_upload->generate_filename();
		$target_file = "../upload_photos_normal/" .$image_file_name;
		$result = $photo_upload->save_image_to_file($target_file, false);
		if($result == 1) {
			$notice = "Vähendatud pilt laeti üles! ";
		} else {
			$photo_upload_error = "Vähendatud pildi salvestamisel tekkis viga!";
		}
		
		//teen pisipildi
		$photo_upload->resize_photo($image_thumbnail_size, $image_thumbnail_size, false);
		
		//salvestame pisipildi faili
		$target_file = "../upload_photos_thumb/" .$image_file_name;
		$result = $photo_upload->save_image_to_file($target_file, false);
		if($result == 1) {
			$notice .= " Pisipilt laeti üles! ";
		} else {
			$photo_upload_error .= " Pisipildi salvestamisel tekkis viga!";
		}
		// originaal faili puhul kasutan näitena orginaal nime
		$target_file = "../upload_photos_orig/" .$_FILES["file_input"]["name"];
		$result = $photo_upload->save_image_to_file($target_file, true);
		if($result == 1){
			$notice .= " Originaalfoto üleslaadimine õnnestus!";
		} else {
			$photo_upload_error .= " Originaalfoto üleslaadimine ebaõnnestus!";
		}

		$photo_upload_error = $photo_upload->photo_upload_error;
		unset($photo_upload);
		//kui kõik hästi, salvestame info andmebaasi!!!
		if($photo_upload_error == null){
			$result = store_photo_data($image_file_name, $_POST["alt_text"], $_POST["privacy_input"], $_FILES["file_input"]["name"]);
			if($result == 1){
				$notice .= " Pildi andmed lisati andmebaasi!";
			} else {
				$photo_upload_error = "Pildi andmete lisamisel andmebaasi tekkis tehniline tõrge: " .$result;
			}
		}
		
	}
}

?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Fotode üleslaadimine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<p>Kasutaja: <?php echo $_SESSION['user_firstname'] .' ' .$_SESSION['user_lastname']; ?></p><p><a href="?logout=1">Logi välja</a></p>
	<hr>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<label for="file_input">Vali foto fail!</label>
		<br>
		<input id="file_input" name="file_input" type="file" >
		<br>
		<br>
		<label for="alt_input">Alternatiivtekst ehk pildi selgitus:</label>
		<br>
		<input id="alt_text" name="alt_text" type="text" placeholder="Pildid on...?">
		<br>
		<br>
		<label>Privaatsustase:</label>
		<br>
		<label for="pirvacy_input_1">Privaatne</label>
		<input id="privacy_input_1" name="privacy_input" type="radio" value="3" checked>
		<br>
		<label for="pirvacy_input_2">Registreeritud kasutajatele</label>
		<input id="privacy_input_2" name="privacy_input" type="radio" value="2">
		<br>
		<label for="pirvacy_input_3">Avalik</label>
		<input id="privacy_input_3" name="privacy_input" type="radio" value="1">
		<br>
		<br>
		<input type="submit" name="photo_submit" value="Lae pilt üles!"><span><?php echo $notice; ?></span>
	</form>
	<p><?php echo $photo_upload_error; echo $notice ?></p>

	<p>Tagasi <a href="home.php">koju</a></p>
	
</body>
</html>