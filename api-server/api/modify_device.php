<?php
	// CHECK if device id is missing
	if ($did == NULL)
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing device id';
		$output[]='Action: query_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}


	// VALIDATE $did, MUST BE INT
	if (!is_numeric($did)) {
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Invalid device id';
		$output[]='Action: none';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	// User did not provide new information
	if (($newName == NULL) && ($newStatus == NULL)) {
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing new device information';
		$output[]='Action: None';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	if ($newName != NULL) {
		// Validate new device name
		// Device name must be:
		//   less than 64 characters
		//   alpha-numeric
		//   spaces
		if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $newName)) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Invalid device name';
			$output[]='Action: None';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		} 
		
	}

	// Validate new status
	if 	($newStatus != NULL) {
		if (($newStatus != 'active') && ($newStatus != 'inactive')) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Invalid status';
			$output[]='Action: None';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
	}
	
	$dblink=db_connect("equipments_db");

	try {
		// Check if name is already taken
		$sql="SELECT `auto_id` FROM `devices` WHERE `name` = '$newName'";
		$rst=$dblink->query($sql);
		
		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}
		
		if ($rst->num_rows > 0) {
			$data=$rst->fetch_array(MYSQLI_ASSOC);
			
			if ($data['auto_id'] != $did) {
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output[]='Status: ERROR';
				$output[]='MSG: Device with this name already exist in database';
				$output[]='Action: query_device';
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
		}
		
		// check if device id is valid 
		$sql="SELECT `auto_id` FROM `devices` WHERE `auto_id` = '$did'";
		$rst=$dblink->query($sql);
		
		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}
		
		if ($rst->num_rows<=0) //device not previously found in db
		{	
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Device with this id does not exist in database';
			$output[]='Action: query_device';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		} else {
			if ($newName == NULL) {
				$sql = "UPDATE `devices` SET `status` = '$newStatus' WHERE `auto_id` = '$did'";
		$rst=$dblink->query($sql);
			} else if ($newStatus == NULL) {
				$sql = "UPDATE `devices` SET `name` = '$newName' WHERE `auto_id` = '$did'";
			} else {
				$sql = "UPDATE `devices` SET `name` = '$newName', `status` = '$newStatus' WHERE `auto_id` = '$did'";
			}
			
			$result=$dblink->query($sql);
			
			if ($result === false) {
				throw new Exception("Query execution failed. " . $dblink->error);
			}
			
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[] = 'Status: Success';
			$output[] = 'MSG: Device successfully modified';
			$output[] = 'Action: None';
			$responseData = json_encode($output);
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