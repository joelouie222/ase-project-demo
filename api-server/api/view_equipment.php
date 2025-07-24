<?php
	// CHECK if equipment id is missing
	if ($eid == NULL)
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing equipment id';
		$output[]='Action: query_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	// $eid MUST BE INT
	if (!is_numeric($eid)) {
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Invalid equipment id';
		$output[]='Action: none';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	
	$dblink=db_connect("equipments_db");
	try {
		// search for the equipment
		$sql = "SELECT
					devices.name AS device_name,
					manufacturers.name AS manufacturer_name,
					serial_nums.serial_number AS serial_number,
					serial_nums.status AS status
				FROM serial_nums
				INNER JOIN devices ON devices.auto_id = serial_nums.device_id
				INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
				WHERE serial_nums.auto_id = '$eid'";
		$rst=$dblink->query($sql);

		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}

		if ($rst->num_rows<=0) { //equipment id not previously found in db
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Equipment with this id does not exist in database';
			$output[]='Action: query_equipment';
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