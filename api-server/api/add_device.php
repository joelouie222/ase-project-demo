<?php
	// CHECK if NAME is NULL
	if ($dname==NULL)//missing device name
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG:Missing device name';
		$output[]='Action: None';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	// Validate device name
	// Device name must be:
	//   less than 64 characters
	//   alpha-numeric
	//   may have spaces
	if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $dname)) {
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Invalid device name';
		$output[]='Action: None';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	} 

	// add slashes
	$dname = addslashes($dname);
	$dblink=db_connect("equipments_db");

	// check if device is already in database else add to database
	try {
		$sql="SELECT `auto_id` FROM `devices` WHERE `name` = '$dname'";
		$rst=$dblink->query($sql);

		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}

		if ($rst->num_rows<=0) //device not previously found in db
		{	
			$sql = "INSERT INTO `devices` (`name`, `status`) VALUES ('$dname', 'active')";
			$result = $dblink->query($sql);

			if ($result === false) {
				throw new Exception("Query execution failed. " . $dblink->error);
			}

			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[] = 'Status: Success';
			$output[] = 'MSG: Device successfully added to database';
			$output[] = 'Action: None';
			$responseData = json_encode($output);
			echo $responseData;
			die();
		} else {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Device already exists in database';
			$output[]='Action: query_device';
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