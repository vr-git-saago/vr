<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
	require_once "fnc_general.php";
	require_once "fnc_image.php";
	require_once "classes/Upload_photo.class.php";
	//echo $server_host;
	//muutujad sisestatud info meelespidamiseks
	
	$news_input_error = null;
	//var_dump($_POST); // on olemas ka $_GET
	// Fotoga seotud muutujad
	$photo_upload_error = null;
	$image_file_type = null;
	$image_file_name = null;
	$file_size_limit = 1 * 1024 * 1024;
	$image_max_w = 600;
	$image_max_h = 400;

	$notice = null;

    $news_id_from_page = (int)$_REQUEST["news_id"];
    $news_info_from_db = edit_news($news_id_from_page);
    $headline_memory = $news_info_from_db[0];
	$content_memory = $news_info_from_db[1];
	$author_memory = $news_info_from_db[2];
    $pic_html = $news_info_from_db[3];
    $photo_id_from_db = $news_info_from_db[4];
    $photo_alttext_from_db = $news_info_from_db[5];
    $new_photo = 0;
    $result = null;

    // vana uudise kuvamine muutmise jaoks
    function edit_news($id){
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        //määrame suhtluse kodeeringu
        $conn -> set_charset("utf8");
		$stmt = $conn -> prepare("SELECT vr21_news.vr21_news_id, vr21_news.vr21_news_news_title, vr21_news.vr21_news_news_content, vr21_news.vr21_news_news_author, vr21_news.vr21_news_photo_id, vr21_news.vr21_news_added, vr21_news_photos.vr21_news_photos_id, vr21_news_photos.vr21_news_photos_filename, vr21_news_photos.vr21_news_photos_alttext FROM vr21_news LEFT JOIN vr21_news_photos ON vr21_news.vr21_news_photo_id = vr21_news_photos.vr21_news_photos_id  WHERE vr21_news_photos.vr21_news_photos_deleted IS NULL GROUP BY vr21_news_photos.vr21_news_photos_id ORDER BY vr21_news.vr21_news_id DESC ");
		echo $conn -> error;
		$stmt -> bind_result($news_id_from_db, $news_title_from_db, $news_content_from_db, $news_author_from_db, $news_photo_id_from_db, $news_added_from_db, $photo_id_from_db, $photo_filename_from_db, $photo_alttext_from_db);
		$stmt -> execute();	
        $news_info_from_db = null;	
        while ($stmt -> fetch() ) {
            if ($id === $news_id_from_db) {
                $headline_memory = $news_title_from_db;
                $content_memory = $news_content_from_db;
                $author_memory = $news_author_from_db;
                $picture = "<label>Praegune foto:.$photo_alttext_from_db</label>";
                $picture .= '<img class="edit_photo" src="../news_photos_normal/' .$photo_filename_from_db .'" alt="' .$photo_alttext_from_db .'" class="thumb" data-fn="'.$photo_filename_from_db .'" data-id="'.$photo_id_from_db.'">';
                // teen listi väärtustest mida tagastada
                $news_info_from_db = [$news_title_from_db, $news_content_from_db, $news_author_from_db, $picture, $photo_id_from_db, $photo_alttext_from_db];
            }
        }
		$stmt -> close();
		$conn -> close();
        return $news_info_from_db;
	}
    // uudise uuendamine
	if(isset($_POST["news_submit"])) {
        //  header viib tagasi show_news.php lehele
        header('location: show_news.php');

        $news_id_from_page = $_POST["news_id_input"];
        $photo_id_from_db = $_POST["photo_id_input"];
        if($_FILES["file_input"]["size"] > 0) {
            
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
                if($photo_upload_error == null && $photo_id_from_db > 0){
                    $result = null;
                    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
                    $conn -> set_charset("utf8");
                    $stmt = $conn->prepare("UPDATE vr21_news_photos SET vr21_news_photos_filename = ?, vr21_news_photos_alttext = ? WHERE vr21_news_photos_id = ?");
                    echo $conn->error;
                    $stmt->bind_param("ssi", $image_file_name, $_POST["alt_text"], $photo_id_from_db);
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
                // Kui uudisel polnud pilti ja me soovime seda lisada
                if($photo_upload_error == null && $photo_id_from_db <= 0) {
                    $result = store_news_photo($_SESSION["user_id"], $image_file_name, $_POST["alt_text"]);
                    if($result[0] == 1){
                        $notice .= " Pildi andmed lisati andmebaasi!";
                        $new_photo = 1;
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
                if($new_photo === 0) {
                    store_news($_POST["news_title_input"], $_POST["news_content_input"], $_POST["news_author_input"], $photo_id_from_db, $news_id_from_page);
                } else { // kui lisati postitusele pilt mida enne polnud
                    store_news($_POST["news_title_input"], $_POST["news_content_input"], $_POST["news_author_input"], $result[1], $news_id_from_page);
                }            }
        } else {
            if(empty($news_input_error)){
                // 3 input rida mis validaator funktsioonist läbi käivad
                $news_content_input = test_input($_POST["news_content_input"]);
                $news_title_input = test_input($_POST["news_title_input"]);
                $news_author_input = test_input($_POST["news_author_input"]);
    
                //Kui valideeritud salvestame andmebaasi
                    store_news($_POST["news_title_input"], $_POST["news_content_input"], $_POST["news_author_input"], $photo_id_from_db, $news_id_from_page);
            }
        }

	} 
	function store_news($news_title, $news_content, $news_author, $news_photo_id, $news_id_from_page){
		//echo $news_title .$news_content .$news_author;
		//echo $GLOBALS["server_host"];
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        //määrame suhtluse kodeeringu
        $conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("UPDATE vr21_news SET vr21_news_news_title = ?, vr21_news_news_content = ?, vr21_news_news_author = ?, vr21_news_photo_id = ? WHERE vr21_news_id = ? ");
		echo $conn -> error;
		//i - integer   s - string   d - decimal
		$stmt -> bind_param("sssii", $news_title, $news_content, $news_author, $news_photo_id, $news_id_from_page);
		$stmt -> execute();
		$stmt -> close();
		$conn -> close();
	}
	
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
	<h1>Uudise muutmine</h1>
    <div class="nupu-kast">
	<div class="nupuke"><a href="show_news.php">Loe Uudiseid 👓📇</a></div>
	<div class="nupuke"><a href="home.php">Koju</a></div>
    <div class="nupuke"><a href="?logout=1">Logi välja</a></div>
    </div>
	<p>See leht on valminud õppetöö raames!</p>
	
	<hr>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<label for="news_title_input">Uudise pealkiri</label>
		<br>
        <input hidden type="text" id="photo_id_input" name="photo_id_input" value='<?php echo $photo_id_from_db; ?>'>
        <input hidden type="text" id="news_id_input" name="news_id_input" value='<?php echo $news_id_from_page; ?>'>
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
        <?php echo $pic_html; ?>
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
		<input id="alt_text" name="alt_text" type="text" placeholder="Pildid on...?" value='<?php echo $photo_alttext_from_db; ?>'>
		<br>
		<input class="color" type="submit" name="news_submit" value="Uuenda uudis!">
	</form>
    <p>Kasutaja: <?php echo $_SESSION['user_firstname'] .' ' .$_SESSION['user_lastname']; ?></p><p></p>
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