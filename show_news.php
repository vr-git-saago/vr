<?php
	
	require_once "../../../conf.php";
	

	function read_news(){
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        //määrame suhtluse kodeeringu
        $conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("SELECT vr21_news_news_title, vr21_news_news_content, vr21_news_news_author FROM vr21_news");
		echo $conn -> error;
		$stmt -> bind_result($news_title_from_db, $news_content_from_db, $news_author_from_db);
		$stmt -> execute();
        $rawnews_html = null;
        while ($stmt -> fetch() ) {
            $rawnews_html .= "\n <h2>" .$news_title_from_db ."</h2>";
            $rawnews_html .= "\n <p>" .nl2br($news_content_from_db) ."</p>";
            $rawnews_html .= "\n <p>Edastas: ";
            if(!empty($news_author_from_db)) {
                $rawnews_html .= $news_author_from_db;
            }
            else {
                $rawnews_html .= "Tundmatu autor";
            }
            $rawnews_html .= "</p>";
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
</head>
<body>
	<h1>Uudiste lugemine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>

	<?php echo $news_html; ?>
</body>
</html>