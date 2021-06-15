<?php
require_once "usesession.php";

	// see peab meeles andmeid kui sisse logime
// session_start();
// // kas on sisse loginud?
// if (!isset($_SESSION["user_id"])) {
// 	header("Location: page.php");
// }
// //  välja logimine
// if (isset($_GET["logout"])) {
// 	session_destroy();
// 	header("Location; page.php");
// }

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
	<h1>Kodu</h1>
	<div class="nupu-kast">
	<div class="nupuke"><a href="show_news.php">Loe Uudiseid 👓📇</a></div>
	<div class="nupuke"><a href="add_news.php">Lisa Uudiseid 📢📇</a></div>
	<div class="nupuke"><a href="gallery.php">Galerii 📸</a></div>
	<div class="nupuke"><a href="upload_photo.php">Fotode üleslaadimine 🤳</a></div>
    <div class="nupuke"><a href="?logout=1">Logi välja 🔑</a></div>
    </div>
	<p>See leht on valminud õppetöö raames!</p>
	<p>Kasutaja: <?php echo $_SESSION['user_firstname'] .' ' .$_SESSION['user_lastname']; ?></p>
	</div>
</body>
</html>