<html>
<head>
<meta charset="utf-8">
<title>Advanced Software Engineering</title>
<link href="../assets/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/font-awesome.min.css">
<link rel="stylesheet" href="../assets/css/owl.carousel.css">
<link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">

<!-- MAIN CSS -->
<link rel="stylesheet" href="../assets/css/templatemo-style.css">
</head>
<body>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
     <!-- MENU -->
     <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
          <div class="container">
               <div class="navbar-header">
                    <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                    </button>

                    <!-- lOGO TEXT HERE -->
                    <a href="#" class="navbar-brand">Modify Equipment</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
						<li><a href="devices.php" class="smoothScroll">Devices</a></li>
						<li><a href="manufacturers.php" class="smoothScroll">Manufactures</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                         <li class="active"><a href="search.php" class="smoothScroll">Modify Equipment</a></li>
						<li><a href="add.php" class="smoothScroll">Add Equipment</a></li>
                    </ul>
               </div>
          </div>
     </section>
 <!-- HOME -->
     <section id="home">
          </div>
     </section>
     <!-- FEATURE -->
     <section id="feature">
          <div class="container">
               <div class="row">
                   <?php
				   		if (isset($_REQUEST['msg'])) {
							// User notification for successful equipment addition
							if ($_REQUEST['msg']=="eqModified") {
								echo '<div class="alert alert-success" role="alert">Equipment successfully modified. </div>';
							}
							
							// Equipment does not exist
							if ($_REQUEST['msg']=="noMatch") {
								echo '<div class="alert alert-danger" role="alert">Equipment with this id does not exist in database!</div>';
							}
							
							// Missing equipment id
							if ($_REQUEST['msg']=="missingId") {
								echo '<div class="alert alert-danger" role="alert">Missing equipment id!</div>';
							}
							
							// Device name empty
							if ($_REQUEST['msg']=="deviceNull") {
								echo '<div class="alert alert-danger" role="alert">Device name cannot be empty!</div>';
							}
							
							// Manufacturer name empty
							if ($_REQUEST['msg']=="manufacturerNull") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer name cannot be empty!</div>';
							}
							
							// Serial number empty
							if ($_REQUEST['msg']=="serialnumberNull") {
								echo '<div class="alert alert-danger" role="alert">Serial number cannot be empty!</div>';
							}
							
							// Status empty
							if ($_REQUEST['msg']=="statusNull") {
								echo '<div class="alert alert-danger" role="alert">Equipment status cannot be empty!</div>';
							}							
							
							// Duplicate Serial Number 
							if ($_REQUEST['msg']=="serialExists") {
								echo '<div class="alert alert-danger" role="alert">Serial Number already exist in database!</div>';
							}
							
							// Invalid equipment id
							if ($_REQUEST['msg']=="invalidId") {
								echo '<div class="alert alert-danger" role="alert">Invalid equipment id!</div>';
							}
							
							// Device not valid
							if ($_REQUEST['msg']=="invalidDevice") {
								echo '<div class="alert alert-danger" role="alert">Device type is not valid!</div>';
							}
							
							// Manufacturer not valid
							if ($_REQUEST['msg']=="invalidManufacturer") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer is not valid!</div>';
							}
							
							// Serial number not valid
							if ($_REQUEST['msg']=="invalidSn") {
								echo '<div class="alert alert-danger" role="alert">Serial number is not valid!</div>';
							}
							
							// Status empty
							if ($_REQUEST['msg']=="invalidStatus") {
								echo '<div class="alert alert-danger" role="alert">Equipment status is not valid!</div>';
							}
							
							// API Conenction Error
							if ($_REQUEST['msg']=="apiError") {
								echo '<div class="alert alert-danger" role="alert">Unable to establish connection with API!</div>';
							}
							
							// General error
							if ($_REQUEST['msg']=="error") {
								echo '<div class="alert alert-danger" role="alert">Something went wrong. Try again later!</div>';
							}
						}
				   						
				   		include("../functions.php");
				   
				   		$eqID = trim($_GET['q']);
				   
				   		if (($eqID == NULL) || (!is_numeric($eqID))) {
							echo "<div><h1>Invalid equipment id!</h1></div>";
						} else {
							// LIST DEVICES API CALL
							$check = call_api("list_devices", "");

							if ($check == FALSE) {
								// error, unable to establish connection with API		
								echo "<div><h1>Unable to establish connection with API. Try again later!</h1></div>";
							}
							$resultDevicesArray = json_decode($check, true);
							
							if ($resultDevicesArray == NULL) {
								// api returned no data
								echo "<div><h1>Something went wrong. Try again later!</h1></div>";	
							}
							$devicePayload = explode("MSG: ", $resultDevicesArray[1]);
							$devices=json_decode($devicePayload[1], true);
							
							
							// LIST MANUFACTURERS API CALL
							$check = call_api("list_manufacturers", "");

							if ($check == FALSE) {
								// error, unable to establish connection with API		
								echo "<div><h1>Unable to establish connection with API. Try again later!</h1></div>";
							}
							$resultManuArray = json_decode($check, true);

							if ($resultDevicesArray == NULL) {
								// api returned no data
								echo "<div><h1>Something went wrong. Try again later!</h1></div>";	
							}		   
							$ManuPayload = explode("MSG: ", $resultManuArray[1]);
							$manufacturers=json_decode($ManuPayload[1], true);
							

							// VIEW EQUIPMENT API CALL
							$check = call_api("view_equipment", "eid=$eqID");
							
							if ($check == FALSE) {
								// error, unable to establish connection with API		
								echo "<div><h1>Unable to establish connection with API. Try again later!</h1></div>";
							} else {
								$result = json_decode($check, true);
								
								if ($result == NULL) {
									// api returned no data
									echo "<div><h1>Something went wrong. Try again later!</h1></div>";					
								} else {
									$resultPayload = explode("MSG: ", $result[1]);
									
									switch ($resultPayload[1]) {
										case "Missing equipment id":
											echo "<div><h1>Equipment id is missing!</h1></div>";
											break;
										case "Invalid equipment id":
											echo "<div><h1>Invalid equipment id!</h1></div>";
											break;
										case "Equipment with this id does not exist in database":
											echo "<div><h1>Equipment with this id does not exist in database!</h1></div>";
											break;		
										default:
											$eqObject = json_decode($resultPayload[1], true);
									
											if (is_array($eqObject)) {	
												// DEVICE FIELD
												echo '<form method="post" action="">';
												echo 	'<div class="form-group">';
												echo 		'<label for="device">Device:</label>';
												echo 		'<select class="form-control" name="device">';
															foreach($devices as $key=>$value)
																if ($value == $eqObject["device_name"]) {
																	echo '<option value="'.$key.'" selected>'.htmlspecialchars($value).'</option>';
																} else {
																	echo '<option value="'.$key.'">'.htmlspecialchars($value).'</option>';
																}
												echo 		'</select>';
												echo 		'<a href="addDevice.php" class="btn btn-default smoothScroll">Add New Device</a></div>';
												
												// MANUFACTURER FIELD
												echo 	'<div class="form-group">';
												echo 		'<label for="manufacturer">Manufacturer:</label>';
												echo 		'<select class="form-control" name="manufacturer">';
															foreach($manufacturers as $key=>$value)
																if ($value == $eqObject["manufacturer_name"]) {
																	echo '<option value="'.$key.'" selected>'.htmlspecialchars($value).'</option>';
																} else {
																	echo '<option value="'.$key.'">'.htmlspecialchars($value).'</option>';
																}
												echo 		'</select>';
												echo 		'<a href="addManufacturer.php" class="btn btn-default smoothScroll">Add New Manufacturer</a></div>';
									
												// SERIAL NUMBER FIELD
												echo 	'<div class="form-group">';
												echo 		'<label for="serialnumber">Serial Number:</label>';
												echo 		'<input type="text" class="form-control" id="serialnumber" name="serialnumber" value='.$eqObject["serial_number"].'></div>';
												
												// STATUS
												echo 	'<div class="form-group">';
												echo 		'<label for="status">Status:</label>';
												echo 		'<select class="form-control" name="status">';
															if ($eqObject["status"] == 'active') {
																echo '<option value="active" selected>'.$eqObject["status"].'</option>';
																echo '<option value="inactive">inactive</option>';
															} else {
																echo '<option value="active">active</option>';
																echo '<option value="inactive" selected>'.$eqObject["status"].'</option>';
															}
												echo 		'</select>';

												// EQUIPMENT ID
												echo 		'<input type="hidden" name="eqID" id="eqID" value="'.$eqID.'">';
											
												// SUBMIT/CANCEL BUTTON
												echo "<div>
														<button type='submit' class='btn btn-primary' name='submit' value='submit'>Save</button>
														<a href='view.php?q=$eqID' class='btn btn-default smoothScroll' style='margin:8px'>Cancel</a>
													</div>";
												echo '</form>';

											} else {
												echo "<div><h1>Something went wrong. Try again later!</h1></div>";
											}
									} // switch
								}
							}
						}
				   ?>
               </div>
          </div>
     </section>
