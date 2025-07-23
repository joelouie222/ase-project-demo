<!doctype html>
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
                    <a href="#" class="navbar-brand">Add New Equipment</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
						<li><a href="devices.php" class="smoothScroll">Devices</a></li>
						<li><a href="manufacturers.php" class="smoothScroll">Manufactures</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                         <li class="active"><a href="add.php" class="smoothScroll">Add Equipment</a></li>
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
				   		include("../functions.php");	   
				   
				   		if (isset($_REQUEST['msg'])) {
							// User notification for successful equipment addition
				   			if ($_REQUEST['msg']=="equipmentAdded") {
								echo '<div class="alert alert-success" role="alert">Equipment successfully added. </div>';
							}
							
							// User notification for successful device addition
							if ($_REQUEST['msg']=="deviceAdded") {
								echo '<div class="alert alert-success" role="alert">Device successfully added.</div>';
							}
							
							// User notification for successful manufacturer addition
							if ($_REQUEST['msg']=="manufacturerAdded") {
								echo '<div class="alert alert-success" role="alert">Manufacturer successfully added.</div>';
							}
							
							// User notification for SN already exist in db
							if ($_REQUEST['msg']=="serialExists") {
								echo '<div class="alert alert-danger" role="alert">Serial Number already exist in database!</div>';
							}
							
							// User notification for device name empty
							if ($_REQUEST['msg']=="deviceNull") {
								echo '<div class="alert alert-danger" role="alert">Device name cannot be empty!</div>';
							}

							// User notification for device name invalid
							if ($_REQUEST['msg']=="deviceInvalid") {
								echo '<div class="alert alert-danger" role="alert">Invalid Device name submitted!</div>';
							}
							
							// User notification for device inactive
							if ($_REQUEST['msg']=="deviceInactive") {
								echo '<div class="alert alert-danger" role="alert">Cannot use Device with inactive status!</div>';
							}

							// Device not in database
							if ($_REQUEST['msg']=="deviceNotExist") {
								echo '<div class="alert alert-danger" role="alert">Device does not exist in database!</div>';
							}
							
							// User notification for manufacturer name empty
							if ($_REQUEST['msg']=="manufacturerNull") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer name cannot be empty!</div>';
							}
							
							// User notification for manufacturer name invalid
							if ($_REQUEST['msg']=="manufacturerInvalid") {
								echo '<div class="alert alert-danger" role="alert">Invalid Manufacturer name submitted!</div>';
							}
							
							// User notification for manufacturer inactive
							if ($_REQUEST['msg']=="manufacturerInactive") {
								echo '<div class="alert alert-danger" role="alert">Cannot use Manufacturer with inactive status!</div>';
							}
							
							// Manufacturer not in database
							if ($_REQUEST['msg']=="manufacturerNotExist") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer does not exist in database!</div>';
							}

							// User notification for serial number empty
							if ($_REQUEST['msg']=="serialnumberNull") {
								echo '<div class="alert alert-danger" role="alert">Serial number cannot be empty!</div>';
							}
							
							// Serial number invalid
							if ($_REQUEST['msg']=="serialnumberInvalid") {
								echo '<div class="alert alert-danger" role="alert">Invalid Serial Number submitted!</div>';
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
						
				   		// LIST DEVICES
				   		$check = call_api("list_devices", "");
		
						if ($check == FALSE) {
							// error, unable to establish connection with API
							redirect("?msg=apiError");
						}
						$resultDevicesArray = json_decode($check, true);

						if ($resultDevicesArray == NULL) {
							// api returned no data
							redirect("?msg=error");
						}
				   		$devicePayload = explode("MSG: ", $resultDevicesArray[1]);
				   		$devices=json_decode($devicePayload[1], true);
				   
				   
				   		// LIST MANUFACTURERS
				   		$check = call_api("list_manufacturers", "");
		
						if ($check == FALSE) {
							// error, unable to establish connection with API
							redirect("?msg=apiError");
						}
						$resultManuArray = json_decode($check, true);

						if ($resultManuArray == NULL) {
							// api returned no data
							redirect("?msg=error");
						}		   
				   		$ManuPayload = explode("MSG: ", $resultManuArray[1]);
				   		$manufacturers=json_decode($ManuPayload[1], true);
  		
				   ?>
                 <form method="post" action="">
                    <div class="form-group">
                        <label for="exampleDevice">Device:</label>
                        <select class="form-control" name="device">
                        	<?php
								foreach($devices as $key=>$value)
									echo '<option value="'.$key.'">'.$value.'</option>';
							?>
						</select>
						<a href="addDevice.php" class="btn btn-default smoothScroll">Add New Device</a>
                    </div>
					 
					 
                    <div class="form-group">
                        <label for="exampleManufacturer">Manufacturer:</label>
                        <select class="form-control" name="manufacturer">
                        	<?php
								foreach($manufacturers as $key=>$value)
									echo '<option value="'.$key.'">'.$value.'</option>';
							?>
						</select>
						<a href="addManufacturer.php" class="btn btn-default smoothScroll">Add New Manufacturer</a>
                    </div>
					 
					 
                    <div class="form-group">
                        <label for="exampleSerial">Serial Number:</label>
                        <input type="text" class="form-control" id="serialnumber" name="serialnumber" value="SN-">
                    </div>
                        <button type="submit" class="btn btn-primary" name="submit" value="submit">Add Equipment</button>
					 	
                 </form>
               </div>
          </div>
     </section>
</body>
</html>
<?php
	if (isset($_POST['submit'])) {
		$device=trim($_POST['device']);
		
		if (empty($device)) {
			redirect("?msg=deviceNull");
		}
		
		// VALIDATE $device, MUST BE INT
		if (!is_numeric($device)) {
			redirect("?msg=deviceInvalid");
		} 
		
		$manufacturer=trim($_POST['manufacturer']);
		if (empty($manufacturer)) {
			redirect("?msg=manufacturerNull");
		}
		
		// VALIDATE $manufacturer, MUST BE INT
		if (!is_numeric($manufacturer)) {
			redirect("?msg=manufacturerInvalid");
		} 
		
		$serialNumber=trim($_POST['serialnumber']);
		if (empty($serialNumber)) {
			redirect("?msg=serialnumberNull");
		}	
		
		// Validate serial number
		// Serial number must:
		//   starts with "SN-"
		//   followed by up to 64 alpha-numeric characters
		if (!preg_match('/^SN-[a-zA-Z0-9]{1,64}$/', $serialNumber)) {
			redirect("?msg=serialnumberInvalid");
		}
		
		
		$check = call_api("add_equipment", "did=$device&mid=$manufacturer&sn=$serialNumber");
		
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
			case "Equipment successfully added to database": redirect("?msg=equipmentAdded");
			case "Serial number exists in database": redirect("?msg=serialExists");
			case "Missing device id": redirect("?msg=deviceNull");
			case "Missing manufacturer id": redirect("?msg=manufacturerNull");
			case "Missing serial number": redirect("?msg=serialnumberNull");
			case "Invalid device id": redirect("?msg=deviceInvalid");
			case "Invalid manufacturer id": redirect("?msg=manufacturerInvalid");
			case "Invalid serial number": redirect("?msg=serialnumberInvalid");
			case "Device does not exist in database": redirect("?msg=deviceNotExist");
			case "Device status inactive": redirect("?msg=deviceInactive");
			case "Manufacturer does not exist in database":  redirect("?msg=manufacturerNotExist"); 
			case "Manufacturer status inactive":redirect("?msg=manufacturerInactive");
			default: redirect("?msg=error");
		}
	}
?>