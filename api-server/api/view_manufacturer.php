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

	$dblink=db_connect("equipments_db");

	try {
		$sql="SELECT `auto_id` AS id, `name`, `status` FROM `manufacturers` WHERE `auto_id` = '$mid'";
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