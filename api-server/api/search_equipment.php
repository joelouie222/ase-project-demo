<?php
	// User did not provide anything to search
	if (($dname == NULL) && ($mname == NULL) && ($sn == NULL)) {
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing search parameters';
		$output[]='Action: None';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	// Finding out which parameters are not NULL and build the SQL query from it
		// 0b3210
		// BIT 0 represents whether or not to include inactive status
		// BIT 1 represents serial number
		// BIT 2 represents manufacturer name
		// BIT 3 represents device name
	$dCase = 0b0000;
	$mCase = 0b0000;
	$sCase = 0b0000;
	$iCase = 0b0000;
	

	// Validate device name
	if ($dname!=NULL) {
		// Device name must be:
		//   less than 64 characters
		//   alpha-numeric
		//   may have spaces
		if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $dname)) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Invalid device name format';
			$output[]='Action: None';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		} 
		$dCase = 0b1000;
	}

	// CHECK if manufacturer is NULL
	if ($mname!=NULL)//missing manufacturer name
	{
		// Validate manufacturer name
		// Manufacturer name must be:
		//   less than 64 characters
		//   alpha-numeric
		//   spaces
		if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $mname)) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Invalid manufacturer name format';
			$output[]='Action: None';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		} 
		$mCase = 0b0100;
	}

	if ($sn!=NULL) {
		// Serial number to be searched
		// S N - are optional
		// alpha-numeric up to 64 character
		if (!preg_match('/^S?N?-?[a-zA-Z0-9]{1,64}$/', $sn)) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Invalid serial number format';
			$output[]='Action: none';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
		$sCase = 0b0010;
	}

	// check if query should include inactive, default is no
	if (($inactive != NULL) && (preg_match('/^[yY]$/', $inactive))) {
		$iCase = 0b0001;
	}

//	// get page number, default page is 0
//	if (($page == NULL) || (!preg_match('/^[0-9]{1,3}$/'))) {
//		$page = 0;
//	}

	// Combine cases using Bitwise OR
	$caseValue = $dCase | $mCase | $sCase | $iCase;
	
	
	// Figuring out what is the appropriate SQL statement based on which variables have data
	switch ($caseValue) {
		case 0b1111:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE devices.name LIKE '%$dname%'
						AND manufacturers.name LIKE '%$mname%'
						AND serial_nums.serial_number LIKE '%$sn%'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b1110:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE devices.name LIKE '%$dname%'
						AND manufacturers.name LIKE '%$mname%'
						AND serial_nums.serial_number LIKE '%$sn%'
						AND serial_nums.status = 'active'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b1101:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE devices.name LIKE '%$dname%'
						AND manufacturers.name LIKE '%$mname%'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b1100:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE devices.name LIKE '%$dname%'
						AND manufacturers.name LIKE '%$mname%'
						AND serial_nums.status = 'active'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b1011:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE devices.name LIKE '%$dname%'
						AND serial_nums.serial_number LIKE '%$sn%'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b1010:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE devices.name LIKE '%$dname%'
						AND serial_nums.serial_number LIKE '%$sn%'
						AND serial_nums.status = 'active'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b1001:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE devices.name LIKE '%$dname%'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b1000:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE devices.name LIKE '%$dname%'
						AND serial_nums.status = 'active'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b0111:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE manufacturers.name LIKE '%$mname%'
						AND serial_nums.serial_number LIKE '%$sn%'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b0110:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE manufacturers.name LIKE '%$mname%'
						AND serial_nums.serial_number LIKE '%$sn%'
						AND serial_nums.status = 'active'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b0101:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE manufacturers.name LIKE '%$mname%'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b0100:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE manufacturers.name LIKE '%$mname%'
						AND serial_nums.status = 'active'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b0011:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE serial_nums.serial_number LIKE '%$sn%'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		case 0b0010:
			$sql = "SELECT
						serial_nums.auto_id AS id,
						devices.name AS device_name,
						manufacturers.name AS manufacturer_name,
						serial_nums.serial_number AS serial_number,
						serial_nums.status AS status
					FROM serial_nums
					INNER JOIN devices ON devices.auto_id = serial_nums.device_id
					INNER JOIN manufacturers ON manufacturers.auto_id = serial_nums.manufacturer_id
					WHERE serial_nums.serial_number LIKE '%$sn%'
						AND serial_nums.status = 'active'
					ORDER BY serial_nums.auto_id
					LIMIT 1000";
			break;
		default: // 0b0000 and 0b0001
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Missing search parameters';
			$output[]='Action: None';
			$responseData=json_encode($output);
			echo $responseData;
			die();
	}


	$dblink=db_connect("equipments_db");
	
	// Execute SQL qeury
	try {
		$rst=$dblink->query($sql);
		
		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}
			
		$equipments=array();
			
		while ($data=$rst->fetch_array(MYSQLI_ASSOC)) {
			$eqObject=array();
			$eqObject['id'] = $data['id'];
			$eqObject['device_name'] = $data['device_name'];
			$eqObject['manufacturer_name'] = $data['manufacturer_name'];
			$eqObject['serial_number'] = $data['serial_number'];
			$eqObject['status'] = $data['status'];
			$equipments[$data['id']] = $eqObject;
		}
		
		if (empty($equipments)) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: Success';
			$output[]='MSG: There are no equipment matching the search parameters';
			$output[]='Action: None';
			$responseData=json_encode($output);
			echo $responseData;
			die(); 
		} else {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: Success';
			$output[]='MSG: '.json_encode($equipments);
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