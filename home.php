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
</head>
<body>
	<h1>Sisseloginud kasutaja, vinge värk</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<p>Kasutaja: <?php echo $_SESSION['user_firstname'] .' ' .$_SESSION['user_lastname']; ?></p><p><a href="?logout=1">Logi välja</a></p>
	<hr>
	<a href="add_news.php"><h2>Lisa Uudis 📢📇</h2></a>
	<br>
	<a href="show_news.php"><h2>Loe Uudiseid 👓📇</h2></a>
	<a href="upload_photo.php">Fotode üleslaadimine</a>
	
</body>
</html>