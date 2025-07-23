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
                    <a href="#" class="navbar-brand">Add New Device</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
						<li><a href="devices.php" class="smoothScroll">Devices</a></li>
						<li><a href="manufacturers.php" class="smoothScroll">Manufactures</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                         <li><a href="add.php" class="smoothScroll">Add Equipment</a></li>
						 <li class="active"><a href="add.php" class="smoothScroll">Add New Device</a></li>
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
							// User notification for device already exist in db
							if ($_REQUEST['msg']=="deviceExists") {
								echo '<div class="alert alert-danger" role="alert">Device already exist in database!</div>';
							}
							
							// User notification for device name empty
							if ($_REQUEST['msg']=="null") {
								echo '<div class="alert alert-danger" role="alert">Device name cannot be empty!</div>';
							}
							
							// Device name invalid format
							if ($_REQUEST['msg']=="deviceInvalid") {
								echo '<div class="alert alert-danger" role="alert">Invalid Device name submitted!</div>';
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
				   ?>
                    <form method="post" action="">
                    <div class="form-group">
                        <label for="newDevice"><h4>What kind of device do you want to add?</h4></label>
                        <input type="text" class="form-control" id="newDevice" name="newDevice">
                    </div>
                        <button type="submit" class="btn btn-primary" name="submit" value="submit">Add Device</button>
                 </form>
               </div>
          </div>
     </section>
</body>
</html>
<?php
	if (isset($_POST['submit'])) {
		include("../functions.php");
		$newDevice=trim($_POST['newDevice']);
		
		if (empty($newDevice)) {
			redirect("?msg=null");
		}
		
		// Validate device name
		// Device name must be:
		//   less than 64 characters
		//   alpha-numeric
		//   may have spaces
		if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $newDevice)) {
			redirect("?msg=deviceInvalid");
		} 
		
		$check = call_api("add_device", "dname=$newDevice");
		
		if ($check == FALSE) {
			// error for unable to establish connection with API
			redirect("?msg=apiError");
		}
		
		$result = json_decode($check, true);
		
		if ($result == NULL) {
			redirect("?msg=error");
		}
		
		$resultPayload = explode("MSG: ", $result[1]);
		
		switch ($resultPayload[1]) {
			case "Device successfully added to database": redirect("add.php?msg=deviceAdded");
			case "Device already exists in database": redirect("?msg=deviceExists");
			case "Missing device name": redirect("?msg=null");
			case "Invalid device name": redirect("?msg=deviceInvalid");
			default: redirect("?msg=error");
		}
	}
?>