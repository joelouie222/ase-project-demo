<?php
	if ($sn==NULL)//missing serial number
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing serial number';
		$output[]='Action: none';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	// Validate serial number
	// Serial number must:
	//   starts with "SN-"
	//   followed by up to 64 alpha-numeric characters
	if (!preg_match('/^SN-[a-zA-Z0-9]{1,64}$/', $sn)) {
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Invalid serial number';
		$output[]='Action: none';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$dblink=db_connect("equipments_db");

	try {
		$sql = "SELECT
					serial_nums.auto_id AS id,
					devices.name AS device_name,
					manufacturers.name AS manufacturer_name,
					serial_nums.serial_number AS serial_number,
					serial_nums.status AS status
				FROM serial_nums
				INNER JOIN devices ON devices.auto_id = serial_nums.device_id
				INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
				WHERE serial_nums.serial_number = '$sn'";
		$rst=$dblink->query($sql);
		
		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}
		
		if ($rst->num_rows<=0) //equipment not previously found in db
		{
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: There are no equipment with this serial number';
			$output[]='Action: add_manufacturer';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		} else {
			$data=$rst->fetch_array(MYSQLI_ASSOC);

			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: Success';
			$output[]='MSG: '.json_encode($data);
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