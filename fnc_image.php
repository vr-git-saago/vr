<?php
				
    //suuruse muutmine normal
    function image_resize($temp_image, $image_max_w, $image_max_h) {
    
        $image_w = imagesx($temp_image);
        $image_h = imagesy($temp_image);
        
        //kuvasuhte säilitamiseks arvutame suuruse muutuse kordaja lähtudes kõrgusest või laiusest
        if($image_w / $image_max_w > $image_h / $image_max_h){
            $image_size_ratio = $image_w / $image_max_w;
        } else {
            $image_size_ratio = $image_h / $image_max_h;
        }

        
        $image_new_w = round($image_w / $image_size_ratio);
        $image_new_h = round($image_h / $image_size_ratio);
        
        //vähendamiseks loome uue image objekti, kuhu kopeerime vähendatud kujutise
        $new_temp_image = imagecreatetruecolor($image_new_w, $image_new_h);
        // 
        imagecopyresampled($new_temp_image, $temp_image, 0, 0, 0, 0, $image_new_w, $image_new_h, $image_w, $image_h);
        return $new_temp_image;
    }
    // suuruse muutmine thumbnail
    function image_resize_thumb($temp_image, $image_max_w, $image_max_h) {
    
        $image_w = imagesx($temp_image);
        $image_h = imagesy($temp_image);
        $square = null;
        //ruudu saamiseks leiame kumb külg on pikkem, ning sellest lahutame lühema külje ja saadud summa jagame kahega
        if($image_w  > $image_h){
            $square = $image_h;
            $offset_h = 0;
            $offset_w = ($image_w - $image_h) / 2;
        } elseif($image_w  < $image_h) {
            $square = $image_w;
            $offset_w = 0;
            $offset_h = ($image_h - $image_w) / 2;
        } else {
            // kui juba on ruudu kujuline siis ei ole vaja arvutada
            $square = $x;
            $offset_w = $offset_h = 0;
        }
        
        // teen eraldi muutuja ruudu külje suurusele, et lihtsam jälgida
        $image_square_size = $image_max_w;

        //vähendamiseks loome uue image objekti, kuhu kopeerime vähendatud kujutise
        $new_temp_image = imagecreatetruecolor($image_max_w, $image_max_h);
        // 
        imagecopyresampled($new_temp_image, $temp_image, 0, 0, $offset_w, $offset_h, $image_square_size, $image_square_size, $square, $square);
        return $new_temp_image;
    }
    // andmebaasi saatmine
    function photo_to_sql($user_id, $image_file_name, $orig_name, $alt_text, $privacy) {
        $notice = 0;
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        $stmt = $conn->prepare("INSERT INTO vr21_photos (vr21_photos_userid, vr21_photos_filename, vr21_photos_origname, vr21_photos_alttext, vr21_photos_privacy) VALUES (?, ?, ?, ?, ?) ");
        // Kui peaks viga olema siis väljastame vea
        echo $conn->error;
        //Seome küsimärgid parameetridega
        $stmt -> bind_param("issss",$user_id, $image_file_name, $orig_name, $alt_text, $privacy);
        // ja lõpuks käivitame
        if ($stmt -> execute()){
            $notice = 1;
        }
        $stmt -> close();
		$conn -> close();
        return $notice;
    }
?>