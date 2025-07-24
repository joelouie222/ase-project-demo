<?php
	if ($did == NULL)//device id is missing
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
//		$output[]='did: '.$did.' - '.$_REQUEST['did'];
//		$output[]='mid: '.$mid.' - '.$_REQUEST['mid'];
//		$output[]='sn: '.$sn.' - '.$_REQUEST['sn'];
		$output[]='Status: ERROR';
		$output[]='MSG: Missing device id';
		$output[]='Action: query_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	if ($mid==NULL)//missing manufacturer id
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
		// check if $did is a valid device id
		$sql = "SELECT `name`, `status` FROM `devices` WHERE `auto_id`='$did'";
		$rst=$dblink->query($sql);

		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}

		if ($rst->num_rows<=0) { //device not previously found in db
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Device does not exist in database';
			$output[]='Action: add_device';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		} else {
			$data=$rst->fetch_array(MYSQLI_ASSOC);

			// device must not be inactive
			if ($data['status'] == 'inactive') {
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output[]='Status: ERROR';
				$output[]='MSG: Device status inactive';
				$output[]='Action: modify_device';
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
		}
		
		
		// check if $mid is a valid manufacturer id
		$sql = "SELECT `name`, `status` FROM `manufacturers` WHERE `auto_id`='$mid'";
		$rst=$dblink->query($sql);

		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}
		
		if ($rst->num_rows<=0) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Manufacturer does not exist in database';
			$output[]='Action: add_manufacturer';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		} else {
			$data=$rst->fetch_array(MYSQLI_ASSOC);

			// manufacturer must not be inactive
			if ($data['status'] == 'inactive') {
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output[]='Status: ERROR';
				$output[]='MSG: Manufacturer status inactive';
				$output[]='Action: modify_manufacturer';
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
		}

		
		// check for duplicate serial number
		$sql="SELECT `auto_id` FROM `serial_nums` WHERE `serial_number` = '$sn'";
		$rst=$dblink->query($sql);
		
		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}
		
		if ($rst->num_rows>0) { // Duplicate Serial number
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Serial number exists in database';
			$output[]='Action: query_equipment';
			$responseData=json_encode($output);
			echo $responseData;
			die();
			
		} else {
			$sql="INSERT into `serial_nums` (`device_id`, `manufacturer_id`, `serial_number`, `status`)
				VALUES ('$did', '$mid', '$sn', 'active')";
			
			$result=$dblink->query($sql);
			
			if ($result === false) {
				throw new Exception("Query execution failed. " . $dblink->error);
			}
			
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[] = 'Status: Success';
			$output[] = 'MSG: Equipment successfully added to database';
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