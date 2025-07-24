<?php
	// CHECK if NAME is NULL
	if ($mname==NULL)//missing manufacturer name
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing manufacturer name';
		$output[]='Action: None';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	
	// Validate manufacturer name
	// Manufacturer name must be:
	//   less than 64 characters
	//   alpha-numeric
	//   spaces
	if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $mname)) {
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Invalid manufacturer name';
		$output[]='Action: None';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	} 


	$dblink=db_connect("equipments_db");
	
	try {
		$sql="SELECT `auto_id` AS id, `name`, `status` FROM `manufacturers` WHERE `name` = '$mname'";
		$rst=$dblink->query($sql);
		
		if ($rst === false) {
			throw new Exception("Query execution failed. " . $dblink->error);
		}
		
		if ($rst->num_rows<=0) //manufacturer not previously found in db
		{
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: There are no manufacturer with this name';
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