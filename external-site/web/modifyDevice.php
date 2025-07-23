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
                    <a href="#" class="navbar-brand">Modify Device</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
						<li><a href="devices.php" class="smoothScroll">Devices</a></li>
						<li><a href="manufacturers.php" class="smoothScroll">Manufactures</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                         <li class="active"><a href="search.php" class="smoothScroll">Modify Device</a></li>
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
							// User notification for successful device addition
							if ($_REQUEST['msg']=="deviceModified") {
								echo '<div class="alert alert-success" role="alert">Device successfully modified.</div>';
							}
							
							// User notification for device already exist in db
							if ($_REQUEST['msg']=="deviceExists") {
								echo '<div class="alert alert-danger" role="alert">Device already exist in database!</div>';
							}
							
							// Device does not exist in db
							if ($_REQUEST['msg']=="noMatch") {
								echo '<div class="alert alert-danger" role="alert">Device with this id does not exist in database!</div>';
							}

							// User notification for device name empty
							if ($_REQUEST['msg']=="null") {
								echo '<div class="alert alert-danger" role="alert">Device name cannot be empty!</div>';
							}
							
							// invalid device id
							if ($_REQUEST['msg']=="invalidId") {
								echo '<div class="alert alert-danger" role="alert">Device id is invalid!</div>';
							}

							// empty device id
							if ($_REQUEST['msg']=="nullId") {
								echo '<div class="alert alert-danger" role="alert">Device id cannot be empty!</div>';
							}
							
							// invalid device name
							if ($_REQUEST['msg']=="invalidName") {
								echo '<div class="alert alert-danger" role="alert">Device name is invalid!</div>';
							}
							
							// User notification for device name empty
							if ($_REQUEST['msg']=="invalidStatus") {
								echo '<div class="alert alert-danger" role="alert">Invalid device status!</div>';
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
   
				   		$devID = trim($_GET['q']);
				   
				   		
				   		if (($devID == NULL) || (!is_numeric($devID))) {
							echo "<div><h1>Invalid device id!</h1></div>";
						} else {
							
							// API CALL
							$check = call_api("view_device", "did=$devID");
							
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
										case "Missing device id":
											echo "<div><h1>Missing device id!</h1></div>";
											break;
										case "Invalid device id": 
											echo "<div><h1>Invalid device id!</h1></div>";
											break;
										case "Device with this id does not exist in database":
											echo "<div><h1>Device with this id does not exist in database!</h1></div>";
											break;
										default:
											$devObject = json_decode($resultPayload[1], true);

											if (is_array($devObject)) {
												echo '<form method="post" action="">';
												echo 	'<div class="form-group">';
												echo 		'<label for="newDevice"><h4>Device name:</h4></label>';
												echo 		'<input type="text" class="form-control" id="newDevice" name="newDevice" value="' . htmlspecialchars($devObject["name"]) . '">';
												echo 	'</div>';
												echo '<label for="status"><h4>Status:</h4></label>';
												echo '<div class="form-group"><select class="form-control" name="status">';
														if ($devObject["status"] == "active")	{
															echo '<option value="active" selected>'.$devObject["status"].'</option>';
															echo '<option value="inactive" >inactive</option>';
														} else {
															echo '<option value="active" >active</option>';
															echo '<option value="inactive" selected>'.$devObject["status"].'</option>';
														}
												echo 	'</select></div>';
												echo 	'<input type="hidden" name="devID" id="devID" value="'.$devID.'">';
												echo 	'<button type="submit" class="btn btn-primary" name="submit" value="submit">Save</button>';
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
		$did = $_POST['devID'];
		
		if (empty($did)) {
			redirect("?msg=nullId");
		}
		
		if (!is_numeric($did)) {
			redirect("?msg=invalidId");
		}
	
		$newDevice = trim($_POST['newDevice']);
		
		if (empty($newDevice)) {
			redirect("?q=$did&msg=null");
		}
		
		if (!isset($_POST['status'])) {
			redirect("?q=$did&msg=invalidStatus");
		}
		
		if (($_POST['status'] != "active") && ($_POST['status'] != "inactive")) {
			redirect("?q=$did&msg=invalidStatus");
		} else {
			$newStatus = trim($_POST['status']);
		}

		// Validate new device name
		// Device name must be:
		//   less than 64 characters
		//   alpha-numeric
		//   spaces
		if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $newDevice)) {
			redirect("?q=$did&msg=invalidName");
		} 
		
		// MODIFY DEVICE API CALL
		$check = call_api("modify_device", "did=$did&dname=$newDevice&status=$newStatus");
		
		if ($check == FALSE) {
			// error for unable to establish connection with API
			redirect("?q=$did&msg=apiError");
		}

		$result = json_decode($check, true);

		if ($result == NULL) {
			// api returned no data
			redirect("?msg=error");
		}

		$resultPayload = explode("MSG: ", $result[1]);
		
		switch ($resultPayload[1]) { 
			case "Device successfully modified": redirect("?q=$did&msg=deviceModified");
			case "Device with this id does not exist in database": redirect("?q=$did&msg=noMatch");
			case "Device with this name already exist in database": redirect("?q=$did&msg=deviceExists"); 
			case "Invalid status": redirect("?q=$did&msg=invalidStatus");
			case "Invalid device name": redirect("?q=$did&msg=invalidName");
			case "Missing new device information": redirect("?q=$did&msg=null");
			case "Invalid device id": redirect("?msg=invalidId");
			case "Missing device id": redirect("?msg=nullId");		
			default: redirect("?msg=error");
		}		
	}
?>