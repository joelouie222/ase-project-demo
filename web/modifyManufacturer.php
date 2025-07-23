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
                    <a href="#" class="navbar-brand">Modify Manufacturer</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
						<li><a href="devices.php" class="smoothScroll">Devices</a></li>
						<li><a href="manufacturers.php" class="smoothScroll">Manufactures</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                         <li class="active"><a href="search.php" class="smoothScroll">Modify Manufacturer</a></li>
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
							// User notification for successful manufacturer modification
							if ($_REQUEST['msg']=="manufacturerModified") {
								echo '<div class="alert alert-success" role="alert">Manufacturer successfully modified.</div>';
							}
							
							// Manufacturer name already exist in db
							if ($_REQUEST['msg']=="manufacturerExists") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer already exist in database!</div>';
							}
							
							// Manufacturer does not exist in db
							if ($_REQUEST['msg']=="noMatch") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer with this id does not exist in database!</div>';
							}
							
							// User notification for manufacturer name empty
							if ($_REQUEST['msg']=="null") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer name cannot be empty!</div>';
							}
							
							// invalid manufacturer id
							if ($_REQUEST['msg']=="invalidId") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer id is invalid!</div>';
							}
							
							// empty manufacturer id
							if ($_REQUEST['msg']=="nullId") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer id cannot be empty!</div>';
							}
							
							// invalid manufacturer name
							if ($_REQUEST['msg']=="invalidName") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer name is invalid!</div>';
							}
							
							// User notification for invalid manufacturer status
							if ($_REQUEST['msg']=="invalidStatus") {
								echo '<div class="alert alert-danger" role="alert">Invalid manufacturer status!</div>';
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
   
				   		$manID = trim($_GET['q']);
				   
				   		// $eid MUST BE INT
						if (($manID == NULL) || (!is_numeric($manID))) {
							echo "<div><h1>Invalid manufacturer id!</h1></div>";
						} else {
							
							// API CALL
							$check = call_api("view_manufacturer", "mid=$manID");
		
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
										case "Missing manufacturer id":
											echo "<div><h1>Missing manufacturer id!</h1></div>";
											break;
										case "Invalid manufacturer id": 
											echo "<div><h1>Invalid manufacturer id!</h1></div>";
											break;
										case "Manufacturer with this id does not exist in database":
											echo "<div><h1>Manufacturer with this id does not exist in database!</h1></div>";
											break;
										default:
											$manObject = json_decode($resultPayload[1], true);
									
											if (is_array($manObject)) {
												echo '<form method="post" action="">';
												echo 	'<div class="form-group">';
												echo 		'<label for="newManu"><h4>Manufacturer name:</h4></label>';
												echo 		'<input type="text" class="form-control" id="newManu" name="newManu" value="' . htmlspecialchars($manObject["name"]) . '">';
												echo 	'</div>';
												echo '<label for="status"><h4>Status:</h4></label>';
												echo '<div class="form-group"><select class="form-control" name="status">';
														if ($manObject["status"] == "active")	{
															echo '<option value="active" selected>'.$manObject["status"].'</option>';
															echo '<option value="inactive" >inactive</option>';
														} else {
															echo '<option value="active" >active</option>';
															echo '<option value="inactive" selected>'.$manObject["status"].'</option>';
														}
												echo 	'</select></div>';
												echo 	'<input type="hidden" name="manID" id="manID" value="'.$manID.'">';
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
		$mid = $_POST['manID'];
		
		if (empty($mid)) {
			redirect("?msg=nullId");
		}
		
		if (!is_numeric($mid)) {
			redirect("?msg=invalidId");
		}
	
		$newManu=trim($_POST['newManu']);
		
		if (empty($newManu)) {
			redirect("?q=$mid&msg=null");
		}
		
		if (!isset($_POST['status'])) {
			redirect("?q=$mid&msg=invalidStatus");
		}
		
		if (($_POST['status'] != "active") && ($_POST['status'] != "inactive")) {
			redirect("?q=$mid&msg=invalidStatus");
		} else {
			$newStatus = trim($_POST['status']);
		}

		// Validate new manufacturer name
		// Manufacturer name must be:
		//   less than 64 characters
		//   alpha-numeric
		//   spaces
		if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $newManu)) {
			redirect("?q=$mid&msg=invalidName");
		} 
		
		// MODIFY MANUFACTURER API CALL
		$check = call_api("modify_manufacturer", "mid=$mid&mname=$newManu&status=$newStatus");
		
		if ($check == FALSE) {
			// error for unable to establish connection with API
			redirect("?q=$mid&msg=apiError");
		}

		$result = json_decode($check, true);

		if ($result == NULL) {
			// api returned no data
			redirect("?msg=error");
		}

		$resultPayload = explode("MSG: ", $result[1]);
		
		switch ($resultPayload[1]) { 
			case "Manufacturer successfully modified": redirect("?q=$mid&msg=manufacturerModified");
			case "Manufacturer with this id does not exist in database": redirect("?q=$mid&msg=noMatch");
			case "Manufacturer with this name already exist in database": redirect("?q=$mid&msg=manufacturerExists"); 
			case "Invalid status": redirect("?q=$mid&msg=invalidStatus");
			case "Invalid manufacturer name": redirect("?q=$mid&msg=invalidName");
			case "Missing new manufacturer information": redirect("?q=$mid&msg=null");
			case "Invalid manufacturer id": redirect("?msg=invalidId");
			case "Missing manufacturer id": redirect("?msg=nullId");		
			default: redirect("?msg=error");
		}		
	}
?>