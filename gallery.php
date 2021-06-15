<?php

require_once "../../../conf.php";
require_once "fnc_image.php";
require_once "usesession.php";

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
    <link rel="stylesheet" href="modal.css">
    <script src="javascript/modal.js" defer></script>
</head>
<body>
    <!--Modaalaken fotogalerii jaoks-->
    <div id="modalarea" class="modalarea">
        <!--sulgemisnupp-->
        <span id="modalclose" class="modalclose">&times;</span>
        <!--pildikoht-->
        <div class="modalhorizontal">
            <div class="modalvertical">
                <p id="modalcaption"></p>
                <img id="modalimg" src="../images/empty.png" alt="galeriipilt"> 			
                <br>
                <div id="rating" class="modalRating">
                    <label><input id="rate1" name="rating" type="radio" value="1">1</label>
                    <label><input id="rate2" name="rating" type="radio" value="2">2</label>
                    <label><input id="rate3" name="rating" type="radio" value="3">3</label>
                    <label><input id="rate4" name="rating" type="radio" value="4">4</label>
                    <label><input id="rate5" name="rating" type="radio" value="5">5</label>
                    <button id="storeRating">Salvesta hinnang!</button>
                    <br>
                    <p id="avgRating"></p>
                </div>
			
                
            </div>
        </div>
    </div>
  
    <div class="container">
	<h1>Galerii</h1>
    <div class="nupu-kast">
	<div class="nupuke"><a href="home.php">Koju 🏠</a></div>
    <div class="nupuke"><a href="?logout=1">Logi välja 🔑</a></div>
    </div>
	<p>See leht on valminud õppetöö raames!</p>
    <p>Kasutaja: <?php echo $_SESSION['user_firstname'] .' ' .$_SESSION['user_lastname']; ?></p>
	<hr>
    <div id="gallery" class="gallery">
    <?php echo $pictures_to_html; ?>
	</div>
    </div>
    
	
</body>
</html>