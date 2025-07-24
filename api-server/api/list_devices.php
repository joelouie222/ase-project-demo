<?php
	$dblink=db_connect("equipments_db");
	try {
		// check if query should include inactive, default is no
		if (($inactive != NULL) && (preg_match('/^[yY]$/', $inactive))) {
			$sql = "SELECT `name`, `auto_id` FROM `devices`";
		} else {
			$sql = "SELECT `name`, `auto_id` FROM `devices` WHERE `status`='active'";
		}
		
		$result=$dblink->query($sql);
		if ($result === false) {
				throw new Exception("Query execution failed. " . $dblink->error);
		}
		
		$devices=array();
		while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
			$devices[$data['auto_id']] = $data['name'];
		}

		if (empty($devices)) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: There was a problem retrieving the data. Try again later.';
			$output[]='Action: None';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		} else {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: Success';
			$jsonDevices=json_encode($devices);
			$output[]='MSG: '.$jsonDevices;
			$output[]='Action: None';
			$responseData=json_encode($output);
			echo $responseData;
			die(); 
		}	
	} catch (Exception $e) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[] = 'Status: ERROR';
			$output[] = 'MSG: ' . $e->getMessage();
			$output[] = 'Action: None';
			$responseData = json_encode($output);
			echo $responseData;
			die();
	}
?>