<?php
    $myname = "Jaanus Saago";
    $currenttime = date("d.m.Y H:i:s");
    
    $semesterbegin = new DateTime("2021-1-25");
    $semesterend = new DateTime("2021-6-30");
    $semesterduration = $semesterbegin->diff($semesterend);
    $semesterdurationdays = $semesterduration->format("%r%a");
    $semesterdurationhtml = "\n <p>2021 kevadsemesteri kestus on " .$semesterdurationdays ." päeva.</p> \n";
    $today = new DateTime("now");
    // Nüüd võtan muutujast today  "N" kasutades hetkel oleva nädalapäeva numbrina.
    $currentday = $today->format("N");
    // List Eestikeelsete nädalapäevadega.
    $days = ["Esmaspäev","Teisipäev","Kolmapäev","Neljapäev","Reede","Laupäev","Pühapäev"];
    //Nüüd tekitan uue muutuja millega leian nädalapäevade listist tänasele päevale vastava indexi numbri, -1 sest indexid hakkavad nullist.
    $weekday = $days[$currentday-1];
    //Toon timehtml allapoole, et hierarhia paigas oleks ja lisans tänase nädalapäeva muutuja enne numbreid, ning vahele ka ühe tühiku.
    $timehtml = "\n <p>Lehe avamise hetkel oli: ".$weekday."".$currenttime  .".</php> \n";
    $fromsemesterbegin = $semesterbegin->diff($today);
    $fromsemesterbegindays = $fromsemesterbegin->format("%r%a");

    // If kontrollib kas semestertri kestus on väiksem kui 0, kui on siis pole veel semester alanud.
    if ($fromsemesterbegindays < 0) {
        $semesterprogress = "\n <p>Semester pole veel alanud.</p> \n";
    }
    // Kui eelmine tingimus ostus vääraks siis proovime teada saada kas semester käib või on läbi.
    elseif ($fromsemesterbegindays <= $semesterdurationdays) {
        $semesterprogress = "\n" .' <p>Semester edeneb: <meter min="0" max="'.$semesterdurationdays .'"value="' .$fromsemesterbegindays . '"></meter>.</p>' ."\n";
    }
    else {
        $semesterprogress = "\n <p>Semester on lõppenud.</p> \n";
    }


    // Loeme piltide kataloogi sisu
    $picsdir = "../pics/";
    $allfiles = array_slice(scandir($picsdir), 2);
    //echo $allfiles[5];
    //var_dump($allfiles);
    $allowedphototypes = ["image/jpeg", "image/png"];
    $picfiles = [];


    foreach($allfiles as $file) {
        $fileinfo = getimagesize($picsdir .$file);
        //var_dump($fileinfo);
        if(isset($fileinfo["mime"])) {
            if(in_array($fileinfo["mime"],$allowedphototypes)) {
                array_push($picfiles, $file);
            }
        }
    }

    $photocount = count($picfiles);

    $photonumbers = []; // Teen uue listi numbritele mis tsükklis olev random funktsioon loosib
do {
    $photonum = mt_rand(0, $photocount-1); //Toimub juhusliku pildi valik
    if(!(in_array($photonum,$photonumbers))) { //Kui randomiga tulev number ei ole sama mis $photonumbers listis siis saab selle listis lisada, vastasel korral võtab järgmise numbri ja kontrollib.
        array_push($photonumbers, $photonum); // Sellega lisab listi $photonumbers numbri muutujast $photonum
    }

}while(count($photonumbers) <= 3); // Seni kuni numbrilistist on vähem kui 3 numbrit töötab tsükkel.
$randomphoto = $picfiles[$photonumbers[0]];
$randomphoto2 = $picfiles[$photonumbers[1]];
$randomphoto3 = $picfiles[$photonumbers[2]];
    


?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
    <h1>
    <?php
        echo $myname;
    ?>
    </h1>
    <p>See leht on valminud õppetöö raames!</p>
    <?php
        echo $timehtml;
        echo $semesterdurationhtml;
        echo $semesterprogress;
        echo $currentday;
    ?>
    <img src="<?php echo $picsdir .$randomphoto; ?>" alt="vaade Haapsalus">
    <img src="<?php echo $picsdir .$randomphoto2; ?>" alt="vaade Haapsalus">
    <img src="<?php echo $picsdir .$randomphoto3; ?>" alt="vaade Haapsalus">
</body>
</html>