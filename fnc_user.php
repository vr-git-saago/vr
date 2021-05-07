<?php
    // UUS KASUTAJA
    function sign_up($name, $surname, $gender, $birth_date, $email, $password) {
        $notice = 0;
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        $stmt = $conn->prepare("INSERT INTO vr21_users (vr21_users_firstname, vr21_users_lastname, vr21_users_birthdate, vr21_users_gender, vr21_users_email, vr21_users_password) VALUES (?, ?, ?, ?, ?, ?) ");
        // Kui peaks viga olema siis väljastame vea
        echo $conn->error;
        // krüpteerime parooli
        $options = ["cost"=> 12, "salt" => substr(sha1(rand()), 0, 22)];
        $pwd_hash = password_hash($password, PASSWORD_BCRYPT, $options);
        //Seome küsimärgid parameetridega
        $stmt ->bind_param("sssiss",$name, $surname, $birth_date, $gender, $email, $pwd_hash);
        // ja lõpuks käivitame
        if ($stmt -> execute()){
            $notice = 1;
        }
        $stmt -> close();
		$conn -> close();
        return $notice;
    }
    // KASUTAJA OLEMASOLU KONTROLL
    function username_check($email) {
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        $stmt = $conn->prepare("SELECT vr21_users_id FROM vr21_users WHERE vr21_users_email = ?");
        // ühenduse vea korral
        echo $conn->error;
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->fetch()){
            $email_error = "Selline kasutaja on juba olemas! Vali teine kasutajanimi.";
            return $email_error;
        }
        $stmt -> close();
		$conn -> close();
    }
    // SISSELOGIMINE
    function sign_in ($email, $password) {
        $notice = 0;
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        $stmt = $conn->prepare("SELECT vr21_users_password FROM vr21_users WHERE vr21_users_email = ?");
        echo $conn -> error;
        $stmt -> bind_param("s", $email);
        $stmt -> bind_result($password_from_db);
        $stmt -> execute();
        // kui leiti
        if ($stmt -> fetch()) {
            // kas parool klapib
            if (password_verify($password, $password_from_db)) {
                $stmt -> close();
		        $conn -> close();
                // sulgesime eelmise päringu
                // ja teeme uue ühenduse
                $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
                $stmt = $conn->prepare("SELECT vr21_users_id, vr21_users_firstname, vr21_users_lastname FROM vr21_users WHERE vr21_users_email = ?");
                echo $conn -> error;
                $stmt -> bind_param("s", $email);
                $stmt -> bind_result($id_from_db, $first_name_from_db, $last_name_from_db);
                $stmt -> execute();
                $stmt->fetch();
                $notice = 1;
                $_SESSION["user_id"] = $id_from_db;
                $_SESSION["user_firstname"] = $first_name_from_db;
                $_SESSION["user_lastname"] = $last_name_from_db;
                $stmt -> close();
		        $conn -> close();
                header("Location: home.php");
                exit();
            }
        } else {
            $login_error = 'Sisselogimine ebaõnnestus! Viga kasutajanimes või paroolis.';
            return $login_error;
        }
        
        $stmt -> close();
		$conn -> close();
        return $notice;
    }

?>