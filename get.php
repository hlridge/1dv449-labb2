<?php

// get the specific message
function getMessages() {
	$db = null;

	try {
        $db = new PDO("sqlite:db.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
        unset($db);
		die("Del -> " .$e->getMessage());
	}
	
	$q = "SELECT * FROM messages";
	
	$result = "";
	$stm = "";
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();
	}
	catch(PDOException $e) {
        echo("Error creating query: " .$e->getMessage());
        unset($stm);
        unset($db);
		return false;
	}
    unset($db);

	if($result) {
        return $result;
    }
	else {
        return false;
    }
}