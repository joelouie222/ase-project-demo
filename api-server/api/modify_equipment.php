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
	$dblink=db_connect("equipments_db");
	try {
		// VALIDATE $eid
		if ($eid != NULL) {
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

			// eid must be in database
			$sql="SELECT `auto_id` FROM `serial_nums` WHERE `auto_id` = '$eid'";
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
			} 
		}

		// User did not provide new information
		if (($did == NULL) && ($mid == NULL) && ($sn == NULL) && ($newStatus == NULL)) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Missing new equipment information';
			$output[]='Action: None';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}

		// VALIDATE $did
		if ($did != NULL) {
			// $did MUST BE INT
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

			// $did must a valid device id in the database
			$sql="SELECT `auto_id` FROM `devices` WHERE `auto_id` = '$did'";
//			$sql="SELECT `auto_id`, `status` FROM `devices` WHERE `auto_id` = '$did'";
			$rst=$dblink->query($sql);

			if ($rst === false) {
				throw new Exception("Query execution failed. " . $dblink->error);
			}

			if ($rst->num_rows<=0) { //device not previously found in db
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output[]='Status: ERROR';
				$output[]='MSG: Device with this id does not exist in database';
				$output[]='Action: query_device';
				$responseData=json_encode($output);
				echo $responseData;
				die();
			} 
//			else {
//				$data=$rst->fetch_array(MYSQLI_ASSOC);
//
//				// device status must be active
//				if ($data['status'] == 'inactive') {
//					header('Content-Type: application/json');
//					header('HTTP/1.1 200 OK');
//					$output[]='Status: ERROR';
//					$output[]='MSG: Device status inactive';
//					$output[]='Action: modify_device';
//					$responseData=json_encode($output);
//					echo $responseData;
//					die();
//				}
//			}
		}
		
		// VALIDATE $mid
		if ($mid != NULL) {
			// $mid MUST BE INT
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


			// $mid must a valid manufacturer id in the database
			$sql="SELECT `auto_id` FROM `manufacturers` WHERE `auto_id` = '$mid'";
//			$sql="SELECT `auto_id`, `status` FROM `manufacturers` WHERE `auto_id` = '$mid'";
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
			} 
//			else {
//				$data=$rst->fetch_array(MYSQLI_ASSOC);
//
//				// manufacturer status must be active
//				if ($data['status'] == 'inactive') {
//					header('Content-Type: application/json');
//					header('HTTP/1.1 200 OK');
//					$output[]='Status: ERROR';
//					$output[]='MSG: Manufacturer status inactive';
//					$output[]='Action: modify_manufacturer';
//					$responseData=json_encode($output);
//					echo $responseData;
//					die();
//				}
//			}
		}
		
		// validate $sn
		if ($sn != NULL){
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

			// search for duplicate sn
			$sql="SELECT `auto_id` FROM `serial_nums` WHERE `serial_number` = '$sn' AND `auto_id` != '$eid'";
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
		
		
		// MODIFY EQUIPMENT //
		
		// Finding out which ones are not NULL 
		// 0b3210
		// BIT 0 represents status
		// BIT 1 represents serial number
		// BIT 2 represents manufacturer id
		// BIT 3 represents device id
		$didCase = 0b0000;
		$midCase = 0b0000;
		$snCase = 0b0000;
		$statusCase = 0b0000;
		
		if ($did != NULL)
			$didCase = 0b1000;
		
		if ($mid != NULL)
			$midCase = 0b0100;
			
		if ($sn != NULL) 
			$snCase = 0b0010;
		
		if ($newStatus != NULL) 
			$statusCase = 0b0001;
		
		// Combine using Bitwise OR
		$caseValue = $didCase | $midCase | $snCase | $statusCase;
		
		// Figuring out what is the appropriate SQL statement based on which variables have data
		switch ($caseValue) {
			case 0b1111:
				$sql="UPDATE `serial_nums`
						SET `device_id` = '$did',
							`manufacturer_id` = '$mid',
						 	`serial_number` = '$sn',
							`status` = '$newStatus'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b1110:
				$sql="UPDATE `serial_nums`
						SET `device_id` = '$did',
							`manufacturer_id` = '$mid',
						 	`serial_number` = '$sn'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b1101:
				$sql="UPDATE `serial_nums`
						SET `device_id` = '$did',
							`manufacturer_id` = '$mid',
							`status` = '$newStatus'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b1100:
				$sql="UPDATE `serial_nums`
						SET `device_id` = '$did',
							`manufacturer_id` = '$mid'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b1011:
				$sql="UPDATE `serial_nums`
						SET `device_id` = '$did',
						 	`serial_number` = '$sn',
							`status` = '$newStatus'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b1010:
				$sql="UPDATE `serial_nums`
						SET `device_id` = '$did',
						 	`serial_number` = '$sn'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b1001:
				$sql="UPDATE `serial_nums`
						SET `device_id` = '$did',
							`status` = '$newStatus'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b1000:
				$sql="UPDATE `serial_nums`
						SET `device_id` = '$did'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b0111:
				$sql="UPDATE `serial_nums`
						SET `manufacturer_id` = '$mid',
						 	`serial_number` = '$sn',
							`status` = '$newStatus'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b0110:
				$sql="UPDATE `serial_nums`
						SET `manufacturer_id` = '$mid',
						 	`serial_number` = '$sn'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b0101:
				$sql="UPDATE `serial_nums`
						SET `manufacturer_id` = '$mid',
							`status` = '$newStatus'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b0100:
				$sql="UPDATE `serial_nums`
						SET `manufacturer_id` = '$mid'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b0011:
				$sql="UPDATE `serial_nums`
						SET `serial_number` = '$sn',
							`status` = '$newStatus'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b0010:
				$sql="UPDATE `serial_nums`
						SET `serial_number` = '$sn'
						WHERE `auto_id` = '$eid'";
				break;
			case 0b0001:
				$sql="UPDATE `serial_nums`
						SET `status` = '$newStatus'
						WHERE `auto_id` = '$eid'";
				break;
			default: //0b0000
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output[]='Status: ERROR';
				$output[]='MSG: Missing new equipment information';
				$output[]='Action: None';
				$responseData=json_encode($output);
				echo $responseData;
				die();
		}
		
		$result=$dblink->query($sql);
			
		if ($result === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}
			
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[] = 'Status: Success';
		$output[] = 'MSG: Equipment successfully modified in the database';
		$output[] = 'Action: None';
		$responseData = json_encode($output);
		echo $responseData;
		die();		
	}  catch (Exception $e) {
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