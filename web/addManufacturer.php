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
                    <a href="#" class="navbar-brand">Add New Manufacturer</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
						<li><a href="devices.php" class="smoothScroll">Devices</a></li>
						<li><a href="manufacturers.php" class="smoothScroll">Manufactures</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                         <li><a href="add.php" class="smoothScroll">Add Equipment</a></li>
						 <li class="active"><a href="add.php" class="smoothScroll">Add New Manufacturer</a></li>
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
							// User notification for manufacturer already exist in db
							if  ($_REQUEST['msg']=="manufacturerExists") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer already exist in database!</div>';
							}
							
							// User notification for manufacturer name empty
							if ($_REQUEST['msg']=="null") {
								echo '<div class="alert alert-danger" role="alert">Manufacturer name cannot be empty!</div>';
							}
							
							// User notification for manufacturer name invalid
							if ($_REQUEST['msg']=="manufacturerInvalid") {
								echo '<div class="alert alert-danger" role="alert">Invalid Manufacturer name submitted!</div>';
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
                        <label for="newManu"><h4>Enter manufacturer name you want to add:</h4></label>
                        <input type="text" class="form-control" id="newManu" name="newManu">
                    </div>
                        <button type="submit" class="btn btn-primary" name="submit" value="submit">Add Manufacturer</button>
                 </form>
               </div>
          </div>
     </section>
</body>
</html>
<?php
	if (isset($_POST['submit'])) {
		include("../functions.php");		
		$newManu=trim($_POST['newManu']);

		if (empty($newManu)) {
			redirect("?msg=null");
		}
		
		// Validate manufacturer name
		// Manufacturer name must be:
		//   less than 64 characters
		//   alpha-numeric
		//   spaces
		if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $newManu)) {
			redirect("?msg=manufacturerInvalid");
		} 
		
		$check = call_api("add_manufacturer", "mname=$newManu");
		
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
			case "Manufacturer successfully added to database": redirect("add.php?msg=manufacturerAdded");
			case "Manufacturer already exists in database": redirect("?msg=manufacturerExists");
			case "Missing manufacturer name": redirect("?msg=null");
			case "Invalid manufacturer name": redirect("?msg=manufacturerInvalid");
			default: redirect("?msg=error");
		}	
	}
?>