</body>
</html>
<?php
	if ((isset($_POST['submit'])) && ($_POST['submit'] == "submit")) {	
		$eid = trim($_POST['eqID']);
		$did = trim($_POST['device']);
		$mid = trim($_POST['manufacturer']);
		$sn = trim($_POST['serialnumber']);
		
		if (empty($eid)) { redirect("?msg=nullId");	}
		if (!is_numeric($eid)) { redirect("?msg=invalidId"); }
		if (empty($did))  {	redirect("?q=$eid&msg=deviceNull"); }
		if (!is_numeric($did)) { redirect("?q=$eid&msg=invalidDevice");	}
		if (empty($mid)) {	redirect("?q=$eid&msg=manufacturerNull"); }
		if (!is_numeric($mid)) { redirect("?q=$eid&msg=invalidManufacturer"); }
		if (empty($sn)) { redirect("?q=$eid&msg=serialnumberNull"); }
		
		// Validate serial number
		// Serial number must:
		//   starts with "SN-"
		//   followed by up to 64 alpha-numeric characters
		if (!preg_match('/^SN-[a-zA-Z0-9]{1,64}$/', $sn)) {
			redirect("?q=$eid&msg=invalidSn");
		}
		
		if (($_POST['status'] != "active") && ($_POST['status'] != "inactive")) {
			redirect("?q=$eid&msg=invalidStatus");
		} else {
			$newStatus = trim($_POST['status']);
		}
		
		
		// MODIFY EQUIPMENT API CALL
		$check = call_api("modify_equipment", "eid=$eid&did=$did&mid=$mid&sn=$sn&status=$newStatus");
		
		if ($check == FALSE) {
			// error for unable to establish connection with API
			redirect("?q=$eid&msg=apiError");
		}

		$result = json_decode($check, true);

		if ($result == NULL) {
			// api returned no data
			redirect("?msg=error");
		}

		$resultPayload = explode("MSG: ", $result[1]);
		
		
		switch ($resultPayload[1]) { 
			case "Equipment successfully modified in the database": redirect("?q=$eid&msg=eqModified");
			case "Serial number exists in database": redirect("?q=$eid&msg=serialExists");
			case "Equipment with this id does not exist in database": redirect("?q=$eid&msg=noMatch");
			case "Missing equipment id": redirect("?msg=nullId");	
			case "Invalid equipment id": redirect("?msg=invalidId"); 
			case "Missing new equipment information": redirect("?q=$eid&msg=missingId");
			case "Invalid device id": redirect("?q=$eid&msg=invalidDevice");
			case "Device with this id does not exist in database": redirect("?q=$eid&msg=invalidDevice");
			case "Invalid manufacturer id": redirect("?q=$eid&msg=invalidManufacturer");
			case "Manufacturer with this id does not exist in database": redirect("?q=$eid&msg=invalidManufacturer");
			case "Invalid serial number": redirect("?q=$eid&msg=invalidSn");
			case "Invalid status": redirect("?q=$eid&msg=invalidStatus");
			default: redirect("?msg=error");	
		}
	}
?>