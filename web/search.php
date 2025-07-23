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
                    <a href="#" class="navbar-brand">Search Database</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
						<li><a href="devices.php" class="smoothScroll">Devices</a></li>
						<li><a href="manufacturers.php" class="smoothScroll">Manufactures</a></li>
                         <li class="active"><a href="search.php" class="smoothScroll">Search Equipment</a></li>
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
							
							// empty search parameter
							if ($_REQUEST['msg']=="null") {
								echo '<div class="alert alert-danger" role="alert">Search parameters cannot be empty!</div>';
							} 
							
							// No match
							if ($_REQUEST['msg']=="noMatch") {
								echo '<div class="alert alert-danger" role="alert">There are no equipment matching the search parameters!</div>';
							}
							
							// Invalid Serial number format
							if ($_REQUEST['msg']=="invalidSN") {
								echo '<div class="alert alert-danger" role="alert">Invalid serial number!</div>';
							}
							
							// Invalid device name format
							if ($_REQUEST['msg']=="invalidDevice") {
								echo '<div class="alert alert-danger" role="alert">Invalid Device name!</div>';
							}
							
							// Invalid manufacturer name format
							if ($_REQUEST['msg']=="invalidManufacturer") {
								echo '<div class="alert alert-danger" role="alert">Invalid Manufacturer name!</div>';
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
				   
				   <h4>Search Equipment</h4>
				   <form method="post" action="result.php">
						<div class="form-group">
							<div class="form-group">
								<label for="device">Device:</label>
								<input type="text" class="form-control" id="device" name="device" placeholder="Device type"></div>
							
							<div class="form-group">
								<label for="manufacturer">Manufacturer:</label>
								<input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Manufacturer name"> </div>
							
				   			<div class="form-group">
								<label for="serialnumber">Serial Number:</label>
								<input type="text" class="form-control" id="serialnumber" name="serialnumber" placeholder="Serial number"></div>
							
							<div class="form-group">
								<label for="sm">Include Inactive Equipments?</label>
									<select class="form-control" name="sm">
										<option value="y">Yes</option>
										<option value="n" selected>No</option>
									</select></div>
							<button type="submit" class="btn btn-primary" name="submit" value="submit">Search Equipment</button>
					   </div>
               </div>
          </div>
     </section>
</body>
</html>

