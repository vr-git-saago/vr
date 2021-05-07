<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
	include "fnc_image.php";

	$photo_upload_error = null;
	$photo_upload_succeeded= null;
	$image_file_type = null;
	$image_file_name = null;
	$file_name_prefix = "vr_";
	$file_size_limit = 1 * 1024 * 1024;
	$image_max_w = 600;
	$image_max_h = 400;
	$privacy = null;
	$alt_text = null;
	$new_temp_image = null;
	$notice = null;
	if(isset($_POST["photo_submit"])){
		//var_dump($_POST);
		// var_dump($_FILES);
		$orig_name = $_FILES["file_input"]['name'];
		//kas üldse on pilt
		$check = getimagesize($_FILES["file_input"]["tmp_name"]);
		if($check !== false){
			//kontrollime, kas aktepteeritud failivorming ja fikseerime laiendi
			if($check["mime"] == "image/jpeg"){
				$image_file_type = "jpg";
			} elseif ($check["mime"] == "image/png"){
				$image_file_type = "png";
			} else {
				$photo_upload_error = "Pole sobiv formaat! Ainult jpg ja png on lubatud!";
			}
		} else {
			$photo_upload_error = "Tegemist pole pildifailiga!";
		}
		
		if(empty($photo_upload_error)){
			//ega pole liiga suur fail
			if($_FILES["file_input"]["size"] > $file_size_limit){
				$photo_upload_error = "Valitud fail on liiga suur! Lubatud kuni 1MiB!";
			}
			
			if(empty($photo_upload_error)){
				//loome oma failinime
				$timestamp = microtime(1) * 10000;
				$image_file_name = $file_name_prefix .$timestamp ."." .$image_file_type;

				 //loome pikslikogumi ehk image objekti
				 $temp_image = null;
				 if($image_file_type == "jpg"){
					 $temp_image = imagecreatefromjpeg($_FILES["file_input"]["tmp_name"]);
				 }
				 if($image_file_type == "png"){
					 $temp_image = imagecreatefrompng($_FILES["file_input"]["tmp_name"]);
				 }

				//suuruse muutmine thumbnail
				$new_temp_image_thumb = image_resize_thumb($temp_image, 100, 100, true);

				$target_file = "../upload_photos_thumb/" .$image_file_name;
				if($image_file_type == "jpg"){
					if(imagejpeg($new_temp_image_thumb, $target_file, 90)){
						$photo_upload_succeeded = "Pisi pilt on salvestatud!";
					} else {
						$photo_upload_error = "Pisi pilti ei salvestatud!";
					}
				}
				if($image_file_type == "png"){
					if(imagepng($new_temp_image_thumb, $target_file, 6)){
						$photo_upload_succeeded = "Pisi pilt on salvestatud!";
					} else {
						$photo_upload_error = "Pisi pilti ei salvestatud!";
					}
				}
				
				//suuruse muutmine normal
				$new_temp_image = image_resize($temp_image, $image_max_w, $image_max_h, false);

				//salvestame pikslikgumi faili
				$target_file = "../upload_photos_normal/" .$image_file_name;
				if($image_file_type == "jpg"){
					if(imagejpeg($new_temp_image, $target_file, 90)){
						$photo_upload_succeeded .= "Vähendatud pilt on salvestatud!";
					} else {
						$photo_upload_error .= "Vähendatud pilti ei salvestatud!";
					}
				}
				if($image_file_type == "png"){
					if(imagepng($new_temp_image, $target_file, 6)){
						$photo_upload_succeeded.= "Vähendatud pilt on salvestatud!";
					} else {
						$photo_upload_error .= "Vähendatud pilti ei salvestatud!";
					}
				}
				
				
				//$target_file = "../upload_photos_orig/" .$_FILES["file_input"]["name"];
				$target_file = "../upload_photos_orig/" .$image_file_name;
				//if(file_exists($target_file))
				if(move_uploaded_file($_FILES["file_input"]["tmp_name"], $target_file)){
					$photo_upload_succeeded .= " Foto üleslaadimine õnnestus!";
				} else {
					$photo_upload_error .= " Foto üleslaadimine ebaõnnestus!";
				}
				if(isset($_POST['privacy_input'])) {
					$privacy = intval($_POST['privacy_input']);
				
				}
				if(isset($_POST['alt_text'])){
					$alt_text = $_POST['alt_text'];
				}
				if(empty($photo_upload_error)){
					$user_id = $_SESSION['user_id'];
					$notice = photo_to_sql($user_id, $image_file_name, $orig_name, $alt_text, $privacy);
						if ($notice == 1) {
							$notice = "Pildi andmed on edukalt andmebaasis!";
						} else {
							$notice = "Pildi andmete andmebaasi lisamisel tekkis tõrge!";
						}
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
	<p><?php echo $photo_upload_error; echo $photo_upload_succeeded; ?></p>

	<p>Tagasi <a href="home.php">koju</a></p>
	
</body>
</html>