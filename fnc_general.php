<?php
// valideerimis funktsioon
	function test_input($user_data) { 
		$user_data = trim($user_data);
		$user_data = stripslashes($user_data);
		$user_data = htmlspecialchars($user_data);
		return $user_data;
	  }
?>