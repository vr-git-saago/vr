<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
	

	function read_news(){
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        //määrame suhtluse kodeeringu
        $conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		//Lisasin veel käsu ORDER BY siis andmebaasi id ja käsk DESC, et oleks kahanevas järjekorras
		// LIMIT käsuga limiteerime kuvatavate uudiste arvu
		// Selleks kasutame muutujat $news_count aga if tsükklis
		// Kui kasutaja ei vali midagi tahame, et oleks mingisugune väärtus.
		
		if (isset($_POST['news_output_submit'])){
			$news_count = $_POST["news_output_num"];
		} else {
			// vaikimisi jääb uudiste kuvaks 2
			$news_count = 3;
		}
		$stmt = $conn -> prepare("SELECT vr21_news.vr21_news_news_title, vr21_news.vr21_news_news_content, vr21_news.vr21_news_news_author, vr21_news.vr21_news_photo_id, vr21_news.vr21_news_added, vr21_news_photos.vr21_news_photos_id, vr21_news_photos.vr21_news_photos_filename, vr21_news_photos.vr21_news_photos_alttext FROM vr21_news LEFT JOIN vr21_news_photos ON vr21_news.vr21_news_photo_id = vr21_news_photos.vr21_news_photos_id  WHERE vr21_news_photos.vr21_news_photos_deleted IS NULL GROUP BY vr21_news_photos.vr21_news_photos_id ORDER BY vr21_news.vr21_news_id DESC LIMIT ? ");
		echo $conn -> error;
		$stmt -> bind_param('i', $news_count);
		$stmt -> bind_result($news_title_from_db, $news_content_from_db, $news_author_from_db, $news_photo_id_from_db, $news_added_from_db, $photo_id_from_db, $photo_filename_from_db, $photo_alttext_from_db);
		$stmt -> execute();
        $rawnews_html = null;
		
        while ($stmt -> fetch() ) {
			// Võtame andmebaasist kuupäeva
			$date_of_news = new DateTime($news_added_from_db);
			// Kuupäeva vormindame päev, kuu, aasta, kellaaega kaasa ei võta :)
			
			$date_format = $date_of_news->format("d.m.Y");
            $rawnews_html .= "\n <h2>" .$news_title_from_db ."</h2>";
            $rawnews_html .= "\n <p>" .nl2br($news_content_from_db) ."</p>";
			$rawnews_html .= '<img src="../news_photos_normal/' .$photo_filename_from_db .'" alt="' .$photo_alttext_from_db .'" class="thumb" data-fn="'.$photo_filename_from_db .'" data-id="'.$photo_id_from_db.'">';
            $rawnews_html .= "\n <p>Edastas: ";
            if(!empty($news_author_from_db)) {
                $rawnews_html .= $news_author_from_db;
            }
            else {
                $rawnews_html .= "Tundmatu autor";
            }
            $rawnews_html .= " ".$date_format ."</p>"; // lisame kuupäeva edastaja nime lõppu
        }
		$stmt -> close();
		$conn -> close();
        return $rawnews_html;
	}

    $news_html = read_news();
	
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
	<h1>Uudiste lugemine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<p>Kasutaja: <?php echo $_SESSION['user_firstname'] .' ' .$_SESSION['user_lastname']; ?></p><p><a href="?logout=1">Logi välja</a></p>
	<hr>
<form method="POST">

<input type="number" min="1" max="10" value="3" name="news_output_num">
<input type="submit" name="news_output_submit" value="Kuva uudiste arv">

</form>
	<?php echo $news_html; ?>
	<div class="nupuke"><a href="add_news.php">Lisa Uudiseid 📢📇</a></div>
	<hr>
        <p>Tagasi <a href="home.php">koju</a></p>
    <hr>
</div>
</body>
</html>