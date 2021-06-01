<?php

require_once "../../../conf.php";
require_once "fnc_image.php";

// see funktsioon on funktsiooni failis
$pictures_to_html = show_pic();

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
<body>
    <div class="container">
	<h1>Galerii</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
    <div class="gallery">
    <?php echo $pictures_to_html; ?>
	</div>
    <div class="nupuke">
        <a href="page.php">Avalehele</a>
    </div>
    </div>
	
</body>
</html>