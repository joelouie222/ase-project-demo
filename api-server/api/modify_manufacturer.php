<?php
	// CHECK if manufacturer id is missing
	if ($mid == NULL)
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing manufacturer id';
		$output[]='Action: query_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	// VALIDATE $mid, MUST BE INT
	if (!is_numeric($mid)) {
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Invalid manufacturer id';
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
		$output[]='MSG: Missing new manufacturer information';
		$output[]='Action: None';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	if ($newName != NULL) {
		// Validate new manufacturer name
		// Manufacturer name must be:
		//   less than 64 characters
		//   alpha-numeric
		//   spaces
		if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $newName)) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Invalid manufacturer name';
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
		$sql="SELECT `auto_id` FROM `manufacturers` WHERE `name` = '$newName'";
		$rst=$dblink->query($sql);
		
		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}
		
		if ($rst->num_rows > 0) {
			$data=$rst->fetch_array(MYSQLI_ASSOC);
			
			if ($data['auto_id'] != $mid) {
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output[]='Status: ERROR';
				$output[]='MSG: Manufacturer with this name already exist in database';
				$output[]='Action: query_manufacturer';
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
		}
		
		// check if manufacturer id is valid 
		$sql="SELECT `auto_id` FROM `manufacturers` WHERE `auto_id` = '$mid'";
		$rst=$dblink->query($sql);
		
		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}
		
		if ($rst->num_rows<=0) //manufacturer not previously found in db
		{	
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Manufacturer with this id does not exist in database';
			$output[]='Action: query_manufacturer';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		} else {
			if ($newName == NULL) {
				$sql = "UPDATE `manufacturers` SET `status` = '$newStatus' WHERE `auto_id` = '$mid'";
		$rst=$dblink->query($sql);
			} else if ($newStatus == NULL) {
				$sql = "UPDATE `manufacturers` SET `name` = '$newName' WHERE `auto_id` = '$mid'";
			} else {
				$sql = "UPDATE `manufacturers` SET `name` = '$newName', `status` = '$newStatus' WHERE `auto_id` = '$mid'";
			}
			
			$result=$dblink->query($sql);
			
			if ($result === false) {
				throw new Exception("Query execution failed. " . $dblink->error);
			}
			
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[] = 'Status: Success';
			$output[] = 'MSG: Manufacturer successfully modified';
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